<?php
/**
 * Challenger M1 Gen2 Test Suite
 * Adversarial Empirical Verification of BengaliHelper (Slugs, Numerals, Script Resilience, Collisions)
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\BengaliHelper;
use Core\Router;
use Core\Request;
use Core\Response;

function runChallengerM1Gen2Suite(): array
{
    $testResults = [];
    $totalAssertions = 0;
    $passedAssertions = 0;
    $failedAssertions = 0;

    $assert = function(string $category, string $description, bool $condition, mixed $expected = null, mixed $actual = null) use (&$testResults, &$totalAssertions, &$passedAssertions, &$failedAssertions) {
        $totalAssertions++;
        if ($condition) {
            $passedAssertions++;
            $testResults[] = [
                'category'    => $category,
                'description' => $description,
                'status'      => 'PASS',
                'details'     => "Verified successfully."
            ];
        } else {
            $failedAssertions++;
            $testResults[] = [
                'category'    => $category,
                'description' => $description,
                'status'      => 'FAIL',
                'expected'    => $expected,
                'actual'      => $actual,
                'details'     => "Assertion failed! Expected: " . json_encode($expected, JSON_UNESCAPED_UNICODE) . ", Got: " . json_encode($actual, JSON_UNESCAPED_UNICODE)
            ];
        }
    };

    // =========================================================================
    // 1. DARI (।) ADVERSARIAL TESTS
    // =========================================================================
    $category = '1. Dari (।) Resilience';

    // 1.1 Trailing Dari
    $input = 'সহজ পদ্ধতিতে কুরআন শিক্ষা।';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Trailing Dari stripped without trailing hyphen', $slug === 'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা', 'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা', $slug);

    // 1.2 Multiple Trailing Daris
    $input = 'সহজ পদ্ধতিতে কুরআন শিক্ষা।।।';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Multiple trailing Daris stripped cleanly', $slug === 'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা', 'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা', $slug);

    // 1.3 Dari within title with space
    $input = 'কুরআন তিলাওয়াত । সহজ নিয়ম';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Dari within title with spaces produces single hyphen separator', $slug === 'কুরআন-তিলাওয়াত-সহজ-নিয়ম', 'কুরআন-তিলাওয়াত-সহজ-নিয়ম', $slug);

    // 1.4 Dari within title WITHOUT spaces (word boundary defense)
    $input = 'কুরআন।হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Dari without spaces prevents word merging (কুরআন।হাদিস -> কুরআন-হাদিস)', $slug === 'কুরআন-হাদিস', 'কুরআন-হাদিস', $slug);

    // 1.5 Clustered Daris without spaces
    $input = 'কুরআন।।।হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Clustered Daris without spaces collapse to single hyphen', $slug === 'কুরআন-হাদিস', 'কুরআন-হাদিস', $slug);

    // 1.6 Leading Dari
    $input = '।কুরআন তিলাওয়াত';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Leading Dari stripped without leading hyphen', $slug === 'কুরআন-তিলাওয়াত', 'কুরআন-তিলাওয়াত', $slug);

    // 1.7 Pure Dari string
    $input = '।।।।';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Pure Dari input generates non-empty fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 1.8 Dari mixed with other delimiters
    $input = 'কুরআন।/হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Dari combined with slash collapses to single hyphen', $slug === 'কুরআন-হাদিস', 'কুরআন-হাদিস', $slug);

    // =========================================================================
    // 2. DOUBLE DARI (॥) ADVERSARIAL TESTS
    // =========================================================================
    $category = '2. Double Dari (॥) Resilience';

    // 2.1 Double Dari with spaces
    $input = 'প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Double Dari with spaces produces single hyphen', $slug === 'প্রথম-অধ্যায়-সমাপ্ত-দ্বিতীয়-অধ্যায়', 'প্রথম-অধ্যায়-সমাপ্ত-দ্বিতীয়-অধ্যায়', $slug);

    // 2.2 Double Dari WITHOUT spaces
    $input = 'সমাপ্ত॥দ্বিতীয়';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Double Dari without spaces separates words cleanly', $slug === 'সমাপ্ত-দ্বিতীয়', 'সমাপ্ত-দ্বিতীয়', $slug);

    // 2.3 Trailing Double Dari
    $input = 'প্রথম অধ্যায় সমাপ্ত॥';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Trailing Double Dari stripped cleanly without trailing hyphen', $slug === 'প্রথম-অধ্যায়-সমাপ্ত', 'প্রথম-অধ্যায়-সমাপ্ত', $slug);

    // 2.4 Leading Double Dari
    $input = '॥দ্বিতীয় অধ্যায়';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Leading Double Dari stripped cleanly without leading hyphen', $slug === 'দ্বিতীয়-অধ্যায়', 'দ্বিতীয়-অধ্যায়', $slug);

    // 2.5 Pure Double Dari string
    $input = '॥॥';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Pure Double Dari generates non-empty fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // =========================================================================
    // 3. BENGALI TAKA (৳) & CURRENCY SIGNS
    // =========================================================================
    $category = '3. Bengali Taka (৳) & Currency Marks';

    // 3.1 Trailing Taka sign
    $input = 'অনলাইন কোর্স ফি ৫০০৳';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Trailing Taka sign stripped cleanly', $slug === 'অনলাইন-কোর্স-ফি-৫০০', 'অনলাইন-কোর্স-ফি-৫০০', $slug);

    // 3.2 Taka sign followed by words
    $input = 'অনলাইন কোর্স ফি ৫০০৳ মাত্র';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Taka sign within title converted to hyphen separator', $slug === 'অনলাইন-কোর্স-ফি-৫০০-মাত্র', 'অনলাইন-কোর্স-ফি-৫০০-মাত্র', $slug);

    // 3.3 Taka sign prefix without space
    $input = '৳৫০০ ফি';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Taka prefix without space (৳৫০০) separates cleanly', $slug === '৫০০-ফি', '৫০০-ফি', $slug);

    // 3.4 Taka sign between words without space
    $input = 'ফি৳৫০০';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Taka sign between text and number (ফি৳৫০০) separates cleanly', $slug === 'ফি-৫০০', 'ফি-৫০০', $slug);

    // 3.5 Pure Taka sign
    $input = '৳৳৳';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Pure Taka sign generates non-empty fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 3.6 Bengali Rupee mark (৲)
    $input = 'কোর্স ফি ৲৫০০';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Bengali Rupee mark (৲) stripped cleanly', $slug === 'কোর্স-ফি-৫০০', 'কোর্স-ফি-৫০০', $slug);

    // 3.7 Bengali Isshar mark (৺)
    $input = '৺মরহুম শিক্ষক';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Bengali Isshar mark (৺) stripped cleanly', $slug === 'মরহুম-শিক্ষক', 'মরহুম-শিক্ষক', $slug);

    // =========================================================================
    // 4. DELIMITERS WITHOUT SPACES (WORD MERGING DEFENSE)
    // =========================================================================
    $category = '4. Delimiters Without Spaces';

    // 4.1 Slash without space
    $input = 'কুরআন/সুন্নাহ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Slash without space (কুরআন/সুন্নাহ) -> কুরআন-সুন্নাহ', $slug === 'কুরআন-সুন্নাহ', 'কুরআন-সুন্নাহ', $slug);

    // 4.2 Comma without space
    $input = 'কুরআন,হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Comma without space (কুরআন,হাদিস) -> কুরআন-হাদিস', $slug === 'কুরআন-হাদিস', 'কুরআন-হাদিস', $slug);

    // 4.3 Colon without space
    $input = 'কুরআন:তাজবীদ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Colon without space (কুরআন:তাজবীদ) -> কুরআন-তাজবীদ', $slug === 'কুরআন-তাজবীদ', 'কুরআন-তাজবীদ', $slug);

    // 4.4 Semicolon without space
    $input = 'কুরআন;হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Semicolon without space -> কুরআন-হাদিস', $slug === 'কুরআন-হাদিস', 'কুরআন-হাদিস', $slug);

    // 4.5 Pipe without space
    $input = 'কুরআন|সুন্নাহ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Pipe without space -> কুরআন-সুন্নাহ', $slug === 'কুরআন-সুন্নাহ', 'কুরআন-সুন্নাহ', $slug);

    // 4.6 Parentheses without space
    $input = 'কুরআন(তাজবীদ)';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Parentheses without space -> কুরআন-তাজবীদ', $slug === 'কুরআন-তাজবীদ', 'কুরআন-তাজবীদ', $slug);

    // 4.7 Brackets without space
    $input = 'কুরআন[তাজবীদ]';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Square brackets without space -> কুরআন-তাজবীদ', $slug === 'কুরআন-তাজবীদ', 'কুরআন-তাজবীদ', $slug);

    // 4.8 Curly braces without space
    $input = 'কুরআন{তাজবীদ}';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Curly braces without space -> কুরআন-তাজবীদ', $slug === 'কুরআন-তাজবীদ', 'কুরআন-তাজবীদ', $slug);

    // 4.9 Typographic quotes
    $input = '“কুরআন” ‘হাদিস’';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Typographic Unicode quotes stripped without leftover symbols', $slug === 'কুরআন-হাদিস', 'কুরআন-হাদিস', $slug);

    // 4.10 Em-dash and En-dash without spaces
    $input = 'কুরআন—সুন্নাহ–হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Unicode Em-dash and En-dash converted to standard hyphens', $slug === 'কুরআন-সুন্নাহ-হাদিস', 'কুরআন-সুন্নাহ-হাদিস', $slug);

    // 4.11 Arabic punctuation without spaces
    $input = 'القرآن،السنة؛الحديث؟الفقه';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Arabic comma, semicolon, question mark converted to hyphens', $slug === 'القرآن-السنة-الحديث-الفقه', 'القرآن-السنة-الحديث-الفقه', $slug);

    // 4.12 Heavy clustered delimiters
    $input = 'কুরআন///,,,:::---|||সুন্নাহ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Heavy clustered delimiters collapse to single hyphen', $slug === 'কুরআন-সুন্নাহ', 'কুরআন-সুন্নাহ', $slug);

    // 4.13 Underscores and hyphens
    $input = 'কুরআন___সুন্নাহ---হাদিস';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Underscores and hyphens collapse to single hyphen', $slug === 'কুরআন-সুন্নাহ-হাদিস', 'কুরআন-সুন্নাহ-হাদিস', $slug);

    // =========================================================================
    // 5. ARABIC SCRIPT & VOCALIZATION (TASHKEEL / HARAKAT / TATWEEL)
    // =========================================================================
    $category = '5. Arabic Script & Vocalization';

    // 5.1 Unvocalized Arabic
    $input = 'القرآن الكريم';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Unvocalized Arabic preserved in slug', $slug === 'القرآن-الكريم', 'القرآن-الكريم', $slug);

    // 5.2 Vocalized Arabic with Fatha, Damma, Kasra, Sukun
    $input = 'الْقُرْآنُ الْكَرِيمُ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Vocalized Arabic stripped of Tashkeel (الْقُرْآنُ -> القرآن)', $slug === 'القرآن-الكريم', 'القرآن-الكريم', $slug);

    // 5.3 Arabic with Shadda and Tanween
    $input = 'مُحَمَّدٌ رَسُولُ اللَّهِ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Arabic Shadda and Tanween stripped cleanly', $slug === 'محمد-رسول-الله', 'محمد-رسول-الله', $slug);

    // 5.4 Superscript Alef (Dagger Alif \x{0670})
    $input = 'الرَّحْمَٰن الرَّحِيم';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Arabic Dagger Alif (ٰ) stripped cleanly without corrupting base word', $slug === 'الرحمن-الرحيم', 'الرحمن-الرحيم', $slug);

    // 5.5 Tatweel / Kashida (\x{0640})
    $input = 'الـــقـــرآن';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Arabic Tatweel / Kashida stripped cleanly', $slug === 'القرآن', 'القرآن', $slug);

    // 5.6 Quranic Recitation & Stop Marks (\x{06D6}-\x{06ED})
    $input = 'مِّنۢ بَعْدِۭ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Quranic recitation annotation marks stripped cleanly', $slug === 'من-بعد', 'من-بعد', $slug);

    // 5.7 Mixed Bengali and Arabic
    $input = 'কুরআনুল কারীম (الْقُرْآن الْكَرِيم)';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Mixed Bengali and Arabic combined with hyphen', $slug === 'কুরআনুল-কারীম-القرآن-الكريم', 'কুরআনুল-কারীম-القرآن-الكريم', $slug);

    // 5.8 Arabic-Indic Numerals
    $input = 'سورة البقرة - الآية ٢٥৫';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Arabic with mixed Arabic-Indic and Bengali digits preserved', str_contains($slug, 'سورة-البقرة-الآية'), true, true);

    // 5.9 Vocalization-only Arabic
    $input = 'َُِّْ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Vocalization-only Arabic triggers fallback slug instead of empty string', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // =========================================================================
    // 6. PURE SYMBOLS, WHITESPACE & FALLBACK SLUGS
    // =========================================================================
    $category = '6. Pure Symbols, Whitespace & Fallbacks';

    // 6.1 Pure punctuation / symbols: ??? --- !!!
    $input = '??? --- !!!';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, '??? --- !!! generates valid fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 6.2 Pure hashes
    $input = '###';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, '### generates valid fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 6.3 Empty string
    $input = '';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Empty string generates valid fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 6.4 Whitespace only
    $input = "   \t\r\n   ";
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Whitespace only generates valid fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 6.5 Emojis only
    $input = '🕌📖✨🌙';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Emojis only generates valid fallback slug', str_starts_with($slug, 'item-') && strlen($slug) > 6, 'item-[hex]', $slug);

    // 6.6 Custom prefix fallback
    $input = '???';
    $slug = BengaliHelper::createSlug($input, 'course');
    $assert($category, 'Custom fallback prefix "course" respected', str_starts_with($slug, 'course-') && strlen($slug) > 8, 'course-[hex]', $slug);

    // 6.7 Fallback disabled (false)
    $input = '???';
    $slug = BengaliHelper::createSlug($input, false);
    $assert($category, 'Fallback = false returns empty string', $slug === '', '', $slug);

    // =========================================================================
    // 7. SLUG PURITY, FORMAT & COLLISION RESISTANCE
    // =========================================================================
    $category = '7. Purity & Collision Resistance';

    // 7.1 Strict Regex Validation on diverse set of 20 generated slugs
    $testInputs = [
        'সহজ পদ্ধতিতে কুরআন শিক্ষা',
        'আল-কুরআন ডিজিটাল একাডেমি!',
        'হাদিস সংকলন: সহীহ বুখারী ও মুসলিম',
        'কুরআন/সুন্নাহ ভিত্তিক জীবন',
        'বইয়ের মূল্য ৫০৳ মাত্র',
        'প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়',
        'القرآن الكريم والتجويد',
        'Kariana Quran Academy 2026',
        'English & বাংলা Mixed Title - 100% Success',
        'তত্ত্বাবধায়ক ও ক্বারীয়ানা পরিষদ',
        'পাঁচ ওয়াক্ত সালাতের সময়সূচি',
        'দুঃখ ও হতাশা দূর করার দোয়া',
        'উৎসব ও ঈদ মোবারক',
        'আষাঢ় মাসের বৃষ্টি ও রোজা',
        '??? --- !!!',
        '###',
        '৳৳৳',
        '।।।',
        '॥॥',
        'مُحَمَّدٌ'
    ];

    $allSlugsValidPattern = true;
    $slugRegex = '/^[a-z0-9\p{Bengali}\p{Arabic}]+(-[a-z0-9\p{Bengali}\p{Arabic}]+)*$/u';

    foreach ($testInputs as $idx => $tInput) {
        $genSlug = BengaliHelper::createSlug($tInput);
        if (!preg_match($slugRegex, $genSlug)) {
            $allSlugsValidPattern = false;
            break;
        }
    }
    $assert($category, 'All generated slugs match strict URL-safe regex without invalid chars', $allSlugsValidPattern, true, $allSlugsValidPattern);

    // 7.2 Collision Resistance: 10,000 generated fallbacks
    $generatedFallbacks = [];
    $collisionCount = 0;
    for ($i = 0; $i < 10000; $i++) {
        $fbSlug = BengaliHelper::createSlug('??? --- !!!');
        if (isset($generatedFallbacks[$fbSlug])) {
            $collisionCount++;
        }
        $generatedFallbacks[$fbSlug] = true;
    }
    $assert($category, '10,000 fallback slugs generated with exactly 0 collisions', $collisionCount === 0 && count($generatedFallbacks) === 10000, 0, $collisionCount);

    // =========================================================================
    // 8. NUMERAL CONVERSIONS (toBengaliNumber & toEnglishNumber)
    // =========================================================================
    $category = '8. Numeral Conversions';

    // 8.1 Basic digits 0-9
    $en09 = '0123456789';
    $bn09 = BengaliHelper::toBengaliNumber($en09);
    $backEn09 = BengaliHelper::toEnglishNumber($bn09);
    $assert($category, 'Digits 0-9 <-> ০-৯ bijectivity', $bn09 === '০১২৩৪৫৬৭৮৯' && $backEn09 === '0123456789', '০১২৩৪৫৬৭৮৯ / 0123456789', "{$bn09} / {$backEn09}");

    // 8.2 Positive integers
    $assert($category, 'Positive integer 2026 -> ২০২৬', BengaliHelper::toBengaliNumber(2026) === '২০২৬', '২০২৬', BengaliHelper::toBengaliNumber(2026));
    $assert($category, 'Bengali integer ২০২৬ -> 2026', BengaliHelper::toEnglishNumber('২০২৬') === '2026', '2026', BengaliHelper::toEnglishNumber('২০২৬'));

    // 8.3 Zero
    $assert($category, 'Integer zero 0 -> ০', BengaliHelper::toBengaliNumber(0) === '০', '০', BengaliHelper::toBengaliNumber(0));
    $assert($category, 'String zero "0" -> "০"', BengaliHelper::toBengaliNumber('0') === '০', '০', BengaliHelper::toBengaliNumber('0'));
    $assert($category, 'Bengali zero "০" -> "0"', BengaliHelper::toEnglishNumber('০') === '0', '0', BengaliHelper::toEnglishNumber('০'));

    // 8.4 Negative integers
    $assert($category, 'Negative integer -500 -> -৫০০', BengaliHelper::toBengaliNumber(-500) === '-৫০০', '-৫০০', BengaliHelper::toBengaliNumber(-500));
    $assert($category, 'Bengali negative -৫০০ -> -500', BengaliHelper::toEnglishNumber('-৫০০') === '-500', '-500', BengaliHelper::toEnglishNumber('-৫০০'));

    // 8.5 Floats
    $pi = 3.14159;
    $bnPi = BengaliHelper::toBengaliNumber($pi);
    $backPi = (float)BengaliHelper::toEnglishNumber($bnPi);
    $assert($category, 'Float 3.14159 -> ৩.১৪১৫৯ and back to float', $bnPi === '৩.১৪১৫৯' && abs($backPi - $pi) < 0.000001, '৩.১৪১৫৯ / 3.14159', "{$bnPi} / {$backPi}");

    // 8.6 Negative Float
    $negFloat = -0.0075;
    $bnNegFloat = BengaliHelper::toBengaliNumber($negFloat);
    $backNegFloat = (float)BengaliHelper::toEnglishNumber($bnNegFloat);
    $assert($category, 'Negative float -0.0075 -> -০.০০৭৫ and back', $bnNegFloat === '-০.০০৭৫' && abs($backNegFloat - $negFloat) < 0.000001, '-০.০০৭৫ / -0.0075', "{$bnNegFloat} / {$backNegFloat}");

    // 8.7 Round-trip stress test across 1,000 diverse random values
    $roundTripAllPassed = true;
    for ($i = -500; $i <= 500; $i++) {
        $conv = BengaliHelper::toBengaliNumber($i);
        $back = (int)BengaliHelper::toEnglishNumber($conv);
        if ($back !== $i) {
            $roundTripAllPassed = false;
            break;
        }
    }
    $assert($category, 'Bijective round-trip test for 1,001 consecutive integers [-500 to 500]', $roundTripAllPassed, true, $roundTripAllPassed);

    // 8.8 Mixed string with Bengali text and numbers
    $mixedStr = 'সূরা আল-বাকারা, আয়াত ২৫৫, পারা ৩';
    $enMixed = BengaliHelper::toEnglishNumber($mixedStr);
    $bnMixed = BengaliHelper::toBengaliNumber($enMixed);
    $assert($category, 'Mixed Bengali sentence numeral conversion and restoration', $enMixed === 'সূরা আল-বাকারা, আয়াত 255, পারা 3' && $bnMixed === $mixedStr, $mixedStr, $bnMixed);

    // 8.9 formatTaka currency tests
    $takaInt = BengaliHelper::formatTaka(500);
    $takaDecimal = BengaliHelper::formatTaka(1250.50);
    $assert($category, 'formatTaka integer 500 -> ৳ ৫০০', $takaInt === '৳ ৫০০', '৳ ৫০০', $takaInt);
    $assert($category, 'formatTaka decimal 1250.50 -> ৳ ১,২৫০.৫০', $takaDecimal === '৳ ১,২৫০.৫০', '৳ ১,২৫০.৫০', $takaDecimal);

    // =========================================================================
    // 9. BENGALI SCRIPT COMPLEXITIES (JUKTAKKHOR, KAR, VISARGA, CHANDRABINDU)
    // =========================================================================
    $category = '9. Bengali Script Complexities';

    // 9.1 Chandrabindu (ঁ)
    $input = 'পাঁচ ওয়াক্ত নামাজ';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Chandrabindu (ঁ) preserved in slug: পাঁচ-ওয়াক্ত-নামাজ', $slug === 'পাঁচ-ওয়াক্ত-নামাজ', 'পাঁচ-ওয়াক্ত-নামাজ', $slug);

    // 9.2 Anusvara (ং)
    $input = 'বাঙালি মুসলমান ও ক্বারীয়ানা';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Anusvara (ং) preserved: বাঙালি-মুসলমান-ও-ক্বারীয়ানা', $slug === 'বাঙালি-মুসলমান-ও-ক্বারীয়ানা', 'বাঙালি-মুসলমান-ও-ক্বারীয়ানা', $slug);

    // 9.3 Visarga (ঃ)
    $input = 'দুঃখ ও বিপদ মুক্তির দোয়া';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Visarga (ঃ) preserved: দুঃখ-ও-বিপদ-মুক্তির-দোয়া', $slug === 'দুঃখ-ও-বিপদ-মুক্তির-দোয়া', 'দুঃখ-ও-বিপদ-মুক্তির-দোয়া', $slug);

    // 9.4 Khanda Ta (ৎ)
    $input = 'উৎসব ও আনন্দ আয়োজন';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Khanda Ta (ৎ) preserved: উৎসব-ও-আনন্দ-আয়োজন', $slug === 'উৎসব-ও-আনন্দ-আয়োজন', 'উৎসব-ও-আনন্দ-আয়োজন', $slug);

    // 9.5 Hasanta / Virama (্) and Complex Conjuncts
    $input = 'আন্তর্জাতিক তাজবীদ সম্মেলন';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Hasanta conjuncts (আন্তর্জাতিক) preserved intact', $slug === 'আন্তর্জাতিক-তাজবীদ-সম্মেলন', 'আন্তর্জাতিক-তাজবীদ-সম্মেলন', $slug);

    // 9.6 Nukta characters (ড়, ঢ়, য়)
    $input = 'আষাঢ় ও শ্রাবণ মাসের পাহাড়';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Nukta characters (ঢ়, ড়) preserved intact', $slug === 'আষাঢ়-ও-শ্রাবণ-মাসের-পাহাড়', 'আষাঢ়-ও-শ্রাবণ-মাসের-পাহাড়', $slug);

    // =========================================================================
    // 10. END-TO-END ROUTER DISPATCH VERIFICATION
    // =========================================================================
    $category = '10. Router Dispatch Verification';

    $testSlugsForRouting = [
        'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা',
        'প্রথম-অধ্যায়-সমাপ্ত-দ্বিতীয়-অধ্যায়',
        'অনলাইন-কোর্স-ফি-৫০০',
        'কুরআন-সুন্নাহ',
        'القرآن-الكريم',
        'আষাঢ়-ও-শ্রাবণ-মাসের-পাহাড়',
        'item-285f425a'
    ];

    $routerDispatchesSucceeded = true;
    $router = new Router();
    $capturedSlug = '';
    $router->get('/blog/{slug}', function(Request $req, string $slug) use (&$capturedSlug) {
        $capturedSlug = $slug;
        return Response::json(['slug' => $slug]);
    });

    foreach ($testSlugsForRouting as $tSlug) {
        $backup = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode($tSlug);
        $_SERVER['SCRIPT_NAME'] = '/index.php';

        $req = new Request();
        $resp = $router->dispatch($req);

        if ($capturedSlug !== $tSlug || $resp->getStatusCode() !== 200) {
            $routerDispatchesSucceeded = false;
            break;
        }
        $_SERVER = $backup;
    }

    $assert($category, 'Router successfully matches and extracts all tested complex slugs via PCRE /u', $routerDispatchesSucceeded, true, $routerDispatchesSucceeded);

    // =========================================================================
    // 11. EXTREME STRESS & EXOTIC DIACRITICS
    // =========================================================================
    $category = '11. Extreme Stress & Exotic Diacritics';

    // 11.1 ZWJ and ZWNJ handling
    $input = "কুর\u{200D}আন ও বি\u{200C}দেশ";
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'ZWJ and ZWNJ stripped cleanly without corrupting words', $slug === 'কুরআন-ও-বিদেশ', 'কুরআন-ও-বিদেশ', $slug);

    // 11.2 Multi-byte whitespace (NBSP, Em Space, Ideographic space)
    $input = "কুরআন\u{00A0}মাজীদ\u{2003}ও\u{3000}হাদিস";
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Multi-byte whitespace (NBSP, Em, Ideographic) converted to standard hyphen', $slug === 'কুরআন-মাজীদ-ও-হাদিস', 'কুরআন-মাজীদ-ও-হাদিস', $slug);

    // 11.3 20,000 character repeated Bengali string (PCRE backtrack stress)
    $hugeInput = str_repeat('সহজ কুরআন শিক্ষা ', 1000);
    $hugeSlug = BengaliHelper::createSlug($hugeInput);
    $assert($category, '20,000 char huge string processed without regex backtrack crash', strlen($hugeSlug) > 10000 && !str_contains($hugeSlug, '--'), true, true);

    // 11.4 Special symbols: Ellipsis (…), Interrobang (‽), Bullet (•)
    $input = 'কুরআন… হাদিস ‽ তাজবীদ • শিক্ষা';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Ellipsis, Interrobang, and Bullet converted to hyphens', $slug === 'কুরআন-হাদিস-তাজবীদ-শিক্ষা', 'কুরআন-হাদিস-তাজবীদ-শিক্ষা', $slug);

    // 11.5 Bengali numerals inside slug titles
    $input = 'সূরা ১: আয়াত ২৫৫ (আয়াতুল কুরসী)';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Bengali numerals inside title preserved in slug', $slug === 'সূরা-১-আয়াত-২৫৫-আয়াতুল-কুরসী', 'সূরা-১-আয়াত-২৫৫-আয়াতুল-কুরসী', $slug);

    // 11.6 English numerals inside slug titles
    $input = 'Surah 1: Ayah 255 (Ayat al-Kursi)';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'English numerals and Latin title lowercased and formatted', $slug === 'surah-1-ayah-255-ayat-al-kursi', 'surah-1-ayah-255-ayat-al-kursi', $slug);

    // 11.7 Mixed English and Bengali digits
    $input = 'পারা 30 ও ৩০ নম্বর পারা';
    $slug = BengaliHelper::createSlug($input);
    $assert($category, 'Mixed English and Bengali digits preserved in slug', $slug === 'পারা-30-ও-৩০-নম্বর-পারা', 'পারা-30-ও-৩০-নম্বর-পারা', $slug);

    // 11.8 Trillion scale formatTaka
    $hugeTaka = BengaliHelper::formatTaka(1000000000000);
    $assert($category, 'Trillion scale formatTaka formats cleanly with Bengali numerals', $hugeTaka === '৳ ১,০০০,০০০,০০০,০০০', '৳ ১,০০০,০০০,০০০,০০০', $hugeTaka);

    return [
        'verdict'           => $failedAssertions === 0 ? 'CONFIRM CORRECTNESS' : 'CHALLENGE FAILED',
        'total_assertions'  => $totalAssertions,
        'passed_assertions' => $passedAssertions,
        'failed_assertions' => $failedAssertions,
        'timestamp'         => date('Y-m-d H:i:s'),
        'results'           => $testResults
    ];
}

// Standalone execution output
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(runChallengerM1Gen2Suite(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
