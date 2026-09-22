## 2026-09-22T15:35:57Z
You are Worker M2 for Kariana Quran (কারিয়ানা কুরআন) Islamic Educational Portal & CMS.
Your working directory: c:\xampp\htdocs\Kariana Website\.agents\worker_m2\

MANDATORY INTEGRITY WARNING:
DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A teamwork_preview_auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

MANDATORY READING:
- c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
- c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen2\PROJECT.md
- c:\xampp\htdocs\Kariana Website\.agents\explorer_m2_1\handoff.md
- c:\xampp\htdocs\Kariana Website\.agents\explorer_m2_2\handoff.md
- c:\xampp\htdocs\Kariana Website\.agents\explorer_m2_3\handoff.md
- c:\xampp\htdocs\Kariana Website\tests\e2e\Tier1_FeatureCoverageTest.php

YOUR TASKS:
1. Implement Models in `app/Models/`:
   - `Course.php` (table `courses`)
   - `Book.php` (table `books`)
   - `Admission.php` (table `admissions`, validate phone with BD format `01[3-9]\d{8}`, sanitize, save lead)
   - `Post.php` (table `posts`, category filtering, slug query, view increment)
   - `Category.php` (table `categories`)
   - `Page.php` (table `pages`, seed about, methodology, teachers, branches, contact if empty)

2. Implement Services in `app/Services/`:
   - `SeoService.php`:
     - Automated Schema.org JSON-LD generation: Organization, Article, Course, FAQPage, BreadcrumbList, WebApplication.
     - Dynamic XML Sitemap (`/sitemap.xml`) with Bengali URLs, lastmod, priorities.
     - Robots.txt (`/robots.txt`) with sitemap directive.
     - OpenGraph and Twitter Cards metadata.

3. Implement Controllers in `app/Controllers/`:
   - `HomeController.php`: Render `home/index` with dynamic featured courses, books, prayer bar, testimonials, and admission CTA.
   - `CourseController.php`: `index()` (tabs for নাজেরা, হিফজ, ক্বেরাত, তাজবীদ), `show($slug)` (details, syllabus, Course schema, admission form).
   - `BookController.php`: `index()` (catalog, PDF sample modal, order button), `show($slug)`.
   - `AdmissionController.php`: `index()` (interactive form), `apply()` (`POST /admissions/apply`, CSRF, phone validation, flash message / JSON).
   - `BlogController.php`: `index()` (listing, category/tag filter, pagination), `show($slug)` (view counter, Article schema).
   - `PageController.php`: `about()`, `methodology()`, `teachers()`, `branches()`, `contact()`.

4. Enhance Master Layout & Views in `app/Views/`:
   - `layouts/main.php`:
     - Include Alpine.js CDN `<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>`.
     - Mobile drawer navigation with links to institutional pages.
     - Royal Islamic Emerald Green (`#064e3b` / `#047857`) and Gold (`#d97706`).
     - GTM `<head>` script injection + `<body>` noscript.
     - Verified fonts: `Hind Siliguri`, `Amiri`, and `AAR-SQ-003.ttf`.
   - Full responsive views for `home/`, `courses/`, `books/`, `admission/`, `blog/`, and `pages/`.

5. Register Routes in `index.php`:
   - Wire all routes cleanly to Controller actions.
   - Route `POST /admissions/apply` to `AdmissionController::apply`.
   - Route `/admission` to `AdmissionController::index`.
   - Route `/courses`, `/courses/{slug}`, `/books`, `/books/{slug}`, `/blog`, `/blog/{slug}`.
   - Route `/about`, `/methodology`, `/teachers`, `/branches`, `/contact`.
   - Route `/sitemap.xml` and `/robots.txt`.

6. Run Verification:
   - Run `php -l` on all PHP files.
   - Run `php tests/e2e/runner.php --tier=1` and ensure Tier 1 passes cleanly.
   - Test `POST /admissions/apply` saves to database.
