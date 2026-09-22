<?php
/**
 * Worker M1 Verification Suite
 * Verifies Autoloader, Router, BengaliHelper, Database, Model, CSRF, and Font Assets
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\App;
use Core\Autoloader;
use Core\BengaliHelper;
use Core\Controller;
use Core\Csrf;
use Core\Database;
use Core\Model;
use Core\Request;
use Core\Response;
use Core\Router;
use Core\Session;
use Core\View;

function runM1Verification(): array
{
    $results = [];
    $allPassed = true;

    // Helper assertion
    $assert = function(string $testName, bool $condition, string $details = '') use (&$results, &$allPassed) {
        $results[] = [
            'test'    => $testName,
            'passed'  => $condition,
            'details' => $details,
        ];
        if (!$condition) {
            $allPassed = false;
        }
    };

    try {
        // 1. Autoloader Verification
        $assert(
            'Autoloader: App and Core classes exist',
            class_exists(App::class) &&
            class_exists(Router::class) &&
            class_exists(Request::class) &&
            class_exists(Response::class) &&
            class_exists(Controller::class) &&
            class_exists(Model::class) &&
            class_exists(View::class) &&
            class_exists(Database::class) &&
            class_exists(BengaliHelper::class) &&
            class_exists(Csrf::class) &&
            class_exists(Session::class),
            'All Core framework classes loaded successfully via PSR-4 autoloader.'
        );

        // 2. BengaliHelper Numeral Verification
        $enNum = 2026;
        $bnNum = BengaliHelper::toBengaliNumber($enNum);
        $backToEn = BengaliHelper::toEnglishNumber($bnNum);

        $assert(
            'BengaliHelper: Numeral Conversion 0-9 <-> ০-৯',
            $bnNum === '২০২৬' && $backToEn === '2026',
            "Expected '২০২৬' and '2026', got '{$bnNum}' and '{$backToEn}'"
        );

        // 3. BengaliHelper 9-Step Slug Verification (Linguistic & Edge Cases)
        $slugStandard = BengaliHelper::createSlug('কুরআন-তিলাওয়াত ও তাজবীদ শিক্ষা!?');
        $slugDari = BengaliHelper::createSlug('সহজ পদ্ধতিতে কুরআন শিক্ষা।');
        $slugDoubleDari = BengaliHelper::createSlug('প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়');
        $slugTaka = BengaliHelper::createSlug('অনলাইন কোর্স ফি ৫০০৳');
        $slugPunctuationMerge = BengaliHelper::createSlug('কুরআন/সুন্নাহ');
        $slugArabic = BengaliHelper::createSlug('الْقُرْآن الْكَرِيم');
        $slugFallback = BengaliHelper::createSlug('??? --- !!!');

        $slugDariPass = ($slugDari === 'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা');
        $slugDoubleDariPass = ($slugDoubleDari === 'প্রথম-অধ্যায়-সমাপ্ত-দ্বিতীয়-অধ্যায়');
        $slugTakaPass = ($slugTaka === 'অনলাইন-কোর্স-ফি-৫০০');
        $slugPunctuationMergePass = ($slugPunctuationMerge === 'কুরআন-সুন্নাহ');
        $slugArabicPass = ($slugArabic === 'القرآن-الكريم');
        $slugFallbackPass = (str_starts_with($slugFallback, 'item-') && strlen($slugFallback) > 6);

        $assert(
            'BengaliHelper: 9-step slug algorithm handles Dari, Taka, delimiters, Arabic, and fallbacks',
            $slugStandard === 'কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা' &&
            $slugDariPass &&
            $slugDoubleDariPass &&
            $slugTakaPass &&
            $slugPunctuationMergePass &&
            $slugArabicPass &&
            $slugFallbackPass,
            "Standard: '{$slugStandard}', Dari: '{$slugDari}', DoubleDari: '{$slugDoubleDari}', Taka: '{$slugTaka}', Slash: '{$slugPunctuationMerge}', Arabic: '{$slugArabic}', Fallback: '{$slugFallback}'"
        );

        // 4. CSRF Verification
        $token = Csrf::token();
        $isValid = Csrf::validate($token);
        $isInvalid = !Csrf::validate('fake-tampered-token-123');
        $isArraySafe = !Csrf::validate(['malicious' => 'array']);
        $isNullSafe = !Csrf::validate(null);
        $isIntSafe = !Csrf::validate(12345);

        $assert(
            'CSRF: Cryptographic token generation and mixed-type safe validation',
            strlen($token) === 64 && ctype_xdigit($token) && $isValid && $isInvalid && $isArraySafe && $isNullSafe && $isIntSafe,
            "Token length: " . strlen($token) . ", valid check: " . ($isValid ? 'true' : 'false') . ", array safe: " . ($isArraySafe ? 'true' : 'false')
        );

        // 5. Database Verification
        $db = Database::getInstance();
        $charsetStmt = $db->query("SHOW VARIABLES LIKE 'character_set_connection'");
        $charsetRow = $charsetStmt->fetch();
        $isUtf8mb4 = str_contains(strtolower($charsetRow['Value'] ?? ''), 'utf8mb4');

        $assert(
            'Database: PDO singleton connected with UTF8MB4 charset',
            $isUtf8mb4,
            "Connection charset: " . ($charsetRow['Value'] ?? 'unknown')
        );

        // Check 12 tables exist
        $tablesStmt = $db->query("SHOW TABLES");
        $allTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        $requiredTables = [
            'users', 'categories', 'posts', 'courses', 'admissions',
            'books', 'pages', 'qr_lessons', 'prayer_districts',
            'zakat_settings', 'site_settings', 'migrations'
        ];

        $missingTables = array_diff($requiredTables, $allTables);
        $assert(
            'Database: 12 normalized tables present',
            empty($missingTables),
            empty($missingTables) ? 'All 12 tables present: ' . implode(', ', $requiredTables) : 'Missing: ' . implode(', ', $missingTables)
        );

        // Check 64 districts seeded
        $districtCount = (int)$db->query("SELECT COUNT(*) FROM `prayer_districts`")->fetchColumn();
        $assert(
            'Database: 64 districts seeded with IFB prayer offsets',
            $districtCount === 64,
            "District count: {$districtCount} / 64"
        );

        // Check Admin user seeded
        $adminStmt = $db->prepare("SELECT `username`, `email`, `role`, `password` FROM `users` WHERE `username` = :u");
        $adminStmt->execute(['u' => 'admin']);
        $adminUser = $adminStmt->fetch();
        $passwordValid = $adminUser && password_verify('admin123', $adminUser['password']);

        $assert(
            'Database: Admin user seeded and bcrypt password verified',
            $adminUser && $adminUser['role'] === 'admin' && $passwordValid,
            "Admin user found with email " . ($adminUser['email'] ?? 'none') . ", role " . ($adminUser['role'] ?? 'none') . ", bcrypt verified: " . ($passwordValid ? 'YES' : 'NO')
        );

        // 6. Model OrderBy SQL Injection Defense Verification
        $testModel = new class extends Model {
            protected string $table = 'prayer_districts';
        };

        // Valid order by queries
        $validSortResult = $testModel->all('id ASC', 1);
        $validMultiSortResult = $testModel->all('division_bn ASC, id DESC', 1);
        $validSortPass = is_array($validSortResult) && is_array($validMultiSortResult);

        // Injected queries must throw InvalidArgumentException
        $injectionSleepBlocked = false;
        try {
            $testModel->all('id ASC, (SELECT 1 FROM (SELECT SLEEP(0.2))a)');
        } catch (\InvalidArgumentException $e) {
            $injectionSleepBlocked = true;
        }

        $injectionDropBlocked = false;
        try {
            $testModel->all('1; DROP TABLE users; --');
        } catch (\InvalidArgumentException $e) {
            $injectionDropBlocked = true;
        }

        $injectionUnionBlocked = false;
        try {
            $testModel->all("' OR '1'='1");
        } catch (\InvalidArgumentException $e) {
            $injectionUnionBlocked = true;
        }

        $injectionStuffingBlocked = false;
        try {
            $testModel->all('id ASC SLEEP col');
        } catch (\InvalidArgumentException $e) {
            $injectionStuffingBlocked = true;
        }

        $assert(
            'Model: OrderBy parameter whitelisting completely neutralizes SQL injection',
            $validSortPass && $injectionSleepBlocked && $injectionDropBlocked && $injectionUnionBlocked && $injectionStuffingBlocked,
            "Valid sort: " . ($validSortPass ? 'PASS' : 'FAIL') . ", Sleep blocked: " . ($injectionSleepBlocked ? 'YES' : 'NO') . ", Drop blocked: " . ($injectionDropBlocked ? 'YES' : 'NO') . ", Union blocked: " . ($injectionUnionBlocked ? 'YES' : 'NO') . ", Keyword stuffing blocked: " . ($injectionStuffingBlocked ? 'YES' : 'NO')
        );

        // 7. Router Verification with Bengali Slug & Safe Reflection
        $router = new Router();
        $capturedSlug = '';
        $router->get('/blog/{slug}', function(Request $req, string $slug) use (&$capturedSlug) {
            $capturedSlug = $slug;
            return Response::json(['slug' => $slug]);
        });

        // Test handler with union type parameter signatures
        $reflectionTested = false;
        $router->get('/test-reflection/{id}', function(Request $req, string|int $id) use (&$reflectionTested) {
            $reflectionTested = true;
            return Response::json(['id' => $id]);
        });

        // Simulate Request for Bengali slug
        $backupServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/blog/%E0%A6%B8%E0%A6%B9%E0%A6%9C-%E0%A6%AA%E0%A6%A6%E0%A7%8D%E0%A6%A7%E0%A6%A4%E0%A6%BF%E0%A6%A4%E0%A7%87-%E0%A6%95%E0%A7%81%E0%A6%B0%E0%A6%86%E0%A6%A8-%E0%A6%B6%E0%A7%87%E0%A6%96%E0%A6%BE';
        $_SERVER['SCRIPT_NAME']    = '/index.php';

        $simRequest = new Request();
        $routerResponse = $router->dispatch($simRequest);

        // Simulate Request for reflection route
        $_SERVER['REQUEST_URI'] = '/test-reflection/42';
        $simRequest2 = new Request();
        $routerResponse2 = $router->dispatch($simRequest2);
        $_SERVER = $backupServer;

        $assert(
            'Router: PCRE /u Unicode routing with Bengali slug and safe parameter reflection',
            $capturedSlug === 'সহজ-পদ্ধতিতে-কুরআন-শেখা' && $reflectionTested,
            "Captured slug: '{$capturedSlug}', Reflection tested: " . ($reflectionTested ? 'YES' : 'NO')
        );

        // 8. Font Asset Verification
        $fontPath = dirname(__DIR__, 2) . '/public/assets/fonts/AAR-SQ-003.ttf';
        $fontExists = file_exists($fontPath);
        $fontSize = $fontExists ? filesize($fontPath) : 0;
        $fontHeader = '';
        if ($fontExists) {
            $fp = fopen($fontPath, 'rb');
            $headerBytes = fread($fp, 12);
            fclose($fp);
            $fontHeader = bin2hex($headerBytes);
        }
        $scalarMatch = ($fontHeader === '000100000012010000040020');

        $assert(
            'Assets: Kariana Arabic font AAR-SQ-003.ttf deployed in public/assets/fonts/',
            $fontExists && $fontSize === 682112 && $scalarMatch,
            "Font path: {$fontPath}, size: {$fontSize} bytes (Expected: 682112), header: {$fontHeader} (scalar match: " . ($scalarMatch ? 'YES' : 'NO') . ")"
        );
    } catch (\Throwable $e) {
        $allPassed = false;
        $results[] = [
            'test'    => 'Exception during test execution',
            'passed'  => false,
            'details' => $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine(),
        ];
    }

    return [
        'success'   => $allPassed,
        'timestamp' => date('Y-m-d H:i:s'),
        'results'   => $results,
    ];
}

// Direct CLI Execution Support
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $report = runM1Verification();
    echo "\n=======================================================\n";
    echo "  Milestone M1 Core Framework Verification Suite\n";
    echo "=======================================================\n\n";
    foreach ($report['results'] as $res) {
        $status = $res['passed'] ? "  [PASS] " : "  [FAIL] ";
        echo $status . str_pad($res['test'], 60) . "\n";
        if (!empty($res['details'])) {
            echo "         -> " . $res['details'] . "\n";
        }
    }
    echo "\n" . str_repeat('-', 55) . "\n";
    echo "Overall Status: " . ($report['success'] ? "ALL TESTS PASSED" : "TESTS FAILED") . "\n";
    echo "Timestamp:      " . $report['timestamp'] . "\n";
    echo str_repeat('-', 55) . "\n\n";
    exit($report['success'] ? 0 : 1);
}
