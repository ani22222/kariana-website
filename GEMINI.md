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

## Global Rules for Antigravity Brain
1. **GitHub Versioning Policy**:
   - Any project or task must be uploaded/maintained on GitHub under account `ani22222`.
   - Continuous commits and pushes must be made as milestones and tasks progress.
2. **Dedicated Port Policy**:
   - Every project runs on its own dedicated port (Port `8015` for Kariana Website) bound to `0.0.0.0`.
3. **Link Reporting Protocol**:
   - Always report (1) Localhost link, (2) Local Wi-Fi IP, (3) Cloudflare Public Tunnel Link, and (4) GitHub Repository.
