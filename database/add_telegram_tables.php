<?php
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Core\Database::getInstance();
    echo "Creating telegram_users and telegram_referrals tables...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS `telegram_users` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `telegram_id` VARCHAR(64) NOT NULL UNIQUE,
            `first_name` VARCHAR(150) NULL,
            `last_name` VARCHAR(150) NULL,
            `username` VARCHAR(150) NULL,
            `role` ENUM('user', 'teacher', 'director', 'manager', 'super_admin') NOT NULL DEFAULT 'user',
            `referral_code` VARCHAR(100) NULL,
            `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
            `last_active_at` DATETIME NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_tg_id` (`telegram_id`),
            INDEX `idx_tg_role` (`role`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Table `telegram_users` ready.\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS `telegram_referrals` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `referrer_code` VARCHAR(100) NOT NULL,
            `referred_telegram_id` VARCHAR(64) NOT NULL,
            `referred_name` VARCHAR(150) NULL,
            `status` ENUM('joined', 'enrolled', 'ordered') NOT NULL DEFAULT 'joined',
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_ref_code` (`referrer_code`),
            INDEX `idx_ref_tg` (`referred_telegram_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Table `telegram_referrals` ready.\n";

    // Insert initial Super Admin Rasel Gazi if not exists
    $stmt = $db->prepare("SELECT COUNT(*) FROM `telegram_users` WHERE `telegram_id` = '1827362508'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $ins = $db->prepare("
            INSERT INTO `telegram_users` 
            (`telegram_id`, `first_name`, `username`, `role`, `is_admin`, `last_active_at`)
            VALUES ('1827362508', 'Rasel Gazi', 'mraselg', 'super_admin', 1, NOW())
        ");
        $ins->execute();
        echo "✓ Super Admin Rasel Gazi added to telegram_users.\n";
    }

    echo "Migration completed successfully!\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
