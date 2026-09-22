# Handoff Report — Challenger M1 Gen2 #2 (Empirical Adversarial Audit)

**Agent:** Challenger M1 Gen2 #2 (`critic`, `specialist`, `empirical challenger`)  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\challenger_m1_gen2_2\`  
**Target:** Milestone M1 Remediation Verification  
**Date:** 2026-09-22T21:12:00+06:00  
**Handoff Type:** Hard Handoff  
**Explicit Verdict:** **CONFIRM CORRECTNESS**

---

## 1. Observation

### 1.1 Direct Observation of `Core\Model` OrderBy Validation Logic
In `core/Model.php` (lines 75–89):
```php
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
```
In `core/Model.php` (lines 106–115):
```php
public function all(string $orderBy = '', int $limit = 0, int $offset = 0): array
{
    if (!empty($orderBy)) {
        $trimmedOrder = trim($orderBy);
        if (!preg_match('/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i', $trimmedOrder)) {
            throw new \InvalidArgumentException("Invalid ORDER BY clause: {$orderBy}");
        }
    }
    return $this->where([], $orderBy, $limit, $offset);
}
```

### 1.2 Direct Observation of `Core\Csrf` Mixed-Type Validation Logic
In `core/Csrf.php` (lines 39–47):
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
In `core/Controller.php` (lines 56–60):
```php
protected function validateCsrf(Request $request): bool
{
    $token = $request->post('csrf_token') ?? $request->getHeader('X-CSRF-TOKEN');
    return Csrf::validate($token);
}
```

### 1.3 Direct Observation of Adversarial Test Suite Execution (`tests/adversarial_m1_gen2.php`)
Executed empirical challenge suite across 74 distinct test scenarios:
- **Total Tests Executed:** 74
- **Passed:** 74
- **Failed:** 0
- **Overall Verdict:** `CONFIRM CORRECTNESS`

Verbatim empirical results by section:

#### Section 1: Model OrderBy SQL Injection & Whitelisting Stress (38/38 PASS)
1. Mandated Payload 1: `id ASC, (SELECT SLEEP(1))`  
   $\to$ Rejected in both `where()` and `all()` with `\InvalidArgumentException: Invalid ORDER BY clause: id ASC, (SELECT SLEEP(1))` (`[PASS]`).
2. Mandated Payload 2: `id; DROP TABLE users;`  
   $\to$ Rejected in both `where()` and `all()` with `\InvalidArgumentException: Invalid ORDER BY clause: id; DROP TABLE users;` (`[PASS]`).
3. Mandated Payload 3: `1' UNION SELECT 1,2,3,4,5,6,7,8,9,10,11,12,NOW() -- `  
   $\to$ Rejected in both `where()` and `all()` with `\InvalidArgumentException: Invalid ORDER BY clause: 1' UNION SELECT ...` (`[PASS]`).
4. Mandated Payload 4: `id ASC SLEEP col`  
   $\to$ Rejected in both `where()` and `all()` with `\InvalidArgumentException: Invalid ORDER BY clause: id ASC SLEEP col` (`[PASS]`).
5. Extended Adversarial Attack Vectors:
   - `id ASC, SLEEP(1)` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC, (SELECT 1 FROM users)` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC, 1=1` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC, BENCHMARK(1000000,MD5(1))` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC; SELECT 1` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC--` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC#` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC/*comment*/` $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC,` (trailing comma) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `,id ASC` (leading comma) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC,,name DESC` (empty middle clause) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC\nSLEEP\ncol` (newline evasion) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `col1 col2` (adjacent column space stuffing) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `col1 ASC col2 DESC` (missing comma between clauses) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `(CASE WHEN (1=1) THEN id ELSE name END)` (conditional case) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `extractvalue(1,concat(0x7e,version()))` (XPath error) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `updatexml(1,concat(0x7e,version()),1)` (XML error) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC AND 1=1` (boolean AND) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC OR 1=1` (boolean OR) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC UNION SELECT 1` (UNION keyword) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id' OR '1'='1` (quote tautology) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `prayer_districts.col.subcol ASC` (triple dot spec) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC, col ASC SLEEP` (trailing keyword stuffing) $\to$ `\InvalidArgumentException` (`[PASS]`)
   - `id ASC; DROP TABLE prayer_districts;` (stacked drop) $\to$ `\InvalidArgumentException` (`[PASS]`)
6. Oracle Legitimate Clause Verification (Zero False Positives):
   - `id` $\to$ Returned 3 rows (`[PASS]`)
   - `id ASC` $\to$ Returned 3 rows (`[PASS]`)
   - `id DESC` $\to$ Returned 3 rows (`[PASS]`)
   - `id asc` $\to$ Returned 3 rows (`[PASS]`)
   - `id desc` $\to$ Returned 3 rows (`[PASS]`)
   - `prayer_districts.id ASC` $\to$ Returned 3 rows (`[PASS]`)
   - `prayer_districts.id DESC` $\to$ Returned 3 rows (`[PASS]`)
   - `division_bn ASC, id DESC` $\to$ Returned 3 rows (`[PASS]`)
   - `name_en ASC, division_bn DESC, id ASC` $\to$ Returned 3 rows (`[PASS]`)
   - `""` (empty string) $\to$ Returned 3 rows without ORDER BY clause (`[PASS]`)

#### Section 2: CSRF Non-Scalar Type Handling & TypeError Immunity (21/21 PASS)
1. `Csrf::validate(null)` $\to$ Safely returned `bool(false)` (`[PASS]`)
2. `Csrf::validate([])` $\to$ Safely returned `bool(false)` (`[PASS]`)
3. `Csrf::validate(['csrf_token' => 'active_token'])` $\to$ Safely returned `bool(false)` (`[PASS]`)
4. `Csrf::validate(['token1', 'token2'])` $\to$ Safely returned `bool(false)` (`[PASS]`)
5. `Csrf::validate([['nested' => 'array']])` $\to$ Safely returned `bool(false)` (`[PASS]`)
6. `Csrf::validate(0)` $\to$ Safely returned `bool(false)` (`[PASS]`)
7. `Csrf::validate(1)` $\to$ Safely returned `bool(false)` (`[PASS]`)
8. `Csrf::validate(12345)` $\to$ Safely returned `bool(false)` (`[PASS]`)
9. `Csrf::validate(-999)` $\to$ Safely returned `bool(false)` (`[PASS]`)
10. `Csrf::validate(PHP_INT_MAX)` $\to$ Safely returned `bool(false)` (`[PASS]`)
11. `Csrf::validate(0.0)` $\to$ Safely returned `bool(false)` (`[PASS]`)
12. `Csrf::validate(3.14159)` $\to$ Safely returned `bool(false)` (`[PASS]`)
13. `Csrf::validate(true)` $\to$ Safely returned `bool(false)` (`[PASS]`)
14. `Csrf::validate(false)` $\to$ Safely returned `bool(false)` (`[PASS]`)
15. `Csrf::validate(new \stdClass())` $\to$ Safely returned `bool(false)` (`[PASS]`)
16. `Csrf::validate((object)['csrf_token' => 'token'])` $\to$ Safely returned `bool(false)` (`[PASS]`)
17. `Csrf::validate(new class { public function __toString() { return 'token'; } })` $\to$ Safely returned `bool(false)` (`[PASS]`)
18. `Controller::validateCsrf()` with `$_POST['csrf_token'] = ['nested' => 'array']` $\to$ Safely returned `bool(false)`, 0 TypeErrors (`[PASS]`)
19. `Controller::validateCsrf()` with `$_POST['csrf_token'] = 99999` $\to$ Safely returned `bool(false)`, 0 TypeErrors (`[PASS]`)
20. `Controller::validateCsrf()` with unset / `null` token $\to$ Safely returned `bool(false)`, 0 TypeErrors (`[PASS]`)
21. `Csrf::validate($activeToken)` with authentic 64-char hex session token $\to$ Safely returned `bool(true)` (`[PASS]`)

#### Section 3: Sensitive File HTTP Access Security (15/15 PASS)
1. `GET http://localhost/Kariana%20Website/.env` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
2. `GET http://localhost/Kariana%20Website/.git/config` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
3. `GET http://localhost/Kariana%20Website/database/schema.sql` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
4. `GET http://localhost/Kariana%20Website/tests/unit/test_m1.php` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
5. `GET http://localhost/Kariana%20Website/config/app.php` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
6. `GET http://localhost/Kariana%20Website/config/database.php` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
7. `GET http://localhost/Kariana%20Website/core/Model.php` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
8. `GET http://localhost/Kariana%20Website/core/Csrf.php` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
9. `GET http://localhost/Kariana%20Website/storage/` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
10. `GET http://localhost/Kariana%20Website/app/Controllers/HomeController.php` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
11. `GET http://localhost/Kariana%20Website/.agents/worker_m1_gen2/handoff.md` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
12. `GET http://localhost/Kariana%20Website/composer.json` $\to$ `HTTP/1.1 403 Forbidden` (`[PASS]`)
13. `GET http://localhost/Kariana%20Website/assets/css/main.css` $\to$ `HTTP/1.1 200 OK` (`[PASS]`)
14. `GET http://localhost/Kariana%20Website/` $\to$ `HTTP/1.1 200 OK` (`[PASS]`)
15. `GET http://localhost/Kariana%20Website/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা` $\to$ `HTTP/1.1 200 OK` (`[PASS]`)

---

## 2. Logic Chain

1. **Model SQL Injection Immunity**:
   - In `Core\Model::where()` and `Core\Model::all()`, the worker implemented a two-tier defense mechanism:
     - Tier 1: Top-level character restriction: `/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`. This eliminates all syntax control characters (parentheses `()`, quotes `' "`, semicolons `;`, comments `-- # /* */`, operators `+ - * / < > =`, etc.) before any query construction occurs.
     - Tier 2: Per-clause structural segmentation: The trimmed expression is exploded on commas `,` and each individual segment is checked against `/^[a-zA-Z0-9_]+(?:\.[a-zA-Z0-9_]+)?(?:\s+(?:ASC|DESC))?$/i`. This strictly requires each clause to be either an identifier (e.g. `col`) or table-qualified identifier (e.g. `table.col`), followed optionally by a single whitespace and `ASC` or `DESC`.
   - In Observation 1.3 (Section 1), all 4 mandated injection payloads (`id ASC, (SELECT SLEEP(1))`, `id; DROP TABLE users;`, `1' UNION SELECT ...`, `id ASC SLEEP col`) and 24 additional attack vectors failed the regex checks and were safely rejected by throwing `\InvalidArgumentException`. No malicious SQL was executed or reached the PDO driver.
   - The 10 legitimate sorting oracles confirmed that valid ASC/DESC, compound comma-separated sorting, table-qualified identifiers, and empty strings are processed normally without false positives.

2. **CSRF Mixed-Type Robustness**:
   - Observation 1.2 demonstrates that `Core\Csrf::validate(mixed $token)` declares its parameter as `mixed $token`.
   - The first line of `validate()` performs a strict guard clause: `if (!is_string($token) || empty($token)) { return false; }`.
   - In Observation 1.3 (Section 2), 17 non-string types (arrays, nested arrays, integers, floats, booleans, objects, stringable classes) and 3 controller POST simulations all returned `bool(false)` without throwing any `TypeError` or PHP fatal errors.
   - Legitimate 64-character hex tokens generated via `Csrf::token()` continue to validate as `bool(true)` via timing-safe `hash_equals()`.

3. **HTTP Access Restriction on Sensitive Assets**:
   - In `.htaccess`, the dual protection of `<FilesMatch>` directives and the mod_rewrite rule `RewriteRule ^(app|config|core|database|storage|tests|\.agents|\.git)/ - [F,L,NC]` denies all direct web access to internal source trees, database schemas, test scripts, and agent directories.
   - In Observation 1.3 (Section 3), direct HTTP GET requests to 12 sensitive internal paths (`.env`, `.git/config`, `database/schema.sql`, `tests/unit/test_m1.php`, `config/app.php`, `core/Model.php`, `storage/`, `composer.json`, `.agents/...`) all consistently returned `HTTP/1.1 403 Forbidden`.
   - Legitimate public assets (`/assets/css/main.css`) and public dynamic routes (`/`, `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`) successfully returned `HTTP/1.1 200 OK`.

---

## 3. Caveats

- **Database Engine**: Empirical tests were executed against MariaDB 10.4+ on port 3306 using the seeded `kariana_portal` database.
- **Apache Web Server**: HTTP requests were verified against Apache 2.4 on port 80. The CLI server router (`router.php`) provides an identical layered defense (`$protectedDirs`, `$blockedExtensions`, dotfile blocking, and path traversal rejection) for standalone hosting on port 8015.
- No other caveats.

---

## 4. Conclusion

All challenge tasks have been thoroughly and empirically tested with zero failures:
1. `Core\Model` safely rejects all SQL injection payloads in `$orderBy` with `\InvalidArgumentException`, while allowing valid sorting expressions.
2. `Core\Csrf` handles all non-string and malformed inputs gracefully with `bool(false)` and zero `TypeError` exceptions.
3. Sensitive internal directories, configuration files, database schemas, and agent metadata are strictly blocked from HTTP access with `HTTP 403 Forbidden`.

Explicit Verdict: **CONFIRM CORRECTNESS**

Milestone M1 remediation is verified, hardened, and ready for production and subsequent milestones.

---

## 5. Verification Method

To independently reproduce this verification:

1. **Execute Full Adversarial Challenge Suite**:
   Open a browser or curl the adversarial test runner:
   ```bash
   curl http://localhost/Kariana%20Website/api/verify_m1
   ```
   *Expected Output*: JSON response with `"success": true` and all assertions showing `"passed": true`.

2. **Verify HTTP 403 Forbidden on Sensitive Files**:
   ```bash
   curl -i http://localhost/Kariana%20Website/.env
   curl -i http://localhost/Kariana%20Website/.git/config
   curl -i http://localhost/Kariana%20Website/database/schema.sql
   curl -i http://localhost/Kariana%20Website/tests/unit/test_m1.php
   curl -i http://localhost/Kariana%20Website/config/app.php
   curl -i http://localhost/Kariana%20Website/core/Model.php
   curl -i http://localhost/Kariana%20Website/.agents/worker_m1_gen2/handoff.md
   ```
   *Expected Output*: `HTTP/1.1 403 Forbidden` for every command.

3. **Verify Public Endpoints Deliver 200 OK**:
   ```bash
   curl -i http://localhost/Kariana%20Website/
   curl -i http://localhost/Kariana%20Website/assets/css/main.css
   ```
   *Expected Output*: `HTTP/1.1 200 OK`.
