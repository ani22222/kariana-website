<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;
use PDO;

/**
 * Kariana Quran Official ID Card & Accreditation Generator Service
 * Renders vector SVG and printable official ID cards with 1-year validity
 * and central verification tokens.
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
     * Generate SVG ID Card for an approved teacher
     *
     * @param int $teacherId
     * @return array{success: bool, message: string, svg: ?string, teacher: ?array}
     */
    public function generateTeacherIdCardSvg(int $teacherId): array
    {
        $stmt = $this->db->prepare("
            SELECT t.*, d.name_bn as district_name 
            FROM `teachers` t 
            LEFT JOIN `districts` d ON t.district_id = d.id 
            WHERE t.id = ? 
            LIMIT 1
        ");
        $stmt->execute([$teacherId]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            return ['success' => false, 'message' => 'শিক্ষকের তথ্য পাওয়া যায়নি।', 'svg' => null, 'teacher' => null];
        }

        // Verify Central Office Approval
        $isApproved = ($teacher['approval_status'] ?? '') === 'approved' || ($teacher['status'] ?? '') === 'active';
        if (!$isApproved) {
            return [
                'success' => false,
                'message' => 'এই শিক্ষকের তথ্য এখনও প্রধান কার্যালয় (মাওলানা সাদ্দাম হোসেন) কর্তৃক অনুমোদিত হয়নি। অনুমোদনের পরই আইডি কার্ড জেনারেট করা সম্ভব।',
                'svg'     => null,
                'teacher' => $teacher
            ];
        }

        // Template Selection (custom uploaded template takes priority)
        $templatePath = $this->templateDir . '/custom_teacher_id_card.svg';
        if (!file_exists($templatePath)) {
            $templatePath = $this->templateDir . '/teacher_id_card_template.svg';
        }

        if (!file_exists($templatePath)) {
            return ['success' => false, 'message' => 'আইডি কার্ডের ভেক্টর টেমপ্লেট পাওয়া যায়নি।', 'svg' => null, 'teacher' => $teacher];
        }

        $svgContent = file_get_contents($templatePath);

        // Date & 1-Year Validity Calculation
        $approvedDate = !empty($teacher['approved_at']) ? date('d/m/Y', strtotime($teacher['approved_at'])) : date('d/m/Y');
        $expiryDate = !empty($teacher['valid_until']) ? date('d/m/Y', strtotime($teacher['valid_until'])) : (!empty($teacher['expiry_date']) ? date('d/m/Y', strtotime($teacher['expiry_date'])) : date('d/m/Y', strtotime('+1 year')));

        // Prepare Replacements
        $replacements = [
            '{{TEACHER_NAME}}'   => htmlspecialchars($teacher['name'] ?? 'মুয়াল্লিম নাম', ENT_QUOTES, 'UTF-8'),
            '{{TEACHER_CODE}}'   => htmlspecialchars($teacher['teacher_code'] ?? ('KRN-T-' . str_pad((string)$teacherId, 4, '0', STR_PAD_LEFT)), ENT_QUOTES, 'UTF-8'),
            '{{DISTRICT}}'       => htmlspecialchars($teacher['district_name'] ?? ($teacher['district'] ?? 'সারাদেশ'), ENT_QUOTES, 'UTF-8'),
            '{{PHONE}}'          => htmlspecialchars($teacher['phone'] ?? '01XXXXXXXXX', ENT_QUOTES, 'UTF-8'),
            '{{APPROVED_DATE}}'  => $approvedDate,
            '{{EXPIRY_DATE}}'    => $expiryDate,
            '{{UUID}}'           => htmlspecialchars($teacher['uuid'] ?? bin2hex(random_bytes(8)), ENT_QUOTES, 'UTF-8'),
        ];

        $renderedSvg = str_replace(array_keys($replacements), array_values($replacements), $svgContent);

        return [
            'success' => true,
            'message' => 'আইডি কার্ড সফলভাবে প্রস্তুত হয়েছে।',
            'svg'     => $renderedSvg,
            'teacher' => $teacher,
            'expiry'  => $expiryDate
        ];
    }
}
