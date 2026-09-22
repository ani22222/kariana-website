<?php
declare(strict_types=1);

namespace Core;

/**
 * Enterprise SEO & Structured Data (JSON-LD) Engine
 * Implements Google Search Console, Schema.org, OpenGraph, Twitter Cards, and BreadcrumbList standards.
 * Follows Global Maximum Level SEO Policy.
 */
class SeoHelper
{
    private static string $siteName = 'কারিয়ানা কুরআন শিক্ষা সোসাইটি';
    private static string $siteNameEn = 'Kariana Quran Academy Bangladesh';
    private static string $defaultDescription = 'সহজ, বৈজ্ঞানিক ও সহীহ পদ্ধতিতে পবিত্র কুরআন শিক্ষা, ১২টি তাজবীদ সংকেতযুক্ত আন্তর্জাতিক মানের প্রকাশনা এবং দেশব্যাপী ৬৪ জেলায় প্রশিক্ষণপ্রাপ্ত মুয়াল্লিম নেটওয়ার্ক।';

    /**
     * Get Base URL for canonical links
     */
    public static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8015';
        return rtrim($protocol . $host, '/');
    }

    /**
     * Render Complete HTML Head SEO Stack (Meta tags, OpenGraph, Twitter Card, and JSON-LD)
     */
    public static function renderHeadStack(array $options = []): string
    {
        $base = self::getBaseUrl();
        $title = $options['title'] ?? self::$siteName . ' | সহজ ও সহীহ পদ্ধতিতে কুরআন শিক্ষা';
        $description = $options['description'] ?? self::$defaultDescription;
        $keywords = $options['keywords'] ?? 'কারিয়ানা কুরআন, কুরআন শিক্ষা, নূরানী কায়দা, তাজবীদ, আমপারা শরীফ, সহজ কুরআন, নামাজের সময়সূচি, যাকাত ক্যালকুলেটর, জেলা পরিচালক';
        $canonical = $options['canonical'] ?? ($base . ($_SERVER['REQUEST_URI'] ?? '/'));
        $ogImage = $options['og_image'] ?? ($base . '/assets/images/branding/og-cover.jpg');
        $ogType = $options['og_type'] ?? 'website';
        $robots = $options['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';

        $html = "    <!-- Global Maximum-Level SEO Meta Directives -->\n";
        $html .= "    <meta name=\"description\" content=\"" . htmlspecialchars($description, ENT_QUOTES) . "\">\n";
        $html .= "    <meta name=\"keywords\" content=\"" . htmlspecialchars($keywords, ENT_QUOTES) . "\">\n";
        $html .= "    <meta name=\"robots\" content=\"" . htmlspecialchars($robots, ENT_QUOTES) . "\">\n";
        $html .= "    <link rel=\"canonical\" href=\"" . htmlspecialchars($canonical, ENT_QUOTES) . "\">\n";

        // OpenGraph
        $html .= "    <!-- OpenGraph / Facebook -->\n";
        $html .= "    <meta property=\"og:site_name\" content=\"" . htmlspecialchars(self::$siteName, ENT_QUOTES) . "\">\n";
        $html .= "    <meta property=\"og:title\" content=\"" . htmlspecialchars($title, ENT_QUOTES) . "\">\n";
        $html .= "    <meta property=\"og:description\" content=\"" . htmlspecialchars($description, ENT_QUOTES) . "\">\n";
        $html .= "    <meta property=\"og:type\" content=\"" . htmlspecialchars($ogType, ENT_QUOTES) . "\">\n";
        $html .= "    <meta property=\"og:url\" content=\"" . htmlspecialchars($canonical, ENT_QUOTES) . "\">\n";
        $html .= "    <meta property=\"og:image\" content=\"" . htmlspecialchars($ogImage, ENT_QUOTES) . "\">\n";
        $html .= "    <meta property=\"og:locale\" content=\"bn_BD\">\n";

        // Twitter Card
        $html .= "    <!-- Twitter Cards -->\n";
        $html .= "    <meta name=\"twitter:card\" content=\"summary_large_image\">\n";
        $html .= "    <meta name=\"twitter:title\" content=\"" . htmlspecialchars($title, ENT_QUOTES) . "\">\n";
        $html .= "    <meta name=\"twitter:description\" content=\"" . htmlspecialchars($description, ENT_QUOTES) . "\">\n";
        $html .= "    <meta name=\"twitter:image\" content=\"" . htmlspecialchars($ogImage, ENT_QUOTES) . "\">\n";

        // Structured Data JSON-LD
        $html .= self::renderJsonLd($options);

        return $html;
    }

    /**
     * Build and render Schema.org JSON-LD scripts
     */
    public static function renderJsonLd(array $options = []): string
    {
        $base = self::getBaseUrl();
        $schemas = [];

        // 1. Organization Schema
        $schemas[] = [
            '@context'    => 'https://schema.org',
            '@type'       => 'EducationalOrganization',
            'name'        => self::$siteName,
            'alternateName' => self::$siteNameEn,
            'url'         => $base,
            'logo'        => $base . '/assets/images/logo.png',
            'description' => self::$defaultDescription,
            'telephone'   => '+8801711756391',
            'contactPoint' => [
                '@type'            => 'ContactPoint',
                'telephone'        => '+8801711756391',
                'contactType'      => 'customer service',
                'areaServed'       => 'BD',
                'availableLanguage'=> ['Bengali', 'Arabic', 'English'],
            ],
            'address'     => [
                '@type'           => 'PostalAddress',
                'addressLocality' => 'Dhaka',
                'addressCountry'  => 'BD',
            ],
        ];

        // 2. WebSite Schema with SearchAction
        $schemas[] = [
            '@context'      => 'https://schema.org',
            '@type'         => 'WebSite',
            'name'          => self::$siteName,
            'url'           => $base,
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => $base . '/blog?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];

        // 3. Dynamic BreadcrumbList Schema
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
        $pathParts = array_values(array_filter(explode('/', trim($currentPath, '/'))));
        if (!empty($pathParts)) {
            $breadcrumbs = [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'হোম',
                    'item'     => $base,
                ]
            ];
            $cumulated = $base;
            $pos = 2;
            foreach ($pathParts as $part) {
                $cumulated .= '/' . $part;
                $decoded = rawurldecode($part);
                $name = self::getReadableBreadcrumbName($decoded);
                $breadcrumbs[] = [
                    '@type'    => 'ListItem',
                    'position' => $pos++,
                    'name'     => $name,
                    'item'     => $cumulated,
                ];
            }
            $schemas[] = [
                '@context'        => 'https://schema.org',
                '@type'           => 'BreadcrumbList',
                'itemListElement' => $breadcrumbs,
            ];
        }

        // 4. Custom Page Schemas (e.g. Book, Product, Course, SoftwareApplication for tools)
        if (!empty($options['schema'])) {
            if (isset($options['schema']['@context'])) {
                $schemas[] = $options['schema'];
            } elseif (is_array($options['schema'])) {
                foreach ($options['schema'] as $s) {
                    $schemas[] = $s;
                }
            }
        }

        // Automatic Tool Schema (SoftwareApplication) if on a devotional tool route
        if (in_array($currentPath, ['/prayer-times', '/zakat', '/tasbeeh', '/scan', '/quran', '/quran-bridge'], true)) {
            $schemas[] = self::getToolSchema($currentPath, $base);
        }

        $output = "    <!-- Schema.org Multi-Entity JSON-LD Structured Data Stack -->\n";
        foreach ($schemas as $item) {
            $json = json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $output .= "    <script type=\"application/ld+json\">\n" . $json . "\n    </script>\n";
        }

        return $output;
    }

    /**
     * Generate Structured Schema for Interactive Tools
     */
    private static function getToolSchema(string $path, string $base): array
    {
        $toolMap = [
            '/prayer-times' => [
                'name'        => '৬৪ জেলার নামাজের সময়সূচি ও সাহরী-ইফতার ট্র্যাকার',
                'description' => 'ইসলামিক ফাউন্ডেশন বাংলাদেশ কনভেনশন ভিত্তিক ৬৪টি জেলার নিখুঁত সালাত ও সাহরী/ইফতার সময়সূচি ক্যালকুলেটর।',
                'appCategory' => 'UtilitiesApplication',
            ],
            '/zakat' => [
                'name'        => 'হানাফী সিলভার নিসাব যাকাত ক্যালকুলেটর',
                'description' => 'হানাফী মাযহাব অনুসারে ৫২.৫ তোলা রূপার চলতি বাজারদরে যাকাত হিসাবের পূর্ণাঙ্গ ডিজিটাল টুল।',
                'appCategory' => 'FinanceApplication',
            ],
            '/tasbeeh' => [
                'name'        => 'ডিজিটাল তাসবীহ ও জিকির কাউন্টার',
                'description' => 'দৈনন্দিন জিকির ও অজিফা গণনার স্মুথ ও ভাইব্রেশন সমৃদ্ধ ডিজিটাল তাসবীহ।',
                'appCategory' => 'UtilitiesApplication',
            ],
            '/scan' => [
                'name'        => 'কারিয়ানা কুরআন কিউআর কোড লেসন স্ক্যানার',
                'description' => 'কারিয়ানা কুরআনের প্রতিটি পৃষ্ঠার কিউআর কোড স্ক্যান করে সরাসরি সহীহ তাজবীদ ভিডিও পাঠ চালুর গেটওয়ে।',
                'appCategory' => 'EducationalApplication',
            ],
            '/quran' => [
                'name'        => 'কারিয়ানা ডিজিটাল কুরআন ওয়েব রিডার',
                'description' => 'ভেরিফায়েড কারিয়ানা ফন্ট ও ১২টি তাজবীদ সংকেত সহ পবিত্র কুরআন ওয়েব রিডার।',
                'appCategory' => 'EducationalApplication',
            ],
            '/quran-bridge' => [
                'name'        => 'কারিয়ানা ডিজিটাল কুরআন ওয়েব রিডার ব্রিজ',
                'description' => 'ভেরিফায়েড কারিয়ানা ফন্ট ও ১২টি তাজবীদ সংকেত সহ পবিত্র কুরআন ওয়েব রিডার।',
                'appCategory' => 'EducationalApplication',
            ],
        ];

        $def = $toolMap[$path] ?? [
            'name'        => 'কারিয়ানা ইসলামিক টুল',
            'description' => 'দৈনন্দিন ইবাদতের আধুনিক ডিজিটাল সহায়িকা।',
            'appCategory' => 'UtilitiesApplication',
        ];

        return [
            '@context'            => 'https://schema.org',
            '@type'               => 'WebApplication',
            'name'                => $def['name'],
            'url'                 => $base . $path,
            'description'         => $def['description'],
            'applicationCategory' => $def['appCategory'],
            'operatingSystem'     => 'All Modern Browsers, Android, iOS, Windows, Mac',
            'browserRequirements'=> 'Requires JavaScript. Requires HTML5.',
            'offers'              => [
                '@type'         => 'Offer',
                'price'         => '0.00',
                'priceCurrency' => 'BDT',
            ],
        ];
    }

    /**
     * Map slug or path to friendly Bengali name for breadcrumbs
     */
    private static function getReadableBreadcrumbName(string $slug): string
    {
        $map = [
            'books'        => 'প্রকাশনা ও গ্রন্থসমূহ',
            'courses'      => 'কোর্সসমূহ',
            'blog'         => 'ইসলামী ব্লগ',
            'directors'    => 'জেলা পরিচালকবৃন্দ',
            'prayer-times' => 'নামাজের সময়সূচি',
            'zakat'        => 'যাকাত ক্যালকুলেটর',
            'tasbeeh'      => 'ডিজিটাল তাসবীহ',
            'scan'         => 'কিউআর স্ক্যানার',
            'quran'        => 'কুরআন ওয়েব রিডার',
            'quran-bridge' => 'কুরআন ওয়েব রিডার ব্রিজ',
            'admin'        => 'অ্যাডমিন পোর্টাল',
            'manager'      => 'ব্যবস্থাপক পোর্টাল',
            'director'     => 'পরিচালক পোর্টাল',
            'teacher'      => 'শিক্ষক পোর্টাল',
            'login'        => 'লগইন',
            'dashboard'    => 'ড্যাশবোর্ড',
            'ledger'       => 'হিসাব খাতা',
            'distributions'=> 'বই বিতরণ',
        ];

        return $map[$slug] ?? ucwords(str_replace(['-', '_'], ' ', $slug));
    }
}
