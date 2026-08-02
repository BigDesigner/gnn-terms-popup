# Tasks: Pipeline & Backlog

This document maps out the current project state, active sprint goals, immediate actions, and the feature backlog.

## 1. Project State & Release Readiness
* **Current Stable Version:** `1.3.12`
* **Release Readiness:** Production-ready packaging is handled via GitHub Actions. A clean git state must be achieved before releasing.

## 2. Active Sprint (v1.4.0)
The goal of this sprint is to finalize output escaping, implement translation hooks, and add minor UX compliance safety layers.
* [ ] **i18n-INIT:** Implement localization support for multilingual sites.
* [ ] **UI-ENHANCE:** Require "Scroll to Bottom" of legal text before the "Agree" button becomes enabled.
* [ ] **SEC-REVIEW:** Verify sanitization of the "Custom Legal Text" editor output.

## 3. Immediate Next Actions
1. Deploy v1.3.12 to staging/test site for environment verification.
2. Begin designing the scroll-to-bottom activation listener in the frontend JS block.

## 4. Backlog
* [ ] **FEAT-LOG:** Implement a database log to record anonymous acceptance timestamps (compliance requirement).
* [ ] **FEAT-ANIM:** Add smooth entrance and exit animations for the modal overlay.
* [ ] **FEAT-PRESET:** Provide default pre-defined templates for Zero-Tolerance and Non-Discrimination terms.
* [ ] **DOCS:** Update manual for path-based display scope configurations.

## 5. Validation Plan
* **Syntax Validation:** Run `php -l <file>` on modified PHP source files.
* **Functional Validation:** Verify focus trapping behavior and scroll activation controls across multiple browsers (Chrome, Firefox, Safari/iOS).
