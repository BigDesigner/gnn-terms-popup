# Verified Worklog

This document tracks all completed work, release history, and recent updates for the GNN Terms Popup plugin.

## 🚀 Release History & Changelogs

### [1.3.10] - 2026-07-04
- **Security Audit Fixes:**
  - Standardized Javascript variable injections using `json_encode()` to prevent output-context issues (SEC-01, SEC-02).
  - Sanitized `$_COOKIE` and `$_GET` inputs before verification checks (SEC-03, SEC-04).
  - Corrected script registration pattern for `gnn-admin-js` using standard `wp_register_script()` and `wp_enqueue_script()` calls (QA-01).
  - Fixed translations to use escaping functions (`esc_html__`) consistently (QA-02).
  - Implemented transient caching sentinel object for remote release fetch failures in updater (QA-04).

### [1.3.9] - 2026-06-26
- **Security Hardening:**
  - Added input parameter validation check to confirm it is an array in the Settings API sanitizer callback.
  - Mitigated type-confusion vulnerabilities.

### [1.3.8] - 2026-06-26
- **CSS/JS Load Stability:**
  - Consolidated CSS styling and Javascript logic blocks directly inline inside `render_modal` within the footer output.
  - Resolved modal rendering and styling issues caused by asset minification/caching optimization plugins.

### [1.3.7] - 2026-06-26
- **Sidebar Admin Menu:**
  - Relocated the plugin settings page from the options submenu to a top-level menu page in the main admin sidebar.
  - Custom brand shield icon (`dashicons-shield`) added for visual alignment.

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
- Last Commit Hash: `485884057faa421d8aeddf5c068b2c24741faefa`
- Active Branch: `main`
- Worktree Status: `clean` (after committing v1.3.10 fixes)
