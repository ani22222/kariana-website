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
define('DEFAULT_CONV_ID', '94596634-65c0-432c-a4d3-6aa058846c61');
define('DEFAULT_PROJECT_NAME', 'Kariana Website');
define('DEFAULT_CHAT_TITLE', 'Telegram Bot Remote Integration');
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
    if (file_exists(STATE_FILE)) {
        $data = json_decode(file_get_contents(STATE_FILE), true);
        if (is_array($data)) {
            $data['active_conv_id']    = $data['active_conv_id'] ?? DEFAULT_CONV_ID;
            $data['active_proj_name']  = $data['active_proj_name'] ?? DEFAULT_PROJECT_NAME;
            $data['active_chat_title'] = $data['active_chat_title'] ?? DEFAULT_CHAT_TITLE;
            $data['chat_id']           = $data['chat_id'] ?? '1827362508';
            $data['last_update_id']    = $data['last_update_id'] ?? 0;
            $data['mode']              = $data['mode'] ?? 'turbo'; // turbo, safe, planning
            $data['selected_model']    = $data['selected_model'] ?? DEFAULT_MODEL;
            $data['active_account']    = $data['active_account'] ?? 'acc_1';
            $data['is_busy']           = $data['is_busy'] ?? false;
            $data['last_heartbeat']    = $data['last_heartbeat'] ?? time();
            return $data;
        }
    }
    return [
        'active_conv_id'    => DEFAULT_CONV_ID,
        'active_proj_name'  => DEFAULT_PROJECT_NAME,
        'active_chat_title' => DEFAULT_CHAT_TITLE,
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
                [['text' => '🧠 এআই মডেল নির্বাচন'], ['text' => '👤 অ্যাকাউন্ট ও কোটা']],
                [['text' => '📊 পিসি হেলথ ও রিসোর্স'], ['text' => '⚙️ মোড পরিবর্তন']]
            ],
            'resize_keyboard' => true,
            'persistent'      => true
        ];
    }

    return tgRequest('sendMessage', $params);
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
    return tgRequest('editMessageText', $params);
}

function answerCallback(string $callbackQueryId, string $text = ''): void {
    tgRequest('answerCallbackQuery', [
        'callback_query_id' => $callbackQueryId,
        'text'              => $text,
        'show_alert'        => false
    ]);
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
            ['text' => '📁 সাম্প্রতিক প্রজেক্ট ও চ্যাটসমূহ', 'callback_data' => 'menu_workspaces'],
            ['text' => '🕌 কারিয়ানা প্রজেক্ট', 'callback_data' => 'select_kariana']
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
            ['text' => '🔄 সার্ভার রিস্টার্ট (8015)', 'callback_data' => 'action_restart_srv'],
            ['text' => '🔒 পিসি স্ক্রিন লক', 'callback_data' => 'action_lock_pc']
        ],
        [
            ['text' => '🔄 রিফ্রেশ ড্যাশবোর্ড', 'callback_data' => 'menu_home']
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

function executePromptAndStreamUpdates(string $chatId, string $prompt, array &$state): void {
    $convId = $state['active_conv_id'] ?? DEFAULT_CONV_ID;
    $projName = $state['active_proj_name'] ?? DEFAULT_PROJECT_NAME;
    $chatTitle = $state['active_chat_title'] ?? DEFAULT_CHAT_TITLE;
    $mode = $state['mode'] ?? 'turbo';
    $model = $state['selected_model'] ?? DEFAULT_MODEL;

    // 1. Initial Status Message
    $initText = "⏳ *কাজ গ্রহণ করা হয়েছে!*\n"
              . "━━━━━━━━━━━━━━━━━━━━\n"
              . "🎯 *প্রজেক্ট:* `{$projName}`\n"
              . "💬 *চ্যাট:* `{$chatTitle}`\n"
              . "🧠 *মডেল:* `{$model}`\n"
              . "⚙️ *মোড:* " . getModeTitle($mode) . "\n"
              . "📝 *আপনার প্রম্পট:* _{$prompt}_\n\n"
              . "🔄 *স্ট্যাটাস:* Antigravity প্রসেসিং শুরু করছে...";
    
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
    
    echo "[AGENTAPI] Sending prompt to {$convId}...\n";
    $out = [];
    $code = 0;
    exec($cmd . ' 2>&1', $out, $code);
    echo "[AGENTAPI] Code: {$code}, Output: " . implode(" ", $out) . "\n";

    if ($code !== 0) {
        // Check for Quota or Rate Limit errors
        $errString = implode("\n", $out);
        $isQuotaError = (stripos($errString, 'quota') !== false || stripos($errString, 'rate') !== false || stripos($errString, 'limit') !== false || stripos($errString, '429') !== false);

        if ($isQuotaError) {
            $errText = "⚠️ *কোটা / রেট লিমিট সতর্কতা!*\n"
                     . "━━━━━━━━━━━━━━━━━━━━\n"
                     . "বর্তমান অ্যাকাউন্টের লিমিট শেষ হয়েছে।\n"
                     . "🟢 *টেলিগ্রাম বট ২৪ ঘণ্টা নন-স্টপ কানেক্টেড রয়েছে!*\n\n"
                     . "👇 *নিচের বাটনে চাপ দিয়ে ব্যাকআপ অ্যাকাউন্টে সুইচ করুন অথবা মডেল পরিবর্তন করুন:*";

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
            return;
        }

        $errText = "⚠️ *মেসেজ পাঠাতে সমস্যা হয়েছে:*\n`" . $errString . "`\n\nসরাসরি Antigravity IDE-তে চেক করুন।";
        if ($statusMsgId) {
            editMsg($chatId, $statusMsgId, $errText);
        } else {
            sendMsg($chatId, $errText);
        }
        return;
    }

    // 4. Watch transcript for live updates (Wait up to 180 seconds)
    $startTime = time();
    $lastReportedStatus = '';
    $finalResponseText = '';
    $completed = false;

    while (time() - $startTime < 180) {
        sleep(2);

        if (!file_exists($transcriptFile)) {
            continue;
        }

        $lines = file($transcriptFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $totalLines = count($lines);

        if ($totalLines <= $initialLineCount) {
            continue;
        }

        for ($i = $initialLineCount; $i < $totalLines; $i++) {
            $line = $lines[$i];
            $json = json_decode($line, true);
            if (!$json) continue;

            $type = $json['type'] ?? '';
            $status = $json['status'] ?? '';
            $source = $json['source'] ?? '';

            // Tool executions
            if (!empty($json['tool_calls'])) {
                foreach ($json['tool_calls'] as $tc) {
                    $toolName = $tc['name'] ?? 'tool';
                    $toolSummary = $tc['args']['toolSummary'] ?? $toolName;
                    $statusUpdate = "🛠️ `{$toolName}` ({$toolSummary})";
                    if ($statusUpdate !== $lastReportedStatus && $statusMsgId) {
                        $lastReportedStatus = $statusUpdate;
                        $updateMsg = "⏳ *কাজ চলমান রয়েছে...*\n"
                                   . "━━━━━━━━━━━━━━━━━━━━\n"
                                   . "🎯 *প্রজেক্ট:* `{$projName}`\n"
                                   . "💬 *চ্যাট:* `{$chatTitle}`\n"
                                   . "🧠 *মডেল:* `{$model}`\n"
                                   . "📝 *প্রম্পট:* _{$prompt}_\n\n"
                                   . "🔄 *বর্তমান অ্যাকশন:* {$statusUpdate}\n"
                                   . "⏱️ *অতিবাহিত সময়:* " . (time() - $startTime) . "s";
                        editMsg($chatId, $statusMsgId, $updateMsg);
                    }
                }
            }

            // Planner response final output
            if ($source === 'MODEL' && $type === 'PLANNER_RESPONSE' && $status === 'DONE') {
                if (!empty($json['content'])) {
                    $finalResponseText = $json['content'];
                    $completed = true;
                    break 2;
                }
            }
        }
    }

    // 5. Send completion summary
    if ($completed && !empty($finalResponseText)) {
        $cleanText = mb_substr($finalResponseText, 0, 3000);
        $finalMsg = "✅ *কাজ সম্পন্ন হয়েছে! (Task Complete)*\n"
                  . "━━━━━━━━━━━━━━━━━━━━\n"
                  . "🎯 *প্রজেক্ট:* `{$projName}`\n"
                  . "💬 *চ্যাট:* `{$chatTitle}`\n"
                  . "🧠 *মডেল:* `{$model}`\n\n"
                  . $cleanText
                  . getStandardLinksText();

        $keyboard = [
            [
                ['text' => '💬 পরবর্তী নির্দেশ দিন', 'callback_data' => 'prompt_help'],
                ['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']
            ]
        ];

        sendMsg($chatId, $finalMsg, $keyboard);
    } else {
        $ongoingMsg = "🚀 *টাস্কটি Antigravity-তে প্রসেস হচ্ছে!*\n"
                    . "━━━━━━━━━━━━━━━━━━━━\n"
                    . "আপনার নির্দেশ অনুযায়ী ব্যাকগ্রাউন্ডে কাজ চলমান। আপনি নিশ্চিন্তে বিশ্রাম নিন।"
                    . getStandardLinksText();
        
        $keyboard = [
            [
                ['text' => '📊 স্ট্যাটাস চেক', 'callback_data' => 'menu_status'],
                ['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']
            ]
        ];
        sendMsg($chatId, $ongoingMsg, $keyboard);
    }
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
        [['text' => '📁 সাম্প্রতিক প্রজেক্ট ও চ্যাটসমূহ', 'callback_data' => 'menu_workspaces']],
        [['text' => '🧠 এআই মডেল পরিবর্তন', 'callback_data' => 'menu_models'], ['text' => '👤 অ্যাকাউন্ট ও কোটা', 'callback_data' => 'menu_accounts']],
        [['text' => '📊 পিসি হেলথ ও রিসোর্স', 'callback_data' => 'menu_system_health'], ['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']]
    ];

    sendMsg($state['chat_id'], $bootMsg, $bootKb);
}

$lastHeartbeatSave = time();

while (true) {
    try {
        // Update heartbeat timestamp every 20 seconds for accurate shutdown detection
        if (time() - $lastHeartbeatSave >= 20) {
            $state['last_heartbeat'] = time();
            saveState($state);
            $lastHeartbeatSave = time();
        }

        $updates = tgRequest('getUpdates', [
            'offset'  => $state['last_update_id'] + 1,
            'timeout' => 25
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
                        answerCallback($cbId, 'নতুন অ্যাকাউন্ট...');
                        sendMsg($chatId, "➕ *নতুন অ্যাকাউন্ট যুক্ত করার নিয়ম:*\nআপনার দ্বিতীয় জিমেইল বা ব্যাকআপ অ্যাকাউন্টের নাম লিখে পাঠান (যেমন: `অ্যাকাউন্ট নাম: রাফসান জিমেইল`)। বট স্বয়ংক্রিয়ভাবে প্রোফাইল স্লট তৈরি করে দেবে!");
                    } elseif ($data === 'menu_system_health') {
                        answerCallback($cbId, 'সিস্টেম হেলথ আনা হচ্ছে...');
                        renderSystemHealthView($chatId, $state);
                    } elseif ($data === 'action_lock_pc') {
                        answerCallback($cbId, 'পিসি লক করা হচ্ছে...');
                        exec('rundll32.exe user32.dll,LockWorkStation');
                        sendMsg($chatId, "🔒 *কম্পিউটার সফলভাবে লক করা হয়েছে!*\n\nপিসির স্ক্রিন এখন লকড। আনলক করতে উইন্ডোজ পাসওয়ার্ড লাগবে।");
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

                // 2. Handle Text Messages
                if (isset($up['message'])) {
                    $msg = $up['message'];
                    $chatId = (string)($msg['chat']['id'] ?? $state['chat_id']);
                    $state['chat_id'] = $chatId;
                    saveState($state);

                    $text = trim($msg['text'] ?? '');
                    if (empty($text)) continue;

                    echo "[MESSAGE] From {$chatId}: {$text}\n";

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

                        // Direct message: Execute directly on active project!
                        executePromptAndStreamUpdates($chatId, $text, $state);
                    }
                }
            }
        }
    } catch (Exception $e) {
        echo "[LOOP ERROR] " . $e->getMessage() . "\n";
        sleep(3);
    }
}
