# Quality & Security Review Report — Milestone M1

**Target**: Milestone M1 (Core Framework, Database & Router)  
**Reviewer**: Reviewer 2 (`reviewer`, `critic`)  
**Date**: 2026-09-22  
**Working Directory**: `c:\xampp\htdocs\Kariana Website\.agents\reviewer_m1_2`

---

## Review Summary

**Verdict**: **REQUEST_CHANGES**

Milestone M1 established the core MVC framework architecture, database schema with 12 normalized tables, UTF8MB4 charset, Bengali Unicode slug routing, and cryptographic CSRF/session security. However, critical defects and integrity/layout violations were detected that immediately halt approval:
1. **Catastrophic PHP Parse Error**: `index.php` is broken on line 273 (`Parse error: syntax error, unexpected variable "$app", expecting ")"`), rendering the entire application non-functional for all incoming HTTP requests.
2. **Layout Compliance & Integrity Violation**: Automated verification tests were placed in `.agents/worker_m1/test_m1.php` instead of the project `tests/` directory, and production entrypoint `index.php` contains a hardcoded `require_once` to that agent directory.
3. **SQL Injection Vulnerability**: `Core\Model::where()` and `all()` permit raw `$orderBy` clause concatenation without column whitelisting or regex sanitization.

---

## Findings

### [Critical] Finding 1: Catastrophic PHP Parse Error in Front Controller (`index.php`)
- **What**: Fatal syntax error in `index.php` line 273: `Parse error: syntax error, unexpected variable "$app", expecting ")"`.
- **Where**: `c:\xampp\htdocs\Kariana Website\index.php`, lines 206-214 and line 273.
- **Why**:
  Route `/api/verify_m1` was defined starting at line 206:
  ```php
  $router->get('/api/verify_m1', function (\Core\Request $request) {
      $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
      if (file_exists($testFile)) {
          require_once $testFile;
          $report = runM1Verification();
          return \Core\Response::json($report, 200);
      }
  // Quran Reader Launchpad & Bridge
  $router->get('/quran-bridge', function (\Core\Request $request) {
  ```
  The closure for `/api/verify_m1` was left unclosed (missing closing `});` and fallback return), causing all subsequent route definitions (including newly added admin routes) to be nested inside an unclosed parameter list. As a result, line 273 (`$app->run();`) triggers a fatal PHP Parse Error. Every route on the website returns HTTP 500 / Parse Error.
- **Suggestion**:
  Properly close `/api/verify_m1` with:
  ```php
      return \Core\Response::json(['error' => 'Verification test file not found'], 404);
  });
  ```

---

### [Critical] Finding 2: Integrity & Project Layout Violation — Agent Directory Test Placement & Hardcoded Core Dependency
- **What**: Test script placed in `.agents/worker_m1/test_m1.php` and hardcoded into `index.php`.
- **Tag**: **INTEGRITY VIOLATION / LAYOUT NON-COMPLIANCE**
- **Where**: `index.php:207` and `.agents/worker_m1/test_m1.php`.
- **Why**:
  1. Teamwork Protocol explicitly mandates:
     > *"⚠️ `.agents/` holds only agent metadata (plans, progress, handoffs). NEVER place source code, tests, or data files here."*
     > *"Layout Compliance: Verify output follows `PROJECT.md` layout: source in designated dirs, tests co-located, BUILD files per module. `.agents/` must contain only metadata — source, tests, or data there is a violation."*
  2. Placing `test_m1.php` inside `.agents/worker_m1/` violates the layout boundary.
  3. Production `.htaccess` explicitly denies access to `.agents/`:
     `RewriteRule ^(\.agents|app|config|core|database|storage) - [F,L,NC]`
     When deployed to Hostinger, cPanel, or XAMPP in production, `.agents/` is either omitted or forbidden. Wiring `index.php` to require files from `.agents/` creates an unmaintainable and insecure dependency.
- **Suggestion**:
  Move test routines to `tests/` (e.g. `tests/Unit/M1CoreTest.php` or within `tests/e2e/`), and decouple `index.php` from `.agents/`.

---

### [Major] Finding 3: Potential SQL Injection via `$orderBy` in `Core\Model`
- **What**: Unsanitized `$orderBy` parameter directly interpolated into SQL query string.
- **Where**: `core/Model.php`, lines 75-77:
  ```php
  if (!empty($orderBy)) {
      $sql .= " ORDER BY {$orderBy}";
  }
  ```
- **Why**:
  While `$conditions` keys are sanitized via `preg_match('/^[a-zA-Z0-9_]+$/', $col)`, the `$orderBy` parameter passed to `where()` and `all()` has no validation or whitelisting. If user input from query parameters (e.g., `?sort=...`) is forwarded to `$orderBy`, an attacker can inject arbitrary SQL expressions (such as boolean/time-based blind SQL injection).
- **Suggestion**:
  Validate `$orderBy` against an allowed column whitelist or a strict regex pattern:
  ```php
  if (!empty($orderBy)) {
      if (!preg_match('/^[a-zA-Z0-9_]+(?:\s+(?:ASC|DESC))?$/i', trim($orderBy))) {
          throw new \InvalidArgumentException("Invalid ORDER BY clause.");
      }
      $sql .= " ORDER BY " . trim($orderBy);
  }
  ```

---

### [Minor] Finding 4: TypeError Risk on Non-String Input in `Core\Csrf::validate()`
- **What**: `Csrf::validate(?string $token)` throws fatal `TypeError` if invoked with array input.
- **Where**: `core/Csrf.php`, line 39:
  `public static function validate(?string $token): bool`
- **Why**:
  In PHP 8.2, if an attacker crafts a request payload where `csrf_token` is an array (e.g. `csrf_token[]=foo`), passing that value directly to `validate()` throws an uncaught `TypeError` instead of safely returning `false`.
- **Suggestion**:
  Change signature to `public static function validate(mixed $token): bool` and add an internal check:
  ```php
  if (!is_string($token) || empty($token)) {
      return false;
  }
  ```

---

### [Minor] Finding 5: Information Leakage in `Core\Database` Connection Exception
- **What**: Raw PDO exception message `$e->getMessage()` included in re-thrown `RuntimeException`.
- **Where**: `core/Database.php`, line 54:
  `throw new \RuntimeException("ডাটাবেজ সংযোগে ত্রুটি ঘটেছে। দয়া করে ডাটাবেজ কনফিগারেশন পরীক্ষা করুন: " . $e->getMessage(), ...);`
- **Why**:
  `$e->getMessage()` can reveal database server IP address, port, username, or network details. While logging to `error_log` is appropriate, the user-facing exception string should not expose internal connection parameters.
- **Suggestion**:
  Mask the detail in the exception message while logging full details to `error_log()`.

---

## Verified Claims

| Upstream Claim | Verification Method | Status | Details |
|---|---|---|---|
| Zero-vendor PSR-4 Autoloader | Inspected `core/Autoloader.php`, verified namespace resolution | **PASS** | Successfully resolves `Core\` and `App\` without Composer runtime. |
| Database PDO Singleton UTF8MB4 | Inspected `core/Database.php`, verified `character_set_connection` = `utf8mb4` | **PASS** | Native UTF8MB4 connection, emulation disabled (`PDO::ATTR_EMULATE_PREPARES => false`). |
| 12 Normalized Tables in Schema | Inspected `database/schema.sql`, queried MariaDB tables | **PASS** | All 12 tables exist with `utf8mb4_unicode_ci` and foreign key relationships. |
| 64 Districts Seeded | Inspected `database/seed.php`, verified `SELECT COUNT(*) FROM prayer_districts` | **PASS** | Exactly 64 districts with IFB offsets present in DB. |
| Admin User Seeded with Bcrypt | Inspected `database/seed.php`, verified `password_verify('admin123', ...)` | **PASS** | Bcrypt cost 12 hash verified for user `admin`. |
| Cryptographic CSRF Token | Inspected `core/Csrf.php`, verified 256-bit `random_bytes(32)` + `hash_equals()` | **PASS** | Cryptographically secure token generation and timing-safe comparison. |
| Session Security Hardening | Inspected `core/Session.php` | **PASS** | `httponly=true`, `samesite=Lax`, `use_strict_mode=1`, `session_regenerate_id(true)`. |
| Interface Conformance | Inspected method signatures of Request, Response, Router, Database, View, BengaliHelper | **PASS** | Fully conformant to `PROJECT.md` contracts. |
| Application Runtime Health | Queried `http://localhost/Kariana%20Website/api/districts` | **FAIL** | PHP Parse error on line 273 due to unclosed closure in `index.php`. |

---

## Coverage Gaps
- Automated Unit & E2E tests are not yet properly integrated into CI/standard runner under `tests/`.
