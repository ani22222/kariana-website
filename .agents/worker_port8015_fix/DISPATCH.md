## 2026-09-22T15:14:29Z

You are Worker Port 8015 Fix for Kariana Quran.
Your working directory: c:\xampp\htdocs\Kariana Website\.agents\worker_port8015_fix\

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

MANDATORY READING:
- c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
- c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen2\PROJECT.md
- c:\xampp\htdocs\Kariana Website\.agents\reviewer_m1_gen2_2\handoff.md
- c:\xampp\htdocs\Kariana Website\router.php

TASKS:
1. Inspect running processes on port 8015 using `Get-NetTCPConnection -LocalPort 8015 -ErrorAction SilentlyContinue`.
2. Kill any stale process listening on port 8015 (`Stop-Process -Id <pid> -Force`).
3. If `router.php` needs any further tightening, check `router.php`. Note that `router.php` line 34 blocks `$protectedDirs = ['app', 'config', 'core', 'database', 'storage', 'tests', '.agents', '.git', 'vendor'];`. Ensure all sensitive files and directories are cleanly intercepted and return HTTP 403 Forbidden.
4. Launch the PHP CLI server in daemon mode on port 8015:
   `run_command(CommandLine="php -S 0.0.0.0:8015 router.php", Cwd="c:\\xampp\\htdocs\\Kariana Website", IsDaemon=true, WaitMsBeforeAsync=2000)`
5. Verify via HTTP requests to `http://localhost:8015`:
   - `http://localhost:8015/database/schema.sql` -> 403 Forbidden
   - `http://localhost:8015/database/seed.php` -> 403 Forbidden
   - `http://localhost:8015/tests/unit/test_m1.php` -> 403 Forbidden
   - `http://localhost:8015/.agents/worker_m1_gen2/handoff.md` -> 403 Forbidden
   - `http://localhost:8015/assets/css/main.css` -> 200 OK
   - `http://localhost:8015/api/verify_m1` -> 200 OK
6. Write `progress.md`, `changes.md`, and `handoff.md` in `.agents/worker_port8015_fix/`.
7. Send a completion message to parent with the verification results.
