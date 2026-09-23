<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\View;
use Core\Database;
use Core\Session;
use App\Services\KycSecurityService;
use PDO;

/**
 * VerificationController
 * Manages Centralized 2-Step KYC Verification (/verify/kyc)
 * and Public Digital Accreditation Credential / Dual-Sided ID Card (/verify/{uuid})
 */
class VerificationController
{
    private PDO $db;
    private KycSecurityService $kycService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->kycService = new KycSecurityService();
    }

    /**
     * Centralized 2-Step KYC Verification Wizard
     * URL: GET /verify/kyc
     */
    public function kycPortal(Request $request): Response
    {
        $token = $request->get('token', '');
        $userData = null;
        $activeUuid = null;
        $kycLevel = 0;

        // 1. Try decrypting secure transit token if provided
        if (!empty($token)) {
            $decrypted = $this->kycService->decryptTransitToken($token);
            if ($decrypted) {
                $userData = [
                    'user_type' => $decrypted['user_type'] ?? 'user',
                    'user_id'   => (int)($decrypted['user_id'] ?? 0),
                    'phone'     => $decrypted['phone'] ?? '',
                    'name'      => $decrypted['name'] ?? '',
                    'district'  => $decrypted['district'] ?? '',
                ];
            }
        }

        // 2. If no token, check active session
        if (!$userData) {
            if (Session::has('director_id')) {
                $stmt = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = ? LIMIT 1");
                $stmt->execute([(int)Session::get('director_id')]);
                $dir = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($dir) {
                    $userData = [
                        'user_type' => 'director',
                        'user_id'   => (int)$dir['id'],
                        'phone'     => $dir['phone'] ?? '',
                        'name'      => $dir['name'] ?? '',
                        'district'  => $dir['district_name'] ?? '',
                    ];
                    $kycLevel = (int)($dir['kyc_level'] ?? 0);
                    $activeUuid = $dir['kyc_uuid'] ?? null;
                }
            } elseif (Session::has('teacher')) {
                $t = Session::get('teacher');
                if (is_array($t) && !empty($t['id'])) {
                    $stmt = $this->db->prepare("SELECT * FROM `teachers` WHERE `id` = ? LIMIT 1");
                    $stmt->execute([(int)$t['id']]);
                    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($teacher) {
                        $userData = [
                            'user_type' => 'teacher',
                            'user_id'   => (int)$teacher['id'],
                            'phone'     => $teacher['phone'] ?? '',
                            'name'      => $teacher['name'] ?? '',
                            'district'  => $teacher['area_name'] ?? '',
                        ];
                        $kycLevel = (int)($teacher['kyc_level'] ?? 0);
                        $activeUuid = $teacher['kyc_uuid'] ?? null;
                    }
                }
            } elseif (Session::isLoggedIn()) {
                $u = Session::getUser();
                if ($u && !empty($u['id'])) {
                    $stmt = $this->db->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
                    $stmt->execute([(int)$u['id']]);
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($user) {
                        $userData = [
                            'user_type' => 'user',
                            'user_id'   => (int)$user['id'],
                            'phone'     => $user['phone'] ?? '',
                            'name'      => $user['name'] ?? '',
                            'district'  => $user['district'] ?? '',
                        ];
                        $kycLevel = (int)($user['kyc_level'] ?? 0);
                        $activeUuid = $user['kyc_uuid'] ?? null;
                    }
                }
            }
        }

        // 3. Check existing record in kyc_verifications table
        if ($userData && empty($activeUuid)) {
            $stmt = $this->db->prepare("
                SELECT * FROM `kyc_verifications` 
                WHERE `user_type` = ? AND `user_id` = ? 
                ORDER BY `id` DESC LIMIT 1
            ");
            $stmt->execute([$userData['user_type'], $userData['user_id']]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                $activeUuid = $existing['uuid'];
                $kycLevel = max($kycLevel, (int)$existing['kyc_level']);
            }
        }

        return new Response(View::render('verify/kyc', [
            'title'       => 'সেন্ট্রালাইজড ২-ধাপ কেওয়াইসি ভেরিফিকেশন | কারিয়ানা কুরআন',
            'userData'    => $userData,
            'token'       => $token,
            'activeUuid'  => $activeUuid,
            'kycLevel'    => $kycLevel,
        ]));
    }

    /**
     * Step 1: Send SMS OTP
     * URL: POST /verify/kyc/send-otp
     */
    public function sendOtp(Request $request): Response
    {
        $phone = trim((string)$request->post('phone', ''));
        $userType = trim((string)$request->post('user_type', 'user'));
        $userId = (int)$request->post('user_id', 0);
        $token = trim((string)$request->post('token', ''));

        // Validate via token if present
        if (!empty($token)) {
            $decrypted = $this->kycService->decryptTransitToken($token);
            if ($decrypted) {
                $userType = $decrypted['user_type'] ?? $userType;
                $userId = (int)($decrypted['user_id'] ?? $userId);
                if (empty($phone)) $phone = $decrypted['phone'] ?? '';
            }
        }

        if (empty($phone)) {
            return Response::json(['success' => false, 'message' => 'মোবাইল নম্বর প্রদান করুন।'], 400);
        }

        $res = $this->kycService->sendSmsOtp($phone, $userType, $userId);
        return Response::json($res, $res['success'] ? 200 : 400);
    }

    /**
     * Step 1 Verify: Verify SMS OTP
     * URL: POST /verify/kyc/verify-otp
     */
    public function verifyOtp(Request $request): Response
    {
        $uuid = trim((string)$request->post('uuid', ''));
        $otp = trim((string)$request->post('otp_code', ''));

        if (empty($uuid) || empty($otp)) {
            return Response::json(['success' => false, 'message' => 'ওটিপি কোড এবং সেশন আইডি দিন।'], 400);
        }

        $res = $this->kycService->verifySmsOtp($uuid, $otp);
        return Response::json($res, $res['success'] ? 200 : 400);
    }

    /**
     * Step 2 Verify: Verify Government NID + Date of Birth
     * URL: POST /verify/kyc/verify-nid
     */
    public function verifyNid(Request $request): Response
    {
        $uuid = trim((string)$request->post('uuid', ''));
        $nid = trim((string)$request->post('nid_number', ''));
        $dob = trim((string)$request->post('date_of_birth', ''));

        if (empty($uuid) || empty($nid) || empty($dob)) {
            return Response::json(['success' => false, 'message' => 'এনআইডি নম্বর ও সঠিক জন্ম তারিখ পূরণ করুন।'], 400);
        }

        $res = $this->kycService->verifyGovernmentNid($uuid, $nid, $dob);

        if (!empty($res['success'])) {
            try {
                $stmtK = $this->db->prepare("SELECT id FROM `kyc_verifications` WHERE `uuid` = ? LIMIT 1");
                $stmtK->execute([$uuid]);
                $kycId = (int)$stmtK->fetchColumn();
                if ($kycId > 0) {
                    \App\Services\OrderRoutingService::routeKycVerification($kycId);
                }
            } catch (\Throwable $e) {}
        }

        return Response::json($res, $res['success'] ? 200 : 400);
    }

    /**
     * Public Accreditation & Dual-Sided ID Card
     * URL: GET /verify/{uuid}
     */
    public function publicCard(Request $request, string $uuid): Response
    {
        $uuid = trim($uuid);
        $stmt = $this->db->prepare("SELECT * FROM `kyc_verifications` WHERE `uuid` = ? LIMIT 1");
        $stmt->execute([$uuid]);
        $kyc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kyc) {
            return new Response(View::render('verify/not_found', [
                'title' => 'সনদ বা কার্ড পাওয়া যায়নি | কারিয়ানা কুরআন',
                'uuid'  => $uuid
            ]), 404);
        }

        $entity = null;
        $roleTitle = 'সম্মানিত সদস্য';
        $photoUrl = null;
        $nameBn = '';
        $nameEn = '';
        $district = '';
        $phone = $kyc['phone'] ?? '';

        // Extract government NID verified data if available
        $nidData = !empty($kyc['nid_response_json']) ? json_decode($kyc['nid_response_json'], true) : [];
        if (!empty($nidData['name_bn'])) $nameBn = $nidData['name_bn'];
        if (!empty($nidData['name_en'])) $nameEn = $nidData['name_en'];
        if (!empty($nidData['photo'])) $photoUrl = $nidData['photo'];
        if (!empty($nidData['district'])) $district = $nidData['district'];

        if ($kyc['user_type'] === 'teacher') {
            $s = $this->db->prepare("SELECT * FROM `teachers` WHERE `id` = ? LIMIT 1");
            $s->execute([(int)$kyc['user_id']]);
            $entity = $s->fetch(PDO::FETCH_ASSOC);
            $roleTitle = 'অনুমোদিত মুয়াল্লিম (শিক্ষক)';
            if (empty($nameBn) && $entity) $nameBn = $entity['name'];
            if (empty($district) && $entity) $district = $entity['area_name'];
            if (empty($photoUrl) && !empty($entity['photo'])) $photoUrl = $entity['photo'];
        } elseif ($kyc['user_type'] === 'director') {
            $s = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = ? LIMIT 1");
            $s->execute([(int)$kyc['user_id']]);
            $entity = $s->fetch(PDO::FETCH_ASSOC);
            $roleTitle = 'অফিসিয়াল জেলা পরিচালক';
            if (empty($nameBn) && $entity) $nameBn = $entity['name'];
            if (empty($district) && $entity) $district = $entity['district_name'];
            if (empty($photoUrl) && !empty($entity['photo'])) $photoUrl = $entity['photo'];
        } else {
            $s = $this->db->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
            $s->execute([(int)$kyc['user_id']]);
            $entity = $s->fetch(PDO::FETCH_ASSOC);
            $roleTitle = 'নিবন্ধিত শিক্ষার্থী / পাঠক';
            if (empty($nameBn) && $entity) $nameBn = $entity['name'];
            if (empty($district) && $entity) $district = $entity['district'];
        }

        $certSerial = 'KQ-CERT-2026-' . strtoupper(substr(hash('sha256', $uuid), 0, 8));
        $verifyUrl = "https://project.rasel.cloud/kariana/verify/{$uuid}";

        return new Response(View::render('verify/public_card', [
            'title'       => "অফিসিয়াল ভেরিফিকেশন ও আইডি কার্ড: {$nameBn} | কারিয়ানা কুরআন",
            'kyc'         => $kyc,
            'entity'      => $entity,
            'roleTitle'   => $roleTitle,
            'nameBn'      => $nameBn,
            'nameEn'      => $nameEn,
            'district'    => $district,
            'photoUrl'    => $photoUrl,
            'phone'       => $phone,
            'certSerial'  => $certSerial,
            'verifyUrl'   => $verifyUrl,
            'uuid'        => $uuid,
        ]));
    }
}
