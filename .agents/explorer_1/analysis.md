# Comprehensive Workspace, Environment, and Asset Analysis
**Project:** Kariana Quran (কারিয়ানা কুরআন) Islamic Educational Portal & CMS  
**Date:** 2026-09-22  
**Author:** Explorer 1  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\explorer_1\`  

---

## 1. Executive Summary

This survey provides a complete audit of the workspace environment, local XAMPP stack, system services, network ports, and high-value digital assets available for the development of the Kariana Quran Islamic Educational Portal & CMS.

### Key Takeaways:
1. **Workspace Status:** Greenfield workspace at `c:\xampp\htdocs\Kariana Website`. Contains only agent metadata (`.agents/`) and `ORIGINAL_REQUEST.md`. No legacy spaghetti code or conflicting configurations exist.
2. **PHP Runtime:** PHP **8.2.12** is fully configured in XAMPP (`C:\xampp\php`). Essential extensions for the project (`pdo_mysql`, `mysqli`, `mbstring`, `curl`, `fileinfo`, `zip`, `exif`) are confirmed enabled in `php.ini`.
3. **Database Service:** MariaDB **10.4.32** is **actively running** as process ID 16108 on port **3306**, listening on all interfaces. Default root access (`user: root`, `password: ''`) is immediately available.
4. **Web Server & Routing:** Apache 2.4.58 is configured with `mod_rewrite` enabled and `AllowOverride All` set across `C:/xampp/htdocs`. Full `.htaccess` URL rewriting for clean Bengali slugs and MVC front-controller routing works natively.
5. **Dedicated Port & Network:** Port **8015** is officially reserved for `Kariana Website` in `PROJECT_PORTS.md`. Multi-device Wi-Fi binding to `0.0.0.0` maps to `http://localhost:8015` and `http://192.168.0.100:8015`.
6. **Goldmine Asset Discovery:** An adjacent project at `c:\xampp\htdocs\Kariana Quran ReMakiking In In design` provides the official, verified custom Kariana Arabic font (`AAR-SQ-003.ttf`), authentic proof texts, Bengali meanings (`Bangla_Meaning_Ref_Pages.txt`, `Para-01_COMPOSE-READY.txt`), and high-resolution offset scans.

---

## 2. Workspace Directory Survey

### 2.1 Target Workspace: `c:\xampp\htdocs\Kariana Website`
- **File Structure:**
  ```
  c:\xampp\htdocs\Kariana Website/
  ├── .agents/
  │   ├── explorer_1/        # Active working folder
  │   ├── explorer_2/
  │   ├── orchestrator_1/
  │   ├── sentinel/
  │   └── spec_miner_1/
  └── ORIGINAL_REQUEST.md    # 4,651 bytes (Complete client specification)
  ```
- **Analysis:**
  - The repository root is pristine.
  - Ready for clean MVC architecture implementation (`app/`, `public/`, `resources/`, `config/`, `database/`).
  - Strict separation of agent metadata (`.agents/`) from production source code is maintained.

---

## 3. Server & Runtime Environment Audit

### 3.1 XAMPP Installation Overview
- **Base Directory:** `C:\xampp\`
- **Distribution:** XAMPP 8.2.12-0 (Windows x64) — verified from `C:\xampp\properties.ini`
- **Apache Directory:** `C:\xampp\apache\`
- **MySQL Directory:** `C:\xampp\mysql\`
- **PHP Directory:** `C:\xampp\php\`

### 3.2 PHP 8.2 Configuration Details
- **Binary Path:** `C:\xampp\php\php.exe`
- **Configuration File:** `C:\xampp\php\php.ini` (2,021 lines, 76,827 bytes)
- **Extension Status (`php.ini` line inspection):**
  | Extension | Status | Verification Reference | Role in Kariana Portal |
  |---|---|---|---|
  | `pdo_mysql` | **Enabled** | Line 944: `extension=pdo_mysql` | Primary database driver for PDO MVC |
  | `mysqli` | **Enabled** | Line 938: `extension=mysqli` | Fallback database utility |
  | `mbstring` | **Enabled** | Line 936: `extension=mbstring` | Multi-byte Bengali & Arabic text processing |
  | `exif` | **Enabled** | Line 937: `extension=exif` | Image metadata handling for uploads |
  | `fileinfo` | **Enabled** | Line 930: `extension=fileinfo` | MIME detection for book & PDF uploads |
  | `curl` | **Enabled** | Line 927: `extension=curl` | External API fetching (Prayer times, Video URLs) |
  | `zip` | **Enabled** | Line 962: `extension=zip` | Backup, export, and resource archiving |
  | `bz2` | **Enabled** | Line 920: `extension=bz2` | Compression library |
  | `pdo_sqlite` | **Enabled** | Line 948: `extension=pdo_sqlite` | Secondary/testing database driver |
  | `gd` | Commented | Line 931: `;extension=gd` | Notice: Can be enabled if server-side thumbnailing is needed; alternatively CSS/HTML responsive scaling or dynamic SVG can be used |
  | `openssl` | Present | `C:\xampp\php\ext\php_openssl.dll` exists | Secure hashing and token generation |

### 3.3 MariaDB / MySQL Database Service
- **Engine:** MariaDB 10.4.32-MariaDB
- **Service Process ID:** `16108` (confirmed via `C:\xampp\mysql\data\mysql.pid`)
- **Activity Log:** `C:\xampp\mysql\data\mysql_error.log` confirms startup on **2026-09-22 17:19:19**:
  ```
  2026-09-22 17:19:19 0 [Note] Starting MariaDB 10.4.32-MariaDB ... as process 16108
  2026-09-22 17:19:20 0 [Note] Server socket created on IP: '::'.
  ```
- **Port:** `3306` (Listening on all interfaces `::`)
- **Default Credentials:**
  - Host: `localhost` / `127.0.0.1`
  - Port: `3306`
  - User: `root`
  - Password: `""` (empty)
- **Database Catalog (`C:\xampp\mysql\data\`):**
  - Existing DBs: `code_codecraftapi`, `digishop`, `iqos`, `mysql`, `performance_schema`, `phpmyadmin`, `test`
  - Recommended new database name: `kariana_portal` (or `kariana_quran`) — completely free with zero name collisions.

### 3.4 Apache Web Server & Routing Support
- **Ports:** 80 (HTTP), 443 (HTTPS)
- **DocumentRoot:** `C:/xampp/htdocs`
- **Rewrite Module:** `mod_rewrite.so` is **loaded** (Line 163 in `C:\xampp\apache\conf\httpd.conf`):
  ```apache
  LoadModule rewrite_module modules/mod_rewrite.so
  ```
- **AllowOverride Configuration:** (Line 273 in `C:\xampp\apache\conf\httpd.conf`):
  ```apache
  <Directory "C:/xampp/htdocs">
      Options Indexes FollowSymLinks Includes ExecCGI
      AllowOverride All
      Require all granted
  </Directory>
  ```
- **Impact for Project:**
  - `.htaccess` files placed inside `c:\xampp\htdocs\Kariana Website` will execute with 100% fidelity.
  - Native support for clean Bengali slug routing (e.g. `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`, `/courses/নাজেরা-কোর্স`).
  - Front-controller URL forwarding to `public/index.php` works seamlessly in both XAMPP and shared hosting (cPanel/Hostinger).

---

## 4. Port Registry & Network Accessibility

### 4.1 Global Port Assignment
As registered in `C:\xampp\htdocs\PROJECT_PORTS.md` (Line 23):
- **Project Folder:** `Kariana Website`
- **Dedicated Port:** `8015`
- **Local Access URL:** `http://localhost:8015`
- **Wi-Fi Multi-Device Access URL:** `http://192.168.0.100:8015`
- **Network Interface Binding Rule:** When launching the PHP development server, it **MUST** bind to `0.0.0.0`:
  ```bash
  php -S 0.0.0.0:8015 -t public
  ```
- **Apache Direct Access Path:**
  Also accessible via standard Apache on port 80 at:
  - `http://localhost/Kariana%20Website/public/`
  - `http://192.168.0.100/Kariana%20Website/public/`

---

## 5. Adjacent Project Assets & Font Discovery

The directory `c:\xampp\htdocs\Kariana Quran ReMakiking In In design` contains authentic Kariana Quran project deliverables:

### 5.1 Custom Kariana Arabic Font
- **Font File:** `AAR-SQ-003.ttf` (682,112 bytes)
  - Location: `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Font\AAR-SQ-003.ttf`
  - Secondary Location: `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\DELIVERABLES\AAR-SQ-003.ttf`
- **Font Details:**
  - Customized OpenType font with specialized GPOS MarkToLigature 4-class architecture.
  - Supports 12 unique Kariana Tajweed & reading symbols:
    - **S1** (`U+08E4`): Made Asli (1 Alif Mad)
    - **S6** (`U+08F6`): Made Arid (Ayah-ending Mad)
    - **S7** (`U+065E`): Wajib Ghunnah (গ)
    - **S8** (`U+065B`): Qalqalah (প)
    - **S9** (`U+065D`): Tafkheem / Bold letter (Shapla symbol)
    - **S10** (`U+0659`): Seeti / Safeer (স)
    - **S11** (`U+065C`): Ikhfa (ৎ)
    - **S12** (`U+F00C` / `U+08F3`): Arid Sakin inside circle (০)
- **Application in Kariana Website:**
  - This font can be copied into `public/assets/fonts/` or referenced directly for the Quran Reader Launchpad & Bridge (R3), rendering pure, authentic Kariana Quran script.

### 5.2 Content & Proof Text Deliverables
- `DELIVERABLES\Bangla_Meaning_Ref_Pages.txt` (17,985 bytes): Bengali translation keyed by reference pages.
- `DELIVERABLES\Bangla_Para01_Paged_Ready.txt` (76,493 bytes): Page-by-page Bengali meanings.
- `DELIVERABLES\Para-01_COMPOSE-READY.txt` (53,435 bytes): Fully composed Arabic text with precise diacritics and Tajweed marks.
- `DELIVERABLES\REF-PAGES-CHECK-REPORT.html` (5.88 MB): Visual alignment report.

### 5.3 High-Resolution Scans & Visual Media
- **Folder:** `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Ref-Pages\`
  - `offset-01.jpg` (1.16 MB) — High-res scan of Kariana Quran printed offset page 1.
  - `p04.jpg`, `p05.jpg`, `p07.jpg`, `p19.jpg`, `p27.jpg`, `v1.jpg`, `v2.jpg`, `v3.jpg` (Original scanned Quran pages).
- **Folder:** `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\pdf_images\`
  - Scanned and rendered proofs suitable for book preview banners and QR scan mockups.

---

## 6. Architecture & Shared Hosting Compatibility Assessment

### 6.1 Hosting Constraints & Specifications
- **Target Environments:**
  1. XAMPP on Windows (Current development environment on dedicated port 8015).
  2. Hostinger File Manager / cPanel `public_html` (Shared hosting production).
- **Strict Prohibition:**
  - **NO** Node.js background daemons or Express processes in production.
  - **NO** Docker or VPS root dependencies.
  - **NO** heavy framework bloat (avoid full-stack framework bloat that requires composer daemons or artisan queues).

### 6.2 Recommended Technical Stack
- **Architecture:** Pure PHP 8.2 MVC Architecture (Lightweight Router, Controllers, Models, PDO abstraction, Views).
- **Directory Layout:**
  ```
  Kariana Website/
  ├── app/
  │   ├── Controllers/    # HomeController, BlogController, CourseController, AdminController, ToolController, ScanController
  │   ├── Models/         # Article, Course, Admission, Book, Page, Setting, District
  │   ├── Core/           # Router, Database, View, Session, CSRF, SEOHelper, DistrictData
  │   └── Helpers/        # BengaliSlug, SchemaHelper, PrayerCalculator, ZakatCalculator
  ├── config/             # database.php, app.php
  ├── database/           # schema.sql, seeders.sql, migrations/
  ├── public/             # DocumentRoot for web serving
  │   ├── index.php       # Single front controller
  │   ├── .htaccess       # Apache rewrite rules for clean URLs
  │   └── assets/
  │       ├── css/        # Tailwind compiled / custom styles
  │       ├── js/         # Alpine.js, interactive tools (Tasbeeh, Zakat, Prayer)
  │       ├── fonts/      # AAR-SQ-003.ttf, Bengali web fonts
  │       └── images/     # Logos, banners, book covers
  ├── views/              # Semantic templates (header, footer, layouts, pages, admin)
  ├── .htaccess           # Root forwarding to public/ for zero-configuration shared hosting
  └── index.php           # Shared hosting root bridge
  ```

---

## 7. Next Steps & Recommendations for Orchestrator

1. **Database Initialization:** Run a pure SQL migration creating tables for:
   - `users` (admin authentication with `password_hash`)
   - `articles` (blog posts with Bengali slugs, categories, tags, SEO meta)
   - `courses` (Nazera, Hifz, Qirat, Tajweed)
   - `admissions` (student applications lead capture with status tracking)
   - `books` (Kariana publications catalog)
   - `pages` (dynamic customizable CMS pages)
   - `qr_codes` / `video_lessons` (Phase 2 QR scanner page-to-video mappings)
   - `settings` (site metadata, social links, contact info)
2. **Font Integration:** Copy `AAR-SQ-003.ttf` into `public/assets/fonts/` and configure `@font-face` in the design system.
3. **Dedicated Port Verification:** Ensure server commands launch with `php -S 0.0.0.0:8015 -t public` to satisfy multi-device Wi-Fi requirements.
