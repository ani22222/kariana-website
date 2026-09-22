<?php
/**
 * Test Dari (।), Taka (৳), and edge cases in Router and Apache
 */
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\Router;
use Core\Request;
use Core\Response;
use Core\BengaliHelper;

$tests = [];

// 1. Check createSlug with Dari, Taka, Isshar
$slugs = [
    'dari_trailing' => [
        'input' => 'সহজ পদ্ধতিতে কুরআন শিক্ষা।',
        'slug'  => BengaliHelper::createSlug('সহজ পদ্ধতিতে কুরআন শিক্ষা।'),
    ],
    'dari_middle' => [
        'input' => 'কুরআন তিলাওয়াত। সহজ নিয়ম',
        'slug'  => BengaliHelper::createSlug('কুরআন তিলাওয়াত। সহজ নিয়ম'),
    ],
    'taka_sign' => [
        'input' => 'বইয়ের মূল্য ৫০৳ মাত্র',
        'slug'  => BengaliHelper::createSlug('বইয়ের মূল্য ৫০৳ মাত্র'),
    ],
    'double_dari' => [
        'input' => 'প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়',
        'slug'  => BengaliHelper::createSlug('প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়'),
    ],
    'arabic_phrase' => [
        'input' => 'القرآن الكريم',
        'slug'  => BengaliHelper::createSlug('القرآن الكريم'),
    ],
    'mixed_arabic_bengali' => [
        'input' => 'কুরআনুল কারীম (القرآن الكريم)',
        'slug'  => BengaliHelper::createSlug('কুরআনুল কারীম (القرآن الكريم)'),
    ],
    'symbols_only' => [
        'input' => '??? --- !!!',
        'slug'  => BengaliHelper::createSlug('??? --- !!!'),
    ],
];

// 2. Can Router.php route a slug with Dari?
$router = new Router();
$captured = null;
$router->get('/blog/{slug}', function(Request $req, string $slug) use (&$captured) {
    $captured = $slug;
    return Response::json(['slug' => $slug]);
});

$testDariSlug = $slugs['dari_trailing']['slug'];
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/blog/' . rawurlencode($testDariSlug);
$_SERVER['SCRIPT_NAME'] = '/index.php';

$req = new Request();
$resp = $router->dispatch($req);

$routerResult = [
    'tested_slug' => $testDariSlug,
    'captured'    => $captured,
    'status_code' => $resp->getStatusCode(),
    'matched'     => $captured === $testDariSlug,
];

echo json_encode([
    'slug_generation_tests' => $slugs,
    'router_with_dari'      => $routerResult,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
