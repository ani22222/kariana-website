<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;
use Core\BengaliHelper;
use PDO;

/**
 * Multi-Tier Order & Admission Routing Service with Telegram Action Cards
 * Handles intelligent matching:
 * - Book Orders: Routed to respective District Director + Central Operations Manager
 * - Admissions: Routed to Huzur (Maulana Saddam Hossain) + Central Admin
 */
class OrderRoutingService
{
    private const BOT_TOKEN = '8830215516:AAHiW5FtCSfIOo5-xYMF0V2TfaMo1641bpE';
    private const ADMIN_CHAT_ID = '1827362508'; // Rasel Gazi (Super Admin)

    /**
     * Dispatch an interactive Action Card message via Telegram Bot API
     */
    public static function sendTelegramCard(string $chatId, string $text, array $inlineKeyboard): ?array
    {
        $url = 'https://api.telegram.org/bot' . self::BOT_TOKEN . '/sendMessage';
        $payload = [
            'chat_id'      => $chatId,
            'text'         => $text,
            'parse_mode'   => 'Markdown',
            'reply_markup' => json_encode(['inline_keyboard' => $inlineKeyboard])
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response ? json_decode($response, true) : null;
    }

    /**
     * Route a new Book Order:
     * 1. Detect matching District Director
     * 2. Update order routing in database
     * 3. Send Telegram Action Cards with 1-click decision buttons
     */
    public static function routeBookOrder(int $orderId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT o.*, b.title as book_title, b.discount_price, b.price
            FROM `book_orders` o
            LEFT JOIN `books` b ON o.book_id = b.id
            WHERE o.id = ? LIMIT 1
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return ['success' => false, 'message' => 'অর্ডারের তথ্য পাওয়া যায়নি।'];
        }

        $district = trim((string)($order['district'] ?? ''));
        $matchedDirector = null;

        if (!empty($district)) {
            // Find matching director by district_name
            $dirStmt = $db->prepare("
                SELECT * FROM `directors` 
                WHERE `district_name` LIKE ? OR ? LIKE CONCAT('%', `district_name`, '%') 
                LIMIT 1
            ");
            $dirStmt->execute(['%' . $district . '%', $district]);
            $matchedDirector = $dirStmt->fetch(PDO::FETCH_ASSOC);
        }

        $directorId = $matchedDirector ? (int)$matchedDirector['id'] : null;
        $status = $matchedDirector ? 'routed_to_director' : 'central_courier';

        // Update database with routing decision
        $upStmt = $db->prepare("
            UPDATE `book_orders` 
            SET `director_id` = :did, `status` = :st, `updated_at` = NOW() 
            WHERE `id` = :id
        ");
        $upStmt->execute([
            ':did' => $directorId,
            ':st'  => $status,
            ':id'  => $orderId
        ]);

        $bookTitle = $order['book_title'] ?? 'কারিয়ানা কুরআন';
        $qty = (int)($order['quantity'] ?? 1);
        $amount = (float)($order['total_amount'] ?? 0.0);
        $orderNum = $order['order_number'] ?? ('ORD-' . $orderId);

        // Build Telegram Action Card Text
        $dirName = $matchedDirector ? $matchedDirector['name'] . ' (' . $matchedDirector['district_name'] . ')' : 'কোনো জেলা পরিচালক পাওয়া যায়নি (সেন্ট্রাল স্টক থেকে যাবে)';
        
        $cardText = "📦 *নতুন বইয়ের অর্ডার — অ্যাকশন কার্ড*\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "🆔 *অর্ডার নং:* `{$orderNum}`\n";
        $cardText .= "📖 *বই:* {$bookTitle}\n";
        $cardText .= "🔢 *পরিমাণ:* " . BengaliHelper::toBengaliNumber($qty) . " কপি | *মোট:* ৳" . BengaliHelper::toBengaliNumber((int)$amount) . "\n";
        $cardText .= "👤 *গ্রাহক:* " . ($order['customer_name'] ?? 'নামবিহীন') . "\n";
        $cardText .= "📞 *মোবাইল:* `" . ($order['customer_phone'] ?? '') . "`\n";
        $cardText .= "📍 *ঠিকানা:* " . ($order['delivery_address'] ?? '') . " (" . ($district ?: 'অজানা') . ")\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "🏢 *রাউটিং:* {$dirName}\n";
        $cardText .= "⚡ *সিদ্ধান্ত নিন (১-ক্লিকে অনুমোদন বা কুরিয়ার):*";

        // Inline Keyboard Buttons
        $keyboard = [
            [
                ['text' => '✅ পরিচালক অনুমোদন', 'callback_data' => 'appv_bo_' . $orderId],
                ['text' => '🚚 সেন্ট্রাল কুরিয়ার', 'callback_data' => 'ship_bo_' . $orderId]
            ],
            [
                ['text' => '📞 ফোনে কথা বলুন', 'url' => 'tel:' . ($order['customer_phone'] ?? '')],
                ['text' => '❌ বাতিল', 'callback_data' => 'canc_bo_' . $orderId]
            ]
        ];

        // 1. Send to Super Admin / Central Manager
        self::sendTelegramCard(self::ADMIN_CHAT_ID, $cardText, $keyboard);

        // 2. If director has registered telegram_id in telegram_users, also notify them
        if ($matchedDirector && !empty($matchedDirector['phone'])) {
            $tgUserStmt = $db->prepare("SELECT telegram_id FROM `telegram_users` WHERE `phone` LIKE ? OR `username` LIKE ? LIMIT 1");
            $tgUserStmt->execute(['%' . substr($matchedDirector['phone'], -10), '%' . $matchedDirector['phone'] . '%']);
            $dirTgId = $tgUserStmt->fetchColumn();
            if ($dirTgId && (string)$dirTgId !== self::ADMIN_CHAT_ID) {
                self::sendTelegramCard((string)$dirTgId, $cardText, $keyboard);
            }
        }

        return [
            'success'          => true,
            'order_id'         => $orderId,
            'order_number'     => $orderNum,
            'routed_to'        => $dirName,
            'status'           => $status
        ];
    }

    /**
     * Route a new Online Course Admission Lead
     * Sends action card directly to Huzur / Principal & Super Admin
     */
    public static function routeAdmission(int $admissionId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT a.*, c.title as course_title, c.fee, c.discount_fee
            FROM `admissions` a
            LEFT JOIN `courses` c ON a.course_id = c.id
            WHERE a.id = ? LIMIT 1
        ");
        $stmt->execute([$admissionId]);
        $adm = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$adm) {
            return ['success' => false, 'message' => 'আবেদনের তথ্য পাওয়া যায়নি।'];
        }

        $appNum = $adm['application_number'] ?? ('ADM-' . $admissionId);
        $name = $adm['applicant_name'] ?? $adm['name'] ?? 'নামবিহীন';
        $phone = $adm['phone'] ?? '';
        $guardian = $adm['guardian_name'] ?? '—';
        $district = $adm['district'] ?? '—';
        $courseTitle = $adm['course_title'] ?? $adm['course_slug'] ?? 'হিফজ ও তাজবীদ কোর্স';

        $cardText = "🎓 *নতুন ভর্তি আবেদন — অ্যাকশন কার্ড*\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "🆔 *আবেদন নং:* `{$appNum}`\n";
        $cardText .= "📚 *কোর্স:* {$courseTitle}\n";
        $cardText .= "👤 *শিক্ষার্থী:* {$name}\n";
        $cardText .= "👨‍👦 *অভিভাবক:* {$guardian}\n";
        $cardText .= "📞 *মোবাইল:* `{$phone}`\n";
        $cardText .= "📍 *জেলা:* {$district}\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "⚡ *হযরত হুজুরের সিদ্ধান্ত:*";

        $keyboard = [
            [
                ['text' => '✅ ভর্তি নিশ্চিত (এনরোল)', 'callback_data' => 'appv_adm_' . $admissionId],
                ['text' => '📞 যোগাযোগ হয়েছে', 'callback_data' => 'cont_adm_' . $admissionId]
            ],
            [
                ['text' => '💬 হোয়াটসঅ্যাপ বার্তা', 'url' => 'https://wa.me/88' . preg_replace('/[^0-9]/', '', $phone)],
                ['text' => '❌ বাতিল', 'callback_data' => 'canc_adm_' . $admissionId]
            ]
        ];

        // Send to Super Admin / Maulana Saddam Hossain
        self::sendTelegramCard(self::ADMIN_CHAT_ID, $cardText, $keyboard);

        return [
            'success'            => true,
            'admission_id'       => $admissionId,
            'application_number' => $appNum
        ];
    }

    /**
     * Route a new Teacher Creation by District Director
     * Sends action card to Super Admin / Maulana Saddam Hossain for 1-click approval
     */
    public static function routeTeacherCreation(int $teacherId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT t.*, d.name as director_name, d.district_name, d.phone as director_phone
            FROM `teachers` t
            LEFT JOIN `directors` d ON t.director_id = d.id
            WHERE t.id = ? LIMIT 1
        ");
        $stmt->execute([$teacherId]);
        $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            return ['success' => false, 'message' => 'শিক্ষকের তথ্য পাওয়া যায়নি।'];
        }

        $cardText = "👨‍🏫 *নতুন শিক্ষক সংযোজন — অনুমোদন অ্যাকশন কার্ড*\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "👤 *শিক্ষক:* {$teacher['name']}\n";
        $cardText .= "📞 *মোবাইল:* `{$teacher['phone']}`\n";
        $cardText .= "📍 *কর্মএলাকা:* {$teacher['area_name']} ({$teacher['district_name']} জেলা)\n";
        $cardText .= "🎓 *যোগ্যতা:* {$teacher['qualification']}\n";
        $cardText .= "👥 *শিক্ষার্থী:* " . BengaliHelper::toBengaliNumber($teacher['total_students']) . " জন\n";
        $cardText .= "🏢 *জেলা পরিচালক:* {$teacher['director_name']}\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "⚡ *হযরত হুজুর / প্রধান অ্যাডমিনের সিদ্ধান্ত:*";

        $keyboard = [
            [
                ['text' => '✅ অনুমোদন (১ বছর মেয়াদ)', 'callback_data' => 'appv_tch_' . $teacherId],
                ['text' => '❌ বাতিল', 'callback_data' => 'canc_tch_' . $teacherId]
            ],
            [
                ['text' => '📞 শিক্ষকের সাথে কথা', 'url' => 'tel:' . $teacher['phone']],
                ['text' => '💬 হোয়াটসঅ্যাপ', 'url' => 'https://wa.me/88' . preg_replace('/[^0-9]/', '', $teacher['phone'])]
            ]
        ];

        self::sendTelegramCard(self::ADMIN_CHAT_ID, $cardText, $keyboard);

        return ['success' => true, 'teacher_id' => $teacherId];
    }

    /**
     * Route a District Director Requisition / Request
     */
    public static function routeDirectorRequest(int $requestId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT r.*, d.name as director_name, d.district_name, d.phone as director_phone
            FROM `director_requests` r
            LEFT JOIN `directors` d ON r.director_id = d.id
            WHERE r.id = ? LIMIT 1
        ");
        $stmt->execute([$requestId]);
        $req = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$req) {
            return ['success' => false, 'message' => 'আবেদনের তথ্য পাওয়া যায়নি।'];
        }

        $cardText = "📦 *জেলা পরিচালক রিকুইজিশন — অ্যাকশন কার্ড*\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "🏢 *পরিচালক:* {$req['director_name']} ({$req['district_name']} জেলা)\n";
        $cardText .= "📞 *মোবাইল:* `{$req['director_phone']}`\n";
        $cardText .= "📋 *ধরন:* " . strtoupper($req['request_type']) . "\n";
        $cardText .= "🔢 *পরিমাণ:* " . BengaliHelper::toBengaliNumber($req['quantity']) . " কপি\n";
        $cardText .= "📝 *বিবরণ:* {$req['details']}\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "⚡ *সিদ্ধান্ত নিন:*";

        $keyboard = [
            [
                ['text' => '✅ রিকুইজিশন অনুমোদন', 'callback_data' => 'appv_req_' . $requestId],
                ['text' => '❌ বাতিল', 'callback_data' => 'canc_req_' . $requestId]
            ],
            [
                ['text' => '📞 পরিচালকের সাথে কথা', 'url' => 'tel:' . $req['director_phone']]
            ]
        ];

        self::sendTelegramCard(self::ADMIN_CHAT_ID, $cardText, $keyboard);

        return ['success' => true, 'request_id' => $requestId];
    }

    /**
     * Route a Teacher Sabak Class / Ceremony Activity
     */
    public static function routeSabakActivity(int $activityId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            SELECT a.*, t.name as teacher_name, t.phone as teacher_phone, t.area_name, d.name as director_name, d.district_name
            FROM `teacher_activities` a
            LEFT JOIN `teachers` t ON a.teacher_id = t.id
            LEFT JOIN `directors` d ON a.director_id = d.id
            WHERE a.id = ? LIMIT 1
        ");
        $stmt->execute([$activityId]);
        $act = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$act) {
            return ['success' => false, 'message' => 'সবক ক্লাসের তথ্য পাওয়া যায়নি।'];
        }

        $cardText = "📖 *নতুন সবক সেশন / ক্লাস — অ্যাকশন কার্ড*\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "👤 *শিক্ষক:* {$act['teacher_name']} (`{$act['teacher_phone']}`)\n";
        $cardText .= "📍 *এলাকা:* {$act['area_name']} ({$act['district_name']} জেলা)\n";
        $cardText .= "📚 *ক্লাস শিরোনাম:* {$act['title']}\n";
        $cardText .= "👥 *শিক্ষার্থী সংখ্যা:* " . BengaliHelper::toBengaliNumber($act['quantity'] ?? 0) . " জন\n";
        $cardText .= "📝 *বিবরণ:* " . ($act['details'] ?? $act['description'] ?? '—') . "\n";
        $cardText .= "🏢 *পরিচালক:* {$act['director_name']}\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "⚡ *সিদ্ধান্ত নিন:*";

        $keyboard = [
            [
                ['text' => '✅ সবক সেশন অনুমোদন', 'callback_data' => 'appv_act_' . $activityId],
                ['text' => '❌ বাতিল', 'callback_data' => 'canc_act_' . $activityId]
            ],
            [
                ['text' => '📞 শিক্ষকের সাথে কথা', 'url' => 'tel:' . $act['teacher_phone']]
            ]
        ];

        self::sendTelegramCard(self::ADMIN_CHAT_ID, $cardText, $keyboard);

        return ['success' => true, 'activity_id' => $activityId];
    }

    /**
     * Route a KYC Verification Submission
     */
    public static function routeKycVerification(int $kycId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM `kyc_verifications` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$kycId]);
        $kyc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$kyc) {
            return ['success' => false, 'message' => 'কেওয়াইসি তথ্য পাওয়া যায়নি।'];
        }

        $cardText = "🪪 *নতুন কেওয়াইসি ভেরিফিকেশন — অ্যাকশন কার্ড*\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "🆔 *ইউজার টাইপ:* " . strtoupper($kyc['user_type']) . "\n";
        $cardText .= "📞 *মোবাইল:* `{$kyc['phone']}`\n";
        $cardText .= "🔢 *এনআইডি নং:* " . ($kyc['nid_number'] ?? '—') . "\n";
        $cardText .= "🛡️ *কেওয়াইসি লেভেল:* লেভেল " . $kyc['kyc_level'] . "\n";
        $cardText .= "🔗 *আইডি লিংক:* https://project.rasel.cloud/kariana/verify/{$kyc['uuid']}\n";
        $cardText .= "━━━━━━━━━━━━━━━━━━━━\n";
        $cardText .= "⚡ *সিদ্ধান্ত নিন:*";

        $keyboard = [
            [
                ['text' => '✅ কেওয়াইসি অনুমোদন', 'callback_data' => 'appv_kyc_' . $kycId],
                ['text' => '❌ বাতিল', 'callback_data' => 'canc_kyc_' . $kycId]
            ],
            [
                ['text' => '🔍 পাবলিক আইডি দেখুন', 'url' => "https://project.rasel.cloud/kariana/verify/{$kyc['uuid']}"]
            ]
        ];

        self::sendTelegramCard(self::ADMIN_CHAT_ID, $cardText, $keyboard);

        return ['success' => true, 'kyc_id' => $kycId];
    }
}
