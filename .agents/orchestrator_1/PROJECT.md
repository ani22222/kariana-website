# Project: Kariana Quran Islamic Educational Portal & CMS

## Architecture
- **Language & Runtime**: Pure PHP 8.2+ with zero runtime Composer/vendor bloat.
- **Architecture Pattern**: Front Controller MVC (`Core\App`, `Core\Router`, `Core\Controller`, `Core\Model`, `Core\View`).
- **Database Engine**: MariaDB / MySQL 10.4+ via PDO singleton (`Core\Database`) with `utf8mb4_unicode_ci` charset and 100% prepared statements.
- **Frontend Stack**: Standalone Tailwind CSS + Alpine.js (zero Node.js daemon required, 100% Shared Hosting ready for Hostinger/cPanel/XAMPP).
- **Branding**: Royal Islamic Emerald Green (`#064e3b` / `#047857`) & Warm Quranic Gold (`#d97706`), typography using Google Fonts `Hind Siliguri` (Bengali) + `Amiri` (Arabic) + verified local Kariana font `AAR-SQ-003.ttf`.
- **Server Deployment**: Dual deployment ready — Apache `.htaccess` URL rewriting for standard web hosting, plus CLI `router.php` on dedicated port 8015 bound to `0.0.0.0` (accessible via `http://localhost:8015` and `http://192.168.0.100:8015`).

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Zero-Vendor PSR-4 Autoloader | Native PHP class autoloader for Core, Models, Controllers, Helpers | M1 | Survey (Explorer 2) |
| 2 | Dynamic Base URL Detection | Auto-resolves subdirectories, custom ports, and root domains | M1 | Survey (Explorer 2) |
| 3 | Unicode PCRE `/u` Router | Regex router supporting clean UTF-8 Bengali slugs and query parameters | M1 | Survey (Explorer 2) |
| 4 | MySQL PDO Singleton | Secure UTF8MB4 database layer with prepared statements and transactions | M1 | Survey (Explorer 2) |
| 5 | Relational Database Schema & Seeder | 9-12 normalized tables with sample data (admin, courses, books, posts) | M1 | Survey (Spec Miner 1) |
| 6 | CSRF & Session Security | Cryptographic token generation/validation, session fixation defense | M1 | Survey (Explorer 2) |
| 7 | Dedicated Port 8015 CLI Router | `router.php` serving static assets and routing dynamic MVC requests on `0.0.0.0:8015` | M1 | Survey (Explorer 1 & 2) |
| 8 | Master Layout & UI Theme | Royal Islamic Emerald Green & Gold design with Tailwind CSS & Alpine.js | M1 | Survey (Explorer 2) |
| 9 | Public Home Page | Hero, quick prayer bar, featured courses, book showcase, testimonials | M2 | Survey (Spec Miner 1) |
| 10 | Course Catalog & Details | Course listing (নাজেরা, হিফজ, ক্বেরাত, তাজবীদ) with syllabus details | M2 | Survey (Spec Miner 1) |
| 11 | Online Student Admission Form | Interactive admission form with instant validation and DB storage | M2 | Survey (Spec Miner 1) |
| 12 | Books & Publications Catalog | Book showcase with cover images, description, and PDF preview modal | M2 | Survey (Spec Miner 1) |
| 13 | Blog & News System | Article listing, category/tag filtering, clean Bengali slug URLs | M2 | Survey (Spec Miner 1) |
| 14 | Dynamic Institutional Pages | About Us, Methodology, Teachers, Branches, Contact pages | M2 | Survey (Spec Miner 1) |
| 15 | Automated Schema.org JSON-LD | Microdata generation: Organization, Article, Course, FAQPage, BreadcrumbList | M2 | Survey (Spec Miner 1) |
| 16 | Dynamic XML Sitemap & Robots.txt | Dynamic XML sitemap supporting Bengali URLs and crawler directives | M2 | Survey (Spec Miner 1) |
| 17 | Social OpenGraph & Twitter Cards | Dynamic meta tags for social media sharing preview | M2 | Survey (Spec Miner 1) |
| 18 | 64 Districts Prayer Times Engine | IFB conventions (Dhaka baseline + 64 districts offset matrix) | M3 | Survey (Spec Miner 1) |
| 19 | Sehri & Iftar Timetable | Dynamic timetable with Ramadan Ashra badges and authentic Duas | M3 | Survey (Spec Miner 1) |
| 20 | Hanafi Silver Nisab Zakat Calculator | 52.5 Tola Silver benchmark, editable BAJUS market rates, net calculation | M3 | Survey (Spec Miner 1) |
| 21 | Interactive Digital Tasbeeh | Web Audio click sound, haptic feedback, LocalStorage count persistence | M3 | Survey (Spec Miner 1) |
| 22 | Dedicated Quran App Bridge | UI launchpad and bridge to Kariana Quran reader with custom font support | M3 | Survey (Spec Miner 1) |
| 23 | Admin Authentication & Guard | Secure login with bcrypt hash, session management, logout | M4 | Survey (Explorer 2) |
| 24 | Admin Dashboard Overview | Metrics overview: total articles, courses, admissions leads, books | M4 | Survey (Spec Miner 1) |
| 25 | Admin Blog & News CMS | CRUD with Bengali slug auto-generator and live Google SERP preview | M4 | Survey (Spec Miner 1) |
| 26 | Admin Courses Manager | CRUD for courses, syllabus modules, fee structures, age groups | M4 | Survey (Spec Miner 1) |
| 27 | Admin Admissions Lead Hub | Admissions database with status tracking and UTF-8 BOM CSV export | M4 | Survey (Spec Miner 1) |
| 28 | Admin Books & Publications CMS | CRUD for books, PDF sample attachments, ordering links | M4 | Survey (Spec Miner 1) |
| 29 | Admin Dynamic Pages & SEO Manager | Per-page meta title, meta description, and canonical URL editor | M4 | Survey (Spec Miner 1) |
| 30 | Admin Marketing & Integration Hub | Facebook Pixel, Google Search Console/Bing meta tags, GTM/GA4, custom <head>, <body> start, and </body> footer script injection | M4 | User Request (2026-09-22) |
| 31 | Phase 2 QR Book Scanner | HTML5 in-browser camera scanner with manual page fallback (`/scan?code=...`) | M5 | Survey (Spec Miner 1) |
| 32 | Phase 2 Video Lesson Gateway | 16:9 distraction-free video player for printed book page lessons | M5 | Survey (Spec Miner 1) |
| 33 | Admin QR Lessons Manager | CRUD linking printed book pages to video lesson URLs and Tajweed tips | M5 | Survey (Spec Miner 1) |
| 34 | E2E Testing Suite (Tiers 1-4) | Systematic automated test suite covering all features, routes, and edge cases | M6 | Survey (Spec Miner 1) |
| 35 | Multi-Device Live Server & Audit | Binding `0.0.0.0:8015`, verifying local & Wi-Fi IP, and Forensic Integrity Audit | M6 | Survey (Explorer 1) |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M1 | Core Framework, Database & Router | Features 1-8: PSR-4 Autoloader, PDO DB singleton, schema.sql, migration runner, router.php (port 8015 on 0.0.0.0), CSRF, base layout | none | PLANNED |
| M2 | Public Portal, Educational Hub & SEO | Features 9-17: Home, Courses, Books, Blog (Bengali slugs), Pages, Schema.org JSON-LD, XML sitemap | M1 | PLANNED |
| M3 | Islamic Utilities & Quran Reader Bridge | Features 18-22: 64 districts prayer times, Sehri/Iftar, Zakat calculator, digital Tasbeeh, Quran App bridge | M1 | PLANNED |
| M4 | Admin CMS & Admissions Management | Features 23-30: Auth, Dashboard, Blog CMS + SERP preview, Courses, Admissions Hub + UTF-8 BOM CSV export, Books, Pages SEO, Marketing & Script Integrations Hub (FB Pixel, GSC, Custom Header/Footer Scripts) | M1 | PLANNED |
| M5 | Phase 2 QR Scanner & Video Gateway | Features 31-33: In-browser QR camera scanner, video lesson gateway (`/scan?code=...`), Admin QR lesson mapping | M1, M4 | PLANNED |
| M6 | E2E Verification & Multi-Device Live Delivery | Features 34-35: Opaque-box E2E test suite pass, port 8015 verification on 0.0.0.0, Forensic Integrity Audit | M2, M3, M4, M5 | PLANNED |

## Interface Contracts
### Core Framework ↔ Controllers & Views
- `Core\Request`:
  - `getPath()`: returns cleaned request path (decoded from UTF-8 URL).
  - `getMethod()`: returns HTTP method ('GET', 'POST').
  - `getBody()`: returns sanitized `$_POST` array.
  - `getQueryParams()`: returns `$_GET` array.
  - `getBaseUrl()`: returns dynamically detected base URL (`http://localhost:8015` or subfolder path).
- `Core\Response`:
  - `json(array $data, int $statusCode = 200)`: sends JSON response.
  - `redirect(string $path)`: redirects using base URL.
- `Core\View`:
  - `render(string $view, array $data = [], string $layout = 'layouts/main')`: renders template within layout.
- `Core\Database`:
  - `getInstance()`: returns singleton PDO instance with UTF8MB4 charset.
- `Core\Csrf`:
  - `token()`: returns current session CSRF token.
  - `validate(?string $token)`: validates token with `hash_equals()`.
- `Core\BengaliHelper`:
  - `toBengaliNumber(int|string $number)`: converts English digits (0-9) to Bengali (০-৯).
  - `toEnglishNumber(string $bengaliNumber)`: converts Bengali digits to English.
  - `createSlug(string $title)`: generates clean Bengali Unicode slug matching `[\x{0980}-\x{09FF}a-zA-Z0-9\-]+`.

### Public Portal ↔ Islamic Calculation Engine
- `App\Services\PrayerTimeService`:
  - `calculatePrayerTimes(string $districtName, ?string $date = null)`: returns prayer times array (Fajr, Sunrise, Dhuhr, Asr, Maghrib, Isha, Tahajjud, Ishraq) using IFB conventions and district offsets.
  - `getDistricts()`: returns all 64 districts in Bengali and English with coordinates and time offsets.
  - `getSehriIftar(string $districtName, ?string $date = null)`: returns Sehri and Iftar times with Dua texts.
- `App\Services\ZakatService`:
  - `calculateZakat(float $cash, float $goldGrams, float $silverGrams, float $businessAssets, float $liabilities, float $goldPricePerGram, float $silverPricePerGram)`: returns Nisab threshold, zakat eligibility boolean, net wealth, and payable Zakat (2.5%).

### Public Portal ↔ QR Lesson Gateway
- `/scan`: In-browser QR camera scanner with manual code/page fallback.
- `/scan?code={code}` or `/lesson/{id}`: Resolves to `App\Controllers\QrLessonController::view($code)`.
- If code matches printed book page, renders distraction-free 16:9 responsive video player with Tajweed badge guide.

## Code Layout
```
c:\xampp\htdocs\Kariana Website\
├── app/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── CourseController.php
│   │   ├── BookController.php
│   │   ├── BlogController.php
│   │   ├── IslamicUtilityController.php
│   │   ├── PageController.php
│   │   ├── QrLessonController.php
│   │   └── Admin/
│   │       ├── AuthController.php
│   │       ├── DashboardController.php
│   │       ├── AdminBlogController.php
│   │       ├── AdminCourseController.php
│   │       ├── AdminAdmissionController.php
│   │       ├── AdminBookController.php
│   │       ├── AdminPageController.php
│   │       └── AdminQrController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Course.php
│   │   ├── Admission.php
│   │   ├── Book.php
│   │   ├── Post.php
│   │   ├── Category.php
│   │   ├── Page.php
│   │   └── QrLesson.php
│   ├── Services/
│   │   ├── PrayerTimeService.php
│   │   ├── ZakatService.php
│   │   └── SeoService.php
│   └── Views/
│       ├── layouts/
│       │   ├── main.php
│       │   ├── admin.php
│       │   └── quran_bridge.php
│       ├── home/
│       ├── courses/
│       ├── books/
│       ├── blog/
│       ├── utilities/
│       ├── pages/
│       ├── qr/
│       └── admin/
├── core/
│   ├── Autoloader.php
│   ├── App.php
│   ├── Request.php
│   ├── Response.php
│   ├── Router.php
│   ├── Controller.php
│   ├── Model.php
│   ├── View.php
│   ├── Database.php
│   ├── Session.php
│   ├── Csrf.php
│   └── BengaliHelper.php
├── config/
│   ├── app.php
│   ├── database.php
│   └── districts.php
├── database/
│   ├── schema.sql
│   ├── migrate.php
│   └── seed.php
├── public/ (and root redirect)
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── fonts/
├── .htaccess
├── index.php
├── router.php
└── tests/
    └── e2e/
```
