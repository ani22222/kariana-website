# Milestone M1 Remediation Changes Log

**Agent:** Worker M1 Gen 2 (`worker_m1_gen2`)  
**Date:** 2026-09-22T21:05:40+06:00  
**Target:** Milestone M1 Remediation (Kariana Quran Portal & CMS)

---

## 1. Relocation of Verification Test Suite & Layout Compliance
- **File Created / Updated:** `tests/unit/test_m1.php`
  - Fully standalone M1 verification suite with direct CLI execution (`php_sapi_name() === 'cli'`) and API execution (`runM1Verification()`).
  - Covers 10 distinct verification dimensions:
    1. Autoloader registration and presence of all 11 Core framework classes.
    2. Bengali numeral two-way conversion (`0-9` <-> `০-৯`).
    3. Comprehensive 9-step slug algorithm: Standard Bengali, Bengali Dari (`।`) and Double Dari (`॥`) removal, Taka (`৳`) currency sign removal, delimiter word boundary preservation (`কুরআন/সুন্নাহ` -> `কুরআন-সুন্নাহ`), Arabic script preservation with Harakat/Tatweel stripping (`الْقُرْآن الْكَرِيم` -> `القرآن-الكريم`), and collision-resistant fallback slug generation (`item-...`).
    4. CSRF token generation (256-bit entropy) and mixed-type safe validation (rejects arrays, integers, null, tampered tokens without `TypeError`).
    5. MariaDB UTF8MB4 PDO connection confirmation.
    6. All 12 normalized relational database tables verified present in schema.
    7. All 64 districts seeded with verified IFB prayer offsets.
    8. Admin user credentials and bcrypt password verification.
    9. Model `$orderBy` parameter two-tier whitelisting neutralizing SQL injection (`SLEEP`, `DROP TABLE`, `UNION`, keyword stuffing).
    10. Router PCRE `/u` Unicode routing with Bengali slug and safe parameter reflection with union types.
    11. Font asset verification: `public/assets/fonts/AAR-SQ-003.ttf` exists with 682,112 bytes.
- **File Deleted:** `.agents/worker_m1/test_m1.php`
  - Deleted to ensure `.agents/` contains strictly metadata (`.md`) files. Confirmed 0 non-markdown files across all `.agents/` directories.

---

## 2. Front Controller (`index.php`) Hardening
- **Path Updated:** `index.php`
  - Updated `/api/verify_m1` route target to `__DIR__ . '/tests/unit/test_m1.php'`.
  - Added OPcache invalidation guard before loading test file.
  - Confirmed closure is cleanly closed with `});`.
  - Verified 0 occurrences of `.agents/` remain in production code or route handlers.

---

## 3. Linguistic & Unicode Slug Generation (`core/BengaliHelper.php`)
- **Path Updated:** `core/BengaliHelper.php`
  - Replaced `createSlug()` with the authoritative 9-step algorithm:
    1. Trim whitespace; generate fallback if empty.
    2. Strip Arabic Harakat (`\x{064B}-\x{065F}`), Tatweel (`\x{0640}`), Dagger Alif (`\x{0670}`), and Quranic recitation marks (`\x{06D6}-\x{06ED}`).
    3. Convert Dari (`।`), Double Dari (`॥`), Taka (`৳`), currency marks, Arabic punctuation, typographic quotes, and ASCII delimiters to spaces to preserve word boundaries.
    4. Filter characters strictly to `\p{Bengali}`, `\p{Arabic}`, `a-zA-Z0-9`, spaces, and hyphens.
    5. Convert spaces and underscores to hyphens.
    6. Collapse consecutive hyphens.
    7. Trim leading and trailing hyphens.
    8. Convert Latin characters to lowercase via `mb_strtolower(..., 'UTF-8')`.
    9. Generate collision-resistant unique fallback (`item-` + 8 hex chars) if slug collapses to empty string to prevent MariaDB unique constraint violations.

---

## 4. SQL Injection Neutralization in Model (`core/Model.php`)
- **Path Updated:** `core/Model.php`
  - Implemented two-tier whitelisting in `where()` and `all()`:
    - Tier 1: Global regex whitelisting `/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`.
    - Tier 2: Per-term structural validation `/^[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)?(?:\s+(?:ASC|DESC))?$/i`.
  - Throws `\InvalidArgumentException` upon detection of invalid tokens, subqueries, parentheses, semicolons, comments, or keyword stuffing.

---

## 5. Robust CSRF Typing (`core/Csrf.php`)
- **Path Updated:** `core/Csrf.php`
  - Updated `validate(mixed $token): bool` signature to accept `mixed $token`.
  - Added `!is_string($token) || empty($token)` validation guard, preventing fatal PHP 8.2 `TypeError` when an array (e.g. `csrf_token[]`) is submitted.

---

## 6. Safe Parameter Reflection in Router (`core/Router.php`)
- **Path Updated:** `core/Router.php`
  - Updated `buildArguments()` to inspect parameter types safely using `instanceof \ReflectionNamedType` and `instanceof \ReflectionUnionType`.
  - Avoids calling `getName()` on non-named reflection types.
  - Added recognition for parameter names `$req` and `$request` alongside `\Core\Request` type hint.

---

## 7. CLI Built-in Web Server Router Security (`router.php`)
- **Path Updated:** `router.php`
  - Completely eliminated direct file serving from project root (Check 1).
  - Added directory traversal defense (rejects `..` and `\`).
  - Added hidden dotfile and dotdirectory blocking (rejects `.*` segments).
  - Added protected directories blocking (`app/`, `config/`, `core/`, `database/`, `storage/`, `tests/`, `.agents/`, `.git/`, `vendor/`).
  - Added sensitive extension blacklist (`.sql`, `.md`, `.json`, `.env`, `.log`, `.ini`, etc.).
  - Restricted static asset delivery exclusively to files inside `public/` matching an approved MIME whitelist.

---

## 8. Apache Environment Hardening (`.htaccess`)
- **Path Updated:** `.htaccess`
  - Protected `tests/` and `.git/` in rewrite rules: `RewriteRule ^(app|config|core|database|storage|tests|\.agents|\.git)/ - [F,L,NC]`.
  - Added portable dual-path asset rewrite supporting both subfolder installations (e.g., `http://localhost/Kariana%20Website/assets/...`) and root domain installations:
    `RewriteCond public/$1 -f [OR]`
    `RewriteCond %{DOCUMENT_ROOT}/public/$1 -f`
