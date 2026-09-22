# Technical Investigation & Security Architecture Analysis — Milestone M1 Retry 3

**Agent**: Explorer Retry 3 (`investigator`, `synthesizer`)  
**Target Directory**: `c:\xampp\htdocs\Kariana Website`  
**Working Directory**: `c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_3`  
**Date**: 2026-09-22T20:35:00+06:00  
**Status**: READ-ONLY INVESTIGATION COMPLETE (No direct implementation applied)  

---

## Executive Summary

Following forensic audit and peer review findings (Auditor M1, Reviewer 1, Challenger 2), this investigation conducts a deep-dive analysis into three critical architectural components of the Kariana Quran Portal & CMS:
1. **`core/Model.php`**: Concatenation of raw `$orderBy` in `where()` and `all()` allowing potential blind SQL injection. Formulates a strict, two-tier regex whitelisting defense based on `/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`.
2. **`router.php`**: Built-in PHP CLI server router on port 8015 (`0.0.0.0`) which previously served arbitrary non-PHP files from the project root via Check 1. Formulates comprehensive security controls blocking sensitive directories, hidden dotfiles, and file types (`.sql`, `.md`, `.json`, `.env`, `config/`, `database/`, `.git/`, `.agents/`) while safely serving static assets from `public/`.
3. **`.htaccess`**: Flawed rewrite rule `RewriteCond %{DOCUMENT_ROOT}/public/$1 -f` which breaks static asset loading under subfolder environments (e.g. `http://localhost/Kariana%20Website/assets/...`). Formulates portable per-directory rewrite rules functioning identically in both root domain and subfolder installations.

---

## 1. Investigation Scope 1: `core/Model.php` OrderBy Parameter Whitelisting

### 1.1 Existing Vulnerability & Blast Radius
In `core/Model.php` lines 51-97, the `where()` method builds SQL queries dynamically:
```php
51: public function where(array $conditions = [], string $orderBy = '', int $limit = 0, int $offset = 0): array
...
75: if (!empty($orderBy)) {
76:     $sql .= " ORDER BY {$orderBy}";
77: }
...
94: public function all(string $orderBy = '', int $limit = 0, int $offset = 0): array
95: {
96:     return $this->where([], $orderBy, $limit, $offset);
97: }
```

While `$conditions` keys are sanitized with regex `/^[a-zA-Z0-9_]+$/` (line 59) and values are bound using PDO prepared statement parameters `:p_col` (lines 66-67), `$orderBy` is concatenated verbatim into the SQL string.

#### Empirical Evidence (Challenger 2 Finding):
Executing:
```php
$model->all("id ASC, (SELECT 1 FROM (SELECT SLEEP(0.2))a)", 1);
```
caused MySQL to execute the `SLEEP` call with an elapsed time of 214.4 ms.
If any downstream controller (e.g., in M2 or M4) binds user input from `Request::get('sort')` directly to `Model::all($sort)` or `Model::where([], $sort)`, an attacker could execute time-based blind SQL injection or extract data character-by-character.

### 1.2 Mathematical & Character-Space Analysis of Whitelisting Regex
The authoritative specification requires formulating strict regex whitelisting:
`/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`

Let us evaluate the character space permitted by this regex:
- **Allowed characters**: ASCII letters (`a-z`, `A-Z`), digits (`0-9`), underscore (`_`), comma (`,`), whitespace (`\s`), dot (`.`), and optional trailing sort direction (`ASC` or `DESC`).
- **Forbidden characters**:
  - Parentheses `(` and `)` are **strictly forbidden** $\implies$ Function calls (`SLEEP()`, `BENCHMARK()`, `COUNT()`, `MID()`, `SUBSTRING()`) are completely neutralized.
  - Quotes `'` and `"` are **strictly forbidden** $\implies$ String literals and string concatenation are impossible.
  - Semicolon `;` is **strictly forbidden** $\implies$ Stacked queries (e.g. `; DROP TABLE users;`) cannot be executed.
  - Comment markers (`--`, `/*`, `*/`, `#`) are **strictly forbidden** $\implies$ Query truncation is impossible.
  - Operators (`=`, `<`, `>`, `+`, `-`, `&`, `|`, `^`, `~`) are **strictly forbidden** $\implies$ Boolean conditional evaluations (`CASE WHEN id=1...`) cannot execute.

### 1.3 Two-Tier Defense Architecture
To prevent any edge case where an attacker crafts valid characters into arbitrary keywords (e.g., `id ASC SLEEP col`), we recommend a **two-tier validation model**:
1. **Tier 1 (Global Regex)**: Checks the entire trimmed string against `/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`. If any prohibited character exists, immediately throws `\InvalidArgumentException`.
2. **Tier 2 (Per-Term Structural Validation)**: Splits the string by `,`. Every individual term must match:
   `/^[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)?(?:\s+(?:ASC|DESC))?$/i`
   This enforces that every element is strictly `column [ASC|DESC]` or `table.column [ASC|DESC]`.

### 1.4 Exact Proposed Code for `core/Model.php`
Replace lines 75-77 in `core/Model.php`:

```php
<<<<
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }
====
        if (!empty($orderBy)) {
            $trimmedOrder = trim($orderBy);
            if (!preg_match('/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i', $trimmedOrder)) {
                throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
            }
            // Secondary per-clause structural validation to eliminate keyword stuffing
            $orderParts = explode(',', $trimmedOrder);
            foreach ($orderParts as $part) {
                $part = trim($part);
                if ($part === '' || !preg_match('/^[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)?(?:\s+(?:ASC|DESC))?$/i', $part)) {
                    throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
                }
            }
            $sql .= " ORDER BY {$trimmedOrder}";
        }
>>>>
```

### 1.5 Validation Matrix
| Input Payload | Tier 1 Check | Tier 2 Check | Outcome |
| :--- | :--- | :--- | :--- |
| `sort_order ASC` | PASS | PASS | Valid SQL generated |
| `created_at DESC` | PASS | PASS | Valid SQL generated |
| `sort_order ASC, id DESC` | PASS | PASS | Valid SQL generated |
| `courses.sort_order ASC` | PASS | PASS | Valid SQL generated |
| `id ASC, (SELECT 1 FROM (SELECT SLEEP(0.2))a)` | **FAIL** (contains `(`, `)`) | N/A | `InvalidArgumentException` |
| `1; DROP TABLE users; --` | **FAIL** (contains `;`, `-`) | N/A | `InvalidArgumentException` |
| `' OR '1'='1` | **FAIL** (contains `'`, `=`) | N/A | `InvalidArgumentException` |
| `id ASC SLEEP col` | PASS | **FAIL** (invalid term format) | `InvalidArgumentException` |
| `id, , sort_order` | PASS | **FAIL** (empty term) | `InvalidArgumentException` |

---

## 2. Investigation Scope 2: `router.php` Built-in CLI Server Hardening

### 2.1 Existing Vulnerability (Reviewer 1 Finding)
In `router.php` lines 15-22:
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
When running on dedicated port 8015 (`php -S 0.0.0.0:8015 router.php`), returning `false` instructs PHP's built-in web server to serve the requested physical file directly.
Consequently, any unauthenticated HTTP client on localhost or the local Wi-Fi network (`192.168.0.100:8015`) could request:
- `http://localhost:8015/database/schema.sql` $\implies$ full database schema and seed data leaked.
- `http://localhost:8015/ORIGINAL_REQUEST.md` $\implies$ confidential requirements leaked.
- `http://localhost:8015/.agents/worker_m1/handoff.md` $\implies$ internal metadata leaked.
- `http://localhost:8015/composer.json` or `.env` $\implies$ environment and credentials leaked.

Furthermore, line 13 attempted directory traversal protection using a single-pass `str_replace(['../', '..\\'], '', ...)` which can be circumvented via nested constructs (e.g. `....//`).

### 2.2 Security Architecture Requirements
To secure `router.php`, four defensive layers must be established:
1. **Directory Traversal Elimination**: Immediately abort with HTTP 400 if `$decodedPath` contains `..` or backslashes `\`.
2. **Hidden Dotfile & Dotfolder Lockdown**: Reject any path where any URL segment begins with `.` (e.g. `/.env`, `/.git`, `/.agents`).
3. **Protected Directory Lockdown**: Explicitly reject requests targeted at internal application directories (`app/`, `config/`, `core/`, `database/`, `storage/`, `tests/`, `.agents/`, `.git/`, `vendor/`).
4. **Sensitive File Extension Blacklist**: Explicitly block `.sql`, `.md`, `.json`, `.env`, `.log`, `.ini`, `.lock`, `.yml`, `.yaml`, `.bak`, `.sh`, `.bat`.
5. **Strict `public/` Directory Isolation**: Eliminate Check 1 entirely. Only serve static files if they reside strictly inside the `public/` directory and match an approved MIME whitelist.

### 2.3 Exact Proposed Code for `router.php`
Replace the entire content of `c:\xampp\htdocs\Kariana Website\router.php` with:

```php
<?php
/**
 * CLI Built-in Web Server Router
 * Dedicated Port: 8015 (Bound to 0.0.0.0 for LAN/Wi-Fi Multi-Device Access)
 * Usage: php -S 0.0.0.0:8015 router.php
 */

declare(strict_types=1);

$rawUri = $_SERVER['REQUEST_URI'] ?? '/';
$parsedPath = parse_url($rawUri, PHP_URL_PATH) ?? '/';
$decodedPath = rawurldecode($parsedPath);

// 1. Directory Traversal Defense: Reject any attempt to climb directory trees
if (str_contains($decodedPath, '..') || str_contains($decodedPath, '\\')) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '400 Bad Request: Invalid path traversal sequence detected.';
    exit;
}

// 2. Dotfile / Dotdirectory Defense: Block all hidden entities (.env, .git, .agents, .htaccess)
$segments = array_values(array_filter(explode('/', trim($decodedPath, '/'))));
foreach ($segments as $segment) {
    if (str_starts_with($segment, '.')) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=UTF-8');
        echo '403 Forbidden: Access to hidden files or directories is denied.';
        exit;
    }
}

// 3. Protected Application Directories Defense: Block direct access to internal source trees
$protectedDirs = ['app', 'config', 'core', 'database', 'storage', 'tests', 'vendor'];
$firstSegment = strtolower($segments[0] ?? '');
if (in_array($firstSegment, $protectedDirs, true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '403 Forbidden: Direct access to internal application directories is denied.';
    exit;
}

// 4. Sensitive Extension Defense: Block direct retrieval of sensitive file formats
$ext = strtolower(pathinfo($decodedPath, PATHINFO_EXTENSION));
$blockedExtensions = ['sql', 'md', 'json', 'env', 'log', 'ini', 'lock', 'yml', 'yaml', 'bak', 'sh', 'bat'];
if (in_array($ext, $blockedExtensions, true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=UTF-8');
    echo '403 Forbidden: Direct access to this file type is denied.';
    exit;
}

// 5. Safe Static Asset Resolution: Strictly isolated to the public/ directory
$publicDir = realpath(__DIR__ . '/public');
if ($publicDir !== false) {
    // Normalization: strip leading /public if already present in request path
    $relativeAssetPath = $decodedPath;
    if (str_starts_with($relativeAssetPath, '/public/')) {
        $relativeAssetPath = substr($relativeAssetPath, 7);
    }

    $candidateFile = $publicDir . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativeAssetPath), DIRECTORY_SEPARATOR);

    if (file_exists($candidateFile) && is_file($candidateFile)) {
        $resolvedCandidate = realpath($candidateFile);
        
        // Ensure resolved realpath is strictly located inside the public/ root
        if ($resolvedCandidate !== false && str_starts_with($resolvedCandidate, $publicDir)) {
            $fileExt = strtolower(pathinfo($resolvedCandidate, PATHINFO_EXTENSION));

            $allowedMimes = [
                'css'   => 'text/css; charset=UTF-8',
                'js'    => 'application/javascript; charset=UTF-8',
                'png'   => 'image/png',
                'jpg'   => 'image/jpeg',
                'jpeg'  => 'image/jpeg',
                'gif'   => 'image/gif',
                'webp'  => 'image/webp',
                'svg'   => 'image/svg+xml',
                'ico'   => 'image/x-icon',
                'ttf'   => 'font/ttf',
                'woff'  => 'font/woff',
                'woff2' => 'font/woff2',
                'eot'   => 'application/vnd.ms-fontobject',
                'otf'   => 'font/otf',
                'pdf'   => 'application/pdf',
                'mp3'   => 'audio/mpeg',
                'mp4'   => 'video/mp4',
                'txt'   => 'text/plain; charset=UTF-8',
                'xml'   => 'application/xml; charset=UTF-8',
            ];

            if (isset($allowedMimes[$fileExt])) {
                header("Content-Type: {$allowedMimes[$fileExt]}");
                header("Content-Length: " . (string)filesize($resolvedCandidate));
                header("Cache-Control: public, max-age=604800");
                header("X-Content-Type-Options: nosniff");
                readfile($resolvedCandidate);
                exit;
            }

            http_response_code(403);
            header('Content-Type: text/plain; charset=UTF-8');
            echo '403 Forbidden: File type not permitted for public static delivery.';
            exit;
        }
    }
}

// 6. Forward all application web requests to the Front Controller
require_once __DIR__ . '/index.php';
```

---

## 3. Investigation Scope 3: `.htaccess` Dual Deployment & Subfolder Asset Resolution

### 3.1 Existing Defect Analysis
In `c:\xampp\htdocs\Kariana Website\.htaccess` lines 38-52:
```apache
# 3. Handle Static Assets directly if file or directory exists
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Also check inside public/ directory if requested without /public/ prefix
RewriteCond %{DOCUMENT_ROOT}/public/$1 -f
RewriteRule ^(.*)$ public/$1 [L]

# 4. Front Controller Routing
# Pass all remaining requests to index.php with original query string preserved
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Why it fails under subfolders:
1. In XAMPP or any Apache subfolder setup, the site URL is `http://localhost/Kariana%20Website/`.
2. Apache's `%{DOCUMENT_ROOT}` is `C:/xampp/htdocs`.
3. The browser requests `http://localhost/Kariana%20Website/assets/css/main.css`.
4. The rewrite engine in `.htaccess` captures `$1` as `assets/css/main.css`.
5. The condition `RewriteCond %{DOCUMENT_ROOT}/public/$1 -f` evaluates:
   `C:/xampp/htdocs/public/assets/css/main.css` $\implies$ **FALSE** (does not exist!).
6. The rule fails to rewrite to `public/assets/css/main.css`.
7. The request falls through to Rule 4, rewriting to `index.php`.
8. `index.php` tries to match route `/assets/css/main.css`, fails, and serves a 404 HTML document with status 404 instead of CSS.

### 3.2 Apache Per-Directory Behavior Solution
In Apache HTTP Server `mod_rewrite`, when `RewriteCond` is evaluated within an `.htaccess` context:
- Any file path argument that does NOT begin with a slash (`/`) or drive letter (`C:/`) is evaluated **relative to the directory containing the `.htaccess` file**.
- Therefore, `RewriteCond public/$1 -f` tests:
  `<directory_of_.htaccess>/public/$1`.
- If `.htaccess` is in `C:\xampp\htdocs\Kariana Website\`, it checks:
  `C:\xampp\htdocs\Kariana Website\public\assets\css\main.css` $\implies$ **TRUE**!
- If `.htaccess` is in `/home/user/public_html/` (Hostinger / cPanel root), it checks:
  `/home/user/public_html/public/assets/css/main.css` $\implies$ **TRUE**!

By pairing `RewriteCond public/$1 -f` with a fallback `RewriteCond %{DOCUMENT_ROOT}/public/$1 -f`, the rule behaves identically and flawlessly across both deployment types.

### 3.3 Protection of Internal Application Directories
In `.htaccess` line 36:
`RewriteRule ^(app|config|core|database|storage|\.agents)/ - [F,L,NC]`
We must also include `tests/` and `.git/` in the protected directory list to ensure test harnesses cannot be run directly via Apache.

### 3.4 Exact Proposed Code for `.htaccess`
Replace lines 33-52 in `c:\xampp\htdocs\Kariana Website\.htaccess`:

```apache
<<<<
RewriteEngine On

# Strictly protect internal application code directories from direct web access
RewriteRule ^(app|config|core|database|storage|\.agents)/ - [F,L,NC]

# 3. Handle Static Assets directly if file or directory exists
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Also check inside public/ directory if requested without /public/ prefix
RewriteCond %{DOCUMENT_ROOT}/public/$1 -f
RewriteRule ^(.*)$ public/$1 [L]

# 4. Front Controller Routing
# Pass all remaining requests to index.php with original query string preserved
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
====
RewriteEngine On

# Strictly protect internal application directories from direct web access
RewriteRule ^(app|config|core|database|storage|tests|\.agents|\.git)/ - [F,L,NC]

# 3. Handle Static Assets directly if file or directory exists in current location
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Check inside public/ directory for requested asset (supporting both subfolder and root domain)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_URI} !public/ [NC]
RewriteCond public/$1 -f [OR]
RewriteCond %{DOCUMENT_ROOT}/public/$1 -f
RewriteRule ^(.*)$ public/$1 [L]

# 4. Front Controller Routing
# Pass all remaining requests to index.php with original query string preserved
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
>>>>
```

---

## 4. Synthesis of Audit Findings for Downstream Implementation

For complete context and seamless Milestone M1 approval, the downstream implementation agent should also be aware of the following findings from Auditor M1 and Reviewer 1:

1. **`index.php` Parse Error**:
   - Location: `index.php` lines 207-214.
   - Issue: The `/api/verify_m1` closure was left unclosed, causing PHP to throw:
     `Parse error: syntax error, unexpected variable "$app", expecting ")"` on line 273.
   - Fix: Properly close the route closure with `});`.
2. **Layout Compliance Violation**:
   - Issue: `index.php` line 208 loaded `.agents/worker_m1/test_m1.php`. Agent folders must strictly contain metadata only.
   - Fix: Move `test_m1.php` into `tests/unit/test_m1.php` or `tests/m1_verify.php` and remove any reference to `.agents/` in production source code.
3. **`core/Router.php` Reflection Named Type**:
   - Location: `core/Router.php:211`.
   - Issue: `$param->getType()->getName()` crashes on PHP 8.2 union types (`\ReflectionUnionType`).
   - Fix: Use `($type instanceof \ReflectionNamedType) ? $type->getName() : null;`.
4. **`core/Controller.php` CSRF Scalar Guard**:
   - Location: `core/Controller.php:58`.
   - Issue: Injected array in `csrf_token[]` causes `TypeError` in `Csrf::validate(?string $token)`.
   - Fix: Guard `$token = is_string($token) ? $token : null;`.

---

## 5. Verification Plan for Downstream Implementer

1. **Model OrderBy Security**:
   - Run `php -r "require 'core/Autoloader.php'; Core\Autoloader::register(); \$m = new class extends Core\Model { protected string \\\$table = 'users'; }; try { \$m->all('id ASC, (SELECT SLEEP(1))'); echo 'FAIL'; } catch (InvalidArgumentException \$e) { echo 'PASS: ' . \$e->getMessage(); }"`
   - Must output `PASS: Invalid ORDER BY clause...`.
2. **Router.php Sensitive File Blocking**:
   - Request `http://localhost:8015/database/schema.sql` $\implies$ Must return HTTP 403.
   - Request `http://localhost:8015/.agents/worker_m1/handoff.md` $\implies$ Must return HTTP 403.
   - Request `http://localhost:8015/assets/css/main.css` $\implies$ Must return HTTP 200 with `Content-Type: text/css; charset=UTF-8`.
3. **Subfolder `.htaccess` Static Asset Delivery**:
   - Request `http://localhost/Kariana%20Website/assets/css/main.css` $\implies$ Must return HTTP 200 with CSS content, not 404 HTML.
