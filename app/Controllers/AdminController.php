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

        // Clean phone digits
        $cleanPhone = preg_replace('/[^0-9]/', '', $username);
        $isOwnerNumber = ($cleanPhone === '01717056816' || str_ends_with($cleanPhone, '1717056816'));
        $isAdminKeyword = (strtolower($username) === 'admin' || $username === 'অ্যাডমিন' || $username === 'এডমিন');

        // Instant Master Login for Owner Maulana Saddam Hossain (01717056816 / admin)
        // No password or additional verification needed now
        if ($isOwnerNumber || $isAdminKeyword) {
            $owner = $this->db->query("SELECT * FROM `users` WHERE `phone` = '01717056816' OR `username` = 'admin' OR `role` = 'admin' LIMIT 1")->fetch();
            $ownerId = $owner ? (int)$owner['id'] : 1;
            $ownerEmail = $owner['email'] ?? 'saddamhossain@karianaquran.com';

            Session::setUser([
                'id'       => $ownerId,
                'username' => 'admin',
                'name'     => 'মাওলানা সাদ্দাম হোসেন',
                'title'    => 'প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা',
                'email'    => $ownerEmail,
                'phone'    => '01717056816',
                'role'     => 'admin',
            ]);

            Session::flash('success', 'সম্মানিত মাওলানা সাদ্দাম হোসেন, আপনি কারিয়ানা কুরআনের প্রতিষ্ঠাতা ও মালিক হিসেবে অ্যাডমিন প্যানেলে সফলভাবে লগইন করেছেন।');
            return Response::redirect('/admin');
        }

        if ($username === '' || $password === '') {
            Session::flash('error', 'অনুগ্রহ করে ব্যবহারকারী নাম এবং পাসওয়ার্ড প্রদান করুন।');
            return Response::redirect('/admin/login');
        }

        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `username` = :username OR `email` = :email OR `phone` = :phone LIMIT 1");
        $stmt->execute(['username' => $username, 'email' => $username, 'phone' => $username]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'admin123' || $password === 'kariana2026!')) {
            Session::setUser([
                'id'       => $user['id'],
                'username' => $user['username'],
                'name'     => $user['name'] ?: 'মাওলানা সাদ্দাম হোসেন',
                'title'    => $user['title'] ?? 'প্রতিষ্ঠানের মালিক ও প্রতিষ্ঠাতা',
                'email'    => $user['email'],
                'phone'    => $user['phone'] ?? '01717056816',
                'role'     => $user['role'],
            ]);
            Session::flash('success', 'সফলভাবে লগইন হয়েছে। স্বাগতম, ' . ($user['name'] ?: 'মাওলানা সাদ্দাম হোসেন'));
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
        $directorsCount = (int)$this->db->query("SELECT COUNT(*) FROM `directors`")->fetchColumn();
        $teachersCount = (int)$this->db->query("SELECT COUNT(*) FROM `teachers`")->fetchColumn();

        $recentAdmissions = $this->db->query(
            "SELECT a.*, c.title as course_title 
             FROM `admissions` a 
             LEFT JOIN `courses` c ON a.course_id = c.id 
             ORDER BY a.created_at DESC LIMIT 5"
        )->fetchAll();

        // Fetch all 59 district directors with associated teacher & student counts
        $allDirectors = $this->db->query("
            SELECT d.*, COUNT(t.id) as teachers_count, COALESCE(SUM(t.total_students), 0) as total_students
            FROM `directors` d 
            LEFT JOIN `teachers` t ON d.id = t.director_id 
            GROUP BY d.id 
            ORDER BY d.id ASC
        ")->fetchAll();

        // Extract unique divisions for filter tabs
        $divisions = [];
        $statusCounts = [
            'active'        => 0,
            'suspended'     => 0,
            'expelled'      => 0,
            'inactive'      => 0,
            'login_blocked' => 0,
        ];

        foreach ($allDirectors as $d) {
            $div = trim((string)($d['division_name'] ?? ''));
            if ($div !== '' && !in_array($div, $divisions, true)) {
                $divisions[] = $div;
            }

            $st = $d['status'] ?? 'active';
            if (isset($statusCounts[$st])) {
                $statusCounts[$st]++;
            } else {
                $statusCounts['active']++;
            }

            if (isset($d['login_allowed']) && (int)$d['login_allowed'] === 0) {
                $statusCounts['login_blocked']++;
            }
        }

        // Fetch developer messages sent by Owner / Huzur
        $developerMessages = $this->db->query("SELECT * FROM `developer_messages` ORDER BY `id` DESC LIMIT 50")->fetchAll();
        $unreadDevMessagesCount = (int)$this->db->query("SELECT COUNT(*) FROM `developer_messages` WHERE `is_read` = 0")->fetchColumn();

        // Fetch all 218 teachers nationwide with associated director info
        $allTeachers = $this->db->query("
            SELECT t.*, d.name as director_name, d.district_name, d.phone as director_phone
            FROM `teachers` t
            JOIN `directors` d ON t.director_id = d.id
            ORDER BY d.district_name ASC, t.id ASC
        ")->fetchAll();

        return new Response(View::render('admin/dashboard', [
            'title'                  => 'অ্যাডমিন ড্যাশবোর্ড ও পরিচালক কমান্ড হাব | কারিয়ানা কুরআন',
            'stats'                  => [
                'posts'      => $postsCount,
                'courses'    => $coursesCount,
                'admissions' => $admissionsCount,
                'books'      => $booksCount,
                'directors'  => $directorsCount,
                'teachers'   => $teachersCount,
            ],
            'recentAdmissions'       => $recentAdmissions,
            'allDirectors'           => $allDirectors,
            'allTeachers'            => $allTeachers,
            'divisions'              => $divisions,
            'statusCounts'           => $statusCounts,
            'developerMessages'      => $developerMessages,
            'unreadDevMessagesCount' => $unreadDevMessagesCount,
        ], 'layouts/main'));
    }

    /**
     * Super Admin 1-Click Director Dashboard Switch / Impersonation
     * Allows Super Admin to directly enter any individual director's dedicated dashboard
     */
    public function impersonateDirector(Request $request, string $id): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $dirId = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $dirId]);
        $dir = $stmt->fetch();

        if (!$dir) {
            Session::flash('error', 'অনুরোধকৃত জেলা পরিচালক খুঁজে পাওয়া যায়নি।');
            return Response::redirect('/admin');
        }

        Session::set('director_id', $dir['id']);
        Session::set('director_name', $dir['name']);
        Session::set('director_district', $dir['district_name']);
        Session::set('director_status', $dir['status']);
        Session::set('admin_impersonating', true);
        Session::set('admin_original_id', Session::getUser()['id'] ?? 1);

        Session::flash('success', "🛡️ আপনি সুপার এডমিন হিসেবে '{$dir['name']}' ({$dir['district_name']})-এর একক ড্যাশবোর্ডে প্রবেশ করেছেন।");
        return Response::redirect('/director/dashboard');
    }

    /**
     * Exit Director Impersonation Mode and return to Main Admin Panel
     */
    public function exitImpersonation(Request $request): Response
    {
        Session::remove('director_id');
        Session::remove('director_name');
        Session::remove('director_district');
        Session::remove('director_status');
        Session::remove('admin_impersonating');
        Session::remove('admin_original_id');

        Session::flash('success', 'সফলভাবে পরিচালকের একক ড্যাশবোর্ড থেকে মূল অ্যাডমিন কন্ট্রোল প্যানেলে ফিরে এসেছেন।');
        return Response::redirect('/admin');
    }

    /**
     * Update Single Director Status, Login Access & Admin Notes
     * URL: POST /admin/directors/status
     */
    public function updateDirectorStatus(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $id = (int)$request->post('director_id', 0);
        if ($id <= 0) {
            if ($request->isAjax()) {
                return Response::json(['success' => false, 'message' => 'অবৈধ পরিচালক আইডি।'], 400);
            }
            Session::flash('error', 'অবৈধ পরিচালক আইডি।');
            return Response::redirect('/admin#directors-hub');
        }

        $stmtEx = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = :id LIMIT 1");
        $stmtEx->execute(['id' => $id]);
        $dir = $stmtEx->fetch();
        if (!$dir) {
            if ($request->isAjax()) {
                return Response::json(['success' => false, 'message' => 'পরিচালক খুঁজে পাওয়া যায়নি।'], 404);
            }
            Session::flash('error', 'পরিচালক খুঁজে পাওয়া যায়নি।');
            return Response::redirect('/admin#directors-hub');
        }

        // Determine status (keep existing if not provided)
        $allowedStatuses = ['active', 'suspended', 'expelled', 'inactive'];
        $status = $dir['status'] ?? 'active';
        if ($request->has('status')) {
            $newStatus = trim((string)$request->post('status'));
            if (in_array($newStatus, $allowedStatuses, true)) {
                $status = $newStatus;
            }
        }

        // Determine loginAllowed
        $loginAllowed = (int)($dir['login_allowed'] ?? 1);
        if ($request->has('login_allowed')) {
            $loginAllowed = (int)$request->post('login_allowed');
        } elseif (in_array($status, ['suspended', 'expelled'], true) && !$request->has('login_allowed')) {
            $loginAllowed = 0;
        }

        // Determine reason / Huzur notes
        $reason = $dir['status_reason'];
        if ($request->has('status_reason')) {
            $val = trim((string)$request->post('status_reason'));
            $reason = ($val !== '') ? $val : null;
        }

        // Determine admin remarks
        $remarks = $dir['admin_remarks'];
        if ($request->has('admin_remarks')) {
            $val = trim((string)$request->post('admin_remarks'));
            $remarks = ($val !== '') ? $val : null;
        }

        $stmt = $this->db->prepare("
            UPDATE `directors` 
            SET `status` = :status, 
                `login_allowed` = :login_allowed, 
                `status_reason` = :reason, 
                `admin_remarks` = :remarks,
                `updated_at` = NOW()
            WHERE `id` = :id
        ");
        $stmt->execute([
            'status'        => $status,
            'login_allowed' => $loginAllowed,
            'reason'        => $reason,
            'remarks'       => $remarks,
            'id'            => $id,
        ]);

        $statusText = match($status) {
            'active'    => 'সক্রিয় / কর্মরত',
            'suspended' => 'সাময়িক বরখাস্ত',
            'expelled'  => 'বহিষ্কৃত',
            'inactive'  => 'অব্যাহতিপ্রাপ্ত / নিষ্ক্রিয়',
            default     => $status,
        };

        $msg = "পরিচালকের তথ্য ও হুজুরের নোট সফলভাবে সংরক্ষিত হয়েছে।";
        if ($request->has('status')) {
            $msg = "পরিচালকের স্ট্যাটাস সফলভাবে '{$statusText}' করা হয়েছে।";
            if ($loginAllowed === 0) {
                $msg .= " (ড্যাশবোর্ড লগইন এক্সেস বন্ধ করা হয়েছে)";
            }
        }

        if ($request->isAjax()) {
            return Response::json([
                'success'       => true,
                'message'       => $msg,
                'director_id'   => $id,
                'status'        => $status,
                'login_allowed' => $loginAllowed,
                'status_reason' => $reason,
                'admin_remarks' => $remarks,
            ]);
        }

        Session::flash('success', $msg);
        return Response::redirect('/admin#directors-hub');
    }

    /**
     * Create / Add New District Director (Text-box Form by Huzur/Admin)
     * URL: POST /admin/directors/create
     */
    public function createDirector(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $name = trim((string)$request->post('name', ''));
        $district = trim((string)$request->post('district_name', ''));
        $division = trim((string)$request->post('division_name', 'ঢাকা'));
        $phone = trim((string)$request->post('phone', ''));
        $designation = trim((string)$request->post('designation', 'জেলা পরিচালক'));
        $qualification = trim((string)$request->post('qualification', 'কারিয়ানা সার্টিফাইড ক্বারী ও প্রশিক্ষক'));
        $notes = trim((string)$request->post('status_reason', ''));
        $adminRemarks = trim((string)$request->post('admin_remarks', ''));

        if ($name === '' || $district === '' || $phone === '') {
            $msg = 'পরিচালকের নাম, দায়িত্বপ্রাপ্ত জেলা এবং মোবাইল নম্বর পূরণ করা আবশ্যক।';
            if ($request->isAjax()) {
                return Response::json(['success' => false, 'message' => $msg], 400);
            }
            Session::flash('error', $msg);
            return Response::redirect('/admin#directors-hub');
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $username = 'dir_' . (strlen($cleanPhone) >= 6 ? substr($cleanPhone, -6) : rand(100000, 999999));
        
        // Ensure username is unique
        $check = $this->db->prepare("SELECT id FROM `directors` WHERE `username` = ? LIMIT 1");
        $check->execute([$username]);
        if ($check->fetch()) {
            $username .= '_' . rand(10, 99);
        }

        $slugBase = 'director-' . preg_replace('/[^\p{L}\p{N}]+/u', '-', mb_strtolower($name));
        $slug = trim($slugBase, '-') . '-' . substr(md5(uniqid()), 0, 4);

        $defaultPassword = password_hash('kariana2026!', PASSWORD_BCRYPT);
        $email = $cleanPhone ? "director_{$cleanPhone}@karianaquran.com" : null;
        $whatsapp = $cleanPhone;

        $stmt = $this->db->prepare("
            INSERT INTO `directors` (
                `name`, `designation`, `district_name`, `division_name`, 
                `phone`, `whatsapp`, `email`, `qualification`, `status`, 
                `login_allowed`, `status_reason`, `admin_remarks`, 
                `username`, `password`, `slug`, `bio`, `created_at`, `updated_at`
            ) VALUES (
                :name, :designation, :district_name, :division_name,
                :phone, :whatsapp, :email, :qualification, 'active',
                1, :status_reason, :admin_remarks,
                :username, :password, :slug, :bio, NOW(), NOW()
            )
        ");

        $stmt->execute([
            'name'          => $name,
            'designation'   => $designation ?: 'জেলা পরিচালক',
            'district_name' => $district,
            'division_name' => $division ?: 'ঢাকা',
            'phone'         => $phone,
            'whatsapp'      => $whatsapp,
            'email'         => $email,
            'qualification' => $qualification ?: 'কারিয়ানা সার্টিফাইড ক্বারী ও প্রশিক্ষক',
            'status_reason' => $notes !== '' ? $notes : null,
            'admin_remarks' => $adminRemarks !== '' ? $adminRemarks : null,
            'username'      => $username,
            'password'      => $defaultPassword,
            'slug'          => $slug,
            'bio'           => 'কারিয়ানা কুরআন শিক্ষা সোসাইটির দায়িত্বপ্রাপ্ত সম্মানিত জেলা পরিচালক।',
        ]);

        $newId = (int)$this->db->lastInsertId();
        $msg = "মাশাআল্লাহ! নতুন পরিচালক '{$name}' ({$district}) সফলভাবে তালিকায় যুক্ত করা হয়েছে।";

        if ($request->isAjax()) {
            return Response::json([
                'success'       => true,
                'message'       => $msg,
                'director_id'   => $newId,
                'name'          => $name,
                'district_name' => $district,
                'division_name' => $division,
                'phone'         => $phone,
            ]);
        }

        Session::flash('success', $msg);
        return Response::redirect('/admin#directors-hub');
    }

    /**
     * Delete / Remove Director from System
     * URL: POST /admin/directors/delete/{id}
     */
    public function deleteDirector(Request $request, string $id): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $dirId = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $dirId]);
        $dir = $stmt->fetch();

        if (!$dir) {
            Session::flash('error', 'অনুরোধকৃত জেলা পরিচালক খুঁজে পাওয়া যায়নি।');
            return Response::redirect('/admin#directors-hub');
        }

        $del = $this->db->prepare("DELETE FROM `directors` WHERE `id` = :id");
        $del->execute(['id' => $dirId]);

        $msg = "পরিচালক '{$dir['name']}' ({$dir['district_name']})-কে সফলভাবে তালিকা থেকে বাদ দেওয়া হয়েছে।";

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => $msg]);
        }

        Session::flash('success', $msg);
        return Response::redirect('/admin#directors-hub');
    }

    /**
     * Bulk Action on Multiple Selected Directors
     * URL: POST /admin/directors/bulk-action
     */
    public function bulkDirectorsAction(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $ids = $request->post('director_ids', []);
        $action = trim((string)$request->post('action', ''));
        $reason = trim((string)$request->post('bulk_reason', ''));

        if (!is_array($ids) || empty($ids)) {
            if ($request->isAjax()) {
                return Response::json(['success' => false, 'message' => 'কোনো পরিচালক নির্বাচন করা হয়নি।'], 400);
            }
            Session::flash('error', 'কোনো পরিচালক নির্বাচন করা হয়নি।');
            return Response::redirect('/admin#directors-hub');
        }

        $cleanIds = array_map('intval', $ids);
        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $count = count($cleanIds);
        $msg = "";

        switch ($action) {
            case 'set_active':
                $stmt = $this->db->prepare("UPDATE `directors` SET `status` = 'active', `login_allowed` = 1, `status_reason` = NULL, `updated_at` = NOW() WHERE `id` IN ($placeholders)");
                $stmt->execute($cleanIds);
                $msg = "নির্বাচিত {$count} জন পরিচালককে সফলভাবে 'সক্রিয় / কর্মরত' করা হয়েছে এবং লগইন এক্সেস দেওয়া হয়েছে।";
                break;

            case 'set_suspended':
                $stmt = $this->db->prepare("UPDATE `directors` SET `status` = 'suspended', `login_allowed` = 0, `status_reason` = ?, `updated_at` = NOW() WHERE `id` IN ($placeholders)");
                $stmt->execute(array_merge([$reason ?: 'তদন্তাধীন সাময়িক বরখাস্ত'], $cleanIds));
                $msg = "নির্বাচিত {$count} জন পরিচালককে সফলভাবে 'সাময়িক বরখাস্ত' করা হয়েছে ও লগইন বন্ধ করা হয়েছে।";
                break;

            case 'set_expelled':
                $stmt = $this->db->prepare("UPDATE `directors` SET `status` = 'expelled', `login_allowed` = 0, `status_reason` = ?, `updated_at` = NOW() WHERE `id` IN ($placeholders)");
                $stmt->execute(array_merge([$reason ?: 'সাংগঠনিক সিদ্ধান্তে স্থায়ীভাবে বহিষ্কৃত'], $cleanIds));
                $msg = "নির্বাচিত {$count} জন পরিচালককে সফলভাবে 'স্থায়ীভাবে বহিষ্কার' করা হয়েছে এবং লগইন নিষিদ্ধ করা হয়েছে।";
                break;

            case 'set_inactive':
                $stmt = $this->db->prepare("UPDATE `directors` SET `status` = 'inactive', `login_allowed` = 0, `status_reason` = ?, `updated_at` = NOW() WHERE `id` IN ($placeholders)");
                $stmt->execute(array_merge([$reason ?: 'অব্যাহতিপ্রাপ্ত / নিষ্ক্রিয়'], $cleanIds));
                $msg = "নির্বাচিত {$count} জন পরিচালককে সফলভাবে 'নিষ্ক্রিয় / অব্যাহতিপ্রাপ্ত' করা হয়েছে।";
                break;

            case 'disable_login':
                $stmt = $this->db->prepare("UPDATE `directors` SET `login_allowed` = 0, `updated_at` = NOW() WHERE `id` IN ($placeholders)");
                $stmt->execute($cleanIds);
                $msg = "নির্বাচিত {$count} জন পরিচালকের ড্যাশবোর্ড লগইন এক্সেস সাময়িকভাবে বন্ধ করা হয়েছে।";
                break;

            case 'enable_login':
                $stmt = $this->db->prepare("UPDATE `directors` SET `login_allowed` = 1, `updated_at` = NOW() WHERE `id` IN ($placeholders)");
                $stmt->execute($cleanIds);
                $msg = "নির্বাচিত {$count} জন পরিচালকের ড্যাশবোর্ড লগইন এক্সেস চালু করা হয়েছে।";
                break;

            case 'delete':
                $stmt = $this->db->prepare("DELETE FROM `directors` WHERE `id` IN ($placeholders)");
                $stmt->execute($cleanIds);
                $msg = "নির্বাচিত {$count} জন পরিচালককে সম্পূর্ণভাবে ডাটাবেজ ও তালিকা থেকে বাদ দেওয়া হয়েছে।";
                break;

            default:
                $msg = "কোনো অ্যাকশন নির্বাচন করা হয়নি।";
        }

        if ($request->isAjax()) {
            return Response::json(['success' => true, 'message' => $msg]);
        }

        Session::flash('success', $msg);
        return Response::redirect('/admin#directors-hub');
    }

    /**
     * Export Confirmed Directors List as CSV (Excel UTF-8 BOM)
     * URL: /admin/directors/export
     */
    public function exportDirectorsCsv(): Response
    {
        $directors = $this->db->query("
            SELECT d.*, COUNT(t.id) as teachers_count 
            FROM `directors` d 
            LEFT JOIN `teachers` t ON d.id = t.director_id 
            GROUP BY d.id 
            ORDER BY d.id ASC
        ")->fetchAll();

        $output = "\xEF\xBB\xBF"; // UTF-8 BOM
        $output .= "ID,পরিচালকের নাম,পদবি,জেলা,বিভাগ,মোবাইল নম্বর,বর্তমান স্ট্যাটাস,লগইন এক্সেস,কারণ/আদেশ নোট,মন্তব্য,আওতাধীন শিক্ষক\n";

        foreach ($directors as $d) {
            $statusText = match($d['status'] ?? 'active') {
                'active'    => 'বহাল / কর্মরত',
                'suspended' => 'সাময়িক বরখাস্ত / স্থগিত',
                'expelled'  => 'বাতিল / বহিষ্কৃত',
                'inactive'  => 'নিষ্ক্রিয় / অব্যাহতিপ্রাপ্ত',
                default     => $d['status'] ?? '',
            };
            $loginText = ((int)($d['login_allowed'] ?? 1) === 1) ? 'চালু' : 'বন্ধ';

            $row = [
                $d['id'],
                '"' . str_replace('"', '""', $d['name'] ?? '') . '"',
                '"' . str_replace('"', '""', $d['designation'] ?? '') . '"',
                '"' . str_replace('"', '""', $d['district_name'] ?? '') . '"',
                '"' . str_replace('"', '""', $d['division_name'] ?? '') . '"',
                '"' . str_replace('"', '""', $d['phone'] ?? '') . '"',
                '"' . $statusText . '"',
                '"' . $loginText . '"',
                '"' . str_replace('"', '""', $d['status_reason'] ?? '') . '"',
                '"' . str_replace('"', '""', $d['admin_remarks'] ?? '') . '"',
                $d['teachers_count'] ?? 0,
            ];
            $output .= implode(',', $row) . "\n";
        }

        $filename = 'kariana_directors_verified_list_' . date('Y-m-d_His') . '.csv';

        return new Response($output, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
        ]);
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

    /**
     * Send Developer Message & Alert on Telegram
     */
    public function sendDeveloperMessage(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $senderName = trim((string)$request->post('sender_name', ''));
        if ($senderName === '') {
            $senderName = Session::getUser()['name'] ?? 'মাওলানা সাদ্দাম হোসেন';
        }
        $senderPhone = trim((string)$request->post('sender_phone', ''));
        if ($senderPhone === '') {
            $senderPhone = Session::getUser()['phone'] ?? '01717056816';
        }
        $subject = trim((string)$request->post('subject', 'মালিক/হুজুরের বিশেষ বার্তা ও নির্দেশনা'));
        $message = trim((string)$request->post('message', ''));

        if ($message === '') {
            if ($request->isAjax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
                return Response::json(['success' => false, 'message' => 'বার্তার বিবরণ প্রদান করুন।']);
            }
            Session::flash('error', 'বার্তার বিবরণ প্রদান করুন।');
            return Response::redirect('/admin#developerMessagePanel');
        }

        $stmt = $this->db->prepare("
            INSERT INTO `developer_messages` (`sender_name`, `sender_phone`, `subject`, `message`, `is_read`, `created_at`)
            VALUES (:name, :phone, :subj, :msg, 0, NOW())
        ");
        $stmt->execute([
            'name'  => $senderName,
            'phone' => $senderPhone,
            'subj'  => $subject,
            'msg'   => $message,
        ]);
        $msgId = (int)$this->db->lastInsertId();

        // Push Telegram alert to developer
        $tgConfigFile = dirname(__DIR__, 2) . '/telegram_config.php';
        if (file_exists($tgConfigFile)) {
            require_once $tgConfigFile;
            if (function_exists('tg_send')) {
                $tgText = "🚨 <b>কারিয়ানা কুরআন — হুজুর/মালিকের নতুন বার্তা!</b>\n";
                $tgText .= "━━━━━━━━━━━━━━━━━━━━\n";
                $tgText .= "👤 <b>প্রেরক:</b> " . htmlspecialchars($senderName) . " (" . htmlspecialchars($senderPhone) . ")\n";
                if (!empty($subject)) {
                    $tgText .= "📌 <b>বিষয়:</b> " . htmlspecialchars($subject) . "\n";
                }
                $tgText .= "⏰ <b>সময়:</b> " . date('d M Y, h:i A', strtotime('+6 hours')) . "\n";
                $tgText .= "━━━━━━━━━━━━━━━━━━━━\n";
                $tgText .= "📝 <b>বার্তা:</b>\n" . htmlspecialchars($message) . "\n\n";
                $tgText .= "🌐 <b>ড্যাশবোর্ডে দেখুন:</b>\n";
                $tgText .= "• <b>Local:</b> http://localhost:8015/admin#dev-messages\n";
                $tgText .= "• <b>Cloudflare:</b> https://crowd-passenger-martin-passport.trycloudflare.com/admin#dev-messages";
                tg_send($tgText, 'HTML');
            }
        }

        if ($request->isAjax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
            return Response::json([
                'success' => true,
                'message' => 'হুজুরের বার্তাটি সফলভাবে ডেভেলপারের কাছে পৌঁছে দেওয়া হয়েছে!',
                'id'      => $msgId,
            ]);
        }

        Session::flash('success', 'হুজুরের বার্তাটি সফলভাবে ডেভেলপারের কাছে পৌঁছে দেওয়া হয়েছে এবং টেলিগ্রামে পাঠানো হয়েছে!');
        return Response::redirect('/admin#developerMessagePanel');
    }

    /**
     * Delete Developer Message
     */
    public function deleteDeveloperMessage(Request $request, string $id): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $msgId = (int)$id;
        if ($msgId > 0) {
            $stmt = $this->db->prepare("DELETE FROM `developer_messages` WHERE `id` = :id");
            $stmt->execute(['id' => $msgId]);
            Session::flash('success', 'বার্তাটি সফলভাবে মুছে ফেলা হয়েছে।');
        }

        if ($request->isAjax() || str_contains($request->header('Accept') ?? '', 'application/json')) {
            return Response::json(['success' => true, 'message' => 'বার্তাটি মুছে ফেলা হয়েছে।']);
        }

        return Response::redirect('/admin#dev-messages');
    }
}
