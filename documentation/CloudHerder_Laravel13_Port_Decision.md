# CloudHerder: Port to Laravel 13 Decision Document

**Date:** 2026-09-29
**Author:** Dallum + Claw
**Status:** Proposed / under consideration
**Current stack:** Laravel 12, PHP 8.5, Livewire 4, Flux UI 2, Fortify, Scout, Spatie packages

---

## The real question

Not "should we port?" — that is leaning toward yes because of bloat, tech debt, and the expanded feature set (agentic content pipeline, decision models, heavy admin polish).

The real question is: **what order minimizes risk and preserves momentum?**

Two options:

| Option | Sequence | Risk |
|--------|----------|------|
| A. Build Anvil first, then port CloudHerder | Build new CMS skeleton, then migrate existing features into it | High risk of Anvil becoming vaporware while CloudHerder stalls |
| B. Port CloudHerder to Laravel 13 directly | Modernize existing app in place, extract reusable pieces into Anvil later | Higher upfront cost, lower total-project risk |

Original plan was A. Current thinking is shifting toward B.

---

## Why the port makes sense now

1. **Tech debt is real.** The app started from the Livewire starter kit and has accumulated ad-hoc decisions. A clean Laravel 13 foundation reduces future drag.
2. **Laravel 13 ASI stack** (AI/Scout/Intelligent features) gives first-class scaffolding for the agentic features you’ve been holding off.
3. **Decision models fit the content pipeline.** Jev/Laya-style classification can power:
   - Content triage and routing
   - Auto-tagging and taxonomy suggestions
   - Spam / quality scoring on comments and submissions
   - Newsletter send/no-send decisions
   - Approval workflows for agent-generated drafts
4. **Laravel 14 is ~4 months away.** Doing the work now front-loads the migration tax and leaves you ready for L14 instead of carrying L12 + debt into it.
5. **The expanded feature set needs a consistent backend dashboard.** Building that polish on top of the current structure means more migration debt later.

---

## Why the port is blocked right now

You mentioned a couple of issues preventing it today. These should be captured explicitly before scheduling the work:

- **Blocker 1: GitHub issue #9** — API: POST /api/v1/posts returns 403 despite valid token + `create posts` permission. Token authenticates but fails authorization. Suspected mismatch between Sanctum `tokenCan()` and Spatie `user->can('create posts')`, or route middleware using wrong ability string.
  - **Resolved 2026-09-30.** Root cause was Spatie resolving permissions against the active Sanctum guard while all permissions are stored with `guard_name='web'`. Fix: add `User::guardName()` returning `'web'` so Spatie always looks up permissions against the web guard, regardless of session or token auth. Commits: `ba4bda5`, `a341e06`.
- **Blocker 2: GitHub issue #8** — No admin UI for Sanctum token management. API-driven workflows currently require SSH + `artisan tinker` to mint tokens.
  - **Resolved 2026-09-30.** Added `ApiTokenManager` Livewire component at `/admin/api-tokens`, gated by `create posts`. Commits: `ba4bda5`, `a341e06`.

Until these are resolved, the port stays in planning.

### Pre-existing test debt discovered during blocker work

While validating the fixes, a full test run returned **393 passed, 110 failed**. These failures are unrelated to the token/permission work and appear to be pre-existing tech debt to address before or during the port:

- **`app/Models/Post.php:108`** — `DateTime::setTimezone(): Argument #1 ($timezone) must be of type DateTimeZone, string given`. This cascades into `CommentPolicyTest`, `PageModelTest`, and `TaxonomyTest` failures.
- **`MarkdownEditor` Livewire component** — `MethodNotFoundException` and `TypeError` across multiple unit tests.

Files not touched by the blocker work: `Post.php`, Markdown editor component/classes, `Page`, `Taxonomy`. A focused cleanup pass should precede the port so failing tests are not carried forward as noise.

---

## Recommended sequence: prototype → then port

Because the port will take longer than the original build and will affect every model/route/view, the safest path is to **validate the AI behavior on Laravel 12 first**, then carry the proven integration into Laravel 13.

### Phase 0: Unblock (this week)
- Resolve the two port blockers.
- Freeze non-essential CloudHerder feature work.

### Phase 1: AI prototype on Laravel 12 (1–2 weeks)
Goal: prove the agentic pipeline is worth the port, without rebuilding the world.

Choose one bounded decision to automate:

- **Content quality gate:** score incoming post drafts before they appear in admin.
- **Auto-tag suggestion:** given title + excerpt, suggest taxonomy terms.
- **Comment moderation:** approve / hold / flag comments.
- **Newsletter send decision:** classify a newsletter as ready-to-send or needs-review.

Implementation:
- Stand up a decision model locally (Laya via Node/ONNX, or FLock this-that-model via LocalAI).
- Expose a thin HTTP service or route inside a Laravel service class.
- Add one model trait + one service + one UI toggle in the admin.
- Track outcomes in the existing `NewsletterActivity` / activity log pattern.

Deliverable: a working feature with tests, plus a short retro on whether the model is accurate enough.

### Phase 2: Laravel 13 port (larger, bounded)
Once the prototype is proven, the port becomes a migration of known features rather than a migration + design effort at the same time.

Key migration areas:

| Area | What to reassess |
|------|------------------|
| Directory structure | Laravel 13 conventions, possibly cleaner than L12 starter-kit layout |
| AI scaffolding | Replace custom service with Laravel ASI primitives where appropriate |
| Flux / Livewire | Confirm component compatibility and new dashboard patterns |
| Auth / Fortify | Keep or replace with Laravel 13’s built-in auth options |
| Media / SEO packages | Verify L13 support, drop or upgrade packages |
| Search / Scout | Re-evaluate engine choice against new AI features |
| Tests / Pest 4 | Carry tests forward, leverage new Pest features |

### Phase 3: Anvil extraction (after port stabilizes)
Only after CloudHerder is clean and the agentic features are proven do you extract reusable CMS primitives into Anvil.

This reverses the original plan but lowers the chance that Anvil is built for requirements that turn out to be wrong.

---

## Decision criteria

Port should proceed when all of these are true:

- [x] The two blockers are resolved.
- [ ] Pre-existing test failures are triaged and either fixed or explicitly accepted as port-side work.
- [ ] Phase 1 prototype has a passing accuracy threshold and a happy user path.
- [ ] You can commit at least one focused block of time per week to the port for 4–6 weeks.
- [ ] There is a rollback plan: keep the Laravel 12 production app deployable until the L13 app reaches parity.

Port should be delayed if:

- [ ] A critical CloudHerder feature (e.g., newsletter send flow) is mid-build.
- [ ] The blockers would push the port into Laravel 14 release window — then it may be worth waiting.

---

## Open questions to resolve before scheduling

1. ~~What are the two specific blockers?~~
   - #8 — Admin Sanctum token management UI
   - #9 — API 403 on POST /api/v1/posts with valid token + `create posts`
2. Which decision-model task is the best prototype candidate?
3. LocalAI + Laya vs. Jev API for the prototype? (Local/self-hosted preference points to Laya.)
4. Does Laravel 13 ASI stack require package versions not yet compatible with current dependencies?
5. What is the minimum feature set for “parity” on the new app before production cutover?

---

## Bottom line

CloudHerder’s next phase is agentic positioning + heavy polish. Laravel 13 is a better runway than Laravel 12 for that. Porting first is probably the right call, but only after the immediate blockers are gone and one AI feature is proven on the current stack.

Original plan: Anvil → port.
Revised plan: prototype AI on L12 → port CloudHerder to L13 → extract Anvil from the cleaned app.
