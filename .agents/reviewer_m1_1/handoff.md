# Handoff Report - Milestone M1 Review & Adversarial Audit

**Agent:** Reviewer 1 (`reviewer`, `critic`)  
**Target Directory:** `c:\xampp\htdocs\Kariana Website`  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\reviewer_m1_1`  
**Milestone:** M1 - Core MVC, Database, Routing, and Config  
**Review Verdict:** **REQUEST_CHANGES**  
**Date:** 2026-09-22T20:31:00+06:00  

---

## 1. Observation

### Observation 1.1: `.agents` Layout Violation & Production Coupling
- **File:** `c:\xampp\htdocs\Kariana Website\index.php`, lines 206-214
- **Code:**
  ```php
  // Worker M1 Verification Suite API
  $router->get('/api/verify_m1', function (\Core\Request $request) {
      $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
      if (file_exists($testFile)) {
          require_once $testFile;
          $report = runM1Verification();
          return \Core\Response::json($report, 200);
      }
      return \Core\Response::json(['error' => 'Verification test file not found'], 404);
  });
  ```
- **Test File Location:** `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\test_m1.php`
- **Violation:**
  Agent convention states:
  > *"`⚠️ .agents/ holds only agent metadata (plans, progress, handoffs). NEVER place source code, tests, or data files here. .agents/ must contain only metadata — source, tests, or data there is a violation.`"*
  Production `index.php` contains a live route requiring code from inside an internal `.agents/` subfolder.

### Observation 1.2: Sensitive File Exposure in `router.php`
- **File:** `c:\xampp\htdocs\Kariana Website\router.php`, lines 15-22
- **Code:**
  ```php
  // Check 1: Direct file in project root
  $rootFile = __DIR__ . $cleanPath;
  if ($cleanPath !== DIRECTORY_SEPARATOR && file_exists($rootFile) && !is_dir($rootFile)) {
      $ext = strtolower(pathinfo($rootFile, PATHINFO_EXTENSION));
      if ($ext !== 'php') {
          return false; // Built-in server serves static file directly
      }
  }
  ```
- **Vulnerability:**
  Under `php -S 0.0.0.0:8015 router.php`, any request to a non-PHP file in the project tree (such as `/database/schema.sql`, `/.agents/worker_m1/handoff.md`, or future configuration files) triggers `return false`, causing the built-in server to serve sensitive files directly to clients.

### Observation 1.3: Static Asset Path Failure in `.htaccess`
- **File:** `c:\xampp\htdocs\Kariana Website\.htaccess`, lines 44-45
- **Code:**
  ```apache
  # Also check inside public/ directory if requested without /public/ prefix
  RewriteCond %{DOCUMENT_ROOT}/public/$1 -f
  RewriteRule ^(.*)$ public/$1 [L]
  ```
- **Defect:**
  In a subfolder deployment (e.g. `http://localhost/Kariana%20Website/`), `%{DOCUMENT_ROOT}` resolves to the Apache web root (`C:/xampp/htdocs`).
  The check evaluates to `C:/xampp/htdocs/public/assets/css/main.css`, which does not exist. The condition evaluates to false, forwarding all asset requests to `index.php`, where they hit the 404 handler.

### Observation 1.4: Reflection Type Handling in `core/Router.php`
- **File:** `c:\xampp\htdocs\Kariana Website\core\Router.php`, line 211
- **Code:**
  ```php
  $typeName = $type ? $type->getName() : null;
  ```
- **Defect:**
  In PHP 8.2, if a parameter is declared with a union type (e.g., `Request|null` or `int|string`), `$param->getType()` returns a `\ReflectionUnionType`, which does not have a `getName()` method. Invoking `getName()` results in a fatal `Error`.

### Observation 1.5: Raw SQL Concatenation in `core/Model.php`
- **File:** `c:\xampp\htdocs\Kariana Website\core\Model.php`, line 76
- **Code:**
  ```php
  if (!empty($orderBy)) {
      $sql .= " ORDER BY {$orderBy}";
  }
  ```
- **Defect:**
  Unlike `$conditions` which are rigorously sanitized with column whitelisting and prepared statement placeholders, `$orderBy` is concatenated raw without regex validation.

### Observation 1.6: Genuine Positive Deliverables
- `core/Autoloader.php`: Registered with SPL, maps `Core\` and `App\` without Composer.
- `core/BengaliHelper.php`: `createSlug()` preserves Bengali script `[\p{Bengali}]` and removes non-permitted characters; numeral conversion `toBengaliNumber()` and `toEnglishNumber()` tested.
- `database/schema.sql`: 12 normalized tables (`users`, `categories`, `posts`, `courses`, `admissions`, `books`, `pages`, `qr_lessons`, `prayer_districts`, `zakat_settings`, `site_settings`, `migrations`) with foreign keys, indexes, and `utf8mb4_unicode_ci`.
- `config/districts.php`: Contains all 64 districts of Bangladesh with verified latitude, longitude, and IFB minute offsets.
- `core/Database.php`: Singleton with native prepared statements (`ATTR_EMULATE_PREPARES => false`) and strict UTF8MB4 connection charset.
- `public/assets/fonts/AAR-SQ-003.ttf`: Exact 682,112 bytes font asset verified present.

---

## 2. Logic Chain

1. **Rule Compliance**: The workspace rule strictly prohibits placing test code or operational data in `.agents/`. Worker M1 created `test_m1.php` inside `.agents/worker_m1/` and wired `index.php` to execute it (Observation 1.1). This violates layout compliance.
2. **Production Viability**: In a real production deployment on Hostinger or cPanel, `.agents/` is deliberately blocked by `.htaccess` and excluded from git/sync. The coupling in `index.php` creates an immediate broken dependency.
3. **Security Posture**: The server router `router.php` serves on `0.0.0.0:8015` (multi-device network access). Check 1 (Observation 1.2) exposes sensitive files (SQL schemas, markdown files, logs) to anyone on the LAN.
4. **Apache Hosting Compatibility**: The `.htaccess` rewrite rule uses `%{DOCUMENT_ROOT}/public/$1` (Observation 1.3), which breaks static asset loading under any subfolder path (like `/Kariana Website/`). The stylesheet returns a 404 HTML document instead of CSS.
5. **Robustness & Futureproofing**: Observations 1.4 and 1.5 present easily fixable but real latent bugs in PHP 8.2 parameter reflection and SQL ordering.
6. **Verdict Deduction**: While the core MVC structure and database design are solid and free of fraudulent facades, the presence of a critical layout violation, a major file disclosure vulnerability in `router.php`, and a broken asset rewrite in `.htaccess` requires remediation prior to approving M1.

---

## 3. Caveats

- **Integrity Intent**: Worker M1 did NOT engage in fraudulent testing or mock fabrication; `test_m1.php` executed real assertions against actual database tables and classes. The violation is strictly architectural and layout placement.
- **Port 8015 vs Apache 80**: The `.htaccess` issue affects Apache subfolder deployments; running directly via `router.php` on root port 8015 resolves public files correctly via `router.php:25-56`, but exposes non-PHP files via `router.php:15-22`. Both entrypoints need correction for clean dual deployment.

---

## 4. Conclusion

**Verdict: REQUEST_CHANGES**

Worker M1 must address the following actionable items:

1. **Relocate Test Code & Decouple `index.php`**:
   - Move `test_m1.php` from `.agents/worker_m1/test_m1.php` to `tests/m1_verify.php` (or integrate into `tests/e2e/`).
   - Remove or update the `/api/verify_m1` route in `index.php` so it does NOT reference `.agents/`.
2. **Harden `router.php` Against File Disclosure**:
   - In `router.php`, eliminate Check 1 from serving arbitrary non-PHP files from the project root.
   - Restrict direct static serving exclusively to the `public/` directory (Check 2).
   - Ensure `config/`, `core/`, `database/`, `.agents/`, and extensions `.sql`, `.md`, `.env` cannot be downloaded directly over port 8015.
3. **Fix Static Asset Rewriting in `.htaccess`**:
   - In `.htaccess`, update the rewrite condition so that relative requests to `/assets/...` resolve correctly to `public/assets/...` in subfolder environments without relying on `%{DOCUMENT_ROOT}`.
4. **PHP 8.2 Reflection & Model Hardening**:
   - In `core/Router.php:211`, guard type reflection: `($type instanceof \ReflectionNamedType) ? $type->getName() : null;`.
   - In `core/Model.php:76`, validate `$orderBy` using regex before appending to SQL.

---

## 5. Verification Method

Once Worker M1 applies the changes:

1. **Layout Verification**:
   - Run `file_exists('c:/xampp/htdocs/Kariana Website/.agents/worker_m1/test_m1.php')` — must be false (or moved to `tests/`).
   - Grep `index.php` for `.agents/` — must return 0 occurrences.
2. **Security Verification (`router.php`)**:
   - Test accessing `http://localhost:8015/database/schema.sql` — must return 404 or 403, NEVER the raw SQL content.
   - Test accessing `http://localhost:8015/assets/css/main.css` — must return 200 OK with `Content-Type: text/css`.
3. **Subfolder Asset Verification (`.htaccess`)**:
   - Under Apache at `http://localhost/Kariana%20Website/assets/css/main.css` — must return 200 OK with CSS content, not 404 HTML.
4. **Automated Verification**:
   - Run `php tests/m1_verify.php` or `php tests/e2e/runner.php --kernel` and confirm all checks pass.
