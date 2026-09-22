<?php
namespace Core;

/**
 * Bengali Language, Numeral, and Slug Utility Helper
 * Supports full Bengali Unicode block (\x{0980}-\x{09FF}) without transliteration loss
 */
class BengaliHelper
{
    private static array $enDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    private static array $bnDigits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];

    /**
     * Convert English digits (0-9) to Bengali digits (০-৯)
     */
    public static function toBengaliNumber(int|string|float $number): string
    {
        return str_replace(self::$enDigits, self::$bnDigits, (string)$number);
    }

    /**
     * Alias for toBengaliNumber
     */
    public static function en2bnNumber(int|string|float $number): string
    {
        return self::toBengaliNumber($number);
    }

    /**
     * Convert Bengali digits (০-৯) to English digits (0-9)
     */
    public static function toEnglishNumber(string $bengaliNumber): string
    {
        return str_replace(self::$bnDigits, self::$enDigits, $bengaliNumber);
    }

    /**
     * Alias for toEnglishNumber
     */
    public static function bn2enNumber(string $bengaliNumber): string
    {
        return self::toEnglishNumber($bengaliNumber);
    }

    /**
     * Create clean, SEO-friendly Unicode URL slug preserving Bengali and Arabic scripts
     * Supports full Bengali (\p{Bengali}), Arabic (\p{Arabic}), Latin alphanumeric, and hyphens.
     * Converts punctuation, delimiters, Dari (।), Double Dari (॥), and Taka (৳) into word separators (spaces).
     * Strips Arabic tashkeel/tatweel and prevents word concatenation across punctuation boundaries.
     * Provides fallback slug for symbol-only titles to prevent SQL unique constraint violations.
     *
     * @param string $title The raw title to slugify
     * @param string|bool $fallback Prefix for generated fallback slug if title yields empty string, or false to return empty
     * @return string Clean URL slug
     */
    public static function createSlug(string $title, string|bool $fallback = 'item'): string
    {
        // 1. Trim surrounding whitespace
        $title = trim($title);
        if ($title === '') {
            return self::generateFallbackSlug($fallback);
        }

        // 2. Strip Arabic Harakat (Tashkeel), Tatweel (Kashida), and Quranic recitation marks
        // \x{064B}-\x{065F}: Fathatan, Dammatan, Kasratan, Fatha, Damma, Kasra, Shadda, Sukun, etc.
        // \x{0670}: Superscript Alef (Dagger Alif)
        // \x{0640}: Tatweel (ـ)
        // \x{06D6}-\x{06ED}: Quranic stop signs and annotation marks
        $title = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}\x{06D6}-\x{06ED}]/u', '', $title);

        // 3. Convert all punctuation, delimiters, currency symbols, and brackets to spaces
        // This prevents word merging (e.g. "কুরআন/সুন্নাহ" -> "কুরআন সুন্নাহ" -> "কুরআন-সুন্নাহ")
        // Explicitly includes:
        // - Bengali Dari (। - \x{0964}) and Double Dari (॥ - \x{0965})
        // - Bengali Taka (৳ - \x{09F3}), Rupee (৲ - \x{09F2}), Isshar (৺ - \x{09FA}), currency marks (\x{09F4}-\x{09FB})
        // - Arabic comma (، - \x{060C}), semicolon (؛ - \x{061B}), question mark (؟ - \x{061F}), full stop (۔ - \x{06D4})
        // - Arabic symbols (٪ - \x{066A}, ٫ - \x{066B}, ٬ - \x{066C}, ٭ - \x{066D}, ۝ - \x{06DD})
        // - Unicode typographic dashes and quotes (—, –, ―, ‘, ’, “, ”, «, », •)
        // - All ASCII punctuation and symbols (/, \, |, :, ;, ,, ., (, ), [, ], {, }, <, >, ?, !, @, #, $, %, ^, &, *, +, =, ~, `, ", ', _)
        $delimiterPattern = '/[।॥৳৲৺\x{09F4}-\x{09FB}\x{060C}\x{061B}\x{061F}\x{06D4}\x{066A}\x{066B}\x{066C}\x{066D}\x{06DD}\x{2014}\x{2013}\x{2015}\x{2018}-\x{201D}\x{00AB}\x{00BB}\x{2022}\/\\\\|:;,\.\(\)\[\]{}<>?!@#$%^&*+=~`"\'_]+/u';
        $title = preg_replace($delimiterPattern, ' ', $title);

        // 4. Remove any remaining characters that are NOT Bengali, Arabic, Latin alphanumeric, whitespace, or hyphen
        // Note: \p{Bengali} covers letters, vowel signs (kar), hasant, nukta, and Bengali digits
        //       \p{Arabic} covers Arabic letters and Arabic-Indic digits
        $slug = preg_replace('/[^\p{Bengali}\p{Arabic}a-zA-Z0-9\s_-]/u', '', $title);

        // 5. Convert whitespace and underscores to single hyphen
        $slug = preg_replace('/[\s_]+/u', '-', $slug);

        // 6. Collapse multiple consecutive hyphens
        $slug = preg_replace('/-+/u', '-', $slug);

        // 7. Trim leading and trailing hyphens
        $slug = trim($slug, '-');

        // 8. Convert Latin characters to lowercase while preserving Unicode Bengali and Arabic
        $slug = mb_strtolower($slug, 'UTF-8');

        // 9. Fallback if slug collapsed to empty string (e.g. symbol-only titles like "??? --- !!!")
        if ($slug === '') {
            return self::generateFallbackSlug($fallback);
        }

        return $slug;
    }

    /**
     * Alias for createSlug
     */
    public static function slugify(string $title, string|bool $fallback = 'item'): string
    {
        return self::createSlug($title, $fallback);
    }

    /**
     * Helper to generate a unique fallback slug when input contains only symbols
     */
    private static function generateFallbackSlug(string|bool $fallback): string
    {
        if ($fallback === false) {
            return '';
        }
        $prefix = is_string($fallback) && $fallback !== '' ? $fallback : 'item';
        try {
            return $prefix . '-' . bin2hex(random_bytes(4));
        } catch (\Throwable) {
            return $prefix . '-' . substr(md5(uniqid((string)mt_rand(), true)), 0, 8);
        }
    }

    /**
     * Format a currency value in BDT with Bengali numerals
     */
    public static function formatTaka(float|int|string $amount): string
    {
        $num = number_format((float)$amount, 2, '.', ',');
        // remove trailing .00 if integer
        if (str_ends_with($num, '.00')) {
            $num = substr($num, 0, -3);
        }
        return '৳ ' . self::toBengaliNumber($num);
    }
}
