# Changes Report — worker_port8015_fix

## Summary of Actions Taken

### 1. Stale Process Identification and Termination on Port 8015
- **Observation:** Port 8015 was occupied by a stale Gen 1 PHP CLI server process (PID `21688`) that was started prior to the hardened `router.php` being implemented.
- **Action:** Inspected port 8015 via TCP connection query and terminated PID `21688` using `taskkill /F /PID 21688`. Verified that port 8015 was completely freed (`No process found on port 8015`).

### 2. Tightened `router.php`
- **File Modified:** `c:\xampp\htdocs\Kariana Website\router.php`
- **Lines Changed:** Lines 33–42
- **Change Details:**
  Added an explicit block for direct requests to `router.php` (`strtolower(basename($decodedPath)) === 'router.php'`), ensuring that attempts to access `http://localhost:8015/router.php` return `HTTP 403 Forbidden` rather than exposing router internals or falling through.
- **Verification:** Verified via `php -l router.php` (no syntax errors) and HTTP probe returning `HTTP 403 Forbidden`.

### 3. Server Re-launch in Daemon Mode on `0.0.0.0:8015`
- **Command:** `php -S 0.0.0.0:8015 router.php`
- **Working Directory:** `c:\xampp\htdocs\Kariana Website`
- **Daemon Task ID:** `8802dd47-1ab8-4077-910a-38446a7ed72a/task-80`
- **Binding:** `0.0.0.0:8015` (Universal multi-device Wi-Fi binding, accessible via `http://localhost:8015` and `http://192.168.0.100:8015`).
- **Runtime Log:** `[Tue Sep 22 21:20:33 2026] PHP 8.2.12 Development Server (http://0.0.0.0:8015) started`.

### 4. HTTP Endpoint Verification
All requested and critical endpoints were probed and confirmed:
1. `http://localhost:8015/database/schema.sql` $\implies$ **HTTP 403 Forbidden** (Blocked)
2. `http://localhost:8015/database/seed.php` $\implies$ **HTTP 403 Forbidden** (Blocked)
3. `http://localhost:8015/tests/unit/test_m1.php` $\implies$ **HTTP 403 Forbidden** (Blocked)
4. `http://localhost:8015/.agents/worker_m1_gen2/handoff.md` $\implies$ **HTTP 403 Forbidden** (Blocked)
5. `http://localhost:8015/assets/css/main.css` $\implies$ **HTTP 200 OK** (Served with correct MIME `text/css; charset=UTF-8` and font definitions)
6. `http://localhost:8015/api/verify_m1` $\implies$ **HTTP 200 OK** (All 11 verification tests pass: autoloader, Bengali numerals, 9-step slug algorithm, CSRF mixed-type safe validation, UTF8MB4 PDO, 12 normalized tables, 64 IFB districts, bcrypt admin auth, orderby SQL injection defense, PCRE /u Unicode routing, and Arabic font asset)
7. `http://localhost:8015/router.php` $\implies$ **HTTP 403 Forbidden** (Blocked)
8. `http://localhost:8015/config/database.php` $\implies$ **HTTP 403 Forbidden** (Blocked)
9. `http://localhost:8015/core/Database.php` $\implies$ **HTTP 403 Forbidden** (Blocked)
10. `http://localhost:8015/.env` $\implies$ **HTTP 403 Forbidden** (Blocked)
11. `http://localhost:8015/` $\implies$ **HTTP 200 OK** (Rendered homepage with emerald/gold theme)
12. `http://localhost:8015/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা` $\implies$ **HTTP 200 OK** (Clean Bengali Unicode slug routed to article)
