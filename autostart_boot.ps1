# =========================================================================
# Antigravity & Telegram Bot Boot-Time Auto-Start
# Runs silently on PC boot / Windows logon
# =========================================================================

$projectDir = "c:\xampp\htdocs\Kariana Website"
Set-Location $projectDir

# 1. Wait for Internet / Network Connectivity (up to 20 seconds)
$connected = $false
for ($i = 0; $i -lt 20; $i++) {
    try {
        $ips = [System.Net.Dns]::GetHostAddresses("api.telegram.org")
        if ($ips) {
            $connected = $true
            break
        }
    } catch {
        Start-Sleep -Seconds 1
    }
}

# 2. Ensure Antigravity IDE is running
$agyProc = Get-Process "Antigravity*" -ErrorAction SilentlyContinue
if (-not $agyProc) {
    Write-Host "[BOOT] Starting Antigravity IDE..."
    Start-Process "C:\Users\UseR\AppData\Local\Programs\antigravity\Antigravity.exe"
    Start-Sleep -Seconds 4
}

# 3. Ensure PHP Built-in Server on Port 8015 is running
$serverRunning = $false
try {
    $tcp = New-Object System.Net.Sockets.TcpClient
    $tcp.Connect("127.0.0.1", 8015)
    $serverRunning = $tcp.Connected
    $tcp.Close()
} catch {
    $serverRunning = $false
}

if (-not $serverRunning) {
    Write-Host "[BOOT] Starting PHP Server on 0.0.0.0:8015..."
    Start-Process "php" -ArgumentList "-S 0.0.0.0:8015 router.php" -WorkingDirectory $projectDir -WindowStyle Hidden
    Start-Sleep -Seconds 2
}

# 4. Ensure Telegram Bot Daemon is running with full file logging
$runningDaemon = Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" -ErrorAction SilentlyContinue |
    Where-Object { $_.CommandLine -like "*telegram_bot_daemon.php*" }

if (-not $runningDaemon) {
    Write-Host "[BOOT] Starting Telegram Bot Daemon with boot notification..."
    Start-Process "cmd.exe" -ArgumentList "/c php telegram_bot_daemon.php --boot >> storage\logs\telegram_bot.log 2>&1" -WorkingDirectory $projectDir -WindowStyle Hidden
}
