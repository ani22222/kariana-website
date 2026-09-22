# Functional Specification: Kariana Quran (কারিয়ানা কুরআন) Islamic Educational Portal & CMS

**Document Reference:** `SPEC-KQ-2026-01`  
**Author:** Spec Miner 1 (`teamwork_preview_spec_miner`)  
**Workspace:** `c:\xampp\htdocs\Kariana Website`  
**Dedicated Port:** `8015` (Host: `0.0.0.0`, Local: `http://localhost:8015`, Mobile Wi-Fi: `http://192.168.0.100:8015`)  
**Target Environment:** PHP 8.2+ / MariaDB/MySQL / Apache Shared Hosting (cPanel `public_html`, Hostinger, XAMPP)  
**Status:** Approved Specification Baseline  

---

## 1. Executive Summary & Architectural Scope

The Kariana Quran Portal & CMS is an ultra-fast, mobile-first, Islamic educational web application engineered specifically for the Bangladeshi Muslim community and diaspora. It serves as both the public organizational face for Kariana Quran's educational methodology and publications, and a comprehensive daily Islamic utility portal.

### Core Architectural Pillars:
1. **Zero-Daemon Shared Hosting Delivery:** Engineered to run 100% out of the box on standard cPanel/Hostinger/XAMPP shared hosting without requiring Node.js background services, Redis, PM2, or a VPS.
2. **SEO Dominance for Google Bangladesh:** Automated Schema.org JSON-LD microdata (`Organization`, `Article`, `Course`, `FAQPage`, `BreadcrumbList`), dynamic XML sitemaps, OpenGraph/Twitter social cards, and native UTF-8 Bengali slug routing (e.g. `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`).
3. **Lightweight Modern Admin CMS:** Zero-WordPress overhead. Clean PHP 8.2 MVC + PDO with live desktop/mobile Google SERP preview, Bengali auto-slug generator, Courses & Admissions lead database with UTF-8 BOM CSV export, and Books catalog.
4. **Authoritative Islamic Daily Utilities:** Precise Islamic Foundation Bangladesh (IFB) convention prayer times covering Dhaka default and an offset matrix across all 64 districts of Bangladesh, dynamic Sehri/Iftar schedules, Hanafi Nisab-based Zakat calculator with configurable Gold/Silver rates, and an interactive digital Tasbeeh counter with LocalStorage persistence.
5. **Phase 2 & Phase 3 Gateways:** Instant camera-based QR-code scanning and URL resolution (`/scan?code=...` or `/lesson/:id`) matching printed Kariana Quran books to video lessons, plus an elegant launchpad bridge to the dedicated Kariana Quran Web Reader application.
6. **Royal Islamic Visual Identity:** Palette centered on Royal Islamic Emerald Green (`#064e3b` / `#047857`) and Warm Quranic Gold (`#d97706`), rendered with Bengali typography (`Hind Siliguri`, `SolaimanLipi`) and custom Kariana Quranic Arabic font support (`AAR-SQ-003.ttf`).

---

## 2. System Architecture & Routing Specification (R5)

### 2.1 Directory Layout for Shared Hosting Compatibility
The application employs a lightweight Front Controller MVC architecture that runs seamlessly either in a subfolder (e.g., `http://localhost/Kariana%20Website/` or `http://localhost:8015/`) or at the document root (`public_html/`):

```
c:\xampp\htdocs\Kariana Website\
├── .htaccess                      # Apache rewrite rules for clean URLs & security headers
├── index.php                      # Application Front Controller & Request Dispatcher
├── router.php                     # CLI built-in web server router for PHP 8.2 (port 8015)
├── config/
│   ├── config.php                 # Global app constants, site metadata, base URL
│   └── database.php               # PDO database credentials & connection factory
├── app/
│   ├── Controllers/               # Page, Blog, Course, Book, Utility, Admin, & Scan controllers
│   ├── Models/                    # Post, Course, Admission, Book, Page, Setting, District models
│   ├── Services/                  # PrayerTimeService, ZakatService, SlugService, SeoService
│   └── Helpers/                   # Security (CSRF, sanitize), BengaliHelper, DateHelper
├── views/
│   ├── layouts/                   # main.php (public), admin.php (CMS), modal.php
│   ├── partials/                  # header, footer, prayer-card, serp-preview, navbar
│   ├── pages/                     # home, about, methodology, contact, 404
│   ├── blog/                      # index, show (with JSON-LD & Bengali slug)
│   ├── courses/                   # index, show, admission-form
│   ├── books/                     # index, show (with PDF sample modal)
│   ├── utilities/                 # prayer-times, sehri-iftar, zakat, tasbeeh, quran-bridge
│   ├── scan/                      # camera-scanner, lesson-view (Phase 2)
│   └── admin/                     # login, dashboard, posts, courses, admissions, books, pages
├── assets/
│   ├── css/                       # Compiled Tailwind CSS + custom Islamic decorative styles
│   ├── js/                        # Alpine.js (local vendor), tasbeeh.js, qr-scanner.js, serp.js
│   ├── fonts/                     # Hind Siliguri (Bengali) & AAR-SQ-003.ttf (Kariana Arabic)
│   └── images/                    # logo, og-cover, book-covers, arabesque-pattern.svg
└── database/
    ├── schema.sql                 # Clean MariaDB/MySQL table definitions
    └── seeds.sql                  # Initial admin user, 64 districts, sample courses & posts
```

### 2.2 Server & Port Configuration
- **Binding Requirement:** The PHP 8.2 built-in server MUST bind to `0.0.0.0:8015`.
- **Command Specification:**
  ```powershell
  php -S 0.0.0.0:8015 router.php
  ```
- **Access Endpoints:**
  - Local workstation: `http://localhost:8015`
  - Multi-device local network / mobile Wi-Fi: `http://192.168.0.100:8015`
- **Apache `.htaccess` Rewrites (for XAMPP & Shared Hosting):**
  ```apache
  <IfModule mod_rewrite.c>
      RewriteEngine On
      RewriteBase /
      RewriteCond %{REQUEST_FILENAME} !-f
      RewriteCond %{REQUEST_FILENAME} !-d
      RewriteRule ^(.*)$ index.php?route=$1 [QSA,L]
  </IfModule>
  ```

---

## 3. R1: SEO-Dominant Portal & Blog System Specification

### 3.1 Schema.org JSON-LD Structured Data Definitions
To achieve Google Bangladesh rich snippet indexing, every public page must inject standardized `<script type="application/ld+json">` tags.

#### 3.1.1 Organization Schema (Site-wide)
```json
{
  "@context": "https://schema.org",
  "@type": "EducationalOrganization",
  "@id": "http://localhost:8015/#organization",
  "name": "কারিয়ানা কুরআন",
  "alternateName": "Kariana Quran Bangladesh",
  "url": "http://localhost:8015/",
  "logo": {
    "@type": "ImageObject",
    "url": "http://localhost:8015/assets/images/logo.png",
    "width": 512,
    "height": 512
  },
  "description": "সহজ ও সহীহ পদ্ধতিতে কুরআন তিলাওয়াত, হিফজ ও তাজবীদ শিক্ষা প্রতিষ্ঠান।",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "ঢাকা",
    "addressRegion": "ঢাকা বিভাগ",
    "addressCountry": "BD"
  },
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+8801700000000",
    "contactType": "Customer Support",
    "areaServed": "BD",
    "availableLanguage": ["bn", "en", "ar"]
  },
  "sameAs": [
    "https://facebook.com/karianaquran",
    "https://youtube.com/@karianaquran"
  ]
}
```

#### 3.1.2 Article / BlogPosting Schema (`/blog/{slug}`)
```json
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "http://localhost:8015/blog/{slug}"
  },
  "headline": "{post_title_bn}",
  "description": "{meta_description_bn}",
  "image": ["{featured_image_url}"],
  "datePublished": "2026-09-22T08:00:00+06:00",
  "dateModified": "2026-09-22T10:30:00+06:00",
  "author": {
    "@type": "Person",
    "name": "{author_name}"
  },
  "publisher": {
    "@type": "Organization",
    "name": "কারিয়ানা কুরআন",
    "logo": {
      "@type": "ImageObject",
      "url": "http://localhost:8015/assets/images/logo.png"
    }
  },
  "inLanguage": "bn-BD"
}
```

#### 3.1.3 Course Schema (`/courses/{slug}`)
```json
{
  "@context": "https://schema.org",
  "@type": "Course",
  "name": "{course_title_bn}",
  "description": "{course_summary_bn}",
  "provider": {
    "@type": "Organization",
    "name": "কারিয়ানা কুরআন",
    "sameAs": "http://localhost:8015/"
  },
  "courseCode": "{course_code}",
  "educationalCredentialAwarded": "সনদপত্র (ক্বারীয়ানা তাজবীদ সার্টিফিকেট)",
  "hasCourseInstance": {
    "@type": "CourseInstance",
    "courseMode": ["Online", "Blended"],
    "courseWorkload": "PT3M"
  },
  "offers": {
    "@type": "Offer",
    "price": "{fee_bdt}",
    "priceCurrency": "BDT",
    "category": "Islamic Education",
    "availability": "https://schema.org/InStock",
    "url": "http://localhost:8015/courses/{slug}#admission"
  }
}
```

#### 3.1.4 FAQPage Schema (Home, Course & Methodology Pages)
```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "ক্বারীয়ানা কুরআন পদ্ধতি কী?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "ক্বারীয়ানা পদ্ধতি হলো বিশেষ ১২টি তাজবীদ সংকেত ও মারকাজভিত্তিক আধুনিক বিজ্ঞানসম্মত কুরআন পাঠ পদ্ধতি, যা অতি সহজে বিশুদ্ধ তিলাওয়াত শিখতে সাহায্য করে।"
      }
    },
    {
      "@type": "Question",
      "name": "অনলাইনে কি ভর্তির সুযোগ রয়েছে?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "হ্যাঁ, বিশ্বের যেকোনো প্রান্ত থেকে আমাদের অনলাইন ব্যাচে ভর্তি হওয়া যায়।"
      }
    }
  ]
}
```

#### 3.1.5 BreadcrumbList Schema (All Inner Pages)
```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "হোম",
      "item": "http://localhost:8015/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "কোর্সসমূহ",
      "item": "http://localhost:8015/courses"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "সহজ তাজবীদ শিক্ষা",
      "item": "http://localhost:8015/courses/tajweed-shikkha"
    }
  ]
}
```

### 3.2 Dynamic XML Sitemap Specification (`/sitemap.xml`)
- **Route:** `GET /sitemap.xml`
- **Output Content-Type:** `application/xml; charset=utf-8`
- **Caching & Freshness:** Generated dynamically or cached with 1-hour TTL in shared hosting temp directory.
- **Payload Structure:**
  ```xml
  <?xml version="1.0" encoding="UTF-8"?>
  <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
      <url>
          <loc>http://localhost:8015/</loc>
          <lastmod>2026-09-22</lastmod>
          <changefreq>daily</changefreq>
          <priority>1.0</priority>
      </url>
      <url>
          <loc>http://localhost:8015/courses</loc>
          <lastmod>2026-09-22</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.9</priority>
      </url>
      <url>
          <loc>http://localhost:8015/prayer-times</loc>
          <lastmod>2026-09-22</lastmod>
          <changefreq>daily</changefreq>
          <priority>0.8</priority>
      </url>
      <!-- Dynamic Blog URLs with Bengali Slug Encoding -->
      <url>
          <loc>http://localhost:8015/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা</loc>
          <lastmod>2026-09-22</lastmod>
          <changefreq>weekly</changefreq>
          <priority>0.7</priority>
      </url>
  </urlset>
  ```

### 3.3 OpenGraph & Twitter Card Metadata Standard
Every view renders the following meta tags within `<head>`:
- `og:site_name`: "কারিয়ানা কুরআন (Kariana Quran)"
- `og:locale`: `bn_BD`
- `og:type`: `website` (for landing pages) or `article` (for blog posts)
- `og:title`: `<Title> | কারিয়ানা কুরআন`
- `og:description`: Up to 160 characters clean plaintext
- `og:image`: 1200×630px high-resolution preview image
- `og:url`: Absolute canonical URL (encoded cleanly)
- `twitter:card`: `summary_large_image`
- `twitter:title`, `twitter:description`, `twitter:image`

### 3.4 Bengali Slug Generation Engine & Normalization Rules
To support natural Bengali URLs without URL corruption:
1. **Normalization Algorithm (`SlugService::generate($text)`):**
   - Step 1: Trim leading/trailing whitespace.
   - Step 2: Convert Latin characters to lowercase.
   - Step 3: Replace spaces, underscores, and dots with single hyphens (`-`).
   - Step 4: Remove all punctuation and special characters (`[^\p{Bengali}\p{L}\p{N}\s-]` / `[!"#$%&'()*+,./:;<=>?@[\]^_`{|}~]`), but **strictly preserve** Bengali Unicode blocks `\x{0980}-\x{09FF}` (including Bengali vowels, consonants, hasant `্`, and nukta `়`).
   - Step 5: Collapse multiple consecutive hyphens (`--+`) into a single hyphen (`-`).
   - Step 6: Trim leading and trailing hyphens.
   - Step 7: Enforce uniqueness by checking the target database table (`posts`, `courses`, `books`) and appending `-1`, `-2` in case of collisions.
2. **Web Server Decoding (`urldecode`):**
   - The Front Controller inspects `$_GET['route']` or `$_SERVER['REQUEST_URI']` and applies `urldecode()` to resolve `urldecode('%E0%A6%B8%E0%A6%B9%E0%A6%9C')` into `সহজ`.

---

## 4. R2: Comprehensive Educational & Publications Admin CMS Specification

### 4.1 Database Architecture & Schema Design (MariaDB/MySQL)

```sql
-- 1. Users / Administrators
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'editor') DEFAULT 'admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Blog Categories
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name_bn` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(150) NOT NULL UNIQUE,
    `description_bn` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Blog Posts & News
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `featured_image` VARCHAR(255) NULL,
    `meta_title` VARCHAR(200) NULL,
    `meta_description` VARCHAR(300) NULL,
    `tags` VARCHAR(255) NULL,
    `status` ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    `views_count` INT UNSIGNED DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Courses Catalog
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title_bn` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `course_code` VARCHAR(50) NOT NULL UNIQUE,
    `category` ENUM('nazera', 'hifz', 'qirat', 'tajweed', 'qaida') NOT NULL,
    `duration_months` INT UNSIGNED NOT NULL DEFAULT 3,
    `total_classes` INT UNSIGNED NOT NULL DEFAULT 36,
    `class_schedule_bn` VARCHAR(150) NULL,
    `fee_bdt` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `syllabus_bn` LONGTEXT NULL,
    `thumbnail` VARCHAR(255) NULL,
    `admission_open` TINYINT(1) DEFAULT 1,
    `is_featured` TINYINT(1) DEFAULT 0,
    `order_num` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Student Admissions / Leads Hub
CREATE TABLE IF NOT EXISTS `admissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `student_name_bn` VARCHAR(150) NOT NULL,
    `father_name_bn` VARCHAR(150) NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(150) NULL,
    `district_id` INT UNSIGNED NULL,
    `gender` ENUM('male', 'female') NOT NULL,
    `preferred_time` VARCHAR(100) NULL,
    `previous_education_bn` TEXT NULL,
    `admin_notes` TEXT NULL,
    `status` ENUM('new', 'contacted', 'interviewed', 'admitted', 'cancelled', 'archived') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Kariana Books & Publications
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title_bn` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `author_bn` VARCHAR(150) NOT NULL,
    `isbn_code` VARCHAR(50) NULL,
    `pages_count` INT UNSIGNED DEFAULT 0,
    `price_bdt` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_price_bdt` DECIMAL(10,2) NULL,
    `cover_image` VARCHAR(255) NULL,
    `preview_pdf_url` VARCHAR(255) NULL,
    `order_link` VARCHAR(255) NULL,
    `stock_status` ENUM('in_stock', 'out_of_stock', 'pre_order') DEFAULT 'in_stock',
    `description_bn` TEXT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `order_num` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Dynamic Pages & Per-Page SEO Settings
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `title_bn` VARCHAR(200) NOT NULL,
    `content_bn` LONGTEXT NULL,
    `meta_title` VARCHAR(200) NULL,
    `meta_description` VARCHAR(300) NULL,
    `meta_keywords` VARCHAR(255) NULL,
    `canonical_url` VARCHAR(255) NULL,
    `robots_directive` VARCHAR(50) DEFAULT 'index, follow',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. 64 Districts of Bangladesh
CREATE TABLE IF NOT EXISTS `districts` (
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
    `isha_offset` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Phase 2: QR Book Lessons Mapping
CREATE TABLE IF NOT EXISTS `qr_lessons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT UNSIGNED NOT NULL,
    `page_number` INT UNSIGNED NOT NULL,
    `lesson_code` VARCHAR(50) NOT NULL UNIQUE,
    `lesson_title_bn` VARCHAR(200) NOT NULL,
    `video_provider` ENUM('youtube', 'vimeo', 'direct') DEFAULT 'youtube',
    `video_id_or_url` VARCHAR(255) NOT NULL,
    `tajweed_symbols` VARCHAR(100) NULL,
    `notes_bn` TEXT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.2 Live Google SERP Preview Component Specification
The Admin CMS post/page editor includes a reactive preview component driven by Alpine.js:
- **Desktop Mode Preview:**
  - Displays search result card replicating Google Desktop SERP (width: ~600px).
  - Blue title text (`#1a0dab`, font-size: 20px, hover: underline).
  - Breadcrumb green/gray URL snippet (`https://karianaquran.com › blog › {slug}`).
  - Grey description snippet (`#4d5156`, max length: 155-160 chars).
- **Mobile Mode Preview:**
  - Displays search result card replicating Google Mobile SERP (rounded card with favicon, site name header, title, and wrapped description).
- **Reactive Character & Pixel Counters:**
  - Title Counter: Live tally showing `X / 60 characters` with progress bar (Green: 40-60, Yellow: 61-70, Red: >70).
  - Meta Description Counter: Live tally showing `Y / 160 characters` (Green: 120-160, Yellow: 161-170, Red: >170).
  - Auto-Slug Generator Button: Clicking "স্লাগ তৈরি করুন" converts Bengali title to normalized Bengali slug via client-side regex or AJAX endpoint.

### 4.3 Admissions Hub & CSV Export with UTF-8 BOM Specification
- **Admissions Lead Dashboard:**
  - Filterable table with tabs: `All`, `New (নতুন)`, `Contacted (যোগাযোগকৃত)`, `Admitted (ভর্তিকৃত)`, `Archived (সংরক্ষিত)`.
  - In-line status changer AJAX or POST.
  - Search by student name, mobile number, or district.
- **CSV Export Engine (`AdmissionsController::exportCsv()`):**
  - Sends headers:
    ```php
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="kariana_admissions_' . date('Y-m-d') . '.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');
    ```
  - **Critical UTF-8 BOM Injection:**
    ```php
    // Output UTF-8 Byte Order Mark (BOM) to force Microsoft Excel to render Bengali correctly
    echo "\xEF\xBB\xBF";
    $output = fopen('php://output', 'w');
    fputcsv($output, ['আইডি', 'শিক্ষার্থীর নাম', 'পিতার নাম', 'মোবাইল নম্বর', 'ইমেইল', 'জেলা', 'কোর্স', 'ব্যাচ সময়', 'অবস্থা', 'তারিখ']);
    // write rows...
    fclose($output);
    exit;
    ```

### 4.4 CMS Security Architecture
1. **PDO Prepared Statements:** Mandatory parameter binding for all database queries. Zero string concatenation into SQL clauses.
2. **CSRF Protection:**
   - Cryptographic token generated via `bin2hex(random_bytes(32))` stored in `$_SESSION['csrf_token']`.
   - Helper function `csrf_field()` emits `<input type="hidden" name="csrf_token" value="...">`.
   - Middleware `SecurityHelper::verifyCsrf()` validates all POST/PUT/DELETE requests with `hash_equals()`.
3. **Password Security:**
   - Hashed using `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])`.
   - Verified via `password_verify()`.
4. **Session Hardening:**
   - `session.cookie_httponly = 1`
   - `session.cookie_samesite = 'Lax'`
   - `session.use_strict_mode = 1`
   - `session_regenerate_id(true)` executed on successful login to neutralize session fixation attacks.

---

## 5. R3: Islamic Daily Utilities & Dedicated Quran App Bridge Specification

### 5.1 Prayer Times Calculation Engine & 64 District Offsets
1. **Baseline Astronomical Coordinates (Dhaka, Bangladesh):**
   - Latitude: `23.8103° N`, Longitude: `90.4125° E`, Elevation: 8m, Timezone: `+06:00` (`Asia/Dhaka`).
2. **Islamic Foundation Bangladesh (IFB) Parameters:**
   - **Fajr Angle:** `18.0°` (Subh Sadiq)
   - **Isha Angle:** `18.0°` (End of twilight)
   - **Asr Method:** **Hanafi Jurisprudence** (Shadow ratio = 2x object length + noon solar shadow).
   - **Dhuhr Buffer:** Zawal (Solar noon) + 1.5 minutes safety buffer.
   - **Maghrib Buffer:** Sunset + 2 minutes safety buffer.
3. **64 District Offset Matrix:**
   Bangladesh spans approximately $88^\circ 01' \text{E}$ to $92^\circ 41' \text{E}$ in longitude (approx. 18.6 minutes time difference).
   The engine implements two synchronized calculation modes:
   - **Mode A (Authoritative IFB Official Minute Offset):** Calculates base Dhaka time and applies official IFB minutes table for each of the 64 districts.
     - *Eastern Districts (e.g. Sylhet, Chittagong, Cox's Bazar):* 5 to 10 minutes earlier than Dhaka (e.g. Sylhet: $-6$ mins, Chittagong: $-5$ mins, Cox's Bazar: $-7$ mins).
     - *Western Districts (e.g. Rajshahi, Chapainawabganj, Meherpur, Jessore):* 5 to 10 minutes later than Dhaka (e.g. Rajshahi: $+7$ mins, Chapainawabganj: $+9$ mins, Jessore: $+5$ mins).
   - **Mode B (High-Precision Astronomical Coordinates):** Uses district-specific Latitude and Longitude with IFB Hanafi parameters.
4. **Utility Interface Contract:**
   - **Inputs:** `district_id` (default: Dhaka, ID: 1), `date` (default: today).
   - **Outputs:**
     - `fajr` (সাহরী শেষ / ফজর)
     - `sunrise` (সূর্যোদয় / ইশরাক)
     - `dhuhr` (যোহর শুরু)
     - `asr` (আসর - হানাফী)
     - `maghrib` (মাগরিব / ইফতার)
     - `isha` (এশা)
     - `current_waqt` (active prayer interval)
     - `next_waqt` (upcoming prayer name and countdown seconds)
   - **Client-Side Persistence:** Active district stored in `localStorage.getItem('kq_district')` and loaded automatically on subsequent visits.

### 5.2 Sehri & Iftar Timetable Specification
- **Sehri Ending (সাহরী শেষ):** Fajr start time minus 3 minutes safety margin (standard IFB Bangladesh practice).
- **Iftar Time (ইফতার শুরু):** Maghrib start time.
- **Ramadan Calendar Mode:**
  - 30-day table displaying: হিজরী তারিখ, ইংরেজি তারিখ, বার, সাহরী শেষ, ফজর শুরু, ইফতারের সময়।
  - Visual badges for 3 Ashras: **রহমত (১-১০ দিন)**, **মাগফিরাত (১১-২০ দিন)**, **নাজাত (২১-৩০ দিন)**.
  - Authentic Duas in Arabic, Bengali phonetic pronunciation, and Bengali meaning:
    - *ইফতারের দুআ:* "আল্লাহুম্মা লাকা সুমতু ওয়া আলা রিযক্বিকা আফতারতু..."
    - *রোজার নিয়ত:* "নাওয়াইতু আন আসুমা গাদাম মিন শাহরি রামাদানাল মুবারাক..."

### 5.3 Nisab-Based Zakat Calculator Specification
1. **Islamic Jurisprudence (Hanafi & IFB Standards):**
   - **Silver Nisab (রৌপ্য নিসাব):** 52.5 Tola (ভরি) $= 612.36\text{ grams}$.
   - **Gold Nisab (স্বর্ণ নিসাব):** 7.5 Tola (ভরি) $= 87.48\text{ grams}$.
   - **Standard Application in Bangladesh:** When an individual holds cash, bank balances, trade inventory, or mixed assets, the **Silver Nisab** is adopted by Bangladeshi scholars to maximize welfare support for the poor.
2. **Dynamic Rates Engine:**
   - Preloaded with current market benchmarks (editable in real-time by the user):
     - Gold 22K per Bhori: ~৳ ১,২৫,০০০ (configurable)
     - Silver per Bhori: ~৳ ২,০০০ (configurable)
3. **Asset & Debt Inputs:**
   - **Assets ($A$):**
     1. নগদ টাকা ও ব্যাংক ব্যালেন্স (Cash in hand, Bank deposit, DPS mature cash)
     2. স্বর্ণের বর্তমান বাজারমূল্য (Total Gold value)
     3. রূপার বর্তমান বাজারমূল্য (Total Silver value)
     4. ব্যবসায়িক পণ্যের বর্তমান পাইকারি মূল্য (Business inventory at wholesale rate)
     5. শেয়ার / বিনিয়োগের যাকাতযোগ্য অংশ (Zakatable shares & dividends)
     6. ফেরত পাওয়ার যোগ্য প্রদত্ত ঋণ (Recoverable debts/loans given)
   - **Liabilities ($L$):**
     1. তাৎক্ষণিক পরিশোধযোগ্য ঋণ (Immediate debts due)
     2. বকেয়া বেতন ও বিলসমূহ (Pending immediate bills/salaries)
4. **Mathematical Formulation:**
   $$\text{Net Wealth } (W) = \sum A - \sum L$$
   $$\text{Nisab Threshold } (N) = 52.5 \times \text{Silver Rate per Bhori}$$
   $$\text{Zakat Payable} = \begin{cases} W \times 0.025 & \text{if } W \ge N \\ 0 & \text{if } W < N \end{cases}$$
5. **Output Display:**
   - Status badge: **যাকাত ফরজ (Zakat Mandatory)** or **যাকাত ফরজ নয় (Below Nisab)**.
   - Total Net Wealth in Bengali numerals (e.g. `৳ ৪,৫০,০০০`).
   - Total Zakat Due in Bengali numerals (e.g. `৳ ১১,২৫০`).
   - Clear breakdown card with download/print summary option.

### 5.4 Interactive Digital Tasbeeh Counter Specification
- **UI Architecture (Alpine.js + LocalStorage):**
  - Tactile circular counter button with animated ring and ripple effect.
  - Large bilingual display: Bengali numerals (`০, ১, ২... ৩৩`) and Latin digits.
- **Preset Dhikr Items:**
  1. *সুবহানাল্লাহ* (SubhanAllah — ৩৩ বার)
  2. *আলহামদুলিল্লাহ* (Alhamdulillah — ৩৩ বার)
  3. *আল্লাহু আকবার* (Allahu Akbar — ৩৪ বার)
  4. *লা ইলাহা ইল্লাল্লাহ* (La ilaha illallah — ১০০ বার)
  5. *আস্তাগফিরুল্লাহ* (Astaghfirullah — ১০০ বার)
  6. *সাল্লাল্লাহু আলাইহি ওয়া সাল্লাম* (Darood Sharif — ১০০ বার)
  7. *কাস্টম তাসবীহ* (User-entered title and custom target count)
- **Interactive Feedback Modes:**
  - **Audio Click:** Soft mechanical click sound (synthesized via Web Audio API `AudioContext` so no external audio file load is required).
  - **Haptic Vibration:** Mobile vibration using `navigator.vibrate(40)`.
  - **Target Completion Feedback:** Distinct double vibration `navigator.vibrate([100, 50, 100])` and celebratory modal when target is reached.
- **Lap & History Tracking:**
  - Session Lap Counter: tracks how many full rounds (e.g. 33/33) have been completed.
  - Total Counts: aggregate count saved in `localStorage.getItem('kq_tasbeeh_total')`.
  - Safe Reset button with confirmation safeguard.

### 5.5 Quran Reader App Launchpad & Bridge Specification
- **Hub Overview:**
  - Serves as the high-impact gateway between the public portal and the standalone Kariana Quran Reader application (`http://localhost:8014` or `/Kariana%20Quran%20ReMakiking%20In%20In%20design/`).
- **Feature Showcase Components:**
  - **Typeface Highlight:** Prominent showcase of the verified custom Arabic font `AAR-SQ-003.ttf`.
  - **12 Tajweed Symbols Legend:**
    1. `S1` (`U+08E4`): মাদে আসলী (১ আলিফ মাদ)
    2. `S6` (`U+08F6`): মাদে আরিদ্ (আয়াত-শেষ মাদ)
    3. `S7` (`U+065E`): ওয়াজিব গুন্নাহ
    4. `S8` (`U+065B`): কলকলাহ
    5. `S9` (`U+065D`): তাফখীম / মোটা হরফ (শাপলা প্রতীক)
    6. `S10` (`U+0659`): সিটির হরফ / সাফীর
    7. `S11` (`U+065C`): ইখফা
    8. `S12` (`U+F00C`/`U+08F3`): গোলের মাঝে আরিদ্ সাকিন
  - **Direct Reader Deep Links:**
    - "১ম পারা তিলাওয়াত শুরু করুন" -> `/reader?para=1`
    - "সূরা ফাতিহা পাঠ করুন" -> `/reader?surah=1`
    - "ক্বারীয়ানা তাজবীদ নির্দেশিকা" -> `/methodology`
- **Configurable Bridge Constant:**
  - Defined in `config/config.php`:
    ```php
    define('QURAN_READER_URL', getenv('QURAN_READER_URL') ?: 'http://localhost:8014');
    ```

---

## 6. R4: Phase 2 QR-Code Book Scanning & Video Lesson Gateway Specification

### 6.1 Routing & URL Resolution Contracts
- **Routes:**
  - `GET /scan`: QR Scanner camera landing page.
  - `GET /scan?code={lesson_code}`: Resolves QR scan directly to lesson modal/view.
  - `GET /scan?page={page_number}`: Looks up lesson associated with physical printed book page.
  - `GET /lesson/{lesson_code}`: Canonical permalink for video lesson.

### 6.2 Browser-Based QR Camera Scanner
- **Web API Integration:**
  - Uses `navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })` with lightweight barcode detector or standalone `html5-qrcode` library.
  - Emerald green viewfinder with pulsating crosshair and corner brackets.
  - Auto-pauses camera stream upon successful scan and performs instantaneous redirect.
- **Manual Input Fallback:**
  - Clear input box for users without camera permissions: "বইয়ের পৃষ্ঠা নম্বর বা কোড লিখুন" (e.g. `12` or `KQ-B1-P12`).
  - "পাঠ দেখুন" CTA button executing lookup.

### 6.3 Distraction-Free Video Lesson Player
- **Player Interface:**
  - 16:9 responsive container supporting YouTube unlisted embeds (`?rel=0&modestbranding=1&controls=1&autoplay=1`), Vimeo, or direct MP4/WebM.
  - Completely strips external recommendations, comments, and third-party advertising distraction.
- **Contextual Lesson Metadata Displayed:**
  - Book Title: e.g. *সহজ ক্বারীয়ানা কায়েদা*
  - Page Number: e.g. *পৃষ্ঠা নং: ১২*
  - Lesson Name: e.g. *কলকলাহ হরফের উচ্চারণ ও অনুশীলন*
  - Active Tajweed Rules: Badges displaying the relevant symbol (e.g. **S8** কলকলাহ).
  - Quick Stepper: `← পূর্ববর্তী পাঠ` and `পরবর্তী পাঠ →`.

### 6.4 Admin QR Lesson Management
- Full CRUD interface in Admin CMS:
  - Select Book (`book_id`)
  - Page Number (`page_number`)
  - Lesson Code (auto-formatted e.g. `KQ-P01-L05`)
  - Video URL / YouTube ID
  - Tajweed symbol tags
  - Accompanying teacher instructions / notes
  - High-Resolution QR Generator: Generates downloadable SVG/PNG QR code pointing to `https://karianaquran.com/scan?code=KQ-P01-L05` for embedding in InDesign print layouts.

---

## 7. Visual Identity, Theming & Typography Specification (R5)

### 7.1 Design Tokens & Color Palette
- **Primary: Royal Islamic Emerald Green:**
  - Deep Emerald: `#064e3b` (Primary header, dark cards, footer, luxury accents)
  - Vibrant Emerald: `#047857` (Primary buttons, active links, badges)
  - Light Mint Wash: `#ecfdf5` (Card backgrounds, success highlights, table stripes)
  - Dark Night Emerald: `#022c22` (High-contrast footer, prayer time active cards)
- **Accent: Warm Quranic Gold:**
  - Rich Gold: `#d97706` (Border accents, call-to-action highlights, badges)
  - Deep Amber: `#b45309` (Hover states, active icons)
  - Shimmer Gold: `#fbbf24` (Subtle stars, ornamental dividers, gold foil borders)
- **Neutrals & Surfaces:**
  - Page Background: `#fcfbf7` (Warm parchment/cream for eye comfort during reading)
  - Pure Surface: `#ffffff` (Elevated cards, modal windows)
  - Slate Dark Text: `#0f172a` and `#334155` (Optimum readability)
  - Muted Borders: `#e2e8f0`

### 7.2 Typography Guidelines
- **Bengali Primary Font:** `Hind Siliguri` (Google Font) with system fallback to `SolaimanLipi`, `Kalpurush`, `sans-serif`.
  - Body Line-Height: `1.7` to `1.8` (critical for preventing overlapping Bengali vowel diacritics / কার-ফলা).
  - Headings: `font-bold tracking-normal`.
- **Quranic Arabic Font:** `AAR-SQ-003` (Kariana Custom Typeface) with system fallback to `Amiri`, `Scheherazade New`, `traditional arabic`.
  - Used specifically for Ayahs, Surah headers, and Tajweed symbol examples.
- **Islamic Ornamental Elements:**
  - Rub el Hizb (`۞`) as section dividers.
  - Subtle SVG arabesque geometric patterns applied to hero banners and card headers.

---

## 8. Features Discovered

| # | Category | Feature | Description | Inputs | Outputs | Error Behavior | Discovered Via |
|---|----------|---------|-------------|--------|---------|----------------|----------------|
| 1 | R1: SEO | Organization JSON-LD | Automated Schema.org microdata for Kariana Quran | None (Site config) | `<script type="application/ld+json">` | Gracefully outputs fallback defaults | ORIGINAL_REQUEST.md § R1 |
| 2 | R1: SEO | Article JSON-LD | Structured data for blog posts & articles | Post model fields | Valid BlogPosting JSON-LD | Fallbacks to default author & image | ORIGINAL_REQUEST.md § R1 |
| 3 | R1: SEO | Course JSON-LD | Educational structured markup for courses | Course model fields | Valid Course JSON-LD | Fallbacks to default BDT currency | ORIGINAL_REQUEST.md § R1 |
| 4 | R1: SEO | FAQPage JSON-LD | Collapsible question & answer structured data | FAQ array | Valid FAQPage JSON-LD | Skips empty questions | ORIGINAL_REQUEST.md § R1 |
| 5 | R1: SEO | BreadcrumbList JSON-LD | Hierarchical navigation structured data | Trail items array | Valid BreadcrumbList JSON-LD | Renders root-only if trail empty | ORIGINAL_REQUEST.md § R1 |
| 6 | R1: SEO | Dynamic XML Sitemap | Auto-generating XML sitemap index & URLs | DB query of posts/courses/pages | `application/xml` valid sitemap | Returns static pages if DB unavailable | ORIGINAL_REQUEST.md § R1 |
| 7 | R1: SEO | OpenGraph & Twitter Cards | Social media preview tags with images | Page/Post SEO metadata | `<meta property="og:...">` tags | Uses default emerald logo banner | ORIGINAL_REQUEST.md § R1 |
| 8 | R1: SEO | Bengali Slug Engine | Converts Bengali titles into URL-safe slugs | Bengali string | URL-safe slug preserving Bengali letters | Collapses symbols, appends index on clash | ORIGINAL_REQUEST.md § R1 |
| 9 | R2: CMS | Blog Post CRUD | Admin management of articles and news | Title, content, image, tags, status | Saved post record | Validation error on missing title | ORIGINAL_REQUEST.md § R2 |
| 10 | R2: CMS | Live Google SERP Preview | Desktop & Mobile search snippet simulator | Title, description, slug input | Reactive SERP preview box | Truncates visually with ellipsis | ORIGINAL_REQUEST.md § R2 |
| 11 | R2: CMS | Courses Management | Course catalog CRUD with fee and syllabus | Title, code, duration, fee, syllabus | Course record in DB | Enforces unique course_code | ORIGINAL_REQUEST.md § R2 |
| 12 | R2: CMS | Admissions Lead Engine | Public admission form & admin inquiry tracking | Student name, father, phone, course | Saved admission lead | BD phone regex validation failure | ORIGINAL_REQUEST.md § R2 |
| 13 | R2: CMS | Admissions CSV Export | Exporting leads with UTF-8 BOM encoding | Filter params (optional) | Downloadable `.csv` file with BOM | Handles empty record sets cleanly | ORIGINAL_REQUEST.md § R2 |
| 14 | R2: CMS | Books Catalog | Showcase of books, syllabuses, and publications | Title, author, pages, price, PDF URL | Book catalog record | Sets placeholder cover if missing | ORIGINAL_REQUEST.md § R2 |
| 15 | R2: CMS | Dynamic Pages & Meta | Per-page meta title, description, and canonical | Slug, title, content, SEO fields | Custom page record | Generates standard fallback meta | ORIGINAL_REQUEST.md § R2 |
| 16 | R2: CMS | CSRF Token Guard | Cryptographic protection against form forgery | `csrf_token` POST parameter | Verification pass / fail | 403 Forbidden on token mismatch | ORIGINAL_REQUEST.md § R2 |
| 17 | R2: CMS | PDO Prepared Statements | Parameterized query execution across models | SQL string + params array | Query results | Throws PDOException, logs securely | ORIGINAL_REQUEST.md § R2 |
| 18 | R3: Utilities | Prayer Times (Dhaka Default) | IFB calculation for Dhaka base coordinates | Date | Fajr, Sunrise, Dhuhr, Asr, Maghrib, Isha | Validates date format YYYY-MM-DD | ORIGINAL_REQUEST.md § R3 |
| 19 | R3: Utilities | 64 Districts Prayer Offsets | Localized prayer times for all 64 BD districts | District ID | Shifted prayer times | Defaults to Dhaka if invalid ID | ORIGINAL_REQUEST.md § R3 |
| 20 | R3: Utilities | Sehri & Iftar Timetable | Sehri end (Fajr-3m) & Iftar with Ramadan ashra | District ID, Date | Sehri & Iftar times + Duas | Shows current day if date omitted | ORIGINAL_REQUEST.md § R3 |
| 21 | R3: Utilities | Nisab Zakat Calculator | Hanafi Silver-Nisab Zakat assessment | Cash, Gold, Silver, Stocks, Debts | Net wealth, Nisab, Zakat payable | Rejects negative asset amounts | ORIGINAL_REQUEST.md § R3 |
| 22 | R3: Utilities | Digital Tasbeeh Counter | Interactive digital clicker with audio/haptic | Tap / Click / Preset selector | Counter display, lap, total | Resets cleanly on confirm | ORIGINAL_REQUEST.md § R3 |
| 23 | R3: Utilities | Quran Reader App Bridge | Launchpad & deep links to dedicated reader app | Navigation clicks | External / internal app redirect | Fallback if local port 8014 offline | ORIGINAL_REQUEST.md § R3 |
| 24 | R4: Phase 2 | QR Camera Scanner | Browser webcam scanner for book QR codes | Video stream frame | Scanned URL / Code redirect | Requests permission; manual fallback | ORIGINAL_REQUEST.md § R4 |
| 25 | R4: Phase 2 | Direct URL Lesson Gateway | Lookup lesson by code or printed page number | `code` or `page` query parameter | Lesson video modal/page | Shows 404 / lesson not found prompt | ORIGINAL_REQUEST.md § R4 |
| 26 | R4: Phase 2 | Distraction-Free Video Player | Embedded player with Tajweed symbol indicators | Video provider & ID | Clean 16:9 player + Tajweed rules | Handles broken video links gracefully | ORIGINAL_REQUEST.md § R4 |
| 27 | R4: Phase 2 | Admin QR Lesson Mapping | Admin tool to map page numbers to video lessons | Book ID, page, code, video URL | QR lesson record in DB | Enforces unique lesson code | ORIGINAL_REQUEST.md § R4 |
| 28 | R5: Deploy | Dedicated Port 8015 Binding | Multi-device local server binding to 0.0.0.0 | CLI arguments | Served HTTP on port 8015 | Port collision error if port in use | User Rule & Port Registry |
| 29 | R5: Shared | Zero-Daemon Shared Hosting | Pure PHP MVC executable on cPanel/Hostinger | HTTP requests | Rendered HTML / JSON | Self-contained routing via .htaccess | ORIGINAL_REQUEST.md § R5 |
| 30 | Domain | AAR-SQ-003 Typeface Support | Custom font rendering with 12 Tajweed symbols | Arabic text string | Rendered Quranic Arabic | Fallback to Amiri / Uthmani font | Adjacent Kariana Project Survey |

---

## 9. Comprehensive Edge Cases & Boundary Conditions Matrix

| # | Feature | Input / Condition | Expected / Observed Behavior | Specification Mandate |
|---|---------|-------------------|------------------------------|-----------------------|
| 1 | Bengali Slug Engine | String with special Bengali conjuncts: `কুরআন-তিলাওয়াত ও তাজবীদ শিক্ষা!?` | Converts to `কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা` | Strict regex preserves `\p{Bengali}` characters, removes `!?`, collapses hyphens. |
| 2 | Bengali Slug Engine | Multiple posts saved with identical title `সহজ ক্বারীয়ানা কায়েদা` | Slugs generated: `সহজ-ক্বারীয়ানা-কায়েদা`, `সহজ-ক্বারীয়ানা-কায়েদা-1`, `...-2` | Must query database for slug existence and append integer suffix. |
| 3 | Admissions CSV Export | Export opened in Microsoft Excel on Windows | Bengali characters render correctly without mojibake | Must prepend UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) before writing headers. |
| 4 | Admissions Form | Bangladeshi mobile number with spaces, hyphens, or `+88`: `+88 01712-345678` | Normalized to `01712345678` | Regex cleans non-digits; validates `^01[3-9]\d{8}$`. |
| 5 | Live SERP Preview | Title exceeding 70 characters or 600px width | Counter turns red; preview truncates with ellipsis `...` | Live JavaScript counter updates pixel meter and displays truncation indicator. |
| 6 | 64 Districts Prayer Times | User travels or switches district from Dhaka to Sylhet (-6m) | Prayer times shift earlier by 6 minutes; countdown adjusts | Re-computes timestamps; caches selection in `localStorage('kq_district')`. |
| 7 | Prayer Times Midday | Current time exactly at solar noon (Zawal) | Waqt indicator displays "যাওয়াল / নিষিদ্ধ সময় (Solar Noon)" | Highlight prohibited prayer interval between Zawal and Dhuhr start. |
| 8 | Zakat Calculator | User enters negative debt or non-numeric characters | System ignores non-numeric inputs; sets value to `0.00` | Input sanitization converts values via `floatval(abs($val))`. |
| 9 | Zakat Calculator | Net wealth is ৳১ below Silver Nisab threshold | Status shows "যাকাত ফরজ নয়"; shows amount needed to reach Nisab | Clear message stating shortfall to Nisab threshold. |
| 10 | Digital Tasbeeh | User reaches 33 on "সুবহানাল্লাহ" preset | Vibrates `[100, 50, 100]`; increments Lap; shows prompt for "আলহামদুলিল্লাহ" | Non-blocking modal/toast suggesting transition to next Dhikr. |
| 11 | Digital Tasbeeh | Browser refresh or tab closed mid-count (e.g. at 27) | Count remains 27 upon reopening page | Read from and write to `localStorage` on every single increment. |
| 12 | QR Lesson Gateway | User navigates to `/scan?code=INVALID-CODE-999` | Displays user-friendly "পাঠ পাওয়া যায়নি" with search box | 404 handled gracefully inside the theme; suggests book catalog. |
| 13 | QR Lesson Gateway | Physical page has no lesson video yet assigned | Displays "এই পৃষ্ঠার জন্য কোনো ভিডিও টিউটোরিয়াল যুক্ত করা হয়নি" | Prompts user to view PDF sample or contact teacher. |
| 14 | Shared Hosting Subfolder | App hosted at `http://localhost/Kariana%20Website/` vs root `http://localhost:8015/` | All assets, links, and routes resolve accurately without hardcoded root paths | Router detects `BASE_URL` dynamically from `dirname($_SERVER['SCRIPT_NAME'])`. |
| 15 | CSRF Verification | Stale form submission or expired session token | HTTP 403 Forbidden with Bengali alert "নিরাপত্তা টোকেনের মেয়াদ উত্তীর্ণ হয়েছে" | Refreshes token and invites user to submit again without losing input. |

---

## 10. Acceptance Criteria & Test Verification Matrix

### Phase 1: Foundation, CMS, SEO & Organizational Platform (Active Milestone)
- **AC 1.1: Shared Hosting & XAMPP Compatibility:**
  - *Verification:* Deploy directly to XAMPP without running Node.js daemon; verify all routes (`/`, `/blog`, `/courses`, `/books`, `/prayer-times`, `/admin`) execute properly via PHP 8.2 PDO.
- **AC 1.2: SEO Dominance & Schema.org JSON-LD:**
  - *Verification:* Inspect source code or run Google Rich Results Test on home, blog, and course pages; verify `Organization`, `Article`, `Course`, `FAQPage`, and `BreadcrumbList` schemas validate with 0 errors.
- **AC 1.3: Dynamic XML Sitemap & Social Tags:**
  - *Verification:* Request `GET /sitemap.xml`; verify valid XML syntax, accurate `<loc>` tags with Bengali slugs, and correct OpenGraph `og:title`, `og:image`, `twitter:card` tags.
- **AC 1.4: Bengali Slug Generation & SERP Preview:**
  - *Verification:* Create post in Admin CMS with Bengali title; verify clean slug auto-generation; verify desktop and mobile Google SERP preview toggles work reactively.
- **AC 1.5: Courses & Online Admission Lead Engine:**
  - *Verification:* Submit public admission form; verify record appears in Admin CMS; update status to `Admitted`; execute CSV export and open in Excel to verify Bengali text is crystal clear.
- **AC 1.6: Books & Publications Catalog:**
  - *Verification:* Verify book showcase renders grid; test PDF sample modal and buy/order button.
- **AC 1.7: Islamic Daily Utilities:**
  - *Verification:* Change district across 64 districts; verify prayer times shift accurately; verify Sehri/Iftar countdown; compute Zakat with custom rates; click Tasbeeh to 33 and verify audio/haptic.
- **AC 1.8: Dedicated Port 8015 Multi-Device Preview:**
  - *Verification:* Start server via `php -S 0.0.0.0:8015 router.php`; verify accessibility on `http://localhost:8015` and `http://192.168.0.100:8015`.

### Phase 2: QR Book Scanner & Educational Video Gateway
- **AC 2.1: QR Camera Scanner:**
  - *Verification:* Navigate to `/scan`; verify camera stream activates; scan QR code containing `/scan?code=KQ-P01-L05`; verify auto-redirect to lesson.
- **AC 2.2: Manual Page Lookup Fallback:**
  - *Verification:* Enter page number `1` or code `KQ-P01-L05` into manual input box; verify correct lesson loads.
- **AC 2.3: Distraction-Free Video Player:**
  - *Verification:* Verify video plays in clean 16:9 frame without YouTube third-party recommendations; verify Tajweed symbol badges display correctly.
- **AC 2.4: Admin QR Management:**
  - *Verification:* Admin creates lesson mapping; generates high-res QR code; verifies QR points to canonical scan URL.

### Phase 3: Quran App Integration & Advanced Bridge
- **AC 3.1: Quran Reader Hub Launchpad:**
  - *Verification:* Launchpad cards link seamlessly to `http://localhost:8014` or configured reader URL.
- **AC 3.2: Verified Custom Font & Tajweed Symbols Legend:**
  - *Verification:* Legend showcases all 12 Tajweed symbols (`S1`-`S12`) with proper font rendering using `AAR-SQ-003.ttf`.
