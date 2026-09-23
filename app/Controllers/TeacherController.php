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
 * Teacher / Muallim Portal Controller
 * Manages Teacher authentication, assigned District Director communication,
 * Book requisition to District Director, Sabak Class booking, and issue reporting.
 */
class TeacherController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Guard: Require Teacher Authentication
     */
    private function requireAuth(Request $request): ?Response
    {
        $teacher = Session::get('teacher');
        if (empty($teacher) || empty($teacher['id'])) {
            return Response::redirect('/teacher/login');
        }
        return null;
    }

    /**
     * Teacher Login Form
     */
    public function login(Request $request): Response
    {
        $teacher = Session::get('teacher');
        if (!empty($teacher) && !empty($teacher['id'])) {
            return Response::redirect('/teacher/dashboard');
        }

        return new Response(View::render('teacher/login', [
            'title' => 'সম্মানিত মুয়াল্লিম / শিক্ষক পোর্টাল লগইন | কারিয়ানা কুরআন',
        ], 'layouts/main'));
    }

    /**
     * Handle Teacher Login
     */
    public function handleLogin(Request $request): Response
    {
        $username = trim((string)$request->post('username', ''));
        $password = (string)$request->post('password', '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'অনুগ্রহ করে ইউজারনেম এবং পাসওয়ার্ড প্রদান করুন।');
            return Response::redirect('/teacher/login');
        }

        $stmt = $this->db->prepare("SELECT * FROM `teachers` WHERE (`username` = :u OR `phone` = :p) AND `status` != 'inactive' LIMIT 1");
        $stmt->execute(['u' => $username, 'p' => $username]);
        $teacher = $stmt->fetch();

        if ($teacher && !empty($teacher['password']) && password_verify($password, $teacher['password'])) {
            // Fetch assigned director details
            $dirStmt = $this->db->prepare("SELECT `id`, `name`, `district_name`, `phone`, `whatsapp` FROM `directors` WHERE `id` = :d LIMIT 1");
            $dirStmt->execute(['d' => $teacher['director_id']]);
            $director = $dirStmt->fetch();

            Session::set('teacher', [
                'id'            => $teacher['id'],
                'name'          => $teacher['name'],
                'username'      => $teacher['username'],
                'phone'         => $teacher['phone'],
                'area_name'     => $teacher['area_name'],
                'director_id'   => $teacher['director_id'],
                'director_name' => $director['name'] ?? 'জেলা পরিচালক',
                'district_name' => $director['district_name'] ?? '',
                'director_phone'=> $director['phone'] ?? '',
            ]);

            Session::flash('success', 'স্বাগতম, ' . $teacher['name'] . '! শিক্ষক পোর্টালে সফলভাবে প্রবেশ করেছেন।');
            return Response::redirect('/teacher/dashboard');
        }

        Session::flash('error', 'ভুল ব্যবহারকারী নাম বা পাসওয়ার্ড। অনুগ্রহ করে পুনরায় চেষ্টা করুন।');
        return Response::redirect('/teacher/login');
    }

    /**
     * Teacher Logout
     */
    public function logout(): Response
    {
        Session::remove('teacher');
        Session::flash('info', 'আপনি সফলভাবে শিক্ষক পোর্টাল থেকে প্রস্থান করেছেন।');
        return Response::redirect('/teacher/login');
    }

    /**
     * Teacher Dashboard
     */
    public function dashboard(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $sessTeacher = Session::get('teacher');
        $teacherId = (int)$sessTeacher['id'];

        // Fresh teacher profile with Director details
        $stmt = $this->db->prepare("
            SELECT t.*, d.name as director_name, d.district_name, d.phone as director_phone, d.whatsapp as director_whatsapp, d.designation as director_designation 
            FROM `teachers` t 
            JOIN `directors` d ON t.director_id = d.id 
            WHERE t.id = :id LIMIT 1
        ");
        $stmt->execute(['id' => $teacherId]);
        $teacher = $stmt->fetch();

        if (!$teacher) {
            Session::destroy();
            return Response::redirect('/teacher/login');
        }

        // Available books to order
        $books = $this->db->query("SELECT `id`, `title`, `price`, `discount_price` FROM `books` ORDER BY `sort_order` ASC")->fetchAll();

        // Fetch teacher's activities / requests
        $actStmt = $this->db->prepare("
            SELECT * FROM `teacher_activities` 
            WHERE `teacher_id` = :tid 
            ORDER BY `id` DESC LIMIT 20
        ");
        $actStmt->execute(['tid' => $teacherId]);
        $activities = $actStmt->fetchAll();

        // WhatsApp Gateway Integration status
        $botPhoneStmt = $this->db->query("SELECT setting_value FROM `site_settings` WHERE `setting_key` = 'whatsapp_bot_phone' LIMIT 1");
        $whatsappBotPhone = $botPhoneStmt ? ($botPhoneStmt->fetchColumn() ?: '01717056816') : '01717056816';
        $whatsappLinked = (bool)$this->db->query("SELECT COUNT(*) FROM `bot_user_links` WHERE `channel` = 'whatsapp' AND `user_type` = 'teacher' AND `user_id` = " . (int)$teacherId . " AND `is_active` = 1")->fetchColumn();

        return new Response(View::render('teacher/dashboard', [
            'title'            => 'শিক্ষক ড্যাশবোর্ড ও কার্যক্রম | কারিয়ানা কুরআন',
            'teacher'          => $teacher,
            'books'            => $books,
            'activities'       => $activities,
            'whatsappBotPhone' => $whatsappBotPhone,
            'whatsappLinked'   => $whatsappLinked,
        ], 'layouts/main'));
    }

    /**
     * Submit Activity (Book order to director, Sabak class request, or Problem report)
     */
    public function submitActivity(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $sessTeacher = Session::get('teacher');
        $teacherId = (int)$sessTeacher['id'];
        $directorId = (int)$sessTeacher['director_id'];
        $activityType = (string)$request->post('activity_type', 'general');
        $title = trim((string)$request->post('title', ''));
        $details = trim((string)$request->post('details', ''));
        $quantity = (int)$request->post('quantity', 0);
        $preferredDate = trim((string)$request->post('preferred_date', ''));

        if ($title === '' || $details === '') {
            Session::flash('error', 'বিষয়ের শিরোনাম এবং বিস্তারিত বিবরণ সঠিকভাবে পূরণ করুন।');
            return Response::redirect('/teacher/dashboard');
        }

        $stmt = $this->db->prepare("
            INSERT INTO `teacher_activities` 
            (`teacher_id`, `director_id`, `activity_type`, `title`, `details`, `quantity`, `preferred_date`, `status`)
            VALUES (:tid, :did, :type, :title, :details, :qty, :pdate, 'pending')
        ");
        $stmt->execute([
            'tid'   => $teacherId,
            'did'   => $directorId,
            'type'  => in_array($activityType, ['book_order', 'sabak_class', 'problem_report', 'general'], true) ? $activityType : 'general',
            'title' => $title,
            'details'=> $details,
            'qty'   => $quantity,
            'pdate' => $preferredDate ?: null,
        ]);

        // Notify Director via WhatsApp / Telegram
        try {
            if ($directorId > 0) {
                $typeBn = match($activityType) {
                    'book_order'  => 'বইয়ের চাহিদা 📦',
                    'sabak_class' => 'সবক ক্লাস উদ্বোধন 🎓',
                    default       => 'সাধারণ আবেদন 📝'
                };
                $msg = "📢 *শিক্ষকের নতুন আবেদন ({$typeBn})*\nশিক্ষক: {$sessTeacher['name']}\nবিষয়: {$title}\nবিবরণ: {$details}";
                if ($quantity > 0) $msg .= "\nসংখ্যা: {$quantity} কপি";
                (new \App\Services\UnifiedMessagingService())->sendNotificationToUser('director', $directorId, $msg);
            }
        } catch (\Throwable $e) {}

        Session::flash('success', 'আলহামদুলিল্লাহ! আপনার আবেদনটি জেলা পরিচালকের নিকট সফলভাবে পাঠানো হয়েছে।');
        return Response::redirect('/teacher/dashboard');
    }

    /**
     * Teacher updates their student count
     * URL: POST /teacher/students/update
     */
    public function updateStudentCount(Request $request): Response
    {
        if ($redirect = $this->requireAuth($request)) {
            return $redirect;
        }

        $sessTeacher = Session::get('teacher');
        $teacherId = (int)$sessTeacher['id'];
        $directorId = (int)$sessTeacher['director_id'];
        $count = (int)$request->post('total_students', 0);

        if ($count < 0) {
            $count = 0;
        }

        $stmt = $this->db->prepare("UPDATE `teachers` SET `total_students` = :cnt, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute(['cnt' => $count, 'id' => $teacherId]);

        // Notify Director via WhatsApp / Telegram
        try {
            if ($directorId > 0) {
                $msg = "📢 *শিক্ষার্থীর সংখ্যা আপডেট*\nআপনার আওতাধীন শিক্ষক *{$sessTeacher['name']}* তার মাদরাসা/মক্তবের বর্তমান শিক্ষার্থী সংখ্যা হালনাগাদ করে *{$count} জন* করেছেন।";
                (new \App\Services\UnifiedMessagingService())->sendNotificationToUser('director', $directorId, $msg);
            }
        } catch (\Throwable $e) {}

        Session::flash('success', 'শিক্ষার্থীর সংখ্যা সফলভাবে হালনাগাদ করা হয়েছে।');
        return Response::redirect('/teacher/dashboard');
    }
}
