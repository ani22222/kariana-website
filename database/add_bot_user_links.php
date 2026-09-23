<?php
/**
 * Migration: Create bot_user_links table & insert WhatsApp settings
 * Allows single Telegram Bot & WhatsApp Gateway to authenticate and route
 * Main Admin, District Directors, Managers, and Teachers.
 */

require_once __DIR__ . '/../core/Database.php';

try {
    $db = Core\Database::getInstance();

    echo "Running Bot Integration Migration...\n";

    // 1. Create bot_user_links table
    $sql = "CREATE TABLE IF NOT EXISTS `bot_user_links` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `channel` ENUM('telegram', 'whatsapp') NOT NULL DEFAULT 'telegram',
        `sender_id` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(30) NOT NULL,
        `user_type` ENUM('admin', 'director', 'manager', 'teacher', 'student') NOT NULL,
        `user_id` INT UNSIGNED NOT NULL DEFAULT 0,
        `user_name` VARCHAR(150) NOT NULL DEFAULT '',
        `role_title` VARCHAR(100) NOT NULL DEFAULT '',
        `session_state` JSON NULL,
        `is_active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME NOT NULL,
        `updated_at` DATETIME NOT NULL,
        UNIQUE KEY `idx_channel_sender` (`channel`, `sender_id`),
        KEY `idx_phone` (`phone`),
        KEY `idx_user_lookup` (`user_type`, `user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "✓ Table `bot_user_links` created or verified successfully.\n";

    // 2. Insert WhatsApp Gateway Settings if not present
    $settings = [
        'whatsapp_enabled'      => '1',
        'whatsapp_gateway_url'  => 'http://localhost:3000/api/send',
        'whatsapp_api_token'    => 'kariana_wa_token_sec_2026',
        'whatsapp_verify_token' => 'kariana_webhook_verify_2026'
    ];

    foreach ($settings as $key => $val) {
        $stmt = $db->prepare("SELECT COUNT(*) FROM `site_settings` WHERE `setting_key` = :k");
        $stmt->execute([':k' => $key]);
        if ($stmt->fetchColumn() == 0) {
            $ins = $db->prepare("INSERT INTO `site_settings` (`setting_key`, `setting_value`, `group_name`, `created_at`, `updated_at`) VALUES (:k, :v, 'api_settings', NOW(), NOW())");
            $ins->execute([':k' => $key, ':v' => $val]);
            echo "✓ Added setting: {$key}\n";
        } else {
            echo "• Setting exists: {$key}\n";
        }
    }

    echo "=== BOT MIGRATION COMPLETE ===\n";

} catch (PDOException $e) {
    echo "ERROR in migration: " . $e->getMessage() . "\n";
    exit(1);
}
