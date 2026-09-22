<?php
namespace Core;

/**
 * Native PSR-4 Compliant Autoloader
 * Zero external Composer dependencies - 100% Shared Hosting ready
 */
class Autoloader
{
    /**
     * An associative array where key is a namespace prefix and value is base directory
     */
    private static array $prefixes = [];

    /**
     * Register autoloader with SPL
     */
    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'loadClass']);

        // Default namespace mappings
        $baseDir = dirname(__DIR__);
        self::addNamespace('Core', $baseDir . '/core');
        self::addNamespace('App', $baseDir . '/app');
    }

    /**
     * Add a base directory for a namespace prefix
     */
    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR;
        self::$prefixes[$prefix] = $baseDir;
    }

    /**
     * Loads the class file for a given class name
     */
    public static function loadClass(string $class): bool
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
        return false;
    }
}
