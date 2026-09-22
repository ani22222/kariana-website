<?php
namespace Core;

/**
 * Cryptographic Cross-Site Request Forgery (CSRF) Protection
 * Generates cryptographically secure 256-bit entropy tokens and validates via timing-attack safe hash_equals
 */
class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    /**
     * Retrieve or generate the current CSRF token
     */
    public static function token(): string
    {
        Session::start();
        $token = Session::get(self::SESSION_KEY);
        if (!$token || !is_string($token) || strlen($token) < 32) {
            $token = self::regenerate();
        }
        return $token;
    }

    /**
     * Alias for token() to get current CSRF token
     */
    public static function getToken(): string
    {
        return self::token();
    }

    /**
     * Regenerate a fresh CSRF token
     */
    public static function regenerate(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }

    /**
     * Validate a submitted token using timing-safe hash_equals
     */
    public static function validate(mixed $token): bool
    {
        if (!is_string($token) || empty($token)) {
            return false;
        }

        $sessionToken = self::token();
        return hash_equals($sessionToken, $token);
    }

    /**
     * Generate HTML hidden input field for forms
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Generate meta tag for AJAX headers
     */
    public static function meta(): string
    {
        $token = htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8');
        return '<meta name="csrf-token" content="' . $token . '">';
    }
}
