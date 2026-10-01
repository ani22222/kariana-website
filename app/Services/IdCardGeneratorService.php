<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;
use PDO;

/**
 * Kariana Quran Official ID Card Generator Service
 * Converts Adobe Illustrator (.ai) vector master templates into dynamic, print-ready
 * ID cards for Teachers (মুয়াল্লিম) and District Directors (জেলা পরিচালক).
 */
class IdCardGeneratorService
{
    private PDO $db;
    private string $templateDir;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->templateDir = dirname(__DIR__, 2) . '/public/assets/templates';
    }

    /**
     * Generate ID Card for Teacher or Director
     *
     * @param string $type 'teacher' | 'director'
     * @param int $id
     * @param string $side 'front' | 'back' | 'both'
     * @return array{
     *   success: bool,
     *   message: string,
     *   front_svg: ?string,
     *   back_svg: ?string,
     *   member: ?array,
     *   type: string
     * }
     */
    public function generateCard(string $type, int $id, string $side = 'both'): array
    {
        if ($type === 'director') {
            return $this->generateDirectorIdCard($id, $side);
        }
        return $this->generateTeacherIdCard($id, $side);
    }

    /**
     * Generate ID Card for Teacher (মুয়াল্লিম)
     */
    public function generateTeacherIdCard(int $teacherId, string $side = 'both'): array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, d.name as director_name, d.district_name as director_district 
            FROM `teachers` t 
            LEFT JOIN `directors` d ON t.director_id = d.id 
            WHERE t.id = ? 
            LIMIT 1
        ");
        $stmt->execute([$teacherId]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            return [
                'success'   => false,
                'message'   => 'শিক্ষকের তথ্য পাওয়া যায়নি।',
                'front_svg' => null,
                'back_svg'  => null,
                'member'    => null,
                'type'      => 'teacher'
            ];
        }

        // Verify Central Office Approval
        $isApproved = ($teacher['approval_status'] ?? '') === 'approved' || ($teacher['status'] ?? '') === 'active';
        if (!$isApproved) {
            return [
                'success'   => false,
                'message'   => 'এই শিক্ষকের তথ্য এখনও প্রধান কার্যালয় (মাওলানা সাদ্দাম হোসেন) কর্তৃক অনুমোদিত হয়নি।',
                'front_svg' => null,
                'back_svg'  => null,
                'member'    => $teacher,
                'type'      => 'teacher'
            ];
        }

        // Ensure UUID exists
        $uuid = $teacher['kyc_uuid'] ?? '';
        if (empty($uuid)) {
            $uuid = bin2hex(random_bytes(16));
            $this->db->prepare("UPDATE `teachers` SET `kyc_uuid` = ? WHERE `id` = ?")->execute([$uuid, $teacherId]);
            $teacher['kyc_uuid'] = $uuid;
        }

        // Format Fields
        $role = 'শিক্ষক';
        $name = !empty($teacher['name']) ? $teacher['name'] : 'শিক্ষকের নাম';
        $fatherName = !empty($teacher['father_name']) ? $teacher['father_name'] : 'মোঃ মালেক মিয়া';
        $dob = !empty($teacher['dob']) ? $teacher['dob'] : 'প্রযোজ্য নয়';
        $address = !empty($teacher['address']) ? $teacher['address'] : (!empty($teacher['area_name']) ? $teacher['area_name'] : 'বাংলাদেশ');
        
        // Joining year in Bengali numerals
        $joinYearRaw = !empty($teacher['joined_date']) ? date('Y', strtotime($teacher['joined_date'])) : date('Y');
        $joinYear = $this->toBengaliNumerals($joinYearRaw) . ' ইং';

        // Teacher ID No in Bengali numerals
        $idNoRaw = (string)$teacherId;
        $idNo = $this->toBengaliNumerals($idNoRaw);

        // Blood Group
        $bloodGroup = !empty($teacher['blood_group']) ? $teacher['blood_group'] : 'A+';

        // Expiry Date in Bengali
        $expiryDate = $this->formatBengaliExpiry($teacher['valid_until'] ?? null);

        // Photo element
        $photoElement = $this->buildPhotoElement($teacher['photo'] ?? null);

        $frontSvg = null;
        $backSvg = null;

        if ($side === 'front' || $side === 'both') {
            $templatePath = $this->templateDir . '/custom_teacher_id_card.svg';
            if (!file_exists($templatePath)) {
                return ['success' => false, 'message' => 'টেমপ্লেট ফাইল পাওয়া যায়নি।', 'front_svg' => null, 'back_svg' => null, 'member' => $teacher, 'type' => 'teacher'];
            }
            $templateContent = file_get_contents($templatePath);

            $replacements = [
                '{{ROLE}}'           => htmlspecialchars($role, ENT_QUOTES, 'UTF-8'),
                '{{NAME}}'           => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                '{{FATHER_NAME}}'    => htmlspecialchars($fatherName, ENT_QUOTES, 'UTF-8'),
                '{{DOB}}'            => htmlspecialchars($dob, ENT_QUOTES, 'UTF-8'),
                '{{ADDRESS}}'        => htmlspecialchars($address, ENT_QUOTES, 'UTF-8'),
                '{{JOIN_YEAR}}'      => htmlspecialchars($joinYear, ENT_QUOTES, 'UTF-8'),
                '{{ID_NO}}'          => htmlspecialchars($idNo, ENT_QUOTES, 'UTF-8'),
                '{{BLOOD_GROUP}}'    => htmlspecialchars($bloodGroup, ENT_QUOTES, 'UTF-8'),
                '{{EXPIRY_DATE}}'    => htmlspecialchars($expiryDate, ENT_QUOTES, 'UTF-8'),
                '{{PHOTO_ELEMENT}}'  => $photoElement,
            ];

            $frontSvg = str_replace(array_keys($replacements), array_values($replacements), $templateContent);
        }

        if ($side === 'back' || $side === 'both') {
            $backTemplatePath = $this->templateDir . '/custom_id_card_back.svg';
            if (file_exists($backTemplatePath)) {
                $backTemplateContent = file_get_contents($backTemplatePath);
                // Dynamic QR code overlay pointing to central verification
                $verifyUrl = "https://project.rasel.cloud/kariana/verify/{$uuid}";
                $qrOverlay = '<g clip-path="url(#backQrClip)"><image href="https://api.qrserver.com/v1/create-qr-code/?size=160x160&amp;data=' . urlencode($verifyUrl) . '" x="220" y="140" width="160" height="160"/></g>';
                $backSvg = str_replace('{{QR_CODE_OVERLAY}}', $qrOverlay, $backTemplateContent);
            }
        }

        return [
            'success'   => true,
            'message'   => 'শিক্ষক পরিচয়পত্র সফলভাবে জেনারেট হয়েছে।',
            'front_svg' => $frontSvg,
            'back_svg'  => $backSvg,
            'member'    => $teacher,
            'type'      => 'teacher'
        ];
    }

    /**
     * Generate ID Card for District Director (জেলা পরিচালক)
     */
    public function generateDirectorIdCard(int $directorId, string $side = 'both'): array
    {
        $stmt = $this->db->prepare("SELECT * FROM `directors` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$directorId]);
        $director = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$director) {
            return [
                'success'   => false,
                'message'   => 'পরিচালকের তথ্য পাওয়া যায়নি।',
                'front_svg' => null,
                'back_svg'  => null,
                'member'    => null,
                'type'      => 'director'
            ];
        }

        // Ensure UUID exists
        $uuid = $director['kyc_uuid'] ?? '';
        if (empty($uuid)) {
            $uuid = bin2hex(random_bytes(16));
            $this->db->prepare("UPDATE `directors` SET `kyc_uuid` = ? WHERE `id` = ?")->execute([$uuid, $directorId]);
            $director['kyc_uuid'] = $uuid;
        }

        // Format Fields
        $role = !empty($director['designation']) ? $director['designation'] : 'জেলা পরিচালক';
        $name = !empty($director['name']) ? $director['name'] : 'পরিচালকের নাম';
        $fatherName = !empty($director['father_name']) ? $director['father_name'] : 'মাওলানা মোহাম্মদ আলী';
        $dob = !empty($director['dob']) ? $director['dob'] : 'প্রযোজ্য নয়';
        $address = !empty($director['address']) ? $director['address'] : (!empty($director['district_name']) ? $director['district_name'] : 'বাংলাদেশ');
        
        // Joining year in Bengali numerals
        $joinYearRaw = !empty($director['created_at']) ? date('Y', strtotime($director['created_at'])) : date('Y');
        $joinYear = $this->toBengaliNumerals($joinYearRaw) . ' ইং';

        // Director ID No in Bengali numerals
        $idNoRaw = (string)$directorId;
        $idNo = $this->toBengaliNumerals($idNoRaw);

        // Blood Group
        $bloodGroup = !empty($director['blood_group']) ? $director['blood_group'] : 'O+';

        // Expiry Date in Bengali (Directors have 1-year or extended validity)
        $expiryDate = $this->formatBengaliExpiry(date('Y-m-d', strtotime('+1 year')));

        // Photo element
        $photoElement = $this->buildPhotoElement($director['photo'] ?? null);

        $frontSvg = null;
        $backSvg = null;

        if ($side === 'front' || $side === 'both') {
            $templatePath = $this->templateDir . '/custom_director_id_card.svg';
            if (!file_exists($templatePath)) {
                $templatePath = $this->templateDir . '/custom_teacher_id_card.svg';
            }
            $templateContent = file_get_contents($templatePath);

            $replacements = [
                '{{ROLE}}'           => htmlspecialchars($role, ENT_QUOTES, 'UTF-8'),
                '{{NAME}}'           => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                '{{FATHER_NAME}}'    => htmlspecialchars($fatherName, ENT_QUOTES, 'UTF-8'),
                '{{DOB}}'            => htmlspecialchars($dob, ENT_QUOTES, 'UTF-8'),
                '{{ADDRESS}}'        => htmlspecialchars($address, ENT_QUOTES, 'UTF-8'),
                '{{JOIN_YEAR}}'      => htmlspecialchars($joinYear, ENT_QUOTES, 'UTF-8'),
                '{{ID_NO}}'          => htmlspecialchars($idNo, ENT_QUOTES, 'UTF-8'),
                '{{BLOOD_GROUP}}'    => htmlspecialchars($bloodGroup, ENT_QUOTES, 'UTF-8'),
                '{{EXPIRY_DATE}}'    => htmlspecialchars($expiryDate, ENT_QUOTES, 'UTF-8'),
                '{{PHOTO_ELEMENT}}'  => $photoElement,
            ];

            $frontSvg = str_replace(array_keys($replacements), array_values($replacements), $templateContent);
        }

        if ($side === 'back' || $side === 'both') {
            $backTemplatePath = $this->templateDir . '/custom_id_card_back.svg';
            if (file_exists($backTemplatePath)) {
                $backTemplateContent = file_get_contents($backTemplatePath);
                $verifyUrl = "https://project.rasel.cloud/kariana/verify/{$uuid}";
                $qrOverlay = '<g clip-path="url(#backQrClip)"><image href="https://api.qrserver.com/v1/create-qr-code/?size=160x160&amp;data=' . urlencode($verifyUrl) . '" x="220" y="140" width="160" height="160"/></g>';
                $backSvg = str_replace('{{QR_CODE_OVERLAY}}', $qrOverlay, $backTemplateContent);
            }
        }

        return [
            'success'   => true,
            'message'   => 'পরিচালক পরিচয়পত্র সফলভাবে জেনারেট হয়েছে।',
            'front_svg' => $frontSvg,
            'back_svg'  => $backSvg,
            'member'    => $director,
            'type'      => 'director'
        ];
    }

    /**
     * Backward-compatible method for teacher ID card SVG
     */
    public function generateTeacherIdCardSvg(int $teacherId): array
    {
        $res = $this->generateTeacherIdCard($teacherId, 'front');
        return [
            'success' => $res['success'],
            'message' => $res['message'],
            'svg'     => $res['front_svg'],
            'teacher' => $res['member'],
            'expiry'  => $res['member']['valid_until'] ?? null,
        ];
    }

    /**
     * Build SVG photo element or elegant scholar silhouette avatar
     */
    private function buildPhotoElement(?string $photoPath): string
    {
        $baseDir = dirname(__DIR__, 2);
        if (!empty($photoPath)) {
            $fullPath = $baseDir . '/public/' . ltrim($photoPath, '/');
            if (file_exists($fullPath) && is_file($fullPath)) {
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $b64 = base64_encode(file_get_contents($fullPath));
                return '<image href="data:' . $mime . ';base64,' . $b64 . '" x="209" y="213" width="182" height="226" preserveAspectRatio="xMidYMid slice"/>';
            }
            // If it's an external URL
            if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
                return '<image href="' . htmlspecialchars($photoPath, ENT_QUOTES, 'UTF-8') . '" x="209" y="213" width="182" height="226" preserveAspectRatio="xMidYMid slice"/>';
            }
        }

        // High-quality Islamic Scholar Silhouette Placeholder
        return '
            <rect x="209" y="213" width="182" height="226" fill="#e8f5e9"/>
            <circle cx="300" cy="295" r="42" fill="#008d36" opacity="0.25"/>
            <ellipse cx="300" cy="405" rx="72" ry="52" fill="#008d36" opacity="0.25"/>
            <text x="300" y="380" font-family="\'Hind Siliguri\', sans-serif" font-size="14" fill="#008d36" text-anchor="middle" font-weight="600">ছবি সংযুক্তি বাকি</text>
        ';
    }

    /**
     * Convert English digits to Bengali numerals
     */
    public function toBengaliNumerals(string $input): string
    {
        $en = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $bn = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        return str_replace($en, $bn, $input);
    }

    /**
     * Format Bengali expiry string
     */
    private function formatBengaliExpiry(?string $dateStr): string
    {
        $time = !empty($dateStr) ? strtotime($dateStr) : strtotime('+1 year');
        $day = $this->toBengaliNumerals(date('d', $time));
        $year = $this->toBengaliNumerals(date('Y', $time));
        
        $monthsBn = [
            1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
            5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর'
        ];
        $month = $monthsBn[(int)date('n', $time)] ?? 'ডিসেম্বর';

        return "{$day} {$month} {$year} ইং পর্যন্ত";
    }
}
