# Handoff Report: Specification Mining for Kariana Quran Portal & CMS

**Author:** Spec Miner 1 (`teamwork_preview_spec_miner`)  
**Target:** Orchestrator (`parent`, ID: `5a011e50-ed48-4482-b181-5ca5e13d7062`)  
**Handoff Type:** Hard (Task Complete)  
**Deliverable File:** `c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\spec.md`  

---

## 1. Observation
1. **Authoritative Request (`ORIGINAL_REQUEST.md`):**
   - Lines 5-8: "A blazing-fast, ultra-SEO-optimized, multifunctional Islamic educational portal and CMS for Kariana Quran (কারিয়ানা কুরআন) in Bangladesh. Built with modern, clean PHP 8.2 (PDO MVC architecture) + MySQL + Tailwind CSS + Alpine.js, engineered specifically for 100% Shared Hosting compatibility (Hostinger File Manager, cPanel public_html, and XAMPP) without requiring a VPS or Node.js background process, featuring Royal Islamic Emerald Green and Golden aesthetics. Dedicated Port: 8015 (http://localhost:8015 and http://192.168.0.100:8015)."
   - Lines 13-36: Detailed requirements R1 (SEO-Dominant Portal & Blog), R2 (Admin CMS), R3 (Islamic Daily Utilities), R4 (QR-Code Book Scanning & Video Lesson Gateway), and R5 (Shared Hosting & Visual Identity).
   - Lines 39-54: Phase 1, Phase 2, and Phase 3 Acceptance Criteria.
2. **Runtime Environment & Installed Tools:**
   - Tool execution `php -v` returned: `PHP 8.2.12 (cli) (built: Oct 24 2023 21:15:15) (ZTS Visual C++ 2019 x64)`.
   - Tool execution checking `pdo_mysql` confirmed: `pdo_mysql loaded`.
3. **Adjacent Project Survey (`C:\xampp\htdocs\Kariana Quran ReMakiking In In design`):**
   - Survey of `AGENT_HANDOVER_README.md` (lines 13-26) confirmed the existence of Kariana's verified custom Arabic font `AAR-SQ-003.ttf` and 12 specialized Tajweed marks (`S1` through `S12`: মাদে আসলী, মাদে আরিদ্, ওয়াজিব গুন্নাহ, কলকলাহ, তাফখীম শাপলা, সিটির হরফ, ইখফা, সাকিন).
   - Confirmed standalone Quran reader portal launcher exists at `c:\xampp\htdocs\Kariana Quran ReMakiking In In design\index.html`.
4. **Global Port Registry (`RULE[user_global]`):**
   - Port 8015 is assigned exclusively to `Kariana Website`.
   - All server processes must bind to `0.0.0.0` for multi-device Wi-Fi access on `http://192.168.0.100:8015` and `http://localhost:8015`.

---

## 2. Logic Chain
1. *From Observation 1 & 2:* The project target is pure PHP 8.2 MVC running on XAMPP and shared hosting. It must operate without any background daemons (Node.js, PM2, Redis). Therefore, the system architecture must use an Apache `.htaccess` Front Controller pattern with `router.php` for local CLI execution (`php -S 0.0.0.0:8015 router.php`).
2. *From Observation 1 (R1):* To achieve maximum Google Bangladesh SEO dominance, the application requires automated Schema.org JSON-LD structured data for `Organization`, `Article`, `Course`, `FAQPage`, and `BreadcrumbList`. Dynamic XML sitemaps must support clean UTF-8 Bengali slugs without character mangling.
3. *From Observation 1 (R2):* The Admin CMS must feature zero WordPress bloat and implement prepared PDO queries, CSRF cryptographic tokens, bcrypt password hashing, and session fixation defense. The admissions hub requires CSV export encoded with a UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) so that Microsoft Excel renders Bengali applicant names cleanly. The blog manager requires an interactive Google SERP preview simulator with live title/description character counters.
4. *From Observation 1 (R3) & Islamic Jurisprudence:* Prayer times must adhere to the Islamic Foundation Bangladesh (IFB) convention (Fajr 18°, Isha 18°, Hanafi Asr 2x shadow), with a built-in offset matrix across all 64 districts of Bangladesh. The Zakat calculator must utilize the Hanafi Silver Nisab threshold (52.5 Tola = 612.36g) with editable market rates in BDT. The digital Tasbeeh counter requires Web Audio synthesized clicks, haptic feedback, and LocalStorage persistence.
5. *From Observation 1 (R4) & Observation 3:* Phase 2 requires an in-browser camera QR code scanner resolving `/scan?code=...` and `/scan?page=...` to a distraction-free 16:9 video player with highlighted Tajweed symbols. The Quran Reader bridge links to the verified Kariana font `AAR-SQ-003.ttf` and reader application.
6. *From Observations 1 & 4:* Visual branding must use Royal Islamic Emerald Green (`#064e3b` / `#047857`) and Warm Quranic Gold (`#d97706`), with high-legibility Bengali typography (`Hind Siliguri`) line-height $1.7\text{--}1.8$ to support Bengali vowel diacritics.

---

## 3. Caveats
1. **Live External Video Streaming:** The video player specification supports YouTube unlisted embeds, Vimeo, and direct MP4/HLS streams. For shared hosting, streaming high-definition videos directly from the web host is discouraged due to bandwidth constraints; embedding unlisted YouTube videos or external CDN streaming is the recommended production practice.
2. **Current BAJUS Rates:** Gold and silver market rates fluctuate regularly in Bangladesh. The Zakat calculator specification includes reasonable default benchmarks (~৳১,২৫,০০০/ভরি for Gold, ~৳২,০০০/ভরি for Silver) while providing full UI controls for users and admins to update them dynamically.
3. **No Code Implementation:** In strict accordance with the Spec Miner archetype, no application or database implementation was performed. All specifications are completely documented in `spec.md`.

---

## 4. Conclusion
Exhaustive functional specifications and interface contracts have been probed and documented in `c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\spec.md`. The specification covers:
- Complete database schema (9 tables) for MariaDB/MySQL.
- Full Schema.org JSON-LD microdata definitions.
- Dynamic XML sitemap generation protocol with Bengali slug normalization algorithms.
- Complete Admin CMS specification (SERP preview, Admissions lead hub with UTF-8 BOM CSV export, Books catalog, Page SEO, PDO security & CSRF).
- Mathematical models and convention specifications for 64 districts prayer times, Sehri/Iftar schedules, Hanafi Silver Nisab Zakat calculator, and digital Tasbeeh counter.
- Phase 2 QR-code scanner and video lesson gateway specifications.
- Acceptance criteria mapping for Phases 1, 2, and 3.

The specification is ready for decomposition into `PROJECT.md` and subsequent milestone execution.

---

## 5. Verification Method
To independently verify the completeness and integrity of this specification:
1. **Inspect Specification File:**
   - Read `c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\spec.md`.
   - Verify that all requirements R1, R2, R3, R4, R5, Features Discovered table (30 features), Edge Cases table (15 scenarios), and Phase 1-3 Acceptance Criteria are fully detailed.
2. **Validate JSON-LD Schemas:**
   - Copy JSON-LD snippets from Sections 3.1.1 to 3.1.5 into the [Google Rich Results Test](https://search.google.com/test/rich-results) or [Schema.org Validator](https://validator.schema.org/) to confirm syntactical validity.
3. **Validate Database Schema:**
   - Review Section 4.1 SQL statements against MariaDB/MySQL syntax standards to confirm strict foreign key constraints and UTF-8MB4 collation support.
