## 2026-09-22T14:11:07Z
You are Worker M1 for the Kariana Quran Islamic Educational Portal & CMS.
Your working directory is c:\xampp\htdocs\Kariana Website\.agents\worker_m1. Write all your reports, logs, and handoff to this directory.

Read the authoritative requirements first:
c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
Also read:
- Master Plan: c:\xampp\htdocs\Kariana Website\.agents\orchestrator_1\PROJECT.md
- Architectural Specifications: c:\xampp\htdocs\Kariana Website\.agents\explorer_2\analysis.md
- Database Schema & Specifications: c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\spec.md
- Environment Details: c:\xampp\htdocs\Kariana Website\.agents\explorer_1\analysis.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Scope and Write Ownership:
You own exclusively:
- core/*
- config/*
- database/*
- .htaccess
- index.php
- router.php
- public/*
- app/Views/layouts/main.php
Do NOT write to tests/ or other agent folders.

Implementation Deliverables:
1. Core MVC Framework under core/:
   - Autoloader.php (PSR-4 zero-vendor native autoloader)
   - App.php (Front Controller bootstrap, exception handling)
   - Request.php (HTTP method, sanitized body, query params, dynamic getBaseUrl() detecting subdirectories or port 8015)
   - Response.php (json, redirect, setHeader, status codes)
   - Router.php (PCRE /u regex routing supporting clean UTF-8 Bengali slugs like /blog/সহজ-পদ্ধতিতে-কুরআন-শেখা)
   - Controller.php (render helper, json helper, model loader)
   - Model.php (PDO base model with find, findBy, where, create, update, delete)
   - View.php (view template rendering with layout support)
   - Database.php (PDO singleton, charset=utf8mb4, COLLATE utf8mb4_unicode_ci, prepared statements)
   - Session.php (session start, flash messages, session fixation protection, auth state)
   - Csrf.php (cryptographic token generation via random_bytes, validation via hash_equals)
   - BengaliHelper.php (toBengaliNumber, toEnglishNumber, createSlug preserving Bengali characters)
2. Configuration under config/:
   - app.php (app name 'কারিয়ানা কুরআন', port 8015, default timezone Asia/Dhaka)
   - database.php (host localhost:3306, dbname kariana_portal, user root, pass empty)
   - districts.php (complete array of 64 districts in Bangladesh with Bengali & English names, division, latitude, longitude, and IFB prayer minute offsets from Dhaka baseline)
3. Database Setup under database/:
   - schema.sql (12 normalized tables with UTF8MB4 charset: users, categories, posts, courses, admissions, books, pages, qr_lessons, prayer_districts, zakat_settings, site_settings)
   - migrate.php (script that connects to MySQL, executes CREATE DATABASE IF NOT EXISTS kariana_portal, and imports schema.sql)
   - seed.php (seeds admin user 'admin'/'admin123' with password_hash, initial categories, initial courses, initial books, sample blog post, 64 districts seed, site settings)
4. Project Root Entrypoints:
   - index.php (Front Controller resolving request and dispatching via Router)
   - .htaccess (Apache mod_rewrite rules for shared hosting / XAMPP)
   - router.php (CLI server router for dedicated port 8015: checks if requested file exists, otherwise rewrites to index.php)
5. Layout & Base Styling:
   - app/Views/layouts/main.php (Royal Islamic Emerald Green #064e3b/#047857 and Warm Quranic Gold #d97706, Tailwind CSS, Alpine.js, Google Fonts Hind Siliguri + Amiri, header navigation, responsive mobile menu, prayer ticker bar, footer)
   - Copy or link verified Kariana Arabic font (AAR-SQ-003.ttf) from adjacent workspace c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Font\ into public/assets/fonts/ and define @font-face in CSS.

Verification Requirement:
- Run php database/migrate.php and php database/seed.php via run_command to verify database setup succeeds without errors.
- Run a verification test verifying autoloader, routing, database query, and CSRF token generation.
- Document all created files, commands, and verification outputs in c:\xampp\htdocs\Kariana Website\.agents\worker_m1\changes.md and handoff.md.
- Send a completion message to the orchestrator when finished.
