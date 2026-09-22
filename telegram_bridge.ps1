# ============================================================
# telegram_bridge.ps1 — Kariana Website Telegram Remote Control
# Bot: @raselcodebot (Integrity)
# Owner Chat ID: 1827362508
# ============================================================

$BOT_TOKEN  = "8811158752:AAEKrP4XvGXDuw3NQKUGZLw6a-ooisnIK_8"
$CHAT_ID    = "1827362508"
$API_BASE   = "https://api.telegram.org/bot$BOT_TOKEN"
$PROJECT_DIR = "c:\xampp\htdocs\Kariana Website"
$AGY_LOG    = "$PROJECT_DIR\telegram_agy.log"

$LOCALHOST_URL  = "http://localhost:8015"
$WIFI_URL       = "http://192.168.0.100:8015"
$CLOUDFLARE_URL = "https://rev-mysql-stops-ext.trycloudflare.com"
$GITHUB_URL     = "https://github.com/ani22222/kariana-website"

$script:LastUpdateId = 0
$script:IsRunning    = $true

# ─── Helper Functions ────────────────────────────────────────

function Send-TelegramMessage {
    param(
        [string]$Text,
        [string]$ParseMode = "Markdown"
    )
    $body = @{
        chat_id              = $CHAT_ID
        text                 = $Text
        parse_mode           = $ParseMode
        disable_web_page_preview = $false
    }
    try {
        $response = Invoke-RestMethod -Uri "$API_BASE/sendMessage" `
            -Method POST -Body $body -TimeoutSec 15
        return $response
    } catch {
        Write-Host "[ERROR] Telegram send failed: $_" -ForegroundColor Red
    }
}

function Get-LinksFooter {
    $now = (Get-Date).ToString("dd MMM yyyy, hh:mm tt")
    return @"

🔗 *লিংকসমূহ:*
🖥 Localhost: $LOCALHOST_URL
📶 Wi-Fi LAN: $WIFI_URL
🌐 Live (Cloudflare): $CLOUDFLARE_URL
📦 GitHub: $GITHUB_URL

⏰ $now
"@
}

function Send-WelcomeMessage {
    $msg = @"
🌟 *কারিয়ানা ওয়েবসাইট — টেলিগ্রাম কন্ট্রোল প্যানেল*
━━━━━━━━━━━━━━━━━━━━
🤖 Bot: @raselcodebot চালু হয়েছে!

📋 *কমান্ড তালিকা:*
/task [নির্দেশ] — Antigravity-কে কাজ দাও
/status — প্রজেক্টের বর্তমান অবস্থা
/git — সর্বশেষ git log দেখো
/help — সব কমান্ড দেখো
/stop — Bridge বন্ধ করো

💡 *উদাহরণ:*
`/task ব্লগ পেজের ডিজাইন ঠিক করো`
`/task fix the blog page layout`
$(Get-LinksFooter)
"@
    Send-TelegramMessage -Text $msg
}

function Handle-StatusCommand {
    $phpVersion = php -r "echo PHP_VERSION;" 2>$null
    $gitBranch  = git -C $PROJECT_DIR rev-parse --abbrev-ref HEAD 2>$null
    $gitCommit  = git -C $PROJECT_DIR log --oneline -1 2>$null

    $msg = @"
📊 *কারিয়ানা ওয়েবসাইট — বর্তমান অবস্থা*
━━━━━━━━━━━━━━━━━━━━
🟢 Bridge: চালু আছে
🐘 PHP: $phpVersion
🌿 Git Branch: $gitBranch
📝 শেষ Commit: $gitCommit
$(Get-LinksFooter)
"@
    Send-TelegramMessage -Text $msg
}

function Handle-GitCommand {
    $gitLog = git -C $PROJECT_DIR log --oneline -5 2>$null
    $msg = @"
📦 *সর্বশেষ ৫টি Commit:*
``````
$gitLog
``````
📌 GitHub: $GITHUB_URL
"@
    Send-TelegramMessage -Text $msg
}

function Handle-HelpCommand {
    $msg = @"
📋 *কমান্ড তালিকা — কারিয়ানা টেলিগ্রাম কন্ট্রোল*
━━━━━━━━━━━━━━━━━━━━
/task [নির্দেশ]
  → Antigravity-কে কাজ দাও (বাংলা বা ইংরেজি)
  → উদাহরণ: `/task ব্লগ পেজ ঠিক করো`

/status
  → প্রজেক্টের বর্তমান অবস্থা দেখো

/git
  → সর্বশেষ ৫টি git commit দেখো

/help
  → এই সাহায্য বার্তা দেখো

/stop
  → Bridge বন্ধ করো (কম্পিউটারে থাকতে হবে)

💡 যেকোনো বার্তা দিলেও AGY-তে পাঠানো হবে!
"@
    Send-TelegramMessage -Text $msg
}

function Handle-TaskCommand {
    param([string]$TaskText)

    Send-TelegramMessage -Text "⏳ *কাজ শুরু হচ্ছে...*`n`n📝 নির্দেশ: $TaskText`n`nঅনুগ্রহ করে অপেক্ষা করুন... 🔄"

    # Log the request
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    "$timestamp | TASK: $TaskText" | Add-Content -Path $AGY_LOG -Encoding UTF8

    # Run AGY CLI with the task
    $escapedTask = $TaskText -replace '"', '\"'
    
    try {
        Write-Host "[AGY] Running task: $TaskText" -ForegroundColor Cyan
        
        # Use agy chat to send the message
        $agyProcess = Start-Process -FilePath "agy" `
            -ArgumentList "chat", "--message", $escapedTask, "--conversation", "94596634-65c0-432c-a4d3-6aa058846c61" `
            -WorkingDirectory $PROJECT_DIR `
            -RedirectStandardOutput "$PROJECT_DIR\telegram_agy_out.txt" `
            -RedirectStandardError "$PROJECT_DIR\telegram_agy_err.txt" `
            -NoNewWindow -PassThru -Wait

        $output = ""
        if (Test-Path "$PROJECT_DIR\telegram_agy_out.txt") {
            $output = Get-Content "$PROJECT_DIR\telegram_agy_out.txt" -Raw -Encoding UTF8
        }

        if ($output -and $output.Length -gt 10) {
            # Truncate if too long for Telegram (max 4096 chars)
            $summary = $output.Substring(0, [Math]::Min($output.Length, 3500))
            $msg = @"
✅ *Antigravity সাড়া দিয়েছে:*
━━━━━━━━━━━━━━━━━━━━
$summary
$(Get-LinksFooter)
"@
        } else {
            $msg = "✅ *কাজ Antigravity-তে পাঠানো হয়েছে।*`nFull response IDE-তে দেখুন।$(Get-LinksFooter)"
        }

        Send-TelegramMessage -Text $msg

    } catch {
        $errMsg = "⚠️ AGY CLI ত্রুটি: $_`n`nকম্পিউটারে IDE খুলুন এবং ম্যানুয়ালি চেষ্টা করুন।"
        Send-TelegramMessage -Text $errMsg
        Write-Host "[ERROR] AGY failed: $_" -ForegroundColor Red
    }
}

function Process-Message {
    param($Update)

    $message = $Update.message
    if (-not $message) { return }

    # Security: Only respond to owner
    if ($message.from.id -ne [int]$CHAT_ID -and $message.chat.id -ne [int]$CHAT_ID) {
        Write-Host "[SECURITY] Ignored message from unknown user: $($message.from.id)" -ForegroundColor Yellow
        return
    }

    $text = $message.text
    if (-not $text) { return }

    Write-Host "[MSG] Received: $text" -ForegroundColor Green

    # Route commands
    if ($text -match "^/start") {
        Send-WelcomeMessage
    } elseif ($text -match "^/status") {
        Handle-StatusCommand
    } elseif ($text -match "^/git") {
        Handle-GitCommand
    } elseif ($text -match "^/help") {
        Handle-HelpCommand
    } elseif ($text -match "^/stop") {
        Send-TelegramMessage -Text "🛑 *Bridge বন্ধ হচ্ছে...*`nআবার চালু করতে: `start_live.bat` রান করুন।"
        $script:IsRunning = $false
    } elseif ($text -match "^/task\s+(.+)") {
        $taskText = $Matches[1]
        Handle-TaskCommand -TaskText $taskText
    } else {
        # Any plain message — treat as a task
        Handle-TaskCommand -TaskText $text
    }
}

# ─── Main Polling Loop ────────────────────────────────────────

Write-Host ""
Write-Host "╔════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  কারিয়ানা টেলিগ্রাম Bridge চালু        ║" -ForegroundColor Cyan
Write-Host "║  Bot: @raselcodebot                    ║" -ForegroundColor Cyan
Write-Host "║  Chat ID: $CHAT_ID               ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Send startup notification
Send-WelcomeMessage
Write-Host "[OK] Startup notification sent to Telegram!" -ForegroundColor Green
Write-Host "[OK] Polling started. Press Ctrl+C to stop." -ForegroundColor Green
Write-Host ""

while ($script:IsRunning) {
    try {
        $params = @{
            offset  = $script:LastUpdateId + 1
            timeout = 30
        }
        $response = Invoke-RestMethod -Uri "$API_BASE/getUpdates" `
            -Method GET -Body $params -TimeoutSec 35

        if ($response.ok -and $response.result.Count -gt 0) {
            foreach ($update in $response.result) {
                $script:LastUpdateId = $update.update_id
                Process-Message -Update $update
            }
        }
    } catch {
        Write-Host "[WARN] Polling error (retrying in 5s): $_" -ForegroundColor Yellow
        Start-Sleep -Seconds 5
    }
}

Write-Host "[STOPPED] Telegram Bridge বন্ধ হয়েছে।" -ForegroundColor Red
