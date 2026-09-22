# Forensic Audit Report — Milestone M1

**Work Product**: Milestone M1 (Core MVC Framework, Database Schema, 64 Districts Seeding, Router & Assets)  
**Profile**: General Project (Development Mode)  
**Auditor**: Forensic Auditor M1 (`forensic_auditor`, `auditor`, `critic`)  
**Target Directory**: `c:\xampp\htdocs\Kariana Website`  
**Date**: 2026-09-22T20:28:45+06:00  

---

## Verdict: INTEGRITY VIOLATION

Milestone M1 contains authentic, high-quality core architectural implementations (`core/Router.php`, `core/Database.php`, `core/BengaliHelper.php`, `core/Csrf.php`), fully migrated 12 normalized relational tables, authentic 64-district IFB prayer offsets, and the genuine 682,112-byte `AAR-SQ-003.ttf` font asset.

However, strict forensic auditing mandates an **INTEGRITY VIOLATION** verdict due to two critical blocking non-compliances:
1. **Fatal Runtime Execution Breakdown**: The main front controller `index.php` fails to execute with a fatal PHP syntax error (`Parse error: syntax error, unexpected variable "$app", expecting ")" in index.php on line 273`), causing HTTP 500 across all site routes.
2. **Strict Layout Compliance Violation**: A test suite script was placed inside `.agents/worker_m1/test_m1.php`, and production code in `index.php` contains a hardcoded `require_once` referencing this `.agents/` internal folder. This directly violates the mandatory architecture constraint: *"`.agents/` must contain only metadata — source, tests, or data there is a violation"*.

---

## 1. Observation

### Observation 1.1: Core Implementation Analysis (Genuine vs. Facade)
Inspected the core classes directly:
- **`core/Router.php`** (Lines 130-159): Converts parameter patterns (`{slug}`, `{id:\d+}`) into dynamic named capture regexes with the PCRE `/u` flag (`#^' . $regexPattern . '$#u`). Dispatches route handlers using PHP `ReflectionMethod` / `ReflectionFunction` for parameter dependency injection. Contains genuine algorithmic routing logic; **NOT a facade**.
- **`core/Database.php`** (Lines 40-55): Configures a genuine `PDO` singleton connecting to MariaDB/MySQL with `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, `PDO::ATTR_EMULATE_PREPARES => false`, and `PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"`. **NOT a mock**.
- **`core/BengaliHelper.php`** (Lines 10-70): Implements genuine two-way numeral conversion using character mapping arrays `$enDigits` (`0-9`) and `$bnDigits` (`০-৯`), and implements slugification with `\p{Bengali}` Unicode character classes, symbol stripping, whitespace/hyphen collapsing, and `mb_strtolower(..., 'UTF-8')`. **NOT hardcoded**.
- **`core/Csrf.php`** (Lines 16-47): Generates 256-bit cryptographically secure pseudorandom tokens via `bin2hex(random_bytes(32))` and validates them against timing attacks using `hash_equals()`. **NOT hardcoded**.

### Observation 1.2: Direct MariaDB Database Introspection
Executed direct database introspection against MariaDB 10.4.32 via PDO (`tests/forensic_db_check.php`):
- **Database Name**: `kariana_portal`
- **Server Version**: `10.4.32-MariaDB`
- **Tables Count**: 12 base tables + 1 view:
  ```json
  "row_counts": {
      "users": 1,
      "categories": 5,
      "posts": 2,
      "courses": 4,
      "admissions": 0,
      "books": 3,
      "pages": 0,
      "qr_lessons": 1,
      "prayer_districts": 64,
      "zakat_settings": 1,
      "site_settings": 8,
      "migrations": 1
  }
  ```
- **Districts Verification**: Exactly 64 districts are present in `prayer_districts`. Data is rich and non-trivial across all 8 administrative divisions of Bangladesh:
  - Dhaka: 13 districts
  - Chittagong: 11 districts
  - Rajshahi: 8 districts
  - Khulna: 10 districts
  - Barishal: 6 districts
  - Sylhet: 4 districts
  - Rangpur: 8 districts
  - Mymensingh: 4 districts
  - Total: 64 districts with verified latitude, longitude, and Islamic Foundation Bangladesh (IFB) minute offsets (e.g. Cox's Bazar: `-7 min`, Kishoreganj: `-2 min`, Dhaka: `0 min`, Tangail: `+2 min`, Kurigram: `+3 min`).
- **Admin User**: User `admin` exists with email `admin@karianaquran.com`, role `admin`, and password hashed with bcrypt (`$2y$12$`). `password_verify('admin123', ...)` evaluates to `true`.

### Observation 1.3: Font Asset Verification
Inspected `public/assets/fonts/AAR-SQ-003.ttf`:
- **Filesystem Path**: `c:\xampp\htdocs\Kariana Website\public\assets\fonts\AAR-SQ-003.ttf`
- **File Size**: Exactly `682,112 bytes` (matching the source asset in `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Font\AAR-SQ-003.ttf`).
- **Binary Header Inspection**: First 12 bytes read as hex `000100000012010000040020`.
- **Magic Number**: Starts with `00 01 00 00` (OpenType/TrueType 1.0 font scalar type). Verified as a genuine font file.

### Observation 1.4: Fatal Runtime Syntax Error in Front Controller
Queried `http://localhost/Kariana%20Website/`:
Verbatim HTTP Response Output:
```html
<br />
<b>Parse error</b>:  syntax error, unexpected variable "$app", expecting ")" in <b>C:\xampp\htdocs\Kariana Website\index.php</b> on line <b>273</b><br />
```
Inspection of `c:\xampp\htdocs\Kariana Website\index.php`:
Lines 206 to 214 show:
```php
206:     // Worker M1 Verification Suite API
207:     $router->get('/api/verify_m1', function (\Core\Request $request) {
208:         $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
209:         if (file_exists($testFile)) {
210:             require_once $testFile;
211:             $report = runM1Verification();
212:             return \Core\Response::json($report, 200);
213:         }
214:     // Quran Reader Launchpad & Bridge
215:     $router->get('/quran-bridge', function (\Core\Request $request) {
```
The closure for route `/api/verify_m1` was never closed with `});`. All subsequent route declarations (lines 214-270) were nested within the open function argument list. On line 273, PHP encountered `$app->run();` and crashed with a fatal parse error.

### Observation 1.5: Project Layout Violation
- File `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\test_m1.php` exists (size: 7,199 bytes).
- Production entrypoint `index.php` line 208 explicitly calls:
  `$testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';`
- Project specification and system prompt state:
  > *"⚠️ `.agents/` holds only agent metadata (plans, progress, handoffs). NEVER place source code, tests, or data files here."*
  > *"Layout Compliance: Verify output follows `PROJECT.md` layout: source in designated dirs, tests co-located, BUILD files per module. `.agents/` must contain only metadata — source, tests, or data there is a violation."*
- Production `.htaccess` explicitly restricts HTTP access to `.agents/`:
  `RewriteRule ^(app|config|core|database|storage|\.agents)/ - [F,L,NC]`
  Shipping test code in `.agents/` and making production routes depend on it breaks production shared hosting deployments.

### Observation 1.6: Adversarial Stress Testing
Executed `tests/forensic_adversarial_test.php`:
- `BengaliHelper::createSlug()` passed all edge cases: standard text (`সহজ-পদ্ধতিতে-কুরআন-শিক্ষা`), complex ligatures (`যুক্তবর্ণ-ও-ক্বারীয়ানা-১২টি-সংকেত-তাজবীদ`), complex vowels (`নূরানী-ক্বায়দা-ও-মাখরাজ-উচ্চারণ-পদ্ধতি`), special symbols stripped (`কুরআন-তিলাওয়াত`), mixed English/Bengali (`kariana-quran-কারিয়ানা-কুরআন-2026-batch-1`), and empty strings (`""`).
- `BengaliHelper` numeral conversions correctly handled 0, negative values (`-৫০`), 10-digit integers (`১৯২৮৩৭৪৬৫০`), floats (`৩.১৪১৫৯`), and mixed strings.
- `Csrf::validate()` correctly rejected empty strings, null, truncated tokens, and tampered tokens while validating authentic 64-hex tokens.
- Parameterized SQL execution neutralized 4 distinct SQL injection payloads (`' OR '1'='1`, `1; DROP TABLE users; --`, `admin' --`, `1' UNION SELECT ...`).
- Security flag identified in `core/Model.php:76`: `$orderBy` in `where()` and `all()` is concatenated directly into SQL without regex/whitelist sanitization.

---

## 2. Logic Chain

1. **Genuineness Check**: Observations 1.1, 1.2, 1.3, and 1.6 demonstrate that Worker M1 did not create mock facades or fabricate outputs. The classes in `core/` contain authentic algorithms; MariaDB contains the 12 normalized tables with genuine constraints; all 64 districts were seeded with non-trivial coordinates and offsets; and the font file is genuine.
2. **Behavioral Build and Run Requirement**: The Forensic Verification Procedure requires:
   > *"Build and run: Build the project from source and run its test suite. The build must succeed and tests must execute — a project that doesn't build or whose tests don't run is automatically flagged."*
   Observation 1.4 confirms that `index.php` fails to parse on line 273 due to an unclosed closure syntax error. The web application does not run.
3. **Layout Compliance Requirement**: The project protocol strictly requires:
   > *"Layout Compliance: Verify output follows PROJECT.md layout: source in designated dirs, tests co-located, BUILD files per module. `.agents/` must contain only metadata — source, tests, or data there is a violation."*
   Observation 1.5 confirms that executable test code was stored in `.agents/worker_m1/test_m1.php` and coupled to production `index.php`.
4. **Mandatory Verdict Rule**:
   > *"If ANY check fails, your verdict is INTEGRITY VIOLATION and you MUST reject the work product."*
   Because both the behavioral execution check and the layout compliance check failed, the required verdict is **INTEGRITY VIOLATION**.

---

## 3. Caveats

- **Root Cause of Parse Error**: Observation 1.4 showed that route definitions for `quran-bridge` and `admin` were inserted into `index.php` right inside the `/api/verify_m1` callback, deleting the closing brackets `});`. While Worker M1's initial state passed verification earlier, the current working directory state is broken and unparseable. Under the auditor's non-modification mandate, this must be reported as a failure rather than silently fixed.
- **Dedicated CLI Server (Port 8015)**: `router.php` shares the same `index.php` front controller. Consequently, running `php -S 0.0.0.0:8015 router.php` would hit the same fatal syntax error.

---

## 4. Conclusion

The Milestone M1 work product is **REJECTED** with the verdict **INTEGRITY VIOLATION**.

### Required Remediations before Resubmission:
1. **Fix Syntax Error in `index.php`**:
   Properly terminate the `/api/verify_m1` route handler closure with:
   ```php
       return \Core\Response::json(['error' => 'Verification test file not found'], 404);
   });
   ```
2. **Relocate Tests to `tests/` Directory**:
   Move `.agents/worker_m1/test_m1.php` to `tests/unit/test_m1.php` or `tests/e2e/test_m1.php`.
   Remove all references to `.agents/` from `index.php` and production codebase. Ensure `.agents/` contains only Markdown metadata files (`BRIEFING.md`, `DISPATCH.md`, `progress.md`, `changes.md`, `handoff.md`).
3. **Sanitize `$orderBy` in `core/Model.php`**:
   Add regex whitelisting (`/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`) before concatenating `$orderBy` into SQL queries.

---

## 5. Verification Method

To independently verify these findings:

1. **Verify Fatal Syntax Error**:
   Run via HTTP or PHP CLI:
   ```bash
   php -l index.php
   ```
   Or visit `http://localhost/Kariana%20Website/` in a browser.
   *Expected Error*: `Parse error: syntax error, unexpected variable "$app", expecting ")"` on line 273.

2. **Verify Layout Violation**:
   Inspect `.agents/worker_m1/test_m1.php`:
   ```bash
   ls -la ".agents/worker_m1/test_m1.php"
   grep -n "\.agents" index.php
   ```
   Observe line 208 of `index.php` referencing `.agents/worker_m1/test_m1.php`.

3. **Verify Database State (Passing)**:
   Access `http://localhost/Kariana%20Website/tests/forensic_db_check.php`.
   Observe `tables_count: 13`, `prayer_districts: 64`, all 8 divisions, and TrueType magic header `000100000012010000040020` on font file.

4. **Verify Adversarial Edge Cases (Passing)**:
   Access `http://localhost/Kariana%20Website/tests/forensic_adversarial_test.php`.
   Observe all Unicode slug ligatures, numerals, and SQL injection defenses passing.
