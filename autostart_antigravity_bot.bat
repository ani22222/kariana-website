@echo off
rem =========================================================================
rem Antigravity & Telegram Bot Boot-Time Auto-Start Script
rem Automatically runs when PC boots or user logs into Windows
rem =========================================================================

cd /d "c:\xampp\htdocs\Kariana Website"

rem 1. Check & Launch Antigravity IDE if not running
tasklist /FI "IMAGENAME eq Antigravity.exe" 2>NUL | find /I /N "Antigravity.exe">NUL
if "%ERRORLEVEL%"=="1" (
    echo Starting Google Antigravity IDE...
    start "" "C:\Users\UseR\AppData\Local\Programs\antigravity\Antigravity.exe"
    timeout /t 5 >nul
)

rem 2. Check & Launch PHP Web Server (Port 8015) if not running
netstat -ano | findstr ":8015 " | findstr "LISTENING" >nul
if "%ERRORLEVEL%"=="1" (
    echo Starting PHP Built-in Server on 0.0.0.0:8015...
    if not exist "storage\logs" mkdir "storage\logs"
    start /b php -S 0.0.0.0:8015 router.php > storage\logs\php_server.log 2>&1
    timeout /t 2 >nul
)

rem 3. Check & Launch Telegram Bot Daemon
tasklist /FI "IMAGENAME eq php.exe" /V 2>NUL | findstr /I "telegram_bot_daemon" >nul
if "%ERRORLEVEL%"=="1" (
    echo Starting Telegram Bot Daemon with boot notification...
    if not exist "storage\logs" mkdir "storage\logs"
    start /b php telegram_bot_daemon.php --boot > storage\logs\telegram_bot.log 2>&1
)

exit 0
