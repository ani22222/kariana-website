## 2026-09-23T16:05:00Z

You are the Project Orchestrator (orchestrator_gen3) for Kariana Website (কারিয়ানা কুরআন).

Working directory: c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen3
Project workspace: c:\xampp\htdocs\Kariana Website
Authoritative User Request: c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md (specifically the latest request under ## 2026-09-23T16:03:15Z).

Key Project Rules & Environment:
- Dedicated Port: 8015. Always bind to 0.0.0.0. Multi-device access: http://localhost:8015 and http://192.168.0.100:8015.
- Remote production domain / Cloudflare SSL: https://project.rasel.cloud/kariana/
- GitHub repo: https://github.com/ani22222/kariana-website
- Telegram Bot: @karianaquranbot (Owner ID: 1827362508)
- Telegram accounts config: telegram_accounts.json, telegram_bot_daemon.php, telegram_config.php

Requirements to execute and verify:
R1. Hero Section 3D Book Flipbook & Zero-CLS Auto-Slider (5-second auto-slide, progress indicator, touch/hover pause, rigid zero-CLS bounding box, 5-page sample flipbook reader modal with para01_proof_p1..p5).
R2. Main Admin Dashboard Telegram Hub & Multi-Admin Assignment (/admin live bot status, ping, config, incoming log, 1-click multi-admin assignment for Owner Rasel Gazi, Founder Maulana Saddam Hussain, GM).
R3. Referral Link & Deep-Linking System (?start=_tgr_...) capture, storage, and dynamic referral link generator in Director and Teacher dashboards.
R4. Dual-Channel Automation & Interactive Action Cards (Sabak class inauguration, student admissions, director support questions with [Approve], [Telegram direct reply], [WhatsApp chat] buttons; synchronized web and bot states).
R5. Telegram In-App Web App (TWA) Integration (persistent menu button '📖 অ্যাপ খুলুন' via setChatMenuButton pointing to https://project.rasel.cloud/kariana/).
R6. Android Studio Hybrid Application Finalization (Kotlin / WebView in android-app/ with splash, offline caching, file/camera upload, domain sync).

Maintain your progress in c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen3\progress.md and BRIEFING.md.
When all requirements and acceptance criteria are fully met and verified, report completion back to parent sentinel so a Victory Audit can be initiated.

## 2026-09-23T16:06:05Z

CRITICAL USER REQUIREMENT UPDATE (R7):

The user has specified an additional core notification & security requirement that must be incorporated into your decomposition, implementation, and verification:

### R7. Centralized Categorized Telegram Notification Engine with Concise Summaries
1. **Concise Format by Default (tl;dr)**:
   - All notifications sent to the Super Admin (chat_id: 1827362508 / assigned admins) must be short, clear, and concise (2-3 lines summary).
   - Each message must include an inline button: `[🔍 বিস্তারিত দেখুন]` (View Full Details), allowing the admin to expand or view full data if needed.
2. **Comprehensive Coverage of All Website & Platform Events**:
   - The assigned Super Admin must receive instant notifications for:
     a) **Book Orders**: `🏷️ [বই অর্ডার]` (Order #ID, Book name, Total BDT, Customer name/phone, `[🔍 বিস্তারিত]`, `[📱 WhatsApp]`).
     b) **Blog Comments**: `💬 [ব্লগ কমেন্ট]` (Blog title, commenter name, comment snippet, `[🔍 বিস্তারিত]`, `[✅ অনুমোদন/ডিলিট]`).
     c) **Student Admissions / Course Enrollments**: `🎓 [ছাত্র ভর্তি]` (Student name, course, phone, `[🔍 বিস্তারিত]`).
     d) **Teacher / Muallim Sabak Class**: `📚 [মুয়াল্লিম সবক ক্লাস]` (Teacher name, district, batch info, `[✅ অনুমোদন]`, `[🔍 বিস্তারিত]`, `[📱 WhatsApp]`).
     e) **District Director Activities**: `👔 [জেলা পরিচালক]` (Director name, district, activity, `[🔍 বিস্তারিত]`).
     f) **Security & Admin Logins**: `🛡️ [সিকিউরিটি ও লগইন]` (Admin login attempt, IP, success/failure, timestamp, `[🔍 বিস্তারিত]`).
     g) **System / Platform Exceptions**: `⚠️ [সিস্টেম অ্যালার্ট]` (Error description, route, timestamp).
3. **Strict Categorization**:
   - Every message MUST explicitly indicate the category tag at the top so the admin instantly knows what type of activity occurred.

This requirement has been appended verbatim to `ORIGINAL_REQUEST.md`. Ensure `TelegramNotificationDispatcher.php` or `UnifiedMessagingService.php` is hooked into the relevant controllers (Order, Blog, Course/Admission, Auth/Admin, Sabak) to dispatch these categorized events with concise summaries and inline buttons. Update your plan and progress tracking accordingly.

## 2026-09-23T16:12:32Z

NEW USER ARCHITECTURE REQUIREMENT (R8):

The user has specified an additional core architecture requirement:

### R8. Multi-Tier E-Commerce & Role-Based Book Procurement Engine
1. **Public Direct Order Form (Quick Checkout)**:
   - On-site order modal/page for general students/visitors (Name, Mobile, Delivery Address, Book/Quantity).
   - Admin settings toggle:
     a) Cash on Delivery (COD) [ON / OFF]
     b) Online Payment Gateway (Payment API Integration from ports 8001-8003 / SMS Sync Pay) [ON / OFF]
   - Placing an order dispatches a categorized instant Telegram alert: `🏷️ [বই অর্ডার - #ORD-ID]` with `[🔍 বিস্তারিত দেখুন]`.
2. **3-Tier Role-Based Book Procurement & Pricing Flow**:
   - **Tier 1 (Public MRP)**: Standard website price set in book catalog.
   - **Tier 2 (District Director Wholesale)**: Directors order stock from Central Admin. Super Admin sets custom wholesale pricing per director or district tier in `/admin`.
   - **Tier 3 (Teacher / Madrasa Procurement)**: Teachers order curriculum books through their assigned District Director. The District Director can configure the teacher discount/price within their district range in `/director/dashboard`.
   - Complete tracking of requisitions, stock, and orders across Admin, Director, and Teacher dashboards.

Please ensure the database schema, models, and controllers support this role-based pricing and order pipeline. This requirement has been appended verbatim to `ORIGINAL_REQUEST.md`. Update your plan and progress tracking accordingly.
