# Original User Request

## Initial Request — 2026-09-22T14:03:55Z

A blazing-fast, ultra-SEO-optimized, multifunctional Islamic educational portal and CMS for Kariana Quran (কারিয়ানা কুরআন) in Bangladesh. Built with modern, clean PHP 8.2 (PDO MVC architecture) + MySQL + Tailwind CSS + Alpine.js, engineered specifically for 100% Shared Hosting compatibility (Hostinger File Manager, cPanel `public_html`, and XAMPP) without requiring a VPS or Node.js background process, featuring Royal Islamic Emerald Green and Golden aesthetics.

Working directory: `c:\xampp\htdocs\Kariana Website`
Dedicated Port: `8015` (`http://localhost:8015` and `http://192.168.0.100:8015`)
Integrity mode: development

## Requirements

### R1. SEO-Dominant Islamic Organizational Portal & Blog System
Build a high-speed, mobile-first web portal with automated Schema.org JSON-LD markup (`Organization`, `Article`, `Course`, `FAQPage`, `BreadcrumbList`), dynamic XML sitemaps, OpenGraph/Twitter social cards, clean Bengali slug URLs (`/blog/সহজ-পদ্ধতিতে-কুরআন-শেখা`), and category/tag indexing optimized for Google Bangladesh search engine rankings.

### R2. Comprehensive Educational & Publications Admin CMS
Develop a clean, lightweight, and secure admin dashboard with zero WordPress bloat:
- **Blog & News Management**: Rich-text/Markdown editor, live Google SERP preview (Desktop/Mobile), Bengali slug generator, categories and tags.
- **Courses & Admissions Hub**: Course catalog management (কোর্স তালিকা - নাজেরা, হিফজ, ক্বেরাত, তাজবীদ) and online student admission/inquiry lead database with status tracking and CSV export.
- **Kariana Books & Publications**: Catalog for official books, syllabuses, and educational publications with preview and ordering/download links.
- **Dynamic Pages & SEO Control**: Custom pages (About Us, Methodology, Teachers, Branches, Contact) with per-page meta titles, descriptions, and canonical URLs.
- **Security**: Prepared PDO statements, CSRF protection, secure password hashing, and session authentication.

### R3. Islamic Daily Utilities & Dedicated Quran App Bridge
- **Interactive Tools**: Dynamic prayer times (calculated for Dhaka and selectable across 64 districts of Bangladesh with Islamic Foundation conventions), Sehri & Iftar timetable, Nisab-based Zakat calculator, and interactive digital Tasbeeh counter.
- **Quran Reader Bridge**: Elegant UI launchpad and integration portal linking seamlessly to Kariana Quran's dedicated web reader application (featuring Kariana's verified font, audio recitations, and Bengali translations).

### R4. QR-Code Book Scanning & Video Lesson Gateway (Phased Roadmap)
- Secure, instant QR scanner / direct URL gateway (`/scan?page=...` or `/lesson/:id`) matching QR codes in Kariana Quran's physical books and Mushaf editions.
- Seamless, distraction-free video player rendering the exact Tajweed/lesson video for that specific printed page.

### R5. Bangladeshi Mobile-First UX, Ultra-Fast Shared Hosting Delivery & Phased Task Checklist
- 100% Shared Hosting and XAMPP drag-and-drop ready (no VPS or Node daemon needed).
- Authentic Islamic visual identity: Royal Islamic Emerald Green (`#064e3b` / `#047857`) and Warm Quranic Gold (`#d97706`) with clean Bengali typography (`Hind Siliguri` / `SolaimanLipi`).
- Maintain a persistent, verifiable phased checklist where completed milestones are marked with checkboxes (`[x]`).

## Acceptance Criteria

### Phase 1: Foundation, CMS, SEO & Organizational Platform (Active Milestone)
- [ ] 100% Shared-Hosting ready (works directly out of the box in Hostinger `public_html` / cPanel / XAMPP).
- [ ] Core Web Vitals optimization (fast mobile TTFB, responsive layout on all devices).
- [ ] Educational CMS: Blog/News, Bengali slug URLs, Category/Tags, SEO Meta tags & live preview.
- [ ] Courses catalog and Online Admission form with student database list in admin.
- [ ] Kariana Books & Publications showcase catalog.
- [ ] Islamic Utilities: Prayer times (64 districts), Sehri/Iftar, Zakat calculator, Digital Tasbeeh.
- [ ] Multi-device preview verified on `http://localhost:8015` and `http://192.168.0.100:8015`.

### Phase 2: QR Book Scanner & Educational Video Gateway
- [ ] Dedicated scan landing page for printed book QR codes (`/scan?code=...`).
- [ ] Admin management to link book pages to video lesson URLs (YouTube / Cloud video).

### Phase 3: Quran App Integration & Advanced Subdomain Bridge
- [ ] Seamless SSO/navigation bridging to the standalone Kariana Quran reader application.

## 2026-09-23T16:03:15Z

A unified, high-performance web platform, Android hybrid mobile application, and dual-channel Telegram management ecosystem for Kariana Quran (কারিয়ানা কুরআন) in Bangladesh, deployed on Spaceship Cloud VPS with Cloudflare SSL at `https://project.rasel.cloud/kariana/`.

Working directory: `c:\xampp\htdocs\Kariana Website`
Integrity mode: development

## Requirements

### R1. Hero Section 3D Book Flipbook & Zero-CLS Auto-Slider
- Implement smooth 5-second auto-sliding with an active progress indicator bar and touch/hover pause.
- Maintain a rigid, responsive bounding box ensuring zero layout shift (CLS = 0) during slide transitions.
- Interactive 3D Sample Page Viewer Modal: clicking any book opens an animated book reader modal displaying 5 authentic proofing pages (`para01_proof_p1..p5`) with page-flip animations and an instant WhatsApp order trigger.

### R2. Main Admin Dashboard Telegram Hub & Multi-Admin Assignment
- Top-level card in `/admin` displaying live `@karianaquranbot` status, ping, and configuration.
- Real-time incoming Telegram message log showing sender user ID, full name, and username.
- 1-Click Multi-Admin Assignment controls enabling promotion/demotion of multiple administrators (Owner Rasel Gazi, Founder Maulana Saddam Hussain, General Manager).

### R3. Referral Link & Deep-Linking System (`?start=_tgr_...`)
- Detect and store referral payloads from deep links like `https://t.me/karianaquranbot?start=_tgr_KHdiM5ZlMDFl`.
- Dynamic referral link generator in Director and Teacher dashboards for tracking attributed student admissions and book orders.

### R4. Dual-Channel Automation & Interactive Action Cards
- Interactive Action Cards dispatched to assigned Telegram admins for pending Sabak class inaugurations, student admissions, and director support questions.
- Inline actionable buttons: `[✅ অনুমোদন করুন]`, `[💬 টেলিগ্রাম থেকে সরাসরি রিপ্লাই দিন]`, and `[📱 WhatsApp চ্যাট খুলুন]`.
- Synchronized state: actions executed from either the Web Admin Panel or Telegram Bot update both interfaces simultaneously in real-time.

### R5. Telegram In-App Web App (TWA) Integration
- Persistent menu button ("📖 অ্যাপ খুলুন") configured via `setChatMenuButton` opening `https://project.rasel.cloud/kariana/` directly inside the Telegram client.

### R6. Android Studio Hybrid Application Finalization
- Kotlin / WebView wrapper in `android-app/` with native splash, offline draft caching, file/camera upload permissions, and domain synchronization pointing to `https://project.rasel.cloud/kariana/`.

## Acceptance Criteria

### Interactive UI & Flipbook
- [ ] Hero slider auto-plays every 5 seconds without container resizing or layout shift.
- [ ] Clicking any book cover opens the 5-page sample flipbook modal smoothly.
- [ ] Page navigation inside the modal works via touch swipe and arrow clicks.

### Telegram Multi-Admin & Referrals
- [ ] `/admin` displays live Telegram Hub with incoming user log.
- [ ] 1-click button promotes/demotes assigned Telegram IDs (`1827362508`, etc.).
- [ ] Deep-link referral payloads (`_tgr_...`) are captured and logged in the database.
- [ ] TWA Menu Button in `@karianaquranbot` opens `https://project.rasel.cloud/kariana/` in-app.

### Dual-Channel Action Cards
- [ ] Sabak class application sends actionable card with `Approve`, `Reply`, and `WhatsApp` buttons.
- [ ] Approving from Telegram updates the web dashboard status immediately and notifies the teacher.

## 2026-09-23T16:05:35Z

CRITICAL USER REQUIREMENT UPDATE (R7):

The user has specified an additional core notification & security requirement:

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

Please incorporate this into your orchestrator and ensure `TelegramNotificationDispatcher.php` or `UnifiedMessagingService.php` is hooked into the relevant controllers (Order, Blog, Course/Admission, Auth/Admin, Sabak) to dispatch these categorized events.

## 2026-09-23T16:12:04Z

NEW USER ARCHITECTURE REQUIREMENT (R8):

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

Please ensure the database schema, models, and controllers support this role-based pricing and order pipeline.


