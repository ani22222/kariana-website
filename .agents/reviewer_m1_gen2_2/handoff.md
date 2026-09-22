# Handoff Report — Reviewer M1 Gen2 #2

**Agent:** Reviewer M1 Gen 2 #2 (`reviewer`, `critic`)  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\reviewer_m1_gen2_2\`  
**Milestone:** M1 Gen2 Remediation Review  
**Date:** 2026-09-22T21:14:00+06:00  
**Handoff Type:** Hard Handoff  
**Verdict:** **REQUEST_CHANGES**

---

## 1. Observation

### 1.1 Direct Observation of Unit Test Verification Suite (`tests/unit/test_m1.php`)
- Requested `http://localhost/Kariana%20Website/api/verify_m1` (which invokes `tests/unit/test_m1.php` directly via front controller):
- Verbatim JSON response:
  ```json
  {
      "success": true,
      "timestamp": "2026-09-22 21:09:26",
      "results": [
          {
              "test": "Autoloader: App and Core classes exist",
              "passed": true,
              "details": "All Core framework classes loaded successfully via PSR-4 autoloader."
          },
          {
              "test": "BengaliHelper: Numeral Conversion 0-9 <-> ০-৯",
              "passed": true,
              "details": "Expected '২০২৬' and '2026', got '২০২৬' and '2026'"
          },
          {
              "test": "BengaliHelper: 9-step slug algorithm handles Dari, Taka, delimiters, Arabic, and fallbacks",
              "passed": true,
              "details": "Standard: 'কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা', Dari: 'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা', DoubleDari: 'প্রথম-অধ্যায়-সমাপ্ত-দ্বিতীয়-অধ্যায়', Taka: 'অনলাইন-কোর্স-ফি-৫০০', Slash: 'কুরআন-সুন্নাহ', Arabic: 'القرآن-الكريم', Fallback: 'item-3ed83293'"
          },
          {
              "test": "CSRF: Cryptographic token generation and mixed-type safe validation",
              "passed": true,
              "details": "Token length: 64, valid check: true, array safe: true"
          },
          {
              "test": "Database: PDO singleton connected with UTF8MB4 charset",
              "passed": true,
              "details": "Connection charset: utf8mb4"
          },
          {
              "test": "Database: 12 normalized tables present",
              "passed": true,
              "details": "All 12 tables present: users, categories, posts, courses, admissions, books, pages, qr_lessons, prayer_districts, zakat_settings, site_settings, migrations"
          },
          {
              "test": "Database: 64 districts seeded with IFB prayer offsets",
              "passed": true,
              "details": "District count: 64 / 64"
          },
          {
              "test": "Database: Admin user seeded and bcrypt password verified",
              "passed": true,
              "details": "Admin user found with email admin@karianaquran.com, role admin, bcrypt verified: YES"
          },
          {
              "test": "Model: OrderBy parameter whitelisting completely neutralizes SQL injection",
              "passed": true,
              "details": "Valid sort: PASS, Sleep blocked: YES, Drop blocked: YES, Union blocked: YES, Keyword stuffing blocked: YES"
          },
          {
              "test": "Router: PCRE /u Unicode routing with Bengali slug and safe parameter reflection",
              "passed": true,
              "details": "Captured slug: 'সহজ-পদ্ধতিতে-কুরআন-শেখা', Reflection tested: YES"
          },
          {
              "test": "Assets: Kariana Arabic font AAR-SQ-003.ttf deployed in public/assets/fonts/",
              "passed": true,
              "details": "Font path: C:\\xampp\\htdocs\\Kariana Website/public/assets/fonts/AAR-SQ-003.ttf, size: 682112 bytes (Expected: 682112 bytes)"
          }
      ]
  }
  ```
- All 11 assertions evaluated to `true`.

### 1.2 Direct Observation of Apache Server & `.htaccess` Security (Port 80)
- Tested sensitive file access via Apache:
  - `http://localhost/Kariana%20Website/database/schema.sql` $\implies$ Returned `HTTP 403 Forbidden`.
  - `http://localhost/Kariana%20Website/tests/unit/test_m1.php` $\implies$ Returned `HTTP 403 Forbidden`.
  - `http://localhost/Kariana%20Website/config/database.php` $\implies$ Returned `HTTP 403 Forbidden`.
  - `http://localhost/Kariana%20Website/.env` $\implies$ Returned `HTTP 403 Forbidden`.
  - `http://localhost/Kariana%20Website/.agents/orchestrator_gen2/PROJECT.md` $\implies$ Returned `HTTP 403 Forbidden`.
  - `http://localhost/Kariana%20Website/core/Database.php` $\implies$ Returned `HTTP 403 Forbidden`.
  - Path traversal `http://localhost/Kariana%20Website/assets/../config/database.php` $\implies$ Returned `HTTP 403 Forbidden`.
- Tested static asset delivery on Apache:
  - `http://localhost/Kariana%20Website/assets/css/main.css` $\implies$ Returned `HTTP 200 OK` (3,254 bytes) with correct CSS, `@font-face` rules for `AAR-SQ-003.ttf`, and custom properties.
- Tested Bengali Unicode slug routing:
  - `http://localhost/Kariana%20Website/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা` $\implies$ Returned `HTTP 200 OK` with article title: `"সহজ পদ্ধতিতে কুরআন তিলাওয়াত ও ক্বারীয়ানা ১২টি সংকেতের ভূমিকা | কারিয়ানা কুরআন"`.

### 1.3 Direct Observation of `.htaccess` Portability Logic
- `.htaccess` lines 43–56:
  ```apache
  # Flexible Asset Rewrite for both Subfolder (XAMPP) and Root Domain (cPanel/Hostinger)
  RewriteCond %{REQUEST_FILENAME} -f [OR]
  RewriteCond public/$1 -f [OR]
  RewriteCond %{DOCUMENT_ROOT}/public/$1 -f [OR]
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^assets/(.*)$ public/assets/$1 [L,NC]

  # Also check inside public/ directory if requested without /public/ prefix
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_URI} !public/ [NC]
  RewriteCond public/$1 -f [OR]
  RewriteCond %{DOCUMENT_ROOT}/public/$1 -f
  RewriteRule ^(.*)$ public/$1 [L]
  ```
- In subfolder installations, `public/$1 -f` evaluates relative to the directory containing `.htaccess`, resolving to `C:/xampp/htdocs/Kariana Website/public/assets/...` and succeeding.
- In root domain installations, `%{DOCUMENT_ROOT}/public/$1 -f` resolves to `/public/...` and succeeds.

### 1.4 Direct Observation of CSRF Type Handling (`core/Csrf.php`)
- `core/Csrf.php` lines 39–47:
  ```php
  public static function validate(mixed $token): bool
  {
      if (!is_string($token) || empty($token)) {
          return false;
      }

      $sessionToken = self::token();
      return hash_equals($sessionToken, $token);
  }
  ```
- Method signature explicitly specifies `mixed $token`.
- Guard `!is_string($token) || empty($token)` executes before `hash_equals()`, guaranteeing that `array`, `int`, `float`, `null`, `object`, or `bool` arguments return `false` without throwing PHP 8.2 `TypeError`.

### 1.5 Direct Observation of Dedicated Port 8015 Active Server Vulnerability
- Requested `http://localhost:8015/database/schema.sql` via HTTP GET:
  - **Result:** Returned `HTTP 200 OK` and dumped lines 1 to 232 of `database/schema.sql` containing the full database schema.
- Requested `http://localhost:8015/database/seed.php` via HTTP GET:
  - **Result:** Returned `HTTP 200 OK` and verbatim output:
    ```
    === [কারিয়ানা কুরআন] ডাটাবেজ সিডার শুরু ===
    ১. প্রশাসক ব্যবহারকারী সিড করা হচ্ছে...
    ✓ প্রশাসক একাউন্ট তৈরি: ইউজার 'admin' / পাসওয়ার্ড 'admin123'
    ...
    === সিডার সফলভাবে সমাপ্ত হয়েছে! ===
    ```
    The database seeder executed anonymously over the network and reseeded the database!
- Contrast with `router.php` source code on disk (`router.php:34-51`):
  ```php
  $protectedDirs = ['app', 'config', 'core', 'database', 'storage', 'tests', '.agents', '.git', 'vendor'];
  $firstSegment = strtolower($segments[0] ?? '');
  if (in_array($firstSegment, $protectedDirs, true)) {
      http_response_code(403);
      header('Content-Type: text/plain; charset=UTF-8');
      echo '403 Forbidden: Direct access to internal application directories is denied.';
      exit;
  }
  ```
- The code on disk in `router.php` contains the correct 403 blocking rules, but the background process currently listening on port 8015 is a stale process from Gen 1 that was never killed and restarted with the new `router.php`.

### 1.6 Direct Observation of Layout Compliance
- Inspected `.agents/` directory: Exactly 0 non-markdown files found.
- Grep search for `.agents/` across production code (`index.php`, `core/`, `app/`, `config/`): Exactly 0 references found (only present as protection pattern in `router.php`).

---

## 2. Logic Chain

1. **Unit Test & Core Quality**:
   - Observations 1.1, 1.4, and 1.6 prove that all unit tests pass, the autoloader operates cleanly, the 9-step Bengali slug algorithm handles edge cases, SQL injection is neutralized in `core/Model.php`, and `.agents/` contains zero non-metadata files.
2. **Apache Security & Portability**:
   - Observations 1.2 and 1.3 prove that under Apache (`.htaccess`), all sensitive internal files (`.sql`, `.php` in `tests/`, `config/`, `database/`, `.env`, `.agents/`) are strictly denied with `HTTP 403 Forbidden`. Static assets resolve cleanly in subfolder mode via the dual condition `RewriteCond public/$1 -f [OR] RewriteCond %{DOCUMENT_ROOT}/public/$1 -f`.
3. **Dedicated Port 8015 Operational Security Gap**:
   - Observation 1.5 proves that the active server listening on `0.0.0.0:8015` serves `database/schema.sql` (200 OK) and allows anyone on localhost or LAN (`192.168.0.100:8015`) to trigger `database/seed.php` (200 OK).
   - This occurs because the background CLI process running on port 8015 was spawned prior to Worker M1 Gen 2 updating `router.php`, and PHP built-in server keeps running until explicitly killed and restarted.
   - Because port 8015 is the user-specified dedicated port required for multi-device access (`http://localhost:8015` and `http://192.168.0.100:8015`), leaving a live process that exposes raw database schema and seeder execution is an unacceptable security exposure.

---

## 3. Caveats

- The source code in `router.php` on disk has been properly modified and includes all required traversal and blocking rules. The failure is strictly at the runtime process lifecycle level (the daemon was not restarted).
- MariaDB is running on port 3306 and all database tables remain intact.

---

## 4. Conclusion

**Verdict: REQUEST_CHANGES**

### Findings

#### [Critical / Operational Security] Finding 1: Dedicated Port 8015 Server Daemon Leaks `schema.sql` and Executes `seed.php`
- **What:** The active background server on port 8015 returns `database/schema.sql` (HTTP 200 OK) and executes `database/seed.php` (HTTP 200 OK) when accessed over HTTP.
- **Where:** Active background process on `0.0.0.0:8015`.
- **Why:** The process running on port 8015 is a stale process from Gen 1 that was never killed and restarted after `router.php` was hardened.
- **Required Action / Fix Direction:**
  1. Kill the existing background process listening on port 8015.
  2. Launch a fresh server instance using:
     ```powershell
     php -S 0.0.0.0:8015 router.php
     ```
  3. Verify that `http://localhost:8015/database/schema.sql` returns `403 Forbidden` and `http://localhost:8015/database/seed.php` returns `403 Forbidden`.

Once the port 8015 process is restarted and confirmed to enforce 403 on sensitive endpoints, Milestone M1 can be fully approved.

---

## 5. Verification Method

To independently verify this report:

1. **Verify Apache Sensitive File Blocking**:
   ```bash
   curl -i http://localhost/Kariana%20Website/database/schema.sql
   curl -i http://localhost/Kariana%20Website/tests/unit/test_m1.php
   curl -i http://localhost/Kariana%20Website/config/database.php
   ```
   *Expected Result*: `HTTP/1.1 403 Forbidden` for all three.

2. **Verify Stale Port 8015 Exposure**:
   ```bash
   curl -i http://localhost:8015/database/schema.sql
   ```
   *Current Result*: `HTTP/1.1 200 OK` (Leaks schema - FAILS until process restarted).  
   *Target Result after Restart*: `HTTP/1.1 403 Forbidden`.

3. **Verify Remediation by Restarting Port 8015 Daemon**:
   ```powershell
   # Kill port 8015 process
   Get-NetTCPConnection -LocalPort 8015 -ErrorAction SilentlyContinue | ForEach-Object { Stop-Process -Id $_.OwningProcess -Force }

   # Start fresh server with hardened router.php
   Start-Process -NoNewWindow php -ArgumentList "-S 0.0.0.0:8015 router.php"
   ```
   Then re-test:
   ```bash
   curl -i http://localhost:8015/database/schema.sql
   curl -i http://localhost:8015/database/seed.php
   curl -i http://localhost:8015/api/verify_m1
   ```
   *Expected Result*: Both `schema.sql` and `seed.php` return `403 Forbidden`, while `api/verify_m1` returns `200 OK` with `"success": true`.
