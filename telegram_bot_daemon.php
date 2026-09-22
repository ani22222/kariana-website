<?php
/**
 * =========================================================================
 * Kariana Website & Antigravity Remote Controller — Telegram Bot Daemon 3.0
 * Bot: @raselcodebot (Integrity)
 * Features:
 *   - Auto-Boot & Exact Shutdown Time Detection
 *   - 24/7 Persistent Connectivity & Heartbeat
 *   - Multi-Project & Multi-Chat Two-Tier Browser
 *   - Smart Intent & Project Detection
 *   - Modes: Turbo, Safe, Planning
 *   - Real-time Transcript Streamer
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
define('STATE_FILE', PROJECT_ROOT . '/telegram_state.json');
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

// Load or initialize state
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
                [['text' => '⚙️ মোড পরিবর্তন'], ['text' => '📊 লাইভ স্ট্যাটাস ও লিংক']]
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
        if (stripos($currentProject, $projKey) !== false) continue; // Already in this project

        foreach ($keywords as $kw) {
            if (mb_strpos($lower, $kw) !== false) {
                // Found matching foreign project!
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
// System Checkers
// ---------------------------------------------------------

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
// View Handlers (Menus & Buttons)
// ---------------------------------------------------------

function renderMainMenu(string $chatId, array $state): void {
    $agyOnline = isAntigravityRunning() ? "🟢 চালু আছে (Active)" : "🟡 ব্যাকগ্রাউন্ড";
    $srvOnline = isServerRunning(8015) ? "🟢 চালু (Port 8015)" : "🔴 অফলাইন";

    $activeTitle = $state['active_chat_title'] ?? DEFAULT_CHAT_TITLE;
    $modeText = getModeTitle($state['mode'] ?? 'turbo');

    $text  = "✨ *অ্যান্টিগ্রাভিটি রিমোট কন্ট্রোল ড্যাশবোর্ড ৩.০*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "💻 *Antigravity IDE:* {$agyOnline}\n";
    $text .= "🌐 *ওয়েব সার্ভার:* {$srvOnline}\n";
    $text .= "📁 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n";
    $text .= "💬 *সক্রিয় চ্যাট:* `{$activeTitle}`\n";
    $text .= "⚙️ *কাজের মোড:* {$modeText}\n";
    $text .= "🆔 *Conversation ID:* `{$state['active_conv_id']}`\n\n";
    $text .= "👇 *নিচের অপশনগুলো বেছে নিন অথবা সরাসরি মেসেজ লিখুন:*";

    $keyboard = [
        [
            ['text' => '📁 সাম্প্রতিক প্রজেক্ট ও চ্যাটসমূহ', 'callback_data' => 'menu_workspaces'],
            ['text' => '🕌 কারিয়ানা প্রজেক্ট', 'callback_data' => 'select_kariana']
        ],
        [
            ['text' => '⚙️ মোড পরিবর্তন', 'callback_data' => 'menu_modes'],
            ['text' => '📊 লাইভ স্ট্যাটাস ও লিংক', 'callback_data' => 'menu_status']
        ],
        [
            ['text' => '➕ নতুন চ্যাট শুরু করুন', 'callback_data' => 'menu_new_task'],
            ['text' => '🔄 রিফ্রেশ ড্যাশবোর্ড', 'callback_data' => 'menu_home']
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

    $text  = "📊 *কারিয়ানা ওয়েবসাইট — সিস্টেম ও লাইভ স্ট্যাটাস*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "💻 *Antigravity:* {$agyOnline}\n";
    $text .= "🐘 *PHP Server:* {$srvOnline}\n";
    $text .= "🌿 *Git Branch:* `{$gitBranch}`\n";
    $text .= "📝 *লাস্ট Commit:* `{$gitCommit}`\n";
    $text .= "🎯 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n";
    $text .= "💬 *সক্রিয় চ্যাট:* `{$state['active_chat_title']}`\n";
    $text .= "⚙️ *মোড:* " . getModeTitle($state['mode'] ?? 'turbo') . "\n";
    $text .= getStandardLinksText();

    $keyboard = [
        [
            ['text' => '⚙️ মোড বদলান', 'callback_data' => 'menu_modes'],
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

    // 1. Initial Status Message
    $initText = "⏳ *কাজ গ্রহণ করা হয়েছে!*\n"
              . "━━━━━━━━━━━━━━━━━━━━\n"
              . "🎯 *প্রজেক্ট:* `{$projName}`\n"
              . "💬 *চ্যাট:* `{$chatTitle}`\n"
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
        $errText = "⚠️ *মেসেজ পাঠাতে সমস্যা হয়েছে:*\n`" . implode("\n", $out) . "`\n\nসরাসরি Antigravity IDE-তে চেক করুন।";
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
                  . "💬 *চ্যাট:* `{$chatTitle}`\n\n"
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
echo "  কারিয়ানা ও অ্যান্টিগ্রাভিটি টেলিগ্রাম বট ৩.০ চালু\n";
echo "  Bot: @raselcodebot\n";
echo "  24/7 Persistent Mode Active\n";
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

    $bootMsg = "⚡ *কম্পিউটার পুনরায় চালু হয়েছে — অ্যান্টিগ্রাভিটি ২.০ অটো-কানেক্টেড!*\n"
             . "━━━━━━━━━━━━━━━━━━━━\n"
             . "⏰ *অন হওয়ার সময়:* {$bootTimeStr}\n"
             . "🛑 *পূর্বে বন্ধ হয়েছিল:* {$offTimeStr}\n"
             . "💻 *কম্পিউটার স্ট্যাটাস:* চালু ও সক্রিয় 🟢\n"
             . "🧠 *Antigravity IDE:* কানেক্টেড 🟢\n"
             . "🌐 *ওয়েব সার্ভার:* Port 8015 রানিং 🟢\n"
             . "📁 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n"
             . "💬 *সক্রিয় চ্যাট:* `{$state['active_chat_title']}`\n"
             . "⚙️ *কাজের মোড:* " . getModeTitle($state['mode'] ?? 'turbo') . "\n\n"
             . "📱 আপনার পিসি সম্পূর্ণ রেডি! আপনি শুয়ে শুয়ে মোবাইলের টেলিগ্রাম থেকে যেকোনো কাজ দিয়ে যেতে পারবেন।"
             . getStandardLinksText();

    $bootKb = [
        [['text' => '📁 সাম্প্রতিক প্রজেক্ট ও চ্যাটসমূহ', 'callback_data' => 'menu_workspaces']],
        [['text' => '⚙️ মোড পরিবর্তন', 'callback_data' => 'menu_modes']],
        [['text' => '🏠 মেইন মেনু', 'callback_data' => 'menu_home']]
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
                    } elseif ($data === 'menu_new_task') {
                        answerCallback($cbId, 'নতুন টাস্ক');
                        sendMsg($chatId, "➕ *নতুন টাস্ক শুরু করতে:*\nআপনার নির্দেশ লিখে পাঠান, সরাসরি অ্যান্টিগ্রাভিটিতে এক্সিকিউট হবে!");
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
                    } elseif ($text === '⚙️ মোড পরিবর্তন' || $text === '/mode') {
                        renderModeMenu($chatId, $state);
                    } elseif ($text === '📊 লাইভ স্ট্যাটাস ও লিংক' || $text === '/status') {
                        renderStatusView($chatId, $state);
                    } elseif ($text === '🔄 রিফ্রেশ') {
                        renderMainMenu($chatId, $state);
                    } else {
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
