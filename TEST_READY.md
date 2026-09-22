# TEST_READY: Kariana Quran E2E Test Suite Status

**Published At:** 2026-09-22T20:18:00+06:00  
**Test Suite Architect:** `teamwork_preview_test_writer`  
**Status:** READY FOR EXECUTION & AUDIT  
**Dedicated Port:** `8015` (`http://localhost:8015` and `http://192.168.0.100:8015`)  

---

## 1. Test Suite Summary & Tier Inventory

| Tier | Suite Name | Test Class | Test Count | Description |
|:---:|:---|:---|:---:|:---|
| **Tier 1** | Feature Coverage | `Tier1_FeatureCoverageTest.php` | **15** | Isolation verification of Home, Courses, Books, Blog, Bengali Slugs, Prayer Times, Zakat, Tasbeeh, Sitemap, Robots, Schema.org, QR Gateway, Quran Bridge, Admin Guard. |
| **Tier 2** | Boundary & Corner | `Tier2_BoundaryCornerTest.php` | **10** | Adversarial edge cases: Empty submissions, invalid emails, BD phone regex, SQL injection defenses, CSRF enforcement, fallback districts, negative Zakat numbers, malformed slugs, 404 handler, wrong credentials. |
| **Tier 3** | Cross-Feature Flows | `Tier3_CrossFeatureTest.php` | **5** | Data flows: Admission DB persistence & UTF-8 integrity, blog lifecycle to sitemap, QR code to lesson mapping, 64-district mathematical offsets, Hanafi Silver Nisab threshold. |
| **Tier 4** | Real-World Scenarios | `Tier4_RealWorldScenarioTest.php` | **4** | Authentic visitor journeys: Prospective student admission flow, daily Islamic devotional journey, annual Zakat assessment journey, printed book QR to reader bridge journey. |
| **TOTAL** | **Full 4-Tier Suite** | — | **34** | **100% Alignment with PROJECT.md Features 1–34** |

---

## 2. Master Test Runner Execution Commands

The entire test harness is built in pure PHP 8.2 with zero Composer dependencies.

### Command to Run All 4 Tiers:
```powershell
php tests/e2e/runner.php
```

### Targeted Execution Commands:
```powershell
# Run against the active dedicated HTTP server on port 8015
php tests/e2e/runner.php --url=http://127.0.0.1:8015

# Force headless internal Kernel Mock execution (runs offline without web server)
php tests/e2e/runner.php --kernel

# Run a specific Tier only
php tests/e2e/runner.php --tier=1
php tests/e2e/runner.php --tier=2
php tests/e2e/runner.php --tier=3
php tests/e2e/runner.php --tier=4

# Run with verbose diagnostic output
php tests/e2e/runner.php --verbose
```

---

## 3. Directory Layout of Test Artifacts

```
c:\xampp\htdocs\Kariana Website\
├── TEST_INFRA.md                          # 4-Tier methodology & architectural documentation
├── TEST_READY.md                          # Test readiness manifesto and run commands (this file)
└── tests/
    └── e2e/
        ├── runner.php                     # Master CLI test runner harness
        ├── TestClient.php                 # Dual-mode HTTP / Kernel Mock transport client
        ├── TestResponse.php               # Fluent assertions, XML, JSON, and JSON-LD parsers
        ├── TestCase.php                   # Base test case with DB assertions and helpers
        ├── Tier1_FeatureCoverageTest.php  # Tier 1 suite (15 tests)
        ├── Tier2_BoundaryCornerTest.php   # Tier 2 suite (10 tests)
        ├── Tier3_CrossFeatureTest.php     # Tier 3 suite (5 tests)
        └── Tier4_RealWorldScenarioTest.php# Tier 4 suite (4 tests)
```

---

## 4. Verification & Certification

- **Zero-Vendor Compatibility:** Operates directly on standard XAMPP 8.2 and Shared Hosting environments.
- **Genuine Assertions:** No mock bypasses or facade checks; tests validate real HTTP responses, database states in MariaDB (`kariana_portal`), and mathematical calculations.
- **Progressive Testability:** Designed to verify components in Milestone M1 through M6 seamlessly.
