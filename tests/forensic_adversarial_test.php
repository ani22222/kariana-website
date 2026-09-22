<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\BengaliHelper;
use Core\Csrf;
use Core\Router;
use Core\Request;
use Core\Response;
use Core\Database;
use Core\Session;

$results = [];

// 1. Stress-test BengaliHelper::createSlug
$slugCases = [
    'standard' => 'সহজ পদ্ধতিতে কুরআন শিক্ষা',
    'ligatures' => 'যুক্তবর্ণ ও ক্বারীয়ানা ১২টি সংকেত (তাজবীদ)',
    'complex_vowels' => 'নূরানী ক্বায়দা ও মাখরাজ-উচ্চারণ পদ্ধতি',
    'special_chars' => 'কুরআন! @#$%^&*()_+~`|}{[]:;?><,./ তিলাওয়াত',
    'mixed_en_bn' => 'Kariana Quran কারিয়ানা কুরআন 2026 Batch #1',
    'all_symbols' => '!@#$%^&*()',
    'empty' => '',
    'multiple_hyphens' => 'কুরআন---তিলাওয়াত___পদ্ধতি',
];

$slugResults = [];
foreach ($slugCases as $key => $input) {
    $slugResults[$key] = BengaliHelper::createSlug($input);
}

// 2. Stress-test BengaliHelper numerals
$numCases = [
    'zero' => 0,
    'negative' => -50,
    'large' => 1928374650,
    'float' => 3.14159,
    'string_mixed' => 'Batch 2026 Room 104',
];
$numResults = [];
foreach ($numCases as $key => $input) {
    $bn = BengaliHelper::toBengaliNumber($input);
    $en = BengaliHelper::toEnglishNumber($bn);
    $numResults[$key] = [
        'input' => $input,
        'to_bengali' => $bn,
        'back_to_english' => $en,
    ];
}

// 3. Stress-test Csrf
$csrfCases = [
    'empty_string' => Csrf::validate(''),
    'null' => Csrf::validate(null),
    'short_string' => Csrf::validate('short'),
    'correct_token' => Csrf::validate(Csrf::token()),
    'tampered_last_char' => false,
];
$validToken = Csrf::token();
$tampered = substr($validToken, 0, -1) . ($validToken[-1] === 'a' ? 'b' : 'a');
$csrfCases['tampered_last_char'] = !Csrf::validate($tampered);

// 4. Stress-test Router with adversarial URLs
$router = new Router();
$captured = [];
$router->get('/blog/{slug}', function(Request $req, string $slug) use (&$captured) {
    $captured['blog_slug'] = $slug;
    return new Response("Blog: {$slug}", 200);
});
$router->get('/courses/{id:\d+}', function(Request $req, string $id) use (&$captured) {
    $captured['course_id'] = $id;
    return new Response("Course ID: {$id}", 200);
});

// Test 4a: Bengali slug with ligatures
$testSlug = urlencode('ক্বারীয়ানা-১২টি-সংকেত');
$req1 = new Request();
// Simulating server variables
$backup = $_SERVER;

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . $testSlug;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$res1 = $router->dispatch(new Request());
$test1Passed = ($captured['blog_slug'] ?? '') === 'ক্বারীয়ানা-১২টি-সংকেত' && $res1->getStatusCode() === 200;

// Test 4b: Parameter regex constraint (numeric vs alpha)
$_SERVER['REQUEST_URI'] = '/courses/abc';
$res2 = $router->dispatch(new Request());
$test2Passed = $res2->getStatusCode() === 404; // Non-digits should not match {id:\d+}

$_SERVER['REQUEST_URI'] = '/courses/42';
$res3 = $router->dispatch(new Request());
$test3Passed = ($captured['course_id'] ?? '') === '42' && $res3->getStatusCode() === 200;

$_SERVER = $backup;

// 5. Database SQL Injection Resilience Test
$db = Database::getInstance();
$injectionAttempts = [
    "' OR '1'='1",
    "1; DROP TABLE users; --",
    "admin' --",
    "1' UNION SELECT 1,2,3,4,5,6,7,8,9 --",
];

$sqlInjectionResults = [];
foreach ($injectionAttempts as $attempt) {
    $stmt = $db->prepare("SELECT * FROM `users` WHERE `username` = :u");
    $stmt->execute(['u' => $attempt]);
    $res = $stmt->fetchAll();
    $sqlInjectionResults[] = [
        'payload' => $attempt,
        'returned_rows' => count($res),
        'safe' => count($res) === 0,
    ];
}

echo json_encode([
    'slug_tests' => $slugResults,
    'numeral_tests' => $numResults,
    'csrf_tests' => $csrfCases,
    'router_tests' => [
        'bengali_ligature_slug_match' => $test1Passed,
        'regex_constraint_rejection' => $test2Passed,
        'regex_constraint_acceptance' => $test3Passed,
    ],
    'sql_injection_tests' => $sqlInjectionResults,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
