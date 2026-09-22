<?php
/**
 * Migration & Seeder for District Directors, Teachers, and Requests
 * Kariana Quran Islamic Educational Portal & CMS
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/core/Autoloader.php';
\Core\Autoloader::register();

$config = require dirname(__DIR__) . '/config/database.php';
$pdo = new PDO(
    "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}",
    $config['username'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "=== [কারিয়ানা কুরআন] জেলা পরিচালক ও শিক্ষক মডিউল মাইগ্রেশন শুরু ===\n";

// 1. Create Directors Table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `directors` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `designation` VARCHAR(100) NOT NULL DEFAULT 'জেলা পরিচালক',
    `district_id` INT UNSIGNED NULL,
    `district_name` VARCHAR(100) NOT NULL,
    `division_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `whatsapp` VARCHAR(30) NULL,
    `email` VARCHAR(150) NULL,
    `photo` VARCHAR(255) NULL,
    `address` TEXT NULL,
    `qualification` VARCHAR(255) NULL,
    `status` ENUM('active', 'suspended', 'expelled') NOT NULL DEFAULT 'active',
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `bio` TEXT NULL,
    `total_books_ordered` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_directors_district` (`district_name`),
    INDEX `idx_directors_division` (`division_name`),
    INDEX `idx_directors_status` (`status`),
    INDEX `idx_directors_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'directors' টেবিল প্রস্তুত।\n";

// 2. Create Teachers / Muallims Table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `teachers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `director_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `phone` VARCHAR(30) NOT NULL,
    `qualification` VARCHAR(255) NULL,
    `photo` VARCHAR(255) NULL,
    `location_type` ENUM('home', 'mosque', 'madrasa', 'institution') NOT NULL DEFAULT 'home',
    `area_name` VARCHAR(150) NOT NULL,
    `total_students` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('active', 'training', 'inactive') NOT NULL DEFAULT 'active',
    `joined_date` DATE NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`director_id`) REFERENCES `directors`(`id`) ON DELETE CASCADE,
    INDEX `idx_teachers_director` (`director_id`),
    INDEX `idx_teachers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'teachers' টেবিল প্রস্তুত।\n";

// 3. Create Director Requests Table (ID cards, Training, Book Orders)
$pdo->exec("
CREATE TABLE IF NOT EXISTS `director_requests` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `director_id` INT UNSIGNED NOT NULL,
    `request_type` ENUM('id_card', 'training', 'book_order', 'general') NOT NULL DEFAULT 'general',
    `details` TEXT NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 0,
    `status` ENUM('pending', 'approved', 'rejected', 'completed') NOT NULL DEFAULT 'pending',
    `admin_note` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`director_id`) REFERENCES `directors`(`id`) ON DELETE CASCADE,
    INDEX `idx_requests_director` (`director_id`),
    INDEX `idx_requests_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'director_requests' টেবিল প্রস্তুত।\n";

// 4. Create SMS Gateway Logs Table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `sms_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `recipient_type` VARCHAR(50) NOT NULL DEFAULT 'single',
    `recipient_phone` VARCHAR(30) NOT NULL,
    `recipient_name` VARCHAR(150) NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('sent', 'failed', 'queued') NOT NULL DEFAULT 'sent',
    `gateway_response` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'sms_logs' টেবিল প্রস্তুত।\n";

// 5. Seed Sample Realistic District Directors across Bangladesh
$defaultPassword = password_hash('director123', PASSWORD_BCRYPT);

$sampleDirectors = [
    [
        'name'          => 'মাওলানা মুফতি আব্দুল্লাহ আল-মামুন',
        'designation'   => 'জেলা পরিচালক (ঢাকা)',
        'district_name' => 'ঢাকা',
        'division_name' => 'ঢাকা',
        'phone'         => '01711-234567',
        'whatsapp'      => '01711-234567',
        'email'         => 'dhaka.director@karianaquran.com',
        'address'       => 'মিরপুর-১০, ঢাকা-১২১৬',
        'qualification' => 'দাওরায়ে হাদীস, বেফাকুল মাদারিসিল আরাবিয়া',
        'status'        => 'active',
        'username'      => 'director_dhaka',
        'slug'          => 'mufti-abdullah-dhaka',
        'bio'           => 'ঢাকা জেলার প্রধান সমন্বয়ক ও কারিয়ানা কুরআন প্রশিক্ষণ প্রকল্পের দীর্ঘদিনের দায়িত্বপ্রাপ্ত পরিচালক।'
    ],
    [
        'name'          => 'হাফেজ মাওলানা মাহমুদুল হাসান',
        'designation'   => 'জেলা পরিচালক (চট্টগ্রাম)',
        'district_name' => 'চট্টগ্রাম',
        'division_name' => 'চট্টগ্রাম',
        'phone'         => '01812-345678',
        'whatsapp'      => '01812-345678',
        'email'         => 'ctg.director@karianaquran.com',
        'address'       => 'আন্দরকিল্লা, চট্টগ্রাম',
        'qualification' => 'হিফজুল কুরআন ও তাফসীর বিভাগ',
        'status'        => 'active',
        'username'      => 'director_ctg',
        'slug'          => 'hafez-mahmudul-chittagong',
        'bio'           => 'চট্টগ্রাম জোনের ৬০+ কুরআনিক সেন্টারের সরাসরি তদারককারী ও সিনিয়র কারী।'
    ],
    [
        'name'          => 'মাওলানা শফিকুল ইসলাম',
        'designation'   => 'জেলা পরিচালক (সিলেট)',
        'district_name' => 'সিলেট',
        'division_name' => 'সিলেট',
        'phone'         => '01713-987654',
        'whatsapp'      => '01713-987654',
        'email'         => 'sylhet.director@karianaquran.com',
        'address'       => 'আম্বরখানা, সিলেট',
        'qualification' => 'মাওলানা, জামিয়া কাসিমুল উলুম দরগাহে হযরত শাহজালাল (রহ.)',
        'status'        => 'active',
        'username'      => 'director_sylhet',
        'slug'          => 'shafiqul-islam-sylhet',
        'bio'           => 'সিলেট বিভাগে ঘরে ঘরে সহীহ কুরআন শিক্ষার মহতী কার্যক্রম পরিচালনা করছেন।'
    ],
    [
        'name'          => 'হাফেজ ক্বারী আব্দুর রহমান',
        'designation'   => 'জেলা পরিচালক (রাজশাহী)',
        'district_name' => 'রাজশাহী',
        'division_name' => 'রাজশাহী',
        'phone'         => '01715-112233',
        'whatsapp'      => '01715-112233',
        'email'         => 'rajshahi.director@karianaquran.com',
        'address'       => 'সাহেব বাজার, রাজশাহী',
        'qualification' => 'ইলমুত তাজবীদ ও ক্বেরাতে সাবআ',
        'status'        => 'active',
        'username'      => 'director_rajshahi',
        'slug'          => 'abdur-rahman-rajshahi',
        'bio'           => 'রাজশাহী জেলায় ১২টি সাংকেতিক চিহ্নে কুরআন শিক্ষার প্রশিক্ষক তৈরি করছেন।'
    ],
    [
        'name'          => 'মাওলানা রফিকুল ইসলাম কাসেমী',
        'designation'   => 'জেলা পরিচালক (খুলনা)',
        'district_name' => 'খুলনা',
        'division_name' => 'খুলনা',
        'phone'         => '01911-445566',
        'whatsapp'      => '01911-445566',
        'email'         => 'khulna.director@karianaquran.com',
        'address'       => 'ময়লাপোতা মোড়, খুলনা',
        'qualification' => 'এম.এ (ইসলামিক স্টাডিজ), দাওরায়ে হাদীস',
        'status'        => 'active',
        'username'      => 'director_khulna',
        'slug'          => 'rafiqul-islam-khulna',
        'bio'           => 'খুলনা অঞ্চলে বয়স্ক ও শিশু কুরআন শিক্ষার ক্লাস্টার সমন্বয়কারী।'
    ],
    [
        'name'          => 'হাফেজ তরিকুল ইসলাম',
        'designation'   => 'জেলা পরিচালক (বগুড়া)',
        'district_name' => 'বগুড়া',
        'division_name' => 'রাজশাহী',
        'phone'         => '01714-778899',
        'whatsapp'      => '01714-778899',
        'email'         => 'bogra.director@karianaquran.com',
        'address'       => 'বনানী মোড়, বগুড়া',
        'qualification' => 'হাফেজে কুরআন ও কারিয়ানা সার্টিফাইড ট্রেইনার',
        'status'        => 'active',
        'username'      => 'director_bogra',
        'slug'          => 'tariqul-islam-bogra',
        'bio'           => 'বগুড়া জেলায় নূরানী ও ক্বারীয়ানা কায়েদা কার্যক্রমের দায়িত্বপ্রাপ্ত পরিচালক।'
    ],
    [
        'name'          => 'মাওলানা ইব্রাহীম খলিল',
        'designation'   => 'জেলা পরিচালক (কুমিল্লা)',
        'district_name' => 'কুমিল্লা',
        'division_name' => 'চট্টগ্রাম',
        'phone'         => '01816-554433',
        'whatsapp'      => '01816-554433',
        'email'         => 'comilla.director@karianaquran.com',
        'address'       => 'কান্দিরপাড়, কুমিল্লা',
        'qualification' => 'দাওরায়ে হাদীস',
        'status'        => 'suspended', // Suspended sample for testing status badge
        'username'      => 'director_comilla',
        'slug'          => 'ibrahim-khalil-comilla',
        'bio'           => 'সাময়িকভাবে সাংগঠনিক কারণে দায়িত্ব স্থগিত রাখা হয়েছে।'
    ]
];

$stmtDir = $pdo->prepare("
INSERT INTO `directors` (`name`, `designation`, `district_name`, `division_name`, `phone`, `whatsapp`, `email`, `address`, `qualification`, `status`, `username`, `password`, `slug`, `bio`)
VALUES (:name, :designation, :district_name, :division_name, :phone, :whatsapp, :email, :address, :qualification, :status, :username, :password, :slug, :bio)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `status` = VALUES(`status`), `phone` = VALUES(`phone`), `bio` = VALUES(`bio`)
");

foreach ($sampleDirectors as $dir) {
    $dir['password'] = $defaultPassword;
    $stmtDir->execute($dir);
}
echo "✓ জেলা পরিচালকগণের ডাটা সিড সম্পন্ন।\n";

// 6. Seed Sample Teachers under Directors
$sampleTeachers = [
    // Under Dhaka Director (ID 1)
    ['director_id' => 1, 'name' => 'ক্বারী নূরুল হক', 'phone' => '01720-000001', 'qualification' => 'তাজবীদ বিশারদ', 'location_type' => 'home', 'area_name' => 'উত্তরা সেক্টর ৭', 'total_students' => 24, 'status' => 'active'],
    ['director_id' => 1, 'name' => 'মুফতি জুবায়ের আহমদ', 'phone' => '01720-000002', 'qualification' => 'হাফেজ ও ইমাম', 'location_type' => 'mosque', 'area_name' => 'মিরপুর ১ কেন্দ্রীয় মসজিদ', 'total_students' => 45, 'status' => 'active'],
    ['director_id' => 1, 'name' => 'হাফেজ বেলাল হোসেন', 'phone' => '01720-000003', 'qualification' => 'নূরানী শিক্ষক', 'location_type' => 'home', 'area_name' => 'ধানমন্ডি লেক পাড়', 'total_students' => 18, 'status' => 'active'],
    ['director_id' => 1, 'name' => 'ক্বারী মোজাম্মেল হক', 'phone' => '01720-000004', 'qualification' => 'কারিয়ানা ট্রেইনার', 'location_type' => 'madrasa', 'area_name' => 'যাত্রাবাড়ী মাদ্রাসা', 'total_students' => 60, 'status' => 'active'],
    ['director_id' => 1, 'name' => 'মুফতি তানভীর মাহমুদ', 'phone' => '01720-000005', 'qualification' => 'দাওরায়ে হাদিস', 'location_type' => 'home', 'area_name' => 'মোহাম্মদপুর টাউন হল', 'total_students' => 15, 'status' => 'active'],

    // Under Chittagong Director (ID 2)
    ['director_id' => 2, 'name' => 'ক্বারী লোকমান হাকিম', 'phone' => '01820-000001', 'qualification' => 'ক্বারীয়ানা তাজবীদ সার্টিফাইড', 'location_type' => 'home', 'area_name' => 'জিইসি মোড়', 'total_students' => 22, 'status' => 'active'],
    ['director_id' => 2, 'name' => 'হাফেজ আব্দুল মতিন', 'phone' => '01820-000002', 'qualification' => 'হাফেজে কুরআন', 'location_type' => 'mosque', 'area_name' => 'চকবাজার জামে মসজিদ', 'total_students' => 38, 'status' => 'active'],
    ['director_id' => 2, 'name' => 'মুফতি ফয়জুল্লাহ', 'phone' => '01820-000003', 'qualification' => 'দাওরায়ে হাদিস', 'location_type' => 'home', 'area_name' => 'আগ্রাবাদ সি/এ', 'total_students' => 19, 'status' => 'active'],

    // Under Sylhet Director (ID 3)
    ['director_id' => 3, 'name' => 'ক্বারী কামরুল হাসান', 'phone' => '01730-000001', 'qualification' => 'হাফেজ ও ক্বারী', 'location_type' => 'home', 'area_name' => 'উপশহর সিলেট', 'total_students' => 20, 'status' => 'active'],
    ['director_id' => 3, 'name' => 'মাওলানা হাবিবুর রহমান', 'phone' => '01730-000002', 'qualification' => 'নূরানী মুয়াল্লিম', 'location_type' => 'madrasa', 'area_name' => 'টিলাগড় মাদ্রাসা', 'total_students' => 50, 'status' => 'active'],

    // Under Rajshahi Director (ID 4)
    ['director_id' => 4, 'name' => 'হাফেজ জিয়াউর রহমান', 'phone' => '01740-000001', 'qualification' => 'হাফেজে কুরআন', 'location_type' => 'home', 'area_name' => 'রাজশাহী বিশ্ববিদ্যালয় এলাকা', 'total_students' => 16, 'status' => 'active'],

    // Under Khulna Director (ID 5)
    ['director_id' => 5, 'name' => 'মাওলানা আসাদুজ্জামান', 'phone' => '01920-000001', 'qualification' => 'ক্বারীয়ানা শিক্ষক', 'location_type' => 'home', 'area_name' => 'সোনাডাঙ্গা খুলনা', 'total_students' => 25, 'status' => 'active'],
];

$stmtTeach = $pdo->prepare("
INSERT INTO `teachers` (`director_id`, `name`, `phone`, `qualification`, `location_type`, `area_name`, `total_students`, `status`, `joined_date`)
VALUES (:director_id, :name, :phone, :qualification, :location_type, :area_name, :total_students, :status, CURDATE())
");

foreach ($sampleTeachers as $teach) {
    $stmtTeach->execute($teach);
}
echo "✓ শিক্ষক ও মুয়াল্লিমগণের ডাটা সিড সম্পন্ন।\n";

echo "=== জেলা পরিচালক ও শিক্ষক মডিউল মাইগ্রেশন সফলভাবে সমাপ্ত! ===\n";
