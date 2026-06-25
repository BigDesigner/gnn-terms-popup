# Verified Worklog

This document tracks all completed work, release history, and recent updates for the GNN Terms Popup plugin.

## 🚀 Release History & Changelogs

### [1.3.6] - 2026-06-26
- **Security & UX Hardening:**
  - Added native escaping functions (`esc_html__`, `esc_html`, `esc_attr`, `esc_html_e`, `esc_attr_e`) across settings forms and description blocks to harden output security.
  - Implemented internationalization for missing admin interface labels.

### [1.3.5] - 2026-05-07
- **Changed:**
  - Updated license to GPLv2 or later for broader plugin community compatibility.

### [1.3.4] - 2026-05-07
- **Changed:**
  - Updated Author URI metadata in plugin header to official GitHub profile link.

### [1.3.3] - 2026-05-07
- **Fixed:**
  - Removed duplicate "Check this plugin" action link that caused conflict with global plugin listing page hooks.

### [1.3.2] - 2026-05-07
- **Added:**
  - GitHub Updater integration via specialized class in `inc/updater.php` for seamless automatic updates via GitHub releases.
  - Added clean action links to the WordPress plugins table (Donate, Settings, Check Updates, Check this plugin).
  - Added GNN System Info card inside settings page for debugging context.

### [1.3.1] - 2026-05-06
- **Security:**
  - Defense-in-depth sanitization on frontend modal outputs using `wp_kses_post()`.
  - Added full translation support (`gnn-terms-popup`).
- **Organization:**
  - Reorganized project documentation into legacy `memory-bank/` folder.

### [1.3.0] - 2026-05-06
- **Added:**
  - Legal content source toggle (Page Slug vs. Custom Text).
  - Dynamic path-based scope control.
  - Interactive expand/collapse panel UX with smooth scroll.
  - Accessibily improvements (focus trapping modal, ARIA attributes).
- **Changed:**
  - GNN Brand Styling Colors: Yellow (`#fdb813`) and Black (`#000000`).

### [1.2.0] - 2026-04-15
- **Added:**
  - Initial settings admin panel dashboard.
  - Customizable labels for buttons and configurable cookie TTL.

### [1.0.0] - 2026-04-01
- **Added:**
  - Initial codebase with modal popup and vanilla cookie acceptance check.

---

## 🛠️ Verification Log
- Last Commit Hash: `bd38078640afc3bd5279791a8c7496c471f5c96e`
- Active Branch: `main`
- Worktree Status: `dirty` (uncommitted changes in `gnn-terms-popup.php` represent version 1.3.6 security/UX hardening)
