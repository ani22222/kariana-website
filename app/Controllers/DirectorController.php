<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Core\Session;

/**
 * District Directors & Teachers Controller
 * Public Directory & Dedicated Non-Technical Director Portal
 */
class DirectorController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Public Directors Directory
     * URL: /directors
     */
    public function index(Request $request): Response
    {
        $sql = "
            SELECT d.*, COUNT(t.id) as teachers_count, COALESCE(SUM(t.total_students), 0) as total_students
            FROM `directors` d
            LEFT JOIN `teachers` t ON d.id = t.director_id
            GROUP BY d.id
            ORDER BY d.id ASC
        ";
        $directors = $this->db->query($sql)->fetchAll();

        // Calculate summary counters
        $activeDistricts = $this->db->query("SELECT COUNT(DISTINCT `district_name`) FROM `directors` WHERE `status` = 'active'")->fetchColumn();
        $totalTeachers = $this->db->query("SELECT COUNT(*) FROM `teachers`")->fetchColumn();
        $totalStudents = $this->db->query("SELECT SUM(`total_students`) FROM `teachers`")->fetchColumn();

        return new Response(View::render('directors/index', [
            'title'                => 'জেলা পরিচালকবৃন্দ ও সাংগঠনিক তালিকা | কারিয়ানা কুরআন শিক্ষা সোসাইটি',
            'directors'            => $directors,
            'activeDistrictsCount' => (int)$activeDistricts,
            'totalTeachersCount'   => (int)$totalTeachers,
            'totalStudentsCount'   => (int)$totalStudents,
        ], 'layouts/main'));
    }

    /**
     * Public Detailed Director Profile & Teacher Directory
     * URL: /directors/{slug}
     */
    public function show(Request $request, string $slug): Response
    {
        $stmt = $this->db->prepare("
            SELECT d.*, COUNT(t.id) as teachers_count 
            FROM `directors` d 
            LEFT JOIN `teachers` t ON d.id = t.director_id
            WHERE d.slug = :slug OR d.id = :id
            GROUP BY d.id
            LIMIT 1
        ");
        $stmt->execute(['slug' => $slug, 'id' => is_numeric($slug) ? (int)$slug : 0]);
        $director = $stmt->fetch();

        if (!$director) {
            return new Response(View::render('errors/404', [
                'title'   => 'পরিচালক পাওয়া যায়নি | কারিয়ানা কুরআন',
                'message' => 'অনুরোধকৃত জেলা পরিচালকের তথ্য পাওয়া যায়নি।',
            ], 'layouts/main'), 404);
        }

        // Fetch all teachers under this director
        $stmtT = $this->db->prepare("SELECT * FROM `teachers` WHERE `director_id` = :did ORDER BY `id` ASC");
        $stmtT->execute(['did' => $director['id']]);
        $teachers = $stmtT->fetchAll();

        return new Response(View::render('directors/show', [
            'title'    => $director['name'] . ' — ' . $director['designation'] . ' | কারিয়ানা কুরআন',
            'director' => $director,
            'teachers' => $teachers,
        ], 'layouts/main'));
    }

    /**
     * Director Non-Technical Login View
     * URL: /director/login
     */
    public function login(Request $request): Response
    {
        if (Session::has('director_id')) {
            return Response::redirect('/director/dashboard');
        }

        return new Response(View::render('directors/login', [
            'title' => 'জেলা পরিচালক পোর্টাল লগইন | কারিয়ানা কুরআন',
        ], 'layouts/main'));
    }

    /**
     * Handle Director Login
     */
    public function handleLogin(Request $request): Response
    {
        $username = trim((string)$request->post('username', ''));
        $password = (string)$request->post('password', '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'অনুগ্রহ করে ইউজারনেম/মোবাইল এবং পাসওয়ার্ড প্রদান করুন।');
            return Response::redirect('/director/login');
        }

        $stmt = $this->db->prepare("SELECT * FROM `directors` WHERE `username` = :u OR `phone` = :p OR `email` = :e LIMIT 1");
        $stmt->execute(['u' => $username, 'p' => $username, 'e' => $username]);
        $dir = $stmt->fetch();

        if ($dir && password_verify($password, $dir['password'])) {
            // Check if Super Admin blocked login access
            if (isset($dir['login_allowed']) && (int)$dir['login_allowed'] === 0) {
                $reasonText = !empty($dir['status_reason']) ? ' (কারণ: ' . $dir['status_reason'] . ')' : '';
                Session::flash('error', 'আপনার ড্যাশবোর্ড লগইন এক্সেস কেন্দ্রীয় প্রশাসনিক সিদ্ধান্তে স্থগিত রাখা হয়েছে' . $reasonText . '। কেন্দ্রীয় অ্যাডমিনের সাথে যোগাযোগ করুন।');
                return Response::redirect('/director/login');
            }

            // Check if status is expelled
            if ($dir['status'] === 'expelled') {
                $reasonText = !empty($dir['status_reason']) ? ' (কারণ: ' . $dir['status_reason'] . ')' : '';
                Session::flash('error', 'আপনার দায়িত্ব সাংগঠনিক সিদ্ধান্তে স্থায়ীভাবে বহিষ্কৃত করা হয়েছে' . $reasonText . '। কেন্দ্রীয় কার্যালয়ে যোগাযোগ করুন।');
                return Response::redirect('/director/login');
            }

            // Check if status is suspended
            if ($dir['status'] === 'suspended') {
                $reasonText = !empty($dir['status_reason']) ? ' (কারণ: ' . $dir['status_reason'] . ')' : '';
                Session::flash('error', 'আপনার দায়িত্ব বর্তমানে সাময়িকভাবে বরখাস্ত/স্থগিত রয়েছে' . $reasonText . '। তদন্ত নিষ্পত্তি না হওয়া পর্যন্ত ড্যাশবোর্ড এক্সেস বন্ধ থাকবে।');
                return Response::redirect('/director/login');
            }

            // Check if status is inactive
            if ($dir['status'] === 'inactive') {
                $reasonText = !empty($dir['status_reason']) ? ' (কারণ: ' . $dir['status_reason'] . ')' : '';
                Session::flash('error', 'আপনার পরিচালক পদ বর্তমানে নিষ্ক্রিয়/অব্যাহতিপ্রাপ্ত রয়েছে' . $reasonText . '।');
                return Response::redirect('/director/login');
            }

            Session::set('director_id', $dir['id']);
            Session::set('director_name', $dir['name']);
            Session::set('director_district', $dir['district_name']);
            Session::set('director_status', $dir['status']);

            Session::flash('success', 'স্বাগতম, ' . $dir['name'] . '। আপনি জেলা পরিচালক পোর্টালে সফলভাবে প্রবেশ করেছেন।');
            return Response::redirect('/director/dashboard');
        }

        Session::flash('error', 'ভুল ব্যবহারকারী তথ্য বা পাসওয়ার্ড। পুনরায় চেষ্টা করুন।');
        return Response::redirect('/director/login');
    }

    /**
     * Dedicated Director Dashboard
     * Non-technical, clean, focused purely on their district's teachers and orders
     * URL: /director/dashboard
     */
    public function dashboard(Request $request): Response
    {
        $directorId = Session::get('director_id');
        if (!$directorId) {
            return Response::redirect('/director/login');
        }

        $stmt = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $directorId]);
        $director = $stmt->fetch();

        if (!$director) {
            Session::destroy();
            return Response::redirect('/director/login');
        }

        // Get teachers under this director
        $stmtT = $this->db->prepare("SELECT * FROM `teachers` WHERE `director_id` = :did ORDER BY `id` ASC");
        $stmtT->execute(['did' => $directorId]);
        $teachers = $stmtT->fetchAll();

        // Get requests submitted by this director
        $stmtR = $this->db->prepare("SELECT * FROM `director_requests` WHERE `director_id` = :did ORDER BY `created_at` DESC LIMIT 10");
        $stmtR->execute(['did' => $directorId]);
        $requests = $stmtR->fetchAll();

        // Get activities and requests submitted by teachers under this director
        $stmtTA = $this->db->prepare("
            SELECT ta.*, t.name as teacher_name, t.phone as teacher_phone, t.area_name
            FROM `teacher_activities` ta
            JOIN `teachers` t ON ta.teacher_id = t.id
            WHERE ta.director_id = :did
            ORDER BY ta.created_at DESC
        ");
        $stmtTA->execute(['did' => $directorId]);
        $teacherActivities = $stmtTA->fetchAll();

        return new Response(View::render('directors/dashboard', [
            'title'             => 'জেলা পরিচালক ড্যাশবোর্ড | ' . $director['district_name'],
            'director'          => $director,
            'teachers'          => $teachers,
            'requests'          => $requests,
            'teacherActivities' => $teacherActivities,
        ], 'layouts/main'));
    }

    /**
     * Update Teacher Activity Status & Director Notes (Sabak approval, schedule, etc.)
     */
    public function updateTeacherActivity(Request $request): Response
    {
        $directorId = Session::get('director_id');
        if (!$directorId) {
            return Response::redirect('/director/login');
        }

        $activityId = (int)$request->post('activity_id', 0);
        $status = (string)$request->post('status', 'approved');
        $notes = trim((string)$request->post('director_notes', ''));

        $allowedStatuses = ['pending', 'approved', 'scheduled', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'approved';
        }

        $stmt = $this->db->prepare("
            UPDATE `teacher_activities`
            SET `status` = :st, `director_notes` = :notes
            WHERE `id` = :id AND `director_id` = :did
        ");
        $stmt->execute([
            'st'    => $status,
            'notes' => $notes,
            'id'    => $activityId,
            'did'   => $directorId,
        ]);

        Session::flash('success', 'শিক্ষকের আবেদনটির স্ট্যাটাস ও নোট সফলভাবে আপডেট করা হয়েছে।');
        return Response::redirect('/director/dashboard');
    }

    /**
     * Submit Request (ID Card, Training, Books)
     */
    public function submitRequest(Request $request): Response
    {
        $directorId = Session::get('director_id');
        if (!$directorId) {
            return Response::redirect('/director/login');
        }

        $type = (string)$request->post('request_type', 'general');
        $details = trim((string)$request->post('details', ''));
        $qty = (int)$request->post('quantity', 0);

        if ($details === '') {
            Session::flash('error', 'অনুগ্রহ করে আবেদনের বিস্তারিত বিবরণ লিখুন।');
            return Response::redirect('/director/dashboard');
        }

        $stmt = $this->db->prepare("
            INSERT INTO `director_requests` (`director_id`, `request_type`, `details`, `quantity`, `status`)
            VALUES (:did, :type, :details, :qty, 'pending')
        ");
        $stmt->execute([
            'did'     => $directorId,
            'type'    => $type,
            'details' => $details,
            'qty'     => $qty,
        ]);

        Session::flash('success', 'আপনার আবেদনটি কেন্দ্রীয় কার্যালয়ে সফলভাবে পাঠানো হয়েছে। পর্যালোচনার পর ব্যবস্থা গ্রহণ করা হবে।');
        return Response::redirect('/director/dashboard');
    }

    /**
     * Director Logout
     */
    public function logout(): Response
    {
        Session::remove('director_id');
        Session::remove('director_name');
        Session::remove('director_district');
        Session::remove('director_status');
        Session::flash('success', 'সফলভাবে পরিচালক পোর্টাল থেকে প্রস্থান করেছেন।');
        return Response::redirect('/director/login');
    }
}
