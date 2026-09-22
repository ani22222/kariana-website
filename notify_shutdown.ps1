# =========================================================================
# Antigravity Telegram Shutdown Notifier
# Sends instant Telegram alert when Windows is shutting down or logging off
# =========================================================================

$token  = "8811158752:AAEKrP4XvGXDuw3NQKUGZLw6a-ooisnIK_8"
$chatId = "1827362508"
$time   = (Get-Date).ToString("dd MMM yyyy, hh:mm tt")

$gitLast = ""
try {
    $gitLast = git -C "c:\xampp\htdocs\Kariana Website" log -1 --pretty=format:"%h - %s" 2>$null
} catch {}

$text = @"
🛑 *কম্পিউটার শাটডাউন / পাওয়ার অফ হচ্ছে...*
━━━━━━━━━━━━━━━━━━━━
⏰ *সময়:* $time
💻 *পিসি স্ট্যাটাস:* সিস্টেম শাটডাউন শুরু হয়েছে
📁 *সক্রিয় প্রজেক্ট:* কারিয়ানা ওয়েবসাইট
🌿 *লেটেস্ট কমিট:* $gitLast
💾 *ডাটা স্ট্যাটাস:* সমস্ত প্রজেক্ট ফাইল ও স্টেট সংরক্ষিত আছে।
🔌 *পাওয়ার:* কম্পিউটার এখন বন্ধ হচ্ছে।

🟢 কম্পিউটার পুনরায় চালু হওয়া মাত্রই অ্যান্টিগ্রাভিটি ও বট অটো-কানেক্ট হয়ে আপনাকে লাইভ আপডেট দেবে!
"@

$body = @{
    chat_id                  = $chatId
    text                     = $text
    parse_mode               = "Markdown"
    disable_web_page_preview = $true
}

try {
    $res = Invoke-RestMethod -Uri "https://api.telegram.org/bot$token/sendMessage" -Method POST -Body $body -TimeoutSec 8
    Write-Host "Shutdown notification sent successfully!"
} catch {
    Write-Host "Failed to send: $_"
}
