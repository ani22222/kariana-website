# Handoff Report — Explorer Retry 1: Milestone M1 Investigation & Remediation Architecture

**Agent:** Explorer Retry 1 (`explorer`, `analyst`)  
**Target:** Milestone M1 Remediation Blueprint  
**Target Directory:** `c:\xampp\htdocs\Kariana Website`  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_1`  
**Recipient:** Orchestrator (`5a011e50-ed48-4482-b181-5ca5e13d7062`) & Worker Retry 1  
**Timestamp:** 2026-09-22T20:40:00+06:00  

---

## 1. Observation

### 1.1 Direct Observation of `index.php` Front Controller
Inspected `c:\xampp\htdocs\Kariana Website\index.php` (Lines 205–216):
```php
205:     // Worker M1 Verification Suite API
206:     $router->get('/api/verify_m1', function (\Core\Request $request) {
207:         $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
208:         if (file_exists($testFile)) {
209:             require_once $testFile;
210:             $report = runM1Verification();
211:             return \Core\Response::json($report, 200);
212:         }
213:         return \Core\Response::json(['error' => 'Verification test file not found'], 404);
214:     });
215:     // Quran Reader Launchpad & Bridge
```
- **Syntax Check (`php -l index.php`)**: Returns exit code 0: `No syntax errors detected in index.php`.
- **Closure Status**: Lines 213–214 currently contain the closing `return ... 404; });`, resolving the syntax error previously reported by Auditor M1 (`Parse error: syntax error, unexpected variable "$app", expecting ")" in index.php on line 273`).
- **Internal Coupling Violation**: Line 207 explicitly requires a test file located in `.agents/`:
  `$testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';`

### 1.2 Direct Observation of `.agents/` Directory Structure
- Inspected `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\`:
  - `test_m1.php` exists with size `7,199 bytes`.
- Executed comprehensive non-markdown search across all subdirectories of `.agents/`:
  - Exactly **1 non-markdown file** was found across the entire `.agents/` folder: `worker_m1/test_m1.php`.
- Project constraint in `PROJECT.md` and system prompt:
  > *"⚠️ `.agents/` holds only agent metadata (plans, progress, handoffs). NEVER place source code, tests, or data files here."*
- Apache configuration in `.htaccess` line 36:
  `RewriteRule ^(app|config|core|database|storage|\.agents)/ - [F,L,NC]`
  Direct access to `.agents/` is blocked by Apache, creating a fragile dependency when production code requires files from `.agents/`.

### 1.3 Observation of Secondary Security & Quality Findings
1. **`core/Model.php` (Line 76)**:
   ```php
   if (!empty($orderBy)) {
       $sql .= " ORDER BY {$orderBy}";
   }
   ```
   `$orderBy` is concatenated raw without whitelist validation. Passing SQL expressions like `"id ASC, (SELECT SLEEP(0.2))"` executes the subquery (measured 214 ms delay in Challenger 2 stress test).
2. **`core/Csrf.php` (Line 39)**:
   ```php
   public static function validate(?string $token): bool
   ```
   Calling `Csrf::validate($_POST['csrf_token'])` when `csrf_token` is an array triggers a fatal PHP 8.2 `TypeError`.
3. **`core/BengaliHelper.php` (Lines 57-60)**:
   - Bengali Dari (`।` - U+0964) and Double Dari (`॥` - U+0965) belong to the `\p{Bengali}` Unicode block and persist in generated slugs (e.g. `'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা।'`).
   - Bengali currency Taka (`৳` - U+09F3) persists in generated slugs (e.g. `'ফি-৫০০৳'`).
   - Delimiters without surrounding whitespace (e.g. `'কুরআন/সুন্নাহ'`) concatenate words into `'কুরআনসুন্নাহ'`.
   - Pure Arabic strings (e.g. `'القرآن الكريم'`) produce empty string `""`, risking database duplicate key collisions.

---

## 2. Logic Chain

1. **Syntax Integrity**: From Observation 1.1, the previous syntax error was triggered by an unclosed route closure. While closing brackets were inserted on line 214, line 207 still couples the production front controller to `.agents/worker_m1/test_m1.php`.
2. **Layout Compliance Rule**: From Observation 1.2, having `test_m1.php` inside `.agents/` is a direct violation of the mandatory layout rule. Relocating `test_m1.php` to `tests/unit/test_m1.php` restores full compliance with `PROJECT.md`.
3. **Path Invariance**: In `tests/unit/test_m1.php`, `dirname(__DIR__, 2)` evaluates to the exact same root directory (`c:\xampp\htdocs\Kariana Website`) as it did in `.agents/worker_m1/test_m1.php`. Therefore, all internal paths for `Autoloader.php` and `AAR-SQ-003.ttf` remain intact.
4. **Endpoint Strategy**:
   - Updating `index.php` line 207 to point to `__DIR__ . '/tests/unit/test_m1.php'` preserves compatibility with external HTTP verification runners and reviewers while removing all `.agents/` references from production code.
   - Adding `tests` to `.htaccess` line 36 prevents direct web execution of test files by browsers, while allowing internal `require_once` by `index.php`.
5. **Security Hardening**:
   - Sanitizing `$orderBy` in `core/Model.php` neutralizes the SQL injection vector before user-facing sorting features are added in M2 and M4.
   - Changing `Csrf::validate(?string $token)` to `Csrf::validate(mixed $token)` eliminates unhandled `TypeError` exceptions under PHP 8.2 strict typing.
   - Pre-replacing punctuation and Bengali Dari/Taka characters in `BengaliHelper::createSlug()` satisfies all empirical linguistic edge cases identified by Challenger 1.

---

## 3. Caveats

- **Read-Only Explorer Mandate**: In accordance with the Explorer archetype rules, Explorer Retry 1 has implemented **zero** changes to production codebase or test directories. All recommended code modifications are detailed below as explicit instructions and diffs for Worker Retry 1.
- **PHP CLI Execution Environment**: When running tests or CLI scripts, use standard commands without interactive prompts. `php tests/unit/test_m1.php` and `php -l index.php` execute synchronously and cleanly.
- **Port Allocation**: Dedicated port `8015` remains reserved and assigned to the Kariana Quran portal per `ORIGINAL_REQUEST.md` and project port rules.

---

## 4. Conclusion & Proposed Code Changes

Milestone M1 can achieve immediate, 100% clean approval by executing the following targeted remediation plan via Worker Retry 1:

### Proposed Change 1: Create `tests/unit/test_m1.php`
Relocate the M1 verification suite to `tests/unit/test_m1.php` and add CLI execution capabilities:
```php
<?php
/**
 * Worker M1 Verification Suite
 * Verifies Autoloader, Router, BengaliHelper, Database, CSRF, and Font Assets
 */

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/core/Autoloader.php';
\Core\Autoloader::register();

use Core\App;
use Core\Autoloader;
use Core\BengaliHelper;
use Core\Controller;
use Core\Csrf;
use Core\Database;
use Core\Model;
use Core\Request;
use Core\Response;
use Core\Router;
use Core\Session;
use Core\View;

function runM1Verification(): array
{
    $results = [];
    $allPassed = true;

    $assert = function(string $testName, bool $condition, string $details = '') use (&$results, &$allPassed) {
        $results[] = [
            'test'    => $testName,
            'passed'  => $condition,
            'details' => $details,
        ];
        if (!$condition) {
            $allPassed = false;
        }
    };

    try {
        // 1. Autoloader Verification
        $assert(
            'Autoloader: App and Core classes exist',
            class_exists(App::class) &&
            class_exists(Router::class) &&
            class_exists(Request::class) &&
            class_exists(Response::class) &&
            class_exists(Controller::class) &&
            class_exists(Model::class) &&
            class_exists(View::class) &&
            class_exists(Database::class) &&
            class_exists(BengaliHelper::class) &&
            class_exists(Csrf::class) &&
            class_exists(Session::class),
            'All Core framework classes loaded successfully via PSR-4 autoloader.'
        );

        // 2. BengaliHelper Verification
        $enNum = 2026;
        $bnNum = BengaliHelper::toBengaliNumber($enNum);
        $backToEn = BengaliHelper::toEnglishNumber($bnNum);
        $slugTest = BengaliHelper::createSlug('কুরআন-তিলাওয়াত ও তাজবীদ শিক্ষা!?');

        $assert(
            'BengaliHelper: Numeral Conversion 0-9 <-> ০-৯',
            $bnNum === '২০২৬' && $backToEn === '2026',
            "Expected '২০২৬' and '2026', got '{$bnNum}' and '{$backToEn}'"
        );

        $assert(
            'BengaliHelper: Bengali Unicode Slug Preservation',
            $slugTest === 'কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা',
            "Expected 'কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা', got '{$slugTest}'"
        );

        // 3. CSRF Verification
        $token = Csrf::token();
        $isValid = Csrf::validate($token);
        $isInvalid = !Csrf::validate('fake-tampered-token-123');

        $assert(
            'CSRF: Cryptographic token generation and timing-safe validation',
            strlen($token) === 64 && ctype_xdigit($token) && $isValid && $isInvalid,
            "Token length: " . strlen($token) . ", valid check: " . ($isValid ? 'true' : 'false')
        );

        // 4. Database Verification
        $db = Database::getInstance();
        $charsetStmt = $db->query("SHOW VARIABLES LIKE 'character_set_connection'");
        $charsetRow = $charsetStmt->fetch();
        $isUtf8mb4 = str_contains(strtolower($charsetRow['Value'] ?? ''), 'utf8mb4');

        $assert(
            'Database: PDO singleton connected with UTF8MB4 charset',
            $isUtf8mb4,
            "Connection charset: " . ($charsetRow['Value'] ?? 'unknown')
        );

        // Check 12 tables exist
        $tablesStmt = $db->query("SHOW TABLES");
        $allTables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

        $requiredTables = [
            'users', 'categories', 'posts', 'courses', 'admissions',
            'books', 'pages', 'qr_lessons', 'prayer_districts',
            'zakat_settings', 'site_settings', 'migrations'
        ];

        $missingTables = array_diff($requiredTables, $allTables);
        $assert(
            'Database: 12 normalized tables present',
            empty($missingTables),
            empty($missingTables) ? 'All 12 tables present: ' . implode(', ', $requiredTables) : 'Missing: ' . implode(', ', $missingTables)
        );

        // Check 64 districts seeded
        $districtCount = (int)$db->query("SELECT COUNT(*) FROM `prayer_districts`")->fetchColumn();
        $assert(
            'Database: 64 districts seeded with IFB prayer offsets',
            $districtCount === 64,
            "District count: {$districtCount} / 64"
        );

        // Check Admin user seeded
        $adminStmt = $db->prepare("SELECT `username`, `email`, `role`, `password` FROM `users` WHERE `username` = :u");
        $adminStmt->execute(['u' => 'admin']);
        $adminUser = $adminStmt->fetch();
        $passwordValid = $adminUser && password_verify('admin123', $adminUser['password']);

        $assert(
            'Database: Admin user seeded and bcrypt password verified',
            $adminUser && $adminUser['role'] === 'admin' && $passwordValid,
            "Admin user found with email " . ($adminUser['email'] ?? 'none') . ", role " . ($adminUser['role'] ?? 'none') . ", bcrypt verified: " . ($passwordValid ? 'YES' : 'NO')
        );

        // 5. Router Verification with Bengali Slug
        $router = new Router();
        $capturedSlug = '';
        $router->get('/blog/{slug}', function(Request $req, string $slug) use (&$capturedSlug) {
            $capturedSlug = $slug;
            return Response::json(['slug' => $slug]);
        });

        // Simulate Request for Bengali slug
        $backupServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']    = '/blog/%E0%A6%B8%E0%A6%B9%E0%A6%9C-%E0%A6%AA%E0%A6%A6%E0%A7%8D%E0%A6%A7%E0%A6%A4%E0%A6%BF%E0%A6%A4%E0%A7%87-%E0%A6%95%E0%A7%81%E0%A6%B0%E0%A6%86%E0%A6%A8-%E0%A6%B6%E0%A7%87%E0%A6%96%E0%A6%BE';
        $_SERVER['SCRIPT_NAME']    = '/index.php';

        $simRequest = new Request();
        $routerResponse = $router->dispatch($simRequest);
        $_SERVER = $backupServer;

        $assert(
            'Router: PCRE /u Unicode routing with Bengali slug /blog/সহজ-পদ্ধতিতে-কুরআন-শেখা',
            $capturedSlug === 'সহজ-পদ্ধতিতে-কুরআন-শেখা',
            "Captured slug: '{$capturedSlug}'"
        );

        // 6. Font Asset Verification
        $fontPath = dirname(__DIR__, 2) . '/public/assets/fonts/AAR-SQ-003.ttf';
        $fontExists = file_exists($fontPath);
        $fontSize = $fontExists ? filesize($fontPath) : 0;

        $assert(
            'Assets: Kariana Arabic font AAR-SQ-003.ttf deployed in public/assets/fonts/',
            $fontExists && $fontSize === 682112,
            "Font path: {$fontPath}, size: {$fontSize} bytes (Expected: 682112 bytes)"
        );
    } catch (\Throwable $e) {
        $allPassed = false;
        $results[] = [
            'test'    => 'Exception during test execution',
            'passed'  => false,
            'details' => $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine(),
        ];
    }

    return [
        'success'   => $allPassed,
        'timestamp' => date('Y-m-d H:i:s'),
        'results'   => $results,
    ];
}

// Direct CLI Execution Support
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    $report = runM1Verification();
    echo "\n=======================================================\n";
    echo "  Milestone M1 Core Framework Verification Suite\n";
    echo "=======================================================\n\n";
    foreach ($report['results'] as $res) {
        $status = $res['passed'] ? "  [PASS] " : "  [FAIL] ";
        echo $status . str_pad($res['test'], 60) . "\n";
        if (!empty($res['details'])) {
            echo "         -> " . $res['details'] . "\n";
        }
    }
    echo "\n" . str_repeat('-', 55) . "\n";
    echo "Overall Status: " . ($report['success'] ? "ALL TESTS PASSED" : "TESTS FAILED") . "\n";
    echo "Timestamp:      " . $report['timestamp'] . "\n";
    echo str_repeat('-', 55) . "\n\n";
    exit($report['success'] ? 0 : 1);
}
```

### Proposed Change 2: Delete `.agents/worker_m1/test_m1.php`
Remove file `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\test_m1.php`.

### Proposed Change 3: Update `index.php` (Line 207)
```diff
--- a/index.php
+++ b/index.php
@@ -204,7 +204,7 @@
 
     // Worker M1 Verification Suite API
     $router->get('/api/verify_m1', function (\Core\Request $request) {
-        $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
+        $testFile = __DIR__ . '/tests/unit/test_m1.php';
         if (file_exists($testFile)) {
             require_once $testFile;
             $report = runM1Verification();
```

### Proposed Change 4: Protect `tests/` in `.htaccess` (Line 36)
```diff
--- a/.htaccess
+++ b/.htaccess
@@ -33,4 +33,4 @@ RewriteEngine On
 
 # Strictly protect internal application code directories from direct web access
-RewriteRule ^(app|config|core|database|storage|\.agents)/ - [F,L,NC]
+RewriteRule ^(app|config|core|database|storage|tests|\.agents)/ - [F,L,NC]
```

### Proposed Change 5: Sanitize `$orderBy` in `core/Model.php` (Lines 75-77)
```diff
--- a/core/Model.php
+++ b/core/Model.php
@@ -74,5 +74,16 @@ class Model
 
         if (!empty($orderBy)) {
-            $sql .= " ORDER BY {$orderBy}";
+            $orderParts = explode(',', $orderBy);
+            $cleanParts = [];
+            foreach ($orderParts as $part) {
+                $part = trim($part);
+                if (preg_match('/^`?([a-zA-Z0-9_]+)`?(?:\s+(ASC|DESC))?$/i', $part, $matches)) {
+                    $col = $matches[1];
+                    $dir = isset($matches[2]) ? strtoupper($matches[2]) : 'ASC';
+                    $cleanParts[] = "`{$col}` {$dir}";
+                }
+            }
+            if (!empty($cleanParts)) {
+                $sql .= " ORDER BY " . implode(', ', $cleanParts);
+            }
         }
```

### Proposed Change 6: Robust CSRF Signature in `core/Csrf.php` (Line 39)
```diff
--- a/core/Csrf.php
+++ b/core/Csrf.php
@@ -39,5 +39,5 @@ class Csrf
-    public static function validate(?string $token): bool
+    public static function validate(mixed $token): bool
     {
-        if (empty($token)) {
+        if (!is_string($token) || empty($token)) {
             return false;
         }
```

### Proposed Change 7: Refine `createSlug()` in `core/BengaliHelper.php` (Lines 50-70)
```diff
--- a/core/BengaliHelper.php
+++ b/core/BengaliHelper.php
@@ -49,15 +49,20 @@ class BengaliHelper
     public static function createSlug(string $title): string
     {
         $title = trim($title);
+        // Pre-replace punctuation, symbols (including Dari U+0964, Double Dari U+0965, Taka U+09F3) with spaces
+        $title = preg_replace('/[\x{0964}\x{0965}\x{09F3}\/\.,;:!?"\'\(\)\[\]\{\}\<\>@#\$%\^&\*\+=~`|]+/u', ' ', $title);
 
-        // Remove characters that are NOT Bengali, English alphanumeric, space, or hyphen
-        $slug = preg_replace('/[^\p{Bengali}a-zA-Z0-9\s_-]/u', '', $title);
+        // Remove characters that are NOT Bengali, Arabic, English alphanumeric, space, or hyphen
+        $slug = preg_replace('/[^\p{Bengali}\p{Arabic}a-zA-Z0-9\s_-]/u', '', $title);
 
         // Convert whitespace and underscores to single hyphen
         $slug = preg_replace('/[\s_]+/u', '-', $slug);
 
         // Collapse multiple consecutive hyphens
         $slug = preg_replace('/-+/u', '-', $slug);
 
         // Trim leading and trailing hyphens
         $slug = trim($slug, '-');
+
+        if ($slug === '') {
+            $slug = 'item-' . substr(md5($title . microtime()), 0, 8);
+        }
 
         return mb_strtolower($slug, 'UTF-8');
     }
```

---

## 5. Verification Method

Once Worker Retry 1 applies the proposed changes, verify independently:

1. **Verify PHP Syntax of Entry Point**:
   ```bash
   php -l index.php
   ```
   *Expected*: `No syntax errors detected in index.php`.

2. **Verify Layout Compliance**:
   ```powershell
   Get-ChildItem -Path ".agents" -Recurse -File | Where-Object { $_.Extension -ne ".md" }
   ```
   *Expected*: Zero files returned. Strictly `.md` files present in `.agents/`.

3. **Verify Zero `.agents` in Codebase**:
   ```powershell
   Select-String -Path "index.php", "core\*.php", "app\*.php" -Pattern "\.agents"
   ```
   *Expected*: Zero matches.

4. **Verify CLI Unit Test Suite**:
   ```bash
   php tests/unit/test_m1.php
   ```
   *Expected*: Output displaying 10/10 `[PASS]` assertions, ending with `Overall Status: ALL TESTS PASSED` and exit code 0.

5. **Verify Web Verification Endpoint**:
   Query `http://localhost/Kariana%20Website/api/verify_m1` (or via CLI test client).
   *Expected*: HTTP 200 JSON with `"success": true` and all 10 checks passing.

6. **Verify Model SQL Injection Defense**:
   Call `(new \Core\Model('prayer_districts'))->all("id ASC, (SELECT SLEEP(1))")`.
   *Expected*: Query executes without sleeping, ordered solely by `` `id` ASC ``.

7. **Verify CSRF Array Robustness**:
   `\Core\Csrf::validate(['malicious' => 'array'])`.
   *Expected*: Returns `false` without throwing `TypeError`.
