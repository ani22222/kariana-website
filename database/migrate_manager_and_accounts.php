<?php
/**
 * Migration for Operations Manager, Accounts Ledger, Teacher Login & Activities
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

echo "=== [কারিয়ানা কুরআন] ম্যানেজার, হিসাব খাতা ও শিক্ষক পোর্টাল মাইগ্রেশন শুরু ===\n";

// 1. Create Managers Table
$pdo->exec("
CREATE TABLE IF NOT EXISTS `managers` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `email` VARCHAR(150) NULL,
    `phone` VARCHAR(30) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `designation` VARCHAR(100) NOT NULL DEFAULT 'অপারেশনস ও হিসাব ব্যবস্থাপক',
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_managers_username` (`username`),
    INDEX `idx_managers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'managers' টেবিল প্রস্তুত।\n";

// Seed Default Manager if not exists
$mgrStmt = $pdo->prepare("SELECT COUNT(*) FROM `managers` WHERE `username` = :u");
$mgrStmt->execute(['u' => 'manager']);
if ((int)$mgrStmt->fetchColumn() === 0) {
    $mgrPass = password_hash('kariana2026!', PASSWORD_BCRYPT);
    $insMgr = $pdo->prepare("
        INSERT INTO `managers` (`name`, `username`, `email`, `phone`, `password`, `designation`, `status`)
        VALUES (:name, :username, :email, :phone, :password, :designation, 'active')
    ");
    $insMgr->execute([
        'name'        => 'মাওলানা মুহাম্মদ আবদুর রহিম (অপারেশনস ম্যানেজার)',
        'username'    => 'manager',
        'email'       => 'manager@karianaquran.com',
        'phone'       => '+8801819000111',
        'password'    => $mgrPass,
        'designation' => 'অপারেশনস ও হিসাব ব্যবস্থাপক',
    ]);
    echo "✓ ডিফল্ট ম্যানেজার তৈরি সম্পন্ন (username: manager, pass: kariana2026!)\n";
}

// 2. Create Accounts Ledger Table (আয়-ব্যয় হিসাব খাতা)
$pdo->exec("
CREATE TABLE IF NOT EXISTS `accounts_ledger` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `manager_id` INT UNSIGNED NULL,
    `type` ENUM('income', 'expense') NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `transaction_date` DATE NOT NULL,
    `description` TEXT NOT NULL,
    `voucher_no` VARCHAR(100) NULL,
    `related_director_id` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ledger_type` (`type`),
    INDEX `idx_ledger_date` (`transaction_date`),
    INDEX `idx_ledger_voucher` (`voucher_no`),
    FOREIGN KEY (`manager_id`) REFERENCES `managers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`related_director_id`) REFERENCES `directors`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'accounts_ledger' টেবিল প্রস্তুত।\n";

// Seed sample ledger records if empty
$ledgerCount = (int)$pdo->query("SELECT COUNT(*) FROM `accounts_ledger`")->fetchColumn();
if ($ledgerCount === 0) {
    $mgrId = (int)$pdo->query("SELECT `id` FROM `managers` LIMIT 1")->fetchColumn();
    $sampleLedger = [
        ['income', 'বই বিক্রি', 25000.00, date('Y-m-d', strtotime('-5 days')), 'মাগুরা জেলা পরিচালকের নিকট কারিয়ানা কুরআন ও আমপারা সরবরাহ', 'VCH-2026-001', 1],
        ['income', 'বই বিক্রি', 18500.00, date('Y-m-d', strtotime('-3 days')), 'রংপুর বিভাগীয় পরিচালকের নিকট কায়েদা ও তাজবীদ বই প্রেরণ', 'VCH-2026-002', 2],
        ['expense', 'কুরিয়ার পরিবহন খরচ', 3200.00, date('Y-m-d', strtotime('-4 days')), 'সুন্দরবন কুরিয়ার সার্ভিসে ৫টি জেলায় বই পার্সেল বুকিং', 'VCH-2026-003', NULL],
        ['expense', 'মুদ্রণ ও প্রিন্টিং খরচ', 15000.00, date('Y-m-d', strtotime('-2 days')), 'সহজ ক্বারীয়ানা কায়েদা নতুন সংস্করণ মুদ্রণ প্রেস বিল', 'VCH-2026-004', NULL],
        ['income', 'অনুদান / ওয়াকফ', 50000.00, date('Y-m-d', strtotime('-1 days')), 'দরিদ্র এলাকার শিক্ষার্থীদের মাঝে বিনামূল্যে কুরআন বিতরণে ওয়াকফ অনুদান', 'VCH-2026-005', NULL],
    ];
    $insLedger = $pdo->prepare("
        INSERT INTO `accounts_ledger` (`manager_id`, `type`, `category`, `amount`, `transaction_date`, `description`, `voucher_no`, `related_director_id`)
        VALUES (:mgr, :type, :cat, :amount, :tx_date, :desc, :vch, :dir)
    ");
    foreach ($sampleLedger as $item) {
        $insLedger->execute([
            'mgr'     => $mgrId,
            'type'    => $item[0],
            'cat'     => $item[1],
            'amount'  => $item[2],
            'tx_date' => $item[3],
            'desc'    => $item[4],
            'vch'     => $item[5],
            'dir'     => $item[6],
        ]);
    }
    echo "✓ নমুনা হিসাব খাতা ভাউচার ডাটা যুক্ত করা হলো।\n";
}

// 3. Create Book Distributions Table (বই/কুরআন বিতরণ ও কুরিয়ার ট্র্যাকিং)
$pdo->exec("
CREATE TABLE IF NOT EXISTS `book_distributions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `manager_id` INT UNSIGNED NULL,
    `recipient_type` ENUM('director', 'teacher', 'general_customer') NOT NULL DEFAULT 'director',
    `director_id` INT UNSIGNED NULL,
    `teacher_id` INT UNSIGNED NULL,
    `customer_name` VARCHAR(150) NULL,
    `customer_phone` VARCHAR(30) NULL,
    `book_id` INT UNSIGNED NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `payment_status` ENUM('paid', 'due', 'partial', 'waived') NOT NULL DEFAULT 'paid',
    `delivery_status` ENUM('pending', 'dispatched', 'in_transit', 'delivered', 'returned') NOT NULL DEFAULT 'dispatched',
    `courier_name` VARCHAR(100) NULL,
    `tracking_number` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_dist_director` (`director_id`),
    INDEX `idx_dist_status` (`delivery_status`),
    INDEX `idx_dist_payment` (`payment_status`),
    FOREIGN KEY (`manager_id`) REFERENCES `managers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'book_distributions' টেবিল প্রস্তুত।\n";

// Seed sample book distributions if empty
$distCount = (int)$pdo->query("SELECT COUNT(*) FROM `book_distributions`")->fetchColumn();
if ($distCount === 0) {
    $mgrId = (int)$pdo->query("SELECT `id` FROM `managers` LIMIT 1")->fetchColumn();
    $firstBook = (int)$pdo->query("SELECT `id` FROM `books` LIMIT 1")->fetchColumn();
    if ($firstBook > 0) {
        $sampleDist = [
            [$mgrId, 'director', 1, NULL, NULL, NULL, $firstBook, 50, 1000.00, 50000.00, 'paid', 'delivered', 'সুন্দরবন কুরিয়ার', 'SBN-98234-MG', 'মাগুরা জেলা কেন্দ্রীয় কার্যালয়ে হস্তান্তরিত'],
            [$mgrId, 'director', 2, NULL, NULL, NULL, $firstBook, 35, 1000.00, 35000.00, 'paid', 'in_transit', 'এসএ পরিবহন', 'SA-7821-RP', 'রংপুর প্রধান শাখা পথে রয়েছে'],
            [$mgrId, 'general_customer', NULL, NULL, 'মুহাম্মদ ইকবাল হোসেন', '+8801712000222', $firstBook, 2, 1000.00, 2000.00, 'paid', 'dispatched', 'রেডএক্স', 'RDX-554201', 'হোম ডেলিভারি মিরপুর, ঢাকা'],
        ];
        $insDist = $pdo->prepare("
            INSERT INTO `book_distributions` 
            (`manager_id`, `recipient_type`, `director_id`, `teacher_id`, `customer_name`, `customer_phone`, `book_id`, `quantity`, `unit_price`, `total_amount`, `payment_status`, `delivery_status`, `courier_name`, `tracking_number`, `notes`)
            VALUES (:mgr, :rec_type, :dir, :tea, :cname, :cphone, :book, :qty, :uprice, :tot, :pay_st, :del_st, :courier, :track, :notes)
        ");
        foreach ($sampleDist as $sd) {
            $insDist->execute([
                'mgr'      => $sd[0],
                'rec_type' => $sd[1],
                'dir'      => $sd[2],
                'tea'      => $sd[3],
                'cname'    => $sd[4],
                'cphone'   => $sd[5],
                'book'     => $sd[6],
                'qty'      => $sd[7],
                'uprice'   => $sd[8],
                'tot'      => $sd[9],
                'pay_st'   => $sd[10],
                'del_st'   => $sd[11],
                'courier'  => $sd[12],
                'track'    => $sd[13],
                'notes'    => $sd[14],
            ]);
        }
        echo "✓ নমুনা বিতরণ ডাটা যুক্ত করা হলো।\n";
    }
}

// 4. Update Teachers Table to add Auth Credentials if missing
$teacherCols = $pdo->query("SHOW COLUMNS FROM `teachers`")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('username', $teacherCols)) {
    $pdo->exec("ALTER TABLE `teachers` ADD COLUMN `username` VARCHAR(100) NULL UNIQUE AFTER `phone`");
    echo "✓ teachers টেবিলে 'username' কলাম যুক্ত হয়েছে।\n";
}
if (!in_array('password', $teacherCols)) {
    $pdo->exec("ALTER TABLE `teachers` ADD COLUMN `password` VARCHAR(255) NULL AFTER `username`");
    echo "✓ teachers টেবিলে 'password' কলাম যুক্ত হয়েছে।\n";
}
if (!in_array('email', $teacherCols)) {
    $pdo->exec("ALTER TABLE `teachers` ADD COLUMN `email` VARCHAR(150) NULL AFTER `password`");
    echo "✓ teachers টেবিলে 'email' কলাম যুক্ত হয়েছে।\n";
}

// Update existing teachers with login credentials if empty
$teachersWithoutLogin = $pdo->query("SELECT `id`, `phone` FROM `teachers` WHERE `username` IS NULL OR `username` = ''")->fetchAll();
if (!empty($teachersWithoutLogin)) {
    $tPass = password_hash('kariana2026!', PASSWORD_BCRYPT);
    $updT = $pdo->prepare("UPDATE `teachers` SET `username` = :u, `password` = :p WHERE `id` = :id");
    foreach ($teachersWithoutLogin as $t) {
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$t['phone']);
        $uName = 'teacher_' . ($cleanPhone ?: (string)$t['id']);
        $updT->execute([
            'u'  => $uName,
            'p'  => $tPass,
            'id' => $t['id']
        ]);
    }
    echo "✓ " . count($teachersWithoutLogin) . " জন শিক্ষকের লগইন ইউজারনেম ও পাসওয়ার্ড প্রস্তুত করা হয়েছে।\n";
}

// Ensure at least one dedicated test teacher exists
$demoTeacher = $pdo->query("SELECT `id` FROM `teachers` WHERE `username` = 'teacher01' LIMIT 1")->fetch();
if (!$demoTeacher) {
    $dirId = (int)$pdo->query("SELECT `id` FROM `directors` LIMIT 1")->fetchColumn() ?: 1;
    $tPass = password_hash('kariana2026!', PASSWORD_BCRYPT);
    $insT = $pdo->prepare("
        INSERT INTO `teachers` (`director_id`, `name`, `phone`, `username`, `password`, `email`, `qualification`, `location_type`, `area_name`, `total_students`, `status`)
        VALUES (:dir, :name, :phone, 'teacher01', :pass, 'teacher01@karianaquran.com', 'হিফজ ও তাজবীদ সনদপ্রাপ্ত মুয়াল্লিম', 'madrasa', 'শ্রীপুর জামিয়া ইসলামিয়া', 45, 'active')
    ");
    $insT->execute([
        'dir'   => $dirId,
        'name'  => 'ক্বারী হাফেজ মাওলানা তারেক হাসান',
        'phone' => '+8801799887766',
        'pass'  => $tPass
    ]);
    echo "✓ পরীক্ষামূলক ডেমো শিক্ষক তৈরি সম্পন্ন (username: teacher01, pass: kariana2026!)\n";
}

// 5. Create Teacher Activities Table (শিক্ষক কার্যক্রম / সবক ক্লাস ও সমস্যা রিপোর্ট)
$pdo->exec("
CREATE TABLE IF NOT EXISTS `teacher_activities` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `teacher_id` INT UNSIGNED NOT NULL,
    `director_id` INT UNSIGNED NOT NULL,
    `activity_type` ENUM('book_order', 'sabak_class', 'problem_report', 'general') NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `details` TEXT NOT NULL,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 0,
    `preferred_date` DATE NULL,
    `status` ENUM('pending', 'approved', 'scheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    `director_notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_ta_teacher` (`teacher_id`),
    INDEX `idx_ta_director` (`director_id`),
    INDEX `idx_ta_status` (`status`),
    FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`director_id`) REFERENCES `directors`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
echo "✓ 'teacher_activities' টেবিল প্রস্তুত।\n";

// Seed sample teacher activity if empty
$actCount = (int)$pdo->query("SELECT COUNT(*) FROM `teacher_activities`")->fetchColumn();
if ($actCount === 0) {
    $sampleTeacher = $pdo->query("SELECT `id`, `director_id` FROM `teachers` LIMIT 1")->fetch();
    if ($sampleTeacher) {
        $insAct = $pdo->prepare("
            INSERT INTO `teacher_activities` (`teacher_id`, `director_id`, `activity_type`, `title`, `details`, `quantity`, `preferred_date`, `status`)
            VALUES (:t_id, :d_id, :type, :title, :details, :qty, :pdate, 'pending')
        ");
        $insAct->execute([
            't_id'    => $sampleTeacher['id'],
            'd_id'    => $sampleTeacher['director_id'],
            'type'    => 'sabak_class',
            'title'   => '২৫ জন শিক্ষার্থীর নাজেরা কুরআন সমাপ্তি ও চূড়ান্ত সবক ক্লাস আবেদন',
            'details' => 'আমাদের শ্রীপুর শাখায় ২৫ জন শিক্ষার্থী পূর্ণ কুরআন মাজিদ সহীহ তিলাওয়াত শেষ করেছে। জেলা পরিচালকের উপস্থিতিতে সবক প্রদান ও দোয়া মাহফিলের তারিখ নির্ধারণ প্রার্থনা করছি।',
            'qty'     => 25,
            'pdate'   => date('Y-m-d', strtotime('+7 days'))
        ]);
        $insAct->execute([
            't_id'    => $sampleTeacher['id'],
            'd_id'    => $sampleTeacher['director_id'],
            'type'    => 'book_order',
            'title'   => 'নতুন ব্যাচের জন্য ৩০ কপি সহজ ক্বারীয়ানা কায়েদা প্রয়োজন',
            'details' => 'নতুন ব্যাচ শুরু হচ্ছে আগামী সপ্তাহে। জেলা পরিচালক মহোদয়ের নিকট থেকে ৩০ কপি কায়েদা দ্রুত সংগ্রহ করতে চাই।',
            'qty'     => 30,
            'pdate'   => NULL
        ]);
        echo "✓ নমুনা শিক্ষক কার্যক্রম ও সবক ক্লাস রিকোয়েস্ট তৈরি সম্পন্ন।\n";
    }
}

echo "=== [কারিয়ানা কুরআন] সকল টেবিল ও ডাটা সফলভাবে সমাপ্ত! ===\n";
