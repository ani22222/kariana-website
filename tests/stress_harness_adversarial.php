<?php
/**
 * Challenger 1 - Adversarial Stress Harness
 * Verifies and stresses:
 * 1. Malformed UTF-8 & PCRE /u resilience in Router.php
 * 2. URL encoding variations (+ vs %20, double encoding, mixed case)
 * 3. Numeral edge cases (PHP_INT_MAX, type handling, floats, scientific notation)
 * 4. Slug edge cases (Dari, Taka, Arabic, punctuation boundaries, empty slugs)
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\Router;
use Core\Request;
use Core\Response;
use Core\BengaliHelper;

$adversarialResults = [
    'timestamp' => date('Y-m-d H:i:s'),
    'suites' => [],
    'summary' => [
        'total' => 0,
        'passed' => 0,
        'failed' => 0,
        'bugs_discovered' => [],
    ],
];

function logResult(string $suite, string $testName, bool $passed, mixed $expected, mixed $actual, string $note = ''): void {
    global $adversarialResults;
    $adversarialResults['suites'][$suite][] = [
        'test' => $testName,
        'passed' => $passed,
        'expected' => $expected,
        'actual' => $actual,
        'note' => $note,
    ];
    $adversarialResults['summary']['total']++;
    if ($passed) {
        $adversarialResults['summary']['passed']++;
    } else {
        $adversarialResults['summary']['failed']++;
        $adversarialResults['summary']['bugs_discovered'][] = [
            'suite' => $suite,
            'test' => $testName,
            'expected' => $expected,
            'actual' => $actual,
            'note' => $note,
        ];
    }
}

// -------------------------------------------------------------
// SUITE 1: Router & Request Adversarial Stress
// -------------------------------------------------------------
$suite = 'router_stress';

$router = new Router();
$captured = [];

$router->get('/blog/{slug}', function(Request $req, string $slug) use (&$captured) {
    $captured['slug'] = $slug;
    return Response::json(['slug' => $slug]);
});

$router->get('/lesson/{id:\d+}', function(Request $req, string $id) use (&$captured) {
    $captured['id'] = $id;
    return Response::json(['id' => $id]);
});

// Test 1.1: Malformed UTF-8 in URI (e.g. \xC0\xAF)
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = "/blog/\xC0\xAF";
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
// Router must not crash or throw unhandled exception; should return 404 or safe response
logResult($suite, 'Malformed UTF-8 bytes in URI does not crash PCRE /u',
    $resp->getStatusCode() === 404 || $resp->getStatusCode() === 200,
    'Graceful 404/200 without fatal error',
    'HTTP ' . $resp->getStatusCode(),
    'PCRE with /u safely ignores or fails match on invalid UTF-8 without throwing error'
);

// Test 1.2: Plus (+) vs %20 in URL path
// In URL paths (RFC 3986), + is a literal plus, not a space. rawurldecode preserves +
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/সহজ+পদ্ধতি';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
logResult($suite, 'Plus (+) in path preserved as literal plus (RFC 3986)',
    $captured['slug'] === 'সহজ+পদ্ধতি',
    'সহজ+পদ্ধতি',
    $captured['slug'] ?? null,
    'rawurldecode strictly adheres to RFC 3986 path semantics'
);

// Test 1.3: Consecutive hyphens in route
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/সহজ--পদ্ধতি---কুরআন';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
logResult($suite, 'Consecutive hyphens captured accurately',
    $captured['slug'] === 'সহজ--পদ্ধতি---কুরআন',
    'সহজ--পদ্ধতি---কুরআন',
    $captured['slug'] ?? null
);

// Test 1.4: Mixed Latin, Bengali, and Numbers
$mixedSlug = 'kariana-কুরআন-101-লেসন';
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode($mixedSlug);
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
logResult($suite, 'Mixed Latin, Bengali, Numbers in slug',
    $captured['slug'] === $mixedSlug,
    $mixedSlug,
    $captured['slug'] ?? null
);

// Test 1.5: Double URL-encoded slug (%25E0%25A6%2595...)
$doubleEncoded = '%25E0%25A6%2595%25E0%25A7%2581%25E0%25A6%25B0%25E0%25A6%2586%25E0%25A6%25A8';
$singleDecoded = '%E0%A6%95%E0%A7%81%E0%A6%B0%E0%A6%86%E0%A6%A8';
$captured = [];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . $doubleEncoded;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$req = new Request();
$resp = $router->dispatch($req);
logResult($suite, 'Double URL-encoded path decoded only once (anti-exploit defense)',
    $captured['slug'] === $singleDecoded,
    $singleDecoded,
    $captured['slug'] ?? null,
    'Decodes only single layer preventing double-encoding bypass'
);

// -------------------------------------------------------------
// SUITE 2: BengaliHelper Numeral Conversion Deep Stress
// -------------------------------------------------------------
$suite = 'numeral_deep_stress';

// Test 2.1: Very large float
$largeFloat = 123456789012345.67;
$largeFloatBn = BengaliHelper::toBengaliNumber($largeFloat);
$largeFloatEn = BengaliHelper::toEnglishNumber($largeFloatBn);
logResult($suite, 'Large float conversion & roundtrip',
    (float)$largeFloatEn === (float)(string)$largeFloat,
    (string)$largeFloat,
    $largeFloatEn
);

// Test 2.2: Negative zero float
$negZero = -0.0;
$res = BengaliHelper::toBengaliNumber($negZero);
logResult($suite, 'Negative zero float (-0.0)',
    $res === '০' || $res === '-০',
    '০ or -০',
    $res
);

// Test 2.3: Small decimal (< 1)
$smallDec = 0.000789;
$smallDecBn = BengaliHelper::toBengaliNumber($smallDec);
$smallDecEn = BengaliHelper::toEnglishNumber($smallDecBn);
logResult($suite, 'Small decimal (<1) conversion',
    $smallDecEn === (string)$smallDec,
    (string)$smallDec,
    $smallDecEn
);

// Test 2.4: Bengali digits roundtrip check for 10,000 numbers generator
$allPass = true;
for ($i = 0; $i < 1000; $i += 17) {
    $bn = BengaliHelper::toBengaliNumber($i);
    $en = BengaliHelper::toEnglishNumber($bn);
    if ((int)$en !== $i) {
        $allPass = false;
        break;
    }
}
logResult($suite, 'Step sequence 0..1000 roundtrip oracle',
    $allPass,
    true,
    $allPass
);

// Test 2.5: String with complex punctuation around numbers
$complexNumStr = "(০১৭১১) ১২-৩৪-৫৬ [রুম #১০২, ব্যাচ #৫]!";
$enNumStr = BengaliHelper::toEnglishNumber($complexNumStr);
$expEnStr = "(01711) 12-34-56 [রুম #102, ব্যাচ #5]!";
logResult($suite, 'toEnglishNumber preserves surrounding Bengali text and punctuation',
    $enNumStr === $expEnStr,
    $expEnStr,
    $enNumStr
);

// -------------------------------------------------------------
// SUITE 3: Bengali Slug Creation Boundary & Defect Hunting
// -------------------------------------------------------------
$suite = 'slug_boundary_stress';

// Defect 1: Bengali Dari (।) and Double Dari (॥) preservation
$titleWithDari = 'সহজ কুরআন শিক্ষা। প্রথম খণ্ড';
$slugWithDari = BengaliHelper::createSlug($titleWithDari);
$isDariInSlug = str_contains($slugWithDari, '।') || str_contains($slugWithDari, '॥');
logResult($suite, 'BUG: Bengali Dari (।) must NOT remain in URL slug',
    !$isDariInSlug, // Expect false (should NOT have Dari)
    'সহজ-কুরআন-শিক্ষা-প্রথম-খণ্ড',
    $slugWithDari,
    $isDariInSlug ? 'DEFECT CONFIRMED: \p{Bengali} includes U+0964 (Dari) so punctuation remains in URL slug: ' . $slugWithDari : 'Passed'
);

// Defect 2: Bengali Currency Taka Sign (৳)
$titleWithTaka = 'অনলাইন কোর্স ফি ৫০০৳';
$slugWithTaka = BengaliHelper::createSlug($titleWithTaka);
$isTakaInSlug = str_contains($slugWithTaka, '৳');
logResult($suite, 'BUG: Bengali Taka symbol (৳) must NOT remain in URL slug',
    !$isTakaInSlug,
    'অনলাইন-কোর্স-ফি-৫০০',
    $slugWithTaka,
    $isTakaInSlug ? 'DEFECT CONFIRMED: \p{Bengali} includes U+09F3 (৳) so currency symbol remains in slug: ' . $slugWithTaka : 'Passed'
);

// Defect 3: Slashes and commas without space merging words
$titleSlash = 'কুরআন/সুন্নাহ';
$slugSlash = BengaliHelper::createSlug($titleSlash);
logResult($suite, 'Word merging bug when slash has no whitespace: কুরআন/সুন্নাহ',
    $slugSlash === 'কুরআন-সুন্নাহ',
    'কুরআন-সুন্নাহ',
    $slugSlash,
    $slugSlash === 'কুরআনসুন্নাহ' ? 'DEFECT: Punctuation is stripped without converting to hyphen first, merging words into কুরআনসুন্নাহ' : 'Passed'
);

$titleComma = 'কুরআন,হাদিস,ফিকহ';
$slugComma = BengaliHelper::createSlug($titleComma);
logResult($suite, 'Word merging bug when comma has no whitespace: কুরআন,হাদিস,ফিকহ',
    $slugComma === 'কুরআন-হাদিস-ফিকহ',
    'কুরআন-হাদিস-ফিকহ',
    $slugComma,
    $slugComma === 'কুরআনহাদিসফিকহ' ? 'DEFECT: Comma stripped without space, resulting in কুরআনহাদিসফিকহ' : 'Passed'
);

// Defect 4: Pure Arabic titles yielding empty slug
$titleArabic = 'القرآن الكريم';
$slugArabic = BengaliHelper::createSlug($titleArabic);
logResult($suite, 'Arabic title produces non-empty slug for Islamic portal',
    !empty($slugArabic),
    'non-empty slug',
    $slugArabic,
    empty($slugArabic) ? 'DEFECT: Pure Arabic book/course titles produce empty string "", violating database NOT NULL UNIQUE constraint' : 'Passed'
);

// Defect 5: Trailing Dari yielding slug ending in punctuation
$titleTrailingDari = 'কুরআনুল কারীম।';
$slugTrailingDari = BengaliHelper::createSlug($titleTrailingDari);
logResult($suite, 'Trailing Dari must not end URL slug',
    !str_ends_with($slugTrailingDari, '।'),
    'কুরআনুল-কারীম',
    $slugTrailingDari,
    str_ends_with($slugTrailingDari, '।') ? 'DEFECT: Slug ends with punctuation mark: ' . $slugTrailingDari : 'Passed'
);

// Final Verdict calculation
$adversarialResults['verdict'] = ($adversarialResults['summary']['failed'] > 0)
    ? 'CHALLENGE FAILED (EMPIRICAL DEFECTS IDENTIFIED)'
    : 'CONFIRM CORRECTNESS';

echo json_encode($adversarialResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
