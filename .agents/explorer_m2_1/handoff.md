# Handoff Report — Explorer M2 #1: Public Portal, Educational Hub & Admissions

## 1. Observation

### 1.1 Controllers & Models Status
- Direct inspection of `c:\xampp\htdocs\Kariana Website\app\Controllers\` reveals only 4 files:
  - `AdminController.php` (17,100 bytes)
  - `DirectorController.php` (9,503 bytes)
  - `ManagerController.php` (17,137 bytes)
  - `TeacherController.php` (7,897 bytes)
- **`HomeController.php` is MISSING**: In `index.php` lines 46-50:
  ```php
  if (class_exists('\\App\\Controllers\\HomeController')) {
      $controller = new \App\Controllers\HomeController();
      return $controller->index($request);
  }
  ```
  Currently falls back to an inline database query and closure.
- **`CourseController.php` is MISSING**: In `index.php` lines 73-76 and 87-90:
  ```php
  if (class_exists('\\App\\Controllers\\CourseController')) {
      $controller = new \App\Controllers\CourseController();
      return $controller->index($request);
  }
  ```
  Currently falls back to inline closures.
- **`BookController.php` is MISSING**: In `index.php` lines 105-108:
  ```php
  if (class_exists('\\App\\Controllers\\BookController')) {
      $controller = new \App\Controllers\BookController();
      return $controller->index($request);
  }
  ```
  Currently falls back to inline query.
- **`AdmissionController.php` is MISSING**: No admission controller exists anywhere in `app/Controllers/`.
- **`app/Models/` directory does NOT exist**: `list_dir` on `app/` confirms only `Controllers` and `Views` exist. No `Course.php`, `Book.php`, or `Admission.php` models exist, despite `Core\Model` being fully implemented in `core/Model.php` (with `find`, `findBy`, `where`, `create`, `update`, `delete`, `all`).

### 1.2 Routing Observations in `index.php`
- In `index.php` lines 38-41:
  ```php
  $routesFile = __DIR__ . '/app/routes.php';
  if (file_exists($routesFile)) {
      require_once $routesFile;
  } else { ... }
  ```
  **Critical Routing Trap**: If a developer creates `app/routes.php` with only public routes, `index.php`'s entire `else` block containing Admin, Operations Manager, Muallim/Teacher, Director, and Sitemap routes will be skipped and rendered unreachable.
- **Missing `/books/{slug}` route**: `index.php` defines `/books` (lines 104-114), but NO `/books/{slug}` route exists! When a visitor clicks on any book in the homepage hero slider (`href="<?= $baseUrl ?>/books/<?= htmlspecialchars($b['slug']) ?>"`, `home/index.php` line 78), the router returns a 404 error.
- **Missing Admission routes**:
  - NO `GET /admission` or `GET /admissions` route exists for prospective students to open the application form.
  - NO `POST /admissions/apply` route exists. Yet `tests/e2e/Tier3_CrossFeatureTest.php` (line 41), `tests/e2e/Tier4_RealWorldScenarioTest.php` (line 62), and `tests/e2e/Tier2_BoundaryCornerTest.php` (line 32 & 164) specifically execute:
    `$res = $this->client->post('/admissions/apply', [...]);`

### 1.3 View Files & Implementation Gaps
- **`app/Views/home/index.php` (45,631 bytes, 672 lines)**:
  - **Hero Slider**: Rich 3D book carousel and tabs are implemented (lines 1-170). However, the "বিস্তারিত ও সূচিপত্র" button links to `/books/{slug}` which 404s.
  - **Featured Courses Grid (lines 340-388)**: Contains 4 hardcoded static HTML cards ("সহীহ কুরআন শিক্ষা", "হিফজুল কুরআন", "নাজেরা বিভাগ", "তাজবীদ কোর্স"). It completely ignores the `$featuredCourses` array passed from `index.php` line 62! It does not display course code, duration, fee, or admission badge from the database, and links generically to `/courses` rather than `/courses/{slug}`.
  - **Prayer Times Widget (lines 393-450)**: Has hardcoded static values for prayer times (ফজর ০৪:৪০, যোহর ১২:১৫, etc.) and a static `<select>` with only 8 divisions. It does not load dynamic prayer times or offsets from the 64 districts database table (`prayer_districts`).
  - **Testimonials Section**: Entirely missing. No testimonials from parents, adult learners, or Islamic scholars.
  - **Admission CTA**: Missing a dedicated online admission lead/intake banner.
- **`app/Views/courses/index.php` (2,694 bytes, 42 lines)**:
  - Very minimal 3-column card grid.
  - Missing category filter tabs (নাজেরা, হিফজ, ক্বেরাত, তাজবীদ).
  - Missing age group suitability badges (শিশু ও কিশোর, বয়স্ক, পুরুষ ও মহিলা পৃথক শিফট).
  - Missing admission status badge ("ভর্তি চলছে" / "আসন সীমিত").
  - Missing schedule preview and direct "ভর্তি আবেদন" CTA button.
- **`app/Views/courses/show.php` (1,891 bytes, 30 lines)**:
  - Very barebones display of title, fee, description, and raw `<pre>` syllabus.
  - Line 26: `<a href="#admission" class="...">ভর্তি আবেদন করুন</a>` points to `#admission`, but NO `#admission` anchor, section, or form exists on the page!
  - Missing age groups and prerequisites section.
  - Missing instructor profile, class schedule, and total classes breakdown.
  - Missing embedded online admission form or modal directly bound to the current course ID.
  - Missing Course Schema.org JSON-LD microdata integration.
- **`app/Views/books/index.php` (2,518 bytes, 39 lines)**:
  - Very basic cards with no cover artwork.
  - Price display bug in lines 25-28:
    ```php
    <span class="text-lg font-bold text-emerald-800">৳ <?= (int)($b['price'] ?? 0) ?></span>
    <?php if (!empty($b['discount_price'])): ?>
        <span class="text-xs text-slate-400 line-through ml-1">৳ <?= (int)$b['price'] ?></span>
    <?php endif; ?>
    ```
    When a discount is present, it prints `$b['price']` both normally and struck-through, instead of showing `discount_price` with `price` struck through!
  - Missing link to `/books/{slug}`.
  - Missing PDF sample preview modal trigger and modal markup.
- **`app/Views/books/show.php`**:
  - File does NOT exist.
- **`app/Views/admission/index.php`**:
  - Directory and file do NOT exist.

### 1.4 Database Schema & Seed Data
- `database/schema.sql` defines:
  - `courses` (id, title, slug, course_code, category, duration, total_classes, fee, class_schedule, instructor_name, description, syllabus, cover_image, admission_open, is_featured, sort_order)
  - `admissions` (id, course_id, student_name, guardian_name, phone, whatsapp, email, district, district_id, gender, age, previous_education, preferred_time, address, admin_notes, status, created_at, updated_at)
  - `books` (id, title, slug, author, isbn, pages_count, price, discount_price, cover_image, pdf_preview_url, buy_link, stock_status, description, is_featured, sort_order)
- Seeded Courses in `database/seed.php`:
  1. `sahaj-kariana-qaida-tajweed` (KQ-C01, tajweed, ৩ মাস, ৳ ১,৫০০)
  2. `nazera-quran-shikkha` (KQ-C02, nazera, ৬ মাস, ৳ ২,৫০০)
  3. `hifzul-quran-course` (KQ-C03, hifz, ২ বছর, ৳ ৩,০০০)
  4. `advanced-qirat-maqamat` (KQ-C04, qirat, ৪ মাস, ৳ ২,০০০)
- Seeded Books in `database/update_books_data.php`:
  1. `kariana-quran-bangla-ortho-soho-quran` (৳ ১২০০, discount ৳ ১০০০)
  2. `kariana-ampara-sharif` (৳ ১৮০, discount ৳ ১৫০)
  3. `sahaj-kariana-qaida` (৳ ১৫০, discount ৳ ১২০)

---

## 2. Logic Chain

1. **Observation 1.1 & 1.2** show that `index.php` is wired to invoke `\App\Controllers\HomeController`, `\App\Controllers\CourseController`, and `\App\Controllers\BookController` whenever those classes exist via `class_exists()`.
   - *Inference*: The core routing infrastructure already supports controller delegation cleanly. Creating `HomeController.php`, `CourseController.php`, and `BookController.php` will immediately activate them without breaking existing fallbacks.
2. **Observation 1.2** reveals that `app/routes.php` does not exist, and creating it naively will bypass lines 42-517 in `index.php`, causing all admin, director, teacher, and manager routes to fail.
   - *Inference*: New routes (`/books/{slug}`, `/admission`, `/admissions/apply`) should be added directly inside `index.php` within the public web routes section, ensuring zero route loss.
3. **Observation 1.1** shows that `app/Models/` is missing, although `Core\Model` is fully operational and `Core\Controller::model()` looks up `\App\Models\{ModelName}`.
   - *Inference*: Creating `app/Models/Course.php`, `app/Models/Book.php`, and `app/Models/Admission.php` extending `\Core\Model` creates a clean, robust data access layer that enforces prepared statements and centralized queries.
4. **Observation 1.2 & 1.3** demonstrate that test suites (`Tier2`, `Tier3`, `Tier4`) explicitly call `POST /admissions/apply` expecting:
   - CSRF validation via `\Core\Csrf::validate($token)`.
   - Phone validation (`01[3-9]\d{8}`).
   - Insertion into `admissions` table with status `'pending'`.
   - Safe HTTP redirect or JSON response with status 200/302.
   - *Inference*: `AdmissionController::apply()` must handle both form POST and AJAX requests, sanitize inputs, validate CSRF and phone, insert into MariaDB, and provide user feedback.
5. **Observation 1.3** reveals that `home/index.php` ignores `$featuredCourses` and hardcodes static cards, while `courses/show.php` points to a non-existent `#admission` anchor.
   - *Inference*: `home/index.php` must be refactored to dynamically render `$featuredCourses` from the database, and `courses/show.php` must include an embedded admission section/modal so clicking "ভর্তি আবেদন করুন" works immediately.
6. **Observation 1.3** shows that `app/Views/books/index.php` has a price display bug and lacks a PDF preview modal, while `/books/{slug}` 404s.
   - *Inference*: Worker must create `app/Views/books/show.php`, implement the PDF preview modal (with sample preview pages or iframe preview), and fix the price formatting logic.

---

## 3. Caveats

1. **Static Assets / Sample PDF Files**: While `database/seed.php` points `pdf_preview_url` to `assets/pdf/qaida-sample.pdf`, `public/assets/pdf/` does not currently contain physical sample PDF files. The PDF preview modal should provide an authentic sample page viewer (visual preview pages or fallback iframe) so the UI remains 100% interactive and does not show an ugly browser crash/missing file icon.
2. **Interactive Prayer Times on Home**: Full IFB astronomical mathematical calculation for all 64 districts is part of M3 (`PrayerTimeService`). However, for M2's Home page prayer bar, the controller should provide the 64 districts and Dhaka baseline offsets from `prayer_districts` table, enabling an interactive client-side selector that updates the 5 Waqt prayer times instantly.
3. **Shared Hosting / Apache vs CLI**: All generated routes must work seamlessly both under CLI router `php -S 0.0.0.0:8015 router.php` and Apache `.htaccess`. All URLs must use dynamic `$baseUrl` helper.

---

## 4. Conclusion & Recommendations for Worker M2

### Architecture Plan for Worker M2:

#### A. Models Layer (`app/Models/`)
1. **`app/Models/Course.php`**:
   - Extends `\Core\Model` (`protected string $table = 'courses';`).
   - Methods: `getFeatured(int $limit = 4)`, `getAllActive()`, `findBySlug(string $slug)`, `getByCategory(string $category)`.
2. **`app/Models/Book.php`**:
   - Extends `\Core\Model` (`protected string $table = 'books';`).
   - Methods: `getFeatured(int $limit = 3)`, `getAllOrdered()`, `findBySlug(string $slug)`.
3. **`app/Models/Admission.php`**:
   - Extends `\Core\Model` (`protected string $table = 'admissions';`).
   - Methods: `createLead(array $data)`, `validate(array $data)`, `getRecent(int $limit = 10)`.

#### B. Controllers Layer (`app/Controllers/`)
1. **`app/Controllers/HomeController.php`**:
   - `index(Request $request): Response`
   - Fetches featured courses (`admission_open = 1`, limit 4), featured books (limit 3), recent published posts (limit 3), active directors, district counts, and 64 districts prayer times data.
   - Renders `home/index` with complete data and SEO meta tags.
2. **`app/Controllers/CourseController.php`**:
   - `index(Request $request): Response`: Renders all active courses grouped by category (নাজেরা, হিফজ, ক্বেরাত, তাজবীদ) with filter tabs.
   - `show(Request $request, string $slug): Response`: Finds course by slug, renders `courses/show` with detailed syllabus, age groups, schedule, fee, and Course Schema JSON-LD.
3. **`app/Controllers/BookController.php`**:
   - `index(Request $request): Response`: Fetches all books ordered by `sort_order`, renders `books/index` with cover images, correct discount pricing, and PDF modal trigger.
   - `show(Request $request, string $slug): Response`: Fetches book by slug, renders `books/show` with detailed specifications, sample preview, and direct WhatsApp order link.
4. **`app/Controllers/AdmissionController.php`**:
   - `index(Request $request): Response`: Renders `admission/index` with active courses list and 64 districts list. Pre-selects course if `?course=...` query param is present.
   - `apply(Request $request): Response`: Validates CSRF token via `\Core\Csrf::validate()`, validates required fields (`student_name`, `phone`, `course_id`, `district`, `gender`), validates phone regex (`/^01[3-9]\d{8}$/`), inserts into `admissions` table with status `'pending'`, and returns redirect with success flash or JSON response `{status: 'success', message: '...', id: ...}`. Rejects invalid submissions with 422 or redirect with errors.

#### C. Views Layer (`app/Views/`)
1. **`app/Views/home/index.php`**:
   - Update courses section to loop over `$featuredCourses` dynamically with real fees, duration, course code, and links to `/courses/{slug}`.
   - Update prayer times widget with interactive district selector calculating local offsets from `$districts`.
   - Add **Testimonials Section**: 3 authentic cards with guardian/student photos/avatars, stars, and reviews praising Kariana Quran's 12 tajweed symbol methodology.
   - Add **Online Admission CTA Section**: Prominent banner driving students to `/admission`.
2. **`app/Views/courses/index.php`**:
   - Category filter tabs (সকল কোর্স, তাজবীদ, নাজেরা, হিফজ, ক্বেরাত).
   - Rich cards showing: Course code badge, Duration, Fee, Age group suitability tag, Admission status ("ভর্তি চলছে"), Schedule preview, and "ভর্তি আবেদন" + "বিস্তারিত" buttons.
3. **`app/Views/courses/show.php`**:
   - Complete layout with Course Breadcrumb, Badge, Duration, Class count, Schedule, Instructor name, and Fee.
   - Structured syllabus accordion/cards (Module 1, 2, 3).
   - Age groups & eligibility guidelines.
   - **Embedded Admission Form (`#admission`)**: Form pre-selected to this course with CSRF token, real-time validation, and instant submission to `/admissions/apply`.
4. **`app/Views/books/index.php`**:
   - Fix price display bug: show discount price prominently and original price struck-through with savings amount.
   - Add cover thumbnail graphics with fallback.
   - Add "নমুনা পাতা (PDF Preview)" button triggering the preview modal.
   - Add "অর্ডার করুন" WhatsApp button.
   - Add link to `/books/{slug}`.
   - Include Alpine.js / vanilla JS PDF sample preview modal markup.
5. **`app/Views/books/show.php` (NEW)**:
   - Book showcase with 3D cover, author, ISBN, page count, pricing, and discount.
   - Detailed chapter overview and 12 tajweed signs guide.
   - Integrated PDF sample preview modal with sample pages.
   - Direct WhatsApp order button with pre-filled message text.
6. **`app/Views/admission/index.php` (NEW)**:
   - Dedicated standalone student admission page.
   - Clean responsive form: Student Name, Guardian Name, Mobile Number, WhatsApp Number, Email, District (64 districts dropdown), Course Selection (dropdown), Gender, Age, Preferred Time Schedule, and Address.
   - CSRF hidden token (`<?= $csrfField ?>`).
   - Client-side validation + server error display.
   - Success confirmation screen / modal with WhatsApp assistance link.

#### D. Routes Registration (`index.php`)
- Add the following routes directly in `index.php`:
  ```php
  // Book Single Detail Route
  $router->get('/books/{slug}', function (\Core\Request $request, string $slug) {
      if (class_exists('\\App\\Controllers\\BookController')) {
          $controller = new \App\Controllers\BookController();
          return $controller->show($request, $slug);
      }
      // Fallback
      $stmt = \Core\Database::getInstance()->prepare("SELECT * FROM `books` WHERE `slug` = :slug LIMIT 1");
      $stmt->execute(['slug' => $slug]);
      $book = $stmt->fetch();
      if (!$book) return new \Core\Response('বইটি পাওয়া যায়নি', 404);
      return \Core\View::render('books/show', ['title' => $book['title'], 'book' => $book]);
  });

  // Online Student Admission Form Routes
  $router->get('/admission', function (\Core\Request $request) {
      if (class_exists('\\App\\Controllers\\AdmissionController')) {
          $controller = new \App\Controllers\AdmissionController();
          return $controller->index($request);
      }
      $courses = \Core\Database::getInstance()->query("SELECT * FROM `courses` WHERE `admission_open` = 1 ORDER BY `sort_order` ASC")->fetchAll();
      $districts = \Core\Database::getInstance()->query("SELECT * FROM `prayer_districts` ORDER BY `name_bn` ASC")->fetchAll();
      return \Core\View::render('admission/index', [
          'title'     => 'অনলাইন ভর্তি আবেদন | কারিয়ানা কুরআন',
          'courses'   => $courses,
          'districts' => $districts,
      ]);
  });
  $router->get('/admissions', function (\Core\Request $request) {
      return \Core\Response::redirect('/admission');
  });
  $router->post('/admissions/apply', function (\Core\Request $request) {
      if (class_exists('\\App\\Controllers\\AdmissionController')) {
          $controller = new \App\Controllers\AdmissionController();
          return $controller->apply($request);
      }
      // Direct handling fallback
      ...
  });
  ```
- In `app/Views/layouts/main.php`, add "অনলাইন ভর্তি" to the header navigation and footer links.

---

## 5. Verification Method

To independently verify after implementation:
1. **Automated Test Suites**:
   - Run `tests/e2e/runner.php` or execute individual tier suites:
     - `php -r "require 'tests/e2e/runner.php';"`
     - Check `Tier1_FeatureCoverageTest.php`: verifies `/courses`, `/courses/{slug}`, `/books`, `/`.
     - Check `Tier2_BoundaryCornerTest.php`: verifies `POST /admissions/apply` rejects empty submissions and invalid CSRF.
     - Check `Tier3_CrossFeatureTest.php`: verifies `POST /admissions/apply` persists student record to MariaDB with UTF-8 Bengali preservation.
     - Check `Tier4_RealWorldScenarioTest.php`: verifies prospective student journey (home -> course detail -> submit admission -> DB verification).
2. **File & Route Inspection**:
   - Inspect `app/Controllers/HomeController.php`, `app/Controllers/CourseController.php`, `app/Controllers/BookController.php`, `app/Controllers/AdmissionController.php`.
   - Inspect `app/Models/Course.php`, `app/Models/Book.php`, `app/Models/Admission.php`.
   - Inspect `app/Views/books/show.php` and `app/Views/admission/index.php`.
3. **Manual Browser Verification on Port 8015**:
   - Navigate to `http://localhost:8015/` -> verify dynamic featured courses, dynamic prayer times widget, testimonials section.
   - Navigate to `http://localhost:8015/courses` -> verify category tabs, fees, age groups, duration.
   - Navigate to `http://localhost:8015/courses/sahaj-kariana-qaida-tajweed` -> verify syllabus, age suitability, and embedded admission form.
   - Navigate to `http://localhost:8015/books` -> verify cover images, discount price format, and PDF modal.
   - Navigate to `http://localhost:8015/books/kariana-ampara-sharif` -> verify book details, PDF sample modal, and WhatsApp link.
   - Navigate to `http://localhost:8015/admission` -> fill out student admission form, submit, verify record inserted into `admissions` table and visible in `/admin/admissions`.
