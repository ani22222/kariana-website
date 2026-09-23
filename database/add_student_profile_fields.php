<?php
/**
 * Migration: Add student profile fields (district, address, gender) to `users` table
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

$db = \Core\Database::getInstance();

echo "Checking users table columns...\n";
$cols = $db->query("DESCRIBE `users`")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('district', $cols)) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `district` VARCHAR(100) NULL AFTER `phone`");
    echo "✓ Added 'district' column to `users` table.\n";
}

if (!in_array('address', $cols)) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `address` TEXT NULL AFTER `district`");
    echo "✓ Added 'address' column to `users` table.\n";
}

if (!in_array('gender', $cols)) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `gender` ENUM('male', 'female') NOT NULL DEFAULT 'male' AFTER `address`");
    echo "✓ Added 'gender' column to `users` table.\n";
}

echo "✓ Users table is fully ready for Student Profile System.\n";
