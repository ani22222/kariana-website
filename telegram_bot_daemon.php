<?php
/**
 * =========================================================================
 * Kariana Website & Antigravity Remote Controller — Telegram Bot Daemon
 * Bot: @raselcodebot (Integrity)
 * =========================================================================
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BOT_TOKEN', '8811158752:AAEKrP4XvGXDuw3NQKUGZLw6a-ooisnIK_8');
define('TG_API', 'https://api.telegram.org/bot' . BOT_TOKEN);

define('PROJECT_ROOT', __DIR__);
define('DEFAULT_CONV_ID', '94596634-65c0-432c-a4d3-6aa058846c61');
define('DEFAULT_PROJECT_NAME', 'Kariana Website (কারিয়ানা)');
define('STATE_FILE', PROJECT_ROOT . '/telegram_state.json');
define('AGENT_API_BAT', 'C:\\Users\\UseR\\.gemini\\antigravity\\bin\\agentapi.bat');
define('CONV_DB_PATH', 'C:/Users/UseR/.gemini/antigravity/conversation_summaries.db');
define('BRAIN_DIR', 'C:/Users/UseR/.gemini/antigravity/brain');

// Standard Access Links
define('LINK_LOCAL', 'http://localhost:8015');
define('LINK_WIFI', 'http://192.168.0.100:8015');
define('LINK_CLOUDFLARE', 'https://rev-mysql-stops-ext.trycloudflare.com');
define('LINK_GITHUB', 'https://github.com/ani22222/kariana-website');

// Load or initialize state
function loadState(): array {
    if (file_exists(STATE_FILE)) {
        $data = json_decode(file_get_contents(STATE_FILE), true);
        if (is_array($data)) return $data;
    }
    return [
        'active_conv_id'    => DEFAULT_CONV_ID,
        'active_proj_name'  => DEFAULT_PROJECT_NAME,
        'chat_id'           => '1827362508',
        'last_update_id'    => 0,
        'is_busy'           => false
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
                [['text' => '📊 লাইভ স্ট্যাটাস ও লিংক'], ['text' => '🔄 রিফ্রেশ']]
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
// System & Project Checkers
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

function getRecentProjects(int $limit = 6): array {
    $projects = [];
    if (file_exists(CONV_DB_PATH)) {
        try {
            $db = new PDO('sqlite:' . CONV_DB_PATH);
            $stmt = $db->query("SELECT conversation_id, preview, last_modified_time, workspace_uris 
                                FROM conversation_summaries 
                                WHERE parent_conversation_id IS NULL OR parent_conversation_id = '' 
                                ORDER BY last_modified_time DESC 
                                LIMIT " . $limit);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $r) {
                $ws = json_decode($r['workspace_uris'] ?? '[]', true);
                $wsName = (!empty($ws) && isset($ws[0])) ? basename(urldecode(str_replace('file:///', '', $ws[0]))) : 'Workspace';
                $preview = trim(str_replace(["\r", "\n", "`", "*"], ' ', $r['preview'] ?? ''));
                if (mb_strlen($preview) > 35) {
                    $preview = mb_substr($preview, 0, 35) . '...';
                }
                if (empty($preview)) $preview = 'General Task';
                $projects[] = [
                    'id'      => $r['conversation_id'],
                    'name'    => $wsName,
                    'preview' => $preview,
                    'time'    => $r['last_modified_time']
                ];
            }
        } catch (Exception $e) {
            echo "[DB ERROR] " . $e->getMessage() . "\n";
        }
    }
    return $projects;
}

function getStandardLinksText(): string {
    return "\n\n🔗 *স্ট্যান্ডার্ড অ্যাক্সেস লিংকসমূহ:*\n"
         . "🖥️ *Localhost:* [http://localhost:8015](" . LINK_LOCAL . ")\n"
         . "📶 *Wi-Fi LAN:* [http://192.168.0.100:8015](" . LINK_WIFI . ")\n"
         . "🌐 *Cloudflare Live:* [Live Link](" . LINK_CLOUDFLARE . ")\n"
         . "📦 *GitHub:* [kariana-website](" . LINK_GITHUB . ")";
}

// ---------------------------------------------------------
// View Handlers (Menus & Buttons)
// ---------------------------------------------------------

function renderMainMenu(string $chatId, array $state): void {
    $agyOnline = isAntigravityRunning() ? "🟢 চালু আছে (Active)" : "🟡 নিষ্ক্রিয় / ব্যাকগ্রাউন্ড";
    $srvOnline = isServerRunning(8015) ? "🟢 রানিং (Port 8015)" : "🔴 বন্ধ";

    $text  = "✨ *অ্যান্টিগ্রাভিটি রিমোট কন্ট্রোল ড্যাশবোর্ড*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "💻 *Antigravity IDE:* {$agyOnline}\n";
    $text .= "🌐 *ওয়েব সার্ভার:* {$srvOnline}\n";
    $text .= "🎯 *বর্তমান প্রজেক্ট:* `{$state['active_proj_name']}`\n";
    $text .= "🆔 *কনভার্সেশন ID:* `{$state['active_conv_id']}`\n\n";
    $text .= "👇 *নিচের অপশনগুলো থেকে এক ক্লিকে সিলেক্ট করুন অথবা সরাসরি নতুন প্রম্পট লিখুন:*";

    $keyboard = [
        [
            ['text' => '📁 সাম্প্রতিক প্রজেক্টসমূহ', 'callback_data' => 'menu_recent'],
            ['text' => '🕌 কারিয়ানা প্রজেক্ট', 'callback_data' => 'select_kariana']
        ],
        [
            ['text' => '📊 লাইভ স্ট্যাটাস ও লিংক', 'callback_data' => 'menu_status'],
            ['text' => '➕ নতুন প্রজেক্ট/টাস্ক', 'callback_data' => 'menu_new_task']
        ],
        [
            ['text' => '🔄 রিফ্রেশ ড্যাশবোর্ড', 'callback_data' => 'menu_home']
        ]
    ];

    sendMsg($chatId, $text, $keyboard);
}

function renderRecentProjectsMenu(string $chatId, array $state): void {
    $projects = getRecentProjects(6);
    $text  = "📁 *সাম্প্রতিক প্রজেক্টসমূহ (Recent Projects)*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "যেকোনো প্রজেক্টে ক্লিক করলেই সেটি অ্যাক্টিভ হয়ে যাবে এবং আপনার পরবর্তী মেসেজ সরাসরি সেখানে চলে যাবে:\n\n";

    $keyboard = [];
    foreach ($projects as $idx => $p) {
        $icon = ($p['id'] === $state['active_conv_id']) ? "✅ " : "📂 ";
        $btnText = $icon . ($idx + 1) . ". " . $p['name'] . ": " . $p['preview'];
        if (mb_strlen($btnText) > 40) {
            $btnText = mb_substr($btnText, 0, 38) . "..";
        }
        $keyboard[] = [
            ['text' => $btnText, 'callback_data' => 'switch_' . substr($p['id'], 0, 18)]
        ];
    }

    $keyboard[] = [
        ['text' => '🔙 মেইন মেনুতে ফিরুন', 'callback_data' => 'menu_home']
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
    $text .= "🎯 *অ্যাক্টিভ কনভার্সেশন:* `{$state['active_proj_name']}`\n";
    $text .= getStandardLinksText();

    $keyboard = [
        [
            ['text' => '💬 প্রম্পট পাঠান', 'callback_data' => 'prompt_help'],
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

    // 1. Initial Status Message
    $initText = "⏳ *কাজ গ্রহণ করা হয়েছে!*\n"
              . "━━━━━━━━━━━━━━━━━━━━\n"
              . "🎯 *টার্গেট প্রজেক্ট:* `{$projName}`\n"
              . "📝 *আপনার প্রম্পট:* _{$prompt}_\n\n"
              . "🔄 *স্ট্যাটাস:* Antigravity প্রসেসিং শুরু করছে...";
    
    $sent = sendMsg($chatId, $initText);
    $statusMsgId = $sent['result']['message_id'] ?? null;

    // 2. Measure current transcript size/lines before sending
    $transcriptFile = BRAIN_DIR . "/{$convId}/.system_generated/logs/transcript.jsonl";
    $initialLineCount = 0;
    if (file_exists($transcriptFile)) {
        $fp = fopen($transcriptFile, 'r');
        while (!feof($fp)) {
            if (fgets($fp) !== false) $initialLineCount++;
        }
        fclose($fp);
    }

    // 3. Send message to Antigravity via agentapi.bat
    $tempPromptFile = PROJECT_ROOT . '/scratch/last_tg_prompt.txt';
    file_put_contents($tempPromptFile, $prompt);

    // Call agentapi send-message
    $escapedPrompt = str_replace('"', '\"', $prompt);
    $cmd = '"' . AGENT_API_BAT . '" send-message ' . escapeshellarg($convId) . ' ' . escapeshellarg($prompt);
    
    echo "[AGENTAPI] Sending message to {$convId}...\n";
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

        // Check new lines from $initialLineCount to end
        for ($i = $initialLineCount; $i < $totalLines; $i++) {
            $line = $lines[$i];
            $json = json_decode($line, true);
            if (!$json) continue;

            $type = $json['type'] ?? '';
            $status = $json['status'] ?? '';
            $source = $json['source'] ?? '';

            // Check tool execution
            if (!empty($json['tool_calls'])) {
                foreach ($json['tool_calls'] as $tc) {
                    $toolName = $tc['name'] ?? 'tool';
                    $toolSummary = $tc['args']['toolSummary'] ?? $toolName;
                    $statusUpdate = "🛠️ *টুল রান হচ্ছে:* `{$toolName}` ({$toolSummary})";
                    if ($statusUpdate !== $lastReportedStatus && $statusMsgId) {
                        $lastReportedStatus = $statusUpdate;
                        $updateMsg = "⏳ *কাজ চলমান রয়েছে...*\n"
                                   . "━━━━━━━━━━━━━━━━━━━━\n"
                                   . "🎯 *প্রজেক্ট:* `{$projName}`\n"
                                   . "📝 *প্রম্পট:* _{$prompt}_\n\n"
                                   . "🔄 *বর্তমান অবস্থা:* {$statusUpdate}\n"
                                   . "⏱️ *অতিবাহিত সময়:* " . (time() - $startTime) . "s";
                        editMsg($chatId, $statusMsgId, $updateMsg);
                    }
                }
            }

            // Check generic command/file outputs
            if ($type === 'GENERIC' && !empty($json['content'])) {
                $statusUpdate = "⚙️ কমান্ড/ফাইল প্রসেসিং সম্পন্ন...";
                if ($statusUpdate !== $lastReportedStatus && $statusMsgId) {
                    $lastReportedStatus = $statusUpdate;
                    $updateMsg = "⏳ *কাজ চলমান রয়েছে...*\n"
                               . "━━━━━━━━━━━━━━━━━━━━\n"
                               . "🎯 *প্রজেক্ট:* `{$projName}`\n"
                               . "🔄 *বর্তমান অবস্থা:* {$statusUpdate}\n"
                               . "⏱️ *অতিবাহিত সময়:* " . (time() - $startTime) . "s";
                    editMsg($chatId, $statusMsgId, $updateMsg);
                }
            }

            // Check if final planner response has arrived with text content
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
        // Strip markdown that might break telegram, keep clean
        $cleanText = mb_substr($finalResponseText, 0, 3000);
        $finalMsg = "✅ *কাজ সম্পন্ন হয়েছে! (Task Complete)*\n"
                  . "━━━━━━━━━━━━━━━━━━━━\n"
                  . "🎯 *প্রজেক্ট:* `{$projName}`\n\n"
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
        // Antigravity is still thinking or long-running
        $ongoingMsg = "🚀 *টাস্কটি Antigravity-তে চলমান অবস্থায় প্রসেস হচ্ছে!*\n"
                    . "━━━━━━━━━━━━━━━━━━━━\n"
                    . "আপনার নির্দেশ অনুযায়ী ব্যাকগ্রাউন্ডে কাজ চলছে। আপনি নিশ্চিন্তে বিশ্রাম নিন। IDE-তে সরাসরি কাজ দেখা যাচ্ছে।"
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
echo "  কারিয়ানা ও অ্যান্টিগ্রাভিটি টেলিগ্রাম বট চালু হচ্ছে\n";
echo "  Bot: @raselcodebot\n";
echo "=====================================================\n";

$state = loadState();

while (true) {
    try {
        $updates = tgRequest('getUpdates', [
            'offset'  => $state['last_update_id'] + 1,
            'timeout' => 25
        ]);

        if (!empty($updates['result'])) {
            foreach ($updates['result'] as $up) {
                $state['last_update_id'] = $up['update_id'];
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
                    } elseif ($data === 'menu_recent') {
                        answerCallback($cbId, 'রিসেন্ট প্রজেক্ট লোড হচ্ছে...');
                        renderRecentProjectsMenu($chatId, $state);
                    } elseif ($data === 'select_kariana') {
                        $state['active_conv_id'] = DEFAULT_CONV_ID;
                        $state['active_proj_name'] = DEFAULT_PROJECT_NAME;
                        saveState($state);
                        answerCallback($cbId, 'কারিয়ানা প্রজেক্ট সিলেক্ট হয়েছে!');
                        sendMsg($chatId, "✅ *কারিয়ানা কুরআন প্রজেক্ট সক্রিয় করা হয়েছে!*\n\nএখন আপনি যেকোনো মেসেজ পাঠালে তা সরাসরি কারিয়ানা প্রজেক্টে যাবে।");
                        renderMainMenu($chatId, $state);
                    } elseif ($data === 'menu_status') {
                        answerCallback($cbId, 'স্ট্যাটাস আনা হচ্ছে...');
                        renderStatusView($chatId, $state);
                    } elseif ($data === 'menu_new_task') {
                        answerCallback($cbId, 'নতুন টাস্ক');
                        sendMsg($chatId, "➕ *নতুন টাস্ক শুরু করতে:*\nসরাসরি আপনার কাঙ্ক্ষিত কাজের বিস্তারিত লিখে পাঠান। স্বয়ংক্রিয়ভাবে অ্যান্টিগ্রাভিটিতে এক্সিকিউট হবে!");
                    } elseif ($data === 'prompt_help') {
                        answerCallback($cbId);
                        sendMsg($chatId, "💬 *প্রম্পট দেওয়ার নিয়ম:*\nআপনি শুয়ে শুয়ে মোবাইলের ভয়েস টাইপিং বা টেক্সট লিখে যেকোনো মেসেজ দিন, সাথে সাথে তা কম্পিউটারের অ্যান্টিগ্রাভিটিতে পৌঁছে যাবে!");
                    } elseif (strpos($data, 'switch_') === 0) {
                        $prefix = substr($data, 7);
                        // Find matching conversation
                        $recent = getRecentProjects(10);
                        foreach ($recent as $p) {
                            if (strpos($p['id'], $prefix) === 0) {
                                $state['active_conv_id'] = $p['id'];
                                $state['active_proj_name'] = $p['name'] . ' (' . $p['preview'] . ')';
                                saveState($state);
                                answerCallback($cbId, 'প্রজেক্ট সুইচ সম্পন্ন!');
                                sendMsg($chatId, "🎯 *সক্রিয় প্রজেক্ট পরিবর্তন করা হয়েছে:*\n\n📁 *{$state['active_proj_name']}*\n🆔 `{$state['active_conv_id']}`\n\n💬 এখন আপনি যা লিখবেন, তা এই প্রজেক্টের কনভার্সেশনে যাবে!");
                                break;
                            }
                        }
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
                        renderRecentProjectsMenu($chatId, $state);
                    } elseif ($text === '📊 লাইভ স্ট্যাটাস ও লিংক' || $text === '/status') {
                        renderStatusView($chatId, $state);
                    } elseif ($text === '🔄 রিফ্রেশ') {
                        renderMainMenu($chatId, $state);
                    } else {
                        // User sent a prompt/instruction!
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
