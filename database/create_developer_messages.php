<?php
require_once __DIR__ . '/../core/Autoloader.php';
\Core\Autoloader::register();
$db = \Core\Database::getInstance();

$sql = "
CREATE TABLE IF NOT EXISTS `developer_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_name` VARCHAR(191) NOT NULL DEFAULT 'মাওলানা সাদ্দাম হোসেন',
    `sender_phone` VARCHAR(50) NULL DEFAULT '01717056816',
    `subject` VARCHAR(255) NULL,
    `message` TEXT NOT NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

$db->exec($sql);
echo "SUCCESS: developer_messages table ready.\n";
