<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;
use Core\Database;
use PDO;

/**
 * TelegramWebhookController
 * Handles incoming Telegram updates, deep-linked referral tracking (?start=_tgr_...),
 * interactive action card callback queries, and 24/7 assistant routing.
 */
class TelegramWebhookController
{
    private PDO $db;
    private string $botToken;
    private string $adminChatId;
    private string $apiBase;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $creds = file_exists(__DIR__ . '/../../telegram_token.php') ? include(__DIR__ . '/../../telegram_token.php') : [];
        $this->botToken = (string)($creds['bot_token'] ?? '8830215516:AAHiW5FtCSfIOo5-xYMF0V2TfaMo1641bpE');
        $this->adminChatId = (string)($creds['admin_chat_id'] ?? '1827362508');
        $this->apiBase = 'https://api.telegram.org/bot' . $this->botToken;
    }

    /**
     * Webhook Entry Point
     * URL: POST /api/telegram/webhook
     */
    public function handle(Request $request): Response
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return Response::json(['status' => 'empty_payload'], 400);
        }

        $update = json_decode($raw, true);
        if (!$update) {
            return Response::json(['status' => 'invalid_json'], 400);
        }

        // 1. Handle Callback Queries (Interactive Action Card buttons)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
            return Response::json(['status' => 'callback_handled'], 200);
        }

        // 2. Handle Normal Messages & /start Commands
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
            return Response::json(['status' => 'message_handled'], 200);
        }

        return Response::json(['status' => 'ignored'], 200);
    }

    /**
     * Handle incoming text messages & /start commands
     */
    private function handleMessage(array $msg): void
    {
        $chatId = (string)($msg['chat']['id'] ?? '');
        $text = trim((string)($msg['text'] ?? ''));
        $from = $msg['from'] ?? [];
        $fromId = (string)($from['id'] ?? $chatId);
        $firstName = $from['first_name'] ?? 'সম্মানিত অতিথি';
        $username = $from['username'] ?? '';

        // Record or update user in telegram_users table
        $this->syncTelegramUser($fromId, $firstName, $from['last_name'] ?? '', $username);

        // Check for /start command with referral parameter
        if (str_starts_with($text, '/start')) {
            $parts = explode(' ', $text, 2);
            $refParam = trim($parts[1] ?? '');

            if (!empty($refParam)) {
                // Record referral tracking
                $this->recordReferral($refParam, $fromId, $firstName);
            }

            $welcomeText = "আসসালামু আলাইকুম, *{$firstName}*!\n\n";
            $welcomeText .= "📖 *কারিয়ানা কুরআন (Kariana Quran)* অফিসিয়াল সেন্টারে আপনাকে স্বাগতম।\n";
            $welcomeText .= "সহজ, বিশুদ্ধ ও আন্তর্জাতিক মানের কুরআন শিক্ষা এবং প্রকাশনা সেবা এখন আপনার আঙুলের ডগায়।\n\n";
            if (!empty($refParam)) {
                $welcomeText .= "🔗 _রেফারেল কোড সনাক্ত হয়েছে: `{$refParam}`_\n\n";
            }
            $welcomeText .= "নিচের অপশনগুলো থেকে প্রয়োজনীয় সেবা নির্বাচন করুন:";

            $keyboard = [
                'inline_keyboard' => [
                    [
                        [
                            'text'     => '📖 কারিয়ানা অ্যাপ খুলুন (Web App)',
                            'web_app'  => ['url' => 'https://project.rasel.cloud/kariana/']
                        ]
                    ],
                    [
                        ['text' => '📚 কিতাব ও প্রকাশনা', 'callback_data' => 'menu:books'],
                        ['text' => '🎓 কুরআন শিক্ষা কোর্স', 'callback_data' => 'menu:courses']
                    ],
                    [
                        ['text' => '🪪 ভেরিফিকেশন পোর্টাল', 'web_app' => ['url' => 'https://project.rasel.cloud/kariana/verify/kyc']],
                        ['text' => '🕌 নামাজের সময়সূচি', 'callback_data' => 'menu:prayer']
                    ],
                    [
                        ['text' => '💬 সরাসরি WhatsApp সাপোর্ট', 'url' => 'https://wa.me/8801827362508']
                    ]
                ]
            ];

            $this->sendTelegramApi('sendMessage', [
                'chat_id'      => $chatId,
                'text'         => $welcomeText,
                'parse_mode'   => 'Markdown',
                'reply_markup' => json_encode($keyboard)
            ]);
            return;
        }

        // General Conversational Response (Warm Bengali Assistant)
        $replyText = "ধন্যবাদ *{$firstName}* ভাই!\n";
        $replyText .= "আপনার মেসেজটি কেন্দ্রীয় সার্ভারে সংরক্ষিত হয়েছে। কারিয়ানা কুরআন পোর্টাল ভিজিট করতে নিচের বাটনটি ব্যবহার করুন।";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📖 কারিয়ানা পোর্টাল খুলুন', 'web_app' => ['url' => 'https://project.rasel.cloud/kariana/']]
                ]
            ]
        ];

        $this->sendTelegramApi('sendMessage', [
            'chat_id'      => $chatId,
            'text'         => $replyText,
            'parse_mode'   => 'Markdown',
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Handle Interactive Action Card Callback Queries
     */
    private function handleCallbackQuery(array $cq): void
    {
        $cqId = $cq['id'] ?? '';
        $data = $cq['data'] ?? '';
        $chatId = (string)($cq['message']['chat']['id'] ?? $this->adminChatId);
        $messageId = $cq['message']['message_id'] ?? null;
        $toastText = 'অ্যাকশন সম্পন্ন হয়েছে!';

        // 1. Teacher Approval / Rejection Actions
        if (str_starts_with($data, 'appv_tch_') || str_starts_with($data, 'canc_tch_')) {
            $teacherId = (int)substr($data, 9);
            if (str_starts_with($data, 'appv_tch_')) {
                $this->db->prepare("
                    UPDATE `teachers` 
                    SET `approval_status` = 'approved',
                        `status` = 'active',
                        `valid_until` = DATE_ADD(CURDATE(), INTERVAL 1 YEAR),
                        `approved_by` = 'মাওলানা সাদ্দাম হোসেন (টেলিগ্রাম)',
                        `approved_at` = NOW()
                    WHERE `id` = ?
                ")->execute([$teacherId]);
                $tRow = $this->db->query("SELECT name FROM `teachers` WHERE `id` = {$teacherId}")->fetch(PDO::FETCH_ASSOC);
                $name = $tRow['name'] ?? "#{$teacherId}";
                $toastText = "✅ শিক্ষক {$name} সফলভাবে অনুমোদিত (১ বছর মেয়াদ সক্রিয়)!";
                $this->editMessageReplyMarkup($chatId, $messageId, "✅ [অনুমোদিত (১ বছর মেয়াদ সক্রিয়)]");
            } else {
                $this->db->prepare("UPDATE `teachers` SET `approval_status` = 'rejected', `status` = 'inactive' WHERE `id` = ?")->execute([$teacherId]);
                $toastText = "❌ শিক্ষক #{$teacherId} এর আবেদন বাতিল করা হয়েছে।";
                $this->editMessageReplyMarkup($chatId, $messageId, "❌ [শিক্ষক আবেদন বাতিল]");
            }
        }
        // 2. Book Order Actions
        elseif (str_starts_with($data, 'appv_bo_') || str_starts_with($data, 'ship_bo_') || str_starts_with($data, 'canc_bo_')) {
            $orderId = (int)substr($data, 8);
            if (str_starts_with($data, 'appv_bo_')) {
                $this->db->prepare("UPDATE `book_orders` SET `status` = 'routed_to_director', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$orderId]);
                $toastText = "✅ কিতাব অর্ডার #{$orderId} জেলা পরিচালকের কাছে অনুমোদিত!";
                $this->editMessageReplyMarkup($chatId, $messageId, "✅ [পরিচালক ডেলিভারি অনুমোদিত]");
            } elseif (str_starts_with($data, 'ship_bo_')) {
                $this->db->prepare("UPDATE `book_orders` SET `status` = 'central_courier', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[🚚 সেন্ট্রাল কুরিয়ার: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$orderId]);
                $toastText = "🚚 অর্ডার #{$orderId} সেন্ট্রাল কুরিয়ার চালানে যুক্ত হয়েছে!";
                $this->editMessageReplyMarkup($chatId, $messageId, "🚚 [সেন্ট্রাল কুরিয়ার চালান প্রস্তুত]");
            } else {
                $this->db->prepare("UPDATE `book_orders` SET `status` = 'cancelled', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$orderId]);
                $toastText = "❌ অর্ডার #{$orderId} বাতিল করা হয়েছে।";
                $this->editMessageReplyMarkup($chatId, $messageId, "❌ [অর্ডার বাতিল]");
            }
        }
        // 3. Admission Actions
        elseif (str_starts_with($data, 'appv_adm_') || str_starts_with($data, 'cont_adm_') || str_starts_with($data, 'canc_adm_')) {
            $admId = (int)substr($data, 9);
            if (str_starts_with($data, 'appv_adm_')) {
                $this->db->prepare("UPDATE `admissions` SET `status` = 'enrolled', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[🎓 ভর্তি নিশ্চিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$admId]);
                $toastText = "🎓 শিক্ষার্থী ভর্তি #{$admId} নিশ্চিত ও এনরোল করা হয়েছে!";
                $this->editMessageReplyMarkup($chatId, $messageId, "🎓 [ভর্তি নিশ্চিত / এনরোল্ড]");
            } elseif (str_starts_with($data, 'cont_adm_')) {
                $this->db->prepare("UPDATE `admissions` SET `status` = 'contacted', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[📞 যোগাযোগ সম্পন্ন: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$admId]);
                $toastText = "📞 শিক্ষার্থী #{$admId} এর সাথে যোগাযোগ সম্পন্ন হিসেবে চিহ্নিত।";
                $this->editMessageReplyMarkup($chatId, $messageId, "📞 [যোগাযোগ সম্পন্ন]");
            } else {
                $this->db->prepare("UPDATE `admissions` SET `status` = 'cancelled', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$admId]);
                $toastText = "❌ ভর্তি আবেদন #{$admId} বাতিল করা হয়েছে।";
                $this->editMessageReplyMarkup($chatId, $messageId, "❌ [ভর্তি আবেদন বাতিল]");
            }
        }
        // 4. Director Requisition Actions
        elseif (str_starts_with($data, 'appv_req_') || str_starts_with($data, 'canc_req_')) {
            $reqId = (int)substr($data, 9);
            if (str_starts_with($data, 'appv_req_')) {
                $this->db->prepare("UPDATE `director_requests` SET `status` = 'approved', `admin_note` = CONCAT(COALESCE(admin_note, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$reqId]);
                $toastText = "✅ জেলা পরিচালক রিকুইজিশন #{$reqId} সফলভাবে অনুমোদিত!";
                $this->editMessageReplyMarkup($chatId, $messageId, "✅ [রিকুইজিশন অনুমোদিত]");
            } else {
                $this->db->prepare("UPDATE `director_requests` SET `status` = 'rejected', `admin_note` = CONCAT(COALESCE(admin_note, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$reqId]);
                $toastText = "❌ রিকুইজিশন #{$reqId} বাতিল করা হয়েছে।";
                $this->editMessageReplyMarkup($chatId, $messageId, "❌ [রিকুইজিশন বাতিল]");
            }
        }
        // 5. Sabak Activity Actions
        elseif (str_starts_with($data, 'appv_act_') || str_starts_with($data, 'canc_act_')) {
            $actId = (int)substr($data, 9);
            if (str_starts_with($data, 'appv_act_')) {
                $this->db->prepare("UPDATE `teacher_activities` SET `status` = 'approved', `director_notes` = CONCAT(COALESCE(director_notes, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$actId]);
                $toastText = "✅ সবক সেশন #{$actId} সফলভাবে অনুমোদিত হয়েছে!";
                $this->editMessageReplyMarkup($chatId, $messageId, "✅ [সবক সেশন অনুমোদিত]");
            } else {
                $this->db->prepare("UPDATE `teacher_activities` SET `status` = 'cancelled', `director_notes` = CONCAT(COALESCE(director_notes, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$actId]);
                $toastText = "❌ সবক সেশন #{$actId} বাতিল করা হয়েছে।";
                $this->editMessageReplyMarkup($chatId, $messageId, "❌ [সবক সেশন বাতিল]");
            }
        }
        // 6. KYC Verification Actions
        elseif (str_starts_with($data, 'appv_kyc_') || str_starts_with($data, 'canc_kyc_')) {
            $kycId = (int)substr($data, 9);
            if (str_starts_with($data, 'appv_kyc_')) {
                $this->db->prepare("UPDATE `kyc_verifications` SET `kyc_status` = 'approved', `verified_at` = NOW(), `admin_remarks` = CONCAT(COALESCE(admin_remarks, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]') WHERE `id` = ?")->execute([$kycId]);
                $kStmt = $this->db->prepare("SELECT * FROM `kyc_verifications` WHERE `id` = ? LIMIT 1");
                $kStmt->execute([$kycId]);
                $kRow = $kStmt->fetch(PDO::FETCH_ASSOC);
                if ($kRow) {
                    $uType = $kRow['user_type'] ?? '';
                    $uId = (int)($kRow['user_id'] ?? 0);
                    $kUuid = $kRow['uuid'] ?? '';
                    if ($uType === 'teacher' && $uId > 0) {
                        $this->db->prepare("UPDATE `teachers` SET `kyc_level` = 2, `kyc_uuid` = ? WHERE `id` = ?")->execute([$kUuid, $uId]);
                    } elseif ($uType === 'director' && $uId > 0) {
                        $this->db->prepare("UPDATE `directors` SET `kyc_level` = 2, `kyc_uuid` = ? WHERE `id` = ?")->execute([$kUuid, $uId]);
                    } elseif ($uId > 0) {
                        $this->db->prepare("UPDATE `users` SET `kyc_level` = 2, `kyc_uuid` = ? WHERE `id` = ?")->execute([$kUuid, $uId]);
                    }
                }
                $toastText = "✅ কেওয়াইসি ভেরিফিকেশন #{$kycId} অনুমোদিত ও ডিজিটাল আইডি সক্রিয়!";
                $this->editMessageReplyMarkup($chatId, $messageId, "✅ [কেওয়াইসি অনুমোদিত ও ডিজিটাল আইডি সক্রিয়]");
            } else {
                $this->db->prepare("UPDATE `kyc_verifications` SET `kyc_status` = 'rejected', `admin_remarks` = CONCAT(COALESCE(admin_remarks, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]') WHERE `id` = ?")->execute([$kycId]);
                $toastText = "❌ কেওয়াইসি আবেদন #{$kycId} বাতিল করা হয়েছে।";
                $this->editMessageReplyMarkup($chatId, $messageId, "❌ [কেওয়াইসি বাতিল]");
            }
        }
        // 7. Colon-separated Legacy / Menu Callbacks
        else {
            $parts = explode(':', $data, 2);
            $action = $parts[0] ?? '';
            $param = $parts[1] ?? '';

            switch ($action) {
                case 'approve_sabak':
                    $this->db->prepare("UPDATE `teacher_activities` SET `status` = 'approved' WHERE `id` = ?")->execute([(int)$param]);
                    $toastText = "✅ সবক আবেদন #{$param} সফলভাবে অনুমোদিত হয়েছে!";
                    $this->editMessageReplyMarkup($chatId, $messageId, "✅ [সবক ক্লাস অনুমোদিত]");
                    break;

                case 'approve_admission':
                    $this->db->prepare("UPDATE `admissions` SET `status` = 'enrolled' WHERE `id` = ?")->execute([(int)$param]);
                    $toastText = "✅ শিক্ষার্থী ভর্তি আবেদন #{$param} অনুমোদিত হয়েছে!";
                    $this->editMessageReplyMarkup($chatId, $messageId, "✅ [ভর্তি অনুমোদিত ও জেলা বরাদ্দ]");
                    break;

                case 'route_to_director':
                    $this->db->prepare("UPDATE `book_orders` SET `status` = 'routed_to_director' WHERE `id` = ?")->execute([(int)$param]);
                    $toastText = "📤 কিতাব অর্ডার #{$param} জেলা পরিচালকের কাছে ট্রান্সফার করা হয়েছে।";
                    $this->editMessageReplyMarkup($chatId, $messageId, "📤 [জেলা পরিচালকের দায়িত্বে]");
                    break;

                case 'suspend_order':
                    $this->db->prepare("UPDATE `book_orders` SET `status` = 'cancelled' WHERE `id` = ?")->execute([(int)$param]);
                    $toastText = "🛑 সতর্কতা: অর্ডার #{$param} সাময়িকভাবে স্থগিত করা হয়েছে।";
                    $this->editMessageReplyMarkup($chatId, $messageId, "🛑 [অ্যাডমিন কর্তৃক স্থগিত]");
                    break;

                case 'central_fulfill':
                    $this->db->prepare("UPDATE `book_orders` SET `status` = 'central_courier' WHERE `id` = ?")->execute([(int)$param]);
                    $toastText = "📦 কিতাব অর্ডার #{$param} সেন্ট্রাল কুরিয়ারে কনফার্ম করা হয়েছে।";
                    $this->editMessageReplyMarkup($chatId, $messageId, "📦 [সেন্ট্রাল কুরিয়ার ডেলিভারি]");
                    break;

                case 'approve_comment':
                    $this->db->prepare("UPDATE `blog_comments` SET `status` = 'approved' WHERE `id` = ?")->execute([(int)$param]);
                    $toastText = "💬 ব্লগ মন্তব্য #{$param} অনুমোদিত ও লাইভ হয়েছে!";
                    $this->editMessageReplyMarkup($chatId, $messageId, "✅ [মন্তব্য প্রকাশিত]");
                    break;

                case 'menu':
                    if ($param === 'books') {
                        $this->sendTelegramApi('sendMessage', [
                            'chat_id' => $chatId,
                            'text'    => "📚 *কারিয়ানা কুরআন প্রকাশনা*\n\n১. কারিয়ানা কায়দা (১২ রঙের তাজবীদ)\n২. কারিয়ানা আমপারা (প্রমিত উচ্চারণ)\n৩. কারিয়ানা পূর্ণাঙ্গ কুরআন শরীফ\n\nঅর্ডার করতে ভিজিট করুন: https://project.rasel.cloud/kariana/books",
                            'parse_mode' => 'Markdown'
                        ]);
                        $toastText = 'বইয়ের তালিকা পাঠানো হয়েছে';
                    } elseif ($param === 'courses') {
                        $this->sendTelegramApi('sendMessage', [
                            'chat_id' => $chatId,
                            'text'    => "🎓 *কারিয়ানা কুরআন কোর্সসমূহ*\n\n১. সহজ পদ্ধতিতে তাজবীদ শিক্ষা (৩০ দিন)\n২. মুয়াল্লিম প্রশিক্ষণ কোর্স (সনদসহ)\n৩. আন্তর্জাতিক হিফজুল কুরআন প্রোগ্রাম\n\nভর্তি লিংক: https://project.rasel.cloud/kariana/courses",
                            'parse_mode' => 'Markdown'
                        ]);
                        $toastText = 'কোর্স তালিকা পাঠানো হয়েছে';
                    }
                    break;

                default:
                    $toastText = 'অ্যাকশন প্রসেস করা হয়েছে।';
                    break;
            }
        }

        // Answer callback query toast
        $this->sendTelegramApi('answerCallbackQuery', [
            'callback_query_id' => $cqId,
            'text'              => $toastText,
            'show_alert'        => false,
        ]);
    }

    /**
     * Send structured Category-Tagged Action Cards to Central Admin
     */
    public static function sendCategoryAlert(string $categoryTag, string $shortSummary, string $details = '', array $buttons = []): array
    {
        $creds = file_exists(__DIR__ . '/../../telegram_token.php') ? include(__DIR__ . '/../../telegram_token.php') : [];
        $token = (string)($creds['bot_token'] ?? '');
        $chatId = (string)($creds['admin_chat_id'] ?? '1827362508');

        if (empty($token)) return ['success' => false, 'message' => 'No bot token'];

        $text = "*{$categoryTag}*\n\n{$shortSummary}\n";
        if (!empty($details)) {
            $text .= "\n_{$details}_\n";
        }

        $payload = [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => 'Markdown'
        ];

        if (!empty($buttons)) {
            $payload['reply_markup'] = json_encode(['inline_keyboard' => $buttons]);
        }

        $ch = curl_init("https://api.telegram.org/bot{$token}/sendMessage");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return json_decode($res, true) ?? [];
    }

    private function editMessageReplyMarkup(string $chatId, ?int $messageId, string $statusBadge): void
    {
        if (!$messageId) return;
        $this->sendTelegramApi('editMessageReplyMarkup', [
            'chat_id'      => $chatId,
            'message_id'   => $messageId,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => $statusBadge, 'callback_data' => 'none']]
                ]
            ])
        ]);
    }

    private function syncTelegramUser(string $tgId, string $first, string $last, string $username): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO `telegram_users` 
                (`telegram_id`, `first_name`, `last_name`, `username`, `last_active_at`, `created_at`, `updated_at`)
                VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                `first_name` = VALUES(`first_name`),
                `last_name` = VALUES(`last_name`),
                `username` = VALUES(`username`),
                `last_active_at` = NOW()
            ");
            $stmt->execute([$tgId, $first, $last, $username]);
        } catch (\Throwable $e) {}
    }

    private function recordReferral(string $code, string $tgId, string $name): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO `telegram_referrals` 
                (`referrer_code`, `referred_telegram_id`, `referred_name`, `status`, `created_at`)
                VALUES (?, ?, ?, 'joined', NOW())
            ");
            $stmt->execute([$code, $tgId, $name]);
        } catch (\Throwable $e) {}
    }

    private function sendTelegramApi(string $method, array $params): array
    {
        $ch = curl_init("{$this->apiBase}/{$method}");
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
        ]);
        $res = curl_exec($ch);
        curl_close($ch);
        return json_decode($res, true) ?? [];
    }
}
