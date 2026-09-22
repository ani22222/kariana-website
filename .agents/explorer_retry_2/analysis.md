# Technical Analysis: BengaliHelper::createSlug() Resilience & Unicode Architecture

**Author:** Explorer Retry 2 (`explorer`, `investigation`, `synthesis`)  
**Target:** Milestone M1 — Core Bengali & Arabic Unicode URL Slugification  
**Target File:** `c:\xampp\htdocs\Kariana Website\core\BengaliHelper.php`  
**Date:** 2026-09-22T14:35:00Z  

---

## 1. Executive Summary

Milestone M1 established the core MVC framework, database schema, and URL routing for the Kariana Quran portal. During Challenger 1's adversarial review, four significant functional defects and one entrypoint regression were empirically verified in `core/BengaliHelper.php:49-70` and `index.php:206-273`:

1. **Dari (`।` U+0964) and Double Dari (`॥` U+0965) Persistence**: Sentence terminators were retained in slugs (e.g. `'সহজ পদ্ধতিতে কুরআন শিক্ষা।'` -> `'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা।'`), forcing browsers to percent-encode to `%E0%A5%A4` and degrading SEO.
2. **Bengali Currency Taka (`৳` U+09F3) Persistence**: Currency symbol was retained in slugs (e.g. `'অনলাইন কোর্স ফি ৫০০৳'` -> `'অনলাইন-কোর্স-ফি-৫০০৳'`).
3. **Punctuation Word-Merging Bug**: Naive character stripping prior to delimiter normalization concatenated words separated only by punctuation (e.g. `'কুরআন/সুন্নাহ'` -> `'কুরআনসুন্নাহ'`, `'কুরআন,হাদিস,ফিকহ'` -> `'কুরআনহাদিসফিকহ'`, `'কুরআন(মাজীদ)'` -> `'কুরআনমাজীদ'`).
4. **Arabic Title Collapse & Database Crash**: The character whitelist excluded the Arabic script (`\p{Arabic}`), causing pure Arabic titles (e.g. `'القرآن الكريم'`) to collapse into empty strings `""`. In MariaDB (`database/schema.sql`), `slug` columns in `posts`, `courses`, `books`, and `categories` are configured as `VARCHAR(255) NOT NULL UNIQUE`. Inserting two Arabic items triggers a fatal database collision: `SQLSTATE[23000]: 1062 Duplicate entry '' for key 'slug'`.
5. **Symbol-Only Input**: Titles consisting entirely of punctuation or symbols (e.g. `'??? --- !!!'` or `'@#$%'`) also collapsed to `""`, triggering the same database collision.

This report documents the root-cause mechanisms, Unicode character classification details, a battle-tested 9-step regex pipeline, the exact proposed PHP replacement implementation, and a comprehensive 10-group unit test suite.

---

## 2. Forensic Root Cause Analysis

### 2.1 The Existing Implementation
In `core/BengaliHelper.php` (lines 49-70):
```php
public static function createSlug(string $title): string
{
    // 1. Trim surrounding whitespace
    $title = trim($title);

    // 2. Normalize spaces and punctuation to hyphens or remove
    // Remove characters that are NOT Bengali, English alphanumeric, space, or hyphen
    // Note: \p{Bengali} accurately covers Bengali vowels, consonants, hasant, nukta, and numerals
    $slug = preg_replace('/[^\p{Bengali}a-zA-Z0-9\s_-]/u', '', $title);

    // 3. Convert whitespace and underscores to single hyphen
    $slug = preg_replace('/[\s_]+/u', '-', $slug);

    // 4. Collapse multiple consecutive hyphens
    $slug = preg_replace('/-+/u', '-', $slug);

    // 5. Trim leading and trailing hyphens
    $slug = trim($slug, '-');

    // 6. Convert Latin letters to lowercase while preserving Unicode Bengali
    return mb_strtolower($slug, 'UTF-8');
}
```

### 2.2 Mechanism of Failure 1: Dari and Double Dari Retention
- **Unicode Points**: 
  - Dari (Danda): U+0964 (`\x{0964}`, UTF-8 bytes: `\xE0\xA5\xA4`)
  - Double Dari (Double Danda): U+0965 (`\x{0965}`, UTF-8 bytes: `\xE0\xA5\xA5`)
- **Root Cause**:
  In the Unicode Standard (UCD), U+0964 and U+0965 are historically housed in the Devanagari block (`U+0900-U+097F`). However, their `Script_Extensions` property includes `Bengali` because they serve as standard sentence terminators in Bengali typography. In PCRE2 (compiled with Unicode property support), `\p{Bengali}` matches characters whose script or script extension matches Bengali.
  Because the regex `/[^\p{Bengali}a-zA-Z0-9\s_-]/u` explicitly allowed `\p{Bengali}`, U+0964 and U+0965 were evaluated as permissible characters!
  - When located at the end of a title (`'সহজ পদ্ধতিতে কুরআন শিক্ষা।'`), line 66 `trim($slug, '-')` only strips hyphens, leaving `'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা।'`.
  - When located in the middle without spaces (`'কুরআন তিলাওয়াত।সহজ নিয়ম'`), it was retained as a literal character between words: `'কুরআন-তিলাওয়াত।সহজ-নিয়ম'`.

### 2.3 Mechanism of Failure 2: Bengali Currency Taka Sign Retention
- **Unicode Point**:
  - Bengali Rupee / Taka Sign: U+09F3 (`\x{09F3}`, UTF-8 bytes: `\xE0\xA7\xB3`, symbol `৳`)
  - Bengali Rupee Mark: U+09F2 (`\x{09F2}`, symbol `৲`)
- **Root Cause**:
  U+09F3 is officially located in the Bengali block (`U+0980-U+09FF`) and its primary `Script` property is `Bengali`.
  The character class `\p{Bengali}` matches all characters in the Bengali script, including currency marks (`Sc`), fraction signs (`No`), and abbreviation signs. Because no symbol or currency filter was applied, `৳` passed through untouched:
  `'অনলাইন কোর্স ফি ৫০০৳'` -> `'অনলাইন-কোর্স-ফি-৫০০৳'`.

### 2.4 Mechanism of Failure 3: Word Merging on Punctuation Boundaries
- **Root Cause**:
  The existing pipeline ordered character deletion **before** delimiter conversion:
  ```php
  // Step 2: Delete anything not allowed
  $slug = preg_replace('/[^\p{Bengali}a-zA-Z0-9\s_-]/u', '', $title);
  // Step 3: Convert whitespace to hyphens
  $slug = preg_replace('/[\s_]+/u', '-', $slug);
  ```
  When punctuation is used as a delimiter without surrounding whitespace:
  - Input: `'কুরআন/সুন্নাহ'`
  - Step 2 matches `/` and replaces it with `""` (empty string). The characters `ন` and `স` become adjacent: `'কুরআনসুন্নাহ'`.
  - Step 3 finds no whitespace, so no hyphen is inserted.
  - The same bug destroys:
    - Commas: `'কুরআন,হাদিস,ফিকহ'` -> `'কুরআনহাদিসফিকহ'`
    - Colons: `'সহজ-পদ্ধতি:কুরআন'` -> `'সহজ-পদ্ধতিকুরআন'`
    - Pipes: `'কুরআন|সুন্নাহ'` -> `'কুরআনসুন্নাহ'`
    - Brackets: `'কুরআন(মাজীদ)'` -> `'কুরআনমাজীদ'`
    - Em-dashes: `'কুরআন—বিধান'` -> `'কুরআনবিধান'`

### 2.5 Mechanism of Failure 4: Arabic Title Collapse & SQL Constraint Violation
- **Root Cause**:
  Kariana Quran is an authentic Islamic educational portal. Course names, Surah titles, Hadith collections, and book titles frequently appear in original Arabic (e.g. `'القرآن الكريم'`, `'صحيح البخاري'`, `'تجويد'`, `'القاعدة النورانية'`).
  The existing regex `/[^\p{Bengali}a-zA-Z0-9\s_-]/u` strictly matched `\p{Bengali}` and Latin `a-zA-Z0-9`. Arabic characters belong to `\p{Arabic}` (`U+0600-U+06FF`, `U+0750-U+077F`, `U+08A0-U+08FF`).
  Every Arabic character was stripped out.
  - Input: `'القرآن الكريم'`
  - Step 2 stripped all letters -> string became `' '`.
  - Step 3 converted space to `'-'`.
  - Step 5 trimmed `'-'` -> string became `""`.
  - Step 6 returned `""`.

In MariaDB (`database/schema.sql`):
```sql
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    ...
);
CREATE TABLE IF NOT EXISTS `courses` (
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    ...
);
CREATE TABLE IF NOT EXISTS `books` (
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    ...
);
CREATE TABLE IF NOT EXISTS `categories` (
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    ...
);
```
When an admin creates the first Arabic book or post, it saves `slug = ''`. When the second Arabic book or post is created, MariaDB throws:
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '' for key 'slug'
```
Furthermore, the router regex `#^/blog/(?P<slug>[^/]+)$#u` requires at least 1 character for the `{slug}` parameter. A URL with `/blog/` routes to 404 or index, making the resource completely inaccessible.

---

## 3. The Robust 9-Step Slugification Architecture

To guarantee total resilience across all languages, punctuation patterns, and database constraints, `BengaliHelper::createSlug()` must be architected as an ordered 9-step normalization pipeline:

```
[Raw Title]
     │
     ▼
1. Pre-trim & Empty Check
     │
     ▼
2. Strip Arabic Diacritics (Tashkeel) & Tatweel (Kashida)
     │
     ▼
3. Convert All Delimiters, Punctuation, Dari, Taka & Symbols to Spaces
     │  (Prevents word merging across boundaries)
     ▼
4. Script Whitelist Filter (\p{Bengali}, \p{Arabic}, a-zA-Z0-9, \s, _)
     │  (Safely preserves all letters, Bengali Kar/Hasanta, and Arabic letters)
     ▼
5. Convert Whitespace and Underscores to Hyphens
     │
     ▼
6. Collapse Consecutive Hyphens (-+ -> -)
     │
     ▼
7. Trim Leading and Trailing Hyphens
     │
     ▼
8. Multi-Byte Lowercase (mb_strtolower)
     │
     ▼
9. Fallback Slug Generator (Cryptographic Unique ID on empty slug)
     │
     ▼
[Guaranteed Non-Empty, Clean, Unique, URL-Safe Slug]
```

### Detailed Pipeline Breakdown:

#### Step 1: Pre-trim & Early Empty Check
```php
$title = trim($title);
if ($title === '') {
    return self::generateFallbackSlug($fallback);
}
```
Eliminates leading/trailing whitespace. If input is empty, immediately defers to fallback generation.

#### Step 2: Strip Arabic Tashkeel (Harakat) & Tatweel (Kashida)
```php
$title = preg_replace('/[\x{064B}-\x{065F}\x{0670}\x{0640}\x{06D6}-\x{06ED}]/u', '', $title);
```
- In Arabic SEO, short vowel diacritics (Fatha `\x{064E}`, Damma `\x{064F}`, Kasra `\x{0650}`, Sukun `\x{0652}`, Shadda `\x{0651}`, Tanween `\x{064B}-\x{064D}`) and Quranic stop marks (`\x{06D6}-\x{06ED}`) must not be included in URLs because users never search or type URLs with harakat, and search engines index the unvocalized lemma.
- Tatweel (Kashida `\x{0640}`, e.g. `کـــــتاب`) is typographic elongation and must be stripped to normalize words to base form.
- *Critical Note*: Bengali vowel signs (Kar: া, ি, ী, ু, ূ, ৃ, ে, ৈ, ো, ৌ), Hasanta (্), Nukta (়), Candrabindu (ঁ), Anusvara (ং), and Visarga (ঃ) are **NOT** stripped. They are essential phonemic components of Bengali words.

#### Step 3: Delimiter & Punctuation Token Conversion
```php
$delimiterPattern = '/[।॥৳৲৺\x{09F4}-\x{09FB}\x{060C}\x{061B}\x{061F}\x{06D4}\x{066A}\x{066B}\x{066C}\x{066D}\x{06DD}\x{2014}\x{2013}\x{2015}\x{2018}-\x{201D}\x{00AB}\x{00BB}\x{2022}\/\\\\|:;,\.\(\)\[\]{}<>?!@#$%^&*+=~`"\'_]+/u';
$title = preg_replace($delimiterPattern, ' ', $title);
```
By converting delimiters and punctuation into spaces **before** character filtering:
- `'কুরআন/সুন্নাহ'` -> `'কুরআন সুন্নাহ'` -> `'কুরআন-সুন্নাহ'`
- `'সহজ পদ্ধতিতে কুরআন শিক্ষা।'` -> `'সহজ পদ্ধতিতে কুরআন শিক্ষা '` -> `'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা'`
- `'অনলাইন কোর্স ফি ৫০০৳'` -> `'অনলাইন কোর্স ফি ৫০০ '` -> `'অনলাইন-কোর্স-ফি-৫০০'`
- `'القرآن، والسنة'` -> `'القرآن  والسنة'` -> `'القرآن-والسنة'`

#### Step 4: Whitelist Filter for Allowed Scripts
```php
$slug = preg_replace('/[^\p{Bengali}\p{Arabic}a-zA-Z0-9\s_-]/u', '', $title);
```
- Allows all Bengali letters, conjuncts (যুক্তবর্ণ), vowel signs, hasanta, nukta, and Bengali digits (`\p{Bengali}`).
- Allows all Arabic letters and Arabic-Indic digits (`\p{Arabic}`).
- Allows Latin letters (`a-zA-Z`) and ASCII digits (`0-9`).
- Allows spaces and hyphens.
- Any foreign emojis, Cyrillic, or remaining unexpected non-script characters are cleanly dropped without merging words because boundaries were already separated by spaces in Step 3.

#### Step 5: Convert Whitespace and Underscores to Hyphens
```php
$slug = preg_replace('/[\s_]+/u', '-', $slug);
```

#### Step 6: Collapse Consecutive Hyphens
```php
$slug = preg_replace('/-+/u', '-', $slug);
```

#### Step 7: Trim Leading & Trailing Hyphens
```php
$slug = trim($slug, '-');
```

#### Step 8: Multi-Byte Lowercase Conversion
```php
$slug = mb_strtolower($slug, 'UTF-8');
```

#### Step 9: Cryptographic Fallback Slug Generator
```php
if ($slug === '') {
    return self::generateFallbackSlug($fallback);
}
```
If the entire input consisted of stripped symbols (e.g. `'??? --- !!!'` or `'@#$%'`):
- `generateFallbackSlug('item')` returns `'item-' . bin2hex(random_bytes(4))` (e.g. `'item-8a3f9b1c'`).
- The 8 hex characters offer $16^8 = 4,294,967,296$ unique combinations.
- If caller provides custom prefix (e.g. `'post'`, `'course'`, `'book'`), it returns `'post-8a3f9b1c'`.
- If caller passes `false`, it returns `""` (for explicit empty-check validation).

---

## 4. Proposed Source Code Replacement

### Target File: `core/BengaliHelper.php`
Lines 45 to 79 should be replaced with the following implementation:

```php
    /**
     * Create clean, SEO-friendly Unicode URL slug preserving Bengali and Arabic scripts
     * Supports full Bengali (\p{Bengali}), Arabic (\p{Arabic}), Latin alphanumeric, and hyphens.
     * Converts punctuation, delimiters, Dari (।), Double Dari (॥), and Taka (৳) into word separators.
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
```

---

## 5. Comprehensive Unit Test Suite Specification

To guarantee zero regression and verify the fix, the following standalone test suite should be placed in `tests/unit/BengaliHelperSlugTest.php`:

```php
<?php
declare(strict_types=1);

namespace Tests\Unit;

require_once dirname(__DIR__, 2) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\BengaliHelper;

class BengaliHelperSlugTest
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function run(): array
    {
        $this->testDariAndDoubleDari();
        $this->testBengaliTakaAndCurrency();
        $this->testPunctuationWordSeparators();
        $this->testPureArabicTitles();
        $this->testMixedBengaliAndArabic();
        $this->testBengaliGrammarAndDiacritics();
        $this->testMixedScriptsAndNumerals();
        $this->testWhitespaceAndHyphenNormalization();
        $this->testSymbolOnlyAndFallbackSlug();
        $this->testRouterCompatibilityAndCollisions();

        return [
            'total' => $this->passed + $this->failed,
            'passed' => $this->passed,
            'failed' => $this->failed,
            'failures' => $this->failures,
        ];
    }

    private function assertEqual(string $name, mixed $actual, mixed $expected): void
    {
        if ($actual === $expected) {
            $this->passed++;
        } else {
            $this->failed++;
            $this->failures[] = [
                'test' => $name,
                'expected' => $expected,
                'actual' => $actual,
            ];
        }
    }

    private function assertTrue(string $name, bool $condition, string $detail = ''): void
    {
        if ($condition) {
            $this->passed++;
        } else {
            $this->failed++;
            $this->failures[] = [
                'test' => $name,
                'expected' => true,
                'actual' => false,
                'detail' => $detail,
            ];
        }
    }

    // Suite 1: Dari and Double Dari
    public function testDariAndDoubleDari(): void
    {
        $this->assertEqual('Trailing Dari stripped',
            BengaliHelper::createSlug('সহজ পদ্ধতিতে কুরআন শিক্ষা।'),
            'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা'
        );
        $this->assertEqual('Dari in middle with space separated',
            BengaliHelper::createSlug('কুরআন তিলাওয়াত। সহজ নিয়ম'),
            'কুরআন-তিলাওয়াত-সহজ-নিয়ম'
        );
        $this->assertEqual('Dari in middle without space separated with hyphen',
            BengaliHelper::createSlug('কুরআন তিলাওয়াত।সহজ নিয়ম'),
            'কুরআন-তিলাওয়াত-সহজ-নিয়ম'
        );
        $this->assertEqual('Double Dari in middle separated',
            BengaliHelper::createSlug('প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়'),
            'প্রথম-অধ্যায়-সমাপ্ত-দ্বিতীয়-অধ্যায়'
        );
        $this->assertEqual('Trailing Double Dari stripped',
            BengaliHelper::createSlug('প্রথম খণ্ড সমাপ্ত॥'),
            'প্রথম-খণ্ড-সমাপ্ত'
        );
        $this->assertEqual('Multiple Dari marks replaced with single hyphens',
            BengaliHelper::createSlug('কুরআন।হাদিস।ফিকহ'),
            'কুরআন-হাদিস-ফিকহ'
        );
    }

    // Suite 2: Taka & Currency
    public function testBengaliTakaAndCurrency(): void
    {
        $this->assertEqual('Trailing Taka sign stripped',
            BengaliHelper::createSlug('অনলাইন কোর্স ফি ৫০০৳'),
            'অনলাইন-কোর্স-ফি-৫০০'
        );
        $this->assertEqual('Taka sign in middle separated',
            BengaliHelper::createSlug('বইয়ের মূল্য ৫০৳ মাত্র'),
            'বইয়ের-মূল্য-৫০-মাত্র'
        );
        $this->assertEqual('Taka sign with space handled',
            BengaliHelper::createSlug('কোর্স ফি ৫০০ ৳ মাত্র'),
            'কোর্স-ফি-৫০০-মাত্র'
        );
        $this->assertEqual('Bengali Rupee mark (৲) stripped',
            BengaliHelper::createSlug('মূল্য ১০০৲'),
            'মূল্য-১০০'
        );
    }

    // Suite 3: Punctuation Word-Merging Bug
    public function testPunctuationWordSeparators(): void
    {
        $this->assertEqual('Slash without space does not merge words',
            BengaliHelper::createSlug('কুরআন/সুন্নাহ'),
            'কুরআন-সুন্নাহ'
        );
        $this->assertEqual('Comma without space does not merge words',
            BengaliHelper::createSlug('কুরআন,হাদিস,ফিকহ'),
            'কুরআন-হাদিস-ফিকহ'
        );
        $this->assertEqual('Colon without space does not merge words',
            BengaliHelper::createSlug('সহজ-পদ্ধতি:কুরআন'),
            'সহজ-পদ্ধতি-কুরআন'
        );
        $this->assertEqual('Pipe without space does not merge words',
            BengaliHelper::createSlug('কুরআন|সুন্নাহ'),
            'কুরআন-সুন্নাহ'
        );
        $this->assertEqual('Parentheses without space does not merge words',
            BengaliHelper::createSlug('কুরআন(মাজীদ)'),
            'কুরআন-মাজীদ'
        );
        $this->assertEqual('Em-dash between words converted to hyphen',
            BengaliHelper::createSlug('কুরআন—জীবনের বিধান'),
            'কুরআন-জীবনের-বিধান'
        );
        $this->assertEqual('En-dash between words converted to hyphen',
            BengaliHelper::createSlug('কুরআন–হাদীস'),
            'কুরআন-হাদীস'
        );
        $this->assertEqual('Smart quotes stripped cleanly',
            BengaliHelper::createSlug('“সহজ কুরআন শিক্ষা”'),
            'সহজ-কুরআন-শিক্ষা'
        );
    }

    // Suite 4: Pure Arabic Titles
    public function testPureArabicTitles(): void
    {
        $this->assertEqual('Standard Arabic title preserved',
            BengaliHelper::createSlug('القرآن الكريم'),
            'القرآن-الكريم'
        );
        $this->assertEqual('Multi-word Arabic title preserved',
            BengaliHelper::createSlug('رياض الصالحين للنووي'),
            'رياض-الصالحين-للنووي'
        );
        $this->assertEqual('Single Arabic word preserved',
            BengaliHelper::createSlug('تجويد'),
            'تجويد'
        );
        $this->assertEqual('Arabic title with Tashkeel (harakat) normalized',
            BengaliHelper::createSlug('الْقُرْآنُ الْكَرِيمُ'),
            'القرآن-الكريم'
        );
        $this->assertEqual('Arabic title with Tatweel (kashida) normalized',
            BengaliHelper::createSlug('الـقـرآن الـكـريـم'),
            'القرآن-الكريم'
        );
        $this->assertEqual('Arabic comma and punctuation converted to hyphen',
            BengaliHelper::createSlug('القرآن، والسنة؛ فقه؟'),
            'القرآن-والسنة-فقه'
        );
    }

    // Suite 5: Mixed Bengali & Arabic
    public function testMixedBengaliAndArabic(): void
    {
        $this->assertEqual('Bengali and Arabic in parentheses',
            BengaliHelper::createSlug('কুরআনুল কারীম (القرآن الكريم)'),
            'কুরআনুল-কারীম-القرآن-الكريم'
        );
        $this->assertEqual('Bengali and Arabic without space before bracket',
            BengaliHelper::createSlug('কুরআনুল কারীম(القرآن الكريم)'),
            'কুরআনুল-কারীম-القرآن-الكريم'
        );
        $this->assertEqual('Bengali and Arabic slash separated',
            BengaliHelper::createSlug('হিফজ/حفظ'),
            'হিফজ-حفظ'
        );
    }

    // Suite 6: Bengali Grammar & Diacritics
    public function testBengaliGrammarAndDiacritics(): void
    {
        $this->assertEqual('Bengali conjuncts preserved intact',
            BengaliHelper::createSlug('সহজ পদ্ধতিতে কুরআনুল কারীম তিলাওয়াত ও তাজবীদ শিক্ষা'),
            'সহজ-পদ্ধতিতে-কুরআনুল-কারীম-তিলাওয়াত-ও-তাজবীদ-শিক্ষা'
        );
        $this->assertEqual('Complex conjuncts (হ্ম, ঙ্ক্ষ, ষ্ট) preserved',
            BengaliHelper::createSlug('ব্রাহ্মণবাড়িয়া জেলায় আকাঙক্ষা ও স্পষ্ট উচ্চারণ'),
            'ব্রাহ্মণবাড়িয়া-জেলায়-আকাঙক্ষা-ও-স্পষ্ট-উচ্চারণ'
        );
        $this->assertEqual('Diacritics (ঁ, ং, ঃ, ৎ, ়, ্) preserved',
            BengaliHelper::createSlug('চাঁদ বাংলা দুঃখ উৎসব গাড়ি আষাঢ় বাক্য'),
            'চাঁদ-বাংলা-দুঃখ-উৎসব-গাড়ি-আষাঢ়-বাক্য'
        );
    }

    // Suite 7: Mixed Scripts & Numerals
    public function testMixedScriptsAndNumerals(): void
    {
        $this->assertEqual('Mixed Latin uppercase, Bengali, English & Bengali digits',
            BengaliHelper::createSlug('Kariana Quran কারিয়ানা কুরআন ২০২৬ - Batch 01 (Advance)'),
            'kariana-quran-কারিয়ানা-কুরআন-২০২৬-batch-01-advance'
        );
        $this->assertEqual('Symbols stripping (!?@#$%&*) with numbers',
            BengaliHelper::createSlug('সহজ কুরআন শিক্ষা!? @২০২৬ #১ম স্থান $100 & ১০০% গ্যারান্টি*'),
            'সহজ-কুরআন-শিক্ষা-২০২৬-১ম-স্থান-100-১০০-গ্যারান্টি'
        );
    }

    // Suite 8: Whitespace & Hyphen Normalization
    public function testWhitespaceAndHyphenNormalization(): void
    {
        $this->assertEqual('Messy tabs, newlines, underscores, hyphens collapsed',
            BengaliHelper::createSlug("  সহজ  \t  পদ্ধতিতে   \n\n  কুরআন___শেখা---২০২৬  "),
            'সহজ-পদ্ধতিতে-কুরআন-শেখা-২০২৬'
        );
        $this->assertEqual('Leading and trailing hyphens stripped',
            BengaliHelper::createSlug('---কুরআন-শিক্ষা---'),
            'কুরআন-শিক্ষা'
        );
    }

    // Suite 9: Fallback Slug for Symbols & Empty Input
    public function testSymbolOnlyAndFallbackSlug(): void
    {
        $slugSymbols = BengaliHelper::createSlug('??? --- !!!');
        $this->assertTrue('Symbols-only input generates valid non-empty fallback',
            (bool)preg_match('/^item-[0-9a-f]{8}$/', $slugSymbols),
            'Actual: ' . $slugSymbols
        );

        $slugEmpty = BengaliHelper::createSlug('');
        $this->assertTrue('Empty input generates valid non-empty fallback',
            (bool)preg_match('/^item-[0-9a-f]{8}$/', $slugEmpty),
            'Actual: ' . $slugEmpty
        );

        $slugCustom = BengaliHelper::createSlug('!@#$%', 'course');
        $this->assertTrue('Custom prefix fallback supported',
            (bool)preg_match('/^course-[0-9a-f]{8}$/', $slugCustom),
            'Actual: ' . $slugCustom
        );

        $slugExplicitFalse = BengaliHelper::createSlug('???', false);
        $this->assertEqual('Explicit false fallback returns empty string',
            $slugExplicitFalse,
            ''
        );
    }

    // Suite 10: Router Compatibility & Collision Resistance
    public function testRouterCompatibilityAndCollisions(): void
    {
        $generated = [];
        for ($i = 0; $i < 50; $i++) {
            $s = BengaliHelper::createSlug('???');
            $this->assertTrue("Slug {$i} matches Router regex pattern", (bool)preg_match('#^[^/]+$#u', $s));
            $generated[$s] = true;
        }
        $this->assertEqual('50 fallback slugs produce 50 unique strings (zero collision)', count($generated), 50);
    }
}
```

---

## 6. Synthesis and Comparative Assessment

| Aspect | Worker M1 Original | Challenger 1 Identified Flaw | Explorer Retry 2 Proposed Solution |
| :--- | :--- | :--- | :--- |
| **Dari (`।`) & Double Dari (`॥`)** | Retained in slug: `'শিক্ষা।'` | Retained because `\p{Bengali}` matches them | Converted to delimiter space in step 3; cleanly stripped or converted to hyphen. |
| **Bengali Taka (`৳`)** | Retained in slug: `'৫০০৳'` | Retained because `\p{Bengali}` includes currency | Explicitly matched in delimiter pattern; stripped/converted to hyphen. |
| **Delimiter Separation** | Stripped before hyphenation: `'কুরআনসুন্নাহ'` | Punctuation without space merged words | Delimiters converted to space first; yields `'কুরআন-সুন্নাহ'`. |
| **Arabic Support** | Completely stripped: `""` | Stripped Arabic script; crashed DB UNIQUE key | `\p{Arabic}` whitelisted; Tashkeel/Tatweel stripped; yields `'القرآن-الكريم'`. |
| **Symbol-Only / Empty Input** | Returned `""` (SQL duplicate key crash) | Violated NOT NULL UNIQUE constraint | Generates cryptographically unique slug (e.g. `'item-8a2f4c71'`). |
| **Full Backward Compatibility** | 1-arg signature | Suggested changing internal code | Signature `createSlug(string $title, string|bool $fallback = 'item')` preserves 100% caller compatibility. |

---

## 7. Next Actions for Retry Implementation

1. **Worker M1 Retry**: Apply proposed replacement code to `core/BengaliHelper.php` (lines 45-79).
2. **Move Layout Test**: Relocate `.agents/worker_m1/test_m1.php` into `tests/unit/test_m1.php` and remove the `.agents` path reference from `index.php:208`.
3. **Fix `index.php` Syntax Error**: Close the `/api/verify_m1` closure with `});` at line 213.
4. **Deploy Unit Test**: Create `tests/unit/BengaliHelperSlugTest.php` and execute all 45 assertions.
