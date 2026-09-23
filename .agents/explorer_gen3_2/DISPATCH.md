# DISPATCH: explorer_gen3_2

## Objective
Survey existing Telegram Bot infrastructure, admin dashboard integration, multi-admin management, referral deep-linking, and Telegram Web App (TWA) configuration in relation to Requirements R2, R3, and R5.

## Context Files
- `c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md` (Read fully, especially sections ## 2026-09-23T16:03:15Z and ## 2026-09-23T16:05:35Z)
- `c:\xampp\htdocs\Kariana Website\telegram_bot_daemon.php`
- `c:\xampp\htdocs\Kariana Website\telegram_config.php`
- `c:\xampp\htdocs\Kariana Website\telegram_accounts.json`
- Admin views & controllers: `app/Controllers/Admin/`, `app/Views/admin/`
- Database schema and tables for telegram logs, admins, and referrals.

## Specific Investigation Tasks
1. **R2 (Admin Dashboard Telegram Hub & Multi-Admin Assignment)**:
   - What Telegram hub or monitoring card currently exists in `/admin`?
   - How are bot status, ping, and configuration retrieved and displayed?
   - Is there a real-time incoming Telegram message log showing sender user ID, full name, and username? Where are messages logged?
   - How are admins stored (in `telegram_accounts.json`, database `telegram_admins` table, or config)?
   - How can 1-Click Multi-Admin Assignment be cleanly implemented for Owner Rasel Gazi (1827362508), Founder Maulana Saddam Hussain, and General Manager?
2. **R3 (Referral Link & Deep-Linking System `?start=_tgr_...`)**:
   - How does the Telegram bot currently parse `/start` parameters? Does it support `_tgr_...` payloads?
   - Where are referral visits/conversions stored in the DB (table schema, foreign keys)?
   - How do Director and Teacher dashboards generate and display dynamic referral links?
   - How are attributed student admissions and book orders tracked back to referrers?
3. **R5 (Telegram In-App Web App / TWA Integration)**:
   - Does `telegram_bot_daemon.php` or a setup script call `setChatMenuButton`?
   - How is the persistent menu button "📖 অ্যাপ খুলুন" configured to point to `https://project.rasel.cloud/kariana/`?
   - What needs to be added/tested to ensure instant registration with Telegram Bot API?

## Deliverable
Write your detailed findings and architectural recommendations to `c:\xampp\htdocs\Kariana Website\.agents\explorer_gen3_2\analysis.md` and deliver a self-contained `handoff.md`.

## 2026-09-23T16:06:50Z
You are explorer_gen3_2.
Working directory: c:\xampp\htdocs\Kariana Website\.agents\explorer_gen3_2
Workspace: c:\xampp\htdocs\Kariana Website

Read your instructions in c:\xampp\htdocs\Kariana Website\.agents\explorer_gen3_2\DISPATCH.md and c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md.
Investigate existing Telegram Bot infrastructure, admin dashboard integration, multi-admin management, referral deep-linking, and Telegram Web App (TWA) configuration in relation to Requirements R2, R3, and R5.

Document your comprehensive findings in c:\xampp\htdocs\Kariana Website\.agents\explorer_gen3_2\analysis.md and write a complete, self-contained handoff report at c:\xampp\htdocs\Kariana Website\.agents\explorer_gen3_2\handoff.md.
Notify orchestrator when done via send_message.
