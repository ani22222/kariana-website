<?php
namespace Core;

/**
 * Hardened Session Manager
 * Includes session fixation defense, flash messaging, and authentication state
 */
class Session
{
    private static bool $started = false;

    /**
     * Start session with hardened cookie settings
     */
    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        // Hardened session parameters (only configurable before headers sent)
        if (!headers_sent()) {
            @ini_set('session.use_strict_mode', '1');
            @ini_set('session.use_only_cookies', '1');

            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        self::$started = true;

        // Initialize flash storage
        if (!isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash_next'] = [];
        }
        if (!isset($_SESSION['_flash_current'])) {
            $_SESSION['_flash_current'] = [];
        }
    }

    /**
     * Regenerate session ID to prevent session fixation attacks
     */
    public static function regenerate(bool $deleteOld = true): void
    {
        self::start();
        session_regenerate_id($deleteOld);
    }

    /**
     * Set a value in session
     */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from session
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a key exists in session
     */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a key from session
     */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Set a flash message for the subsequent request
     */
    public static function setFlash(string $key, mixed $message): void
    {
        self::start();
        $_SESSION['_flash_next'][$key] = $message;
    }

    /**
     * Unified flash getter / setter
     * - 1 argument: gets and clears the flash message (alias for getFlash)
     * - 2 arguments: sets the flash message for next request (alias for setFlash)
     */
    public static function flash(string $key, mixed $message = null): mixed
    {
        if ($message === null) {
            return self::getFlash($key);
        }
        self::setFlash($key, $message);
        return null;
    }

    /**
     * Get a flash message
     */
    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::start();
        if (isset($_SESSION['_flash_current'][$key])) {
            return $_SESSION['_flash_current'][$key];
        }
        if (isset($_SESSION['_flash_next'][$key])) {
            $val = $_SESSION['_flash_next'][$key];
            unset($_SESSION['_flash_next'][$key]);
            return $val;
        }
        return $default;
    }

    /**
     * Check if a flash message exists
     */
    public static function hasFlash(string $key): bool
    {
        self::start();
        return isset($_SESSION['_flash_current'][$key]) || isset($_SESSION['_flash_next'][$key]);
    }

    /**
     * Advance flash messages across requests (called on kernel boot)
     */
    public static function ageFlash(): void
    {
        self::start();
        $_SESSION['_flash_current'] = $_SESSION['_flash_next'] ?? [];
        $_SESSION['_flash_next'] = [];
    }

    /**
     * Authentication state helpers
     */
    public static function setUser(array $user): void
    {
        self::start();
        self::regenerate(true);
        $_SESSION['user'] = $user;
        $_SESSION['is_logged_in'] = true;
    }

    public static function getUser(): ?array
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function isLoggedIn(): bool
    {
        self::start();
        return !empty($_SESSION['is_logged_in']) && !empty($_SESSION['user']);
    }

    public static function logout(): void
    {
        self::start();
        unset($_SESSION['user'], $_SESSION['is_logged_in']);
        self::regenerate(true);
    }

    /**
     * Destroy entire session
     */
    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
            self::$started = false;
        }
    }
}
