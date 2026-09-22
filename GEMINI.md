# Project Configuration & Global Rules: Kariana Website (কারিয়ানা কুরআন)

## Dedicated Port
- **Dedicated Port**: `8015`
- **Host**: `0.0.0.0`
- **Command to Run**: `php -S 0.0.0.0:8015 router.php`
- **Cloudflare Tunnel**: `.\bin\cloudflared.exe tunnel --url http://127.0.0.1:8015`

## Standard Access Links
Whenever this project or any project is served, always provide all four standard links:
1. **Local Link (This PC)**: `http://localhost:8015`
2. **Local Wi-Fi IP (LAN / Mobile on same Wi-Fi)**: `http://192.168.0.100:8015` (Private, not public)
3. **Cloudflare Live Web Tunnel (Global WAN)**: `https://rev-mysql-stops-ext.trycloudflare.com` (Accessible from outside mobile data / anywhere)
4. **GitHub Repository**: `https://github.com/ani22222/kariana-website`

## Telegram Remote Control Daemon
- **Bot Username**: `@raselcodebot`
- **Owner Chat ID**: `1827362508`
- **Daemon Script**: `php telegram_bot_daemon.php`
- **Features**: Interactive Menu, Instant Project Switcher, Real-time Transcript Streaming, One-Click Actions.

## Global Rules for Antigravity Brain
1. **GitHub Versioning Policy**:
   - Any project or task must be uploaded/maintained on GitHub under account `ani22222`.
   - Continuous commits and pushes must be made as milestones and tasks progress.
2. **Dedicated Port Policy**:
   - Every project runs on its own dedicated port (Port `8015` for Kariana Website) bound to `0.0.0.0`.
3. **Link Reporting Protocol**:
   - Always report (1) Localhost link, (2) Local Wi-Fi IP, (3) Cloudflare Public Tunnel Link, and (4) GitHub Repository.
4. **AI Model Quota & Rate Limit Proactive Alerting Protocol (90-95% Threshold)**:
   - Continuously monitor model quota and step limits.
   - When quota usage approaches 90-95% or exhaustion is near, proactively alert the user on Telegram with red emoji (🔴/⚠️) and provide 1-click buttons to immediately switch accounts, re-authorize, or switch to quota-saving models (Flash-Lite) before any interruption occurs.
5. **24/7 Universal Personal Assistant Protocol on Telegram**:
   - The bot is a full-fledged personal AI companion, not merely a coding task runner.
   - Never send generic receipt templates ("কাজ গ্রহণ করা হয়েছে") for casual messages, greetings, or questions.
   - Provide warm, direct, helpful conversational responses in Bengali via the fast Flash-Lite engine with 1-click action buttons.
   - User is resting in bed and must be able to control, query, and command everything 100% remotely without touching the PC.
