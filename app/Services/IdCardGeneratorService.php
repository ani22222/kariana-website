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
     * Generate A4 Ready-To-Print Sheet SVG containing both Front & Back ID Card faces
     * with standard CR80 physical dimensions (54mm x 85.6mm) and cutting guidelines.
     */
    public function generateA4SheetSvg(string $type, int $id): array
    {
        $card = $this->generateCard($type, $id, 'both');
        if (!$card['success']) {
            return $card;
        }

        $frontSvg = $card['front_svg'] ?? '';
        $backSvg = $card['back_svg'] ?? '';

        $frontInner = $this->extractInnerSvg($frontSvg, 'f');
        $backInner = $this->extractInnerSvg($backSvg, 'b');

        // Scale factors: 600 -> 540 (0.9), 960 -> 856 (856 / 960)
        $sx = number_format(540.0 / 600.0, 4, '.', '');
        $sy = number_format(856.0 / 960.0, 4, '.', '');

        $member = $card['member'];
        $roleTitle = ($type === 'director') ? 'জেলা পরিচালক' : 'শিক্ষক (মুয়াল্লিম)';
        $memberName = htmlspecialchars($member['name'] ?? '', ENT_QUOTES, 'UTF-8');

        $a4Svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 2100 2970" width="2100" height="2970">
  <defs>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&amp;display=swap');
      .a4-title { font-family: 'Hind Siliguri', 'Nirmala UI', sans-serif; font-size: 42px; font-weight: 700; fill: #064e3b; text-anchor: middle; }
      .a4-sub   { font-family: 'Hind Siliguri', 'Nirmala UI', sans-serif; font-size: 26px; font-weight: 600; fill: #4b5563; text-anchor: middle; }
      .a4-meta  { font-family: 'Hind Siliguri', 'Nirmala UI', sans-serif; font-size: 24px; font-weight: 500; fill: #6b7280; text-anchor: middle; }
      .a4-guide { font-family: 'Hind Siliguri', 'Nirmala UI', sans-serif; font-size: 24px; font-weight: 700; fill: #047857; text-anchor: middle; }
      .a4-dim   { font-family: monospace; font-size: 20px; font-weight: bold; fill: #6b7280; text-anchor: middle; }
    </style>
  </defs>

  <!-- Clean A4 White Page Background -->
  <rect x="0" y="0" width="2100" height="2970" fill="#ffffff"/>

  <!-- Top Header Information on Sheet -->
  <g transform="translate(1050, 180)">
    <text y="0" class="a4-title">ক্ব-রিয়ানা কুরআন শিক্ষা সোসাইটি — অফিসিয়াল পরিচয়পত্র প্রিন্ট শিট</text>
    <text y="50" class="a4-sub">প্রমিত সাইজ: CR80 (৫৪ মিমি × ৮৫.৬ মিমি) • রেডি-টু-প্রিন্ট উভয় পৃষ্ঠা (Front &amp; Back) • {$roleTitle}: {$memberName}</text>
    <text y="100" class="a4-meta">প্রিন্ট নির্দেশনা: A4 সাইজের ফটো পেপারে ১০০% স্কেলে (Actual Size / 100%) প্রিন্ট করুন।</text>
    <line x1="-700" y1="140" x2="700" y2="140" stroke="#d1d5db" stroke-width="2"/>
  </g>

  <!-- FRONT CARD SECTION (x=460, y=420, w=540, h=856) -->
  <g>
    <!-- Label above Front Card -->
    <text x="730" y="375" class="a4-guide">সম্মুখ ভাগ (Front Face)</text>
    <text x="730" y="405" class="a4-dim">54.0 mm × 85.6 mm</text>

    <!-- Corner Crop Marks for Front Card -->
    <line x1="420" y1="420" x2="455" y2="420" stroke="#111827" stroke-width="2.5"/>
    <line x1="460" y1="380" x2="460" y2="415" stroke="#111827" stroke-width="2.5"/>
    <line x1="1005" y1="420" x2="1040" y2="420" stroke="#111827" stroke-width="2.5"/>
    <line x1="1000" y1="380" x2="1000" y2="415" stroke="#111827" stroke-width="2.5"/>
    <line x1="420" y1="1276" x2="455" y2="1276" stroke="#111827" stroke-width="2.5"/>
    <line x1="460" y1="1281" x2="460" y2="1316" stroke="#111827" stroke-width="2.5"/>
    <line x1="1005" y1="1276" x2="1040" y2="1276" stroke="#111827" stroke-width="2.5"/>
    <line x1="1000" y1="1281" x2="1000" y2="1316" stroke="#111827" stroke-width="2.5"/>

    <!-- Front Card Content -->
    <g transform="translate(460, 420) scale({$sx}, {$sy})">
      {$frontInner}
    </g>

    <!-- Outer Hairline Cut Border -->
    <rect x="460" y="420" width="540" height="856" rx="25" ry="25" fill="none" stroke="#d1d5db" stroke-width="1.5"/>
  </g>

  <!-- CENTER FOLDING & CUTTING GUIDELINE (x=1050) -->
  <g>
    <line x1="1050" y1="370" x2="1050" y2="1320" stroke="#6b7280" stroke-width="2.5" stroke-dasharray="8 8"/>
    <circle cx="1050" cy="848" r="34" fill="#ffffff" stroke="#6b7280" stroke-width="2"/>
    <text x="1050" y="858" font-size="28" text-anchor="middle">✂️</text>
    <text x="1050" y="910" font-family="'Hind Siliguri', sans-serif" font-size="18" fill="#4b5563" text-anchor="middle" font-weight="bold">ভাঁজ / কাটার দাগ</text>
  </g>

  <!-- BACK CARD SECTION (x=1100, y=420, w=540, h=856) -->
  <g>
    <!-- Label above Back Card -->
    <text x="1370" y="375" class="a4-guide">পশ্চাৎ ভাগ (Back Face)</text>
    <text x="1370" y="405" class="a4-dim">54.0 mm × 85.6 mm</text>

    <!-- Corner Crop Marks for Back Card -->
    <line x1="1060" y1="420" x2="1095" y2="420" stroke="#111827" stroke-width="2.5"/>
    <line x1="1100" y1="380" x2="1100" y2="415" stroke="#111827" stroke-width="2.5"/>
    <line x1="1645" y1="420" x2="1680" y2="420" stroke="#111827" stroke-width="2.5"/>
    <line x1="1640" y1="380" x2="1640" y2="415" stroke="#111827" stroke-width="2.5"/>
    <line x1="1060" y1="1276" x2="1095" y2="1276" stroke="#111827" stroke-width="2.5"/>
    <line x1="1100" y1="1281" x2="1100" y2="1316" stroke="#111827" stroke-width="2.5"/>
    <line x1="1645" y1="1276" x2="1680" y2="1276" stroke="#111827" stroke-width="2.5"/>
    <line x1="1640" y1="1281" x2="1640" y2="1316" stroke="#111827" stroke-width="2.5"/>

    <!-- Back Card Content -->
    <g transform="translate(1100, 420) scale({$sx}, {$sy})">
      {$backInner}
    </g>

    <!-- Outer Hairline Cut Border -->
    <rect x="1100" y="420" width="540" height="856" rx="25" ry="25" fill="none" stroke="#d1d5db" stroke-width="1.5"/>
  </g>

  <!-- LAMINATION & CUTTING INSTRUCTIONS BOX -->
  <g transform="translate(1050, 1420)">
    <rect x="-650" y="0" width="1300" height="220" rx="20" fill="#f8fafc" stroke="#cbd5e1" stroke-width="2"/>
    <text x="-600" y="50" font-family="'Hind Siliguri', sans-serif" font-size="28" font-weight="bold" fill="#065f46" text-anchor="start">📋 প্রিন্টিং ও লেমিনেশন গাইডলাইন (Printing &amp; Pouch Instructions):</text>
    <text x="-600" y="95" font-family="'Hind Siliguri', sans-serif" font-size="22" fill="#334155" text-anchor="start">১. যেকোনো স্ট্যান্ডার্ড কালার প্রিন্টারে A4 সাইজের গ্লসি পেপার (Glossy Photo Paper) নির্বাচন করুন।</text>
    <text x="-600" y="135" font-family="'Hind Siliguri', sans-serif" font-size="22" fill="#334155" text-anchor="start">২. প্রিন্টার ডায়ালগে স্কেল (Scale) অপশনে অবশ্যই "100%" অথবা "Actual Size" সিলেক্ট করবেন (Fit to page নয়)।</text>
    <text x="-600" y="175" font-family="'Hind Siliguri', sans-serif" font-size="22" fill="#334155" text-anchor="start">৩. প্রিন্ট শেষে কাঁচি বা পেপার কাটার দিয়ে ক্রপ মার্ক (কাটিং দাগ) বরাবর কেটে প্লাস্টিক আইডি কার্ড পাউচে প্রবেশ করিয়ে লেমিনেশন করুন।</text>
  </g>

  <!-- BOTTOM FOOTER -->
  <g transform="translate(1050, 2850)">
    <line x1="-700" y1="0" x2="700" y2="0" stroke="#e5e7eb" stroke-width="2"/>
    <text y="40" class="a4-meta">ক্ব-রিয়ানা কুরআন শিক্ষা সোসাইটি • নিবন্ধিত কেন্দ্রীয় কার্যালয় • ওয়েবসাইট: www.karianaquran.com • হেল্পলাইন: 01712-415613</text>
  </g>
</svg>
SVG;

        return [
            'success'   => true,
            'message'   => 'A4 সাইজের পরিচয়পত্র শিট সফলভাবে প্রস্তুত হয়েছে।',
            'a4_svg'    => $a4Svg,
            'member'    => $member,
            'type'      => $type,
            'front_svg' => $frontSvg,
            'back_svg'  => $backSvg
        ];
    }

    /**
     * Helper to extract inner SVG content and prefix IDs to prevent collision
     */
    private function extractInnerSvg(string $svgStr, string $prefix = ''): string
    {
        // Strip outer <svg ...> and </svg>
        $inner = preg_replace('/^.*?<svg[^>]*>/is', '', $svgStr);
        $inner = preg_replace('/<\/svg>\s*$/is', '', $inner);

        if (!empty($prefix)) {
            // Find all id attributes and prefix them
            if (preg_match_all('/id=["\']([^"\']+)["\']/i', $inner, $matches)) {
                foreach (array_unique($matches[1]) as $idVal) {
                    $newId = "{$prefix}_{$idVal}";
                    $inner = str_replace([
                        "id=\"{$idVal}\"",
                        "id='{$idVal}'",
                        "url(#{$idVal})",
                        "url('#{$idVal}')",
                        "url(\"#{$idVal}\")",
                        "href=\"#{$idVal}\"",
                        "href='#{$idVal}'"
                    ], [
                        "id=\"{$newId}\"",
                        "id='{$newId}'",
                        "url(#{$newId})",
                        "url('#{$newId}')",
                        "url(\"#{$newId}\")",
                        "href=\"#{$newId}\"",
                        "href='#{$newId}'"
                    ], $inner);
                }
            }
        }

        return $inner;
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
