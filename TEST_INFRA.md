# Kariana Quran Portal & CMS — E2E Test Infrastructure & Methodology

**Document Version:** 1.0.0  
**Target Environment:** PHP 8.2+ / MariaDB 10.4+ / Apache / Built-in CLI Server (Port 8015)  
**Author:** E2E Test Suite Architect (`teamwork_preview_test_writer`)  
**Project Dedicated Port:** `8015` (`http://localhost:8015` and `http://192.168.0.100:8015`)  

---

## 1. Overview & Test Architecture

The Kariana Quran E2E Test Suite provides end-to-end verification across all 34 features specified in `PROJECT.md` and `spec.md`. The testing infrastructure is built with **zero external composer dependencies** using pure PHP 8.2, guaranteeing 100% portability across local workstations, XAMPP, cPanel, and CI/CD pipelines.

### Dual-Mode Execution Architecture
The test suite operates in two execution modes:
1. **Live HTTP Network Mode (`HttpNetworkDriver`)**:
   - Executes real HTTP/1.1 requests using PHP's native `curl` / stream transport against the active server on `http://127.0.0.1:8015` (bound to `0.0.0.0`).
   - Verifies HTTP status codes, headers, cookie persistence, redirects, and network round-trips.
2. **Headless Kernel Dispatcher Mode (`KernelMockDriver`)**:
   - Dispatches simulated HTTP requests directly through the front controller routing table (`Core\Router`), executing real controllers, services, database models, and view templates in-memory.
   - Captures output buffers, response status codes, and injected headers without requiring an active network daemon.
   - Enables instant verification during CI runs or offline environments.

```
+-------------------------------------------------------------------------+
|                         tests/e2e/runner.php                            |
+-------------------------------------------------------------------------+
                                    |
          +-------------------------+-------------------------+
          |                                                   |
          v                                                   v
 [HttpNetworkDriver]                                [KernelMockDriver]
 cURL / Stream HTTP                                Direct Request / Response
   http://127.0.0.1:8015                             Core\Router -> App
          |                                                   |
          +-------------------------+-------------------------+
                                    |
          +---------------------------------------------------+
          |            E2E Test Suites (Tiers 1-4)            |
          +---------------------------------------------------+
          |  Tier 1: Feature Coverage (Features 1-34)         |
          |  Tier 2: Boundary & Corner Edge Cases             |
          |  Tier 3: Cross-Feature Interactions & Data Flows  |
          |  Tier 4: Real-World User Journey Simulations      |
          +---------------------------------------------------+
                                    |
                                    v
          +---------------------------------------------------+
          |          Database & Forensic Verification         |
          |           MariaDB 10.4+ via Core\Database         |
          +---------------------------------------------------+
```

---

## 2. The 4-Tier Testing Methodology

The test suite is structured into four progressive tiers:

### Tier 1: Feature Coverage (Isolation Testing)
Verifies that every public page, utility, API endpoint, and admin entry point returns valid responses and adheres to contracts in isolation:
- **T1.01 Public Home Page**: Verifies HTTP 200 OK, title, hero section, quick prayer ticker, and essential CSS/JS assets.
- **T1.02 Course Catalog & Details**: Verifies `/courses` index and individual course details (`/courses/{slug}`) with syllabus display.
- **T1.03 Books & Publications**: Verifies `/books` catalog showcase and book detail modal/page.
- **T1.04 Blog & News System**: Verifies `/blog` index, categories listing, and article retrieval.
- **T1.05 Bengali Slug Routing**: Verifies Unicode URL resolution for native Bengali slugs (e.g. `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`).
- **T1.06 Prayer Times API & UI**: Verifies `/prayer-times` and JSON calculation endpoints for Dhaka baseline.
- **T1.07 Zakat Calculator**: Verifies `/zakat` calculation page and reactive formula output.
- **T1.08 Digital Tasbeeh Counter**: Verifies `/tasbeeh` page markup, audio synthesizer hook, and counter state elements.
- **T1.09 Dynamic XML Sitemap**: Verifies `/sitemap.xml` returns `application/xml`, valid XML structure, and UTF-8 Bengali URLs.
- **T1.10 Robots.txt**: Verifies `/robots.txt` directives for search engine crawlers and sitemap pointer.
- **T1.11 Schema.org JSON-LD**: Verifies presence and valid JSON-LD structures for `Organization`, `Article`, `Course`, `FAQPage`, and `BreadcrumbList`.
- **T1.12 QR Book Scanner Gateway**: Verifies `/scan` landing page and `/lesson/{code}` permalink.
- **T1.13 Dedicated Quran App Bridge**: Verifies `/quran-bridge` UI launchpad, verified font indicators, and 12 Tajweed symbol legend.
- **T1.14 Admin Auth Guard**: Verifies `/admin` redirects unauthenticated users to `/admin/login`.

### Tier 2: Boundary & Corner Cases (Edge & Security Testing)
Tests system resiliency, input validation, and defensive controls against unexpected, malformed, or malicious inputs:
- **T2.01 Empty Admission Form**: Submitting an empty admission form triggers validation errors and rejects insertion.
- **T2.02 Invalid Email Format**: Submitting malformed email strings (e.g. `invalid@`, `@domain`, `plainaddress`) is rejected.
- **T2.03 Bangladeshi Phone Number Validation**: Validates phone regex `^01[3-9]\d{8}$`, stripping whitespace, hyphens, and `+88` prefixes while rejecting invalid numbers (e.g. `01212345678`, `12345`).
- **T2.04 SQL Injection Prevention**: Submitting SQL payload strings (`' OR '1'='1`, `1; DROP TABLE users;--`) in search queries, URL parameters, and form fields is neutralized via PDO prepared statements.
- **T2.05 CSRF Token Protection**: POST requests with missing, invalid, or expired `csrf_token` receive HTTP 403 Forbidden.
- **T2.06 Invalid District Fallback**: Requesting non-existent district IDs or unknown district names gracefully falls back to Dhaka baseline (District 1) without exceptions.
- **T2.07 Zakat Negative & Non-Numeric Handling**: Negative asset inputs or non-numeric strings are sanitized using absolute float casting (`floatval(abs($val))`) without calculation crashes.
- **T2.08 Malformed Bengali Slugs**: Slugs with illegal punctuation, excessive hyphens, or malformed UTF-8 sequences are cleaned or return HTTP 404 gracefully.
- **T2.09 Non-Existent Resources (404 Handling)**: Accessing non-existent books, courses, or blog posts returns HTTP 404 with styled error page.
- **T2.10 Admin Brute Force & Invalid Auth**: Submitting invalid admin credentials fails authentication and preserves password security.

### Tier 3: Cross-Feature Interactions (Integration & Data Flow)
Verifies multi-step workflows spanning database mutations, cache invalidation, and cross-module linkages:
- **T3.01 Online Admission Flow to Database**:
  - Step 1: Submit public admission form with valid student data and CSRF token.
  - Step 2: Query MariaDB `admissions` table to verify record was persisted with correct fields.
  - Step 3: Verify initial status is `'new'`.
  - Step 4: Verify UTF-8 Bengali characters in student name and father's name are uncorrupted.
- **T3.02 Blog Post Lifecycle & Dynamic SEO Flow**:
  - Step 1: Seed/publish a new blog post with Bengali title.
  - Step 2: Verify post appears in public `/blog` listing.
  - Step 3: Verify post is accessible via its canonical Bengali slug URL.
  - Step 4: Verify post URL is immediately included in the dynamic `/sitemap.xml`.
- **T3.03 QR Code Lesson Gateway Resolution**:
  - Step 1: Seed a lesson mapping linking physical book page (e.g. Page 12 of Qaida) to video lesson code `KQ-QAIDA-P12`.
  - Step 2: Access `/scan?code=KQ-QAIDA-P12` and `/scan?page=12`.
  - Step 3: Verify resolution to lesson view displaying embedded video and Tajweed symbol badges.
- **T3.04 64-District Prayer Offset Accuracy**:
  - Step 1: Calculate prayer times for Dhaka baseline.
  - Step 2: Calculate prayer times for Sylhet (Eastern: -6 mins).
  - Step 3: Calculate prayer times for Rajshahi (Western: +7 mins).
  - Step 4: Assert exact mathematical delta matching IFB convention offset matrix.
- **T3.05 Hanafi Silver Nisab Calculation Consistency**:
  - Step 1: Calculate net wealth with cash and silver holdings.
  - Step 2: Test sub-Nisab amount -> verify Zakat due is 0.00 and status is "যাকাত ফরজ নয়".
  - Step 3: Test supra-Nisab amount -> verify Zakat due is exactly 2.5% of net wealth and status is "যাকাত ফরজ".

### Tier 4: Real-World Scenarios (End-to-End User Journeys)
Simulates end-to-end user journeys representative of authentic visitor behavior:
- **T4.01 Prospective Student Journey**:
  - Visitor lands on Homepage -> views Featured Courses -> clicks "সহজ তাজবীদ শিক্ষা" course details -> reviews syllabus modules -> fills out online admission form -> submits form with valid phone number -> receives admission confirmation.
- **T4.02 Daily Islamic Devotional Journey**:
  - Visitor checks today's prayer times for Dhaka -> switches district to Sylhet -> verifies adjusted Fajr/Maghrib times -> checks Sehri and Iftar countdown -> opens digital Tasbeeh counter -> clicks 33 times on "সুবহানাল্লাহ" preset -> completes lap.
- **T4.03 Annual Zakat Calculation & Assessment Journey**:
  - User opens Zakat Calculator -> inputs ৳ 50,000 cash, 10 Bhori gold, 20 Bhori silver, and ৳ 15,000 immediate debt -> system fetches/applies current BAJUS gold/silver rates -> computes net wealth and 52.5 Tola Silver Nisab threshold -> outputs itemized summary and payable Zakat in Bengali numerals.
- **T4.04 Physical Book to Video Lesson & Quran Reader Bridge Journey**:
  - Visitor browses Kariana Publications -> views "সহজ ক্বারীয়ানা কায়েদা" -> scans printed book QR code for Page 5 -> views dedicated video lesson for Harakat -> transitions to Quran App Bridge to test interactive Quran reader with custom `AAR-SQ-003` font.

---

## 3. Test Runner & CLI Usage

### Running the Full Test Suite
The primary test harness is located at `tests/e2e/runner.php`:

```powershell
# Run full suite (auto-detects HTTP server on port 8015 or uses kernel mock)
php tests/e2e/runner.php

# Run against a specific base URL
php tests/e2e/runner.php --url=http://127.0.0.1:8015

# Force headless internal kernel execution mode
php tests/e2e/runner.php --kernel

# Run a specific tier only
php tests/e2e/runner.php --tier=1
php tests/e2e/runner.php --tier=2
php tests/e2e/runner.php --tier=3
php tests/e2e/runner.php --tier=4

# Run with verbose output (shows full request/response diagnostics)
php tests/e2e/runner.php --verbose
```

### Exit Codes
- `0`: All tests passed successfully.
- `1`: One or more tests failed.
- `2`: System configuration or environment error (e.g. database unreachable).

---

## 4. Quality Thresholds & Acceptance Criteria

| Metric | Target Threshold | Minimum Acceptable |
|---|---|---|
| **Tier 1 Feature Coverage** | 100% of Features 1-34 | 100% |
| **Tier 2 Boundary/Edge Cases** | 10+ adversarial test vectors | 10 test vectors |
| **Tier 3 Cross-Feature Flows** | 5 integrated workflows | 5 workflows |
| **Tier 4 Real-World Journeys** | 4 full user journeys | 4 journeys |
| **Overall Assertion Pass Rate** | 100% | 100% |
| **Execution Time** | < 15.0 seconds | < 30.0 seconds |
| **Memory Consumption** | < 32 MB | < 64 MB |
| **Database UTF-8 Integrity** | Zero mojibake in Bengali text | Zero mojibake |

---

## 5. Directory Layout of Test Infrastructure

```
tests/
├── e2e/
│   ├── runner.php                         # Master CLI test harness & report generator
│   ├── TestCase.php                       # Base test case with assertions & helpers
│   ├── TestClient.php                     # Unified HTTP Network / Kernel Mock client
│   ├── Tier1_FeatureCoverageTest.php      # Feature isolation tests
│   ├── Tier2_BoundaryCornerTest.php       # Edge cases, validation, security
│   ├── Tier3_CrossFeatureTest.php         # Cross-module flows & DB assertions
│   └── Tier4_RealWorldScenarioTest.php    # Complete user journey simulations
```
