# Milestone 2 Exploration Report & Worker Handoff
**Topic**: Blog System, Dynamic Institutional Pages, and Master Layout Polish
**Agent**: Explorer M2 #2
**Target Project**: Kariana Quran (`c:\xampp\htdocs\Kariana Website`)
**Date**: 2026-09-22T15:35:00Z

---

## 1. Observation

### 1.1 Blog System
1. **Controller Missing**:
   - `app/Controllers/BlogController.php` does not exist on disk.
   - `index.php` lines 117–145 handle `/blog` and `/blog/{slug}` using fallback anonymous closures because `class_exists('\\App\\Controllers\\BlogController')` evaluates to false.
2. **Blog Views Incomplete**:
   - `app/Views/blog/index.php` (36 lines total):
     - Lacks category filtering (`?category={slug}` or `/blog/category/{slug}`).
     - Lacks tag filtering (`?tag={tag}`).
     - Lacks search query input and handling (`?q={term}`).
     - Lacks pagination (loads all posts unconditionally).
     - Lacks a sidebar (categories with counts, popular tags, recent articles).
     - Dates are printed as raw substring `substr($p['created_at'] ?? '', 0, 10)` without Bengali digits or formatting.
   - `app/Views/blog/show.php` (38 lines total):
     - Line 28 uses CSS class `font-bengali`, but `app/Views/layouts/main.php` only registers `sans` (Hind Siliguri), `arabic` (Amiri), and `kariana` in Tailwind config.
     - Lacks category pill link.
     - Lacks clickable tags (`$post['tags']` is a raw comma-delimited string in the DB and is not rendered).
     - Does not increment `views_count` on post visit.
     - Does not pass Schema.org `Article` / `BlogPosting` JSON-LD to `$schemaJsonLd`.
     - Lacks social sharing buttons (WhatsApp, Facebook, Twitter, Copy Link).
     - Lacks author bio, related articles section, and breadcrumb linking to category.
3. **Bengali Slug Routing & Handling**:
   - `core/Router.php` lines 135–141 convert route parameters into Unicode regex: `#^/blog/(?P<slug>[^/]+)$#u`.
   - `core/Request.php` line 54 applies `rawurldecode($rawPath)`, properly decoding UTF-8 Bengali URLs like `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`.
   - `core/BengaliHelper.php` lines 46–106 (`createSlug`) correctly strips Dari (।), double Dari (॥), Taka (৳), quotes, and converts punctuation to hyphens while preserving the Bengali Unicode block (`\p{Bengali}`).
   - Database: `database/seed.php` line 277 seeds 1 post with slug `'সহজ-পদ্ধতিতে-কুরআন-শেখা'`. Having only 1 seeded post limits verification of filtering and pagination.

### 1.2 Dynamic Institutional Pages
1. **Controller Missing**:
   - `app/Controllers/PageController.php` does not exist.
2. **Views Directory Missing**:
   - The directory `app/Views/pages/` does not exist on disk.
   - None of the 5 required institutional views exist:
     - `app/Views/pages/about.php` (About Us / পরিচিতি)
     - `app/Views/pages/methodology.php` (Methodology / ক্বারীয়ানা ১২টি সংকেত ও শিক্ষাপদ্ধতি)
     - `app/Views/pages/teachers.php` (Certified Muallims & Teachers / শিক্ষক ও মুয়াল্লিমবৃন্দ)
     - `app/Views/pages/branches.php` (64 District Markaz & Branches / দেশব্যাপী শাখাসমূহ)
     - `app/Views/pages/contact.php` (Contact Us & Inquiry Form / যোগাযোগ ও অনুসন্ধান)
     - `app/Views/pages/show.php` (Dynamic CMS page fallback for `/page/{slug}`)
3. **Routes Missing**:
   - `index.php` contains NO route definitions for `/about`, `/methodology`, `/teachers`, `/branches`, `/contact`, or `/page/{slug}`.
4. **Database & Sitemap Missing**:
   - Table `pages` is defined in `database/schema.sql` (lines 134–148) with columns `slug`, `title`, `content`, `meta_title`, `meta_description`, `canonical_url`, `is_system`, but `database/seed.php` inserts 0 rows into `pages`.
   - `index.php` lines 431–442 (`/sitemap.xml`) static routes array does NOT include `/about`, `/methodology`, `/teachers`, `/branches`, `/contact`.
   - `core/SeoHelper.php` lines 236–260 (`getReadableBreadcrumbName`) lacks mappings for `about`, `methodology`, `teachers`, `branches`, `contact`.

### 1.3 Master Layout (`app/Views/layouts/main.php`)
1. **Alpine.js Missing**:
   - Both `ORIGINAL_REQUEST.md` (line 5) and `PROJECT.md` (line 7) require Alpine.js.
   - Neither CDN script nor local script for Alpine.js is included in `app/Views/layouts/main.php`.
2. **Mobile Drawer (Hamburger Menu) Missing**:
   - Header (lines 163–225) only has a logo and `<nav class="hidden md:flex">`.
   - On mobile screens (`< 768px`), desktop navigation is completely hidden.
   - There is NO hamburger icon button, NO off-canvas drawer, and NO dropdown for mobile users.
   - The fixed `.bottom-nav` (lines 346–369) only provides 5 buttons (হোম, কোর্স, নামাজ, বই, ব্লগ).
   - Mobile users CANNOT reach About Us, Methodology, Teachers, Branches, Contact, District Directors, Zakat, Tasbeeh, QR Scanner, or the Login Portal.
3. **Navigation Links Incomplete**:
   - Desktop header nav (lines 178–222) has no link or dropdown for institutional pages (About, Methodology, Teachers, Branches, Contact).
   - Footer (lines 320–328) "প্রয়োজনীয় লিংক" column only lists 4 links (directors, director login, blog, prayer times) and omits About, Methodology, Teachers, Branches, Contact.
4. **Font Configuration**:
   - Google Fonts `Amiri` and `Hind Siliguri` are imported in `<head>` (line 70).
   - Local verified Arabic font `public/assets/fonts/AAR-SQ-003.ttf` exists and is declared in `public/assets/css/main.css` under `@font-face` families `'AAR-SQ-003'` and `'KarianaQuran'`.
   - Tailwind config defines `sans`, `arabic`, `kariana`, but lacks `bengali: ['Hind Siliguri', 'sans-serif']` (which triggers unstyled fallback if `font-bengali` is used in views).

---

## 2. Logic Chain

1. **Routing Architecture**:
   - `index.php` checks `if (class_exists('\\App\\Controllers\\BlogController'))` at lines 118 and 131. When `app/Controllers/BlogController.php` is created under namespace `App\Controllers`, `core/Autoloader.php` will automatically autoload it without requiring changes to core autoloading logic.
2. **Blog System Flow**:
   - Users accessing `/blog` expect to browse articles by category, tag, or search term, with pagination.
   - A single SQL query joining `posts`, `categories`, and `users` with dynamic WHERE clauses handles all filter combinations securely using prepared PDO statements.
   - For `/blog/{slug}`, `Request::getPath()` automatically decodes UTF-8 Bengali slugs. The controller must fetch post details, increment `views_count`, generate Schema.org `Article` structured JSON-LD, and render related posts.
3. **Institutional Pages Flow**:
   - Pages like About Us, Methodology, Teachers, Branches, and Contact are essential trust signals for an educational institution and mandatory for Milestone 2.
   - Storing default content in the `pages` table and allowing `PageController` to query from `pages` allows both dynamic CMS editing (Milestone 4) and immediate rich presentation with specific layouts.
   - For Teachers, `PageController::teachers()` should query the `teachers` table joined with `directors` and `prayer_districts` to display real instructor profiles.
   - For Branches, `PageController::branches()` should query `directors` and `prayer_districts` grouped by division (Dhaka, Chittagong, Rajshahi, Khulna, Barishal, Sylhet, Rangpur, Mymensingh).
   - For Contact, `PageController` must support both `GET` (render form and central academy info) and `POST` (validate CSRF token, store inquiry or flash message).
4. **Master Layout & Mobile First UX**:
   - Over 85% of traffic in Bangladesh is mobile. An educational site without a mobile menu prevents prospective students from learning about methodology, teachers, and admissions.
   - Adding Alpine.js (`https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js`) provides zero-build, shared-hosting-compatible reactivity for an off-canvas drawer, dropdowns, and modal launchers.
   - An "একাডেমি" (Institution) dropdown on desktop and clear menu sections in the mobile drawer create a seamless user journey.

---

## 3. Caveats

1. **Database Connection**: In development, ensure MySQL/MariaDB service is running in XAMPP on port 3306 with database `kariana_portal`.
2. **Multi-device Port**: Dedicated port is `8015` (`http://localhost:8015` and `http://192.168.0.100:8015`), per ORIGINAL_REQUEST.md and CLI router setup.
3. **No External Build Daemons**: All frontend changes must use standalone Tailwind CDN and Alpine.js CDN without requiring npm, vite, or node daemons, guaranteeing 100% Shared Hosting drag-and-drop compatibility.

---

## 4. Conclusion & Actionable Blueprint for Worker

### Blueprint 1: Create `app/Controllers/BlogController.php`
Create class `App\Controllers\BlogController` with:
1. `index(Request $request): Response`:
   - Inputs: `$request->get('category')`, `$request->get('tag')`, `$request->get('q')`, `$request->get('page', 1)`.
   - SQL:
     ```sql
     SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name 
     FROM posts p 
     LEFT JOIN categories c ON p.category_id = c.id 
     LEFT JOIN users u ON p.author_id = u.id 
     WHERE p.status = 'published'
     /* + dynamic AND c.slug = :category */
     /* + dynamic AND p.tags LIKE :tag */
     /* + dynamic AND (p.title LIKE :q OR p.content LIKE :q) */
     ORDER BY p.created_at DESC 
     LIMIT :limit OFFSET :offset
     ```
   - Also fetch all categories with published post counts for filter pills and sidebar:
     ```sql
     SELECT c.*, COUNT(p.id) AS posts_count 
     FROM categories c 
     LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published' 
     GROUP BY c.id 
     ORDER BY c.sort_order ASC
     ```
   - Compute pagination: `$limit = 6`, `$offset = ($page - 1) * $limit`, `$totalPages = ceil($totalCount / $limit)`.
   - Render `blog/index` with `posts`, `categories`, `activeCategory`, `activeTag`, `searchQuery`, `page`, `totalPages`, `recentPosts`.
2. `show(Request $request, string $slug): Response`:
   - Decode slug: `$slug = rawurldecode($slug)`.
   - Retrieve post:
     ```sql
     SELECT p.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name 
     FROM posts p 
     LEFT JOIN categories c ON p.category_id = c.id 
     LEFT JOIN users u ON p.author_id = u.id 
     WHERE p.slug = :slug AND p.status = 'published' LIMIT 1
     ```
   - If not found: return `new Response(View::render('errors/404', [...]), 404)`.
   - View counter increment: `UPDATE posts SET views_count = views_count + 1 WHERE id = :id`.
   - Retrieve 3 related posts from same category:
     ```sql
     SELECT * FROM posts WHERE category_id = :cid AND id != :pid AND status = 'published' ORDER BY created_at DESC LIMIT 3
     ```
   - Construct Schema.org `Article` JSON-LD array:
     ```php
     $schema = [
         '@context'      => 'https://schema.org',
         '@type'         => 'Article',
         'headline'      => $post['title'],
         'description'   => $post['excerpt'] ?? mb_substr(strip_tags($post['content']), 0, 160, 'UTF-8'),
         'image'         => $post['cover_image'] ? $request->url($post['cover_image']) : $request->url('/assets/images/logo.png'),
         'datePublished' => date('c', strtotime($post['published_at'] ?? $post['created_at'])),
         'dateModified'  => date('c', strtotime($post['updated_at'] ?? $post['created_at'])),
         'author'        => [
             '@type' => 'Organization',
             'name'  => 'কারিয়ানা নূরানী কুরআন একাডেমি'
         ],
         'publisher'     => [
             '@type' => 'Organization',
             'name'  => 'কারিয়ানা কুরআন শিক্ষা সোসাইটি',
             'logo'  => ['@type' => 'ImageObject', 'url' => $request->url('/assets/images/logo.png')]
         ],
         'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $request->url('/blog/' . $post['slug'])]
     ];
     ```
   - Render `blog/show` passing `$post`, `$relatedPosts`, `$tags` (array), `$schemaJsonLd = $schema`, `$title`, `$description`, `$canonical`.

### Blueprint 2: Enhance Blog Views
1. `app/Views/blog/index.php`:
   - Hero header with breadcrumb navigation.
   - Category filter pill bar (All, Quran Learning, Tajweed, Hifz, Islamic Guidance, Academy News) with active styling (`bg-emerald-deep text-white` vs `bg-white text-slate-700`).
   - 2-column layout:
     - Left (col-span-8): Post cards with thumbnail, category badge, Bengali date (`BengaliHelper::toBengaliNumber`), views count, title, excerpt, "সম্পূর্ণ পড়ুন →" button.
     - Right (col-span-4): Sticky sidebar with search box form, category list with count badges, recent articles list, tag cloud.
   - Pagination bar with Bengali digits (`BengaliHelper::toBengaliNumber($i)`).
   - Empty state when no posts match query with "সকল ব্লগে ফিরে যান" link.
2. `app/Views/blog/show.php`:
   - Breadcrumb: হোম > ব্লগ > [ক্যাটাগরি] > [পোস্ট শিরোনাম].
   - Header with category pill, title, published date (Bengali), view counter, author badge.
   - Article content styled with Tailwind typography (`prose max-w-none text-slate-800 leading-relaxed font-sans`).
   - Clickable tags section with `#` badges linking to `/blog?tag={tag}`.
   - Social share bar: WhatsApp direct share link, Facebook share link, Twitter share link, and JavaScript "কপি লিংক" button with toast notification.
   - Author signature card: "কারিয়ানা নূরানী কুরআন একাডেমি রিসার্চ উইং".
   - Related articles grid (3 cards) with thumbnails and titles.

### Blueprint 3: Create `app/Controllers/PageController.php` & Views
1. Create `app/Controllers/PageController.php`:
   - `about(Request $request): Response`
   - `methodology(Request $request): Response`
   - `teachers(Request $request): Response`
   - `branches(Request $request): Response`
   - `contact(Request $request): Response`
   - `handleContact(Request $request): Response` (processes contact POST, validates CSRF, validates phone/email, sets flash message, redirects)
   - `show(Request $request, string $slug): Response` (dynamic CMS handler for custom pages)
2. Create directory `app/Views/pages/` and view files:
   - `pages/about.php`:
     - Vision, mission, history, founding scholars, Shariah advisory board.
     - Core statistics (students trained, teachers certified, 64 districts).
     - Methodology highlights with CTA to `/courses`.
   - `pages/methodology.php`:
     - The 12 proprietary Kariana phonetic/visual tajweed symbols explained with color-coded badges.
     - Visual diagram of Makharij zones (হালকী, লাহভী, শাজারী, তারফী, খাইশুম).
     - Comparison table: Traditional 1-2 year rote method vs Kariana 30-day scientific method.
     - Audio/visual sample cards using `font-kariana` and Arabic font `Amiri`.
   - `pages/teachers.php`:
     - Certified Muallim network directory queried from `teachers` and `directors`.
     - Filter tabs by division/district.
     - Teacher profile cards (name, qualification, assigned district, supervisor director).
     - Muallim training course & certification application CTA.
   - `pages/branches.php`:
     - Nationwide branch & markaz network across 64 districts.
     - Division navigation tabs (Dhaka, Chittagong, Rajshahi, Khulna, Barishal, Sylhet, Rangpur, Mymensingh).
     - District branch cards: local office address, coordinator name, hotline phone.
   - `pages/contact.php`:
     - Central office address (Dhaka, Bangladesh), hotline phone, official email.
     - Direct WhatsApp messaging button (`wa.me/880...`).
     - Interactive contact/inquiry form with CSRF field, validation feedback, and flash alert message.
     - Office hours & FAQ accordion.
   - `pages/show.php`:
     - Dynamic CMS page template with breadcrumb, title, rich text content, and publication date.

### Blueprint 4: Update Routes in `index.php`
Register the institutional page routes:
```php
// Dynamic Institutional Pages
$router->get('/about', function (\Core\Request $request) {
    $controller = new \App\Controllers\PageController();
    return $controller->about($request);
});
$router->get('/methodology', function (\Core\Request $request) {
    $controller = new \App\Controllers\PageController();
    return $controller->methodology($request);
});
$router->get('/teachers', function (\Core\Request $request) {
    $controller = new \App\Controllers\PageController();
    return $controller->teachers($request);
});
$router->get('/branches', function (\Core\Request $request) {
    $controller = new \App\Controllers\PageController();
    return $controller->branches($request);
});
$router->get('/contact', function (\Core\Request $request) {
    $controller = new \App\Controllers\PageController();
    return $controller->contact($request);
});
$router->post('/contact', function (\Core\Request $request) {
    $controller = new \App\Controllers\PageController();
    return $controller->handleContact($request);
});
$router->get('/page/{slug}', function (\Core\Request $request, string $slug) {
    $controller = new \App\Controllers\PageController();
    return $controller->show($request, $slug);
});
```
Also add these routes to `$staticRoutes` in `/sitemap.xml` (lines 431–442 of `index.php`).

### Blueprint 5: Update `core/SeoHelper.php` Breadcrumb Names
In `core/SeoHelper.php` line 236 (`getReadableBreadcrumbName`):
Add:
```php
'about'       => 'আমাদের পরিচিতি',
'methodology' => 'ক্বারীয়ানা শিক্ষাপদ্ধতি',
'teachers'    => 'মুয়াল্লিম ও শিক্ষকবৃন্দ',
'branches'    => 'দেশব্যাপী শাখাসমূহ',
'contact'     => 'যোগাযোগ',
```

### Blueprint 6: Update Master Layout `app/Views/layouts/main.php`
1. **Include Alpine.js**:
   Add in `<head>`:
   ```html
   <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
   ```
2. **Body State Initialization**:
   Add `x-data="{ mobileDrawer: false, toolsDropdown: false, portalModal: false }"` to `<body>`.
3. **Header Mobile Hamburger Button**:
   In `<header>` right next to the logo on mobile:
   ```html
   <button @click="mobileDrawer = true" type="button" class="md:hidden text-white p-2 rounded-lg bg-emerald-night/60 hover:bg-emerald-night border border-gold-rich/40">
       <i class="fas fa-bars text-xl text-gold-shimmer"></i>
   </button>
   ```
4. **Header Desktop Nav "একাডেমি" Dropdown**:
   Add an "একাডেমি" dropdown alongside Courses, Books, Directors, Tools, Blog:
   - আমাদের পরিচিতি (`/about`)
   - শিক্ষাপদ্ধতি ও ১২ সংকেত (`/methodology`)
   - শিক্ষক ও মুয়াল্লিমবৃন্দ (`/teachers`)
   - শাখাসমূহ ও মারকাজ (`/branches`)
   - যোগাযোগ (`/contact`)
5. **Full-Featured Mobile Slide-Over Drawer**:
   Add an Alpine.js slide-out drawer:
   ```html
   <div x-show="mobileDrawer" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-hidden bg-black/60 backdrop-blur-sm md:hidden" 
        style="display: none;">
       <div @click.away="mobileDrawer = false" 
            x-show="mobileDrawer"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="w-4/5 max-w-sm bg-[#fffefb] h-full shadow-2xl p-6 flex flex-col justify-between overflow-y-auto border-r-2 border-gold-rich">
           <!-- Drawer Header, Brand, Nav Sections, Portal Button, Close Button -->
       </div>
   </div>
   ```
6. **Footer Links Expansion**:
   Add links to `/about`, `/methodology`, `/teachers`, `/branches`, `/contact` in the footer navigation.
7. **Tailwind Config Polish**:
   Add `bengali: ['Hind Siliguri', 'sans-serif']` to `tailwind.config` in `main.php`.

### Blueprint 7: Seed Realistic Articles & Pages in Database
Create or update database seeder to insert:
1. 4–6 realistic Islamic articles into `posts` covering categories `quran-learning`, `tajweed-tartil`, `hifzul-quran`, `islamic-guidance` with rich Bengali text, clean Bengali slugs, and tags.
2. Initial records into `pages` table for `about`, `methodology`, `teachers`, `branches`, `contact` with proper `meta_title` and `meta_description`.

---

## 5. Verification Method

To independently verify after worker implements Milestone 2:

1. **Blog List & Filtering Test**:
   - `GET /blog`: verify HTTP 200, category filter pills present, search form present, sidebar present.
   - `GET /blog?category=quran-learning`: verify filtered list only contains matching posts.
   - `GET /blog?tag=তাজবীদ`: verify tag filter works.
   - `GET /blog?q=কুরআন`: verify search returns matching posts.
2. **Bengali Slug Article Detail Test**:
   - `GET /blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`: verify HTTP 200, article title displayed, view counter increments, Schema.org `Article` JSON-LD present in `<head>`, related posts displayed.
3. **Institutional Pages Test**:
   - `GET /about`: verify HTTP 200, mission/vision/history rendered.
   - `GET /methodology`: verify HTTP 200, 12 Kariana symbols & visual tajweed guide rendered.
   - `GET /teachers`: verify HTTP 200, certified muallim directory rendered.
   - `GET /branches`: verify HTTP 200, 64 district branches rendered with division tabs.
   - `GET /contact`: verify HTTP 200, contact info & CSRF-protected form rendered.
   - `POST /contact`: submit form with valid CSRF token; verify redirect and success flash message.
4. **Master Layout & Mobile Drawer Test**:
   - Verify Alpine.js `<script>` tag is present in `<head>`.
   - Inspect mobile viewport (< 768px): verify hamburger button appears in header, clicking opens off-canvas drawer containing links to all institutional pages, tools, and login portals.
   - Verify Google Fonts `Amiri` and `Hind Siliguri` are active and Arabic font `AAR-SQ-003.ttf` displays properly.
5. **SEO & Sitemap Test**:
   - `GET /sitemap.xml`: verify XML contains `/about`, `/methodology`, `/teachers`, `/branches`, `/contact`, and `/blog/{slug}`.
