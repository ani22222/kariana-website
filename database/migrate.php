<?php
/**
 * Database Migration Runner
 * Creates database if not exists and imports schema.sql
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

$config = require dirname(__DIR__) . '/config/database.php';

$host     = $config['host'] ?? 'localhost';
$port     = $config['port'] ?? 3306;
$dbname   = $config['database'] ?? 'kariana_portal';
$username = $config['username'] ?? 'root';
$password = $config['password'] ?? '';
$charset  = $config['charset'] ?? 'utf8mb4';

echo "=== [কারিয়ানা কুরআন] ডাটাবেজ মাইগ্রেশন শুরু ===\n";
echo "হোস্ট: {$host}:{$port}\n";
echo "ডাটাবেজ: {$dbname}\n";

try {
    // 1. Initial connection without database selected to ensure DB creation
    $rootDsn = "mysql:host={$host};port={$port};charset={$charset}";
    $pdo = new PDO($rootDsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "MySQL সার্ভারের সাথে সংযোগ সফল হয়েছে।\n";

    // 2. Create Database if not exists
    $sqlCreateDb = "CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET {$charset} COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sqlCreateDb);
    echo "✓ ডাটাবেজ '{$dbname}' নিশ্চিত/তৈরি করা হয়েছে।\n";

    // 3. Switch to target database
    $pdo->exec("USE `{$dbname}`");

    // 4. Load and execute schema.sql
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new RuntimeException("schema.sql ফাইল পাওয়া যায়নি: {$schemaFile}");
    }

    $schemaSql = file_get_contents($schemaFile);
    if ($schemaSql === false) {
        throw new RuntimeException("schema.sql ফাইল পড়তে ব্যর্থ হয়েছে।");
    }

    echo "স্কিমা ফাইল কার্যকর করা হচ্ছে...\n";
    $pdo->exec($schemaSql);
    echo "✓ ১২টি টেবিল এবং ভিউ সফলভাবে তৈরি হয়েছে।\n";

    // 5. Record migration entry
    $stmt = $pdo->prepare("INSERT IGNORE INTO `migrations` (`migration`, `batch`, `executed_at`) VALUES (:migration, 1, NOW())");
    $stmt->execute(['migration' => '001_initial_schema']);
    echo "✓ মাইগ্রেশন লগ রেকর্ড করা হয়েছে: 001_initial_schema\n";

    echo "\n=== মাইগ্রেশন সফলভাবে সমাপ্ত হয়েছে! ===\n";
    exit(0);
} catch (Throwable $e) {
    echo "\n[ERROR] মাইগ্রেশন ব্যর্থ হয়েছে: " . $e->getMessage() . "\n";
    echo "ফাইল: " . $e->getFile() . " (লাইন: " . $e->getLine() . ")\n";
    exit(1);
}
