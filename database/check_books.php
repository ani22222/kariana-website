<?php
require_once __DIR__ . '/../core/Autoloader.php';
\Core\Autoloader::register();

$db = \Core\Database::getInstance();
$books = $db->query("SELECT id, title, slug, price, discount_price, is_featured, sort_order FROM books ORDER BY sort_order ASC")->fetchAll();
echo json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
