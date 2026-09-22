# Adversarial Challenge & Stress-Test Report — Milestone M1

**Target**: Milestone M1 Deliverables (Framework Core, Security, Database)  
**Critic**: Reviewer 2 (`reviewer`, `critic`)  
**Date**: 2026-09-22  
**Working Directory**: `c:\xampp\htdocs\Kariana Website\.agents\reviewer_m1_2`

---

## Challenge Summary

**Overall risk assessment**: **CRITICAL**

While foundational security mechanisms (Bcrypt, `hash_equals`, `utf8mb4`, PDO prepare emulation off) are properly designed, adversarial stress-testing surfaced critical structural fragility, a live application crash, an SQL injection vector in the query builder, and edge-case type vulnerabilities in CSRF validation.

---

## Challenges

### [Critical] Challenge 1: Single Point of Syntax Failure in Central Route Dispatcher
- **Assumption challenged**: Assumed that route definitions in `index.php` will be cleanly updated without destabilizing the whole application.
- **Attack / Failure scenario**:
  An incomplete edit in `index.php` (e.g. omitting a closure closing `});` when injecting a route) breaks PHP AST compilation for the entire file before any exception handler or router execution can occur.
- **Blast radius**:
  100% of incoming web traffic to all endpoints (homepage, API, assets routed through index, error pages) fails immediately with HTTP 500 / Parse Error. The application is completely offline.
- **Mitigation**:
  Keep `index.php` strictly as a thin bootstrap file (5-10 lines) and isolate route definitions into modular route files (e.g. `app/routes.php` or `routes/web.php`, `routes/api.php`) with syntax validation prior to dispatch.

---

### [High] Challenge 2: SQL Injection via Arbitrary Expression in `$orderBy`
- **Assumption challenged**: Assumed that caller code will only supply trusted column names to `Model::where()` and `Model::all()`.
- **Attack scenario**:
  An attacker passes a malicious parameter in a URL query string (e.g., `?sort=(CASE+WHEN+(SELECT+1)=1+THEN+id+ELSE+created_at+END)` or time-delay injection `?sort=(SELECT+1+FROM+(SELECT+SLEEP(5))a)`). Because `core/Model.php` line 76 directly concatenates `ORDER BY {$orderBy}` without sanitization, arbitrary SQL expressions execute in MariaDB.
- **Blast radius**:
  Data exfiltration (blind Boolean or time-based SQL injection against `users`, `admissions`, or settings tables), denial of service via sleep delays or heavy subqueries.
- **Mitigation**:
  Enforce a strict regex on `$orderBy` (`/^[a-zA-Z0-9_]+(\s+(ASC|DESC))?$/i`) or validate against an explicit whitelist of allowed columns for each model.

---

### [Medium] Challenge 3: Type Confusion / Fatal Error in CSRF Validation
- **Assumption challenged**: Assumed that `csrf_token` input will always be scalar string or null.
- **Attack scenario**:
  An attacker submits a POST request with `csrf_token[]=` (an array rather than a string). When `Request::post('csrf_token')` returns an array, passing it to `Csrf::validate(?string $token)` violates PHP 8.2 strict type checking, throwing an uncaught `TypeError: Core\Csrf::validate(): Argument #1 ($token) must be of type ?string, array given`.
- **Blast radius**:
  Uncaught 500 error triggered on demand by external users without reaching authentication or business logic handlers, enabling low-cost denial of service against state-changing endpoints.
- **Mitigation**:
  Use `mixed $token` in `Csrf::validate()` and verify `is_string($token)` before processing.

---

### [Medium] Challenge 4: Environment Leakage on Database Failure
- **Assumption challenged**: Assumed that database connection exceptions are sanitized for production.
- **Attack scenario**:
  If the MariaDB server is temporarily unreachable, has exceeded max connections, or has misconfigured permissions, `Core\Database::getInstance()` catches `PDOException` and throws:
  `new \RuntimeException("... " . $e->getMessage())`.
  If debug mode is enabled (which defaults to true in `config/app.php`), full connection strings, internal server IPs, and system usernames are displayed in HTML to unauthorized clients.
- **Blast radius**:
  Infrastructure reconnaissance and information disclosure.
- **Mitigation**:
  Do not include raw `$e->getMessage()` in user-facing exceptions. Provide generic error messages and log raw messages to internal system logs only.

---

### [Low] Challenge 5: Host Header Injection in Base URL Detection
- **Assumption challenged**: Assumed `$_SERVER['HTTP_HOST']` is trusted.
- **Attack scenario**:
  In `core/Request.php` line 80:
  `$host = $this->serverParams['HTTP_HOST'] ?? 'localhost';`
  An attacker sending spoofed `Host: evil.com` header may cause dynamically generated links, canonical URLs, and redirects to point to `evil.com`.
- **Blast radius**:
  Cache poisoning, password reset poisoning (when implemented), or phishing via open redirects.
- **Mitigation**:
  Validate `HTTP_HOST` against an `app.allowed_hosts` configuration whitelist.

---

## Stress Test Results

| Test Scenario | Expected Behavior | Actual Behavior | Result |
|---|---|---|---|
| Request to `/api/districts` over HTTP | Returns 200 with 64 districts JSON | Fatal Parse Error in `index.php` line 273 | **FAIL** |
| Request to `/api/verify_m1` over HTTP | Returns verification suite JSON | Parse Error (prior to fix) or works if standalone | **FAIL** |
| CSRF validation with invalid token string | Returns `false` without exception | Returns `false` | **PASS** |
| CSRF validation with array input `csrf_token[]` | Returns `false` gracefully | Would throw fatal `TypeError` under PHP 8.2 | **FAIL** |
| Model condition with malicious column name `id; DROP TABLE users;` | Rejects/ignores non-alphanumeric column | Filtered out by `preg_match('/^[a-zA-Z0-9_]+$/', $col)` | **PASS** |
| Model `where()` with malicious `$orderBy` injection | Rejects or sanitizes order expression | Directly concatenated into SQL | **FAIL** |
| Bengali slug preservation `কুরআন-তিলাওয়াত ও তাজবীদ শিক্ষা!?` | Produces `কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা` | Produces `কুরআন-তিলাওয়াত-ও-তাজবীদ-শিক্ষা` | **PASS** |
| Unicode router pattern `/blog/{slug}` matching Bengali path | Captures multi-byte UTF-8 string | Captures Bengali string via PCRE `/u` | **PASS** |
| Session fixation on login (`setUser`) | Regenerates session ID | Calls `session_regenerate_id(true)` | **PASS** |

---

## Unchallenged Areas
- Full production load and concurrency on port 8015: deferred to Milestone M6 (E2E & Multi-Device verification).
- Payment gateway and external API timeouts: not in Milestone M1 scope.
