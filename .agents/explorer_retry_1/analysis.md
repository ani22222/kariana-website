# Technical Investigation & Architecture Fix Analysis — Milestone M1

**Agent:** Explorer Retry 1 (`explorer`, `analyst`)  
**Target:** Milestone M1 Remediation (Front Controller Syntax, Layout Integrity & Security Hardening)  
**Target Directory:** `c:\xampp\htdocs\Kariana Website`  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_1`  
**Execution Timestamp:** 2026-09-22T20:38:00+06:00  

---

## Executive Summary

Forensic Auditor M1 and Reviewer 2 issued an **INTEGRITY VIOLATION / REQUEST_CHANGES** verdict against the initial Milestone M1 submission due to two blocking structural failures:
1. **Fatal Parse Error in `index.php`**: An unclosed route closure on `/api/verify_m1` caused PHP compilation to crash on line 273 (`$app->run();`), resulting in HTTP 500 across all site routes.
2. **Layout Non-Compliance Integrity Violation**: Executable test code was placed inside `.agents/worker_m1/test_m1.php` and hardcoded into production `index.php`, violating the core rule that `.agents/` must strictly contain metadata files (`.md`) and zero source or test code.

In addition, empirical stress-testing by Challenger 1, Challenger 2, and Reviewer 2 identified three secondary quality/security defects:
- Blind SQL injection vector in `Core\Model::where()` and `Core\Model::all()` via unvalidated `$orderBy` string concatenation.
- Fatal `TypeError` vulnerability in `Core\Csrf::validate(?string $token)` when array payloads are passed.
- Subtle slugification edge cases in `Core\BengaliHelper::createSlug()` regarding Bengali Dari (`।`), currency Taka (`৳`), punctuation word concatenation, and empty slugs on Arabic text.

This report delivers a complete forensic diagnosis, architectural assessment of repair options, and an exact, drop-in remediation specification for Worker Retry 1. As an Explorer, no implementation changes have been applied to production files.

---

## 1. Deep Dive: `index.php` Parse Error & Route Restructuring

### 1.1 Root Cause Forensic Trace
Inspection of `index.php` around lines 200–276 revealed how the fatal parse error originated:

In the original implementation by Worker M1, the verification suite route was defined as:
```php
// Worker M1 Verification Suite API
$router->get('/api/verify_m1', function (\Core\Request $request) {
    $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
    if (file_exists($testFile)) {
        require_once $testFile;
        $report = runM1Verification();
        return \Core\Response::json($report, 200);
    }
```
When subsequent routes (Quran Reader Bridge `/quran-bridge` and Admin CMS routes `/admin`, `/admin/login`, `/admin/dashboard`, etc.) were introduced, the terminating lines of the callback:
```php
    return \Core\Response::json(['error' => 'Verification test file not found'], 404);
});
```
were inadvertently omitted. 

Because the opening parentheses `(` and curly brace `{` of `$router->get('/api/verify_m1', function (...) {` were never closed:
- Every subsequent route definition (`$router->get(...)`, `$router->post(...)`) was parsed by PHP as statements inside the unclosed closure.
- The closing curly brace `}` at line 272 (intended for the `else` block `if (file_exists($routesFile)) ... else {`) was consumed as the closure's closing brace.
- When the parser reached line 275 (`$app->run();`), PHP was still in the outer parameter list expecting a closing `)` for the `$router->get(...)` invocation.
- PHP halted execution with the fatal compile-time diagnostic:
  `Parse error: syntax error, unexpected variable "$app", expecting ")"`

### 1.2 Current State of `index.php`
A recent manual edit in `index.php` closed lines 213–214:
```php
213:         return \Core\Response::json(['error' => 'Verification test file not found'], 404);
214:     });
```
Running `php -l index.php` now returns `No syntax errors detected in index.php`.

**However**, line 207 still contains the invalid reference:
```php
207:         $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
```
This leaves the application in an architectural integrity violation state.

### 1.3 Route Callback Restructuring Strategy
To ensure route definitions in `index.php` are resilient, decoupled, and clean:
1. **Dedicated Route File Fallback**: The structure in `index.php` checks if `app/routes.php` exists:
   ```php
   $routesFile = __DIR__ . '/app/routes.php';
   if (file_exists($routesFile)) {
       require_once $routesFile;
   } else {
       // Inline fallback routes...
   }
   ```
2. **Clean Route Callback Encapsulation**:
   Every route callback must have explicit return typing and guaranteed closure termination.
   For `/api/verify_m1`:
   - Point `$testFile` to `__DIR__ . '/tests/unit/test_m1.php'`.
   - Ensure explicit fallback JSON response if the test file is absent.
   - Guard against unauthorized exposure in production by conditioning on environment or debug mode (or allowing local diagnostic calls).

---

## 2. Deep Dive: Layout Non-Compliance & Test Relocation

### 2.1 The Violation
- File: `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\test_m1.php` (7,199 bytes).
- System Rule:
  > *"⚠️ `.agents/` holds only agent metadata (plans, progress, handoffs). NEVER place source code, tests, or data files here."*
  > *"Layout Compliance: Verify output follows PROJECT.md layout: source in designated dirs, tests co-located, BUILD files per module. `.agents/` must contain only metadata — source, tests, or data there is a violation."*
- Full tree audit of `.agents/`: A comprehensive scan across all subdirectories of `.agents/` confirms that `worker_m1/test_m1.php` is the **only** non-markdown file present.

### 2.2 Relocation Target Analysis
The canonical test directory structure according to `PROJECT.md` is:
```
tests/
├── e2e/        # Master E2E runner and test tiers (Tiers 1-4)
├── security/   # Dedicated security stress tests
└── unit/       # Unit & component verification tests
```
Creating `tests/unit/test_m1.php` provides the following benefits:
1. **Strict Layout Compliance**: All test scripts reside within `tests/`. `.agents/` is stripped of all code.
2. **Path Depth Compatibility**:
   - In `.agents/worker_m1/test_m1.php`, the relative root path was computed as `dirname(__DIR__, 2)`.
   - In `tests/unit/test_m1.php`, `dirname(__DIR__, 2)` also resolves to the project root (`c:\xampp\htdocs\Kariana Website`).
   - Autoloader and font path assertions (`dirname(__DIR__, 2) . '/core/Autoloader.php'` and `dirname(__DIR__, 2) . '/public/assets/fonts/AAR-SQ-003.ttf'`) remain 100% valid without path calculation breakage.
3. **Dual Execution Mode (CLI + Web API)**:
   Currently, `test_m1.php` only defines `function runM1Verification(): array`. If run from CLI (`php test_m1.php`), it produces no output.
   By adding a CLI execution wrapper at the bottom:
   ```php
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
   Developers and auditors can execute `php tests/unit/test_m1.php` directly from PowerShell / Bash, receiving instant colored/formatted output with exit code 0 or 1.

### 2.3 Evaluation: Updating `index.php` vs. Removing `/api/verify_m1`
| Criterion | Approach A: Repoint to `tests/unit/test_m1.php` | Approach B: Remove `/api/verify_m1` entirely |
|---|---|---|
| **Layout Compliance** | 100% Compliant (Zero `.agents/` references) | 100% Compliant |
| **Reviewer / Automated Test Compatibility** | High: External harnesses calling `/api/verify_m1` continue to receive 200 OK JSON reports | Low: Any caller expecting `/api/verify_m1` receives 404 |
| **Shared Hosting Security** | Safe when `tests/` is protected via `.htaccess` | Safe |
| **Recommendation** | **Adopt Approach A** (Repoint to `tests/unit/test_m1.php`) | Deprecate in future milestones if unneeded |

In addition, `.htaccess` line 36 must be updated to include `tests`:
```apache
RewriteRule ^(app|config|core|database|storage|tests|\.agents)/ - [F,L,NC]
```
This ensures Apache denies direct browser requests to `http://localhost/Kariana%20Website/tests/unit/test_m1.php` while allowing internal PHP `require_once` within `index.php`.

---

## 3. Deep Dive: Secondary Quality & Security Remediations

### 3.1 Blind SQL Injection in `Core\Model.php`
- **Location**: `core/Model.php:75-77`
- **Vulnerability**:
  ```php
  if (!empty($orderBy)) {
      $sql .= " ORDER BY {$orderBy}";
  }
  ```
  Passing `$orderBy = "id ASC, (SELECT 1 FROM (SELECT SLEEP(0.2))a)"` executes arbitrary SQL syntax.
- **Remediation**:
  Parse and validate every comma-separated ordering directive against a strict regex whitelist:
  ```php
  if (!empty($orderBy)) {
      $orderParts = explode(',', $orderBy);
      $cleanParts = [];
      foreach ($orderParts as $part) {
          $part = trim($part);
          if (preg_match('/^`?([a-zA-Z0-9_]+)`?(?:\s+(ASC|DESC))?$/i', $part, $matches)) {
              $col = $matches[1];
              $dir = isset($matches[2]) ? strtoupper($matches[2]) : 'ASC';
              $cleanParts[] = "`{$col}` {$dir}";
          }
      }
      if (!empty($cleanParts)) {
          $sql .= " ORDER BY " . implode(', ', $cleanParts);
      }
  }
  ```

### 3.2 CSRF Parameter Type Safety in `Core\Csrf.php`
- **Location**: `core/Csrf.php:39`
- **Vulnerability**:
  ```php
  public static function validate(?string $token): bool
  ```
  In PHP 8.2, passing an array payload `$_POST['csrf_token'] = ['a' => 'b']` triggers an unhandled `TypeError`.
- **Remediation**:
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

### 3.3 BengaliHelper Edge Cases (`core/BengaliHelper.php`)
- **Issues Identified by Challenger 1**:
  1. Bengali Dari (`।` - U+0964) and Double Dari (`॥` - U+0965) belong to the `\p{Bengali}` Unicode block and are not stripped by `preg_replace('/[^\p{Bengali}...]/u')`.
  2. Bengali Currency Taka (`৳` - U+09F3) belongs to `\p{Bengali}` and is not stripped.
  3. Punctuation delimiters without spaces (e.g. `কুরআন/সুন্নাহ`) concatenate into single words (`কুরআনসুন্নাহ`).
  4. Pure Arabic titles (e.g. `القرآن الكريم`) strip to empty string `""`, crashing on unique DB constraints.
- **Remediation**:
  Pre-convert delimiters and symbols (slashes, commas, colons, Dari, Taka) to spaces *before* stripping, allow `\p{Arabic}` if desired or provide a deterministic fallback for empty slugs:
  ```php
  public static function createSlug(string $title): string
  {
      $title = trim($title);
      // Pre-replace punctuation and symbols (including Bengali Dari U+0964, Double Dari U+0965, Taka U+09F3) with spaces
      $title = preg_replace('/[\x{0964}\x{0965}\x{09F3}\/\.,;:!?"\'\(\)\[\]\{\}\<\>@#\$%\^&\*\+=~`|]+/u', ' ', $title);
      
      // Preserve Bengali and English alphanumeric characters and spaces
      $slug = preg_replace('/[^\p{Bengali}\p{Arabic}a-zA-Z0-9\s_-]/u', '', $title);
      
      // Convert whitespace and underscores to single hyphen
      $slug = preg_replace('/[\s_]+/u', '-', $slug);
      
      // Collapse multiple consecutive hyphens
      $slug = preg_replace('/-+/u', '-', $slug);
      
      // Trim hyphens
      $slug = trim($slug, '-');
      
      // Fallback for empty strings
      if ($slug === '') {
          $slug = 'item-' . substr(md5($title . microtime()), 0, 8);
      }
      
      return mb_strtolower($slug, 'UTF-8');
  }
  ```

---

## 4. Complete Action Plan for Worker Retry 1

Worker Retry 1 should execute the following 7 atomic tasks:

### Task 1: Create `tests/unit/test_m1.php`
- Create directory `tests/unit` if it does not exist.
- Write `tests/unit/test_m1.php` containing the verified M1 verification suite, updated relative paths, and the CLI execution block.

### Task 2: Remove `.agents/worker_m1/test_m1.php`
- Delete `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\test_m1.php`.
- Verify `.agents/` contains strictly `.md` files.

### Task 3: Update `index.php`
- Update line 207:
  ```php
  $testFile = __DIR__ . '/tests/unit/test_m1.php';
  ```
- Confirm lines 213–214 cleanly close `/api/verify_m1`:
  ```php
          return \Core\Response::json(['error' => 'Verification test file not found'], 404);
      });
  ```

### Task 4: Update `.htaccess`
- In line 36, add `tests` to protected directory list:
  ```apache
  RewriteRule ^(app|config|core|database|storage|tests|\.agents)/ - [F,L,NC]
  ```

### Task 5: Harden `core/Model.php`
- Replace line 75–77 with strict `$orderBy` token validation and backtick quoting.

### Task 6: Harden `core/Csrf.php`
- Change `public static function validate(?string $token)` to `public static function validate(mixed $token)` with `is_string($token)` check.

### Task 7: Refine `core/BengaliHelper.php`
- Update `createSlug()` to handle Dari, Taka symbol, delimiter spacing, and empty string fallback.

---

## 5. Verification Matrix for Worker & Reviewer

| # | Check | Verification Command / Target | Expected Output |
|---|---|---|---|
| 1 | PHP Syntax Check | `php -l index.php` | `No syntax errors detected in index.php` |
| 2 | Front Controller HTTP | `http://localhost/Kariana%20Website/api/health` | HTTP 200, `{"status":"ok",...}` |
| 3 | M1 CLI Unit Test | `php tests/unit/test_m1.php` | Exit code 0, `ALL TESTS PASSED` (10/10) |
| 4 | M1 HTTP Verification API | `http://localhost/Kariana%20Website/api/verify_m1` | HTTP 200, `{"success":true,...}` |
| 5 | Layout Compliance | `ls .agents/worker_m1/` & search non-md in `.agents/` | Zero non-md files. Strictly `.md` files only |
| 6 | Model ORDER BY Defense | Pass `"id ASC, (SELECT SLEEP(1))"` to `Model::all()` | No SQL error, no sleep execution, safely filtered |
| 7 | CSRF Array Payload | Submit `csrf_token[] = 1` | Returns `false` without throwing `TypeError` |
| 8 | Bengali Dari & Taka Slug | `BengaliHelper::createSlug('কুরআন শিক্ষা। ফি ৫০০৳')` | Output: `কুরআন-শিক্ষা-ফি-৫০০` (No `।`, no `৳`) |
