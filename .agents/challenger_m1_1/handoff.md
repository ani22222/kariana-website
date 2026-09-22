# Empirical Challenge Report - Milestone M1: Unicode Routing & Bengali Helper Stress Testing

**Challenger:** Challenger 1 (`critic`, `specialist`)  
**Target:** Milestone M1 (Unicode Routing & Bengali Helper)  
**Target Directory:** `c:\xampp\htdocs\Kariana Website`  
**Execution Timestamp:** 2026-09-22T20:30:00+06:00  
**Overall Verdict:** **CHALLENGE FAILED (EMPIRICAL DEFECTS IDENTIFIED)**  

---

## 1. Observation

### 1.1 Empirical Stress Test Execution Results
We constructed and executed three automated stress harnesses:
1. `tests/m1_stress_runner.php` (78 assertions across 4 functional suites)
2. `tests/test_dari_and_edge_cases.php` (Diacritic, currency, and script edge cases)
3. `tests/stress_harness_adversarial.php` (16 adversarial edge cases)

The tests were executed live via Apache HTTP on `http://localhost/Kariana%20Website/`.

#### Suite 1: Numeral Conversion (`Core\BengaliHelper`)
- **Total Tests**: 32 | **Passed**: 32 | **Failed**: 0
- **0 Handling**:
  - `toBengaliNumber(0)` -> verbatim `"০"` (Passed)
  - `toBengaliNumber("0")` -> verbatim `"০"` (Passed)
  - `toBengaliNumber(0.0)` -> verbatim `"০"` (Passed)
  - `toBengaliNumber(-0.0)` -> verbatim `"-০"` (Passed)
  - `toEnglishNumber("০")` -> verbatim `"0"` (Passed)
- **Extreme & Large Integers**:
  - `toBengaliNumber(9876543210)` -> verbatim `"৯৮৭৬৫৪৩২১০"` (Passed)
  - `toBengaliNumber(PHP_INT_MAX)` (9223372036854775807) -> verbatim `"৯২২৩৩৭২০৩৬৮৫৪৭৭৫৮০৭"` (Passed)
  - `toEnglishNumber("৯২২৩৩৭২০৩৬৮৫৪৭৭৫৮০৭")` -> verbatim `"9223372036854775807"` (Passed)
- **Negative Numbers & Decimals**:
  - `toBengaliNumber(-42)` -> verbatim `"-৪২"` (Passed)
  - `toEnglishNumber("-৪২")` -> verbatim `"-42"` (Passed)
  - `toBengaliNumber(3.14159)` -> verbatim `"৩.১৪১৫৯"` (Passed)
  - `toBengaliNumber(-0.75)` -> verbatim `"-০.৭৫"` (Passed)
  - `toBengaliNumber(1.5e4)` -> verbatim `"১৫০০০"` (Passed)
- **Complex Text & Strings**:
  - Phone number `"মোবাইল: 01711-123456"` -> `"মোবাইল: ০১৭১১-১২৩৪৫৬"` (Passed)
  - Currency `"৳ 12,34,567.89"` -> `"৳ ১২,৩৪,৫৬৭.৮৯"` (Passed)
  - Idempotency on already-converted digits: `"২০২৬"` remains `"২০২৬"` (Passed)
  - Surrounding brackets & punctuation: `"(০১৭১১) ১২-৩৪-৫৬ [রুম #১০২, ব্যাচ #৫]!"` -> `"(01711) 12-34-56 [রুম #102, ব্যাচ #5]!"` (Passed)
  - Step sequence roundtrip oracle for 1,000 numbers: 100% match.

#### Suite 2: Unicode URL Routing (`Core\Router` & `Core\Request`)
- **Total Tests**: 27 | **Passed**: 27 | **Failed**: 0
- **Varied Bengali Slugs**:
  - Raw Unicode slugs matched: `সহজ-পদ্ধতিতে-কুরআন-শেখা`, `ক্বারীয়ানা-কায়েদা-ও-তাজবীদ-শিক্ষা`, `নাজেরা-কুরআন-পাঠ-ও-সহীহ-তিলাওয়াত`, `হিফজুল-কুরআন-ও-মারকাজুল-হুফফাজ`, `উন্নত-ক্বেরাত-ও-মাক্বামাত-প্রশিক্ষণ`, `চাঁদ-দেখা-ও-রমজানের-রোজা`, `বাংলা-ভাষায়-কুরআনের-অনুবাদ`, `দুঃখ-কষ্ট-ও-সবর`, `উৎসব-ও-ঈদ`, `গাড়ি-ও-ভ্রমণের-দোয়া`, `kariana-কুরআন-২০২৬`, `part-1-প্রথম-পাঠ`.
- **URL-Encoding Variations**:
  - Uppercase percent-encoded (`/blog/%E0%A6%B8...`) -> captured decoded slug verbatim `"সহজ-পদ্ধতিতে-কুরআন-শেখা"` with HTTP 200 (Passed).
  - Lowercase percent-encoded (`/blog/%e0%a6%b8...`) -> captured decoded slug verbatim `"সহজ-পদ্ধতিতে-কুরআন-শেখা"` with HTTP 200 (Passed).
  - Double URL-encoded (`%25E0%25A6%2595...`) -> decoded exactly once to `%E0%A6%95...`, preventing double-encoding bypass (Passed).
  - Literal `+` in URL path (`/blog/সহজ+পদ্ধতি`) -> preserved as `+` per RFC 3986 path specification (Passed).
  - Encoded spaces (`%20`) (`/blog/সহজ%20পদ্ধতি`) -> captured as `"সহজ পদ্ধতি"` (Passed).
  - Consecutive hyphens (`/blog/সহজ--পদ্ধতি---কুরআন`) -> captured as `"সহজ--পদ্ধতি---কুরআন"` (Passed).
  - Malformed UTF-8 byte sequences (`/blog/\xC0\xAF`) -> handled gracefully by PCRE `/u` returning HTTP 404 without crashing or fatal exceptions (Passed).
  - Multi-segment routes with constraints (`/courses/হিফজুল-কুরআন/105`) -> matched category `"হিফজুল-কুরআন"` and id `"105"` (Passed).
  - District names parameter testing: all 10 sample districts (`ঢাকা`, `চট্টগ্রাম`, `রাজশাহী`, `খুলনা`, `বরিশাল`, `সিলেট`, `রংপুর`, `ময়মনসিংহ`, `ব্রাহ্মণবাড়িয়া`, `কক্সবাজার`) matched accurately.
- **Request Parsing**:
  - Query strings (`?source=facebook&page=2#heading`) stripped cleanly from `getPath()`.
  - Subdirectory prefix (`/Kariana Website/`) stripped cleanly in shared hosting deployments.
  - Base URL detection handles custom ports (`http://localhost:8015`) and subdirectories (`http://localhost/Kariana Website`).

---

### 1.2 Identified Bugs & Vulnerabilities

#### Defect 1: Bengali Dari (`।` - U+0964) and Double Dari (`॥` - U+0965) Remain in URL Slugs
- **File & Line**: `c:\xampp\htdocs\Kariana Website\core\BengaliHelper.php:57`
- **Verbatim Code**:
  ```php
  // 2. Normalize spaces and punctuation to hyphens or remove
  // Remove characters that are NOT Bengali, English alphanumeric, space, or hyphen
  // Note: \p{Bengali} accurately covers Bengali vowels, consonants, hasant, nukta, and numerals
  $slug = preg_replace('/[^\p{Bengali}a-zA-Z0-9\s_-]/u', '', $title);
  ```
- **Empirical Test Result**:
  - Input: `'সহজ পদ্ধতিতে কুরআন শিক্ষা।'` (with trailing Dari)
    - Actual Output: `'সহজ-পদ্ধতিতে-কুরআন-শিক্ষা।'` (Slug ends with literal punctuation `।`)
  - Input: `'সহজ কুরআন শিক্ষা। প্রথম খণ্ড'` (Dari in middle)
    - Actual Output: `'সহজ-কুরআন-শিক্ষা।-প্রথম-খণ্ড'` (Literal `।-` inside URL slug)
  - Input: `'প্রথম অধ্যায় সমাপ্ত॥ দ্বিতীয় অধ্যায়'`
    - Actual Output: `'প্রথম-অধ্যায়-সমাপ্ত॥-দ্বিতীয়-অধ্যায়'` (Literal `॥-` inside URL slug)
- **Impact**: In Unicode, `\p{Bengali}` script property includes punctuation marks U+0964 (Bengali Dari / Danda) and U+0965 (Bengali Double Dari / Double Danda). Leaving punctuation marks in URL slugs breaks SEO URL specifications and forces browsers to percent-encode them to `%E0%A5%A4`.

#### Defect 2: Bengali Currency Taka Symbol (`৳` - U+09F3) Remains in URL Slugs
- **File & Line**: `c:\xampp\htdocs\Kariana Website\core\BengaliHelper.php:57`
- **Empirical Test Result**:
  - Input: `'অনলাইন কোর্স ফি ৫০০৳'`
  - Expected Output: `'অনলাইন-কোর্স-ফি-৫০০'`
  - Actual Output: `'অনলাইন-কোর্স-ফি-৫০০৳'`
- **Impact**: The currency symbol `৳` (U+09F3) is in the `\p{Bengali}` block and is not stripped, polluting slugs.

#### Defect 3: Word Concatenation Bug When Punctuation Lacks Surrounding Whitespace
- **File & Line**: `c:\xampp\htdocs\Kariana Website\core\BengaliHelper.php:57-60`
- **Verbatim Code**:
  ```php
  $slug = preg_replace('/[^\p{Bengali}a-zA-Z0-9\s_-]/u', '', $title);
  $slug = preg_replace('/[\s_]+/u', '-', $slug);
  ```
- **Empirical Test Result**:
  - Input: `'কুরআন/সুন্নাহ'`
    - Actual Output: `'কুরআনসুন্নাহ'` (Words concatenated)
    - Expected Output: `'কুরআন-সুন্নাহ'`
  - Input: `'কুরআন,হাদিস,ফিকহ'`
    - Actual Output: `'কুরআনহাদিসফিকহ'` (Words concatenated)
    - Expected Output: `'কুরআন-হাদিস-ফিকহ'`
  - Input: `'সহজ-পদ্ধতি:কুরআন'`
    - Actual Output: `'সহজ-পদ্ধতিকুরআন'`
- **Impact**: Punctuation delimiters (slashes, commas, colons) are deleted *before* whitespace/delimiters are converted to hyphens. Titles containing compound phrases without explicit spaces become illegible concatenated words.

#### Defect 4: Pure Arabic Titles Generate Empty Slugs (`""`) Causing SQL Duplicate Key Crashes
- **File & Line**: `c:\xampp\htdocs\Kariana Website\core\BengaliHelper.php:57`
- **Empirical Test Result**:
  - Input: `'القرآن الكريم'` (Surah/Book title in Arabic)
  - Actual Output: `""` (Empty string)
- **Impact**: In `database/schema.sql`, `posts.slug`, `courses.slug`, `books.slug`, and `categories.slug` are defined as:
  ```sql
  `slug` VARCHAR(255) NOT NULL UNIQUE
  ```
  If an administrator publishes a book, course, or blog post with an Arabic title, `BengaliHelper::createSlug()` strips all characters, generating `""`. Inserting the second Arabic item throws a fatal database exception:
  `SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '' for key 'slug'`.
  Furthermore, route `/blog/{slug}` cannot match an empty slug.

#### Defect 5: Fatal Parse Error in `index.php` (Site-Wide Outage)
- **File & Line**: `c:\xampp\htdocs\Kariana Website\index.php:206-273`
- **Verbatim Error**:
  ```
  Parse error: syntax error, unexpected variable "$app", expecting ")" in C:\xampp\htdocs\Kariana Website\index.php on line 273
  ```
- **Root Cause Observation**:
  On line 206, the callback for `/api/verify_m1` was defined:
  ```php
  205:     // Worker M1 Verification Suite API
  206:     $router->get('/api/verify_m1', function (\Core\Request $request) {
  207:         $testFile = __DIR__ . '/.agents/worker_m1/test_m1.php';
  208:         if (file_exists($testFile)) {
  209:             require_once $testFile;
  210:             $report = runM1Verification();
  211:             return \Core\Response::json($report, 200);
  212:         }
  213:     // Quran Reader Launchpad & Bridge
  ```
  The closure was never closed with `});`. Lines 213 through 270 were erroneously appended inside the unclosed closure. On line 273, `$app->run();` triggers a fatal parse error.
- **Impact**: The front controller `index.php` is completely broken. Every public page request (`http://localhost/Kariana Website/`) fails with HTTP 500 / Fatal Parse Error. Standalone test scripts in `tests/` still work because Apache serves them directly.

---

## 2. Logic Chain

1. **Routing and Numeral Soundness**: Observations in Section 1.1 prove that `Core\Router`, `Core\Request`, and `Core\BengaliHelper::toBengaliNumber()` / `toEnglishNumber()` are mathematically and architecturally sound. They handle large integers up to `PHP_INT_MAX`, float precision, phone numbers, mixed-language strings, URL-encoded variations, and malformed UTF-8 without crashing.
2. **Slug Generator Assumptions**: Observation 1.2 (Defects 1 & 2) demonstrates that `BengaliHelper::createSlug()` assumed `\p{Bengali}` matched only letters and vowel signs. However, the Unicode standard includes punctuation (`।`, `॥`) and currency (`৳`) in the Bengali script block.
3. **Punctuation Stripping Order**: Observation 1.2 (Defect 3) proves that by deleting `[^\p{Bengali}a-zA-Z0-9\s_-]` before converting delimiters to hyphens, any punctuation used as a word divider (`/`, `,`, `:`) collapses adjacent words into single strings (`কুরআনসুন্নাহ`).
4. **Script Inclusivity in Islamic Domain**: Observation 1.2 (Defect 4) proves that an Islamic educational portal frequently requires Arabic titles (e.g. `القرآن الكريم`). Stripping Arabic script creates empty slugs, violating the relational database schema's `UNIQUE` constraint and causing catastrophic runtime SQL failures.
5. **Entrypoint Stability**: Observation 1.2 (Defect 5) proves that `index.php` currently has a syntax error that halts the entire front controller.

---

## 3. Caveats

- **Scope Adherence**: In accordance with the Challenger protocol (`Review-only — do NOT modify implementation code`), no implementation files (`BengaliHelper.php` or `index.php`) were modified. Fixes are proposed as actionable mitigations below.
- **Testing Scope**: Test suites were executed locally against Apache on PHP 8.2 with MariaDB. Production environments running Nginx or LiteSpeed may have different URL decoding configurations at the web server layer, but `Request.php`'s internal normalization ensures uniform behavior once the request reaches PHP.

---

## 4. Conclusion

**Verdict: CHALLENGE FAILED (EMPIRICAL DEFECTS IDENTIFIED)**

While the core router and numeral conversion mechanisms exceed requirements, Milestone M1 fails challenge review due to 4 defects in `BengaliHelper::createSlug()` and a fatal syntax regression in `index.php`.

### Actionable Mitigations for Worker:

1. **Fix `index.php:206-213`**:
   Close the `/api/verify_m1` route handler properly:
   ```php
           if (file_exists($testFile)) {
               require_once $testFile;
               $report = runM1Verification();
               return \Core\Response::json($report, 200);
           }
           return \Core\Response::json(['error' => 'Verification test file not found'], 404);
       });
   ```
2. **Upgrade `BengaliHelper::createSlug()`**:
   Replace steps 2-6 with:
   ```php
   public static function createSlug(string $title): string
   {
       $title = trim($title);
       // A. Convert punctuation, slashes, Dari (। ॥), and currency symbols to spaces first
       $title = preg_replace('/[।॥৳\/,;:|&+=~`!?@#$%^*()\[\]{}<>"\'\\\]+/u', ' ', $title);
       // B. Allow Bengali (\p{Bengali} except punctuation), Arabic (\p{Arabic}), Latin alphanumeric, space, hyphen
       $slug = preg_replace('/[^\p{Bengali}\p{Arabic}a-zA-Z0-9\s_-]/u', '', $title);
       // C. Convert whitespace and underscores to hyphens
       $slug = preg_replace('/[\s_]+/u', '-', $slug);
       // D. Collapse consecutive hyphens
       $slug = preg_replace('/-+/u', '-', $slug);
       // E. Trim hyphens
       $slug = trim($slug, '-');
       // F. Fallback for pure symbol titles
       if ($slug === '') {
           $slug = 'post-' . bin2hex(random_bytes(3));
       }
       return mb_strtolower($slug, 'UTF-8');
   }
   ```

---

## 5. Verification Method

To independently verify all findings:

1. **Run Full 78-Test Stress Suite**:
   Open browser or execute HTTP GET:
   ```
   http://localhost/Kariana%20Website/tests/m1_stress_runner.php
   ```
   Observe the JSON report detailing all 32 numeral conversion passes and the failed Dari test.

2. **Run Adversarial Defect Suite**:
   Open browser or execute HTTP GET:
   ```
   http://localhost/Kariana%20Website/tests/stress_harness_adversarial.php
   ```
   Observe the 6 empirical defect confirmations in the `slug_boundary_stress` suite.

3. **Verify Index.php Syntax Error**:
   Open browser or execute HTTP GET:
   ```
   http://localhost/Kariana%20Website/
   ```
   Observe verbatim output:
   `Parse error: syntax error, unexpected variable "$app", expecting ")"` in `index.php:273`.
