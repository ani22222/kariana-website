# Handoff Report: SEO & Marketing Infrastructure Investigation (M2 #3)

**Author:** Explorer M2 #3  
**Working Directory:** `c:\xampp\htdocs\Kariana Website\.agents\explorer_m2_3\`  
**Target Audience:** Worker implementing M2 SEO & Marketing Infrastructure  
**Date:** 2026-09-22  

---

## 1. Observation

### Obs 1.1: Schema.org JSON-LD Generation in `core/SeoHelper.php`
- In `core/SeoHelper.php` (lines 78–167), `renderJsonLd()` generates:
  - `EducationalOrganization` (`lines 79–100`):
    ```php
    $schemas[] = [
        '@context'    => 'https://schema.org',
        '@type'       => 'EducationalOrganization',
        'name'        => self::$siteName,
        'alternateName' => self::$siteNameEn,
        'url'         => $base,
        'logo'        => $base . '/assets/images/logo.png',
        ...
    ];
    ```
  - `WebSite` with `SearchAction` (`lines 104–113`).
  - `BreadcrumbList` (`lines 116–145`): Generated only when `pathParts` is non-empty (`$currentPath !== '/'`).
  - `WebApplication` (`lines 159–161`): Injected when the request path matches `/prayer-times`, `/zakat`, `/tasbeeh`, `/scan`, `/quran`, or `/quran-bridge`.
- **Missing Schema.org entities**:
  - `Article` (or `BlogPosting`): Neither `core/SeoHelper.php`, `index.php` (route `/blog/{slug}`, lines 130–145), nor `app/Views/blog/show.php` generate or pass an `Article` Schema.org JSON-LD payload.
  - `Course`: Neither `core/SeoHelper.php`, `index.php` (route `/courses/{slug}`, lines 86–101), nor `app/Views/courses/show.php` generate a `Course` Schema.org payload.
  - `FAQPage`: Neither `core/SeoHelper.php` nor any view or controller creates a `FAQPage` Schema.org payload.
  - There is no `App\Services\SeoService.php` class in the codebase (`app/Services/` directory does not exist).

### Obs 1.2: Dynamic XML Sitemap (`/sitemap.xml`) in `index.php`
- In `index.php` (lines 423–500), the `/sitemap.xml` route is declared inline as a closure:
  - Generates `<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">`.
  - Static URLs: `/`, `/courses`, `/books`, `/blog`, `/directors`, `/prayer-times`, `/zakat`, `/tasbeeh`, `/scan`, `/quran` with `<changefreq>` and `<priority>`. Static URLs omit `<lastmod>`.
  - Dynamic entities queried from MariaDB:
    - `courses`: `SELECT slug, updated_at FROM courses WHERE admission_open = 1`
    - `books`: `SELECT slug, updated_at FROM books`
    - `posts`: `SELECT slug, updated_at FROM posts WHERE status = 'published'` (encoded via `rawurlencode($p['slug'])` at line 478)
    - `directors`: `SELECT slug, id, updated_at FROM directors WHERE status = 'active'`
  - Returns `new \Core\Response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8'])`.
- Omissions:
  - Institutional pages (`/about`, `/methodology`, `/teachers`, `/branches`, `/contact`) are omitted from the sitemap.
  - No database exception handling (`try / catch`) surrounds the SQL queries; a database fault during sitemap crawl results in an uncaught 500 error instead of a graceful fallback.

### Obs 1.3: Robots.txt (`/robots.txt`) in `index.php`
- In `index.php` (lines 503–515), `/robots.txt` returns:
  ```text
  User-agent: *
  Allow: /
  Disallow: /admin/
  Disallow: /manager/
  Disallow: /director/dashboard
  Disallow: /teacher/dashboard
  Disallow: /api/
  Sitemap: http://localhost:8015/sitemap.xml
  ```
- Complies with crawler directives and points to dynamic sitemap URL via `\Core\SeoHelper::getBaseUrl()`.

### Obs 1.4: Social OpenGraph and Twitter Cards in `core/SeoHelper.php` & Layout
- In `core/SeoHelper.php` (lines 30–67):
  - Emits `og:site_name`, `og:title`, `og:description`, `og:type`, `og:url`, `og:image`, `og:locale` (`bn_BD`).
  - Emits `twitter:card` (`summary_large_image`), `twitter:title`, `twitter:description`, `twitter:image`.
  - Emits canonical `<link rel="canonical">` and meta `description`, `keywords`, `robots`.
- In `app/Views/layouts/main.php` (lines 9–15):
  ```php
  <?= \Core\SeoHelper::renderHeadStack([
      'title'       => $title ?? null,
      'description' => $description ?? null,
      'keywords'    => $keywords ?? null,
      'canonical'   => $canonical ?? null,
      'schema'      => $schemaJsonLd ?? null,
  ]) ?>
  ```
  Notice: `og_image`, `og_type`, and `robots` are **not passed** into `renderHeadStack()` from `$data`.
- In `public/assets/`:
  - `public/assets/` contains only `css/`, `fonts/`, and `js/`.
  - `public/assets/images/` does not exist on disk.
  - Default image paths `og-cover.jpg` (`/assets/images/branding/og-cover.jpg`) and logo (`/assets/images/logo.png`) result in HTTP 404 on crawlers.

### Obs 1.5: Marketing & External Integrations Hub in Layout and Admin
- In `app/Views/layouts/main.php`:
  - Lines 21–25: Queries `site_settings` directly in the view template (`SELECT setting_key, setting_value FROM site_settings WHERE group_name = 'marketing'`).
  - Lines 29–43: Facebook Pixel (`fb_pixel_id` snippet and `fb_pixel_script`).
  - Lines 46–51: Search Console (`google_search_console_tag`) and Bing (`bing_webmaster_tag`).
  - Lines 54–62: Google Analytics 4 gtag (`google_analytics_id`).
  - Lines 65–67: `custom_head_scripts`.
  - Lines 134–140: GTM `<noscript>` iframe (`gtm_id`) and `custom_body_start_scripts`.
  - Lines 372–374: `custom_body_end_scripts`.
- **Missing Component**: Google Tag Manager requires a primary JavaScript snippet in `<head>` in addition to `<noscript>` in `<body>`. The `<head>` block in `app/Views/layouts/main.php` has no conditional snippet for `gtm_id`.
- In `app/Controllers/AdminController.php` (lines 147–193) & `app/Views/admin/settings.php`:
  - Full CRUD interface for all 9 marketing setting keys exists and is fully functional.

### Obs 1.6: E2E Test Expectations (`tests/e2e/`)
- `Tier1_FeatureCoverageTest.php`:
  - `testDynamicXmlSitemapValidity()` (lines 250–259): Asserts HTTP 200, valid XML, root element `<urlset>`, count of `<url>` > 0, first `<loc>` starts with `http`.
  - `testRobotsTxtDirectives()` (lines 264–272): Asserts HTTP 200, contains `User-agent:` and `Sitemap:`.
  - `testSchemaOrgJsonLdPresenceInHtml()` (lines 277–289): Asserts HTTP 200 on `/`, runs `$res->assertValidJsonLd()` which verifies presence and valid JSON syntax of `<script type="application/ld+json">`.
- `Tier3_CrossFeatureTest.php`:
  - `testBlogPostLifecycleAndSitemapInclusion()` (lines 127–135): Asserts that after creating a blog post with a Bengali slug, `/sitemap.xml` contains `$slug` or `rawurlencode($slug)`.
- `TestResponse.php` (lines 168–209):
  - `assertValidJsonLd(?string $expectedType = null)` checks either top-level `@type` or elements inside `@graph`. If `$expectedType` is provided (e.g. `Article`, `Course`, `FAQPage`, `BreadcrumbList`), it enforces an exact match.

---

## 2. Logic Chain

1. **Schema.org Specification vs Implementation Gap**:
   - `ORIGINAL_REQUEST.md` (R1) and `PROJECT.md` (Feature 15) specify automated Schema.org markup for 5 distinct entities: `Organization`, `Article`, `Course`, `FAQPage`, and `BreadcrumbList`.
   - `core/SeoHelper.php` currently generates `EducationalOrganization` (Organization) and `BreadcrumbList` (on subpaths).
   - Because `Article`, `Course`, and `FAQPage` are absent, visiting `/blog/{slug}` lacks an `Article` microdata block, visiting `/courses/{slug}` lacks a `Course` microdata block, and there is no `FAQPage` microdata block on the site.
   - If an E2E test or external auditor invokes `$res->assertValidJsonLd('Article')` or `$res->assertValidJsonLd('Course')`, it will throw an `AssertionError`.

2. **Breadcrumb Name Fallback in `core/SeoHelper.php`**:
   - `core/SeoHelper::getReadableBreadcrumbName()` maintains a static array map of predefined English route segments.
   - When a user visits dynamic Bengali slug routes (e.g. `/courses/tajweed-shikkha` or `/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`), the slug is not in the map, so line 259 executes `ucwords(str_replace(['-', '_'], ' ', $slug))`.
   - This causes the BreadcrumbList schema to display English title strings or raw slug hyphens instead of the actual entity title. Allowing controllers/views to pass custom breadcrumb items or passing entity titles fixes this.

3. **Separation of Concerns & Architecture (`App\Services\SeoService`)**:
   - Currently, `/sitemap.xml` and `/robots.txt` are implemented as procedural closures spanning ~90 lines directly inside `index.php`.
   - `PROJECT.md` and `ORIGINAL_REQUEST.md` specify an SEO service infrastructure (`App\Services\SeoService.php` or `Core\Seo`).
   - Extracting `generateSitemapXml()`, `generateRobotsTxt()`, and entity-specific schema generators (`articleSchema()`, `courseSchema()`, `faqSchema()`) into `App\Services\SeoService.php` (or expanding `Core\SeoHelper.php`) centralizes SEO logic, allows unit testing without HTTP boots, and keeps `index.php` clean.

4. **Marketing Hub GTM Discrepancy**:
   - The admin UI allows setting `gtm_id` (e.g. `GTM-XXXXXXX`).
   - In `app/Views/layouts/main.php`, only the `<body>` fallback (`<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=..."></iframe></noscript>`) is rendered.
   - Without the `<head>` JavaScript snippet for Google Tag Manager, GTM will never initialize or track page views on client browsers.

5. **OpenGraph Broken Assets**:
   - `renderHeadStack()` specifies default images: `$base . '/assets/images/branding/og-cover.jpg'` and `$base . '/assets/images/logo.png'`.
   - Neither the `images/` directory nor these files exist in `public/assets/`.
   - Crawlers fetching OpenGraph images will receive HTTP 404, breaking rich link previews on social platforms. Providing fallback images or SVG placeholders resolves this.

---

## 3. Caveats

1. **E2E Test Lenience in Tier 1**:
   - `Tier1_FeatureCoverageTest.php::testSchemaOrgJsonLdPresenceInHtml()` only tests `/` and does not pass an explicit `$expectedType` to `assertValidJsonLd()`. Consequently, Tier 1 currently passes because `EducationalOrganization` is detected. However, subsequent tiers or forensic audits checking for `Article`, `Course`, and `FAQPage` will require these schemas.
2. **Database Dependency of Sitemap**:
   - The sitemap dynamically pulls from `courses`, `books`, `posts`, and `directors`. If the database contains no records or is unreachable, the query must fail gracefully.
3. **Read-Only Explorer Scope**:
   - Per role constraints, no source code files were edited during this investigation. All recommended changes are provided with concrete snippets for the implementer worker.

---

## 4. Conclusion

The current SEO implementation in `Core\SeoHelper.php` and `index.php` provides a strong baseline (valid XML sitemap, functional robots.txt, OpenGraph tags, and base Organization schema), but has four concrete gaps:
1. **Missing Schema.org Types**: `Article`, `Course`, and `FAQPage` are missing from the schema generation stack.
2. **Missing Service Encapsulation**: Sitemap and robots logic are inlined in `index.php`; `App\Services\SeoService.php` has not yet been created.
3. **Layout Integration Flaws**:
   - `app/Views/layouts/main.php` does not forward `og_image` and `og_type` to `SeoHelper::renderHeadStack()`.
   - GTM `<head>` script snippet is missing when `gtm_id` is configured.
   - `SELECT` query runs inside the layout on every request without caching.
4. **Missing Asset Files**: `public/assets/images/branding/og-cover.jpg` and `public/assets/images/logo.png` are referenced but missing on disk.

---

## 5. Verification Method & Actionable Worker Recommendations

### Concrete Actionable Recommendations for Worker

#### Step 1: Create `App\Services\SeoService.php` (or enrich `Core\SeoHelper.php`)
Implement methods:
```php
namespace App\Services;

use Core\SeoHelper;
use Core\Database;

class SeoService
{
    /**
     * Generate Schema.org Article JSON-LD
     */
    public static function articleSchema(array $post): array
    {
        $base = SeoHelper::getBaseUrl();
        $slug = $post['slug'] ?? '';
        return [
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            'headline'         => $post['title'] ?? '',
            'description'      => $post['excerpt'] ?? $post['meta_description'] ?? '',
            'image'            => !empty($post['cover_image']) ? $base . $post['cover_image'] : $base . '/assets/images/branding/og-cover.jpg',
            'datePublished'    => date('c', strtotime($post['published_at'] ?? $post['created_at'] ?? 'now')),
            'dateModified'     => date('c', strtotime($post['updated_at'] ?? $post['created_at'] ?? 'now')),
            'mainEntityOfPage' => $base . '/blog/' . rawurlencode(rawurldecode($slug)),
            'author'           => [
                '@type' => 'EducationalOrganization',
                'name'  => 'কারিয়ানা কুরআন শিক্ষা সোসাইটি',
                'url'   => $base,
            ],
            'publisher'        => [
                '@type' => 'EducationalOrganization',
                'name'  => 'কারিয়ানা কুরআন শিক্ষা সোসাইটি',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => $base . '/assets/images/logo.png',
                ],
            ],
        ];
    }

    /**
     * Generate Schema.org Course JSON-LD
     */
    public static function courseSchema(array $course): array
    {
        $base = SeoHelper::getBaseUrl();
        return [
            '@context'    => 'https://schema.org',
            '@type'       => 'Course',
            'name'        => $course['title'] ?? '',
            'description' => $course['description'] ?? '',
            'provider'    => [
                '@type'  => 'EducationalOrganization',
                'name'   => 'কারিয়ানা কুরআন শিক্ষা সোসাইটি',
                'sameAs' => $base,
            ],
            'offers'      => [
                '@type'         => 'Offer',
                'category'      => 'Islamic Education',
                'price'         => preg_replace('/[^\d.]/', '', (string)($course['fee'] ?? '0')) ?: '0',
                'priceCurrency' => 'BDT',
            ],
        ];
    }

    /**
     * Generate Schema.org FAQPage JSON-LD
     */
    public static function faqSchema(array $faqItems): array
    {
        $mainEntity = [];
        foreach ($faqItems as $q => $a) {
            $mainEntity[] = [
                '@type'          => 'Question',
                'name'           => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $a,
                ],
            ];
        }
        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Generate Dynamic XML Sitemap Content
     */
    public static function generateSitemapXml(): string
    {
        // Encapsulate the ~80 lines from index.php with try/catch and lastmod on all URLs
    }

    /**
     * Generate Robots.txt Content
     */
    public static function generateRobotsTxt(): string
    {
        $base = SeoHelper::getBaseUrl();
        return "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /manager/\nDisallow: /director/dashboard\nDisallow: /teacher/dashboard\nDisallow: /api/\nSitemap: {$base}/sitemap.xml\n";
    }
}
```

#### Step 2: Update `index.php` and Public Routes
1. In `/blog/{slug}`:
   Pass `'description'`, `'og_type' => 'article'`, and `'schemaJsonLd' => \App\Services\SeoService::articleSchema($post)`.
2. In `/courses/{slug}`:
   Pass `'description'`, and `'schemaJsonLd' => \App\Services\SeoService::courseSchema($course)`.
3. In `/` (homepage):
   Include default FAQ schema or pass `$schemaJsonLd` containing Organization + FAQ.
4. Delegate `/sitemap.xml` and `/robots.txt` routes in `index.php` directly to `SeoService`:
   ```php
   $router->get('/sitemap.xml', fn() => new \Core\Response(\App\Services\SeoService::generateSitemapXml(), 200, ['Content-Type' => 'application/xml; charset=UTF-8']));
   $router->get('/robots.txt', fn() => new \Core\Response(\App\Services\SeoService::generateRobotsTxt(), 200, ['Content-Type' => 'text/plain; charset=UTF-8']));
   ```

#### Step 3: Fix `app/Views/layouts/main.php`
1. Forward `og_image` and `og_type`:
   ```php
   <?= \Core\SeoHelper::renderHeadStack([
       'title'       => $title ?? null,
       'description' => $description ?? null,
       'keywords'    => $keywords ?? null,
       'canonical'   => $canonical ?? null,
       'og_image'    => $ogImage ?? null,
       'og_type'     => $ogType ?? null,
       'schema'      => $schemaJsonLd ?? null,
   ]) ?>
   ```
2. Add GTM `<head>` script snippet when `gtm_id` is set:
   ```php
   <?php if (!empty($mkt['gtm_id'])): ?>
   <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
   new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
   j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
   'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
   })(window,document,'script','dataLayer','<?= htmlspecialchars($mkt['gtm_id']) ?>');</script>
   <?php endif; ?>
   ```

#### Step 4: Ensure Fallback Branding Assets Exist
Create `public/assets/images/branding/` with:
- `og-cover.jpg` (or standard fallback graphic)
- `public/assets/images/logo.png`

### Verification Commands
```powershell
# 1. Run full Tier 1 Feature Coverage Test
php tests/e2e/runner.php --tier=1 --kernel

# 2. Run Tier 3 Cross Feature Test (tests blog lifecycle & sitemap inclusion)
php tests/e2e/runner.php --tier=3 --kernel

# 3. Direct verification of XML Sitemap & Robots endpoints
php -r "require 'core/Autoloader.php'; \Core\Autoloader::register(); echo \App\Services\SeoService::generateRobotsTxt();"
```
