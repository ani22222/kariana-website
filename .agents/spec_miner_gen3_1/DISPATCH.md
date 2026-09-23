# DISPATCH: spec_miner_gen3_1

## Objective
Investigate specifications, controllers, models, and notification engines for Dual-Channel Action Cards & State Synchronization (R4) and Centralized Categorized Telegram Notification Engine with Concise Summaries (R7).

## Context Files
- `c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md` (Read fully, especially sections ## 2026-09-23T16:03:15Z and ## 2026-09-23T16:05:35Z)
- Existing services: `app/Services/`, `TelegramNotificationDispatcher.php`, `UnifiedMessagingService.php` or similar
- Controllers: `app/Controllers/` (OrderController, BlogController, AdmissionController, SabakController, AuthController, Admin controllers)
- Telegram daemon callback query handling in `telegram_bot_daemon.php`
- Database tables: admissions, sabak_classes, orders, blog_comments, audit/logs

## Specific Investigation Tasks
1. **R4 (Dual-Channel Automation & Interactive Action Cards)**:
   - For Sabak class inauguration, student admissions, and director support questions:
     * What models and database tables store these entities?
     * How are interactive action cards dispatched to assigned Telegram admins?
     * What inline buttons are required: `[✅ অনুমোদন করুন]`, `[💬 টেলিগ্রাম থেকে সরাসরি রিপ্লাই দিন]`, `[📱 WhatsApp চ্যাট খুলুন]`?
     * How is callback query handled when an admin clicks `[✅ অনুমোদন করুন]` in Telegram?
     * How does approving from Telegram update the web dashboard in real time, and vice versa (web admin approval updating Telegram message)?
2. **R7 (Centralized Categorized Telegram Notification Engine with Concise Summaries)**:
   - Concise format specification (tl;dr 2-3 lines + inline button `[🔍 বিস্তারিত দেখুন]`).
   - The 7 required categories:
     a) `🏷️ [বই অর্ডার]` (Order #ID, Book name, Total BDT, Customer name/phone, `[🔍 বিস্তারিত]`, `[📱 WhatsApp]`)
     b) `💬 [ব্লগ কমেন্ট]` (Blog title, commenter name, comment snippet, `[🔍 বিস্তারিত]`, `[✅ অনুমোদন/ডিলিট]`)
     c) `🎓 [ছাত্র ভর্তি]` (Student name, course, phone, `[🔍 বিস্তারিত]`)
     d) `📚 [মুয়াল্লিম সবক ক্লাস]` (Teacher name, district, batch info, `[✅ অনুমোদন]`, `[🔍 বিস্তারিত]`, `[📱 WhatsApp]`)
     e) `👔 [জেলা পরিচালক]` (Director name, district, activity, `[🔍 বিস্তারিত]`)
     f) `🛡️ [সিকিউরিটি ও লগইন]` (Admin login attempt, IP, success/failure, timestamp, `[🔍 বিস্তারিত]`)
     g) `⚠️ [সিস্টেম অ্যালার্ট]` (Error description, route, timestamp)
   - What service should centralize this? How should controllers hook into it without blocking user requests?
   - How does `[🔍 বিস্তারিত দেখুন]` expand/show full details (via Telegram callback answer, editMessageText, or link)?

## Deliverable
Write your detailed findings and architectural specifications to `c:\xampp\htdocs\Kariana Website\.agents\spec_miner_gen3_1\analysis.md` and deliver a self-contained `handoff.md`.

## 2026-09-23T16:06:50Z
You are spec_miner_gen3_1.
Working directory: c:\xampp\htdocs\Kariana Website\.agents\spec_miner_gen3_1
Workspace: c:\xampp\htdocs\Kariana Website

Read your instructions in c:\xampp\htdocs\Kariana Website\.agents\spec_miner_gen3_1\DISPATCH.md and c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md.
Investigate specifications, controllers, models, and notification engines for Dual-Channel Action Cards & State Synchronization (R4) and Centralized Categorized Telegram Notification Engine with Concise Summaries (R7).

Document your comprehensive findings and detailed technical specifications in c:\xampp\htdocs\Kariana Website\.agents\spec_miner_gen3_1\analysis.md and write a complete, self-contained handoff report at c:\xampp\htdocs\Kariana Website\.agents\spec_miner_gen3_1\handoff.md.
Notify orchestrator when done via send_message.
