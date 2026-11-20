# Tasks: Pipeline & Backlog

This document maps out the current project state, active sprint goals, immediate actions, and the feature backlog.

## 1. Project State & Release Readiness
* **Current Stable Version:** `1.4.1`
* **Release Readiness:** Production-ready packaging is handled via GitHub Actions. A clean git state must be achieved before releasing.

## 2. Completed Sprint: Bulletproof Hardening (v1.4.0 / v1.4.1)
The goal of this sprint was to eliminate client-side bypasses, prevent full-page cache poisoning, fix subpath scope false positives, make plugin paths dynamic (ADR-0009), and resolve v1.4.1 security/a11y/i18n findings. All sprint tasks are complete.

### Task Breakdown & Implementation Checklist:
* [x] **TASK-01 [SEC-PATH-BOUND]: Boundary-Safe Path Matching**
  - Location: `gnn-terms-popup/gnn-terms-popup.php` (`inList()` JS function)
  - Objective: Fix `p.indexOf(item) === 0` false positives. Enforce exact match or delimited slash subpath: `(item === '/' ? p === '/' : (p === item || p.startsWith(item + '/')))`.
  - Verification: Paths like `/about` must NOT trigger on `/about-us` or `/about-team`.

* [x] **TASK-02 [CACHE-SAFE-DOM]: Full-Page Cache Invariance**
  - Location: `gnn-terms-popup/gnn-terms-popup.php` (`should_skip()` and `render_modal()`)
  - Objective: In Soft Overlay mode, ensure the modal markup is unconditionally rendered in `wp_footer` (hidden by default via CSS `display:none`). Do not suppress HTML output based on server-side `$_COOKIE` check, preventing full-page cache engines (WP Rocket, LiteSpeed, Cloudflare) from caching modals-free pages for unauthenticated visitors.
  - Verification: Test with static HTML cache enabled; new incognito sessions must reliably display the popup.

* [x] **TASK-03 [SEC-HARD-GATE]: Server-Side Hard Gate Mode**
  - Location: `gnn-terms-popup/gnn-terms-popup.php` (Admin Settings + `template_redirect` hook)
  - Objective: Add a setting for "Enforcement Mode" (`soft` = CSS/JS Modal, `hard` = Server Barrier). When `hard` is enabled and cookie is missing:
    - Block theme template loading in `template_redirect`.
    - Render a standalone, clean terms screen without leaking the underlying page post content or DOM.
    - Set `nocache_headers()` and optional HTTP 403 or standalone 200 gate view.
  - Verification: Inspect page source without cookie; underlying post content must be 100% absent.

* [x] **TASK-04 [CORE-DYN-SLUG]: Dynamic Plugin Slug in Updater**
  - Location: `gnn-terms-popup/inc/updater.php`
  - Objective: Replace hardcoded `$this->plugin_slug = 'gnn-terms-popup/gnn-terms-popup.php'` with dynamic `plugin_basename(dirname(__DIR__) . '/gnn-terms-popup.php')`.
  - Verification: Ensure directory renames do not break update hooks or `after_install()` file movements.

## 3. Immediate Next Actions
1. Verify `v1.4.1` behavior on a live/staging WordPress instance (Hard Gate, `/` scope, admin tabs a11y, SVG menu icon).
2. Select next backlog item (`FEAT-LOG`, `UI-ENHANCE`, `i18n-INIT`, or `FEAT-ANIM`) for `v1.5.0`.

## 4. Backlog
* [ ] **FEAT-LOG:** Implement a database log to record anonymous acceptance timestamps and hashes (compliance audit trail).
* [ ] **UI-ENHANCE:** Require "Scroll to Bottom" of legal text before the "Agree" button becomes enabled.
* [ ] **i18n-INIT:** Implement localization support for multilingual sites (WPML/Polylang hooks).
* [ ] **FEAT-ANIM:** Add smooth entrance and exit animations for the modal overlay.

## 5. Validation Plan
* **Syntax Validation:** Run `php -l <file>` on modified PHP source files.
* **Functional Validation:** Verify scope path boundaries, hard-gate content blocking, and cache behavior.
