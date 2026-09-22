## 2026-09-22T14:37:40Z
Worker M1 Retry Assignment:
You are Worker M1 Retry for the Kariana Quran Islamic Educational Portal & CMS.
Your working directory is c:\xampp\htdocs\Kariana Website\.agents\worker_m1_retry. Write all your reports, logs, and handoff to this directory.

Read the authoritative requirements first:
c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
Also read:
- Master Plan: c:\xampp\htdocs\Kariana Website\.agents\orchestrator_1\PROJECT.md
- Full Forensic Audit Report: c:\xampp\htdocs\Kariana Website\.agents\auditor_m1_1\handoff.md
- Explorer Retry 1 Plan (Syntax & Layout): c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_1\handoff.md and analysis.md
- Explorer Retry 2 Plan (Bengali/Arabic Slug Engine): c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_2\handoff.md and analysis.md
- Explorer Retry 3 Plan (Security & Server Hardening): c:\xampp\htdocs\Kariana Website\.agents\explorer_retry_3\handoff.md and analysis.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Scope and Write Ownership:
You own exclusively:
- index.php
- router.php
- .htaccess
- core/BengaliHelper.php
- core/Model.php
- core/Csrf.php
- tests/unit/test_m1.php
- Removing .agents/worker_m1/test_m1.php so .agents/ contains ONLY metadata (.md) files.

Concrete Implementation Tasks:
1. Layout Compliance & Syntax Fix in index.php:
   - Move/copy .agents/worker_m1/test_m1.php to tests/unit/test_m1.php.
   - Delete .agents/worker_m1/test_m1.php so NO non-md files remain in .agents/.
   - In index.php line 208, update test path to __DIR__ . '/tests/unit/test_m1.php'.
   - Ensure the closure on /api/verify_m1 is properly closed with }); and index.php has zero syntax errors (verify with php -l index.php).
2. Implement Enhanced BengaliHelper::createSlug() in core/BengaliHelper.php:
   - Apply the 9-step algorithm from Explorer Retry 2:
     * Pre-replace delimiters (slashes, brackets, pipes, colons, underscores) with hyphens so কুরআন/সুন্নাহ -> কুরআন-সুন্নাহ.
     * Strip Bengali Dari (।), Double Dari (॥), and Taka symbol (৳).
     * Retain Bengali (\p{Bengali} or \x{0980}-\x{09FF}), Arabic (\p{Arabic} or \x{0600}-\x{06FF}), English letters (a-zA-Z), and digits (0-9).
     * Collapse multiple consecutive hyphens (-+) to a single hyphen.
     * Trim leading and trailing hyphens.
     * Fallback to 'kariana-entry-' . time() if title is purely symbols.
3. Harden core/Model.php against SQL injection:
   - In where() and all() methods, validate $orderBy against regex /^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i before concatenating to SQL string. Throw InvalidArgumentException if invalid.
4. Harden core/Csrf.php:
   - In validate($token), ensure that if $token is not a string (e.g. array, object, null), it gracefully returns false without throwing a fatal PHP TypeError.
5. Secure router.php (port 8015):
   - Apply Explorer Retry 3's security filter: block access to hidden files, internal folders (app, config, core, database, storage, tests, .agents, .git), and sensitive extensions (.sql, .md, .json, .env).
   - Only serve static files from public/ with allowed MIME types (CSS, JS, images, fonts). All other requests route to index.php.
6. Fix .htaccess:
   - Update asset rewrite rule to handle subfolders flexibly:
     RewriteCond %{REQUEST_FILENAME} -f [OR]
     RewriteCond public/$1 -f [OR]
     RewriteCond %{DOCUMENT_ROOT}/public/$1 -f
     RewriteRule ^assets/(.*)$ public/assets/$1 [L,NC]
   - Protect tests/ and .git/ from direct web access.

Verification Requirements:
- Run php -l on all modified files.
- Run php tests/unit/test_m1.php and verify all tests pass.
- Run the stress harness (php tests/stress_harness_adversarial.php or tests/m1_stress_runner.php).
- Confirm that .agents/ contains only .md files.
- Document changes in c:\xampp\htdocs\Kariana Website\.agents\worker_m1_retry\changes.md and handoff.md.
- Send a completion message when finished.
