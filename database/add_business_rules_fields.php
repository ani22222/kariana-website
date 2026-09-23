<?php
/**
 * Migration: Add Business Rules Fields for Teacher Approval, Validity & Director MOQ
 */

require_once __DIR__ . '/../core/Autoloader.php';
\Core\Autoloader::register();

$db = \Core\Database::getInstance();

echo "Running Migration: add_business_rules_fields...\n";

// 1. Teachers Table Additions
$teacherCols = $db->query("SHOW COLUMNS FROM `teachers`")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('approval_status', $teacherCols)) {
    $db->exec("ALTER TABLE `teachers` ADD COLUMN `approval_status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'approved' AFTER `status`");
    echo "✓ Added `approval_status` to `teachers`\n";
}

if (!in_array('valid_until', $teacherCols)) {
    $db->exec("ALTER TABLE `teachers` ADD COLUMN `valid_until` DATE NULL AFTER `approval_status`");
    // Seed default 1 year validity for existing teachers
    $db->exec("UPDATE `teachers` SET `valid_until` = DATE_ADD(CURDATE(), INTERVAL 1 YEAR) WHERE `valid_until` IS NULL");
    echo "✓ Added `valid_until` (1 year validity) to `teachers`\n";
}

if (!in_array('approved_by', $teacherCols)) {
    $db->exec("ALTER TABLE `teachers` ADD COLUMN `approved_by` VARCHAR(100) NULL AFTER `valid_until`");
    echo "✓ Added `approved_by` to `teachers`\n";
}

if (!in_array('approved_at', $teacherCols)) {
    $db->exec("ALTER TABLE `teachers` ADD COLUMN `approved_at` DATETIME NULL AFTER `approved_by`");
    echo "✓ Added `approved_at` to `teachers`\n";
}

// 2. Directors Table Additions
$directorCols = $db->query("SHOW COLUMNS FROM `directors`")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('min_order_quantity', $directorCols)) {
    $db->exec("ALTER TABLE `directors` ADD COLUMN `min_order_quantity` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `total_books_ordered`");
    echo "✓ Added `min_order_quantity` (Custom Director MOQ) to `directors`\n";
}

if (!in_array('wallet_balance', $directorCols)) {
    $db->exec("ALTER TABLE `directors` ADD COLUMN `wallet_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER `min_order_quantity`");
    echo "✓ Added `wallet_balance` to `directors`\n";
}

// 3. Ensure Teacher Training Course exists in courses table
$teacherCourseCount = (int)$db->query("SELECT COUNT(*) FROM `courses` WHERE `slug` = 'muallim-training' OR `title` LIKE '%মুয়াল্লিম%' OR `title` LIKE '%শিক্ষক%'")->fetchColumn();

if ($teacherCourseCount === 0) {
    $stmt = $db->prepare("INSERT INTO `courses` 
        (`title`, `slug`, `course_code`, `category`, `duration`, `total_classes`, `fee`, `class_schedule`, `instructor_name`, `description`, `admission_open`, `is_featured`, `sort_order`, `created_at`) 
        VALUES 
        (:title, :slug, :course_code, :category, :duration, :total_classes, :fee, :class_schedule, :instructor_name, :description, :admission_open, :is_featured, :sort_order, NOW())");
    $stmt->execute([
        'title'           => 'মুয়াল্লিম ও শিক্ষক প্রশিক্ষণ কোর্স',
        'slug'            => 'muallim-training',
        'course_code'     => 'KRN-TRN-01',
        'category'        => 'training',
        'duration'        => '১ মাস',
        'total_classes'   => 24,
        'fee'             => 'যোগাযোগ সাপেক্ষ',
        'class_schedule'  => 'সরাসরি ও অনলাইন',
        'instructor_name' => 'মাওলানা সাদ্দাম হোসেন ও সিনিয়র প্রশিক্ষকবৃন্দ',
        'description'     => 'কারিয়ানা কুরআন শিক্ষা বোর্ডের আওতায় মাদরাসা ও মক্তবের শিক্ষকদের জন্য বিশেষ মুয়াল্লিম প্রশিক্ষণ কোর্স। প্রশিক্ষণ শেষে জেলা ভিত্তিক দায়িত্ব ও সনদ প্রদান।',
        'admission_open'  => 1,
        'is_featured'     => 1,
        'sort_order'      => 1,
    ]);
    echo "✓ Added `মুয়াল্লিম ও শিক্ষক প্রশিক্ষণ কোর্স` to `courses`\n";
} else {
    echo "✓ Teacher training course verified in `courses`\n";
}

echo "Migration finished successfully!\n";
