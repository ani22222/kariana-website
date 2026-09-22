<?php
namespace Core;

use PDO;
use PDOException;

/**
 * High-Security PDO Database Singleton
 * Configured with full UTF8MB4 charset, strict prepared statements, and error handling
 */
class Database
{
    private static ?PDO $instance = null;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {}

    /**
     * Retrieve the singleton PDO database connection
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $configFile = dirname(__DIR__) . '/config/database.php';
            if (!file_exists($configFile)) {
                throw new \RuntimeException("Database configuration file not found at: {$configFile}");
            }

            $config = require $configFile;

            $host = $config['host'] ?? 'localhost';
            $port = $config['port'] ?? 3306;
            $dbname = $config['database'] ?? 'kariana_portal';
            $charset = $config['charset'] ?? 'utf8mb4';
            $username = $config['username'] ?? 'root';
            $password = $config['password'] ?? '';

            $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, $username, $password, $options);
            } catch (PDOException $e) {
                // Log and throw user-friendly error without leaking sensitive credentials
                error_log("Database Connection Error: " . $e->getMessage());
                throw new \RuntimeException("ডাটাবেজ সংযোগে ত্রুটি ঘটেছে। দয়া করে ডাটাবেজ কনফিগারেশন পরীক্ষা করুন: " . $e->getMessage(), (int)$e->getCode(), $e);
            }
        }

        return self::$instance;
    }

    /**
     * Alias for getInstance()
     */
    public static function getConnection(): PDO
    {
        return self::getInstance();
    }

    /**
     * Reset connection instance (useful for migrations/testing)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
