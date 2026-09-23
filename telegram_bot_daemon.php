<?php
declare(strict_types=1);

/**
 * =========================================================================
 * Kariana Quran (কারিয়ানা কুরআন) — Official Telegram Bot Daemon
 * Bot: @karianaquranbot
 * Purely Dedicated to Kariana Quran Client Portal, Education & Order Routing
 * =========================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

$secretCreds = file_exists(__DIR__ . '/telegram_token.php') ? include(__DIR__ . '/telegram_token.php') : [];
define('BOT_TOKEN', $secretCreds['bot_token'] ?? '8830215516:AAHiW5FtCSfIOo5-xYMF0V2TfaMo1641bpE');
define('ADMIN_CHAT_ID', (string)($secretCreds['admin_chat_id'] ?? '1827362508'));
define('TG_API', 'https://api.telegram.org/bot' . BOT_TOKEN);

require_once __DIR__ . '/core/Autoloader.php';
\Core\Autoloader::register();

echo "====================================================\n";
echo "📖 Kariana Quran Official Client Bot Gateway Starting\n";
echo "Bot: @karianaquranbot | Admin: " . ADMIN_CHAT_ID . "\n";
echo "Dedicated: Islamic Educational Portal & Order Routing\n";
echo "====================================================\n";

function tgRequest(string $method, array $params = []): array {
    $ch = curl_init(TG_API . '/' . $method);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($params),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res ? (json_decode($res, true) ?? []) : [];
}

function answerCallback(string $callbackQueryId, string $text, bool $showAlert = false): void {
    tgRequest('answerCallbackQuery', [
        'callback_query_id' => $callbackQueryId,
        'text'              => $text,
        'show_alert'        => $showAlert,
    ]);
}

function editMessageReplyBadge(string $chatId, ?int $messageId, string $statusBadge): void {
    if (!$messageId) return;
    tgRequest('editMessageReplyMarkup', [
        'chat_id'      => $chatId,
        'message_id'   => $messageId,
        'reply_markup' => [
            'inline_keyboard' => [
                [['text' => $statusBadge, 'callback_data' => 'none']]
            ]
        ]
    ]);
}

function sendWelcomeMessage(string $chatId, string $firstName, string $refParam = ''): void {
    $text = "আসসালামু আলাইকুম, *{$firstName}*!\n\n";
    $text .= "📖 *কারিয়ানা কুরআন (Kariana Quran)* অফিসিয়াল সেন্টারে আপনাকে স্বাগতম।\n";
    $text .= "সহজ, বিশুদ্ধ ও আন্তর্জাতিক মানের কুরআন শিক্ষা এবং প্রকাশনা সেবা এখন আপনার আঙুলের ডগায়।\n\n";
    if (!empty($refParam)) {
        $text .= "🔗 _রেফারেল কোড সনাক্ত হয়েছে: `{$refParam}`_\n\n";
    }
    $text .= "নিচের অপশনগুলো থেকে প্রয়োজনীয় সেবা নির্বাচন করুন:";

    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text'    => '📖 কারিয়ানা অ্যাপ খুলুন (Web App)',
                    'web_app' => ['url' => 'https://project.rasel.cloud/kariana/']
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

    tgRequest('sendMessage', [
        'chat_id'      => $chatId,
        'text'         => $text,
        'parse_mode'   => 'Markdown',
        'reply_markup' => $keyboard,
    ]);
}

$db = \Core\Database::getInstance();
$offset = 0;

while (true) {
    $updates = tgRequest('getUpdates', [
        'offset'  => $offset,
        'timeout' => 20,
    ]);

    if (!empty($updates['ok']) && !empty($updates['result'])) {
        foreach ($updates['result'] as $up) {
            $offset = $up['update_id'] + 1;

            // 1. Callback Query Handler (Action Cards & Menus)
            if (isset($up['callback_query'])) {
                $cq = $up['callback_query'];
                $cqId = $cq['id'] ?? '';
                $data = $cq['data'] ?? '';
                $chatId = (string)($cq['message']['chat']['id'] ?? ADMIN_CHAT_ID);
                $messageId = $cq['message']['message_id'] ?? null;

                // Teacher Approval
                if (str_starts_with($data, 'appv_tch_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `teachers` SET `approval_status` = 'approved', `status` = 'active', `valid_until` = DATE_ADD(CURDATE(), INTERVAL 1 YEAR), `approved_by` = 'মাওলানা সাদ্দাম হোসেন (টেলিগ্রাম)', `approved_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'শিক্ষক সফলভাবে অনুমোদিত! ১ বছর মেয়াদ সক্রিয়। ✅');
                    editMessageReplyBadge($chatId, $messageId, '✅ [অনুমোদিত (১ বছর মেয়াদ সক্রিয়)]');
                } elseif (str_starts_with($data, 'canc_tch_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `teachers` SET `approval_status` = 'rejected', `status` = 'inactive' WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'শিক্ষকের আবেদন বাতিল করা হয়েছে ❌');
                    editMessageReplyBadge($chatId, $messageId, '❌ [শিক্ষক আবেদন বাতিল]');
                }
                // Book Orders
                elseif (str_starts_with($data, 'appv_bo_')) {
                    $id = (int)substr($data, 8);
                    $db->prepare("UPDATE `book_orders` SET `status` = 'routed_to_director', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'অর্ডার জেলা পরিচালকের কাছে অনুমোদিত! ✅');
                    editMessageReplyBadge($chatId, $messageId, '✅ [পরিচালক ডেলিভারি অনুমোদিত]');
                } elseif (str_starts_with($data, 'ship_bo_')) {
                    $id = (int)substr($data, 8);
                    $db->prepare("UPDATE `book_orders` SET `status` = 'central_courier', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[🚚 সেন্ট্রাল কুরিয়ার: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'সেন্ট্রাল কুরিয়ার চালান প্রস্তুত! 🚚');
                    editMessageReplyBadge($chatId, $messageId, '🚚 [সেন্ট্রাল কুরিয়ার চালান প্রস্তুত]');
                } elseif (str_starts_with($data, 'canc_bo_')) {
                    $id = (int)substr($data, 8);
                    $db->prepare("UPDATE `book_orders` SET `status` = 'cancelled', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'অর্ডার বাতিল করা হয়েছে ❌');
                    editMessageReplyBadge($chatId, $messageId, '❌ [অর্ডার বাতিল]');
                }
                // Admissions
                elseif (str_starts_with($data, 'appv_adm_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `admissions` SET `status` = 'enrolled', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[🎓 ভর্তি নিশ্চিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'ভর্তি নিশ্চিত ও এনরোল করা হয়েছে! 🎓');
                    editMessageReplyBadge($chatId, $messageId, '🎓 [ভর্তি নিশ্চিত / এনরোল্ড]');
                } elseif (str_starts_with($data, 'cont_adm_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `admissions` SET `status` = 'contacted', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[📞 যোগাযোগ সম্পন্ন: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'যোগাযোগ সম্পন্ন হিসেবে চিহ্নিত 📞');
                    editMessageReplyBadge($chatId, $messageId, '📞 [যোগাযোগ সম্পন্ন]');
                } elseif (str_starts_with($data, 'canc_adm_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `admissions` SET `status` = 'cancelled', `admin_notes` = CONCAT(COALESCE(admin_notes, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'ভর্তি আবেদন বাতিল করা হয়েছে ❌');
                    editMessageReplyBadge($chatId, $messageId, '❌ [ভর্তি আবেদন বাতিল]');
                }
                // Director Requests
                elseif (str_starts_with($data, 'appv_req_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `director_requests` SET `status` = 'approved', `admin_note` = CONCAT(COALESCE(admin_note, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'রিকুইজিশন সফলভাবে অনুমোদিত! ✅');
                    editMessageReplyBadge($chatId, $messageId, '✅ [রিকুইজিশন অনুমোদিত]');
                } elseif (str_starts_with($data, 'canc_req_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `director_requests` SET `status` = 'rejected', `admin_note` = CONCAT(COALESCE(admin_note, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'রিকুইজিশন বাতিল করা হয়েছে ❌');
                    editMessageReplyBadge($chatId, $messageId, '❌ [রিকুইজিশন বাতিল]');
                }
                // Sabak Activities
                elseif (str_starts_with($data, 'appv_act_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `teacher_activities` SET `status` = 'approved', `director_notes` = CONCAT(COALESCE(director_notes, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'সবক সেশন অনুমোদিত হয়েছে! ✅');
                    editMessageReplyBadge($chatId, $messageId, '✅ [সবক সেশন অনুমোদিত]');
                } elseif (str_starts_with($data, 'canc_act_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `teacher_activities` SET `status` = 'cancelled', `director_notes` = CONCAT(COALESCE(director_notes, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]'), `updated_at` = NOW() WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'সবক সেশন বাতিল করা হয়েছে ❌');
                    editMessageReplyBadge($chatId, $messageId, '❌ [সবক সেশন বাতিল]');
                }
                // KYC Verifications
                elseif (str_starts_with($data, 'appv_kyc_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `kyc_verifications` SET `kyc_status` = 'approved', `verified_at` = NOW(), `admin_remarks` = CONCAT(COALESCE(admin_remarks, ''), '\n[✅ অনুমোদিত: Telegram ID {$chatId}]') WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'কেওয়াইসি ভেরিফিকেশন অনুমোদিত! 🪪');
                    editMessageReplyBadge($chatId, $messageId, '✅ [কেওয়াইসি অনুমোদিত]');
                } elseif (str_starts_with($data, 'canc_kyc_')) {
                    $id = (int)substr($data, 9);
                    $db->prepare("UPDATE `kyc_verifications` SET `kyc_status` = 'rejected', `admin_remarks` = CONCAT(COALESCE(admin_remarks, ''), '\n[❌ বাতিল: Telegram ID {$chatId}]') WHERE `id` = ?")->execute([$id]);
                    answerCallback($cqId, 'কেওয়াইসি আবেদন বাতিল করা হয়েছে ❌');
                    editMessageReplyBadge($chatId, $messageId, '❌ [কেওয়াইসি বাতিল]');
                }
                // Menus
                elseif ($data === 'menu:books') {
                    tgRequest('sendMessage', [
                        'chat_id'    => $chatId,
                        'text'       => "📚 *কারিয়ানা কুরআন প্রকাশনা ও কিতাব তালিকা*\n\n১. কারিয়ানা কায়দা (১২ রঙের তাজবীদ সংকেতযুক্ত)\n২. কারিয়ানা আমপারা (প্রমিত উচ্চারণ ও ক্যালিগ্রাফি)\n৩. কারিয়ানা পূর্ণাঙ্গ কুরআনুল কারীম\n\nঅর্ডার করতে ভিজিট করুন: https://project.rasel.cloud/kariana/books",
                        'parse_mode' => 'Markdown',
                    ]);
                    answerCallback($cqId, 'কিতাব তালিকা পাঠানো হয়েছে');
                } elseif ($data === 'menu:courses') {
                    tgRequest('sendMessage', [
                        'chat_id'    => $chatId,
                        'text'       => "🎓 *কারিয়ানা কুরআন শিক্ষা কোর্সসমূহ*\n\n১. সহজ পদ্ধতিতে তাজবীদ শিক্ষা (৩০ দিন)\n২. মুয়াল্লিম ও শিক্ষক প্রশিক্ষণ কোর্স (সনদসহ)\n৩. আন্তর্জাতিক হিফজুল কুরআন প্রোগ্রাম\n\nভর্তি হতে ভিজিট করুন: https://project.rasel.cloud/kariana/courses",
                        'parse_mode' => 'Markdown',
                    ]);
                    answerCallback($cqId, 'কোর্স তালিকা পাঠানো হয়েছে');
                } elseif ($data === 'menu:prayer') {
                    tgRequest('sendMessage', [
                        'chat_id'    => $chatId,
                        'text'       => "🕌 *৬৪ জেলার নামাজের সময়সূচি*\n\nইসলামিক ফাউন্ডেশন বাংলাদেশের নির্ভুল সময়সূচি দেখতে ভিজিট করুন:\nhttps://project.rasel.cloud/kariana/prayer-times",
                        'parse_mode' => 'Markdown',
                    ]);
                    answerCallback($cqId, 'নামাজের সময়সূচি লিংক পাঠানো হয়েছে');
                } else {
                    answerCallback($cqId, 'অ্যাকশন গৃহীত হয়েছে।');
                }
                continue;
            }

            // 2. Incoming Messages
            if (isset($up['message'])) {
                $msg = $up['message'];
                $chatId = (string)($msg['chat']['id'] ?? '');
                $text = trim((string)($msg['text'] ?? ''));
                $from = $msg['from'] ?? [];
                $firstName = $from['first_name'] ?? 'সম্মানিত অতিথি';

                if (str_starts_with($text, '/start')) {
                    $parts = explode(' ', $text, 2);
                    $refParam = trim($parts[1] ?? '');
                    sendWelcomeMessage($chatId, $firstName, $refParam);
                } else {
                    $reply = "ধন্যবাদ *{$firstName}* ভাই!\n";
                    $reply .= "কারিয়ানা কুরআন ইসলামিক এডুকেশনাল পোর্টালে আপনাকে স্বাগতম। পোর্টালটি ব্রাউজ করতে নিচের বাটনে চাপ দিন:";
                    tgRequest('sendMessage', [
                        'chat_id'      => $chatId,
                        'text'         => $reply,
                        'parse_mode'   => 'Markdown',
                        'reply_markup' => [
                            'inline_keyboard' => [
                                [['text' => '📖 কারিয়ানা পোর্টাল খুলুন', 'web_app' => ['url' => 'https://project.rasel.cloud/kariana/']]]
                            ]
                        ]
                    ]);
                }
            }
        }
    }

    usleep(500000); // 0.5s pause
}
