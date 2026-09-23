<?php
/**
 * Upgrade Directors Management Schema
 * Adds login_allowed, status_reason, admin_remarks, and ensures status supports all states
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

$config = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
    $config['username'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "Checking directors table schema...\n";

// 1. Modify status column to include 'inactive' if not already
try {
    $pdo->exec("ALTER TABLE `directors` MODIFY COLUMN `status` ENUM('active', 'suspended', 'expelled', 'inactive') NOT NULL DEFAULT 'active'");
    echo "✓ Status ENUM modified successfully.\n";
} catch (\Exception $e) {
    echo "Note on status column: " . $e->getMessage() . "\n";
}

// 2. Add login_allowed column
$cols = $pdo->query("SHOW COLUMNS FROM `directors` LIKE 'login_allowed'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE `directors` ADD COLUMN `login_allowed` TINYINT(1) NOT NULL DEFAULT 1 AFTER `status`");
    echo "✓ Added login_allowed column.\n";
} else {
    echo "• login_allowed column already exists.\n";
}

// 3. Add status_reason column
$cols = $pdo->query("SHOW COLUMNS FROM `directors` LIKE 'status_reason'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE `directors` ADD COLUMN `status_reason` TEXT NULL AFTER `login_allowed`");
    echo "✓ Added status_reason column.\n";
} else {
    echo "• status_reason column already exists.\n";
}

// 4. Add admin_remarks column
$cols = $pdo->query("SHOW COLUMNS FROM `directors` LIKE 'admin_remarks'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE `directors` ADD COLUMN `admin_remarks` TEXT NULL AFTER `status_reason`");
    echo "✓ Added admin_remarks column.\n";
} else {
    echo "• admin_remarks column already exists.\n";
}

echo "=== Directors table upgrade completed! ===\n";
