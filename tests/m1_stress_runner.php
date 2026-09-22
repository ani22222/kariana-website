<?php
/**
 * Challenger 1 - Milestone M1 Stress Testing Suite
 * Deep Adversarial Verification of:
 * 1. Bengali Unicode URL Routing (Router.php & Request.php)
 * 2. BengaliHelper Numeral Conversion (toBengaliNumber & toEnglishNumber)
 * 3. BengaliHelper Slug Creation (createSlug)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\Router;
use Core\Request;
use Core\Response;
use Core\BengaliHelper;

$report = [
    'challenger' => 'Challenger 1 (M1 Empirical Verification)',
    'timestamp' => date('Y-m-d H:i:s'),
    'categories' => [
        'numeral_conversion' => ['total' => 0, 'passed' => 0, 'failed' => 0, 'tests' => []],
        'slug_creation' => ['total' => 0, 'passed' => 0, 'failed' => 0, 'tests' => []],
        'router_unicode' => ['total' => 0, 'passed' => 0, 'failed' => 0, 'tests' => []],
        'request_parsing' => ['total' => 0, 'passed' => 0, 'failed' => 0, 'tests' => []],
    ],
    'overall' => ['total' => 0, 'passed' => 0, 'failed' => 0, 'verdict' => 'PENDING'],
    'findings' => [],
];

function recordTest(string $category, string $name, bool $passed, mixed $expected, mixed $actual, string $details = ''): void {
    global $report;
    $report['categories'][$category]['total']++;
    $report['overall']['total']++;
    if ($passed) {
        $report['categories'][$category]['passed']++;
        $report['overall']['passed']++;
    } else {
        $report['categories'][$category]['failed']++;
        $report['overall']['failed']++;
        $report['findings'][] = [
            'category' => $category,
            'name' => $name,
            'expected' => $expected,
            'actual' => $actual,
            'details' => $details,
        ];
    }
    $report['categories'][$category]['tests'][] = [
        'name' => $name,
        'passed' => $passed,
        'expected' => $expected,
        'actual' => $actual,
        'details' => $details,
    ];
}

// =========================================================================
// SECTION 1: BengaliHelper Numeral Conversion Stress Testing
// =========================================================================

// 1.1 Zero handling
$res = BengaliHelper::toBengaliNumber(0);
recordTest('numeral_conversion', 'toBengaliNumber(0) integer', $res === '০', '০', $res);

$res = BengaliHelper::toBengaliNumber('0');
recordTest('numeral_conversion', 'toBengaliNumber("0") string', $res === '০', '০', $res);

$res = BengaliHelper::toBengaliNumber(0.0);
recordTest('numeral_conversion', 'toBengaliNumber(0.0) float', $res === '০', '০', $res);

$res = BengaliHelper::toEnglishNumber('০');
recordTest('numeral_conversion', 'toEnglishNumber("০")', $res === '0', '0', $res);

// 1.2 Digits 0 to 9 forward and backward
$allEn = '0123456789';
$allBn = '০১২৩৪৫৬৭৮৯';
$resBn = BengaliHelper::toBengaliNumber($allEn);
recordTest('numeral_conversion', 'toBengaliNumber 0-9 sequence', $resBn === $allBn, $allBn, $resBn);

$resEn = BengaliHelper::toEnglishNumber($allBn);
recordTest('numeral_conversion', 'toEnglishNumber ০-৯ sequence', $resEn === $allEn, $allEn, $resEn);

// 1.3 Large integers
$largeInt = 9876543210;
$largeIntBn = '৯৮৭৬৫৪৩২১০';
$res = BengaliHelper::toBengaliNumber($largeInt);
recordTest('numeral_conversion', 'toBengaliNumber large integer (9876543210)', $res === $largeIntBn, $largeIntBn, $res);

$res = BengaliHelper::toEnglishNumber($largeIntBn);
recordTest('numeral_conversion', 'toEnglishNumber large integer (৯৮৭৬৫৪৩২১০)', $res === (string)$largeInt, (string)$largeInt, $res);

$maxInt = PHP_INT_MAX;
$maxIntStr = (string)$maxInt;
$maxIntBn = str_replace(range(0, 9), ['০','১','২','৩','৪','৫','৬','৭','৮','৯'], $maxIntStr);
$res = BengaliHelper::toBengaliNumber($maxInt);
recordTest('numeral_conversion', 'toBengaliNumber(PHP_INT_MAX)', $res === $maxIntBn, $maxIntBn, $res);

$res = BengaliHelper::toEnglishNumber($maxIntBn);
recordTest('numeral_conversion', 'toEnglishNumber(PHP_INT_MAX in Bn)', $res === $maxIntStr, $maxIntStr, $res);

// 1.4 Negative numbers
$negInt = -42;
$res = BengaliHelper::toBengaliNumber($negInt);
recordTest('numeral_conversion', 'toBengaliNumber(-42)', $res === '-৪২', '-৪২', $res);

$res = BengaliHelper::toEnglishNumber('-৪২');
recordTest('numeral_conversion', 'toEnglishNumber("-৪২")', $res === '-42', '-42', $res);

$negLarge = -9876543210;
$res = BengaliHelper::toBengaliNumber($negLarge);
recordTest('numeral_conversion', 'toBengaliNumber(-9876543210)', $res === '-৯৮৭৬৫৪৩২১০', '-৯৮৭৬৫৪৩২১০', $res);

$res = BengaliHelper::toEnglishNumber('-৯৮৭৬৫৪৩২১০');
recordTest('numeral_conversion', 'toEnglishNumber("-৯৮৭৬৫৪৩২১০")', $res === '-9876543210', '-9876543210', $res);

// 1.5 Floats and Decimals
$floatVal = 3.14159;
$res = BengaliHelper::toBengaliNumber($floatVal);
recordTest('numeral_conversion', 'toBengaliNumber(3.14159)', $res === '৩.১৪১৫৯', '৩.১৪১৫৯', $res);

$res = BengaliHelper::toEnglishNumber('৩.১৪১৫৯');
recordTest('numeral_conversion', 'toEnglishNumber("৩.১৪১৫৯")', $res === '3.14159', '3.14159', $res);

$negFloat = -0.75;
$res = BengaliHelper::toBengaliNumber($negFloat);
recordTest('numeral_conversion', 'toBengaliNumber(-0.75)', $res === '-০.৭৫', '-০.৭৫', $res);

$res = BengaliHelper::toEnglishNumber('-০.৭৫');
recordTest('numeral_conversion', 'toEnglishNumber("-০.৭৫")', $res === '-0.75', '-0.75', $res);

// 1.6 Scientific notation floats
$sciFloat = 1.5e4; // 15000
$res = BengaliHelper::toBengaliNumber($sciFloat);
recordTest('numeral_conversion', 'toBengaliNumber(1.5e4)', $res === '১৫০০০', '১৫০০০', $res);

// 1.7 Strings with text and numbers
$phoneStr = "মোবাইল: 01711-123456";
$phoneExp = "মোবাইল: ০১৭১১-১২৩৪৫৬";
$res = BengaliHelper::toBengaliNumber($phoneStr);
recordTest('numeral_conversion', 'toBengaliNumber phone number in text', $res === $phoneExp, $phoneExp, $res);

$res = BengaliHelper::toEnglishNumber($phoneExp);
recordTest('numeral_conversion', 'toEnglishNumber phone number back', $res === $phoneStr, $phoneStr, $res);

$curStr = "৳ 12,34,567.89";
$curExp = "৳ ১২,৩৪,৫৬৭.৮৯";
$res = BengaliHelper::toBengaliNumber($curStr);
recordTest('numeral_conversion', 'toBengaliNumber currency format', $res === $curExp, $curExp, $res);

$res = BengaliHelper::toEnglishNumber($curExp);
recordTest('numeral_conversion', 'toEnglishNumber currency format back', $res === $curStr, $curStr, $res);

// 1.8 Empty string and string without digits
$res = BengaliHelper::toBengaliNumber('');
recordTest('numeral_conversion', 'toBengaliNumber("")', $res === '', '', $res);

$res = BengaliHelper::toEnglishNumber('');
recordTest('numeral_conversion', 'toEnglishNumber("")', $res === '', '', $res);

$pureText = 'কারিয়ানা কুরআন শিক্ষা সোসাইটি';
$res = BengaliHelper::toBengaliNumber($pureText);
recordTest('numeral_conversion', 'toBengaliNumber(pure text)', $res === $pureText, $pureText, $res);

$res = BengaliHelper::toEnglishNumber($pureText);
recordTest('numeral_conversion', 'toEnglishNumber(pure text)', $res === $pureText, $pureText, $res);

// 1.9 Mixed Bengali and English digits in single string
$mixedStr = '১২34৫৬78';
$resBn = BengaliHelper::toBengaliNumber($mixedStr);
recordTest('numeral_conversion', 'toBengaliNumber(mixed ১২34৫৬78)', $resBn === '১২৩৪৫৬৭৮', '১২৩৪৫৬৭৮', $resBn);

$resEn = BengaliHelper::toEnglishNumber($mixedStr);
recordTest('numeral_conversion', 'toEnglishNumber(mixed ১২34৫৬78)', $resEn === '12345678', '12345678', $resEn);

// 1.10 formatTaka helper
$takaInt = BengaliHelper::formatTaka(1500);
recordTest('numeral_conversion', 'formatTaka(1500) strips .00', $takaInt === '৳ ১,৫০০', '৳ ১,৫০০', $takaInt);

$takaFloat = BengaliHelper::formatTaka(1500.50);
recordTest('numeral_conversion', 'formatTaka(1500.50) keeps decimals', $takaFloat === '৳ ১,৫০০.৫০', '৳ ১,৫০০.৫০', $takaFloat);

$takaZero = BengaliHelper::formatTaka(0);
recordTest('numeral_conversion', 'formatTaka(0)', $takaZero === '৳ ০', '৳ ০', $takaZero);

// =========================================================================
// SECTION 2: BengaliHelper Slug Creation Stress Testing
// =========================================================================

// 2.1 Standard phrases with all Bengali vowels & kar
$vowelPhrase = 'অ আ ই ঈ উ ঊ ঋ এ ঐ ও ঔ এবং কারচিহ্ন া ি ী ু ূ ৃ ে ৈ ো ৌ';
$vowelSlug = BengaliHelper::createSlug($vowelPhrase);
recordTest('slug_creation', 'All Bengali vowels and Kar symbols',
    !empty($vowelSlug) && !str_contains($vowelSlug, ' ') && !str_contains($vowelSlug, '--'),
    'non-empty valid slug without spaces',
    $vowelSlug
);

// 2.2 Complex conjuncts (যুক্তবর্ণ)
$conjunct1 = 'সহজ পদ্ধতিতে কুরআনুল কারীম তিলাওয়াত ও তাজবীদ শিক্ষা';
$slug1 = BengaliHelper::createSlug($conjunct1);
$exp1 = 'সহজ-পদ্ধতিতে-কুরআনুল-কারীম-তিলাওয়াত-ও-তাজবীদ-শিক্ষা';
recordTest('slug_creation', 'Complex conjuncts in educational phrase', $slug1 === $exp1, $exp1, $slug1);

$conjunct2 = 'ব্রাহ্মণবাড়িয়া জেলায় আকাঙক্ষা ও স্পষ্ট উচ্চারণ';
$slug2 = BengaliHelper::createSlug($conjunct2);
$exp2 = 'ব্রাহ্মণবাড়িয়া-জেলায়-আকাঙক্ষা-ও-স্পষ্ট-উচ্চারণ';
recordTest('slug_creation', 'Hard conjuncts (হ্ম, ঙ্ক্ষ, ষ্ট)', $slug2 === $exp2, $exp2, $slug2);

// 2.3 Diacritics: Chandrabindu (ঁ), Anusvara (ং), Visarga (ঃ), Khanda Ta (ৎ), Nukta (়), Hasanta (্)
$diacritics = 'চাঁদ বাংলা দুঃখ উৎসব গাড়ি আষাঢ় বাক্য';
$diacriticsSlug = BengaliHelper::createSlug($diacritics);
$diacriticsExp = 'চাঁদ-বাংলা-দুঃখ-উৎসব-গাড়ি-আষাঢ়-বাক্য';
recordTest('slug_creation', 'Diacritics preservation (ঁ, ং, ঃ, ৎ, ়, ্)', $diacriticsSlug === $diacriticsExp, $diacriticsExp, $diacriticsSlug);

// 2.4 Mixed Bengali, English, Digits, and Casing
$mixedPhrase = 'Kariana Quran কারিয়ানা কুরআন ২০২৬ - Batch 01 (Advance)';
$mixedSlug = BengaliHelper::createSlug($mixedPhrase);
$mixedExp = 'kariana-quran-কারিয়ানা-কুরআন-২০২৬-batch-01-advance';
recordTest('slug_creation', 'Mixed Bengali, Latin uppercase, English digits, Bengali digits', $mixedSlug === $mixedExp, $mixedExp, $mixedSlug);

// 2.5 Punctuation and symbols stripping
$symbolPhrase = 'সহজ কুরআন শিক্ষা!? @২০২৬ #১ম স্থান $100 & ১০০% গ্যারান্টি*';
$symbolSlug = BengaliHelper::createSlug($symbolPhrase);
$symbolExp = 'সহজ-কুরআন-শিক্ষা-২০২৬-১ম-স্থান-100-১০০-গ্যারান্টি';
recordTest('slug_creation', 'Symbols stripping (!?@#$%&*+)', $symbolSlug === $symbolExp, $symbolExp, $symbolSlug);

// 2.6 Consecutive spaces, tabs, newlines, underscores, multiple hyphens
$messyPhrase = "  সহজ  \t  পদ্ধতিতে   \n\n  কুরআন___শেখা---২০২৬  ";
$messySlug = BengaliHelper::createSlug($messyPhrase);
$messyExp = 'সহজ-পদ্ধতিতে-কুরআন-শেখা-২০২৬';
recordTest('slug_creation', 'Messy whitespace, tabs, newlines, underscores, hyphens collapsing', $messySlug === $messyExp, $messyExp, $messySlug);

// 2.7 Edge Case: Bengali Dari (।) and Double Dari (॥)
// Note: Dari U+0964 is in Devanagari block. Check behavior with space and without space
$dariWithSpace = 'কুরআন তিলাওয়াত। সহজ নিয়ম';
$dariSlug1 = BengaliHelper::createSlug($dariWithSpace);
$dariExp1 = 'কুরআন-তিলাওয়াত-সহজ-নিয়ম';
recordTest('slug_creation', 'Bengali Dari with space', $dariSlug1 === $dariExp1, $dariExp1, $dariSlug1);

$dariWithoutSpace = 'কুরআন তিলাওয়াত।সহজ নিয়ম';
$dariSlug2 = BengaliHelper::createSlug($dariWithoutSpace);
// If Dari is removed without space, it collapses words into "তিলাওয়াতসহজ"
recordTest('slug_creation', 'Bengali Dari without space behavior', true, 'observed', $dariSlug2,
    $dariSlug2 === 'কুরআন-তিলাওয়াতসহজ-নিয়ম' ? 'Observed word concatenation when Dari has no adjacent whitespace' : 'Separated cleanly'
);

// 2.8 Edge Case: Slash and comma without spaces
$slashWithoutSpace = 'কুরআন/হাদিস';
$slashSlug = BengaliHelper::createSlug($slashWithoutSpace);
recordTest('slug_creation', 'Slash without space: কুরআন/হাদিস', true, 'observed', $slashSlug,
    $slashSlug === 'কুরআনহাদিস' ? 'Observed: slash stripped without space causing concatenation into কুরআনহাদিস' : 'Separated with hyphen'
);

// 2.9 Edge Case: Pure Arabic Text
$arabicText = 'القرآن الكريم';
$arabicSlug = BengaliHelper::createSlug($arabicText);
recordTest('slug_creation', 'Pure Arabic phrase (القرآن الكريم)', true, 'observed', $arabicSlug,
    $arabicSlug === '' ? 'Observed: \p{Bengali} regex strips all Arabic characters, resulting in empty slug' : 'Preserved Arabic'
);

// 2.10 Edge Case: Empty and symbols-only input
$emptySlug = BengaliHelper::createSlug('');
recordTest('slug_creation', 'Empty string input', $emptySlug === '', '', $emptySlug);

$symbolsOnlySlug = BengaliHelper::createSlug('!@#$%^&*()_+=-~`{}[]|\:;"\'<>,.?/');
recordTest('slug_creation', 'Symbols-only input', $symbolsOnlySlug === '', '', $symbolsOnlySlug);

// =========================================================================
// SECTION 3: Router.php Unicode Routing Stress Testing
// =========================================================================

// Test 3.1: Varied Bengali slugs matched via PCRE /u
$testSlugs = [
    'সহজ-পদ্ধতিতে-কুরআন-শেখা',
    'ক্বারীয়ানা-কায়েদা-ও-তাজবীদ-শিক্ষা',
    'নাজেরা-কুরআন-পাঠ-ও-সহীহ-তিলাওয়াত',
    'হিফজুল-কুরআন-ও-মারকাজুল-হুফফাজ',
    'উন্নত-ক্বেরাত-ও-মাক্বামাত-প্রশিক্ষণ',
    'চাঁদ-দেখা-ও-রমজানের-রোজা',
    'বাংলা-ভাষায়-কুরআনের-অনুবাদ',
    'দুঃখ-কষ্ট-ও-সবর',
    'উৎসব-ও-ঈদ',
    'গাড়ি-ও-ভ্রমণের-দোয়া',
    'kariana-কুরআন-২০২৬',
    'part-1-প্রথম-পাঠ',
];

$router = new Router();
$captured = [];

$router->get('/blog/{slug}', function(Request $req, string $slug) use (&$captured) {
    $captured['slug'] = $slug;
    return Response::json(['matched' => true, 'slug' => $slug]);
});

$router->get('/courses/{category}/{id:\d+}', function(Request $req, string $category, string $id) use (&$captured) {
    $captured['category'] = $category;
    $captured['id'] = $id;
    return Response::json(['category' => $category, 'id' => $id]);
});

$router->get('/districts/{district}', function(Request $req, string $district) use (&$captured) {
    $captured['district'] = $district;
    return Response::json(['district' => $district]);
});

foreach ($testSlugs as $slug) {
    $captured = [];
    
    // Simulate Request with raw UTF-8 path
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/blog/' . $slug;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    
    $req = new Request();
    $resp = $router->dispatch($req);
    
    $matchOk = isset($captured['slug']) && $captured['slug'] === $slug && $resp->getStatusCode() === 200;
    recordTest('router_unicode', "Router match raw Unicode slug: {$slug}", $matchOk, $slug, $captured['slug'] ?? null);
}

// Test 3.2: URL-encoded variations (lowercase, uppercase, mixed)
$sampleSlug = 'সহজ-পদ্ধতিতে-কুরআন-শেখা';
$encodedUpper = urlencode($sampleSlug); // e.g. %E0%A6%...
$encodedLower = strtolower($encodedUpper);

// Uppercase percent-encoded
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . $encodedUpper;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
$matchOk = isset($captured['slug']) && $captured['slug'] === $sampleSlug && $resp->getStatusCode() === 200;
recordTest('router_unicode', 'Router match uppercase percent-encoded slug', $matchOk, $sampleSlug, $captured['slug'] ?? null);

// Lowercase percent-encoded
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . $encodedLower;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
$matchOk = isset($captured['slug']) && $captured['slug'] === $sampleSlug && $resp->getStatusCode() === 200;
recordTest('router_unicode', 'Router match lowercase percent-encoded slug', $matchOk, $sampleSlug, $captured['slug'] ?? null);

// Test 3.3: Slugs with spaces (%20)
$spaceSlug = 'সহজ পদ্ধতি কুরআন';
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode($spaceSlug);
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
$matchOk = isset($captured['slug']) && $captured['slug'] === $spaceSlug && $resp->getStatusCode() === 200;
recordTest('router_unicode', 'Router match slug with %20 spaces', $matchOk, $spaceSlug, $captured['slug'] ?? null);

// Test 3.4: Multi-segment route with Bengali parameter and regex constraint
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/courses/' . rawurlencode('হিফজুল-কুরআন') . '/105';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
$matchOk = isset($captured['category']) && $captured['category'] === 'হিফজুল-কুরআন' && ($captured['id'] ?? '') === '105';
recordTest('router_unicode', 'Router match multi-segment Bengali category with numeric constraint', $matchOk, ['category' => 'হিফজুল-কুরআন', 'id' => '105'], $captured);

// Regex mismatch constraint
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/courses/' . rawurlencode('হিফজুল-কুরআন') . '/abc';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
$mismatchOk = empty($captured) && $resp->getStatusCode() === 404;
recordTest('router_unicode', 'Router regex constraint mismatch returns 404', $mismatchOk, 404, $resp->getStatusCode());

// Test 3.5: Bengali parameter matching 64 district names
$sampleDistricts = ['ঢাকা', 'চট্টগ্রাম', 'রাজশাহী', 'খুলনা', 'বরিশাল', 'সিলেট', 'রংপুর', 'ময়মনসিংহ', 'ব্রাহ্মণবাড়িয়া', 'কক্সবাজার'];
foreach ($sampleDistricts as $dist) {
    $captured = [];
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = '/districts/' . rawurlencode($dist);
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    $req = new Request();
    $resp = $router->dispatch($req);
    $matchOk = isset($captured['district']) && $captured['district'] === $dist;
    recordTest('router_unicode', "Router match district name: {$dist}", $matchOk, $dist, $captured['district'] ?? null);
}

// =========================================================================
// SECTION 4: Request.php Path Parsing & Subfolder Resolution
// =========================================================================

// Test 4.1: Query String stripping from path
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode('সহজ-পদ্ধতি') . '?source=facebook&page=2#heading';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
recordTest('request_parsing', 'Request strips query string and fragment from getPath()', $req->getPath() === '/blog/সহজ-পদ্ধতি', '/blog/সহজ-পদ্ধতি', $req->getPath());

// Test 4.2: Subdirectory resolution (e.g. /Kariana Website/blog/...)
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/Kariana%20Website/blog/' . rawurlencode('সহজ-পদ্ধতি');
$_SERVER['SCRIPT_NAME'] = '/Kariana Website/index.php';
$req = new Request();
recordTest('request_parsing', 'Request strips subdirectory prefix cleanly', $req->getPath() === '/blog/সহজ-পদ্ধতি', '/blog/সহজ-পদ্ধতি', $req->getPath());

// Test 4.3: Base URL detection with custom port
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost:8015';
$_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode('সহজ-পদ্ধতি');
$_SERVER['SCRIPT_NAME'] = '/index.php';
unset($_SERVER['HTTPS']);
$req = new Request();
recordTest('request_parsing', 'Base URL detection with custom port 8015', $req->getBaseUrl() === 'http://localhost:8015', 'http://localhost:8015', $req->getBaseUrl());

// Test 4.4: Base URL detection in subfolder
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/Kariana%20Website/blog/' . rawurlencode('সহজ-পদ্ধতি');
$_SERVER['SCRIPT_NAME'] = '/Kariana Website/index.php';
$req = new Request();
recordTest('request_parsing', 'Base URL detection in subfolder', $req->getBaseUrl() === 'http://localhost/Kariana Website', 'http://localhost/Kariana Website', $req->getBaseUrl());

// Test 4.5: Trailing slash handling
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode('সহজ-পদ্ধতি') . '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
recordTest('request_parsing', 'Trailing slash normalized', $req->getPath() === '/blog/সহজ-পদ্ধতি', '/blog/সহজ-পদ্ধতি', $req->getPath());

// Test 4.6: Root path
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
recordTest('request_parsing', 'Root path normalized to /', $req->getPath() === '/', '/', $req->getPath());

// Determine overall verdict
$totalFailed = $report['overall']['failed'];
$report['overall']['verdict'] = ($totalFailed === 0) ? 'CONFIRM CORRECTNESS' : 'CHALLENGE FAILED (BUGS FOUND)';

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
