-- =============================================================================
-- Kariana Quran Islamic Educational Portal & CMS
-- Full Relational Database Schema
-- Normalized 12 Tables with full UTF8MB4 Unicode Support (utf8mb4_unicode_ci)
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users / Admin Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `username` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor') NOT NULL DEFAULT 'admin',
    `remember_token` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Blog Categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(191) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts (Blog & Articles)
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NULL,
    `author_id` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `cover_image` VARCHAR(255) NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` VARCHAR(300) NULL,
    `canonical_url` VARCHAR(255) NULL,
    `tags` VARCHAR(255) NULL,
    `status` ENUM('published', 'draft', 'archived') NOT NULL DEFAULT 'published',
    `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_posts_slug` (`slug`),
    INDEX `idx_posts_status_published` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Courses Catalog
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `course_code` VARCHAR(50) NOT NULL UNIQUE,
    `category` VARCHAR(100) NOT NULL DEFAULT 'tajweed',
    `duration` VARCHAR(100) NOT NULL DEFAULT '৩ মাস',
    `total_classes` INT UNSIGNED NOT NULL DEFAULT 36,
    `fee` VARCHAR(100) NOT NULL DEFAULT '৳ ১,৫০০',
    `class_schedule` VARCHAR(150) NULL,
    `instructor_name` VARCHAR(150) NULL,
    `description` TEXT NOT NULL,
    `syllabus` LONGTEXT NULL,
    `cover_image` VARCHAR(255) NULL,
    `admission_open` TINYINT(1) NOT NULL DEFAULT 1,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_courses_slug` (`slug`),
    INDEX `idx_courses_code` (`course_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Student Admissions & Lead Database
CREATE TABLE IF NOT EXISTS `admissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `student_name` VARCHAR(150) NOT NULL,
    `guardian_name` VARCHAR(150) NULL,
    `phone` VARCHAR(30) NOT NULL,
    `whatsapp` VARCHAR(30) NULL,
    `email` VARCHAR(150) NULL,
    `district` VARCHAR(100) NOT NULL,
    `district_id` INT UNSIGNED NULL,
    `gender` ENUM('male', 'female') NOT NULL DEFAULT 'male',
    `age` INT NULL,
    `previous_education` VARCHAR(255) NULL,
    `preferred_time` VARCHAR(100) NULL,
    `address` TEXT NULL,
    `admin_notes` TEXT NULL,
    `status` ENUM('pending', 'contacted', 'enrolled', 'cancelled') NOT NULL DEFAULT 'pending',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    INDEX `idx_admissions_course` (`course_id`),
    INDEX `idx_admissions_status` (`status`),
    INDEX `idx_admissions_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Kariana Books & Publications
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `author` VARCHAR(150) NOT NULL DEFAULT 'কারিআনা নূরানী কুরআন একাডেমি',
    `isbn` VARCHAR(50) NULL,
    `pages_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_price` DECIMAL(10,2) NULL,
    `cover_image` VARCHAR(255) NULL,
    `pdf_preview_url` VARCHAR(255) NULL,
    `buy_link` VARCHAR(255) NULL,
    `stock_status` ENUM('in_stock', 'out_of_stock', 'pre_order') NOT NULL DEFAULT 'in_stock',
    `description` TEXT NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_books_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Dynamic Pages & SEO Metadata
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `content` LONGTEXT NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` VARCHAR(300) NULL,
    `meta_keywords` VARCHAR(255) NULL,
    `canonical_url` VARCHAR(255) NULL,
    `robots_directive` VARCHAR(50) NOT NULL DEFAULT 'index, follow',
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_pages_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. QR Book Lessons Mapping (Phase 2)
CREATE TABLE IF NOT EXISTS `qr_lessons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT UNSIGNED NOT NULL,
    `page_number` INT NOT NULL,
    `lesson_code` VARCHAR(50) NOT NULL UNIQUE,
    `lesson_title` VARCHAR(255) NOT NULL,
    `video_provider` ENUM('youtube', 'vimeo', 'direct') NOT NULL DEFAULT 'youtube',
    `video_url` VARCHAR(255) NOT NULL,
    `tajweed_symbols` VARCHAR(100) NULL,
    `notes` TEXT NULL,
    `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
    INDEX `idx_qr_lesson_code` (`lesson_code`),
    INDEX `idx_qr_book_page` (`book_id`, `page_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. 64 Districts & Prayer Offsets
CREATE TABLE IF NOT EXISTS `prayer_districts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_bn` VARCHAR(100) NOT NULL,
    `name_en` VARCHAR(100) NOT NULL,
    `division_bn` VARCHAR(100) NOT NULL,
    `latitude` DECIMAL(8,5) NOT NULL,
    `longitude` DECIMAL(8,5) NOT NULL,
    `fajr_offset` INT NOT NULL DEFAULT 0,
    `sunrise_offset` INT NOT NULL DEFAULT 0,
    `dhuhr_offset` INT NOT NULL DEFAULT 0,
    `asr_offset` INT NOT NULL DEFAULT 0,
    `maghrib_offset` INT NOT NULL DEFAULT 0,
    `isha_offset` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_prayer_district_name_bn` (`name_bn`),
    INDEX `idx_prayer_district_name_en` (`name_en`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Zakat Calculation Settings & Historical Rates
CREATE TABLE IF NOT EXISTS `zakat_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `silver_nisab_tola` DECIMAL(8,2) NOT NULL DEFAULT 52.50,
    `gold_nisab_tola` DECIMAL(8,2) NOT NULL DEFAULT 7.50,
    `silver_rate_per_bhori` DECIMAL(12,2) NOT NULL DEFAULT 2000.00,
    `gold_rate_per_bhori` DECIMAL(12,2) NOT NULL DEFAULT 125000.00,
    `zakat_rate_percent` DECIMAL(5,2) NOT NULL DEFAULT 2.50,
    `notes_bn` TEXT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Site Settings & Metadata
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` LONGTEXT NULL,
    `group_name` VARCHAR(50) NOT NULL DEFAULT 'general',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`setting_key`),
    INDEX `idx_settings_group` (`group_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Migrations Execution Tracker
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(191) NOT NULL UNIQUE,
    `batch` INT NOT NULL DEFAULT 1,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backward-Compatibility View for `districts` -> `prayer_districts`
CREATE OR REPLACE VIEW `districts` AS SELECT * FROM `prayer_districts`;

SET FOREIGN_KEY_CHECKS = 1;
