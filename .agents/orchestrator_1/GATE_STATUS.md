# Gate Status Log

## Gate — Iteration 1 (Milestone M1)

| Agent | Role | Verdict | Source |
|-------|------|---------|--------|
| worker_m1 | teamwork_preview_worker | DONE | .agents/worker_m1/handoff.md |
| reviewer_m1_1 | teamwork_preview_reviewer | REQUEST_CHANGES | .agents/reviewer_m1_1/handoff.md |
| reviewer_m1_2 | teamwork_preview_reviewer | REQUEST_CHANGES | .agents/reviewer_m1_2/handoff.md |
| challenger_m1_1 | teamwork_preview_challenger | CHALLENGE FAILED | .agents/challenger_m1_1/handoff.md |
| challenger_m1_2 | teamwork_preview_challenger | CONFIRM CORRECTNESS | .agents/challenger_m1_2/handoff.md |
| auditor_m1_1 | teamwork_preview_auditor | INTEGRITY VIOLATION | .agents/auditor_m1_1/handoff.md |

Gate Result: **FAIL** (auditor_m1_1 INTEGRITY VIOLATION — binary veto; reviewer_m1_1 REQUEST_CHANGES; reviewer_m1_2 REQUEST_CHANGES; challenger_m1_1 CHALLENGE FAILED)

### Required Remediations:
1. **Fix syntax error in `index.php`**: Close the closure for `/api/verify_m1` properly with `});`.
2. **Strict Layout Compliance**: Relocate `test_m1.php` out of `.agents/worker_m1/` into `tests/unit/test_m1.php`. Remove all references to `.agents/` from `index.php` and production codebase (`.agents/` holds ONLY metadata).
3. **Enhance `BengaliHelper::createSlug()`**:
   - Strip Bengali Dari (`।`, `\x{0964}`) and Double Dari (`॥`, `\x{0965}`).
   - Strip Bengali Taka symbol (`৳`, `\x{09F3}`).
   - Replace punctuation with hyphens before stripping so words without whitespace (`কুরআন/সুন্নাহ`) don't concatenate into `কুরআনসুন্নাহ`.
   - Support Arabic characters (`\p{Arabic}`) so Arabic titles don't collapse into empty strings `""`.
4. **Harden `core/Model.php`**: Add regex whitelisting (`/^[a-zA-Z0-9_,\s\.]+(?:\s+(?:ASC|DESC))?$/i`) on `$orderBy` parameters in `where()` and `all()`.
5. **Secure `router.php`**: Block direct web serving of `.sql`, `.md`, `.json`, `config/`, `database/`, and dotfiles over port 8015.
6. **Fix `.htaccess`**: Use relative path or dynamic environment path for `public/` assets so assets work in both root domains and subfolder environments.
