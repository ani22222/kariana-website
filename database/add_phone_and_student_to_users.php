<?php
require_once __DIR__ . '/../core/Autoloader.php';
\Core\Autoloader::register();

$db = \Core\Database::getInstance();

// Check columns in users table
$cols = $db->query("DESCRIBE `users`")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('phone', $cols)) {
    $db->exec("ALTER TABLE `users` ADD COLUMN `phone` VARCHAR(30) NULL AFTER `email`");
    echo "✓ Added 'phone' column to `users` table.\n";
}

// Modify role column to allow student / user
$db->exec("ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(50) NOT NULL DEFAULT 'user'");
echo "✓ Flexible 'role' column configured in `users` table.\n";

// Add index on phone if not present
try {
    $db->exec("CREATE INDEX `idx_users_phone` ON `users` (`phone`)");
} catch (\Throwable $e) {
    // index may already exist
}

echo "✓ Users table schema ready for universal authentication & student registration.\n";
