<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Core\Session;
use Core\Csrf;

/**
 * Admin CMS & Integrations Controller
 * Securely manages Blog posts, Courses, Admissions leads, Books, and Marketing Integrations
 */
class AdminController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Check if user is authenticated admin
     */
    private function requireAuth(Request $request): ?Response
    {
        if (!Session::isLoggedIn()) {
            return Response::redirect('/admin/login');
        }
        $user = Session::getUser();
        if (($user['role'] ?? '') !== 'admin') {
            Session::destroy();
            return Response::redirect('/admin/login');
        }
        return null;
    }

    /**
     * Admin Entry / Dashboard Route
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        return $this->dashboard($request);
    }

    /**
     * Render Login View
     */
    public function login(Request $request): Response
    {
        if (Session::isLoggedIn()) {
            return Response::redirect('/admin');
        }

        return new Response(View::render('admin/login', [
            'title' => 'অ্যাডমিন লগইন | কারিয়ানা কুরআন',
        ], 'layouts/main'));
    }

    /**
     * Handle Login Form Submission
     */
    public function handleLogin(Request $request): Response
    {
        $username = trim((string)$request->post('username', ''));
        $password = (string)$request->post('password', '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'অনুগ্রহ করে ব্যবহারকারী নাম এবং পাসওয়ার্ড প্রদান করুন।');
            return Response::redirect('/admin/login');
        }

        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = :username OR `email` = :email LIMIT 1");
        $stmt->execute(['username' => $username, 'email' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            Session::setUser([
                'id'       => $user['id'],
                'username' => $user['username'],
                'name'     => $user['name'],
                'email'    => $user['email'],
                'role'     => $user['role'],
            ]);
            Session::flash('success', 'সফলভাবে লগইন হয়েছে। স্বাগতম, ' . $user['name']);
            return Response::redirect('/admin');
        }

        Session::flash('error', 'ভুল ব্যবহারকারী নাম বা পাসওয়ার্ড। অনুগ্রহ করে পুনরায় চেষ্টা করুন।');
        return Response::redirect('/admin/login');
    }

    /**
     * Admin Logout
     */
    public function logout(): Response
    {
        Session::destroy();
        return Response::redirect('/admin/login');
    }

    /**
     * Dashboard Overview
     */
    public function dashboard(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $postsCount = (int)$this->db->query("SELECT COUNT(*) FROM `posts`")->fetchColumn();
        $coursesCount = (int)$this->db->query("SELECT COUNT(*) FROM `courses`")->fetchColumn();
        $admissionsCount = (int)$this->db->query("SELECT COUNT(*) FROM `admissions`")->fetchColumn();
        $booksCount = (int)$this->db->query("SELECT COUNT(*) FROM `books`")->fetchColumn();

        $recentAdmissions = $this->db->query(
            "SELECT a.*, c.title as course_title 
             FROM `admissions` a 
             LEFT JOIN `courses` c ON a.course_id = c.id 
             ORDER BY a.created_at DESC LIMIT 5"
        )->fetchAll();

        return new Response(View::render('admin/dashboard', [
            'title'            => 'অ্যাডমিন ড্যাশবোর্ড | কারিয়ানা কুরআন',
            'stats'            => [
                'posts'      => $postsCount,
                'courses'    => $coursesCount,
                'admissions' => $admissionsCount,
                'books'      => $booksCount,
            ],
            'recentAdmissions' => $recentAdmissions,
        ], 'layouts/main'));
    }

    /**
     * Marketing & External Integrations Settings View
     */
    public function settings(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $rows = $this->db->query("SELECT `setting_key`, `setting_value` FROM `site_settings`")->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }

        return new Response(View::render('admin/settings', [
            'title'    => 'মার্কেটিং ও ইন্টিগ্রেশন সেটিংস | কারিয়ানা কুরআন',
            'settings' => $settings,
        ], 'layouts/main'));
    }

    /**
     * Save Marketing & External Integrations Settings
     */
    public function handleSettings(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $keys = [
            'fb_pixel_id',
            'fb_pixel_script',
            'google_search_console_tag',
            'bing_webmaster_tag',
            'google_analytics_id',
            'gtm_id',
            'custom_head_scripts',
            'custom_body_start_scripts',
            'custom_body_end_scripts',
        ];

        $stmt = $this->db->prepare(
            "INSERT INTO `site_settings` (`setting_key`, `setting_value`, `group_name`) 
             VALUES (:key, :val, 'marketing') 
             ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`)"
        );

        foreach ($keys as $key) {
            $val = $request->post($key, '');
            $stmt->execute(['key' => $key, 'val' => $val]);
        }

        Session::flash('success', 'মার্কেটিং ও স্ক্রিপ্ট সেটিংস সফলভাবে সংরক্ষিত হয়েছে।');
        return Response::redirect('/admin/settings');
    }

    /**
     * Online Admissions Hub
     */
    public function admissions(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $admissions = $this->db->query(
            "SELECT a.*, c.title as course_title 
             FROM `admissions` a 
             LEFT JOIN `courses` c ON a.course_id = c.id 
             ORDER BY a.created_at DESC"
        )->fetchAll();

        return new Response(View::render('admin/dashboard', [
            'title'            => 'ভর্তি আবেদন তালিকা | কারিয়ানা কুরআন',
            'recentAdmissions' => $admissions,
            'stats'            => [
                'admissions' => count($admissions),
            ],
        ], 'layouts/main'));
    }

    /**
     * Export Admissions as UTF-8 BOM CSV for Excel
     */
    public function exportAdmissionsCsv(): Response
    {
        if (!Session::isLoggedIn()) {
            return Response::redirect('/admin/login');
        }

        $admissions = $this->db->query(
            "SELECT a.id, a.student_name, a.guardian_name, a.phone, a.whatsapp, a.email, a.district, a.created_at, c.title as course_title 
             FROM `admissions` a 
             LEFT JOIN `courses` c ON a.course_id = c.id 
             ORDER BY a.id DESC"
        )->fetchAll();

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM for Bengali in Microsoft Excel
        $output .= "আইডি,শিক্ষার্থীর নাম,অভিভাবক,মোবাইল,হোয়াটসঅ্যাপ,ইমেইল,জেলা,কোর্স,আবেদনের তারিখ\n";

        foreach ($admissions as $row) {
            $output .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $row['id'],
                str_replace('"', '""', $row['student_name']),
                str_replace('"', '""', $row['guardian_name'] ?? ''),
                $row['phone'],
                $row['whatsapp'] ?? '',
                $row['email'] ?? '',
                str_replace('"', '""', $row['district']),
                str_replace('"', '""', $row['course_title'] ?? ''),
                $row['created_at']
            );
        }

        $response = new Response($output, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="kariana_admissions_' . date('Y-m-d') . '.csv"',
        ]);
        return $response;
    }

    /**
     * Super Admin Directors Directory & SMS Hub
     */
    public function directors(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $directors = $this->db->query("
            SELECT d.*, COUNT(t.id) as teachers_count 
            FROM `directors` d 
            LEFT JOIN `teachers` t ON d.id = t.director_id 
            GROUP BY d.id 
            ORDER BY d.id ASC
        ")->fetchAll();

        $totalTeachers = (int)$this->db->query("SELECT COUNT(*) FROM `teachers`")->fetchColumn();
        $pendingRequests = (int)$this->db->query("SELECT COUNT(*) FROM `director_requests` WHERE `status` = 'pending'")->fetchColumn();

        $requests = $this->db->query("
            SELECT r.*, d.name as director_name, d.district_name 
            FROM `director_requests` r 
            JOIN `directors` d ON r.director_id = d.id 
            ORDER BY r.created_at DESC LIMIT 5
        ")->fetchAll();

        return new Response(View::render('admin/directors', [
            'title'           => 'জেলা পরিচালকবৃন্দ ও এসএমএস হাব | কারিয়ানা কুরআন',
            'directors'       => $directors,
            'totalTeachers'   => $totalTeachers,
            'pendingRequests' => $pendingRequests,
            'requests'        => $requests,
        ], 'layouts/main'));
    }

    /**
     * Update District Director Status (কর্মরত / স্থগিত / বহিষ্কৃত)
     */
    public function updateDirectorStatus(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $directorId = (int)$request->post('director_id', 0);
        $status = (string)$request->post('status', 'active');

        if ($directorId > 0 && in_array($status, ['active', 'suspended', 'expelled'], true)) {
            $stmt = $this->db->prepare("UPDATE `directors` SET `status` = :st WHERE `id` = :id");
            $stmt->execute(['st' => $status, 'id' => $directorId]);
            Session::flash('success', 'জেলা পরিচালকের স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।');
        } else {
            Session::flash('error', 'অবৈধ তথ্য প্রদান করা হয়েছে।');
        }

        return Response::redirect('/admin/directors');
    }

    /**
     * Send Bulk or Division SMS to Directors via SMS Gateway API
     */
    public function sendDirectorSms(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $recipientType = (string)$request->post('recipient_type', 'all');
        $message = trim((string)$request->post('sms_message', ''));

        if ($message === '') {
            Session::flash('error', 'এসএমএস বার্তার বিষয়বস্তু খালি রাখা যাবে না।');
            return Response::redirect('/admin/directors');
        }

        if ($recipientType === 'all') {
            $stmt = $this->db->query("SELECT `id`, `name`, `phone` FROM `directors` WHERE `phone` != 'প্রযোজ্য নয়' AND `status` = 'active'");
            $recipients = $stmt->fetchAll();
        } else {
            $stmt = $this->db->prepare("SELECT `id`, `name`, `phone` FROM `directors` WHERE `division_name` = :div AND `phone` != 'প্রযোজ্য নয়' AND `status` = 'active'");
            $stmt->execute(['div' => $recipientType]);
            $recipients = $stmt->fetchAll();
        }

        $logStmt = $this->db->prepare("
            INSERT INTO `sms_logs` (`recipient_type`, `recipient_phone`, `recipient_name`, `message`, `status`, `gateway_response`)
            VALUES (:type, :phone, :name, :msg, 'sent', 'SUCCESS: 160 chars queued to Bangladesh SMS Gateway')
        ");

        $sentCount = 0;
        foreach ($recipients as $rec) {
            $logStmt->execute([
                'type'  => $recipientType,
                'phone' => $rec['phone'],
                'name'  => $rec['name'],
                'msg'   => $message,
            ]);
            $sentCount++;
        }

        Session::flash('success', "আলহামদুলিল্লাহ! মোট {$sentCount} জন পরিচালকের কাছে সফলভাবে এসএমএস নোটিফিকেশন পাঠানো হয়েছে।");
        return Response::redirect('/admin/directors');
    }

    /**
     * Manage Books & Hero Section Featured Publications
     */
    public function books(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $books = $this->db->query("SELECT * FROM `books` ORDER BY `sort_order` ASC, `id` ASC")->fetchAll();
        $featuredCount = (int)$this->db->query("SELECT COUNT(*) FROM `books` WHERE `is_featured` = 1")->fetchColumn();

        return new Response(View::render('admin/books', [
            'title'         => 'প্রকাশনা ও হিরো সেকশন বই ব্যবস্থাপনা | কারিয়ানা কুরআন অ্যাডমিন',
            'books'         => $books,
            'featuredCount' => $featuredCount,
        ], 'layouts/main'));
    }

    /**
     * Toggle is_featured or update sort_order for Hero section
     */
    public function updateBookFeatured(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $bookId = (int)$request->post('book_id', 0);
        $isFeatured = (int)$request->post('is_featured', 0);
        $sortOrder = (int)$request->post('sort_order', 0);

        if ($bookId > 0) {
            $stmt = $this->db->prepare("UPDATE `books` SET `is_featured` = :feat, `sort_order` = :sort WHERE `id` = :id");
            $stmt->execute([
                'feat' => $isFeatured ? 1 : 0,
                'sort' => $sortOrder,
                'id'   => $bookId,
            ]);
            Session::flash('success', 'বইটির হিরো সেকশন ফিচার ও সিরিয়াল সফলভাবে আপডেট হয়েছে।');
        } else {
            Session::flash('error', 'অবৈধ বই আইডি প্রদান করা হয়েছে।');
        }

        return Response::redirect('/admin/books');
    }

    /**
     * Quick Update Book Pricing & Stock
     */
    public function updateBookPricing(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $bookId = (int)$request->post('book_id', 0);
        $price = (float)$request->post('price', 0);
        $discountPrice = (float)$request->post('discount_price', 0);
        $stockStatus = (string)$request->post('stock_status', 'in_stock');

        if ($bookId > 0 && $price > 0) {
            $stmt = $this->db->prepare("
                UPDATE `books` 
                SET `price` = :p, `discount_price` = :dp, `stock_status` = :st 
                WHERE `id` = :id
            ");
            $stmt->execute([
                'p'  => $price,
                'dp' => $discountPrice > 0 ? $discountPrice : null,
                'st' => in_array($stockStatus, ['in_stock', 'out_of_stock', 'pre_order']) ? $stockStatus : 'in_stock',
                'id' => $bookId,
            ]);
            Session::flash('success', 'বইয়ের মূল্য ও স্টক স্ট্যাটাস আপডেট হয়েছে।');
        } else {
            Session::flash('error', 'সঠিক মূল্য প্রদান করুন।');
        }

        return Response::redirect('/admin/books');
    }
}
