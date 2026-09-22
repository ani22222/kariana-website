<?php
require_once __DIR__ . '/../core/Autoloader.php';
\Core\Autoloader::register();

$db = \Core\Database::getInstance();

// Update Book 3 to be the flagship "কারিয়ানা কুরআন (বাংলা অর্থসহ কুরআন মাজীদ)" with price 1000 and sort order 1
$db->exec("
    UPDATE `books` SET 
        `title` = 'কারিয়ানা কুরআন (বাংলা অর্থসহ কুরআন মাজীদ)',
        `slug` = 'kariana-quran-bangla-ortho-soho-quran',
        `price` = 1200.00,
        `discount_price` = 1000.00,
        `is_featured` = 1,
        `sort_order` = 1,
        `description` = '১২টি বিশেষ সাংকেতিক চিহ্ন এবং তাজবীদ কালার কোড সহ বাংলা অর্থসহ পূর্ণাঙ্গ কুরআনুল কারীম। মাত্র কয়েক সপ্তাহে সহীহ তিলাওয়াত ও কুরআন অনুধাবনের অপূর্ব গ্রন্থ।'
    WHERE `id` = 3 OR `slug` = 'kariana-30-para-quran-majeed'
");

// Update Book 2: আমপারা শরীফ
$db->exec("
    UPDATE `books` SET 
        `title` = 'ক্বারীয়ানা আমপারা শরীফ (তাজবীদ কালার কোডেড)',
        `slug` = 'kariana-ampara-sharif',
        `price` = 180.00,
        `discount_price` = 150.00,
        `is_featured` = 1,
        `sort_order` = 2,
        `description` = 'সূরা নাবা থেকে সূরা নাস পর্যন্ত তাজবীদ নির্দেশিকা ও প্রতিটি পৃষ্ঠায় ভিডিও লেসনের কিউআর কোডসহ মুদ্রিত।'
    WHERE `id` = 2 OR `slug` = 'kariana-ampara-sharif'
");

// Update Book 1: কায়েদা
$db->exec("
    UPDATE `books` SET 
        `title` = 'সহজ ক্বারীয়ানা কায়েদা (১২টি সংকেত সম্বলিত)',
        `slug` = 'sahaj-kariana-qaida',
        `price` = 150.00,
        `discount_price` = 120.00,
        `is_featured` = 1,
        `sort_order` = 3,
        `description` = 'শিশুদের ও বয়স্কদের জন্য ৩০ দিনে সহীহ কুরআন তিলাওয়াত শেখার বৈজ্ঞানিক নূরানী কায়েদা।'
    WHERE `id` = 1 OR `slug` = 'sahaj-kariana-qaida'
");

echo "✓ Books updated with authentic titles, prices and sort orders.\n";
