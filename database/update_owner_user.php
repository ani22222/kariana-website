<?php
/**
 * Update / Seed Owner Maulana Saddam Hossain in users table
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\Database;

$db = Database::getInstance();

// Check if phone column exists in users
$cols = $db->query("SHOW COLUMNS FROM `users` LIKE 'phone'")->fetchAll();
if (empty($cols)) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(30) NULL AFTER `email`, ADD INDEX `idx_users_phone` (`phone`)");
    echo "✓ Added phone column to users table.\n";
}

// Check if designation / title column exists in users
$cols = $db->query("SHOW COLUMNS FROM `users` LIKE 'title'")->fetchAll();
if (empty($cols)) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `title` VARCHAR(150) NULL DEFAULT 'প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা' AFTER `name`");
    echo "✓ Added title column to users table.\n";
}

// Update admin user to Maulana Saddam Hossain
$adminPassword = password_hash('01717056816', PASSWORD_BCRYPT, ['cost' => 12]);

// Check existing admin
$existing = $db->query("SELECT * FROM `users` WHERE `username` = 'admin' OR `phone` = '01717056816' OR `role` = 'admin' LIMIT 1")->fetch();

if ($existing) {
    $stmt = $db->prepare("
        UPDATE `users` 
        SET `name` = 'মাওলানা সাদ্দাম হোসেন',
            `title` = 'প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা',
            `phone` = '01717056816',
            `username` = 'admin',
            `email` = 'saddamhossain@karianaquran.com',
            `role` = 'admin',
            `password` = :pass
        WHERE `id` = :id
    ");
    $stmt->execute([
        'pass' => $adminPassword,
        'id'   => $existing['id']
    ]);
    echo "✓ Updated existing user #{$existing['id']} to মাওলানা সাদ্দাম হোসেন (01717056816)!\n";
} else {
    $stmt = $db->prepare("
        INSERT INTO `users` (`name`, `title`, `email`, `phone`, `username`, `password`, `role`, `created_at`)
        VALUES ('মাওলানা সাদ্দাম হোসেন', 'প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা', 'saddamhossain@karianaquran.com', '01717056816', 'admin', :pass, 'admin', NOW())
    ");
    $stmt->execute(['pass' => $adminPassword]);
    echo "✓ Inserted new user মাওলানা সাদ্দাম হোসেন (01717056816)!\n";
}
