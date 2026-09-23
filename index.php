<?php
/**
 * Kariana Quran Islamic Educational Portal & CMS
 * Front Controller & Central Request Dispatcher
 */

declare(strict_types=1);

// 1. Register Zero-Vendor PSR-4 Autoloader
require_once __DIR__ . '/core/Autoloader.php';
\Core\Autoloader::register();

// Ensure verified Kariana Arabic Font is deployed in public/assets/fonts/
$fontDest = __DIR__ . '/public/assets/fonts/AAR-SQ-003.ttf';
if (!file_exists($fontDest)) {
    $fontDir = dirname($fontDest);
    if (!is_dir($fontDir)) {
        @mkdir($fontDir, 0777, true);
    }
    $fontSources = [
        'c:/xampp/htdocs/Kariana Quran ReMakiking In In design/Font/AAR-SQ-003.ttf',
        'c:/xampp/htdocs/Kariana Quran ReMakiking In In design/DELIVERABLES/AAR-SQ-003.ttf',
        dirname(__DIR__) . '/Kariana Quran ReMakiking In In design/Font/AAR-SQ-003.ttf',
    ];
    foreach ($fontSources as $src) {
        if (file_exists($src)) {
            @copy($src, $fontDest);
            break;
        }
    }
}

// 2. Initialize Application Kernel
$app = new \Core\App();
$router = $app->getRouter();

// 3. Load External Routes if defined, otherwise define core route handlers
$routesFile = __DIR__ . '/app/routes.php';
if (file_exists($routesFile)) {
    require_once $routesFile;
} else {
    // --- Public Web Routes ---

    // Homepage
    $router->get('/', function (\Core\Request $request) {
        if (class_exists('\\App\\Controllers\\HomeController')) {
            $controller = new \App\Controllers\HomeController();
            return $controller->index($request);
        }

        // Default Home Rendering
        $db = \Core\Database::getInstance();
        $featuredCourses = $db->query("SELECT * FROM `courses` WHERE `admission_open` = 1 ORDER BY `sort_order` ASC LIMIT 4")->fetchAll();
        $featuredBooks = $db->query("SELECT * FROM `books` WHERE `is_featured` = 1 ORDER BY `sort_order` ASC, `id` ASC LIMIT 10")->fetchAll();
        if (empty($featuredBooks)) {
            $featuredBooks = $db->query("SELECT * FROM `books` ORDER BY `sort_order` ASC, `id` ASC LIMIT 10")->fetchAll();
        }
        $recentPosts = $db->query("SELECT * FROM `posts` WHERE `status` = 'published' ORDER BY `created_at` DESC LIMIT 3")->fetchAll();
        $featuredDirectors = $db->query("SELECT * FROM `directors` WHERE `status` = 'active' ORDER BY `id` ASC LIMIT 4")->fetchAll();
        $totalDirectorsCount = (int)$db->query("SELECT COUNT(*) FROM `directors` WHERE `status` = 'active'")->fetchColumn();
        $totalTeachersCount = (int)$db->query("SELECT COUNT(*) FROM `teachers`")->fetchColumn();

        return \Core\View::render('home/index', [
            'title'               => 'কারিয়ানা কুরআন | সহজ ও সহীহ পদ্ধতিতে কুরআন শিক্ষা',
            'featuredCourses'     => $featuredCourses,
            'featuredBooks'       => $featuredBooks,
            'recentPosts'         => $recentPosts,
            'featuredDirectors'   => $featuredDirectors,
            'totalDirectorsCount' => $totalDirectorsCount,
            'totalTeachersCount'  => $totalTeachersCount,
        ]);
    });

    // --- Universal Single Multi-Role Authentication & Self-Registration Routes ---
    $router->get('/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\AuthController();
        return $controller->login($request);
    });

    $router->post('/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\AuthController();
        return $controller->handleLogin($request);
    });

    $router->post('/register', function (\Core\Request $request) {
        $controller = new \App\Controllers\AuthController();
        return $controller->handleRegister($request);
    });

    $router->get('/api/check-phone', function (\Core\Request $request) {
        $controller = new \App\Controllers\AuthController();
        return $controller->checkPhone($request);
    });

    $router->get('/logout', function (\Core\Request $request) {
        $controller = new \App\Controllers\AuthController();
        return $controller->logout();
    });

    // --- Smart Dynamic PWA App Launcher ---
    $router->get('/app-launcher', function (\Core\Request $request) {
        if (\Core\Session::has('director_id')) {
            return \Core\Response::redirect('/director/dashboard');
        }
        if (\Core\Session::has('teacher')) {
            return \Core\Response::redirect('/teacher/dashboard');
        }
        if (\Core\Session::has('manager')) {
            return \Core\Response::redirect('/manager/dashboard');
        }
        $user = \Core\Session::getUser();
        if ($user) {
            if (($user['role'] ?? '') === 'admin') {
                return \Core\Response::redirect('/admin');
            }
            return \Core\Response::redirect('/profile');
        }
        return \Core\Response::redirect('/login?source=pwa');
    });

    // --- Student & General User Profile Routes ---
    $router->get('/profile', function (\Core\Request $request) {
        $controller = new \App\Controllers\StudentController();
        return $controller->dashboard($request);
    });

    $router->post('/profile/update', function (\Core\Request $request) {
        $controller = new \App\Controllers\StudentController();
        return $controller->updateProfile($request);
    });

    $router->post('/profile/password', function (\Core\Request $request) {
        $controller = new \App\Controllers\StudentController();
        return $controller->updatePassword($request);
    });

    // Courses List
    $router->get('/courses', function (\Core\Request $request) {
        if (class_exists('\\App\\Controllers\\CourseController')) {
            $controller = new \App\Controllers\CourseController();
            return $controller->index($request);
        }
        $db = \Core\Database::getInstance();
        $courses = $db->query("SELECT * FROM `courses` ORDER BY `sort_order` ASC")->fetchAll();
        return \Core\View::render('courses/index', [
            'title'   => 'সকল কোর্সসমূহ | কারিয়ানা কুরআন',
            'courses' => $courses,
        ]);
    });

    // Course Detail
    $router->get('/courses/{slug}', function (\Core\Request $request, string $slug) {
        if (class_exists('\\App\\Controllers\\CourseController')) {
            $controller = new \App\Controllers\CourseController();
            return $controller->show($request, $slug);
        }
        $stmt = \Core\Database::getInstance()->prepare("SELECT * FROM `courses` WHERE `slug` = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $course = $stmt->fetch();
        if (!$course) {
            return new \Core\Response('কোর্সটি পাওয়া যায়নি', 404);
        }
        return \Core\View::render('courses/show', [
            'title'  => $course['title'] . ' | কারিয়ানা কুরআন',
            'course' => $course,
        ]);
    });

    // Books Catalog
    $router->get('/books', function (\Core\Request $request) {
        if (class_exists('\\App\\Controllers\\BookController')) {
            $controller = new \App\Controllers\BookController();
            return $controller->index($request);
        }
        $books = \Core\Database::getInstance()->query("SELECT * FROM `books` ORDER BY `sort_order` ASC")->fetchAll();
        return \Core\View::render('books/index', [
            'title' => 'ক্বারীয়ানা প্রকাশনা ও গ্রন্থসমূহ | কারিয়ানা কুরআন',
            'books' => $books,
        ]);
    });

    // Blog Index
    $router->get('/blog', function (\Core\Request $request) {
        if (class_exists('\\App\\Controllers\\BlogController')) {
            $controller = new \App\Controllers\BlogController();
            return $controller->index($request);
        }
        $posts = \Core\Database::getInstance()->query("SELECT * FROM `posts` WHERE `status` = 'published' ORDER BY `created_at` DESC")->fetchAll();
        return \Core\View::render('blog/index', [
            'title' => 'ইসলামী ব্লগ ও সংবাদ | কারিয়ানা কুরআন',
            'posts' => $posts,
        ]);
    });

    // Blog Post Detail with Clean Bengali Unicode Slug: /blog/সহজ-পদ্ধতিতে-কুরআন-শেখা
    $router->get('/blog/{slug}', function (\Core\Request $request, string $slug) {
        if (class_exists('\\App\\Controllers\\BlogController')) {
            $controller = new \App\Controllers\BlogController();
            return $controller->show($request, $slug);
        }
        $stmt = \Core\Database::getInstance()->prepare("SELECT * FROM `posts` WHERE `slug` = :slug LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();
        if (!$post) {
            return new \Core\Response('পোস্টটি পাওয়া যায়নি', 404);
        }
        return \Core\View::render('blog/show', [
            'title' => $post['title'] . ' | কারিয়ানা কুরআন',
            'post'  => $post,
        ]);
    });

    // Privacy Policy Page (Telegram Bot & PWA Verified)
    $router->get('/privacy', function (\Core\Request $request) {
        return \Core\View::render('home/privacy', [
            'title' => 'গোপনীয়তা নীতি (Privacy Policy) — কারিয়ানা কুরআন',
            'metaDescription' => 'কারিয়ানা কুরআন শিক্ষা বোর্ড ও প্রকাশনীর গোপনীয়তা নীতি ও আইনি তথ্যাবলি।'
        ]);
    });

    // Prayer Times & Utilities
    $router->get('/prayer-times', function (\Core\Request $request) {
        if (class_exists('\\App\\Controllers\\UtilityController')) {
            $controller = new \App\Controllers\UtilityController();
            return $controller->prayerTimes($request);
        }
        $districts = \Core\Database::getInstance()->query("SELECT * FROM `prayer_districts` ORDER BY `name_bn` ASC")->fetchAll();
        return \Core\View::render('utilities/prayer-times', [
            'title'     => '৬৪ জেলার নামাজের সময়সূচি | কারিয়ানা কুরআন',
            'districts' => $districts,
        ]);
    });

    // Zakat Calculator
    $router->get('/zakat', function (\Core\Request $request) {
        if (class_exists('\\App\\Controllers\\UtilityController')) {
            $controller = new \App\Controllers\UtilityController();
            return $controller->zakat($request);
        }
        $settings = \Core\Database::getInstance()->query("SELECT * FROM `zakat_settings` WHERE `id` = 1 LIMIT 1")->fetch();
        return \Core\View::render('utilities/zakat', [
            'title'    => 'হানাফী সিলভার নিসাব যাকাত ক্যালকুলেটর | কারিয়ানা কুরআন',
            'settings' => $settings,
        ]);
    });

    // Digital Tasbeeh
    $router->get('/tasbeeh', function (\Core\Request $request) {
        return \Core\View::render('utilities/tasbeeh', [
            'title' => 'ডিজিটাল তাসবীহ কাউন্টার | কারিয়ানা কুরআন',
        ]);
    });

    // QR Scan Gateway
    $router->get('/scan', function (\Core\Request $request) {
        $code = $request->get('code') ?? $request->get('page');
        return \Core\View::render('scan/index', [
            'title' => 'কিউআর কোড স্ক্যানার ও পাঠ নির্দেশিকা | কারিয়ানা কুরআন',
            'code'  => $code,
        ]);
    });

    // Health Check API
    $router->get('/api/health', function (\Core\Request $request) {
        return \Core\Response::json([
            'status'      => 'ok',
            'portal'      => 'কারিয়ানা কুরআন (Kariana Quran)',
            'port'        => 8015,
            'time'        => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION,
            'timezone'    => date_default_timezone_get(),
        ]);
    });

    // 64 Districts API
    $router->get('/api/districts', function (\Core\Request $request) {
        $districts = \Core\Database::getInstance()->query("SELECT * FROM `prayer_districts` ORDER BY `name_bn` ASC")->fetchAll();
        return \Core\Response::json([
            'status'    => 'success',
            'count'     => count($districts),
            'districts' => $districts,
        ]);
    });

    // Worker M1 Verification Suite API
    $router->get('/api/verify_m1', function (\Core\Request $request) {
        $testFile = __DIR__ . '/tests/unit/test_m1.php';
        if (file_exists($testFile)) {
            require_once $testFile;
            $report = runM1Verification();
            return \Core\Response::json($report, 200);
        }
        return \Core\Response::json(['error' => 'Verification test file not found'], 404);
    });

    // Quran Reader Launchpad & Bridge
    $router->get('/quran-bridge', function (\Core\Request $request) {
        return \Core\View::render('quran/bridge', [
            'title' => 'কুরআন ওয়েব রিডার ও ব্রিজ | কারিয়ানা কুরআন',
        ]);
    });
    $router->get('/quran', function (\Core\Request $request) {
        return \Core\View::render('quran/bridge', [
            'title' => 'কুরআন ওয়েব রিডার ও ব্রিজ | কারিয়ানা কুরআন',
        ]);
    });

    // --- Centralized 2-Step KYC Verification & Public Credential Routes ---
    $router->get('/verify/kyc', function (\Core\Request $request) {
        return (new \App\Controllers\VerificationController())->kycPortal($request);
    });
    $router->post('/verify/kyc/send-otp', function (\Core\Request $request) {
        return (new \App\Controllers\VerificationController())->sendOtp($request);
    });
    $router->post('/verify/kyc/verify-otp', function (\Core\Request $request) {
        return (new \App\Controllers\VerificationController())->verifyOtp($request);
    });
    $router->post('/verify/kyc/verify-nid', function (\Core\Request $request) {
        return (new \App\Controllers\VerificationController())->verifyNid($request);
    });
    $router->get('/verify/{uuid}', function (\Core\Request $request, string $uuid) {
        return (new \App\Controllers\VerificationController())->publicCard($request, $uuid);
    });

    // --- Decoupled RESTful API (v1) for Mobile Clients & Android Studio Kotlin Quran App ---
    $router->get('/api/v1/health', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->health($request);
    });
    $router->post('/api/v1/auth/login', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->login($request);
    });
    $router->get('/api/v1/books', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->books($request);
    });
    $router->post('/api/v1/books/order', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->orderBook($request);
    });
    $router->get('/api/v1/courses', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->courses($request);
    });
    $router->post('/api/v1/admissions', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->createAdmission($request);
    });
    $router->get('/api/v1/prayer-times', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->prayerTimes($request);
    });
    $router->get('/api/v1/kyc/status', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->kycStatus($request);
    });
    $router->post('/api/v1/kyc/verify-nid', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->verifyNid($request);
    });

    // --- Telegram Bot Bidirectional Webhook & Action Card Router ---
    $router->post('/api/telegram/webhook', function (\Core\Request $request) {
        return (new \App\Controllers\TelegramWebhookController())->handle($request);
    });
    $router->get('/api/telegram/webhook', function (\Core\Request $request) {
        return \Core\Response::json(['status' => 'ok', 'gateway' => 'Telegram Webhook Gateway Ready']);
    });

    // --- District Directors & Public Organization Routes ---
    $router->get('/directors', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->index($request);
    });

    $router->get('/directors/{slug}', function (\Core\Request $request, string $slug) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->show($request, $slug);
    });

    $router->get('/director/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->login($request);
    });

    $router->post('/director/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->handleLogin($request);
    });

    $router->get('/director/logout', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->logout();
    });

    $router->get('/director/dashboard', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->dashboard($request);
    });

    $router->post('/director/request', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->submitRequest($request);
    });

    $router->post('/director/activity/update', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->updateTeacherActivity($request);
    });

    $router->post('/director/teachers/create', function (\Core\Request $request) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->createTeacher($request);
    });

    $router->post('/director/teachers/delete/{id}', function (\Core\Request $request, string $id) {
        $controller = new \App\Controllers\DirectorController();
        return $controller->deleteTeacher($request, $id);
    });

    // --- Admin CMS & Marketing Integrations Routes ---
    $router->get('/admin', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->index($request);
    });

    $router->get('/admin/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->login($request);
    });

    $router->post('/admin/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->handleLogin($request);
    });

    $router->get('/admin/logout', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->logout();
    });

    $router->get('/admin/dashboard', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->dashboard($request);
    });

    $router->get('/admin/id-card/{type}/{id}', function (\Core\Request $request, string $type, string $id) {
        $controller = new \App\Controllers\AdminController();
        return $controller->generateIdCard($request, $type, $id);
    });

    $router->post('/admin/teachers/approve/{id}', function (\Core\Request $request, string $id) {
        $controller = new \App\Controllers\AdminController();
        return $controller->approveTeacher($request, $id);
    });

    $router->post('/admin/teachers/reject/{id}', function (\Core\Request $request, string $id) {
        $controller = new \App\Controllers\AdminController();
        return $controller->rejectTeacher($request, $id);
    });

    $router->get('/admin/settings', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->settings($request);
    });

    $router->post('/admin/settings', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->handleSettings($request);
    });

    $router->get('/admin/admissions', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->admissions($request);
    });

    $router->get('/admin/admissions/export', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->exportAdmissionsCsv();
    });

    $router->get('/admin/directors', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->directors($request);
    });

    $router->get('/admin/directors/export', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->exportDirectorsCsv();
    });

    $router->get('/admin/directors/impersonate/{id}', function (\Core\Request $request, string $id) {
        $controller = new \App\Controllers\AdminController();
        return $controller->impersonateDirector($request, $id);
    });

    $router->get('/admin/directors/exit-impersonation', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->exitImpersonation($request);
    });

    $router->post('/admin/directors/status', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->updateDirectorStatus($request);
    });

    $router->post('/admin/directors/create', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->createDirector($request);
    });

    $router->post('/admin/developer-message', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->sendDeveloperMessage($request);
    });

    $router->post('/admin/developer-message/delete/{id}', function (\Core\Request $request, string $id) {
        $controller = new \App\Controllers\AdminController();
        return $controller->deleteDeveloperMessage($request, $id);
    });

    $router->post('/admin/directors/delete/{id}', function (\Core\Request $request, string $id) {
        $controller = new \App\Controllers\AdminController();
        return $controller->deleteDirector($request, $id);
    });

    $router->post('/admin/directors/bulk-action', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->bulkDirectorsAction($request);
    });

    $router->post('/admin/directors/sms', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->sendDirectorSms($request);
    });

    // Admin Books Management & Hero Top 3 Books Toggle
    $router->get('/admin/books', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->books($request);
    });

    $router->post('/admin/books/featured', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->updateBookFeatured($request);
    });

    $router->post('/admin/books/update', function (\Core\Request $request) {
        $controller = new \App\Controllers\AdminController();
        return $controller->updateBookPricing($request);
    });

    // --- Operations & Accounts Manager Routes ---
    $router->get('/manager', function (\Core\Request $request) {
        return \Core\Response::redirect('/manager/dashboard');
    });

    $router->get('/manager/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->login($request);
    });

    $router->post('/manager/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->handleLogin($request);
    });

    $router->get('/manager/logout', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->logout();
    });

    $router->get('/manager/dashboard', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->dashboard($request);
    });

    $router->get('/manager/ledger', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->ledger($request);
    });

    $router->post('/manager/ledger', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->saveLedger($request);
    });

    $router->get('/manager/distributions', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->distributions($request);
    });

    $router->post('/manager/distributions', function (\Core\Request $request) {
        $controller = new \App\Controllers\ManagerController();
        return $controller->saveDistribution($request);
    });

    // --- Teacher / Muallim Portal Routes ---
    $router->get('/teacher', function (\Core\Request $request) {
        return \Core\Response::redirect('/teacher/dashboard');
    });

    $router->get('/teacher/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\TeacherController();
        return $controller->login($request);
    });

    $router->post('/teacher/login', function (\Core\Request $request) {
        $controller = new \App\Controllers\TeacherController();
        return $controller->handleLogin($request);
    });

    $router->get('/teacher/logout', function (\Core\Request $request) {
        $controller = new \App\Controllers\TeacherController();
        return $controller->logout();
    });

    $router->get('/teacher/dashboard', function (\Core\Request $request) {
        $controller = new \App\Controllers\TeacherController();
        return $controller->dashboard($request);
    });

    $router->post('/teacher/activity', function (\Core\Request $request) {
        $controller = new \App\Controllers\TeacherController();
        return $controller->submitActivity($request);
    });

    $router->post('/teacher/students/update', function (\Core\Request $request) {
        $controller = new \App\Controllers\TeacherController();
        return $controller->updateStudentCount($request);
    });

    // --- Global Maximum-Level SEO: Dynamic XML Sitemap ---
    $router->get('/sitemap.xml', function (\Core\Request $request) {
        $db = \Core\Database::getInstance();
        $base = \Core\SeoHelper::getBaseUrl();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Static & Core Devotional Tools
        $staticRoutes = [
            ['path' => '/', 'priority' => '1.0', 'freq' => 'daily'],
            ['path' => '/courses', 'priority' => '0.9', 'freq' => 'weekly'],
            ['path' => '/books', 'priority' => '0.9', 'freq' => 'weekly'],
            ['path' => '/blog', 'priority' => '0.9', 'freq' => 'daily'],
            ['path' => '/directors', 'priority' => '0.9', 'freq' => 'weekly'],
            ['path' => '/prayer-times', 'priority' => '0.8', 'freq' => 'daily'],
            ['path' => '/zakat', 'priority' => '0.8', 'freq' => 'monthly'],
            ['path' => '/tasbeeh', 'priority' => '0.7', 'freq' => 'monthly'],
            ['path' => '/scan', 'priority' => '0.7', 'freq' => 'monthly'],
            ['path' => '/quran', 'priority' => '0.8', 'freq' => 'monthly'],
        ];

        foreach ($staticRoutes as $r) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($base . $r['path'], ENT_XML1) . "</loc>\n";
            $xml .= "    <changefreq>" . $r['freq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $r['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        // Dynamic Courses
        $courses = $db->query("SELECT slug, updated_at FROM courses WHERE admission_open = 1")->fetchAll();
        foreach ($courses as $c) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($base . '/courses/' . $c['slug'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($c['updated_at'] ?? 'now')) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // Dynamic Books
        $books = $db->query("SELECT slug, updated_at FROM books")->fetchAll();
        foreach ($books as $b) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($base . '/books/' . $b['slug'], ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($b['updated_at'] ?? 'now')) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            $xml .= "  </url>\n";
        }

        // Dynamic Blog Posts
        $posts = $db->query("SELECT slug, updated_at FROM posts WHERE status = 'published'")->fetchAll();
        foreach ($posts as $p) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($base . '/blog/' . rawurlencode($p['slug']), ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($p['updated_at'] ?? 'now')) . "</lastmod>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.9</priority>\n";
            $xml .= "  </url>\n";
        }

        // Dynamic District Directors
        $directors = $db->query("SELECT slug, id, updated_at FROM directors WHERE status = 'active'")->fetchAll();
        foreach ($directors as $d) {
            $slug = !empty($d['slug']) ? $d['slug'] : (string)$d['id'];
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($base . '/directors/' . $slug, ENT_XML1) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($d['updated_at'] ?? 'now')) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return new \Core\Response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    });

    // --- Decoupled RESTful API (v1) for Mobile & Kotlin Native Android Studio App ---
    $router->get('/api/v1/health', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->health($request);
    });
    $router->post('/api/v1/auth/login', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->login($request);
    });
    $router->get('/api/v1/books', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->books($request);
    });
    $router->post('/api/v1/order', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->orderBook($request);
    });
    $router->get('/api/v1/courses', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->courses($request);
    });
    $router->post('/api/v1/admissions', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->createAdmission($request);
    });
    $router->get('/api/v1/prayer-times', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->prayerTimes($request);
    });
    $router->get('/api/v1/kyc/status', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->kycStatus($request);
    });
    $router->post('/api/v1/kyc/verify-nid', function (\Core\Request $request) {
        return (new \App\Controllers\Api\V1Controller())->verifyNidLevel2($request);
    });

    // --- WhatsApp Server Gateway & Bidirectional Webhook ---
    $router->get('/api/whatsapp/webhook', function (\Core\Request $request) {
        return (new \App\Controllers\WhatsAppWebhookController())->verify($request);
    });
    $router->post('/api/whatsapp/webhook', function (\Core\Request $request) {
        return (new \App\Controllers\WhatsAppWebhookController())->receive($request);
    });
    $router->post('/api/whatsapp/send', function (\Core\Request $request) {
        return (new \App\Controllers\WhatsAppWebhookController())->sendTest($request);
    });

    // --- Global Maximum-Level SEO: Dynamic Robots.txt ---
    $router->get('/robots.txt', function (\Core\Request $request) {
        $base = \Core\SeoHelper::getBaseUrl();
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /manager/\n";
        $robots .= "Disallow: /director/dashboard\n";
        $robots .= "Disallow: /teacher/dashboard\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Sitemap: " . $base . "/sitemap.xml\n";

        return new \Core\Response($robots, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    });
}

// 4. Dispatch Request
$app->run();
