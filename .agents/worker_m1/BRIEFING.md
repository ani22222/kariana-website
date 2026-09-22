# BRIEFING — 2026-09-22T20:23:00+06:00

## Mission
Deliver Milestone M1 for Kariana Quran Islamic Educational Portal & CMS: pure PHP 8.2 MVC framework (core/), configuration (config/), database schema, migration & seed scripts (database/), project root entrypoints (.htaccess, index.php, router.php), and master layout with Royal Islamic theme & font assets (app/Views/layouts/main.php and public/).

## 🔒 My Identity
- Archetype: implementer
- Roles: implementer, qa, specialist
- Working directory: c:\xampp\htdocs\Kariana Website\.agents\worker_m1
- Original parent: 5a011e50-ed48-4482-b181-5ca5e13d7062
- Milestone: M1 - Core Framework, Database & Router

## 🔒 Key Constraints
- Pure PHP 8.2 without runtime Composer/vendor bloat (100% Shared Hosting and XAMPP ready).
- Dedicated Port 8015 with binding to 0.0.0.0 (Local: http://localhost:8015, Wi-Fi: http://192.168.0.100:8015).
- Write ownership strictly limited to: core/*, config/*, database/*, .htaccess, index.php, router.php, public/*, app/Views/layouts/main.php, .agents/worker_m1/*.
- DO NOT write to tests/ or other agent folders.
- Integrity Mandate: No dummy implementations, real state and real database operations.
- Full UTF8MB4 charset support with PCRE Unicode /u regex for clean Bengali slugs.

## Current Parent
- Conversation ID: 5a011e50-ed48-4482-b181-5ca5e13d7062
- Updated: 2026-09-22T20:23:00+06:00

## Task Summary
- **What to build**:
  1. Core MVC framework under `core/`: Autoloader, App, Request, Response, Router, Controller, Model, View, Database, Session, Csrf, BengaliHelper.
  2. Configuration under `config/`: app.php, database.php, districts.php (all 64 districts with IFB offsets).
  3. Database under `database/`: schema.sql (12 normalized tables), migrate.php, seed.php.
  4. Root entrypoints: index.php, .htaccess, router.php.
  5. Layout & Base Styling: app/Views/layouts/main.php, copy AAR-SQ-003.ttf to public/assets/fonts/, setup public/assets/css/ and public/assets/js/.
- **Success criteria**:
  - `php database/migrate.php` and `php database/seed.php` execute flawlessly against MariaDB.
  - Verification test passes for Autoloader, Router, BengaliHelper, Database queries, and Csrf token generation.
  - changes.md and handoff.md populated with verbatim commands and outputs.
- **Interface contracts**: PROJECT.md § Interface Contracts, spec.md § 4 & 5.
- **Code layout**: PROJECT.md § Code Layout.

## Key Decisions Made
- Implemented zero-dependency PSR-4 autoloader mapping `Core\` and `App\`.
- Engineered dynamic base URL detection supporting root, dedicated port 8015, and subfolder `/Kariana Website/`.
- Router uses PCRE `/u` regex allowing full Bengali Unicode paths like `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`.
- Database singleton enforces `utf8mb4` and `utf8mb4_unicode_ci` with prepared statements.
- Built master layout in `app/Views/layouts/main.php` with Royal Islamic Emerald Green `#064e3b` / `#047857` and Warm Quranic Gold `#d97706`, Tailwind CSS, Alpine.js, Google Fonts, prayer ticker, and footer.
- Copied authentic Kariana Quranic Arabic font (`AAR-SQ-003.ttf`, 682,112 bytes) to `public/assets/fonts/` with `@font-face` configured.

## Artifact Index
- `.agents/worker_m1/DISPATCH.md` — Assignment requirements
- `.agents/worker_m1/BRIEFING.md` — Situational awareness
- `.agents/worker_m1/progress.md` — Step-by-step progress heartbeat
- `.agents/worker_m1/changes.md` — Detailed file changes log
- `.agents/worker_m1/test_m1.php` — Automated verification test suite
- `.agents/worker_m1/handoff.md` — Final 5-component handoff report

## Change Tracker
- **Files modified**: All M1 deliverables created across core/, config/, database/, public/, views/, and root.
- **Build status**: PASS (10/10 automated tests passed via `/api/verify_m1`).
- **Pending issues**: None.

## Quality Status
- **Build/test result**: 100% Pass (All 10 tests green).
- **Lint status**: Clean PHP 8.2 syntax.
- **Tests added/modified**: `test_m1.php` verifying autoloader, numeral conversion, Bengali slug preservation, CSRF, database tables, 64 districts count, bcrypt admin auth, router PCRE `/u`, and font file size.

## Loaded Skills
- None required for this milestone.
