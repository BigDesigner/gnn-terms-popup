# Task List: GNN Terms Popup

This document tracks the evolution of the GNN Terms Popup WordPress plugin.

## ✅ Completed Milestones

### MB — Foundation & Infrastructure
- [x] **MB-001:** Initialize project structure as a WordPress Plugin.
- [x] **MB-002:** Create monolithic class-based architecture (`GNN_Terms_Popup`).
- [x] **MB-003:** Create specialized Memory Bank for AI context persistence.

### SEC — Security & Hardening
- [x] **SEC-001:** Implement Settings API with Nonce protection.
- [x] **SEC-002:** Implement strict sanitization using `wp_kses_post` and `wp_kses`.
- [x] **SEC-003:** Implement `defined('ABSPATH') || exit;` guard.
- [x] **SEC-004:** Add capability checks (`manage_options`) for all admin actions.

### UI — GNN Brand Design System
- [x] **UI-001:** Implement GNN Brand Palette (#fdb813 & Black).
- [x] **UI-002:** Design responsive modal with inline expanding legal section.
- [x] **UI-003:** Add accessibility features (Focus trap, ARIA roles).
- [x] **UI-004:** Implement body scroll locking when modal is active.

### FEAT — Core Features
- [x] **FEAT-001:** Cookie-based acceptance persistence.
- [x] **FEAT-002:** Dynamic Legal Source (Page Slug or Custom Text).
- [x] **FEAT-003:** Path-specific display scope control.

---

## 🚀 Release History

### v1.3.2 — Advanced Integration
- [x] Integrated GitHub Releases updater logic (`inc/updater.php`).
- [x] Added professional action links (Donate, Settings, Check Updates, Check this plugin).
- [x] Added "GNN System Info" card to the settings page.

### v1.3.1 — Security & Organization
- [x] Implemented defense-in-depth output sanitization (`wp_kses_post`).
- [x] Added full localization support (`gnn-terms-popup`).
- [x] Consolidated all markdown documentation into the `memory-bank/` directory.

### v1.3.0 — Content Flexibility & Scope Control
- [x] Added "Legal Content Source" toggle (Page vs Custom).
- [x] Implemented "Display Scope" (All pages vs Specific paths).
- [x] Enhanced accessibility with focus trapping logic.
- [x] Reset project documentation to match GNN Terms Popup identity.

### v1.2.0 — Settings & Persistence
- [x] Implemented Admin Settings panel.
- [x] Added configurable button labels and cookie lifetime.
- [x] Refined modal styling for mobile devices.

### v1.0.0 — Initial Release
- [x] Core modal popup logic.
- [x] Basic cookie acceptance tracking.

## 📂 Backlog
- [ ] Implement localization (i18n) for multilingual support.
- [ ] Add "Scroll to bottom" requirement for acceptance.
- [ ] Implement anonymized audit logging for compliance.
