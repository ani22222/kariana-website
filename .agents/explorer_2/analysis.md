# Architectural Framework & Technical Analysis
**Project**: Kariana Quran (কারিয়ানা কুরআন) Islamic Educational Portal & CMS  
**Document**: System Architecture, Pure PHP 8.2 MVC, Database, Routing & Security Blueprint  
**Target Environments**: 100% Shared Hosting (Hostinger `public_html` / cPanel), Local XAMPP (`http://localhost/Kariana%20Website/`), and Dedicated Port 8015 (`0.0.0.0:8015`)  
**Author**: Explorer 2 (Teamwork Explorer & Architect)  
**Date**: 2026-09-22  

---

## Executive Summary

The Kariana Quran platform is designed as an ultra-fast, search-engine-dominant Islamic educational portal and administrative CMS. To achieve **zero-dependency shared hosting portability** (no Node.js daemon, no PM2, no Docker, and no VPS requirements), the platform is architected around a **pure PHP 8.2 modern MVC engine** that runs natively out-of-the-box on Hostinger, cPanel, and XAMPP.

The architecture guarantees:
1. **100% Shared Hosting Portability**: Drag-and-drop deployment via FTP / cPanel File Manager with zero build steps required at runtime.
2. **Dual-Mode Deployment**: Operates identically in Apache subdirectories (`/Kariana Website/`), Apache virtual hosts (`http://localhost:8015/`), or PHP CLI development servers (`php -S 0.0.0.0:8015 router.php`).
3. **Native Bengali Unicode Slug Routing**: Flawless handling of Bengali URL paths (e.g. `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`, `/courses/হিফজুল-কুরআন`) using UTF-8 decoding and PCRE Unicode regexes (`/u`).
4. **Hardened MySQL/MariaDB PDO Layer**: Full `utf8mb4_unicode_ci` charset compliance for Quranic diacritics, Bengali glyphs, and emojis; singleton connection pool; and 100% prepared statements.
5. **Royal Islamic Visual Identity & Zero-Build Frontend**: Royal Islamic Emerald Green (`#064e3b`, `#047857`) and Warm Quranic Gold (`#d97706`) with Tailwind CSS + Alpine.js and typography for Bengali (`Hind Siliguri`) and Quranic Arabic (`Amiri` and Kariana `AASQ` verified font).
6. **Enterprise-Grade Security**: CSRF token validation, session fixation protection, Argon2ID/Bcrypt password hashing, and XSS sanitization.

---

## 1. Directory Structure & Modular Layout

```
Kariana Website/
├── .htaccess                         # Root Apache rewrite & directory security rules
├── index.php                         # Unified Front Controller entry point
├── router.php                        # CLI server router for php -S 0.0.0.0:8015
├── config/
│   ├── app.php                       # Application metadata, timezone, base URL detection
│   └── database.php                  # Database connection credentials (host, dbname, user, pass)
├── core/                             # Zero-dependency Lightweight MVC Framework
│   ├── Autoloader.php                # PSR-4 native autoloader (App\ and Core\ namespaces)
│   ├── App.php                       # Application kernel and lifecycle manager
│   ├── Controller.php                # Base Controller (view rendering, JSON responses, redirects)
│   ├── Model.php                     # Base Model (PDO query builder, CRUD helpers)
│   ├── View.php                      # View rendering engine with layouts, partials & data injection
│   ├── Router.php                    # Multi-method router with regex and Unicode Bengali support
│   ├── Request.php                   # Request parser, HTTP verb resolution & input sanitization
│   ├── Response.php                  # Response builder (status codes, headers, output buffering)
│   ├── Database.php                  # PDO connection singleton with UTF8MB4 configuration
│   ├── Session.php                   # Hardened session management with flash messaging
│   └── Csrf.php                      # Cryptographic CSRF token generation & validation
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php        # Homepage, hero, featured courses, prayer times widget
│   │   ├── BlogController.php        # Blog list, category/tag filtering, article detail with Bengali slug
│   │   ├── CourseController.php      # Course catalog & course detail pages
│   │   ├── AdmissionController.php   # Admission form submission, validation & confirmation
│   │   ├── BookController.php        # Publications catalog, book details, sample preview
│   │   ├── UtilityController.php     # Prayer times (64 districts), Sehri/Iftar, Zakat, Tasbeeh
│   │   ├── ScanController.php        # Phase 2 QR book gateway (/scan?code=... and /lesson/:id)
│   │   ├── PageController.php        # Dynamic pages (About, Methodology, Teachers, Contact)
│   │   ├── SitemapController.php     # Dynamic XML Sitemap and robots.txt generation
│   │   ├── AuthController.php        # Admin login, logout, and session lifecycle
│   │   └── Admin/
│   │       ├── DashboardController.php   # Overview stats, quick actions, recent admissions
│   │       ├── BlogAdminController.php   # CRUD for posts, categories, tags, SERP preview
│   │       ├── CourseAdminController.php # CRUD for courses and curriculum
│   │       ├── AdmissionAdminController.php # Applicant management, status updating, CSV export
│   │       ├── BookAdminController.php   # CRUD for publications and downloadable previews
│   │       ├── ScanAdminController.php   # QR lesson code management and YouTube video links
│   │       └── SettingAdminController.php# General settings, SEO defaults, contact info
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   ├── Course.php
│   │   ├── Admission.php
│   │   ├── Book.php
│   │   ├── QrLesson.php
│   │   ├── Page.php
│   │   └── Setting.php
│   ├── Middlewares/
│   │   ├── AuthMiddleware.php        # Checks valid admin session; redirects to /admin/login
│   │   ├── GuestMiddleware.php       # Redirects authenticated admins away from login page
│   │   └── CsrfMiddleware.php        # Verifies CSRF token for POST/PUT/DELETE requests
│   └── Helpers/
│       ├── BengaliHelper.php         # Bengali number conversion, date formatting, Unicode slugifier
│       ├── SeoHelper.php             # JSON-LD Schema generator, OpenGraph tags, canonical URLs
│       ├── PrayerTimeHelper.php      # Islamic Foundation Bangladesh prayer calculation & district offsets
│       └── SanitizeHelper.php        # XSS filtering, HTML purification, string trimming
├── views/
│   ├── layouts/
│   │   ├── main.php                  # Public website layout with Royal Islamic theme
│   │   ├── admin.php                 # Admin dashboard layout with sidebar and status bars
│   │   └── blank.php                 # Minimal layout for QR scan and distraction-free lessons
│   ├── partials/
│   │   ├── header.php                # Top bar, branding, mobile navigation toggle
│   │   ├── footer.php                # Quick links, prayer time shortcut, copyright
│   │   ├── nav.php                   # Desktop & mobile navigation bar
│   │   ├── admin-sidebar.php         # Admin navigation drawer
│   │   ├── flash.php                 # Success, error, and warning alert toasts
│   │   └── seo-meta.php              # Automated meta tags, OpenGraph, JSON-LD container
│   ├── home/index.php
│   ├── blog/
│   │   ├── index.php                 # Blog grid with search and category tabs
│   │   └── show.php                  # Blog single view with social share and author box
│   ├── courses/
│   │   ├── index.php                 # Course cards with syllabus highlights
│   │   ├── show.php                  # Detailed curriculum and admission CTA
│   │   └── apply.php                 # Clean, mobile-friendly admission form
│   ├── books/
│   │   ├── index.php                 # Book shelf display
│   │   └── show.php                  # Book detail, preview modal, ordering link
│   ├── utilities/
│   │   ├── prayer-times.php          # 64 Bangladesh district selector, live countdown
│   │   ├── sehri-iftar.php           # Ramadan & daily timetable with Islamic Foundation conventions
│   │   ├── zakat-calculator.php      # Gold/Silver Nisab calculator in BDT
│   │   └── tasbeeh.php               # Digital interactive Tasbeeh with vibration/audio cues
│   ├── scan/
│   │   └── index.php                 # Instant video lesson player matching printed book QR code
│   └── admin/
│       ├── login.php
│       ├── dashboard.php
│       ├── blog/
│       ├── courses/
│       ├── admissions/
│       ├── books/
│       ├── scan/
│       └── settings/
├── database/
│   ├── schema.sql                    # Pure SQL schema for phpMyAdmin single-click import
│   ├── migrations/
│   │   └── 001_initial_schema.php
│   └── seeders/
│       └── 001_initial_seed.php      # Default admin account, sample courses, books, settings
├── storage/
│   ├── uploads/                      # Uploaded images, covers, PDFs
│   │   ├── blog/
│   │   ├── books/
│   │   └── courses/
│   ├── cache/                        # Cached prayer times, XML sitemaps
│   └── logs/                         # Application error & security access logs
└── assets/
    ├── css/
    │   ├── custom.css                # Quranic calligraphy classes, Islamic gradients, animations
    │   └── tailwind.min.css          # Pre-compiled standalone Tailwind CSS
    ├── js/
    │   ├── alpine.min.js             # Alpine.js standalone library (v3.x)
    │   ├── app.js                    # Global interactivity, toast handlers
    │   └── utilities.js              # District prayer time calculators & Tasbeeh logic
    ├── fonts/
    │   └── README.md                 # Local font instructions (Hind Siliguri, Amiri, AASQ)
    └── images/
        ├── logo.png
        └── placeholders/
```

---

## 2. Shared Hosting Portability & Dual-Deployment Mechanics

### 2.1 The Shared Hosting Problem & Solution
Standard PHP frameworks enforce a separate `/public` folder as the web DocumentRoot. In cPanel and Hostinger shared hosting environments, changing the DocumentRoot is either disallowed or requires complex `.htaccess` hacks that often break asset paths and subfolders.

**The Kariana Architecture Solution:**
- **Root Front Controller**: The project can be uploaded directly into `public_html/` or any subfolder (e.g. `/Kariana Website/`).
- **Internal Protection via `.htaccess`**: All core application logic (`/app`, `/config`, `/core`, `/database`, `/storage/logs`) is strictly barred from public HTTP access via Apache access controls.
- **Dynamic Base URL Auto-Detection**: The router calculates the application's base URL automatically regardless of whether it is hosted at:
  - Root domain: `https://karianabd.com/`
  - Subfolder: `http://localhost/Kariana%20Website/`
  - Dedicated development port: `http://localhost:8015/` or `http://192.168.0.100:8015/`

### 2.2 Apache `.htaccess` Production Configuration
Placed in the project root:

```apache
# ==============================================================================
# Kariana Quran Portal - Apache Server Configuration
# Optimized for Hostinger public_html, cPanel, and local XAMPP
# ==============================================================================

# 1. Charset & Options
AddDefaultCharset UTF-8
Options -Indexes +FollowSymLinks
DirectoryIndex index.php

# 2. Prevent Access to Protected Core Directories & Files
<IfModule mod_authz_core.c>
    # Apache 2.4+
    <FilesMatch "^\.">
        Require all denied
    </FilesMatch>
    <FilesMatch "\.(sql|log|ini|env|md|json|lock)$">
        Require all denied
    </FilesMatch>
</IfModule>
<IfModule !mod_authz_core.c>
    # Apache 2.2 Fallback
    <FilesMatch "^\.">
        Order allow,deny
        Deny from all
    </FilesMatch>
    <FilesMatch "\.(sql|log|ini|env|md|json|lock)$">
        Order allow,deny
        Deny from all
    </FilesMatch>
</IfModule>

# Protect internal directories
RewriteEngine On
RewriteRule ^(app|config|core|database|storage/logs)/ - [F,L,NC]

# 3. Handle Static Assets directly
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# 4. Front Controller Routing
# Pass all non-file requests to index.php with original query string preserved
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# 5. Browser Caching for Performance (Core Web Vitals)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/ttf "access plus 1 year"
</IfModule>

# 6. Enable Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>
```

### 2.3 Built-in Development Server (`router.php`) for Port 8015
To satisfy the rule of dedicated port `8015` (`php -S 0.0.0.0:8015 router.php`) without needing Apache running:

```php
<?php
/**
 * CLI Built-in Server Router for dedicated port 8015
 * Binds to 0.0.0.0 for LAN/Wi-Fi multi-device access (192.168.0.100:8015)
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);
$filePath = __DIR__ . $uri;

// If file exists and is not a PHP script, serve directly
if ($uri !== '/' && file_exists($filePath) && !is_dir($filePath)) {
    $ext = pathinfo($filePath, PATHINFO_EXTENSION);
    if ($ext !== 'php') {
        // Return false to let PHP CLI serve the static file directly
        return false;
    }
}

// Forward everything else to index.php
require_once __DIR__ . '/index.php';
```

---

## 3. Pure PHP 8.2 Autoloader & Core Kernel

### 3.1 Zero-Vendor Native PSR-4 Autoloader (`core/Autoloader.php`)
Ensures no Composer binary is required on shared hosting:

```php
<?php
namespace Core;

class Autoloader
{
    private static array $prefixes = [];

    public static function register(): void
    {
        spl_autoload_register([__CLASS__, 'loadClass']);

        // Register default namespace mappings
        self::addNamespace('App', dirname(__DIR__) . '/app');
        self::addNamespace('Core', dirname(__DIR__) . '/core');
    }

    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR;
        self::$prefixes[$prefix] = $baseDir;
    }

    public static function loadClass(string $class): bool
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;
            }

            $relativeClass = substr($class, $len);
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

            if (file_exists($file)) {
                require_once $file;
                return true;
            }
        }
        return false;
    }
}
```

---

## 4. URL Routing & Unicode Bengali Slug Handling

### 4.1 The Bengali Slug Challenge & Technical Solution
Bengali characters are multi-byte UTF-8 sequences (3 bytes per character in UTF-8).
- A URL such as `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা` arrives at the server percent-encoded as `%E0%A6%B8%E0%A6%B9%E0%A6%9C...`.
- In PHP, `$_SERVER['REQUEST_URI']` contains the percent-encoded string.
- If standard regex matching is used without the PCRE `u` (Unicode) flag or without decoding, route matches fail or capture corrupted substrings.
- Furthermore, query strings (`?page=2&tag=তাজবীদ`) must be cleanly parsed and stripped from the matching path.

### 4.2 Dynamic Base Path Resolution
The router must automatically determine whether the project is running at the server root (`/`) or in a subfolder (`/Kariana Website/`).

```php
<?php
namespace Core;

class Request
{
    private string $method;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private string $baseUrl;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        // Support method override for PUT/DELETE in HTML forms
        if ($this->method === 'POST' && isset($_POST['_method'])) {
            $this->method = strtoupper($_POST['_method']);
        }

        // 1. Parse URI and Query
        $rawUri = $_SERVER['REQUEST_URI'] ?? '/';
        $parsedUrl = parse_url($rawUri);
        $rawPath = $parsedUrl['path'] ?? '/';
        
        // 2. Decode percent-encoded Unicode (Bengali)
        $decodedPath = rawurldecode($rawPath);

        // 3. Dynamic Base Path Stripping (Works on both root & subfolders)
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $scriptDir = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;

        if ($scriptDir !== '' && str_starts_with($decodedPath, $scriptDir)) {
            $decodedPath = substr($decodedPath, strlen($scriptDir));
        }

        $this->path = '/' . trim($decodedPath, '/');
        $this->queryParams = $_GET;
        $this->bodyParams = $_POST;

        // Base URL calculation for asset links
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->baseUrl = rtrim($scheme . '://' . $host . $scriptDir, '/');
    }

    public function getMethod(): string { return $this->method; }
    public function getPath(): string { return $this->path; }
    public function getBaseUrl(): string { return $this->baseUrl; }
    public function get(string $key, mixed $default = null): mixed { return $this->queryParams[$key] ?? $default; }
    public function post(string $key, mixed $default = null): mixed { return $this->bodyParams[$key] ?? $default; }
    public function all(): array { return array_merge($this->queryParams, $this->bodyParams); }
}
```

### 4.3 Unicode-Compliant Regex Router (`core/Router.php`)
The router transforms `{slug}` into `([^/]+)` and executes regex matches using the `/u` (UTF-8) modifier:

```php
<?php
namespace Core;

class Router
{
    private array $routes = [];
    private array $namedRoutes = [];

    public function get(string $path, array|callable $handler, array $middlewares = [], string $name = ''): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares, $name);
    }

    public function post(string $path, array|callable $handler, array $middlewares = [], string $name = ''): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares, $name);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares, string $name): void
    {
        $cleanPath = '/' . trim($path, '/');
        $this->routes[] = [
            'method' => $method,
            'path' => $cleanPath,
            'handler' => $handler,
            'middlewares' => $middlewares,
            'name' => $name
        ];
        if ($name !== '') {
            $this->namedRoutes[$name] = $cleanPath;
        }
    }

    public function dispatch(Request $request): Response
    {
        $requestMethod = $request->getMethod();
        $requestPath = $request->getPath();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Convert route definition to regex with Unicode support
            // Example: /blog/{slug} -> #^/blog/(?P<slug>[^/]+)$#u
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#u';

            if (preg_match($pattern, $requestPath, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Execute Middlewares (e.g. Auth, CSRF)
                foreach ($route['middlewares'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $result = $middleware->handle($request);
                    if ($result instanceof Response) {
                        return $result;
                    }
                }

                // Execute Controller Handler
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$controllerClass, $method] = $handler;
                    $controller = new $controllerClass();
                    return call_user_func_array([$controller, $method], [$request, $params]);
                }

                return call_user_func_array($handler, [$request, $params]);
            }
        }

        // Route Not Found (404)
        $response = new Response();
        $response->setStatusCode(404);
        $response->setContent(View::render('errors/404', ['title' => 'পাতাটি পাওয়া যায়নি | ৪MD']));
        return $response;
    }
}
```

### 4.4 Bengali Slug Generator Helper (`app/Helpers/BengaliHelper.php`)
Generates search-engine-friendly Bengali slugs without transliteration loss:

```php
<?php
namespace App\Helpers;

class BengaliHelper
{
    /**
     * Converts a Bengali or English title into a clean URL slug
     * Preserves Bengali characters: \p{Bengali}
     */
    public static function slugify(string $text): string
    {
        // 1. Trim whitespace
        $text = trim($text);

        // 2. Replace unwanted characters with hyphen (preserves Bengali script and ASCII alphanumeric)
        // \p{Bengali} covers Bengali Unicode block U+0980 to U+09FF
        $text = preg_replace('/[^\p{Bengali}a-zA-Z0-9\s-]/u', '', $text);

        // 3. Replace repeated spaces and underscores with single hyphen
        $text = preg_replace('/[\s_]+/', '-', $text);

        // 4. Clean consecutive hyphens
        $text = preg_replace('/-+/', '-', $text);

        // 5. Trim hyphens from ends
        $slug = trim($text, '-');

        return mb_strtolower($slug, 'UTF-8');
    }

    /**
     * Convert English digits to Bengali digits
     */
    public static function en2bnNumber(int|string $number): string
    {
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        return str_replace($en, $bn, (string)$number);
    }

    /**
     * Convert Bengali digits to English digits
     */
    public static function bn2enNumber(string $number): string
    {
        $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        $en = ['0','1','2','3','4','5','6','7','8','9'];
        return str_replace($bn, $en, $number);
    }
}
```

---

## 5. Database Architecture & MySQL/MariaDB PDO Engine

### 5.1 Connection Singleton (`core/Database.php`)
- Strict UTF8MB4 configuration (`utf8mb4_unicode_ci`) ensures Arabic diacritics, Quranic symbols, and Bengali conjuncts are never mangled or converted to question marks (`???`).
- Emulated prepares disabled (`PDO::ATTR_EMULATE_PREPARES => false`) to enforce server-side prepared statements and defeat SQL injection.

```php
<?php
namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $config = require dirname(__DIR__) . '/config/database.php';
            
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'] ?? 3306,
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], $options);
            } catch (PDOException $e) {
                // In production, log to file and show clean error page
                error_log("Database Connection Failed: " . $e->getMessage());
                die("ডাটাবেজ সংযোগে সমস্যা হয়েছে। অনুগ্রহ করে কিছুক্ষণ পর আবার চেষ্টা করুন।");
            }
        }
        return self::$instance;
    }
}
```

### 5.2 Complete Database Schema Definition (`database/schema.sql`)

```sql
-- =============================================================================
-- Kariana Quran Islamic Educational Portal & CMS
-- Database Schema (MySQL 5.7+ / MariaDB 10.3+)
-- Charset: utf8mb4, Collation: utf8mb4_unicode_ci
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Users Table (Admin & Authors)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('superadmin', 'editor') NOT NULL DEFAULT 'editor',
    `remember_token` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Post Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(191) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Posts Table (Blog & News)
CREATE TABLE IF NOT EXISTS `posts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `excerpt` TEXT NULL,
    `content` LONGTEXT NOT NULL,
    `cover_image` VARCHAR(255) NULL,
    `category_id` INT UNSIGNED NULL,
    `author_id` INT UNSIGNED NOT NULL,
    `status` ENUM('published', 'draft') NOT NULL DEFAULT 'published',
    `meta_title` VARCHAR(255) NULL,
    `meta_description` VARCHAR(300) NULL,
    `canonical_url` VARCHAR(255) NULL,
    `views_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `published_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_slug` (`slug`),
    INDEX `idx_status_published` (`status`, `published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tags Table
CREATE TABLE IF NOT EXISTS `tags` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Post-Tag Pivot Table
CREATE TABLE IF NOT EXISTS `post_tags` (
    `post_id` INT UNSIGNED NOT NULL,
    `tag_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`post_id`, `tag_id`),
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`tag_id`) REFERENCES `tags`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Courses Table
CREATE TABLE IF NOT EXISTS `courses` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT NOT NULL,
    `curriculum` LONGTEXT NULL,
    `duration` VARCHAR(100) NOT NULL,
    `fee` VARCHAR(100) NOT NULL,
    `class_schedule` VARCHAR(150) NULL,
    `instructor_name` VARCHAR(150) NULL,
    `cover_image` VARCHAR(255) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Online Admissions Table
CREATE TABLE IF NOT EXISTS `admissions` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `course_id` INT UNSIGNED NOT NULL,
    `student_name` VARCHAR(150) NOT NULL,
    `guardian_name` VARCHAR(150) NULL,
    `phone` VARCHAR(30) NOT NULL,
    `whatsapp` VARCHAR(30) NULL,
    `email` VARCHAR(150) NULL,
    `district` VARCHAR(100) NOT NULL,
    `address` TEXT NULL,
    `gender` ENUM('male', 'female') NOT NULL DEFAULT 'male',
    `age` INT NULL,
    `previous_education` VARCHAR(255) NULL,
    `status` ENUM('pending', 'contacted', 'enrolled', 'cancelled') NOT NULL DEFAULT 'pending',
    `admin_notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE,
    INDEX `idx_status` (`status`),
    INDEX `idx_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Books & Publications Table
CREATE TABLE IF NOT EXISTS `books` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `author` VARCHAR(150) NOT NULL DEFAULT 'কারিআনা নূরানী কুরআন একাডেমি',
    `description` TEXT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `buy_link` VARCHAR(255) NULL,
    `pdf_preview_url` VARCHAR(255) NULL,
    `cover_image` VARCHAR(255) NULL,
    `isbn` VARCHAR(50) NULL,
    `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. QR Lesson Gateway Table (Phase 2 Roadmap)
CREATE TABLE IF NOT EXISTS `qr_lessons` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `book_id` INT UNSIGNED NOT NULL,
    `page_number` INT NOT NULL,
    `qr_code_token` VARCHAR(100) NOT NULL UNIQUE,
    `title` VARCHAR(255) NOT NULL,
    `video_url` VARCHAR(255) NOT NULL,
    `notes` TEXT NULL,
    `view_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
    INDEX `idx_token` (`qr_code_token`),
    INDEX `idx_book_page` (`book_id`, `page_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Dynamic Pages Table
CREATE TABLE IF NOT EXISTS `pages` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(191) NOT NULL UNIQUE,
    `content` LONGTEXT NOT NULL,
    `meta_title` VARCHAR(255) NULL,
    `meta_description` VARCHAR(300) NULL,
    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. System Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
    `setting_key` VARCHAR(100) PRIMARY KEY,
    `setting_value` TEXT NULL,
    `group_name` VARCHAR(50) NOT NULL DEFAULT 'general',
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Migrations Tracking Table
CREATE TABLE IF NOT EXISTS `migrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(191) NOT NULL UNIQUE,
    `batch` INT NOT NULL DEFAULT 1,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
```

---

## 6. Frontend Asset Integration & Islamic Visual Aesthetics

### 6.1 Color Palette Specifications
The visual theme reflects authentic Royal Islamic craftsmanship, combining lush emerald green with warm Quranic gold:
- **Primary — Royal Islamic Emerald Green**:
  - `bg-emerald-950` (`#064e3b`): Deep emerald header backgrounds, hero sections, footer base, card hover accents.
  - `bg-emerald-700` / `text-emerald-700` (`#047857`): Primary interactive elements, buttons, badges, navigation active state.
  - `bg-emerald-600` (`#059669`): Button hover and focus ring colors.
  - `bg-emerald-50` (`#ecfdf5`): Clean, soft tinted backgrounds for feature cards, prayer time displays, and blockquotes.
- **Secondary — Warm Quranic Gold**:
  - `bg-amber-600` / `text-amber-600` (`#d97706`): Quranic accents, call-to-action buttons, border highlights, prayer countdown timers.
  - `bg-amber-500` (`#f59e0b`): Secondary badges, star ratings, golden borders.
  - `bg-amber-50` (`#fffbeb`): Warm parchment glow behind Quranic ayah cards.
- **Neutral & Reading Comfort**:
  - `bg-[#fcfbf7]`: Soft eggshell cream background to minimize eye fatigue during extended Quranic and Tajweed reading.
  - `text-slate-800` (`#1e293b`): High-contrast, sharp typography for effortless legibility on mobile screens under bright daylight.

### 6.2 Typography Integration
1. **Bengali Primary**: `Hind Siliguri` (Google Web Font)
   ```html
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Hind+Siliguri:wght@300;400;500;600;700&family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
   ```
2. **Quranic Arabic**: `Amiri` and `Scheherazade New` + Local `@font-face` referencing Kariana Quran custom font (`AAR-SQ-003` / `AASQv10`) discovered in sibling project `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Font\`.
3. **CSS Typography Definition**:
   ```css
   body {
       font-family: 'Hind Siliguri', 'SolaimanLipi', sans-serif;
   }
   .font-quran {
       font-family: 'AASQv10', 'Amiri', 'Scheherazade New', serif;
       line-height: 2.2;
       direction: rtl;
   }
   ```

### 6.3 Zero-Build Tailwind CSS & Alpine.js Delivery
To eliminate Node.js daemon and `npm` build requirements on shared hosting:
- **Tailwind CDN with Embedded Config**:
  ```html
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            emerald: {
              950: '#064e3b',
              900: '#064e3b',
              800: '#065f46',
              700: '#047857',
              600: '#059669',
              50: '#ecfdf5',
            },
            gold: {
              600: '#d97706',
              500: '#f59e0b',
              100: '#fef3c7',
              50: '#fffbeb',
            }
          },
          fontFamily: {
            bengali: ['"Hind Siliguri"', 'SolaimanLipi', 'sans-serif'],
            quran: ['"AASQv10"', 'Amiri', '"Scheherazade New"', 'serif'],
          }
        }
      }
    }
  </script>
  ```
- **Alpine.js Standalone Script**:
  Delivered locally in `assets/js/alpine.min.js` (and fallback CDN):
  ```html
  <script defer src="/assets/js/alpine.min.js"></script>
  ```
  Powers mobile nav drawer, prayer time district dropdown, Tasbeeh counter, Zakat live calculator, and SERP live preview with zero build overhead.

---

## 7. Security Architecture: Authentication, Sessions & CSRF

### 7.1 Cryptographic CSRF Protection Middleware (`core/Csrf.php`)
Every state-changing request (`POST`, `PUT`, `DELETE`) is guarded against Cross-Site Request Forgery:

```php
<?php
namespace Core;

class Csrf
{
    private const TOKEN_KEY = '_csrf_token';

    public static function generateToken(): string
    {
        Session::start();
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::TOKEN_KEY];
    }

    public static function getToken(): string
    {
        return self::generateToken();
    }

    public static function field(): string
    {
        $token = self::getToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validate(Request $request): bool
    {
        Session::start();
        $storedToken = $_SESSION[self::TOKEN_KEY] ?? '';
        if (empty($storedToken)) {
            return false;
        }

        // Check POST parameter first, then X-CSRF-TOKEN header
        $submittedToken = $request->post('_csrf_token') ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        return hash_equals($storedToken, $submittedToken);
    }
}
```

### 7.2 Hardened Session Security (`core/Session.php`)
- Prevents session hijacking and fixation.
- Automatically marks session cookies with `HttpOnly`, `SameSite=Lax`, and `Secure` (when HTTPS is active).
- Regenerates session ID upon authentication to invalidate previous identifiers.

```php
<?php
namespace Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            
            session_set_cookie_params([
                'lifetime' => 0, // Until browser closes
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();

            // Session hijacking defense: User-Agent validation
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            if (!isset($_SESSION['_user_agent'])) {
                $_SESSION['_user_agent'] = $ua;
            } elseif ($_SESSION['_user_agent'] !== $ua) {
                // Potential hijacking attempt: destroy session
                self::destroy();
                session_start();
            }
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, ?string $message = null): mixed
    {
        self::start();
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return null;
        }
        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
        }
    }
}
```

### 7.3 Authentication Guard (`app/Middlewares/AuthMiddleware.php`)

```php
<?php
namespace App\Middlewares;

use Core\Request;
use Core\Response;
use Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): ?Response
    {
        Session::start();
        if (!Session::get('user_id')) {
            $response = new Response();
            $response->redirect($request->getBaseUrl() . '/admin/login');
            return $response;
        }
        return null;
    }
}
```

---

## 8. Dedicated Port 8015 & Multi-Device Access Protocol

Adhering strictly to user rules:
- **Dedicated Port**: `8015`
- **Network Interface**: `0.0.0.0`
- **Access URLs**:
  - **Local PC**: `http://localhost:8015`
  - **Mobile / Same Wi-Fi**: `http://192.168.0.100:8015`
- **Development Launch Command**:
  ```powershell
  php -S 0.0.0.0:8015 router.php
  ```
- **XAMPP Apache Option**:
  Alternatively, if served via Apache under XAMPP:
  `http://localhost/Kariana%20Website/`
  Both modes work interchangeably because `Core\Request` dynamically detects and strips subfolder paths.

---

## 9. Next Steps for Implementation Agents

1. **Phase 1 Execution (Foundation & Core MVC)**:
   - Create core framework classes (`core/Autoloader.php`, `core/App.php`, `core/Router.php`, `core/Request.php`, `core/Response.php`, `core/Database.php`, `core/Session.php`, `core/Csrf.php`, `core/Controller.php`, `core/View.php`).
   - Create entry points (`index.php`, `router.php`, `.htaccess`).
   - Populate `config/app.php` and `config/database.php`.
   - Implement `database/schema.sql` and seeder.
2. **Phase 2 (Public Features & SEO Dominance)**:
   - Public controllers for Home, Blog, Courses, Books, Utilities, and Pages.
   - Layout templates with Royal Emerald Green & Gold Tailwind styling.
   - Bengali slug resolution and Schema.org JSON-LD generation.
   - 64-district Islamic Foundation prayer time calculator.
3. **Phase 3 (Admin CMS & Lead Management)**:
   - Admin authentication and secure session management.
   - Blog editor with Bengali slug auto-generator & Google SERP preview.
   - Admission lead management with status updates & CSV export.
   - QR lesson manager for printed book scanning.
