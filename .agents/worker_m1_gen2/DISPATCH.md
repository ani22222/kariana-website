## 2026-09-22T14:58:39Z
You are Worker M1 Gen 2 for Kariana Quran (কারিয়ানা কুরআন) Islamic Educational Portal & CMS.
Your working directory: c:\xampp\htdocs\Kariana Website\.agents\worker_m1_gen2\

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

MANDATORY READING:
Before writing any code, read:
1. c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
2. c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen2\PROJECT.md
3. c:\xampp\htdocs\Kariana Website\.agents\orchestrator_1\GATE_STATUS.md
4. c:\xampp\htdocs\Kariana Website\.agents\auditor_m1_1\handoff.md
5. c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_1\handoff.md
6. c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_2\handoff.md
7. c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_3\handoff.md

FILE OWNERSHIP (You have exclusive write access to these files):
- index.php
- router.php
- .htaccess
- core/BengaliHelper.php
- core/Model.php
- core/Csrf.php
- core/Router.php
- tests/unit/test_m1.php
- Deletion of .agents/worker_m1/test_m1.php
- Metadata files in c:\xampp\htdocs\Kariana Website\.agents\worker_m1_gen2\

YOUR TASKS:
1. Create `tests/unit/test_m1.php` with full verification logic and direct CLI execution support (as detailed in Explorer Retry 1 handoff).
2. Delete `c:\xampp\htdocs\Kariana Website\.agents\worker_m1\test_m1.php` so that `.agents/` contains ONLY `.md` files.
3. Update `index.php`:
   - Point `/api/verify_m1` route to `__DIR__ . '/tests/unit/test_m1.php'`.
   - Ensure the closure is cleanly closed with `});`.
   - Confirm zero occurrences of `.agents/` remain in `index.php` or any production code.
4. Update `core/BengaliHelper.php`:
   - Replace `createSlug()` with the comprehensive 9-step algorithm detailed in Explorer Retry 2 handoff.
   - Strip Arabic Harakat/Tatweel/stop marks.
   - Pre-convert Dari (`।`), Double Dari (`॥`), Taka (`৳`), currency marks, and punctuation to spaces to prevent word-merging bugs (e.g., `কুরআন/সুন্নাহ` -> `কুরআন-সুন্নাহ`).
   - Support `\p{Arabic}` alongside `\p{Bengali}` and Latin alphanumerics.
   - Provide a safe, unique fallback slug (e.g., `item-...`) when title consists purely of stripped symbols to prevent MariaDB unique constraint collisions.
5. Update `core/Model.php`:
   - Whitelist `$orderBy` in `where()` and `all()` to completely neutralize SQL injection risks.
6. Update `core/Csrf.php`:
   - Change `validate(?string $token)` to accept `mixed $token` and check `!is_string($token) || empty($token)` to avoid fatal `TypeError` under PHP 8.2 when an array is passed.
7. Update `core/Router.php`:
   - Safely handle parameter reflection (including union types or untyped parameters).
8. Update `router.php`:
   - Defend against sensitive file exposure over CLI server port 8015: eliminate direct serving of `.sql`, `.md`, `.json`, `.env`, and block `app/`, `config/`, `core/`, `database/`, `storage/`, `tests/`, `.agents/`, `.git/`. Restrict static delivery to legitimate `public/` files with safe MIME types.
9. Update `.htaccess`:
   - Protect `tests/` and `.git/` in rewrite rules.
   - Fix asset rewrite condition for Apache subfolder environments (`RewriteCond public/$1 -f [OR] RewriteCond %{DOCUMENT_ROOT}/public/$1 -f`).
10. Run tests & verification:
    - Run `php -l` on all modified files.
    - Run `php tests/unit/test_m1.php` and confirm all assertions pass.
    - Verify that `.agents/` has NO non-md files.
    - Test that `core/Model.php` rejects/neutralizes SQL injection in `$orderBy`.

OUTPUT:
- Write `progress.md` in `.agents/worker_m1_gen2/` with timestamps and task status.
- Write `changes.md` in `.agents/worker_m1_gen2/` documenting all modifications.
- Write `handoff.md` in `.agents/worker_m1_gen2/` following the Handoff Protocol (Observation, Logic Chain, Caveats, Conclusion, Verification Method).
- Send a completion message to parent with summary and test results.
