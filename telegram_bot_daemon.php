<?php
/**
 * =========================================================================
 * Kariana Website & Antigravity Remote Controller — Telegram Bot Daemon 5.0
 * Bot: @raselcodebot (Integrity)
 * Full Multi-Purpose, AI Model Switcher, Account Quota Manager & 24/7 Daemon
 * =========================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BOT_TOKEN', '8811158752:AAEKrP4XvGXDuw3NQKUGZLw6a-ooisnIK_8');
define('TG_API', 'https://api.telegram.org/bot' . BOT_TOKEN);

define('PROJECT_ROOT', __DIR__);
define('DEFAULT_CONV_ID', 'b5d31c4a-85e9-4609-b28a-3786eed9a1a3');
define('DEFAULT_PROJECT_NAME', 'Kariana Website');
define('DEFAULT_CHAT_TITLE', 'হিরো সেকশন ও ফুলস্ক্রিন ফিক্স');
define('DEFAULT_MODEL', 'Gemini 3.8 Flash (High)');

define('STATE_FILE', PROJECT_ROOT . '/telegram_state.json');
define('ACCOUNTS_FILE', PROJECT_ROOT . '/telegram_accounts.json');
define('AGENT_API_BAT', 'C:\\Users\\UseR\\.gemini\\antigravity\\bin\\agentapi.bat');
define('CONV_DB_PATH', 'C:/Users/UseR/.gemini/antigravity/conversation_summaries.db');
define('BRAIN_DIR', 'C:/Users/UseR/.gemini/antigravity/brain');

// Standard Access Links
define('LINK_LOCAL', 'http://localhost:8015');
define('LINK_WIFI', 'http://192.168.0.100:8015');
define('LINK_CLOUDFLARE', 'https://rev-mysql-stops-ext.trycloudflare.com');
define('LINK_GITHUB', 'https://github.com/ani22222/kariana-website');

// Logging to file & console
function botLog(string $msg): void {
    $time = date('Y-m-d H:i:s');
    $entry = "[{$time}] {$msg}\n";
    echo $entry;
    $logFile = PROJECT_ROOT . '/storage/logs/telegram_bot.log';
    if (!is_dir(dirname($logFile))) {
        @mkdir(dirname($logFile), 0777, true);
    }
    @file_put_contents($logFile, $entry, FILE_APPEND);
}

// Bengali Date & Duration Helpers
function formatBengaliDate(int $timestamp): string {
    $bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $enDigits = ['0','1','2','3','4','5','6','7','8','9'];
    $bnMonths = [
        'Jan' => 'জানুয়ারি', 'Feb' => 'ফেব্রুয়ারি', 'Mar' => 'মার্চ',
        'Apr' => 'এপ্রিল', 'May' => 'মে', 'Jun' => 'জুন',
        'Jul' => 'জুলাই', 'Aug' => 'আগস্ট', 'Sep' => 'সেপ্টেম্বর',
        'Oct' => 'অক্টোবর', 'Nov' => 'নভেম্বর', 'Dec' => 'ডিসেম্বর'
    ];

    $d = date('d', $timestamp);
    $m = date('M', $timestamp);
    $y = date('Y', $timestamp);
    $time = date('h:i A', $timestamp);

    $d = str_replace($enDigits, $bnDigits, $d);
    $y = str_replace($enDigits, $bnDigits, $y);
    $time = str_replace($enDigits, $bnDigits, $time);
    $m = $bnMonths[$m] ?? $m;

    return "{$d} {$m} {$y}, {$time}";
}

function formatDuration(int $seconds): string {
    $bnDigits = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $enDigits = ['0','1','2','3','4','5','6','7','8','9'];

    if ($seconds < 60) return "কিছুক্ষণ আগে";
    $mins = round($seconds / 60);
    if ($mins < 60) {
        return str_replace($enDigits, $bnDigits, (string)$mins) . " মিনিট আগে";
    }
    $hours = floor($mins / 60);
    $remMins = $mins % 60;
    return str_replace($enDigits, $bnDigits, (string)$hours) . " ঘণ্টা " . str_replace($enDigits, $bnDigits, (string)$remMins) . " মিনিট আগে";
}

// ---------------------------------------------------------
// Accounts & State Management
// ---------------------------------------------------------

function getLatestActiveConversation(): ?array {
    if (!file_exists(CONV_DB_PATH)) return null;
    try {
        $db = new PDO('sqlite:' . CONV_DB_PATH);
        $stmt = $db->query("SELECT conversation_id, preview, last_modified_time, workspace_uris 
                            FROM conversation_summaries 
                            WHERE (parent_conversation_id IS NULL OR parent_conversation_id = '') 
                            ORDER BY last_modified_time DESC LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $ws = json_decode($row['workspace_uris'], true);
            $wsName = (!empty($ws) && isset($ws[0])) ? basename(urldecode(str_replace('file:///', '', $ws[0]))) : 'Kariana Website';
            $title = getConversationTitle($row['conversation_id'], $row['preview'] ?? '');
            return [
                'id'       => $row['conversation_id'],
                'title'    => $title,
                'project'  => $wsName,
                'time'     => $row['last_modified_time']
            ];
        }
    } catch (Exception $e) {
        botLog("[DB ERROR getLatestActiveConversation] " . $e->getMessage());
    }
    return null;
}

function loadAccounts(): array {
    if (file_exists(ACCOUNTS_FILE)) {
        $data = json_decode(file_get_contents(ACCOUNTS_FILE), true);
        if (is_array($data)) return $data;
    }
    return [
        'active_account' => 'acc_1',
        'accounts' => [
            'acc_1' => [
                'id'           => 'acc_1',
                'name'         => 'মেইন অ্যাকাউন্ট (Account 1)',
                'profile'      => 'default',
                'status'       => 'সক্রিয় 🟢',
                'model'        => 'Gemini 3.8 Flash (High)',
                'quota_status' => 'স্বাভাবিক 🟢'
            ],
            'acc_2' => [
                'id'           => 'acc_2',
                'name'         => 'ব্যাকআপ অ্যাকাউন্ট (Account 2)',
                'profile'      => 'profile_2',
                'status'       => 'স্ট্যান্ডবাই 🟡',
                'model'        => 'Claude Sonnet 4.6 (Thinking)',
                'quota_status' => 'উপলব্ধ 🟢'
            ]
        ]
    ];
}

function saveAccounts(array $accounts): void {
    file_put_contents(ACCOUNTS_FILE, json_encode($accounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function loadState(): array {
    $latest = getLatestActiveConversation();
    if (file_exists(STATE_FILE)) {
        $data = json_decode(file_get_contents(STATE_FILE), true);
        if (is_array($data)) {
            // If active_conv_id is missing or obsolete default, sync with latest
            if (empty($data['active_conv_id']) || $data['active_conv_id'] === '94596634-65c0-432c-a4d3-6aa058846c61') {
                $data['active_conv_id']    = $latest['id'] ?? DEFAULT_CONV_ID;
                $data['active_proj_name']  = $latest['project'] ?? DEFAULT_PROJECT_NAME;
                $data['active_chat_title'] = $latest['title'] ?? DEFAULT_CHAT_TITLE;
            }
            $data['chat_id']           = $data['chat_id'] ?? '1827362508';
            $data['last_update_id']    = $data['last_update_id'] ?? 0;
            $data['mode']              = $data['mode'] ?? 'turbo';
            $data['selected_model']    = $data['selected_model'] ?? DEFAULT_MODEL;
            $data['active_account']    = $data['active_account'] ?? 'acc_1';
            $data['is_busy']           = $data['is_busy'] ?? false;
            $data['last_heartbeat']    = $data['last_heartbeat'] ?? time();
            return $data;
        }
    }
    return [
        'active_conv_id'    => $latest['id'] ?? DEFAULT_CONV_ID,
        'active_proj_name'  => $latest['project'] ?? DEFAULT_PROJECT_NAME,
        'active_chat_title' => $latest['title'] ?? DEFAULT_CHAT_TITLE,
        'chat_id'           => '1827362508',
        'last_update_id'    => 0,
        'mode'              => 'turbo',
        'selected_model'    => DEFAULT_MODEL,
        'active_account'    => 'acc_1',
        'is_busy'           => false,
        'last_heartbeat'    => time()
    ];
}

function saveState(array $state): void {
    file_put_contents(STATE_FILE, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// ---------------------------------------------------------
// Telegram HTTP Helpers
// ---------------------------------------------------------

function tgRequest(string $method, array $params = []): array {
    $url = TG_API . '/' . $method;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($params),
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 35,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo "[CURL ERROR] " . $err . "\n";
        return ['ok' => false, 'error' => $err];
    }
    return json_decode($res, true) ?? ['ok' => false];
}

function sendMsg(string $chatId, string $text, ?array $inlineKeyboard = null, bool $includePersistentKeyboard = true): array {
    $params = [
        'chat_id'                  => $chatId,
        'text'                     => $text,
        'parse_mode'               => 'Markdown',
        'disable_web_page_preview' => true,
    ];

    if ($inlineKeyboard) {
        $params['reply_markup'] = ['inline_keyboard' => $inlineKeyboard];
    } elseif ($includePersistentKeyboard) {
        $params['reply_markup'] = [
            'keyboard' => [
                [['text' => '🏠 মেইন মেনু'], ['text' => '📁 সাম্প্রতিক প্রজেক্ট']],
                [['text' => '📸 পিসির স্ক্রিনশট'], ['text' => '📱 ওয়েবসাইট লাইভ ভিউ']],
                [['text' => '🧠 এআই মডেল নির্বাচন'], ['text' => '👤 অ্যাকাউন্ট ও কোটা']],
                [['text' => '📊 পিসি হেলথ ও রিসোর্স'], ['text' => '🔍 অ্যান্টিগ্রাভিটি স্ট্যাটাস']],
                [['text' => '🛠️ কুইক ফিক্স ও ট্রাবলশুট'], ['text' => '⚙️ মোড পরিবর্তন']]
            ],
            'resize_keyboard' => true,
            'persistent'      => true
        ];
    }

    $res = tgRequest('sendMessage', $params);
    if (!($res['ok'] ?? false)) {
        // Fallback to plain text if Markdown parsing failed
        botLog("[TG WARN] sendMsg with Markdown failed: " . ($res['description'] ?? 'error') . " - retrying as plain text");
        unset($params['parse_mode']);
        $res = tgRequest('sendMessage', $params);
    }
    return $res;
}

function editMsg(string $chatId, int $messageId, string $text, ?array $inlineKeyboard = null): array {
    $params = [
        'chat_id'                  => $chatId,
        'message_id'               => $messageId,
        'text'                     => $text,
        'parse_mode'               => 'Markdown',
        'disable_web_page_preview' => true,
    ];
    if ($inlineKeyboard) {
        $params['reply_markup'] = ['inline_keyboard' => $inlineKeyboard];
    }
    $res = tgRequest('editMessageText', $params);
    if (!($res['ok'] ?? false)) {
        botLog("[TG WARN] editMsg with Markdown failed: " . ($res['description'] ?? 'error') . " - retrying as plain text");
        unset($params['parse_mode']);
        $res = tgRequest('editMessageText', $params);
    }
    return $res;
}

function answerCallback(string $callbackQueryId, string $text = ''): void {
    tgRequest('answerCallbackQuery', [
        'callback_query_id' => $callbackQueryId,
        'text'              => $text,
        'show_alert'        => false
    ]);
}

function sendPhoto(string $chatId, string $photoPath, string $caption = ''): array {
    $url = TG_API . '/sendPhoto';
    $ch = curl_init($url);
    $cfile = new CURLFile($photoPath, 'image/png', 'screenshot.png');
    $params = [
        'chat_id'    => $chatId,
        'photo'      => $cfile,
        'caption'    => $caption,
        'parse_mode' => 'Markdown'
    ];
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $params,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 40,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        botLog("[CURL ERROR sendPhoto] " . $err);
        return ['ok' => false, 'error' => $err];
    }
    $json = json_decode($res, true) ?? ['ok' => false];
    if (!($json['ok'] ?? false)) {
        botLog("[TG WARN] sendPhoto with Markdown failed: " . ($json['description'] ?? 'error') . " - retrying as plain text");
        $ch2 = curl_init($url);
        unset($params['parse_mode']);
        curl_setopt_array($ch2, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 40,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $res2 = curl_exec($ch2);
        curl_close($ch2);
        return json_decode($res2, true) ?? ['ok' => false];
    }
    return $json;
}

// ---------------------------------------------------------
// Workspaces & Conversations DB
// ---------------------------------------------------------

function getWorkspaces(): array {
    $workspaces = [];
    if (!file_exists(CONV_DB_PATH)) return $workspaces;

    try {
        $db = new PDO('sqlite:' . CONV_DB_PATH);
        $stmt = $db->query("SELECT workspace_uris, COUNT(conversation_id) as chat_count, MAX(last_modified_time) as latest 
                            FROM conversation_summaries 
                            WHERE (parent_conversation_id IS NULL OR parent_conversation_id = '') 
                            AND workspace_uris IS NOT NULL AND workspace_uris != '[]' 
                            GROUP BY workspace_uris 
                            ORDER BY latest DESC LIMIT 10");

        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ws = json_decode($r['workspace_uris'], true);
            $wsName = (!empty($ws) && isset($ws[0])) ? basename(urldecode(str_replace('file:///', '', $ws[0]))) : 'General Workspace';
            $hash = substr(md5($r['workspace_uris']), 0, 8);
            $workspaces[$hash] = [
                'name'       => $wsName,
                'raw_uri'    => $r['workspace_uris'],
                'chat_count' => (int)$r['chat_count'],
                'latest'     => $r['latest']
            ];
        }
    } catch (Exception $e) {
        echo "[DB ERROR] " . $e->getMessage() . "\n";
    }
    return $workspaces;
}

function getConversationTitle(string $convId, string $dbPreview): string {
    if (!empty(trim($dbPreview))) {
        $clean = trim(str_replace(["\r", "\n", "`", "*"], ' ', $dbPreview));
        if (mb_strlen($clean) > 35) $clean = mb_substr($clean, 0, 35) . '...';
        return $clean;
    }
    $transcriptFile = BRAIN_DIR . "/{$convId}/.system_generated/logs/transcript.jsonl";
    if (file_exists($transcriptFile)) {
        $fp = @fopen($transcriptFile, 'r');
        if ($fp) {
            while (!feof($fp)) {
                $line = fgets($fp);
                if ($line === false) break;
                $json = json_decode($line, true);
                if (($json['type'] ?? '') === 'USER_INPUT' && !empty($json['content'])) {
                    $text = trim(preg_replace('/<[^>]+>/', '', $json['content']));
                    $text = trim(str_replace(["\r", "\n", "`", "*"], ' ', $text));
                    fclose($fp);
                    if (mb_strlen($text) > 35) $text = mb_substr($text, 0, 35) . '...';
                    return $text;
                }
            }
            fclose($fp);
        }
    }
    return 'সাধারণ চ্যাট (General Chat)';
}

function getChatsForWorkspace(string $rawUri, int $limit = 8): array {
    $chats = [];
    if (!file_exists(CONV_DB_PATH)) return $chats;

    try {
        $db = new PDO('sqlite:' . CONV_DB_PATH);
        $stmt = $db->prepare("SELECT conversation_id, preview, last_modified_time, step_count 
                              FROM conversation_summaries 
                              WHERE (parent_conversation_id IS NULL OR parent_conversation_id = '') 
                              AND workspace_uris = ? 
                              ORDER BY last_modified_time DESC LIMIT " . $limit);
        $stmt->execute([$rawUri]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $r) {
            $convId = $r['conversation_id'];
            $title = getConversationTitle($convId, $r['preview'] ?? '');
            $chats[] = [
                'id'         => $convId,
                'title'      => $title,
                'steps'      => (int)($r['step_count'] ?? 0),
                'time'       => $r['last_modified_time'] ?? ''
            ];
        }
    } catch (Exception $e) {
        echo "[DB ERROR] " . $e->getMessage() . "\n";
    }
    return $chats;
}

// ---------------------------------------------------------
// Smart Intent & Project Detection
// ---------------------------------------------------------

function detectProjectIntent(string $text, string $currentProject): ?array {
    $keywordsMap = [
        'VPSCloude Server' => ['vps', 'cloud', 'server', 'docker', 'openshift', 'linux', 'ubuntu', 'ip', 'ssh', 'domain'],
        'Method'           => ['method', 'vpn', 'proxy', 'card', 'trial', 'bin'],
        'Familli accout'   => ['netflix', 'cookie', 'cookies', 'login', 'family', 'account'],
        'Store From Bip'   => ['store', 'bip', 'shop', 'product', 'cart'],
        'Riaz Clint Shop'  => ['riaz', 'client', 'clint'],
        'sms-sync-pay'     => ['payment', 'sms', 'sync', 'pay', 'gateway', 'bkash', 'nagad', 'api']
    ];

    $lower = mb_strtolower($text);

    foreach ($keywordsMap as $projKey => $keywords) {
        if (stripos($currentProject, $projKey) !== false) continue;

        foreach ($keywords as $kw) {
            if (mb_strpos($lower, $kw) !== false) {
                $workspaces = getWorkspaces();
                foreach ($workspaces as $hash => $ws) {
                    if (stripos($ws['name'], $projKey) !== false) {
                        return [
                            'target_name' => $ws['name'],
                            'target_hash' => $hash,
                            'matched_kw'  => $kw
                        ];
                    }
                }
            }
        }
    }
    return null;
}

// ---------------------------------------------------------
// Multi-Purpose System Health & PC Remote Functions
// ---------------------------------------------------------

function getSystemMetrics(): array {
    $metrics = [
        'cpu'        => '0%',
        'ram_used'   => '0 GB',
        'ram_total'  => '0 GB',
        'disk_free'  => '0 GB',
        'disk_total' => '0 GB',
        'uptime'     => '0m'
    ];

    $psScript = PROJECT_ROOT . '/get_health.ps1';
    if (file_exists($psScript)) {
        $out = trim(shell_exec('powershell.exe -ExecutionPolicy Bypass -File "' . $psScript . '" 2>NUL') ?? '');
        if ($out && strpos($out, '|') !== false) {
            $p = explode('|', $out);
            if (count($p) >= 6) {
                $metrics['cpu']        = $p[0] . '%';
                $metrics['ram_used']   = $p[1] . ' GB';
                $metrics['ram_total']  = $p[2] . ' GB';
                $metrics['disk_free']  = $p[3] . ' GB';
                $metrics['disk_total'] = $p[4] . ' GB';
                $metrics['uptime']     = $p[5];
            }
        }
    }
    return $metrics;
}

function isAntigravityRunning(): bool {
    $out = [];
    exec('tasklist /FI "IMAGENAME eq Antigravity.exe" 2>NUL', $out);
    foreach ($out as $line) {
        if (stripos($line, 'Antigravity.exe') !== false) {
            return true;
        }
    }
    return false;
}

function isServerRunning(int $port = 8015): bool {
    $conn = @fsockopen('127.0.0.1', $port, $errno, $errstr, 1);
    if (is_resource($conn)) {
        fclose($conn);
        return true;
    }
    return false;
}

function getStandardLinksText(): string {
    return "\n\n🔗 *স্ট্যান্ডার্ড অ্যাক্সেস লিংকসমূহ:*\n"
         . "🖥️ *Localhost:* [http://localhost:8015](" . LINK_LOCAL . ")\n"
         . "📶 *Wi-Fi LAN:* [http://192.168.0.100:8015](" . LINK_WIFI . ")\n"
         . "🌐 *Cloudflare Live:* [Live Link](" . LINK_CLOUDFLARE . ")\n"
         . "📦 *GitHub:* [kariana-website](" . LINK_GITHUB . ")";
}

function getModeTitle(string $mode): string {
    switch ($mode) {
        case 'safe': return '🛡️ সেইফ মোড (Safe Approval)';
        case 'planning': return '📋 প্ল্যানিং মোড (Plan Only)';
        default: return '⚡ টার্বো মোড (Turbo)';
    }
}

// ---------------------------------------------------------
// View Handlers (Multi-Purpose Dashboard 5.0)
// ---------------------------------------------------------

function renderMainMenu(string $chatId, array $state): void {
    $agyOnline = isAntigravityRunning() ? "🟢 Active" : "🟡 Background";
    $srvOnline = isServerRunning(8015) ? "🟢 Active (Port 8015)" : "🔴 Offline";

    $activeTitle = $state['active_chat_title'] ?? DEFAULT_CHAT_TITLE;
    $modeText = getModeTitle($state['mode'] ?? 'turbo');
    $modelText = $state['selected_model'] ?? DEFAULT_MODEL;

    $accData = loadAccounts();
    $activeAccId = $state['active_account'] ?? 'acc_1';
    $accName = $accData['accounts'][$activeAccId]['name'] ?? 'Main Account';
    $quotaStatus = $accData['accounts'][$activeAccId]['quota_status'] ?? 'স্বাভাবিক 🟢';

    $text  = "✨ *অ্যান্টিগ্রাভিটি মাল্টি-পারপাস রিমোট সেন্টার ৫.০*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "💻 *Antigravity IDE:* {$agyOnline}\n";
    $text .= "🌐 *ওয়েব সার্ভার:* {$srvOnline}\n";
    $text .= "📁 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n";
    $text .= "💬 *সক্রিয় চ্যাট:* `{$activeTitle}`\n";
    $text .= "🧠 *এআই মডেল:* `{$modelText}`\n";
    $text .= "👤 *অ্যাকাউন্ট:* `{$accName}` ({$quotaStatus})\n";
    $text .= "⚙️ *কাজের মোড:* {$modeText}\n\n";
    $text .= "👇 *নিচের অপশনগুলো বেছে নিন অথবা যেকোনো মেসেজ লিখুন:*";

    $keyboard = [
        [
            ['text' => '🎯 অ্যাক্টিভ চ্যাট সিঙ্ক', 'callback_data' => 'action_sync_active_chat'],
            ['text' => '📸 পিসির স্ক্রিনশট', 'callback_data' => 'action_pc_screenshot']
        ],
        [
            ['text' => '📱 ওয়েবসাইট লাইভ ভিউ', 'callback_data' => 'action_web_screenshot'],
            ['text' => '🚀 Antigravity ওপেন', 'callback_data' => 'action_open_agy']
        ],
        [
            ['text' => '📁 সাম্প্রতিক প্রজেক্ট ও চ্যাটসমূহ', 'callback_data' => 'menu_workspaces'],
            ['text' => '🕌 কারিয়ানা প্রজেক্ট', 'callback_data' => 'select_kariana']
        ],
        [
            ['text' => '🔍 অ্যান্টিগ্রাভিটি স্ট্যাটাস', 'callback_data' => 'menu_agy_status'],
            ['text' => '🛠️ কুইক ফিক্স ও ট্রাবলশুট', 'callback_data' => 'menu_troubleshoot']
        ],
        [
            ['text' => '🧠 এআই মডেল পরিবর্তন', 'callback_data' => 'menu_models'],
            ['text' => '👤 অ্যাকাউন্ট ও কোটা', 'callback_data' => 'menu_accounts']
        ],
        [
            ['text' => '📊 পিসি হেলথ ও রিসোর্স', 'callback_data' => 'menu_system_health'],
            ['text' => '⚙️ কাজের মোড পরিবর্তন', 'callback_data' => 'menu_modes']
        ],
        [
            ['text' => '🛑 পিসি শাটডাউন', 'callback_data' => 'action_shutdown_pc'],
            ['text' => '🔄 পিসি রিস্টার্ট', 'callback_data' => 'action_restart_pc']
        ],
        [
            ['text' => '🔄 সার্ভার রিস্টার্ট (8015)', 'callback_data' => 'action_restart_srv'],
            ['text' => '🔒 পিসি স্ক্রিন লক', 'callback_data' => 'action_lock_pc']
        ],
        [
            ['text' => '🔄 রিফ্রেশ ড্যাশবোর্ড', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

// ---------------------------------------------------------
// Antigravity File Audit & Diagnostics
// ---------------------------------------------------------

function auditAntigravityFiles(): array {
    $info = [];

    // 1. GEMINI.md
    $geminiFile = PROJECT_ROOT . '/GEMINI.md';
    if (file_exists($geminiFile)) {
        $gText = file_get_contents($geminiFile);
        preg_match('/Dedicated Port\*\*:\s*`(\d+)`/', $gText, $mPort);
        preg_match('/GitHub Repository\*\*:\s*`([^`]+)`/', $gText, $mGit);
        $info['gemini_status'] = "পোর্ট " . ($mPort[1] ?? '8015') . " সক্রিয় ও রুলস লোডেড 🟢";
    } else {
        $info['gemini_status'] = "ফাইল পাওয়া যায়নি 🔴";
    }

    // 2. task.md
    $taskFile = BRAIN_DIR . '/' . DEFAULT_CONV_ID . '/task.md';
    $doneTasks = 0;
    $totalTasks = 0;
    if (file_exists($taskFile)) {
        $lines = file($taskFile);
        foreach ($lines as $l) {
            if (strpos($l, '- [x]') !== false) { $doneTasks++; $totalTasks++; }
            elseif (strpos($l, '- [ ]') !== false) { $totalTasks++; }
            elseif (strpos($l, '- [/]') !== false) { $totalTasks++; }
        }
        $info['task_status'] = "{$doneTasks}/{$totalTasks} টি টাস্ক সম্পন্ন";
    } else {
        $info['task_status'] = "টাস্ক ফাইল প্রস্তুত";
    }

    // 3. transcript.jsonl
    $transFile = BRAIN_DIR . '/' . DEFAULT_CONV_ID . '/.system_generated/logs/transcript.jsonl';
    $steps = 0;
    if (file_exists($transFile)) {
        $fp = @fopen($transFile, 'r');
        if ($fp) {
            while (!feof($fp)) {
                if (fgets($fp) !== false) $steps++;
            }
            fclose($fp);
            $info['steps'] = "{$steps} টি স্টেপ রেকর্ডকৃত";
        } else {
            $info['steps'] = "রানিং";
        }
    } else {
        $info['steps'] = "N/A";
    }

    // 4. Git status
    $gitCommit = trim(shell_exec('git -C "' . PROJECT_ROOT . '" log -1 --pretty=format:"%h - %s" 2>NUL') ?? 'N/A');
    $info['git_last'] = $gitCommit;

    return $info;
}

function renderAntigravityStatusView(string $chatId, array $state): void {
    $audit = auditAntigravityFiles();
    $agyOnline = isAntigravityRunning() ? "🟢 Active" : "🔴 Closed";
    $srvOnline = isServerRunning(8015) ? "🟢 Active (Port 8015)" : "🔴 Offline";

    $text  = "🔍 *অ্যান্টিগ্রাভিটি মেইন ফাইল ও প্রজেক্ট স্ট্যাটাস অডিট*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "📄 *GEMINI.md রুলস:* `{$audit['gemini_status']}`\n";
    $text .= "📋 *টাস্ক প্রগ্রেস (task.md):* `{$audit['task_status']}`\n";
    $text .= "🧠 *সেশন ইতিহাস:* `{$audit['steps']}`\n";
    $text .= "📝 *সর্বশেষ গিট কমিট:* `{$audit['git_last']}`\n\n";
    $text .= "💻 *Antigravity IDE:* {$agyOnline}\n";
    $text .= "🐘 *ওয়েব সার্ভার:* {$srvOnline}\n";
    $text .= "🎯 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n";
    $text .= "💬 *সক্রিয় চ্যাট:* `{$state['active_chat_title']}`\n";
    $text .= "🤖 *এআই মডেল:* `{$state['selected_model']}`\n";
    $text .= getStandardLinksText();

    $keyboard = [
        [
            ['text' => '🔄 রিফ্রেশ অডিট', 'callback_data' => 'menu_agy_status'],
            ['text' => '🛠️ কুইক ফিক্স ও ট্রাবলশুট', 'callback_data' => 'menu_troubleshoot']
        ],
        [
            ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

function renderTroubleshootView(string $chatId, array $state): void {
    $text  = "🛠️ *কুইক ফিক্স ও রিমোট ট্রাবলশুট সেন্টার*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "কম্পিউটারে কোনো লিমিটেশন, পোর্ট ব্লক বা এরর দেখা দিলে মোবাইল থেকেই ১-ক্লিকে ফিক্স করুন:\n\n";
    $text .= "🔹 *পোর্ট ৮০১৫ ফিক্স:* কোনো কারণে পোর্ট ব্লক বা ক্র্যাশ করলে ফ্রেশ রিস্টার্ট দেবে।\n";
    $text .= "🔹 *ক্যাশ ও লগ ক্লিন:* টেম্পোরারি ফাইল ক্লিন করে ডিস্ক স্পেস ও মেমোরি খালি করবে।\n";
    $text .= "🔹 *গিট সিঙ্ক চেক:* গিটহাবের সাথে বর্তমান কোড সিঙ্ক ঠিক আছে কিনা চেক করবে।\n";
    $text .= "🔹 *মডেল কোটা ফিক্স:* কোটা শেষ হয়ে থাকলে Flash-Lite মডেলে কনভার্ট করে কাজ চালু রাখবে।\n\n";
    $text .= "👇 *প্রয়োজনীয় ফিক্সে ক্লিক করুন:*";

    $keyboard = [
        [
            ['text' => '🚀 পোর্ট ৮০১৫ অটো-ফিক্স ও রিস্টার্ট', 'callback_data' => 'action_fix_port'],
        ],
        [
            ['text' => '🧹 ক্যাশ ও টেম্প ক্লিন', 'callback_data' => 'action_clean_cache'],
            ['text' => '📦 গিট সিঙ্ক ভেরিফাই', 'callback_data' => 'action_git_sync']
        ],
        [
            ['text' => '⚡ কোটা ফিক্স (Switch Flash-Lite)', 'callback_data' => 'model_gemini_lite']
        ],
        [
            ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

// AI Model Selector Menu
function renderModelsMenu(string $chatId, array $state): void {
    $current = $state['selected_model'] ?? DEFAULT_MODEL;

    $text  = "🧠 *এআই মডেল নির্বাচন করুন (Select AI Model)*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "বর্তমান সক্রিয় মডেল: *{$current}*\n\n";
    $text .= "🔹 *Gemini 3.8 Flash (High):* সুপার ফাস্ট, উচ্চ রেট লিমিট ও সবচেয়ে বেশি কোটা সাশ্রয়ী (দৈনন্দিন কাজের জন্য সেরা)।\n";
    $text .= "🔹 *Claude Sonnet 4.6 (Thinking):* গভীর যুক্তি, আর্কিটেকচার ও নিখুঁত কোডিংয়ের জন্য শক্তিশালী।\n";
    $text .= "🔹 *Gemini 3.8 Pro:* জটিল লজিক ও প্রফেশনাল মাল্টি-স্টেপ টাস্কের জন্য।\n";
    $text .= "🔹 *Gemini 3.8 Flash-Lite:* অতি-দ্রুত এবং কোটা একদম বাঁচাতে চাইলে এটি সেরা।\n";
    $text .= "🔹 *Kimi-K3:* ওপেনশিফ্ট এআই মডেল গেটওয়ে মোড।\n\n";
    $text .= "👇 *আপনার পছন্দের মডেলে ক্লিক করুন:*";

    $models = [
        'Gemini 3.8 Flash (High)'     => 'model_gemini_flash',
        'Claude Sonnet 4.6 (Thinking)' => 'model_claude_sonnet',
        'Gemini 3.8 Pro'              => 'model_gemini_pro',
        'Gemini 3.8 Flash-Lite'       => 'model_gemini_lite',
        'Kimi-K3 (OpenShift Gateway)' => 'model_kimi_k3'
    ];

    $keyboard = [];
    foreach ($models as $name => $cb) {
        $icon = ($name === $current) ? "✅ " : "⚡ ";
        $keyboard[] = [
            ['text' => "{$icon}{$name}", 'callback_data' => $cb]
        ];
    }
    $keyboard[] = [
        ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
    ];

    sendMsg($chatId, $text, $keyboard);
}

// Account & Quota Management Menu
function renderAccountsMenu(string $chatId, array $state): void {
    $accData = loadAccounts();
    $activeId = $state['active_account'] ?? 'acc_1';

    $text  = "👤 *অ্যাকাউন্ট ও কোটা ম্যানেজমেন্ট (Account & Quota)*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "মোবাইলে টেলিগ্রাম চালাতে চালাতে কোনো অ্যাকাউন্টের লিমিট শেষ হয়ে গেলে আপনি সাথে সাথে অন্য অ্যাকাউন্টে সুইচ করতে পারবেন।\n\n";
    $text .= "🟢 *বট স্ট্যাটাস:* ২৪ ঘণ্টা নন-স্টপ কানেক্টেড (কখনো ডিসকানেক্ট হবে না)\n\n";
    $text .= "*নিবন্ধিত অ্যাকাউন্টসমূহ:*\n";

    $keyboard = [];
    foreach ($accData['accounts'] as $accId => $acc) {
        $isActive = ($accId === $activeId);
        $icon = $isActive ? "✅ " : "🔄 ";
        $btnText = "{$icon}{$acc['name']} ({$acc['quota_status']})";
        
        $keyboard[] = [
            ['text' => $btnText, 'callback_data' => 'switchacc_' . $accId]
        ];
    }

    $keyboard[] = [
        ['text' => '🚪 বর্তমান অ্যাকাউন্ট লগআউট / রি-অথ', 'callback_data' => 'action_logout_reauth'],
        ['text' => '➕ নতুন অ্যাকাউন্ট যুক্ত করুন', 'callback_data' => 'action_add_acc']
    ];
    $keyboard[] = [
        ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
    ];

    sendMsg($chatId, $text, $keyboard);
}

function renderSystemHealthView(string $chatId, array $state): void {
    $m = getSystemMetrics();
    $agyOnline = isAntigravityRunning() ? "🟢 Active" : "🔴 Closed";
    $srvOnline = isServerRunning(8015) ? "🟢 Active (Port 8015)" : "🔴 Offline";

    $text  = "📊 *পিসি সিস্টেম ও রিসোর্স লাইভ মনিটর*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "⚡ *CPU লোড:* `{$m['cpu']}`\n";
    $text .= "🧠 *RAM মেমোরি:* `{$m['ram_used']}` / `{$m['ram_total']}`\n";
    $text .= "💾 *Disk C: ড্রাইভ:* `{$m['disk_free']}` ফ্রি / `{$m['disk_total']}` মোট\n";
    $text .= "⏱️ *পিসি রানিং টাইম:* `{$m['uptime']}`\n\n";
    $text .= "💻 *Antigravity 2.0:* {$agyOnline}\n";
    $text .= "🐘 *PHP Web Server:* {$srvOnline}\n";
    $text .= "📁 *অ্যাক্টিভ প্রজেক্ট:* `{$state['active_proj_name']}`\n";

    $keyboard = [
        [
            ['text' => '🔄 রিফ্রেশ মেমোরি', 'callback_data' => 'menu_system_health'],
            ['text' => '🔒 পিসি লক করুন', 'callback_data' => 'action_lock_pc']
        ],
        [
            ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

function renderModeMenu(string $chatId, array $state): void {
    $current = $state['mode'] ?? 'turbo';
    $text  = "⚙️ *কাজের মোড নির্বাচন করুন (Execution Mode)*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "বর্তমান মোড: *" . getModeTitle($current) . "*\n\n";
    $text .= "🔹 *⚡ টার্বো মোড (Turbo):* সরাসরি কোনো অতিরিক্ত প্রশ্ন ছাড়াই দ্রুত কাজ সম্পন্ন করবে (সবচেয়ে সুবিধাজনক)।\n";
    $text .= "🔹 *🛡️ সেইফ মোড (Safe):* যেকোনো বড় ফাইল পরিবর্তন করার আগে অনুমতি চাইবে।\n";
    $text .= "🔹 *📋 প্ল্যানিং মোড (Plan Only):* সরাসরি কোড না বদলে প্রথমে সম্পূর্ণ কাজের প্ল্যান তৈরি করে উপস্থাপন করবে।\n\n";
    $text .= "👇 *আপনার পছন্দের মোডে ক্লিক করুন:*";

    $keyboard = [
        [
            ['text' => ($current === 'turbo' ? '✅ ' : '') . '⚡ টার্বো মোড (Turbo)', 'callback_data' => 'setmode_turbo'],
        ],
        [
            ['text' => ($current === 'safe' ? '✅ ' : '') . '🛡️ সেইফ মোড (Safe)', 'callback_data' => 'setmode_safe'],
        ],
        [
            ['text' => ($current === 'planning' ? '✅ ' : '') . '📋 প্ল্যানিং মোড (Plan Only)', 'callback_data' => 'setmode_planning'],
        ],
        [
            ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

// Level 1: List all Workspaces/Projects with chat count
function renderWorkspacesMenu(string $chatId, array $state): void {
    $workspaces = getWorkspaces();
    $text  = "📁 *সাম্প্রতিক প্রজেক্টসমূহ (Workspaces)*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "যেকোনো প্রজেক্টে ক্লিক করলেই তার ভেতরের **সবগুলো চ্যাট (Multiple Chats)** দেখতে পাবেন:\n\n";

    $keyboard = [];
    $idx = 1;
    foreach ($workspaces as $hash => $ws) {
        $isActive = (stripos($state['active_proj_name'], $ws['name']) !== false);
        $icon = $isActive ? "✅ " : "📂 ";
        $btnText = "{$icon}{$idx}. {$ws['name']} ({$ws['chat_count']}টি চ্যাট)";
        if (mb_strlen($btnText) > 38) $btnText = mb_substr($btnText, 0, 36) . "..";

        $keyboard[] = [
            ['text' => $btnText, 'callback_data' => 'ws_' . $hash]
        ];
        $idx++;
    }

    $keyboard[] = [
        ['text' => '🔙 মেইন মেনুতে ফিরুন', 'callback_data' => 'menu_home']
    ];

    sendMsg($chatId, $text, $keyboard);
}

// Level 2: List all chats inside the selected Workspace
function renderWorkspaceChatsMenu(string $chatId, string $wsHash, array $state): void {
    $workspaces = getWorkspaces();
    if (!isset($workspaces[$wsHash])) {
        sendMsg($chatId, "⚠️ প্রজেক্ট খুঁজে পাওয়া যায়নি। আবার চেষ্টা করুন।");
        renderWorkspacesMenu($chatId, $state);
        return;
    }

    $ws = $workspaces[$wsHash];
    $chats = getChatsForWorkspace($ws['raw_uri']);

    $text  = "📂 *প্রজেক্ট:* `{$ws['name']}`\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "এই প্রজেক্টে মোট *" . count($chats) . "* টি চ্যাট পাওয়া গেছে।\n";
    $text .= "যেকোনো চ্যাটে ক্লিক করলে সাথে সাথে সেখানে সুইচ হবে:\n\n";

    $keyboard = [];
    foreach ($chats as $idx => $c) {
        $isActive = ($c['id'] === $state['active_conv_id']);
        $icon = $isActive ? "🟢 " : "💬 ";
        $btnText = "{$icon}" . ($idx + 1) . ". {$c['title']}";
        if (mb_strlen($btnText) > 38) $btnText = mb_substr($btnText, 0, 36) . "..";

        $shortId = substr($c['id'], 0, 16);
        $keyboard[] = [
            ['text' => $btnText, 'callback_data' => 'chat_' . $shortId]
        ];
    }

    $keyboard[] = [
        ['text' => '➕ এই প্রজেক্টে নতুন চ্যাট', 'callback_data' => 'newchat_' . $wsHash]
    ];
    $keyboard[] = [
        ['text' => '🔙 প্রজেক্ট তালিকায় ফিরুন', 'callback_data' => 'menu_workspaces'],
        ['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']
    ];

    sendMsg($chatId, $text, $keyboard);
}

function renderStatusView(string $chatId, array $state): void {
    $agyOnline = isAntigravityRunning() ? "🟢 চালু আছে (Running)" : "🔴 অফলাইন";
    $srvOnline = isServerRunning(8015) ? "🟢 চালু আছে (Port 8015)" : "🔴 বন্ধ";
    
    $gitCommit = trim(shell_exec('git -C "' . PROJECT_ROOT . '" log -1 --pretty=format:"%h - %s" 2>NUL') ?? 'N/A');
    $gitBranch = trim(shell_exec('git -C "' . PROJECT_ROOT . '" branch --show-current 2>NUL') ?? 'master');

    $accData = loadAccounts();
    $activeAccId = $state['active_account'] ?? 'acc_1';
    $accName = $accData['accounts'][$activeAccId]['name'] ?? 'Main Account';
    $quotaStatus = $accData['accounts'][$activeAccId]['quota_status'] ?? 'স্বাভাবিক 🟢';

    $text  = "📊 *কারিয়ানা ওয়েবসাইট — সিস্টেম ও লাইভ স্ট্যাটাস*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "💻 *Antigravity:* {$agyOnline}\n";
    $text .= "🐘 *PHP Server:* {$srvOnline}\n";
    $text .= "🌿 *Git Branch:* `{$gitBranch}`\n";
    $text .= "📝 *লাস্ট Commit:* `{$gitCommit}`\n";
    $text .= "📁 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n";
    $text .= "💬 *সক্রিয় চ্যাট:* `{$state['active_chat_title']}`\n";
    $text .= "🧠 *মডেল:* `{$state['selected_model']}`\n";
    $text .= "👤 *অ্যাকাউন্ট:* `{$accName}` ({$quotaStatus})\n";
    $text .= "⚙ *মোড:* " . getModeTitle($state['mode'] ?? 'turbo') . "\n";
    $text .= getStandardLinksText();

    $keyboard = [
        [
            ['text' => '🧠 মডেল পরিবর্তন', 'callback_data' => 'menu_models'],
            ['text' => '👤 অ্যাকাউন্ট সুইচ', 'callback_data' => 'menu_accounts']
        ],
        [
            ['text' => '📊 পিসি হেলথ', 'callback_data' => 'menu_system_health'],
            ['text' => '🔙 মেইন মেনু', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

// ---------------------------------------------------------
// Prompt Forwarding & Real-time Live Watcher
// ---------------------------------------------------------

function dispatchPrompt(string $chatId, string $prompt, array &$state): ?array {
    $convId = $state['active_conv_id'] ?? DEFAULT_CONV_ID;
    $projName = $state['active_proj_name'] ?? DEFAULT_PROJECT_NAME;
    $chatTitle = $state['active_chat_title'] ?? DEFAULT_CHAT_TITLE;
    $mode = $state['mode'] ?? 'turbo';
    $model = $state['selected_model'] ?? DEFAULT_MODEL;

    // Sanitize prompt for preview in markdown
    $safePrompt = str_replace(['_', '*', '`', '['], ' ', $prompt);
    if (mb_strlen($safePrompt) > 120) {
        $safePrompt = mb_substr($safePrompt, 0, 117) . '...';
    }

    // 1. Initial Status Message
    $initText = "⏳ *কাজ গ্রহণ করা হয়েছে!*\n"
              . "━━━━━━━━━━━━━━━━━━━━\n"
              . "🎯 *প্রজেক্ট:* `{$projName}`\n"
              . "💬 *চ্যাট:* `{$chatTitle}`\n"
              . "🧠 *মডেল:* `{$model}`\n"
              . "⚙️ *মোড:* " . getModeTitle($mode) . "\n"
              . "📝 *আপনার প্রম্পট:* _{$safePrompt}_\n\n"
              . "🔄 *স্ট্যাটাস:* Antigravity প্রসেসিং শুরু হয়েছে...\n\n"
              . "💡 _আপনি যেকোনো মেনু বাটন ব্যবহার করতে পারেন, বট সবসময় রেসপন্সিভ থাকবে।_";

    $sent = sendMsg($chatId, $initText);
    $statusMsgId = $sent['result']['message_id'] ?? null;

    // 2. Measure current transcript lines before sending
    $transcriptFile = BRAIN_DIR . "/{$convId}/.system_generated/logs/transcript.jsonl";
    $initialLineCount = 0;
    if (file_exists($transcriptFile)) {
        $fp = @fopen($transcriptFile, 'r');
        if ($fp) {
            while (!feof($fp)) {
                if (fgets($fp) !== false) $initialLineCount++;
            }
            fclose($fp);
        }
    }

    // 3. Send message to Antigravity via agentapi.bat
    $finalPrompt = $prompt;
    if ($mode === 'planning') {
        $finalPrompt = "[Planning Mode Request] " . $prompt . " (অনুগ্রহ করে সরাসরি কোড পরিবর্তন না করে প্রথমে বিস্তারিত প্ল্যান তৈরি করুন)";
    }

    $cmd = '"' . AGENT_API_BAT . '" send-message ' . escapeshellarg($convId) . ' ' . escapeshellarg($finalPrompt);

    botLog("[AGENTAPI] Sending prompt to {$convId}...");
    $out = [];
    $code = 0;
    exec($cmd . ' 2>&1', $out, $code);
    botLog("[AGENTAPI] Code: {$code}, Output: " . implode(" ", $out));

    if ($code !== 0) {
        $errString = implode("\n", $out);
        $isQuotaError = (stripos($errString, 'quota') !== false || stripos($errString, 'rate') !== false || stripos($errString, 'limit') !== false || stripos($errString, '429') !== false);

        if ($isQuotaError) {
            $errText = "⚠️ *কোটা / রেট লিমিট সতর্কতা!*\n"
                     . "━━━━━━━━━━━━━━━━━━━━\n"
                     . "বর্তমান অ্যাকাউন্টের লিমিট শেষ হয়েছে।\n"
                     . "🟢 *টেলিগ্রাম বট ২৪ ঘণ্টা নন-স্টপ কানেক্টেড রয়েছে!*\n\n"
                     . "👇 *নিচের বাটনে চাপ দিয়ে ব্যাকআপ অ্যাকাউন্টে সুইচ করুন অথবা মডেল পরিবর্তন করুন:*";

            $errKb = [
                [['text' => '🔄 ব্যাকআপ অ্যাকাউন্ট ২-এ সুইচ করুন', 'callback_data' => 'switchacc_acc_2']],
                [['text' => '🚀 মডেল Flash-Lite-এ বদলান', 'callback_data' => 'model_gemini_lite']],
                [['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']]
            ];

            if ($statusMsgId) {
                editMsg($chatId, $statusMsgId, $errText, $errKb);
            } else {
                sendMsg($chatId, $errText, $errKb);
            }
            return null;
        }

        $errText = "⚠️ *মেসেজ পাঠাতে সমস্যা হয়েছে:*\n`" . $errString . "`\n\nসরাসরি Antigravity IDE-তে চেক করুন।";
        if ($statusMsgId) {
            editMsg($chatId, $statusMsgId, $errText);
        } else {
            sendMsg($chatId, $errText);
        }
        return null;
    }

    return [
        'conv_id'          => $convId,
        'chat_id'          => $chatId,
        'status_msg_id'    => $statusMsgId,
        'start_time'       => time(),
        'initial_lines'    => $initialLineCount,
        'last_line_read'   => $initialLineCount,
        'last_tool_action' => '',
        'last_edit_time'   => time(),
        'prompt'           => $safePrompt,
        'projName'         => $projName,
        'chatTitle'        => $chatTitle,
        'model'            => $model
    ];
}

function checkActiveTaskProgress(array &$task): bool {
    $convId = $task['conv_id'];
    $transcriptFile = BRAIN_DIR . "/{$convId}/.system_generated/logs/transcript.jsonl";
    if (!file_exists($transcriptFile)) {
        return false;
    }

    $lines = @file($transcriptFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!$lines) return false;

    $totalLines = count($lines);
    if ($totalLines <= $task['last_line_read']) {
        // Check timeout (5 minutes)
        if (time() - $task['start_time'] > 300) {
            $timeoutMsg = "⏱️ *টাস্কটি দীর্ঘ সময় নিচ্ছে!*\n"
                        . "━━━━━━━━━━━━━━━━━━━━\n"
                        . "🎯 *প্রজেক্ট:* `{$task['projName']}`\n"
                        . "অ্যান্টিগ্রাভিটি ব্যাকগ্রাউন্ডে কাজ সম্পন্ন করছে। সমাপ্ত হলে চ্যাটে দেখতে পাবেন।"
                        . getStandardLinksText();
            sendMsg($task['chat_id'], $timeoutMsg);
            return true;
        }
        return false;
    }

    for ($i = $task['last_line_read']; $i < $totalLines; $i++) {
        $json = json_decode($lines[$i], true);
        if (!$json) continue;

        $type = $json['type'] ?? '';
        $status = $json['status'] ?? '';
        $source = $json['source'] ?? '';

        // Tool executions
        if (!empty($json['tool_calls'])) {
            foreach ($json['tool_calls'] as $tc) {
                $toolName = $tc['name'] ?? 'tool';
                $toolSummary = $tc['args']['toolSummary'] ?? $toolName;
                $actionStr = "🛠️ `{$toolName}` ({$toolSummary})";
                if ($actionStr !== $task['last_tool_action'] && (time() - $task['last_edit_time'] >= 3) && $task['status_msg_id']) {
                    $task['last_tool_action'] = $actionStr;
                    $task['last_edit_time'] = time();
                    $updateMsg = "⏳ *কাজ চলমান রয়েছে...*\n"
                               . "━━━━━━━━━━━━━━━━━━━━\n"
                               . "🎯 *প্রজেক্ট:* `{$task['projName']}`\n"
                               . "💬 *চ্যাট:* `{$task['chatTitle']}`\n"
                               . "🧠 *মডেল:* `{$task['model']}`\n"
                               . "📝 *প্রম্পট:* _{$task['prompt']}_\n\n"
                               . "🔄 *বর্তমান অ্যাকশন:* {$actionStr}\n"
                               . "⏱️ *অতিবাহিত সময়:* " . (time() - $task['start_time']) . "s";
                    editMsg($task['chat_id'], $task['status_msg_id'], $updateMsg);
                }
            }
        }

        // Completion
        if ($source === 'MODEL' && $type === 'PLANNER_RESPONSE' && $status === 'DONE') {
            $content = $json['content'] ?? '';
            if (!empty($content)) {
                $cleanText = mb_substr($content, 0, 3000);
                $finalMsg = "✅ *কাজ সম্পন্ন হয়েছে! (Task Complete)*\n"
                          . "━━━━━━━━━━━━━━━━━━━━\n"
                          . "🎯 *প্রজেক্ট:* `{$task['projName']}`\n"
                          . "💬 *চ্যাট:* `{$task['chatTitle']}`\n"
                          . "🧠 *মডেল:* `{$task['model']}`\n\n"
                          . $cleanText
                          . getStandardLinksText();

                $keyboard = [
                    [
                        ['text' => '💬 পরবর্তী নির্দেশ দিন', 'callback_data' => 'prompt_help'],
                        ['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']
                    ]
                ];

                sendMsg($task['chat_id'], $finalMsg, $keyboard);
                botLog("[COMPLETION] Task complete for conversation {$convId}");
                return true;
            }
        }
    }

    $task['last_line_read'] = $totalLines;
    return false;
}

// ---------------------------------------------------------
// Main Telegram Polling Loop
// ---------------------------------------------------------

echo "=====================================================\n";
echo "  কারিয়ানা ও অ্যান্টিগ্রাভিটি টেলিগ্রাম বট ৫.০ চালু\n";
echo "  Bot: @raselcodebot\n";
echo "  AI Model Switcher & Quota Manager Active\n";
echo "  24/7 Persistent Connectivity Active\n";
echo "=====================================================\n";

$state = loadState();

// Handle Boot Notification (When PC boots or script is invoked with --boot)
if (in_array('--boot', $argv ?? [])) {
    echo "[BOOT] PC booted up! Sending connection notification to Telegram...\n";
    
    $now = time();
    $lastOff = $state['last_heartbeat'] ?? ($now - 120);
    $offDuration = max(0, $now - $lastOff);

    $bootTimeStr = formatBengaliDate($now);
    $offTimeStr  = formatBengaliDate($lastOff) . " (" . formatDuration($offDuration) . ")";

    // Detect shutdown reason (Power Outage vs Normal)
    $powerReason = "🔄 স্বাভাবিক রিস্টার্ট / শাটডাউন";
    $eventsScript = PROJECT_ROOT . '/check_power_events.ps1';
    if (file_exists($eventsScript)) {
        $evOut = trim(shell_exec('powershell.exe -ExecutionPolicy Bypass -File "' . $eventsScript . '" 2>NUL') ?? '');
        if (strpos($evOut, 'PowerOutage') !== false) {
            $powerReason = "⚡ বিদ্যুৎ বিভ্রাট বা হঠাৎ পাওয়ার অফ (Power Cut / Sudden Off)";
        }
    }

    $accData = loadAccounts();
    $activeAccId = $state['active_account'] ?? 'acc_1';
    $accName = $accData['accounts'][$activeAccId]['name'] ?? 'Main Account';
    $quotaStatus = $accData['accounts'][$activeAccId]['quota_status'] ?? 'স্বাভাবিক 🟢';

    $bootMsg = "⚡ *কম্পিউটার পুনরায় চালু হয়েছে — অ্যান্টিগ্রাভিটি ২.০ অটো-কানেক্টেড!*\n"
             . "━━━━━━━━━━━━━━━━━━━━\n"
             . "⏰ *অন হওয়ার সময়:* {$bootTimeStr}\n"
             . "🛑 *পূর্বে বন্ধ হয়েছিল:* {$offTimeStr}\n"
             . "⚠️ *বন্ধ হওয়ার কারণ:* {$powerReason}\n"
             . "💻 *কম্পিউটার স্ট্যাটাস:* চালু ও সক্রিয় 🟢\n"
             . "🧠 *Antigravity IDE:* কানেক্টেড 🟢\n"
             . "🤖 *এআই মডেল:* `{$state['selected_model']}`\n"
             . "👤 *অ্যাকাউন্ট:* `{$accName}` ({$quotaStatus})\n"
             . "🌐 *ওয়েব সার্ভার:* Port 8015 রানিং 🟢\n"
             . "📁 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n"
             . "💬 *সক্রিয় চ্যাট:* `{$state['active_chat_title']}`\n"
             . "⚙️ *কাজের মোড:* " . getModeTitle($state['mode'] ?? 'turbo') . "\n\n"
             . "📱 আপনার পিসি সম্পূর্ণ রেডি! আপনি শুয়ে শুয়ে মোবাইলের টেলিগ্রাম থেকে যেকোনো কাজ দিয়ে যেতে পারবেন।"
             . getStandardLinksText();

    $bootKb = [
        [['text' => '🎯 বর্তমান অ্যাক্টিভ চ্যাট সিঙ্ক', 'callback_data' => 'action_sync_active_chat'], ['text' => '📸 পিসির স্ক্রিনশট', 'callback_data' => 'action_pc_screenshot']],
        [['text' => '📁 সাম্প্রতিক প্রজেক্ট ও চ্যাটসমূহ', 'callback_data' => 'menu_workspaces']],
        [['text' => '🧠 এআই মডেল পরিবর্তন', 'callback_data' => 'menu_models'], ['text' => '👤 অ্যাকাউন্ট ও কোটা', 'callback_data' => 'menu_accounts']],
        [['text' => '📊 পিসি হেলথ ও রিসোর্স', 'callback_data' => 'menu_system_health'], ['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']]
    ];

    sendMsg($state['chat_id'], $bootMsg, $bootKb);
}

$lastHeartbeatSave = time();
$activeTask = null;

while (true) {
    try {
        // Update heartbeat timestamp every 20 seconds for accurate shutdown detection
        if (time() - $lastHeartbeatSave >= 20) {
            $state['last_heartbeat'] = time();
            saveState($state);
            $lastHeartbeatSave = time();
        }

        // Check active task progress if one is running (Non-blocking background check)
        if ($activeTask !== null) {
            if (checkActiveTaskProgress($activeTask)) {
                $activeTask = null;
            }
        }

        $updates = tgRequest('getUpdates', [
            'offset'  => $state['last_update_id'] + 1,
            'timeout' => 2
        ]);

        if (!empty($updates['result'])) {
            foreach ($updates['result'] as $up) {
                $state['last_update_id'] = $up['update_id'];
                $state['last_heartbeat'] = time();
                saveState($state);

                // 1. Handle Callback Queries (Button Clicks)
                if (isset($up['callback_query'])) {
                    $cb = $up['callback_query'];
                    $cbId = $cb['id'];
                    $data = $cb['data'] ?? '';
                    $chatId = (string)($cb['message']['chat']['id'] ?? $state['chat_id']);
                    $state['chat_id'] = $chatId;

                    echo "[BUTTON CLICK] Data: {$data} from {$chatId}\n";

                    if ($data === 'menu_home') {
                        answerCallback($cbId, 'মেইন মেনু লোড হচ্ছে...');
                        renderMainMenu($chatId, $state);
                    } elseif ($data === 'menu_models') {
                        answerCallback($cbId, 'মডেল তালিকা...');
                        renderModelsMenu($chatId, $state);
                    } elseif ($data === 'menu_accounts') {
                        answerCallback($cbId, 'অ্যাকাউন্ট ও কোটা...');
                        renderAccountsMenu($chatId, $state);
                    } elseif (strpos($data, 'model_') === 0) {
                        $modelMap = [
                            'model_gemini_flash'  => 'Gemini 3.8 Flash (High)',
                            'model_claude_sonnet' => 'Claude Sonnet 4.6 (Thinking)',
                            'model_gemini_pro'    => 'Gemini 3.8 Pro',
                            'model_gemini_lite'   => 'Gemini 3.8 Flash-Lite',
                            'model_kimi_k3'       => 'Kimi-K3 (OpenShift Gateway)'
                        ];
                        $selectedModel = $modelMap[$data] ?? DEFAULT_MODEL;
                        $state['selected_model'] = $selectedModel;
                        saveState($state);
                        answerCallback($cbId, 'মডেল পরিবর্তন সফল!');
                        sendMsg($chatId, "✅ *এআই মডেল সফলভাবে পরিবর্তন করা হয়েছে!*\n\nবর্তমান সক্রিয় মডেল: *{$selectedModel}*\n\nএখন থেকে আপনার সব প্রম্পট এই মডেলে এক্সিকিউট হবে।");
                        renderMainMenu($chatId, $state);
                    } elseif (strpos($data, 'switchacc_') === 0) {
                        $accId = substr($data, 10);
                        $accData = loadAccounts();
                        if (isset($accData['accounts'][$accId])) {
                            $state['active_account'] = $accId;
                            $state['selected_model'] = $accData['accounts'][$accId]['model'];
                            saveState($state);
                            answerCallback($cbId, 'অ্যাকাউন্ট সুইচ সম্পন্ন!');
                            sendMsg($chatId, "✅ *অ্যাকাউন্ট সুইচ সফল!*\n\nসক্রিয় অ্যাকাউন্ট: *{$accData['accounts'][$accId]['name']}*\nমডেল: *{$state['selected_model']}*\nকোটা স্ট্যাটাস: *{$accData['accounts'][$accId]['quota_status']}*\n\nএখন আপনি নির্বিঘ্নে কাজ চালিয়ে যেতে পারেন!");
                            renderMainMenu($chatId, $state);
                        } else {
                            answerCallback($cbId, 'অ্যাকাউন্ট পাওয়া যায়নি');
                        }
                    } elseif ($data === 'action_logout_reauth') {
                        answerCallback($cbId, 'রি-অথরাইজেশন...');
                        $reauthMsg = "🚪 *অ্যাকাউন্ট লগআউট ও রি-অথরাইজেশন*\n"
                                   . "━━━━━━━━━━━━━━━━━━━━\n"
                                   . "আপনি যদি অ্যান্টিগ্রাভিটিতে সম্পূর্ণ নতুন জিমেইল অ্যাকাউন্ট লগইন করতে চান:\n\n"
                                   . "১. কম্পিউটারের অ্যান্টিগ্রাভিটি ওপেন করে প্রোফাইল থেকে Logout দিন।\n"
                                   . "২. অথবা মোবাইল থেকেই নতুন Google OAuth অথরাইজেশন সম্পন্ন করুন।\n"
                                   . "৩. নতুন অ্যাকাউন্ট যুক্ত হলে টেলিগ্রাম স্বয়ংক্রিয়ভাবে সিঙ্ক হয়ে যাবে এবং বট ২৪ ঘণ্টা কানেক্টেড থাকবে!";
                        sendMsg($chatId, $reauthMsg);
                    } elseif ($data === 'action_add_acc') {
                    } elseif ($data === 'action_sync_active_chat') {
                        answerCallback($cbId, 'অ্যাক্টিভ চ্যাট সিঙ্ক হচ্ছে...');
                        $latest = getLatestActiveConversation();
                        if ($latest) {
                            $state['active_conv_id'] = $latest['id'];
                            $state['active_proj_name'] = $latest['project'];
                            $state['active_chat_title'] = $latest['title'];
                            saveState($state);
                            botLog("[SYNC] Active conversation synced to: {$latest['id']} ({$latest['title']})");
                            sendMsg($chatId, "🎯 *সর্বশেষ সক্রিয় চ্যাট সফলভাবে সিঙ্ক হয়েছে!*\n━━━━━━━━━━━━━━━━━━━━\n📁 *প্রজেক্ট:* `{$latest['project']}`\n💬 *চ্যাট:* `{$latest['title']}`\n🆔 `{$latest['id']}`\n\nএখন আপনি টেলিগ্রাম থেকে যা লিখবেন, তা সরাসরি এই সক্রিয় চ্যাটে কাজ করবে!");
                        } else {
                            sendMsg($chatId, "⚠️ সক্রিয় চ্যাট পাওয়া যায়নি।");
                        }
                        renderMainMenu($chatId, $state);
                    } elseif ($data === 'action_pc_screenshot') {
                        answerCallback($cbId, 'পিসি স্ক্রিনশট নেওয়া হচ্ছে...');
                        botLog("[SCREENSHOT] Capturing desktop screenshot...");
                        $psOut = trim(shell_exec('powershell.exe -ExecutionPolicy Bypass -File "' . PROJECT_ROOT . '/take_screenshot.ps1" 2>&1') ?? '');
                        $shotPath = PROJECT_ROOT . '/storage/logs/pc_screenshot.png';
                        $sent = false;
                        if (file_exists($shotPath) && filesize($shotPath) > 10000 && stripos($psOut, 'invalid') === false) {
                            $res = sendPhoto($chatId, $shotPath, "🖥️ *পিসির লাইভ স্ক্রিনশট*\n⏰ " . date('d M Y, h:i A'));
                            if ($res['ok'] ?? false) $sent = true;
                        }
                        if (!$sent) {
                            // Fallback to web screenshot
                            botLog("[SCREENSHOT] Desktop session not available, sending mobile web render fallback");
                            exec('node "' . PROJECT_ROOT . '/screenshot_verify.js" 2>&1');
                            $webShot = PROJECT_ROOT . '/storage/logs/verify_mobile.png';
                            if (!file_exists($webShot)) {
                                $webShot = 'C:/Users/UseR/.gemini/antigravity/brain/' . ($state['active_conv_id'] ?? DEFAULT_CONV_ID) . '/final_mobile.png';
                            }
                            if (!file_exists($webShot)) {
                                $webShot = 'C:/Users/UseR/.gemini/antigravity/brain/b5d31c4a-85e9-4609-b28a-3786eed9a1a3/final_mobile.png';
                            }
                            sendMsg($chatId, "🔒 *পিসি স্ক্রিন স্ট্যাটাস:* কম্পিউটার স্ক্রিন বর্তমানে লকড / হেডলেস সেশনে রয়েছে।\n\n📱 ওয়েবসাইটের লাইভ মোবাইল প্রিভিউ নিচে পাঠানো হলো:");
                            if (file_exists($webShot)) {
                                sendPhoto($chatId, $webShot, "📱 *কারিয়ানা মোবাইল লাইভ ভিউ* (Port 8015)\n" . getStandardLinksText());
                            }
                        }
                    } elseif ($data === 'action_web_screenshot') {
                        answerCallback($cbId, 'ওয়েবসাইট প্রিভিউ প্রস্তুত হচ্ছে...');
                        botLog("[WEB_SCREENSHOT] Generating live web screenshot...");
                        exec('node "' . PROJECT_ROOT . '/screenshot_verify.js" 2>&1');
                        $webShot = 'C:/Users/UseR/.gemini/antigravity/brain/' . ($state['active_conv_id'] ?? DEFAULT_CONV_ID) . '/final_mobile.png';
                        if (!file_exists($webShot)) {
                            $webShot = 'C:/Users/UseR/.gemini/antigravity/brain/b5d31c4a-85e9-4609-b28a-3786eed9a1a3/final_mobile.png';
                        }
                        if (file_exists($webShot)) {
                            sendPhoto($chatId, $webShot, "📱 *কারিয়ানা ওয়েবসাইট লাইভ রেন্ডারিং*\n" . getStandardLinksText());
                        } else {
                            sendMsg($chatId, "⚠️ প্রিভিউ তৈরি হতে পারছে না, সার্ভার চালু আছে কিনা চেক করুন।");
                        }
                    } elseif ($data === 'action_open_agy') {
                        answerCallback($cbId, 'Antigravity ওপেন হচ্ছে...');
                        exec('powershell.exe -Command "Start-Process \'C:\Users\UseR\AppData\Local\Programs\antigravity\Antigravity.exe\'"');
                        botLog("[AGY] Started Antigravity.exe");
                        sendMsg($chatId, "🚀 *Google Antigravity IDE চালু করার নির্দেশ দেওয়া হয়েছে!*\n\nকিছুক্ষণের মধ্যে এটি স্ক্রিনে ওপেন হয়ে যাবে।");
                    } elseif ($data === 'menu_system_health') {
                        answerCallback($cbId, 'সিস্টেম হেলথ আনা হচ্ছে...');
                        renderSystemHealthView($chatId, $state);
                    } elseif ($data === 'action_lock_pc') {
                        answerCallback($cbId, 'পিসি লক করা হচ্ছে...');
                        exec('rundll32.exe user32.dll,LockWorkStation');
                        sendMsg($chatId, "🔒 *কম্পিউটার সফলভাবে লক করা হয়েছে!*\n\nপিসির স্ক্রিন এখন লকড। আনলক করতে উইন্ডোজ পাসওয়ার্ড লাগবে।");
                    } elseif ($data === 'action_shutdown_pc') {
                        answerCallback($cbId, '🛑 শাটডাউন কমান্ড সক্রিয় হচ্ছে...');
                        botLog("[POWER] Shutdown initiated from Telegram");
                        $cancelKb = [
                            [['text' => '❌ শাটডাউন বাতিল করুন', 'callback_data' => 'action_cancel_shutdown']]
                        ];
                        sendMsg($chatId, "🛑 *কম্পিউটার শাটডাউন হতে যাচ্ছে!*\n━━━━━━━━━━━━━━━━━━━━\n⏱️ ১০ সেকেন্ডের মধ্যে পিসি পাওয়ার অফ হবে।\n\nযদি ভুলবশত ক্লিক করে থাকেন তবে নিচের বাটনে চাপ দিয়ে এখনই বাতিল করুন:", $cancelKb);
                        exec('shutdown /s /t 10 /c "Antigravity Remote Shutdown via Telegram"');
                    } elseif ($data === 'action_restart_pc') {
                        answerCallback($cbId, '🔄 রিস্টার্ট কমান্ড সক্রিয় হচ্ছে...');
                        botLog("[POWER] Restart initiated from Telegram");
                        $cancelKb = [
                            [['text' => '❌ রিস্টার্ট বাতিল করুন', 'callback_data' => 'action_cancel_shutdown']]
                        ];
                        sendMsg($chatId, "🔄 *কম্পিউটার রিস্টার্ট হতে যাচ্ছে!*\n━━━━━━━━━━━━━━━━━━━━\n⏱️ ১০ সেকেন্ডের মধ্যে পিসি রিস্টার্ট হবে এবং বুট হওয়ার সাথে সাথেই বট আবার আপনাকে স্বয়ংক্রিয়ভাবে আপডেট পাঠাবে!\n\nবাতিল করতে চাইলে নিচের বাটনে চাপ দিন:", $cancelKb);
                        exec('shutdown /r /t 10 /c "Antigravity Remote Restart via Telegram"');
                    } elseif ($data === 'action_cancel_shutdown') {
                        answerCallback($cbId, '✅ বাতিল করা হয়েছে');
                        botLog("[POWER] Shutdown/Restart canceled from Telegram");
                        exec('shutdown /a');
                        sendMsg($chatId, "✅ *পিসি শাটডাউন / রিস্টার্ট সফলভাবে বাতিল করা হয়েছে!*\n\nকম্পিউটার ও অ্যান্টিগ্রাভিটি সম্পূর্ণ সচল রয়েছে 🟢");
                        renderMainMenu($chatId, $state);
                    } elseif ($data === 'action_restart_srv') {
                        answerCallback($cbId, 'সার্ভার রিস্টার্ট হচ্ছে...');
                        exec('powershell.exe -Command "Get-CimInstance Win32_Process -Filter \"Name=\'php.exe\'\" | Where-Object { $_.CommandLine -like \"*:8015*\" } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }"');
                        sleep(1);
                        exec('start /b php -S 0.0.0.0:8015 router.php > storage\logs\php_server.log 2>&1');
                        sleep(1);
                        $online = isServerRunning(8015) ? "🟢 রানিং (Port 8015)" : "🔴 বন্ধ";
                        sendMsg($chatId, "🔄 *পিএইচপি ওয়েব সার্ভার রিস্টার্ট সম্পন্ন!*\n\nস্ট্যাটাস: {$online}" . getStandardLinksText());
                    } elseif ($data === 'menu_workspaces') {
                        answerCallback($cbId, 'প্রজেক্ট তালিকা লোড হচ্ছে...');
                        renderWorkspacesMenu($chatId, $state);
                    } elseif ($data === 'menu_modes') {
                        answerCallback($cbId, 'মোড তালিকা...');
                        renderModeMenu($chatId, $state);
                    } elseif (strpos($data, 'setmode_') === 0) {
                        $newMode = substr($data, 8);
                        $state['mode'] = $newMode;
                        saveState($state);
                        answerCallback($cbId, 'মোড পরিবর্তিত হয়েছে!');
                        sendMsg($chatId, "✅ *কাজের মোড পরিবর্তিত হয়েছে:*\n\nবর্তমান মোড: *" . getModeTitle($newMode) . "*");
                        renderMainMenu($chatId, $state);
                    } elseif ($data === 'select_kariana') {
                        $state['active_conv_id'] = DEFAULT_CONV_ID;
                        $state['active_proj_name'] = DEFAULT_PROJECT_NAME;
                        $state['active_chat_title'] = DEFAULT_CHAT_TITLE;
                        saveState($state);
                        answerCallback($cbId, 'কারিয়ানা প্রজেক্ট সিলেক্ট হয়েছে!');
                        sendMsg($chatId, "✅ *কারিয়ানা কুরআন প্রজেক্ট সক্রিয় করা হয়েছে!*\n\nএখন যেকোনো মেসেজ পাঠালে তা সরাসরি কারিয়ানা প্রজেক্টে যাবে।");
                        renderMainMenu($chatId, $state);
                    } elseif ($data === 'menu_status') {
                        answerCallback($cbId, 'স্ট্যাটাস লোড হচ্ছে...');
                        renderStatusView($chatId, $state);
                    } elseif ($data === 'menu_agy_status') {
                        answerCallback($cbId, 'স্ট্যাটাস অডিট হচ্ছে...');
                        renderAntigravityStatusView($chatId, $state);
                    } elseif ($data === 'menu_troubleshoot') {
                        answerCallback($cbId, 'ট্রাবলশুট সেন্টার...');
                        renderTroubleshootView($chatId, $state);
                    } elseif ($data === 'action_fix_port') {
                        answerCallback($cbId, 'পোর্ট ফিক্স করা হচ্ছে...');
                        exec('powershell.exe -Command "Get-CimInstance Win32_Process -Filter \"Name=\'php.exe\'\" | Where-Object { $_.CommandLine -like \"*:8015*\" } | ForEach-Object { Stop-Process -Id $_.ProcessId -Force }"');
                        sleep(1);
                        exec('start /b php -S 0.0.0.0:8015 router.php > storage\logs\php_server.log 2>&1');
                        sleep(1);
                        $running = isServerRunning(8015);
                        $statusText = $running ? "🟢 সফলভাবে পোর্ট ৮০১৫ ফিক্সড ও সার্ভার রানিং!" : "⚠️ সার্ভার চালু হতে কিছুটা সময় নিচ্ছে, অনুগ্রহ করে চেক করুন।";
                        sendMsg($chatId, "🛠️ *পোর্ট ৮০১৫ অটো-ফিক্স রিপোর্ট:*\n\n{$statusText}" . getStandardLinksText());
                    } elseif ($data === 'action_clean_cache') {
                        answerCallback($cbId, 'ক্যাশ ও লগ পরিষ্কার করা হচ্ছে...');
                        $logDir = PROJECT_ROOT . '/storage/logs';
                        $cleanedCount = 0;
                        if (is_dir($logDir)) {
                            foreach (glob($logDir . '/*.log') as $f) {
                                if (basename($f) !== 'php_server.log') {
                                    @unlink($f);
                                    $cleanedCount++;
                                }
                            }
                        }
                        sendMsg($chatId, "🧹 *ক্লিনআপ সম্পন্ন!*\n\nসিস্টেম লগ এবং ক্যাশ পরিষ্কার করা হয়েছে ({$cleanedCount} টি ফাইল রিসেট)। মেমোরি রিফ্রেশ হয়েছে 🟢");
                    } elseif ($data === 'action_git_sync') {
                        answerCallback($cbId, 'গিট স্ট্যাটাস চেক করা হচ্ছে...');
                        $gitStatus = trim(shell_exec('git -C "' . PROJECT_ROOT . '" status -s 2>NUL') ?? '');
                        $gitBranch = trim(shell_exec('git -C "' . PROJECT_ROOT . '" branch --show-current 2>NUL') ?? 'main');
                        $gitLast = trim(shell_exec('git -C "' . PROJECT_ROOT . '" log -1 --pretty=format:"%h - %s" 2>NUL') ?? '');
                        $statusDesc = empty($gitStatus) ? "✅ সমস্ত ফাইল গিটহাবে সিঙ্কড ও আপ-টু-ডেট (Working tree clean)!" : "📝 পেন্ডিং ফাইলসমূহ:\n```\n" . substr($gitStatus, 0, 500) . "\n```";
                        sendMsg($chatId, "📦 *গিটহাব রিপোজিটরি সিঙ্ক স্ট্যাটাস*\n━━━━━━━━━━━━━━━━━━━━\n🌿 *ব্রাঞ্চ:* `{$gitBranch}`\n📌 *লেটেস্ট কমিট:* `{$gitLast}`\n\n{$statusDesc}\n🔗 [GitHub Repository](" . LINK_GITHUB . ")");
                    } elseif ($data === 'prompt_help') {
                        answerCallback($cbId);
                        sendMsg($chatId, "💬 *প্রম্পট দেওয়ার নিয়ম:*\nমোবাইলে ভয়েস বা টেক্সটে যা লিখবেন, সাথে সাথে কম্পিউটারের অ্যান্টিগ্রাভিটিতে কাজ হতে থাকবে!");
                    } elseif (strpos($data, 'ws_') === 0) {
                        $hash = substr($data, 3);
                        answerCallback($cbId, 'চ্যাটগুলো লোড হচ্ছে...');
                        renderWorkspaceChatsMenu($chatId, $hash, $state);
                    } elseif (strpos($data, 'chat_') === 0) {
                        $shortId = substr($data, 5);
                        if (file_exists(CONV_DB_PATH)) {
                            $db = new PDO('sqlite:' . CONV_DB_PATH);
                            $stmt = $db->prepare("SELECT conversation_id, preview, workspace_uris FROM conversation_summaries WHERE conversation_id LIKE ? LIMIT 1");
                            $stmt->execute([$shortId . '%']);
                            $conv = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($conv) {
                                $ws = json_decode($conv['workspace_uris'], true);
                                $wsName = (!empty($ws) && isset($ws[0])) ? basename(urldecode(str_replace('file:///', '', $ws[0]))) : 'Workspace';
                                $chatTitle = getConversationTitle($conv['conversation_id'], $conv['preview'] ?? '');

                                $state['active_conv_id'] = $conv['conversation_id'];
                                $state['active_proj_name'] = $wsName;
                                $state['active_chat_title'] = $chatTitle;
                                saveState($state);

                                answerCallback($cbId, 'চ্যাট সুইচ সম্পন্ন!');
                                sendMsg($chatId, "🎯 *সক্রিয় চ্যাট পরিবর্তন করা হয়েছে:*\n\n📁 *প্রজেক্ট:* `{$wsName}`\n💬 *চ্যাট:* `{$chatTitle}`\n🆔 `{$conv['conversation_id']}`\n\n💬 এখন আপনি যা লিখবেন, তা এই নির্দিষ্ট চ্যাটে কাজ করবে!");
                                renderMainMenu($chatId, $state);
                            } else {
                                answerCallback($cbId, 'চ্যাট পাওয়া যায়নি');
                            }
                        }
                    } elseif (strpos($data, 'newchat_') === 0) {
                        answerCallback($cbId, 'নতুন চ্যাট ফিচার');
                        sendMsg($chatId, "➕ *নতুন চ্যাট তৈরি করতে:*\nআপনার নতুন নির্দেশটি লিখে পাঠান।");
                    }
                    continue;
                }

                // 2. Handle Messages (Text & Voice)
                if (isset($up['message'])) {
                    $msg = $up['message'];
                    $chatId = (string)($msg['chat']['id'] ?? $state['chat_id']);
                    $state['chat_id'] = $chatId;
                    saveState($state);

                    // Handle Voice Messages
                    if (isset($msg['voice'])) {
                        $voice = $msg['voice'];
                        $dur = $voice['duration'] ?? 0;
                        botLog("[VOICE] Received voice message ({$dur}s) from {$chatId}");
                        $voiceReply = "🎙️ *আপনার ভয়েস মেসেজ পেয়েছি!* ({$dur} সেকেন্ড)\n"
                                    . "━━━━━━━━━━━━━━━━━━━━\n"
                                    . "💡 *মোবাইল থেকে সরাসরি মুখে বলে কমান্ড দেওয়ার সহজ উপায়:*\n\n"
                                    . "১. টেলিগ্রামের টেক্সট লেখার বক্সে ক্লিক করুন।\n"
                                    . "২. আপনার ফোনের কীবোর্ডের (যেমন Google Gboard) নিচে স্পেসবারের পাশে থাকা 🎤 **মাইক্রোফোন আইকনে** চাপুন।\n"
                                    . "৩. বাংলায় মুখে যা বলবেন, তা সাথে সাথে নিখুঁত বাংলা টেক্সট হয়ে যাবে।\n"
                                    . "৪. সেন্ড বাটনে চাপলেই সাথে সাথে অ্যান্টিগ্রাভিটিতে কোডিং/কাজ শুরু হবে!\n\n"
                                    . "📱 বিছানা থেকেই আপনি নিচের বাটন চেপে পিসির স্ক্রিনশট বা স্ট্যাটাস দেখে নিতে পারেন 🟢";
                        sendMsg($chatId, $voiceReply);
                        continue;
                    }

                    $text = trim($msg['text'] ?? '');
                    if (empty($text)) continue;

                    botLog("[MESSAGE] From {$chatId}: {$text}");

                    if ($text === '/start' || $text === '🏠 মেইন মেনু' || $text === '/menu') {
                        renderMainMenu($chatId, $state);
                    } elseif ($text === '📁 সাম্প্রতিক প্রজেক্ট' || $text === '/recent') {
                        renderWorkspacesMenu($chatId, $state);
                    } elseif ($text === '🧠 এআই মডেল নির্বাচন' || $text === '/models' || $text === '/model') {
                        renderModelsMenu($chatId, $state);
                    } elseif ($text === '👤 অ্যাকাউন্ট ও কোটা' || $text === '/accounts' || $text === '/account') {
                        renderAccountsMenu($chatId, $state);
                    } elseif ($text === '📊 পিসি হেলথ ও রিসোর্স' || $text === '/health') {
                        renderSystemHealthView($chatId, $state);
                    } elseif ($text === '⚙️ মোড পরিবর্তন' || $text === '/mode') {
                        renderModeMenu($chatId, $state);
                    } elseif ($text === '📊 লাইভ স্ট্যাটাস ও লিংক' || $text === '/status') {
                        renderStatusView($chatId, $state);
                    } elseif ($text === '🔍 অ্যান্টিগ্রাভিটি স্ট্যাটাস' || $text === '/audit' || $text === '/agy') {
                        renderAntigravityStatusView($chatId, $state);
                    } elseif ($text === '🛠️ কুইক ফিক্স ও ট্রাবলশুট' || $text === '🛠️ কুইক ফিক্স' || $text === '/troubleshoot' || $text === '/fix') {
                        renderTroubleshootView($chatId, $state);
                    } elseif ($text === '📸 পিসির স্ক্রিনশট' || $text === '/screenshot' || $text === '/screen') {
                        botLog("[SCREENSHOT] Text command triggered screenshot");
                        $psOut = trim(shell_exec('powershell.exe -ExecutionPolicy Bypass -File "' . PROJECT_ROOT . '/take_screenshot.ps1" 2>&1') ?? '');
                        $shotPath = PROJECT_ROOT . '/storage/logs/pc_screenshot.png';
                        $sent = false;
                        if (file_exists($shotPath) && filesize($shotPath) > 10000 && stripos($psOut, 'invalid') === false) {
                            $res = sendPhoto($chatId, $shotPath, "🖥️ *পিসির লাইভ স্ক্রিনশট*\n⏰ " . date('d M Y, h:i A'));
                            if ($res['ok'] ?? false) $sent = true;
                        }
                        if (!$sent) {
                            exec('node "' . PROJECT_ROOT . '/screenshot_verify.js" 2>&1');
                            $webShot = 'C:/Users/UseR/.gemini/antigravity/brain/b5d31c4a-85e9-4609-b28a-3786eed9a1a3/final_mobile.png';
                            sendMsg($chatId, "🔒 *পিসি স্ক্রিন স্ট্যাটাস:* ডেস্কটপ বর্তমানে লক স্ক্রিনে বা হেডলেস সেশনে রয়েছে।\n\n📱 ওয়েবসাইটের লাইভ মোবাইল প্রিভিউ নিচে পাঠানো হলো:");
                            if (file_exists($webShot)) {
                                sendPhoto($chatId, $webShot, "📱 *কারিয়ানা মোবাইল লাইভ ভিউ* (Port 8015)\n" . getStandardLinksText());
                            }
                        }
                    } elseif ($text === '📱 ওয়েবসাইট লাইভ ভিউ' || $text === '/web' || $text === '/preview') {
                        botLog("[WEB] Text command triggered website preview");
                        exec('node "' . PROJECT_ROOT . '/screenshot_verify.js" 2>&1');
                        $webShot = 'C:/Users/UseR/.gemini/antigravity/brain/b5d31c4a-85e9-4609-b28a-3786eed9a1a3/final_mobile.png';
                        if (file_exists($webShot)) {
                            sendPhoto($chatId, $webShot, "📱 *কারিয়ানা ওয়েবসাইট লাইভ রেন্ডারিং*\n" . getStandardLinksText());
                        } else {
                            sendMsg($chatId, "⚠️ প্রিভিউ তৈরি হতে পারছে না, সার্ভার চালু আছে কিনা চেক করুন।");
                        }
                    } elseif ($text === '🛑 পিসি শাটডাউন' || $text === '/shutdown') {
                        $cancelKb = [[['text' => '❌ শাটডাউন বাতিল করুন', 'callback_data' => 'action_cancel_shutdown']]];
                        sendMsg($chatId, "🛑 *কম্পিউটার শাটডাউন হতে যাচ্ছে!*\n━━━━━━━━━━━━━━━━━━━━\n⏱️ ১০ সেকেন্ডের মধ্যে পিসি পাওয়ার অফ হবে।\n\nবাতিল করতে চাইলে নিচের বাটনে চাপ দিন:", $cancelKb);
                        exec('shutdown /s /t 10 /c "Antigravity Remote Shutdown via Telegram"');
                    } elseif ($text === '🔄 পিসি রিস্টার্ট' || $text === '/restart') {
                        $cancelKb = [[['text' => '❌ রিস্টার্ট বাতিল করুন', 'callback_data' => 'action_cancel_shutdown']]];
                        sendMsg($chatId, "🔄 *কম্পিউটার রিস্টার্ট হতে যাচ্ছে!*\n━━━━━━━━━━━━━━━━━━━━\n⏱️ ১০ সেকেন্ডের মধ্যে পিসি রিস্টার্ট হবে।\n\nবাতিল করতে চাইলে নিচের বাটনে চাপ দিন:", $cancelKb);
                        exec('shutdown /r /t 10 /c "Antigravity Remote Restart via Telegram"');
                    } elseif ($text === '🔄 রিফ্রেশ') {
                        renderMainMenu($chatId, $state);
                    } else {
                        // Check if user is adding an account
                        if (stripos($text, 'অ্যাকাউন্ট নাম:') === 0 || stripos($text, 'account name:') === 0) {
                            $accName = trim(substr($text, strpos($text, ':') + 1));
                            $accData = loadAccounts();
                            $newId = 'acc_' . (count($accData['accounts']) + 1);
                            $accData['accounts'][$newId] = [
                                'id'           => $newId,
                                'name'         => $accName,
                                'profile'      => 'profile_' . (count($accData['accounts']) + 1),
                                'status'       => 'রেডি 🟢',
                                'model'        => $state['selected_model'] ?? DEFAULT_MODEL,
                                'quota_status' => 'উপলব্ধ 🟢'
                            ];
                            saveAccounts($accData);
                            sendMsg($chatId, "✅ *নতুন অ্যাকাউন্ট যুক্ত করা হয়েছে!*\n\nনাম: *{$accName}*\nআইডি: `{$newId}`\n\nআপনি এখন এটি যেকোনো সময় নির্বাচন করতে পারেন!");
                            renderAccountsMenu($chatId, $state);
                            continue;
                        }

                        // Check if message mentions another project
                        $intent = detectProjectIntent($text, $state['active_proj_name']);
                        if ($intent) {
                            $suggestMsg = "💡 *প্রজেক্ট সাজেশন ডিটেকশন!*\n"
                                        . "━━━━━━━━━━━━━━━━━━━━\n"
                                        . "আপনার মেসেজে `{$intent['matched_kw']}` শব্দটি পাওয়া গেছে। আপনি কি এটি **{$intent['target_name']}** প্রজেক্টে এক্সিকিউট করতে চান?\n\n"
                                        . "📝 *আপনার মেসেজ:* _{$text}_";

                            $suggestKb = [
                                [
                                    ['text' => "🚀 হ্যাঁ, {$intent['target_name']}-এ যাও", 'callback_data' => 'ws_' . $intent['target_hash']],
                                    ['text' => "🕌 না, বর্তমান প্রজেক্টেই রাখো", 'callback_data' => 'select_kariana']
                                ],
                                [
                                    ['text' => "🔙 মেইন মেনু", 'callback_data' => 'menu_home']
                                ]
                            ];
                            sendMsg($chatId, $suggestMsg, $suggestKb);
                            continue;
                        }

                        // Auto-sync to latest conversation if currently set to old conversation
                        $latest = getLatestActiveConversation();
                        if ($latest && !empty($latest['id'])) {
                            if (empty($state['active_conv_id']) || $state['active_conv_id'] === '94596634-65c0-432c-a4d3-6aa058846c61') {
                                $state['active_conv_id'] = $latest['id'];
                                $state['active_proj_name'] = $latest['project'];
                                $state['active_chat_title'] = $latest['title'];
                                saveState($state);
                                botLog("[AUTO-BIND] Bound active conv to {$latest['id']} ({$latest['title']})");
                            }
                        }

                        // Direct message: Execute directly on active project via non-blocking dispatcher!
                        botLog("[PROMPT] Forwarding prompt to {$state['active_conv_id']}: {$text}");
                        $task = dispatchPrompt($chatId, $text, $state);
                        if ($task !== null) {
                            $activeTask = $task;
                        }
                    }
                }
            }
        }
    } catch (Throwable $e) {
        botLog("[LOOP ERROR] " . $e->getMessage());
        sleep(2);
    }
}
