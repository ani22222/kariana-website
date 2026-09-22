# Milestone M1 - Changes & Deliverables Log

**Project:** Kariana Quran (কারিয়ানা কুরআন) Islamic Educational Portal & CMS  
**Milestone:** M1 - Core Framework, Database & Router  
**Author:** Worker M1 (`implementer`)  
**Date:** 2026-09-22  

---

## 1. Core Framework Architecture (`core/`)

| File | Description | Key Methods / Capabilities |
|---|---|---|
| `core/Autoloader.php` | Zero-vendor PSR-4 native class autoloader | `register()`, `addNamespace($prefix, $baseDir)`, `loadClass($class)`. Handles case-sensitive Linux shared hosting and Windows XAMPP. |
| `core/App.php` | Application Kernel & Front Controller Bootstrap | Lifecycle orchestration, timezone initialization (`Asia/Dhaka`), exception handler with detailed stack in debug mode and clean 500 in production, session aging. |
| `core/Request.php` | Normalized HTTP Request Parser | `getMethod()`, `isGet()`, `isPost()`, `getPath()`, `getBaseUrl()`, `url()`, `get()`, `post()`, `all()`, `getHeader()`, `getIp()`. Features automatic subfolder stripping (e.g. `/Kariana Website/`) and URL percent-decoding for Bengali paths. |
| `core/Response.php` | HTTP Response Builder | `setStatusCode()`, `setHeader()`, `setContent()`, `send()`, `json($data, $code)`, `redirect($url, $code)`. |
| `core/Router.php` | Unicode PCRE `/u` Regex Router | `get()`, `post()`, `put()`, `delete()`, `patch()`, `any()`, `dispatch()`, `setNotFound()`. Supports clean UTF-8 Bengali slugs like `/blog/{slug}` -> `#^/blog/(?P<slug>[^/]+)$#u`, parameter reflection injection, and middleware execution. |
| `core/Controller.php` | Base MVC Controller | `render($view, $data, $layout)`, `json($data, $code)`, `redirect($path)`, `model($name)`, `validateCsrf($request)`. |
| `core/Model.php` | PDO Database Base Model | `find($id)`, `findBy($col, $val)`, `where($conditions)`, `all()`, `count()`, `create($data)`, `update($id, $data)`, `delete($id)`, `raw($sql, $params)`. 100% prepared statements with parameterized binding. |
| `core/View.php` | Template & Layout Rendering Engine | `render($view, $data, $layout)`, `partial($name, $data)`, `escape($val)`. Looks up templates in `app/Views/` with fallback to `views/`, injecting global shared variables (`$baseUrl`, `$csrfToken`, `$user`). |
| `core/Database.php` | High-Security PDO Singleton | Strict `utf8mb4` charset and `utf8mb4_unicode_ci` collation, `PDO::ATTR_EMULATE_PREPARES => false`, `PDO::ERRMODE_EXCEPTION`. Prevents credential leakage on error. |
| `core/Session.php` | Hardened Session & Flash Messenger | `start()`, `regenerate(true)` (session fixation defense), `set()`, `get()`, `has()`, `setFlash()`, `getFlash()`, `setUser()`, `getUser()`, `isLoggedIn()`, `logout()`. Configured with `samesite=Lax`, `httponly=true`, `use_strict_mode=1`. |
| `core/Csrf.php` | Cryptographic CSRF Defense | `token()` (256-bit cryptographic entropy via `random_bytes(32)`), `validate($token)` (timing-safe verification via `hash_equals()`), `field()` (HTML hidden input), `meta()`. |
| `core/BengaliHelper.php` | Bengali Numeral & Unicode Slug Engine | `toBengaliNumber()` (0-9 to ০-৯), `toEnglishNumber()` (০-৯ to 0-9), `createSlug()` (strictly preserves `\p{Bengali}` Unicode block while stripping symbols, lowercasing Latin, collapsing hyphens). |

---

## 2. Configuration (`config/`)

| File | Description | Details |
|---|---|---|
| `config/app.php` | Application Metadata & Defaults | App name ('কারিয়ানা কুরআন'), port 8015, timezone 'Asia/Dhaka', locale 'bn_BD', Quran reader link 'http://localhost:8014', IFB prayer parameters and Hanafi Zakat defaults. |
| `config/database.php` | Database Credentials | Host `localhost`, port `3306`, database `kariana_portal`, username `root`, password `""`, charset `utf8mb4`, collation `utf8mb4_unicode_ci`. |
| `config/districts.php` | Complete 64 Districts Matrix | All 64 districts in Bangladesh across 8 divisions with Bengali names, English names, latitude, longitude, and Islamic Foundation Bangladesh (IFB) prayer minute offsets from Dhaka baseline. |

---

## 3. Database Schema, Migration & Seeder (`database/`)

| File | Description | Execution Result |
|---|---|---|
| `database/schema.sql` | Full 12 Normalized Relational Tables + View | `users`, `categories`, `posts`, `courses`, `admissions`, `books`, `pages`, `qr_lessons`, `prayer_districts`, `zakat_settings`, `site_settings`, `migrations`, plus backward-compatibility view `districts`. Fully indexed with foreign keys and `utf8mb4_unicode_ci`. |
| `database/migrate.php` | Database Migration Runner | Connects to MariaDB, runs `CREATE DATABASE IF NOT EXISTS \`kariana_portal\``, executes `schema.sql`, logs `001_initial_schema` to `migrations`. Tested and verified via CLI and API runner. |
| `database/seed.php` | Comprehensive Data Seeder | Seeds administrator `admin` / `admin123` (bcrypt hash), all 64 districts with IFB offsets, 5 blog categories, 4 initial courses (কায়েদা, নাজেরা, হিফজ, ক্বেরাত), 3 publications, sample blog post with Bengali slug `সহজ-পদ্ধতিতে-কুরআন-শেখা`, Zakat defaults, and site settings. |

---

## 4. Root Entrypoints & Web Server Routing

| File | Description | Details |
|---|---|---|
| `index.php` | Front Controller & Request Dispatcher | Bootstraps autoloader, initializes `Core\App`, copies `AAR-SQ-003.ttf` if missing, maps core web routes (Home, Courses, Books, Blog with Bengali slug, Prayer Times, Zakat, Tasbeeh, QR Scan, Health API, 64 Districts API, Verification API), dispatches response. |
| `.htaccess` | Apache Mod_Rewrite Engine | Configured for Hostinger/cPanel `public_html` and local XAMPP Apache. Blocks sensitive directories (`app`, `config`, `core`, `database`, `storage`, `.agents`), serves physical files directly, forwards dynamic URLs to `index.php [QSA,L]`, activates Gzip compression and browser caching. |
| `router.php` | Dedicated Port 8015 Server Router | Serves PHP built-in web server `php -S 0.0.0.0:8015 router.php`. Maps static assets with exact MIME types and rewrites dynamic requests to `index.php`. |

---

## 5. Layout & Base Styling

| File | Description | Details |
|---|---|---|
| `public/assets/css/main.css` | Islamic Visual Identity & Font Tokens | Defines `@font-face` for verified Kariana Arabic font `AAR-SQ-003.ttf`, `@import` for `Hind Siliguri` & `Amiri`, CSS variables for Royal Islamic Emerald Green (`#064e3b`, `#047857`, `#022c22`) and Warm Quranic Gold (`#d97706`, `#b45309`, `#fbbf24`), arabesque background, gold borders, and prayer badge styles. |
| `public/assets/fonts/AAR-SQ-003.ttf` | Verified Kariana Arabic Font File | 682,112 bytes copied from adjacent workspace `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Font\AAR-SQ-003.ttf`. |
| `public/assets/js/app.js` | Client Application Script | Dynamic Bengali date generator (converts system Gregorian date to Bengali day, month, and numerals). |
| `app/Views/layouts/main.php` | Master Public Layout | Tailwind CSS + Alpine.js, Google Fonts, top prayer ticker bar with live Bengali date & waqt times, header with logo and responsive mobile dropdown menu, main slot, and rich 4-column Islamic footer. |
| `app/Views/home/index.php` | Public Homepage View | Emerald & Gold hero banner, Arabic Ayah (`font-kariana`), 3-step feature cards, and CTAs. |
| `app/Views/errors/404.php` | 404 Not Found View | Clean Islamic-themed error display with home redirect. |
| Additional Views | Course, Book, Blog, Utilities | Initial semantic views created for `courses/index`, `courses/show`, `books/index`, `blog/index`, `blog/show`, `utilities/prayer-times`, `utilities/zakat`, `utilities/tasbeeh`, `scan/index`. |
