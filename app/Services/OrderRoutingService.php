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
}
