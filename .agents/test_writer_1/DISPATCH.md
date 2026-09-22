## 2026-09-22T14:11:07Z
You are the E2E Test Suite Architect for the Kariana Quran Islamic Educational Portal & CMS.
Your working directory is c:\xampp\htdocs\Kariana Website\.agents\test_writer_1. Write all your reports, logs, and handoff to this directory.

Read the authoritative requirements first:
c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
Also read:
- Master Plan: c:\xampp\htdocs\Kariana Website\.agents\orchestrator_1\PROJECT.md
- Functional Specifications: c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\spec.md

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Scope and Write Ownership:
You own exclusively:
- tests/*
- TEST_INFRA.md at project root
- TEST_READY.md at project root
Do NOT modify core/, app/, config/, or database/ files.

Deliverables:
1. TEST_INFRA.md: Document the 4-tier testing methodology, test architecture, command runner, and coverage thresholds according to PROJECT.md § Feature Inventory (Features 1-34).
2. Pure PHP 8.2 E2E Test Runner under tests/e2e/:
   - tests/e2e/runner.php: Command-line test harness executing HTTP requests against http://127.0.0.1:8015 or internal request mock, displaying formatted pass/fail metrics, execution time, and tier summaries.
   - tests/e2e/Tier1_FeatureCoverageTest.php: Test cases verifying every feature in isolation (home page 200 OK, courses list, book list, blog index, Bengali slug route /blog/..., prayer times endpoint, zakat calculation API/form, tasbeeh page, sitemap.xml validity, robots.txt, schema.org JSON-LD presence in HTML).
   - tests/e2e/Tier2_BoundaryCornerTest.php: Test cases for edge cases (empty admission form submission, invalid email, SQL injection attempts, missing CSRF token on POST, invalid/non-existent district name, negative values in Zakat calculator, malformed Bengali slugs, non-existent book/post 404).
   - tests/e2e/Tier3_CrossFeatureTest.php: Test cases for feature interactions (admission form submission -> verify record saved in DB -> verify status; blog post publish -> verify listed in /blog -> verify included in /sitemap.xml; QR code lookup -> verify lesson resolution).
   - tests/e2e/Tier4_RealWorldScenarioTest.php: Full user journey simulations (prospective student exploring courses and submitting admission; user calculating prayer times for Sylhet and Chottogram; user calculating Zakat on cash and silver; visitor reading Quran reader bridge page).
3. Publish TEST_READY.md at project root when test suite is ready, listing the exact runner command and test counts per tier.
4. Write comprehensive report in c:\xampp\htdocs\Kariana Website\.agents\test_writer_1\handoff.md and update progress.md.
5. Send completion message to orchestrator.
