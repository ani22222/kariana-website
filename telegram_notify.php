<?php
/**
 * telegram_notify.php — Send project summary to Telegram
 * Usage: php telegram_notify.php "Task Title" "Change 1" "Change 2" ...
 *
 * Kariana Website Remote Notification System
 */

require_once __DIR__ . '/telegram_config.php';

// CLI usage
if (php_sapi_name() === 'cli') {
    $args = array_slice($argv, 1);
    if (empty($args)) {
        echo "Usage: php telegram_notify.php \"Task Title\" \"Change 1\" \"Change 2\"\n";
        exit(1);
    }

    $title   = $args[0];
    $changes = array_slice($args, 1);

    $result = tg_notify_task_done($title, $changes);

    if ($result['ok'] ?? false) {
        echo "✅ টেলিগ্রামে পাঠানো সম্পন্ন!\n";
    } else {
        echo "❌ ব্যর্থ: " . json_encode($result) . "\n";
        exit(1);
    }
    exit(0);
}

// Web usage — send project status
$status_text  = "📊 *" . PROJECT_NAME . " — বর্তমান অবস্থা*\n";
$status_text .= "━━━━━━━━━━━━━━━━━━━━\n";
$status_text .= "🟢 সার্ভার চালু আছে (Port: " . PROJECT_PORT . ")\n";
$status_text .= "🕐 সময়: " . date('d M Y, h:i A', strtotime('+6 hours')) . "\n";
$status_text .= tg_links_footer();

$result = tg_send($status_text, 'Markdown');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
