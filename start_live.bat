@echo off
title Kariana Quran Web Portal & Cloudflare Live Runner
color 0A
echo =====================================================================
echo    Kariana Quran Portal - Dedicated Port 8015 & Cloudflare Live
echo =====================================================================
echo.
echo [1/2] Starting PHP Built-in Server on 0.0.0.0:8015...
start /b php -S 0.0.0.0:8015 router.php > storage\logs\php_server.log 2>&1
timeout /t 2 >nul

echo [2/2] Starting Cloudflare Tunnel...
echo Localhost Link : http://localhost:8015
echo Local Wi-Fi IP : http://192.168.0.100:8015
echo.
echo Opening Cloudflare Tunnel (Press Ctrl+C to stop)...
.\bin\cloudflared.exe tunnel --url http://127.0.0.1:8015
