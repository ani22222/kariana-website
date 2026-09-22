# Handoff Report — Explorer 2 (Architecture & PHP 8.2 MVC Framework)

**Sender**: Explorer 2 (`1bc83657-5a37-4042-9f43-fc9b47350ec9`)  
**Recipient**: Orchestrator (`5a011e50-ed48-4482-b181-5ca5e13d7062`)  
**Timestamp**: 2026-09-22T20:09:00+06:00  
**Status**: Complete (Hard Handoff)  
**Primary Artifact**: `c:\xampp\htdocs\Kariana Website\.agents\explorer_2\analysis.md`  

---

## 1. Observation

1. **Workspace and Environment**:
   - `c:\xampp\properties.ini` (lines 5, 8, 16):
     ```ini
     5: base_stack_version=8.2.12-0
     8: apache_server_port=80
     16: mysql_port=3306
     ```
     Observed: Local system runs PHP 8.2.12, Apache 2.4, MySQL 3306 on Windows x64.
   - `c:\xampp\php\php.ini` (lines 927, 930, 936, 938, 944):
     ```ini
     927: extension=curl
     930: extension=fileinfo
     936: extension=mbstring
     938: extension=mysqli
     944: extension=pdo_mysql
     ```
     Observed: Both `pdo_mysql` and `mbstring` (vital for UTF-8 Bengali Unicode parsing) are pre-installed and enabled.
   - `c:\xampp\apache\conf\httpd.conf` (lines 163, 273):
     ```apache
     163: LoadModule rewrite_module modules/mod_rewrite.so
     273: AllowOverride All
     ```
     Observed: `mod_rewrite` is loaded and `AllowOverride All` is active for `c:/xampp/htdocs`, permitting `.htaccess` routing rules to function without server reconfiguration.
   - Port Registry in User Rules:
     Port 8014 is registered to `Kariana Quran ReMakiking In In design`.
     Port 8015 is assigned to `Kariana Website` (this project), requiring binding to `0.0.0.0` for local (`http://localhost:8015`) and multi-device Wi-Fi access (`http://192.168.0.100:8015`).
   - Sibling Asset Discovery:
     `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\Font\Font015-DETAILS.txt` (lines 1-5):
     ```text
     1: Rase Made Font015.ttf — সম্পূর্ণ ডিটেইল (সারা-কুরআন + ১২ তাজভীদ টপ-সিম্বল)
     2: Internal name: AASQv10 Regular | Version 10.00 | 2026-08-19
     ```
     Observed: The exact verified Kariana Quran font files (`AAR-SQ-002.ttf`, `AAR-SQ-003.ttf`) reside locally in this adjacent directory.
   - Greenfield Status:
     Workspace directory `c:\xampp\htdocs\Kariana Website\` contains only `ORIGINAL_REQUEST.md` and `.agents/`. No existing PHP codebase exists yet.

---

## 2. Logic Chain

1. **Shared Hosting Feasibility**:
   - *Observation*: Hostinger, cPanel, and standard shared web hosts do not provide persistent Node.js daemons, SSH root access, or custom DocumentRoot modification out-of-the-box for basic tiers.
   - *Deduction*: The architecture cannot rely on Composer at runtime or Node.js/Vite build pipelines. A zero-dependency native PSR-4 autoloader (`Core\Autoloader`) coupled with pre-compiled/CDN Tailwind CSS and standalone Alpine.js delivers 100% drag-and-drop shared hosting readiness.
2. **Dynamic Subdirectory vs. Root Port 8015 Portability**:
   - *Observation*: The project will be accessed under XAMPP as `http://localhost/Kariana%20Website/`, under CLI server as `http://localhost:8015/` and `http://192.168.0.100:8015/`, and on production as `https://karianabd.com/`.
   - *Deduction*: Hardcoding paths in controllers or views will cause asset and routing breakage. `Core\Request` must dynamically detect `dirname($_SERVER['SCRIPT_NAME'])`, strip it from the request URI, and expose `getBaseUrl()` for clean asset and route generation.
3. **Bengali Unicode Slug Decoding & Matching**:
   - *Observation*: Bengali characters (`/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`) are percent-encoded by browsers into multi-byte UTF-8 sequences.
   - *Deduction*: Using standard ASCII regex or failing to call `rawurldecode()` will cause route matching to fail. The router must execute `rawurldecode()` on the request path and compile route parameters using PCRE regex with the `/u` flag (`#^/blog/(?P<slug>[^/]+)$#u`).
4. **Database UTF8MB4 & Security Integrity**:
   - *Observation*: Arabic Quranic text, Tajweed ligatures, and Bengali conjuncts require 4 bytes per character. SQL injections occur when query strings are concatenated.
   - *Deduction*: The database singleton (`Core\Database`) must connect with `charset=utf8mb4`, execute `SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci`, disable emulated prepares (`PDO::ATTR_EMULATE_PREPARES => false`), and force 100% prepared statement bindings across all models.
5. **State & CSRF Protection**:
   - *Observation*: Form submissions (e.g. online admissions, admin actions) are prone to CSRF attacks and session hijacking.
   - *Deduction*: `Core\Csrf` and `Core\Session` must enforce cryptographic tokens via `random_bytes(32)`, validate them via `hash_equals()`, enforce `HttpOnly` and `SameSite=Lax` cookies, and regenerate session IDs on state transitions.

---

## 3. Caveats

- **External Font CDNs vs. Offline**: The primary font stack uses Google Fonts (`Hind Siliguri`, `Amiri`) via CDN for zero local footprint. If completely offline, standard system fallback fonts (`SolaimanLipi`, `Kalpurush`, `sans-serif`) are configured. Local font copies of `AASQv10` can be placed into `assets/fonts/` if offline Quranic font rendering is strictly required.
- **MySQL Database Existence**: In local development, the MySQL database `kariana_db` must be created in MySQL/MariaDB (via `CREATE DATABASE kariana_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`) prior to running migrations.

---

## 4. Conclusion

A comprehensive architectural blueprint has been engineered and documented in `c:\xampp\htdocs\Kariana Website\.agents\explorer_2\analysis.md`. The design fulfills every architectural requirement from `ORIGINAL_REQUEST.md`:
- Pure PHP 8.2 MVC structure with zero-vendor native autoloader.
- Apache `.htaccess` rules and CLI `router.php` supporting dual deployment (root domain, dedicated port 8015 on `0.0.0.0`, and XAMPP subdirectories).
- Robust Bengali Unicode slug routing with PCRE `/u` regex.
- Complete 12-table MySQL PDO database schema (`database/schema.sql`) configured with `utf8mb4_unicode_ci`.
- Royal Islamic Emerald Green (`#064e3b`, `#047857`) and Warm Quranic Gold (`#d97706`) styling specifications with zero-build Tailwind CSS and Alpine.js.
- Complete CSRF middleware, session security, and authentication guards.

The implementation team can immediately begin generating the codebase based directly on this blueprint.

---

## 5. Verification Method

To independently verify this architectural specification:
1. **Inspect Analysis Document**:
   View `c:\xampp\htdocs\Kariana Website\.agents\explorer_2\analysis.md`. Verify that all classes (`Autoloader`, `App`, `Request`, `Response`, `Router`, `Database`, `Session`, `Csrf`, `BengaliHelper`) have concrete code implementations and the database schema is fully defined.
2. **Test Base URL & Bengali Slug Regex**:
   In any PHP 8.2 interpreter, verify that `#^/blog/(?P<slug>[^/]+)$#u` accurately matches `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা` and captures the exact Bengali slug `সহজ-পদ্ধতিতে-কুরআন-শেখা`.
3. **Dedicated Port Verification**:
   Inspect `router.php` specification in Section 2.3 of `analysis.md` to confirm it serves static assets when files exist and routes dynamic endpoints to `index.php` when launched via `php -S 0.0.0.0:8015 router.php`.
