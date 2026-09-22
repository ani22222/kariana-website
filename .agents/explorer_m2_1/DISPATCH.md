# Explorer M2 #1 Dispatch

## Mission:
Investigate existing and required implementations for Public Portal:
- Home Page (`app/Views/home/`, `app/Controllers/HomeController.php`)
- Course Catalog & Details (`app/Controllers/CourseController.php`, `app/Views/courses/`)
- Books & Publications Catalog (`app/Controllers/BookController.php`, `app/Views/books/`)
- Online Student Admission Form & Lead Database (`app/Controllers/CourseController.php` or `app/Controllers/AdmissionController.php`, `app/Models/Admission.php`, validation, CSRF, database insertion).

## Mandatory Inputs:
- `c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md`
- `c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen2\PROJECT.md`
- Existing codebase in `app/Controllers/`, `app/Views/`, `app/Models/`, `database/schema.sql`.

## Output:
Write `handoff.md` in `.agents/explorer_m2_1/` with exact status of existing code, missing features, and complete implementation recommendations for the worker.

## 2026-09-22T15:28:50Z
You are Explorer M2 #1 for Kariana Quran.
Your working directory: c:\xampp\htdocs\Kariana Website\.agents\explorer_m2_1\

Read:
- c:\xampp\htdocs\Kariana Website\ORIGINAL_REQUEST.md
- c:\xampp\htdocs\Kariana Website\.agents\orchestrator_gen2\PROJECT.md

Investigate:
1. Public Home page (`app/Controllers/HomeController.php`, `app/Views/home/`): hero section, prayer time bar, featured courses, book showcase, testimonials.
2. Course catalog & details (`app/Controllers/CourseController.php`, `app/Views/courses/`): courses (নাজেরা, হিফজ, ক্বেরাত, তাজবীদ), syllabus, age groups, fees.
3. Online student admission form: interactive form with validation, CSRF, database persistence into `admissions` table.
4. Books catalog (`app/Controllers/BookController.php`, `app/Views/books/`): book list, details, PDF sample preview modal, ordering link.
5. Check what exists in `app/` and what needs implementation or enhancement.
6. Write handoff.md in your working directory with concrete findings and recommendations for the worker.
7. Send a message to parent with your summary.
