<?php
/**
 * Telegram Bot Configuration & Helper Functions
 * Kariana Website — Remote Control via Telegram
 * Bot: @raselcodebot (Integrity)
 */

$secretCreds = file_exists(__DIR__ . '/telegram_token.php') ? include(__DIR__ . '/telegram_token.php') : [];
define('TELEGRAM_BOT_TOKEN', $secretCreds['bot_token'] ?? getenv('TELEGRAM_BOT_TOKEN') ?? '');
define('TELEGRAM_CHAT_ID',   (string)($secretCreds['admin_chat_id'] ?? '1827362508'));
define('TELEGRAM_API_BASE',  'https://api.telegram.org/bot' . TELEGRAM_BOT_TOKEN);

// Project info
define('PROJECT_NAME',       'কারিয়ানা ওয়েবসাইট');
define('PROJECT_PORT',       '8015');
define('CLOUDFLARE_URL',     'https://wireless-docs-camel-mls.trycloudflare.com');
define('GITHUB_URL',         'https://github.com/ani22222/kariana-website');
define('WIFI_IP',            '192.168.0.100');

/**
 * Send a Telegram message to the owner
 */
function tg_send(string $text, string $parse_mode = 'Markdown'): array {
    $url  = TELEGRAM_API_BASE . '/sendMessage';
    $data = [
        'chat_id'    => TELEGRAM_CHAT_ID,
        'text'       => $text,
        'parse_mode' => $parse_mode,
        'disable_web_page_preview' => false,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ?? [];
}

/**
 * Send a Telegram message with inline keyboard buttons
 */
function tg_send_with_buttons(string $text, array $buttons): array {
    $url  = TELEGRAM_API_BASE . '/sendMessage';

    $keyboard = ['inline_keyboard' => $buttons];
    $data = [
        'chat_id'      => TELEGRAM_CHAT_ID,
        'text'         => $text,
        'parse_mode'   => 'Markdown',
        'reply_markup' => json_encode($keyboard),
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true) ?? [];
}

/**
 * Build standard project links footer
 */
function tg_links_footer(): string {
    return "\n\n🔗 *লিংকসমূহ:*\n"
         . "🖥 [Localhost](http://localhost:" . PROJECT_PORT . ")\n"
         . "📶 [Wi\\-Fi LAN](http://" . WIFI_IP . ":" . PROJECT_PORT . ")\n"
         . "🌐 [Live \\(Cloudflare\\)](" . CLOUDFLARE_URL . ")\n"
         . "📦 [GitHub](" . GITHUB_URL . ")\n\n"
         . "⏰ " . date('d M Y, h:i A', strtotime('+6 hours'));
}

/**
 * Send a task-completion summary notification
 */
function tg_notify_task_done(string $task_title, array $changes = [], string $extra = ''): void {
    $text  = "🌟 *" . PROJECT_NAME . " আপডেট*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "✅ *কাজ সম্পন্ন:* " . $task_title . "\n";

    if (!empty($changes)) {
        $text .= "\n📝 *যা করা হয়েছে:*\n";
        foreach ($changes as $change) {
            $text .= "• " . $change . "\n";
        }
    }

    if ($extra) {
        $text .= "\n💬 " . $extra . "\n";
    }

    $text .= tg_links_footer();

    tg_send($text, 'Markdown');
}

/**
 * Send error/alert notification
 */
function tg_notify_error(string $title, string $details = ''): void {
    $text  = "⚠️ *" . PROJECT_NAME . " — সমস্যা*\n";
    $text .= "━━━━━━━━━━━━━━━━━━━━\n";
    $text .= "❌ *" . $title . "*\n";
    if ($details) {
        $text .= "\n```\n" . substr($details, 0, 3000) . "\n```";
    }
    $text .= "\n⏰ " . date('d M Y, h:i A', strtotime('+6 hours'));
    tg_send($text, 'Markdown');
}
