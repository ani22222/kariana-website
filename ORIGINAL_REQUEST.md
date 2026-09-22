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
