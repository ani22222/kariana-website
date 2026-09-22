<?php
/**
 * Database Seeder Runner
 * Seeds admin user, 64 districts, initial categories, courses, books, sample blog post, and settings
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\Database;

echo "=== [কারিয়ানা কুরআন] ডাটাবেজ সিডার শুরু ===\n";

try {
    $db = Database::getInstance();

    // 1. Seed Admin User
    echo "১. প্রশাসক ব্যবহারকারী সিড করা হচ্ছে...\n";
    $adminPassword = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare("
        INSERT INTO `users` (`name`, `email`, `username`, `password`, `role`, `created_at`)
        VALUES (:name, :email, :username, :password, :role, NOW())
        ON DUPLICATE KEY UPDATE `password` = VALUES(`password`), `name` = VALUES(`name`)
    ");
    $stmt->execute([
        'name'     => 'প্রধান প্রশাসক',
        'email'    => 'admin@karianaquran.com',
        'username' => 'admin',
        'password' => $adminPassword,
        'role'     => 'admin',
    ]);
    echo "✓ প্রশাসক একাউন্ট তৈরি: ইউজার 'admin' / পাসওয়ার্ড 'admin123'\n";

    // 2. Seed 64 Districts
    echo "২. ৬৪টি জেলার তথ্য এবং ইসলামিক ফাউন্ডেশন অফসেট সিড করা হচ্ছে...\n";
    $districts = require dirname(__DIR__) . '/config/districts.php';
    $districtStmt = $db->prepare("
        INSERT INTO `prayer_districts` 
        (`id`, `name_bn`, `name_en`, `division_bn`, `latitude`, `longitude`, 
         `fajr_offset`, `sunrise_offset`, `dhuhr_offset`, `asr_offset`, `maghrib_offset`, `isha_offset`, `created_at`)
        VALUES 
        (:id, :name_bn, :name_en, :division_bn, :latitude, :longitude, 
         :fajr_offset, :sunrise_offset, :dhuhr_offset, :asr_offset, :maghrib_offset, :isha_offset, NOW())
        ON DUPLICATE KEY UPDATE
        `name_bn` = VALUES(`name_bn`),
        `name_en` = VALUES(`name_en`),
        `division_bn` = VALUES(`division_bn`),
        `latitude` = VALUES(`latitude`),
        `longitude` = VALUES(`longitude`),
        `fajr_offset` = VALUES(`fajr_offset`),
        `sunrise_offset` = VALUES(`sunrise_offset`),
        `dhuhr_offset` = VALUES(`dhuhr_offset`),
        `asr_offset` = VALUES(`asr_offset`),
        `maghrib_offset` = VALUES(`maghrib_offset`),
        `isha_offset` = VALUES(`isha_offset`)
    ");

    foreach ($districts as $d) {
        $districtStmt->execute([
            'id'             => $d['id'],
            'name_bn'        => $d['name_bn'],
            'name_en'        => $d['name_en'],
            'division_bn'    => $d['division_bn'],
            'latitude'       => $d['latitude'],
            'longitude'      => $d['longitude'],
            'fajr_offset'    => $d['fajr_offset'],
            'sunrise_offset' => $d['sunrise_offset'],
            'dhuhr_offset'   => $d['dhuhr_offset'],
            'asr_offset'     => $d['asr_offset'],
            'maghrib_offset' => $d['maghrib_offset'],
            'isha_offset'    => $d['isha_offset'],
        ]);
    }
    echo "✓ ৬৪টি জেলার তথ্য সংরক্ষিত হয়েছে।\n";

    // 3. Seed Categories
    echo "৩. ব্লগ ক্যাটাগরি সিড করা হচ্ছে...\n";
    $categories = [
        [
            'name'        => 'কুরআন শিক্ষা',
            'slug'        => 'quran-learning',
            'description' => 'সহজ ও বিশুদ্ধ পদ্ধতিতে কুরআনুল কারীম শেখার নির্দেশিকা ও প্রবন্ধ।',
            'sort_order'  => 1,
        ],
        [
            'name'        => 'তাজবীদ ও তারতীল',
            'slug'        => 'tajweed-tartil',
            'description' => 'ক্বারীয়ানা তাজবীদ সংকেত, মাখরাজ ও সিফাত বিষয়ক আলোচনা।',
            'sort_order'  => 2,
        ],
        [
            'name'        => 'হিফজুল কুরআন',
            'slug'        => 'hifzul-quran',
            'description' => 'হিফজ সংরক্ষণ, দাওর ও তিলাওয়াত কৌশলের টিপস।',
            'sort_order'  => 3,
        ],
        [
            'name'        => 'ইসলামী জীবন ও বিধান',
            'slug'        => 'islamic-guidance',
            'description' => 'দৈনন্দিন দোয়া, নামায, যাকাত ও মাসআলা সম্পর্কিত সঠিক দিকনির্দেশনা।',
            'sort_order'  => 4,
        ],
        [
            'name'        => 'প্রতিষ্ঠান ও একাডেমি সংবাদ',
            'slug'        => 'announcements',
            'description' => 'কারিয়ানা নূরানী কুরআন একাডেমির ভর্তি ও সর্বশেষ সংবাদ।',
            'sort_order'  => 5,
        ],
    ];

    $catStmt = $db->prepare("
        INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `created_at`)
        VALUES (:name, :slug, :description, :sort_order, NOW())
        ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `description` = VALUES(`description`)
    ");
    foreach ($categories as $cat) {
        $catStmt->execute($cat);
    }
    echo "✓ ৫টি ক্যাটাগরি সংরক্ষিত হয়েছে।\n";

    // 4. Seed Courses
    echo "৪. প্রাথমিক কোর্সসমূহ সিড করা হচ্ছে...\n";
    $courses = [
        [
            'title'           => 'সহজ ক্বারীয়ানা কায়েদা ও তাজবীদ শিক্ষা',
            'slug'            => 'sahaj-kariana-qaida-tajweed',
            'course_code'     => 'KQ-C01',
            'category'        => 'tajweed',
            'duration'        => '৩ মাস',
            'total_classes'   => 36,
            'fee'             => '৳ ১,৫০০',
            'class_schedule'  => 'শনি-সোম-বুধ (রাত ৮:৩০ - ৯:৩০)',
            'instructor_name' => 'মাওলানা কারী মাহমুদুল হাসান',
            'description'     => 'ক্বারীয়ানা পদ্ধতির বিশেষ ১২টি সংকেত ও মারকাজভিত্তিক আধুনিক বিজ্ঞানসম্মত কুরআন পাঠ কোর্স। শিশু ও বয়স্কদের জন্য সমান উপযোগী।',
            'syllabus'        => "১ম মাস: হরফ চেনা, মাখরাজ ও হরকতের উচ্চারণ\n২য় মাস: তানভীন, জযম ও ক্বারীয়ানা ১২টি তাজবীদ সংকেত\n৩য় মাস: সরাসরি কুরআনুল কারীমের আমপারা মস্ক ও বিশুদ্ধ তিলাওয়াত অনুশীলন",
            'cover_image'     => 'assets/images/courses/qaida.jpg',
            'admission_open'  => 1,
            'is_featured'     => 1,
            'sort_order'      => 1,
        ],
        [
            'title'           => 'নাজেরা কুরআন পাঠ ও সহীহ তিলাওয়াত কোর্স',
            'slug'            => 'nazera-quran-shikkha',
            'course_code'     => 'KQ-C02',
            'category'        => 'nazera',
            'duration'        => '৬ মাস',
            'total_classes'   => 72,
            'fee'             => '৳ ২,৫০০',
            'class_schedule'  => 'রবি-মঙ্গল-বৃহস্পতি (রাত ৯:০০ - ১০:০০)',
            'instructor_name' => 'মাওলানা হাফেজ মুফতী আবু সালেহ',
            'description'     => 'সম্পূর্ণ ৩০ পারা সহীহ তারতীলের সাথে নাজেরা পড়ার বিশেষ কোর্স। কুরআনুল কারীমের প্রতিটি সূরায় ওয়াকফ ও ইবতিদা আয়ত্তকরণ।',
            'syllabus'        => "মডিউল ১: সুরা ফাতিহা ও শেষ ১০ সূরার বিশুদ্ধ মস্ক\nমডিউল ২: বড় সূরাসমূহের কঠিন শব্দ ও তারতীল\nমডিউল ৩: সম্পূর্ণ ৩০ পারা তিলাওয়াত ও সনদ প্রদান",
            'cover_image'     => 'assets/images/courses/nazera.jpg',
            'admission_open'  => 1,
            'is_featured'     => 1,
            'sort_order'      => 2,
        ],
        [
            'title'           => 'হিফজুল কুরআন ও মারকাজুল হুফফাজ',
            'slug'            => 'hifzul-quran-course',
            'course_code'     => 'KQ-C03',
            'category'        => 'hifz',
            'duration'        => '২ বছর',
            'total_classes'   => 240,
            'fee'             => '৳ ৩,০০০',
            'class_schedule'  => 'প্রতিদিন সকাল ও সন্ধ্যা সেশন',
            'instructor_name' => 'আন্তর্জাতিক পুরস্কারপ্রাপ্ত হাফেজ কারী শফিকুল ইসলাম',
            'description'     => 'আন্তর্জাতিক মানের সুর ও তারতীলের সাথে পূর্ণাঙ্গ কুরআন মুখস্থ করার বিশেষ হিফজ ব্যাচ। অভিজ্ঞ হুফফাজের সার্বক্ষণিক নিবিড় তত্ত্বাবধান।',
            'syllabus'        => "পর্যায় ১: সাবাক (নতুন পাঠ মুখস্থ)\nপর্যায় ২: সাবাকী (সাম্প্রতিক পারার পুনরাবৃত্তি)\nপর্যায় ৩: আমুখতা (বিগত সকল পারার মজবুত দাওর)",
            'cover_image'     => 'assets/images/courses/hifz.jpg',
            'admission_open'  => 1,
            'is_featured'     => 1,
            'sort_order'      => 3,
        ],
        [
            'title'           => 'উন্নত ক্বেরাত ও মাক্বামাত প্রশিক্ষণ',
            'slug'            => 'advanced-qirat-maqamat',
            'course_code'     => 'KQ-C04',
            'category'        => 'qirat',
            'duration'        => '৪ মাস',
            'total_classes'   => 48,
            'fee'             => '৳ ২,০০০',
            'class_schedule'  => 'শুক্রবার ও শনিবার (বিকাল ৪:০০ - ৬:০০)',
            'instructor_name' => 'শায়খ কারী আহমাদুল্লাহ আল-আজহারী',
            'description'     => 'মিশরীয় ও হিজাজী লাহনে কুরআন তিলাওয়াত, শ্বাস নিয়ন্ত্রণ ও বিভিন্ন মাক্বামাত (বায়াতী, সিগাহ, হিজায, নাহারওয়ান্দ) শিক্ষা।',
            'syllabus'        => "মাক্বামাত পরিচিতি, সুরের আরোহণ ও অবতরণ, আন্তর্জাতিক তিলাওয়াত প্রতিযোগিতা প্রস্তুতি",
            'cover_image'     => 'assets/images/courses/qirat.jpg',
            'admission_open'  => 1,
            'is_featured'     => 0,
            'sort_order'      => 4,
        ],
    ];

    $courseStmt = $db->prepare("
        INSERT INTO `courses` 
        (`title`, `slug`, `course_code`, `category`, `duration`, `total_classes`, `fee`, `class_schedule`, `instructor_name`, `description`, `syllabus`, `cover_image`, `admission_open`, `is_featured`, `sort_order`, `created_at`)
        VALUES 
        (:title, :slug, :course_code, :category, :duration, :total_classes, :fee, :class_schedule, :instructor_name, :description, :syllabus, :cover_image, :admission_open, :is_featured, :sort_order, NOW())
        ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `fee` = VALUES(`fee`), `description` = VALUES(`description`)
    ");
    foreach ($courses as $crs) {
        $courseStmt->execute($crs);
    }
    echo "✓ ৪টি কোর্স সফলভাবে সিড করা হয়েছে।\n";

    // 5. Seed Books
    echo "৫. ক্বারীয়ানা প্রকাশনা ও গ্রন্থতালিকা সিড করা হচ্ছে...\n";
    $books = [
        [
            'title'          => 'সহজ ক্বারীয়ানা কায়েদা (১২টি সংকেত সম্বলিত)',
            'slug'           => 'sahaj-kariana-qaida',
            'author'         => 'মুফতী কারী সাইয়েদ আহমদ',
            'isbn'           => '978-984-34-1234-1',
            'pages_count'    => 48,
            'price'          => 150.00,
            'discount_price' => 120.00,
            'cover_image'    => 'assets/images/books/qaida-cover.jpg',
            'pdf_preview_url'=> 'assets/pdf/qaida-sample.pdf',
            'buy_link'       => 'https://wa.me/8801700000000?text=I+want+to+order+Kariana+Qaida',
            'stock_status'   => 'in_stock',
            'description'    => '১২টি বৈজ্ঞানিক সংকেত ও মারকাজভিত্তিক আধুনিক নূরানী কায়েদা যা মাত্র ৩০ দিনে বিশুদ্ধ কুরআন পাঠের নিশ্চয়তা দেয়। রঙিন তাজবীদ নির্দেশিকা যুক্ত।',
            'is_featured'    => 1,
            'sort_order'     => 1,
        ],
        [
            'title'          => 'ক্বারীয়ানা আমপারা শরীফ (তাজবীদ কালার কোডেড)',
            'slug'           => 'kariana-ampara-sharif',
            'author'         => 'কারিআনা নূরানী কুরআন একাডেমি',
            'isbn'           => '978-984-34-1234-2',
            'pages_count'    => 64,
            'price'          => 180.00,
            'discount_price' => 150.00,
            'cover_image'    => 'assets/images/books/ampara-cover.jpg',
            'pdf_preview_url'=> 'assets/pdf/ampara-sample.pdf',
            'buy_link'       => 'https://wa.me/8801700000000?text=I+want+to+order+Ampara',
            'stock_status'   => 'in_stock',
            'description'    => 'সূরা নাবা থেকে সূরা নাস পর্যন্ত তাজবীদ নির্দেশিকা ও বিশেষ সংকেতযুক্ত পূর্ণাঙ্গ আমপারা। প্রতিটি পৃষ্ঠায় ভিডিও পাঠের কিউআর কোড অন্তর্ভুক্ত।',
            'is_featured'    => 1,
            'sort_order'     => 2,
        ],
        [
            'title'          => 'ক্বারীয়ানা ৩০ পারা নূরানী কুরআন মাজীদ',
            'slug'           => 'kariana-30-para-quran-majeed',
            'author'         => 'কারিআনা নূরানী কুরআন একাডেমি',
            'isbn'           => '978-984-34-1234-3',
            'pages_count'    => 611,
            'price'          => 850.00,
            'discount_price' => 750.00,
            'cover_image'    => 'assets/images/books/quran-cover.jpg',
            'pdf_preview_url'=> 'assets/pdf/quran-sample.pdf',
            'buy_link'       => 'https://wa.me/8801700000000?text=I+want+to+order+Full+Quran',
            'stock_status'   => 'in_stock',
            'description'    => 'অনবদ্য মুদ্রণ, উচ্চমানের চোখ জুড়ানো কাগজ ও ক্বারীয়ানা ১২টি তাজবীদ সংকেতযুক্ত বাংলা অর্থসহ পূর্ণাঙ্গ কুরআনুল কারীম।',
            'is_featured'    => 1,
            'sort_order'     => 3,
        ],
    ];

    $bookStmt = $db->prepare("
        INSERT INTO `books` 
        (`title`, `slug`, `author`, `isbn`, `pages_count`, `price`, `discount_price`, `cover_image`, `pdf_preview_url`, `buy_link`, `stock_status`, `description`, `is_featured`, `sort_order`, `created_at`)
        VALUES 
        (:title, :slug, :author, :isbn, :pages_count, :price, :discount_price, :cover_image, :pdf_preview_url, :buy_link, :stock_status, :description, :is_featured, :sort_order, NOW())
        ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `price` = VALUES(`price`), `description` = VALUES(`description`)
    ");
    foreach ($books as $bk) {
        $bookStmt->execute($bk);
    }
    echo "✓ ৩টি প্রকাশনা সফলভাবে সিড করা হয়েছে।\n";

    // 6. Seed Sample Blog Post with clean Bengali slug
    echo "৬. নমুনা বাংলা ব্লগ পোস্ট সিড করা হচ্ছে...\n";
    $samplePost = [
        'title'            => 'সহজ পদ্ধতিতে কুরআন তিলাওয়াত ও ক্বারীয়ানা ১২টি সংকেতের ভূমিকা',
        'slug'             => 'সহজ-পদ্ধতিতে-কুরআন-শেখা',
        'category_id'      => 1,
        'author_id'        => 1,
        'excerpt'          => 'শুদ্ধভাবে কুরআন পাঠের জন্য তাজবীদের মৌলিক নিয়ম ও ক্বারীয়ানা পদ্ধতির বিশেষ ১২টি সংকেত কীভাবে শিক্ষার্থীকে মাত্র কয়েক সপ্তাহে দক্ষ করে তোলে...',
        'content'          => "<h2>বিসমিল্লাহির রাহমানির রাহীম</h2>
<p>পবিত্র কুরআনুল কারীম মানবজাতির জন্য সর্বশ্রেষ্ঠ পথনির্দেশিকা। বিশুদ্ধ ও সহীহভাবে আল্লাহর কালাম তিলাওয়াত করা প্রত্যেক মুসলিমের জন্য ঈমানী দায়িত্ব। কিন্তু তাজবীদের জটিল নিয়মাবলীর কারণে অনেকেই কুরআন বিশুদ্ধভাবে শিখতে ভয় পান।</p>
<h3>ক্বারীয়ানা পদ্ধতির বিশেষত্ব</h3>
<p>ক্বারীয়ানা নূরানী পদ্ধতি এমনভাবে সাজানো হয়েছে যেখানে জটিল ব্যাকরণগত সংজ্ঞা মুখস্থ না করিয়ে বিশেষ ১২টি রঙের সংকেতের মাধ্যমে শিক্ষার্থীকে স্বাভাবিকভাবে সঠিক উচ্চারণে অভ্যস্ত করানো হয়।</p>
<ul>
    <li><strong>মাদে আসলী ও মাদে আরিদ্:</strong> টানের মাত্রা চোখের দেখায় নির্ণয় করা যায়।</li>
    <li><strong>ওয়াজিব গুন্নাহ ও ইখফা:</strong> নাকের বাঁশিতে সঠিক গুঞ্জরণ নিশ্চিত হয়।</li>
    <li><strong>কলকলাহ:</strong> বিশেষ ধ্বনি তরঙ্গের মাধ্যমে সঠিক প্রতিধ্বনি তৈরি হয়।</li>
</ul>
<p>আজই আপনার কুরআন তিলাওয়াতকে বিশুদ্ধ ও সুন্দর করতে আমাদের নিয়মিত ব্যাচসমূহে অংশ নিন।</p>",
        'cover_image'      => 'assets/images/blog/quran-recitation.jpg',
        'meta_title'       => 'সহজ পদ্ধতিতে কুরআন তিলাওয়াত ও ক্বারীয়ানা ১২টি সংকেতের ভূমিকা | কারিয়ানা কুরআন',
        'meta_description' => 'ক্বারীয়ানা পদ্ধতির বিশেষ ১২টি সংকেত ও মারকাজভিত্তিক নিয়মে অতি সহজে সহীহ তিলাওয়াত শিখুন। বাংলা ব্লগ পোস্ট।',
        'canonical_url'    => 'http://localhost:8015/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা',
        'tags'             => 'কুরআন শিক্ষা, তাজবীদ, ক্বারীয়ানা পদ্ধতি, সহজ কুরআন',
        'status'           => 'published',
        'views_count'      => 128,
    ];

    $postStmt = $db->prepare("
        INSERT INTO `posts` 
        (`title`, `slug`, `category_id`, `author_id`, `excerpt`, `content`, `cover_image`, `meta_title`, `meta_description`, `canonical_url`, `tags`, `status`, `views_count`, `published_at`, `created_at`)
        VALUES 
        (:title, :slug, :category_id, :author_id, :excerpt, :content, :cover_image, :meta_title, :meta_description, :canonical_url, :tags, :status, :views_count, NOW(), NOW())
        ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `content` = VALUES(`content`)
    ");
    $postStmt->execute($samplePost);
    echo "✓ নমুনা বাংলা পোস্ট তৈরি হয়েছে (স্লাগ: সহজ-পদ্ধতিতে-কুরআন-শেখা)।\n";

    // 7. Seed Zakat Settings
    echo "৭. যাকাত নিসাব ডিফল্ট মান সিড করা হচ্ছে...\n";
    $db->exec("
        INSERT INTO `zakat_settings` (`id`, `silver_nisab_tola`, `gold_nisab_tola`, `silver_rate_per_bhori`, `gold_rate_per_bhori`, `zakat_rate_percent`, `notes_bn`)
        VALUES (1, 52.50, 7.50, 2000.00, 125000.00, 2.50, 'হানাফী মাযহাব অনুসারে নগদ অর্থ ও মিশ্র সম্পদের ক্ষেত্রে রূপার নিসাব (৫২.৫ তোলা) প্রযোজ্য।')
        ON DUPLICATE KEY UPDATE `silver_rate_per_bhori` = VALUES(`silver_rate_per_bhori`), `gold_rate_per_bhori` = VALUES(`gold_rate_per_bhori`)
    ");
    echo "✓ যাকাত কনফিগারেশন সংরক্ষিত।\n";

    // 8. Seed Site Settings
    echo "৮. ওয়েবসাইট সেটিংস সিড করা হচ্ছে...\n";
    $settings = [
        'site_name'        => 'কারিয়ানা কুরআন',
        'site_tagline'     => 'সহজ ও সহীহ পদ্ধতিতে কুরআন তিলাওয়াত, হিফজ ও তাজবীদ শিক্ষা',
        'site_phone'       => '+880 1700-000000',
        'site_email'       => 'info@karianaquran.com',
        'site_address'     => 'ঢাকা, বাংলাদেশ',
        'facebook_url'     => 'https://facebook.com/karianaquran',
        'youtube_url'      => 'https://youtube.com/@karianaquran',
        'quran_reader_url' => 'http://localhost:8014',
    ];

    $settingStmt = $db->prepare("
        INSERT INTO `site_settings` (`setting_key`, `setting_value`, `group_name`, `created_at`)
        VALUES (:key, :val, 'general', NOW())
        ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)
    ");
    foreach ($settings as $k => $v) {
        $settingStmt->execute(['key' => $k, 'val' => $v]);
    }
    echo "✓ ৮টি সাইট সেটিংস সংরক্ষিত।\n";

    echo "\n=== সিডার সফলভাবে সমাপ্ত হয়েছে! ===\n";
    if (php_sapi_name() === 'cli' && !defined('SEED_INCLUDED')) {
        exit(0);
    }
    return;
} catch (Throwable $e) {
    echo "\n[ERROR] সিডার ব্যর্থ হয়েছে: " . $e->getMessage() . "\n";
    echo "ফাইল: " . $e->getFile() . " (লাইন: " . $e->getLine() . ")\n";
    if (php_sapi_name() === 'cli' && !defined('SEED_INCLUDED')) {
        exit(1);
    }
    throw $e;
}

