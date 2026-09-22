# BRIEFING — 2026-09-22T14:10:00Z

## Mission
Probe and document exhaustive functional specifications for Kariana Quran Islamic Educational Portal & CMS covering R1-R5, data models, endpoints, calculation algorithms, JSON-LD schemas, UI/UX, and acceptance criteria.

## 🔒 My Identity
- Archetype: spec_miner
- Roles: specification_miner, domain_analyst
- Working directory: c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1
- Original parent: 5a011e50-ed48-4482-b181-5ca5e13d7062
- Milestone: Survey & Specification Mining

## 🔒 Key Constraints
- Read-only on codebase/production code (no code implementation)
- Output functional specifications to spec.md and handoff.md in .agents\spec_miner_1\
- Dedicated port: 8015 (bind 0.0.0.0, preview at localhost:8015 and 192.168.0.100:8015)
- Shared hosting zero-daemon requirement (Pure PHP 8.2 MVC + MySQL + Tailwind + Alpine.js)
- Cover all features R1, R2, R3, R4, R5 and acceptance criteria mapping for Phases 1-3

## Current Parent
- Conversation ID: 5a011e50-ed48-4482-b181-5ca5e13d7062
- Updated: 2026-09-22T14:10:00Z

## Task Summary
- **What to build**: Exhaustive functional specifications and edge-case matrix for Kariana Quran Portal & CMS
- **Success criteria**: Complete spec covering R1 (SEO/Blog/Schema/Sitemap/Bengali slug), R2 (Admin CMS, SERP preview, Courses/Admissions, Books, Pages, Security), R3 (Islamic utilities: 64 districts prayer times, Sehri/Iftar, Zakat calculator, Tasbeeh counter, Quran Reader bridge), R4 (QR Book Scanner & Lesson Gateway), R5 (Shared hosting, visual design, port 8015), and Acceptance Criteria mapping.
- **Interface contracts**: spec.md, handoff.md, progress.md
- **Code layout**: .agents/spec_miner_1/

## Key Decisions Made
- Anchored specifications in Islamic Foundation Bangladesh (IFB) prayer conventions, Hanafi Silver Nisab standards, W3C Schema.org microdata, and 100% shared hosting zero-daemon architecture.
- Identified and specified support for Kariana's verified custom font `AAR-SQ-003.ttf` and 12 Tajweed symbols (`S1`-`S12`).
- Designed UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) requirement for Admissions CSV export to prevent Excel Bengali font corruption.

## Artifact Index
- c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md — Source requirements
- c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\DISPATCH.md — Task assignment
- c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\progress.md — Liveness & status tracking
- c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\spec.md — Authoritative functional specification (30 features, 15 edge cases)
- c:\xampp\htdocs\Kariana Website\.agents\spec_miner_1\handoff.md — 5-component hard handoff report
