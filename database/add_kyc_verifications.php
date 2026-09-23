<?php
/**
 * Migration: Create kyc_verifications table & extend user/teacher/director tables with KYC columns
 * Implements 2-step verification (Level 1: SMS OTP, Level 2: Government NID API + DOB)
 */

require_once __DIR__ . '/../core/Database.php';

try {
    $db = Core\Database::getInstance();
    echo "Running KYC & 2-Step Verification Migration...\n";

    // 1. Create kyc_verifications table
    $sql = "CREATE TABLE IF NOT EXISTS `kyc_verifications` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `uuid` VARCHAR(64) NOT NULL UNIQUE,
        `user_type` ENUM('user', 'teacher', 'director', 'student') NOT NULL,
        `user_id` INT UNSIGNED NOT NULL,
        `phone` VARCHAR(30) NOT NULL,
        `otp_code` VARCHAR(10) NULL,
        `otp_expires_at` DATETIME NULL,
        `is_phone_verified` TINYINT(1) NOT NULL DEFAULT 0,
        `phone_verified_at` DATETIME NULL,
        `nid_number` VARCHAR(30) NULL,
        `date_of_birth` DATE NULL,
        `nid_response_json` LONGTEXT NULL,
        `kyc_level` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0=none, 1=otp_verified, 2=full_nid_kyc',
        `kyc_status` ENUM('unverified', 'pending', 'approved', 'rejected') NOT NULL DEFAULT 'unverified',
        `verification_token` TEXT NULL,
        `verified_at` DATETIME NULL,
        `admin_remarks` TEXT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_kyc_uuid` (`uuid`),
        INDEX `idx_kyc_user` (`user_type`, `user_id`),
        INDEX `idx_kyc_nid` (`nid_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "✓ Table `kyc_verifications` created or verified successfully.\n";

    // 2. Add columns to users, teachers, directors if not existing
    $tables = ['users', 'teachers', 'directors'];
    foreach ($tables as $t) {
        $cols = $db->query("SHOW COLUMNS FROM `$t`")->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('kyc_level', $cols)) {
            $db->exec("ALTER TABLE `$t` ADD COLUMN `kyc_level` TINYINT UNSIGNED NOT NULL DEFAULT 0");
            echo "✓ Added `kyc_level` to `$t`.\n";
        }
        if (!in_array('kyc_uuid', $cols)) {
            $db->exec("ALTER TABLE `$t` ADD COLUMN `kyc_uuid` VARCHAR(64) NULL");
            echo "✓ Added `kyc_uuid` to `$t`.\n";
        }
    }

    // 3. Add default NID API & SMS settings in site_settings
    $settings = [
        'nid_api_enabled'     => '1',
        'nid_api_endpoint'    => 'https://api.porichoybd.com/api/v2/verifications/autofill',
        'nid_api_key'         => '',
        'sms_api_enabled'     => '1',
        'sms_api_endpoint'    => 'https://api.smsnet24.com/send',
        'sms_api_sender_id'   => 'KARIANA',
        'sms_api_key'         => '',
        'kyc_encryption_key'  => bin2hex(random_bytes(32))
    ];

    $stmtCheck = $db->prepare("SELECT COUNT(*) FROM `site_settings` WHERE `setting_key` = ?");
    $stmtInsert = $db->prepare("INSERT INTO `site_settings` (`setting_key`, `setting_value`, `group_name`) VALUES (?, ?, 'kyc')");

    foreach ($settings as $key => $val) {
        $stmtCheck->execute([$key]);
        if ($stmtCheck->fetchColumn() == 0) {
            $stmtInsert->execute([$key, $val]);
            echo "✓ Added setting: $key\n";
        }
    }

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
