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
 * Student & General User Profile Controller
 * Provides student portal, enrolled courses tracker, Quran study bridge, and profile settings.
 */
class StudentController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Guard: Ensure user is logged in
     */
    private function requireAuth(): ?Response
    {
        if (!Session::isLoggedIn()) {
            Session::flash('error', 'প্রোফাইলে প্রবেশ করতে অনুগ্রহ করে প্রথমে লগইন করুন।');
            return Response::redirect('/login?redirect=' . urlencode('/profile'));
        }
        return null;
    }

    /**
     * Student Portal & Dashboard
     * URL: GET /profile
     */
    public function dashboard(Request $request): Response
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        // Fetch fresh user profile from DB
        $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user) {
            Session::logout();
            return Response::redirect('/login');
        }

        $userPhone = trim((string)($user['phone'] ?? ''));
        $cleanPhone = preg_replace('/[^0-9]/', '', $userPhone);
        $clean10 = strlen($cleanPhone) >= 10 ? substr($cleanPhone, -10) : $cleanPhone;

        // Fetch enrolled or applied courses from `admissions` table
        $enrolledCourses = [];
        if (!empty($clean10)) {
            $stmtAdm = $this->db->prepare("
                SELECT a.*, c.title as course_title, c.course_code, c.duration, c.fee, c.class_schedule, c.cover_image, c.slug as course_slug
                FROM `admissions` a
                JOIN `courses` c ON a.course_id = c.id
                WHERE a.phone LIKE :phone
                ORDER BY a.created_at DESC
            ");
            $stmtAdm->execute(['phone' => '%' . $clean10]);
            $enrolledCourses = $stmtAdm->fetchAll();
        }

        // Fetch user's district or default to Dhaka for prayer times
        $userDistrict = $user['district'] ?: 'ঢাকা';
        $distStmt = $this->db->prepare("SELECT * FROM `prayer_districts` WHERE `name_bn` = :dist1 OR `name_en` = :dist2 LIMIT 1");
        $distStmt->execute(['dist1' => $userDistrict, 'dist2' => $userDistrict]);
        $districtData = $distStmt->fetch();
        if (!$districtData) {
            $districtData = $this->db->query("SELECT * FROM `prayer_districts` WHERE `name_bn` = 'ঢাকা' LIMIT 1")->fetch();
        }

        // Fetch all 64 districts for dropdown
        $allDistricts = $this->db->query("SELECT `name_bn` FROM `prayer_districts` ORDER BY `name_bn` ASC")->fetchAll(\PDO::FETCH_COLUMN);

        // Available courses for quick enrollment
        $availableCourses = $this->db->query("SELECT id, title, slug, duration, fee, cover_image FROM courses WHERE admission_open = 1 ORDER BY sort_order ASC LIMIT 4")->fetchAll();

        return new Response(View::render('student/profile', [
            'title'            => 'আমার প্রোফাইল ও শিক্ষার্থী পোর্টাল | কারিয়ানা কুরআন',
            'user'             => $user,
            'enrolledCourses'  => $enrolledCourses,
            'districtData'     => $districtData,
            'allDistricts'     => $allDistricts,
            'availableCourses' => $availableCourses,
            'success'          => Session::getFlash('success'),
            'error'            => Session::getFlash('error'),
        ], 'layouts/main'));
    }

    /**
     * Update Student Profile
     * URL: POST /profile/update
     */
    public function updateProfile(Request $request): Response
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $name = trim((string)$request->post('name', ''));
        $phone = trim((string)$request->post('phone', ''));
        $district = trim((string)$request->post('district', ''));
        $address = trim((string)$request->post('address', ''));
        $gender = trim((string)$request->post('gender', 'male'));

        if ($name === '') {
            Session::flash('error', 'আপনার নাম প্রদান করা আবশ্যক।');
            return Response::redirect('/profile');
        }

        if ($gender !== 'male' && $gender !== 'female') {
            $gender = 'male';
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE `users` 
                SET `name` = :name, `phone` = :phone, `district` = :district, `address` = :address, `gender` = :gender, `updated_at` = NOW()
                WHERE `id` = :id
            ");
            $stmt->execute([
                'name'     => $name,
                'phone'    => $phone,
                'district' => $district,
                'address'  => $address,
                'gender'   => $gender,
                'id'       => $userId,
            ]);

            // Update session
            $sessionUser['name'] = $name;
            $sessionUser['phone'] = $phone;
            Session::setUser($sessionUser);

            Session::flash('success', 'আপনার প্রোফাইল তথ্য সফলভাবে হালনাগাদ করা হয়েছে।');
        } catch (\Throwable $e) {
            Session::flash('error', 'তথ্য সংরক্ষণে সমস্যা হয়েছে: ' . $e->getMessage());
        }

        return Response::redirect('/profile');
    }

    /**
     * Update Student Password
     * URL: POST /profile/password
     */
    public function updatePassword(Request $request): Response
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        $sessionUser = Session::getUser();
        $userId = (int)($sessionUser['id'] ?? 0);

        $currentPassword = (string)$request->post('current_password', '');
        $newPassword = (string)$request->post('new_password', '');
        $confirmPassword = (string)$request->post('confirm_password', '');

        if ($newPassword === '' || strlen($newPassword) < 6) {
            Session::flash('error', 'নতুন পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।');
            return Response::redirect('/profile#security');
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'নতুন পাসওয়ার্ড এবং নিশ্চিতকরণ পাসওয়ার্ড মেলেনি।');
            return Response::redirect('/profile#security');
        }

        $stmt = $this->db->prepare("SELECT `password` FROM `users` WHERE `id` = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            Session::flash('error', 'বর্তমান পাসওয়ার্ডটি সঠিক নয়।');
            return Response::redirect('/profile#security');
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $this->db->prepare("UPDATE `users` SET `password` = :p, `updated_at` = NOW() WHERE `id` = :id");
        $updateStmt->execute(['p' => $hashedPassword, 'id' => $userId]);

        Session::flash('success', 'আপনার পাসওয়ার্ড সফলভাবে পরিবর্তন করা হয়েছে।');
        return Response::redirect('/profile#security');
    }
}
