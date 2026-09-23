<?php
require_once __DIR__ . '/../core/Database.php';

try {
    $db = Core\Database::getInstance();
    $cols = $db->query('SHOW COLUMNS FROM `books`')->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('director_price', $cols)) {
        $db->exec("ALTER TABLE `books` ADD COLUMN `director_price` DECIMAL(10,2) NULL AFTER `discount_price`");
        echo "✓ Added `director_price` to `books`\n";
    }

    if (!in_array('teacher_price', $cols)) {
        $db->exec("ALTER TABLE `books` ADD COLUMN `teacher_price` DECIMAL(10,2) NULL AFTER `director_price`");
        echo "✓ Added `teacher_price` to `books`\n";
    }

    // Set default wholesale discounts for books if null
    $db->exec("
        UPDATE `books` 
        SET `director_price` = ROUND(COALESCE(`discount_price`, `price`) * 0.70, 2),
            `teacher_price`  = ROUND(COALESCE(`discount_price`, `price`) * 0.85, 2)
        WHERE `director_price` IS NULL OR `teacher_price` IS NULL
    ");
    echo "✓ Seeded default role-based pricing for all existing books.\n";

    // Also ensure book_orders table exists
    $db->exec("
        CREATE TABLE IF NOT EXISTS `book_orders` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `order_number` VARCHAR(50) NOT NULL UNIQUE,
            `book_id` INT UNSIGNED NULL,
            `customer_name` VARCHAR(150) NOT NULL,
            `customer_phone` VARCHAR(30) NOT NULL,
            `delivery_address` TEXT NOT NULL,
            `district` VARCHAR(100) NULL,
            `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
            `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `ordered_by_role` ENUM('public', 'teacher', 'director') NOT NULL DEFAULT 'public',
            `director_id` INT UNSIGNED NULL,
            `status` ENUM('pending', 'routed_to_director', 'central_courier', 'shipped', 'delivered', 'suspended', 'cancelled') NOT NULL DEFAULT 'pending',
            `admin_notes` TEXT NULL,
            `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_ord_num` (`order_number`),
            INDEX `idx_ord_status` (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "✓ Table `book_orders` verified.\n";
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
