# Verified Worklog

This document tracks all completed work, release history, and recent updates for the GNN Terms Popup plugin.

## 🚀 Release History & Changelogs

### [1.4.1] - 2026-11-20
- **Security & Validation Fixes:**
  - Preserved root `/` path in `sanitize()` instead of stripping it via `rtrim()` + `array_filter()` (`K-01`).
  - Removed duplicate `esc_html()` call on `$accept_label` and `$read_label` in `render_modal()` (`Ö-01`).
  - Added `isset()` guard for `$body->zipball_url` in `inc/updater.php`, prevented caching empty download URLs as valid releases, and returned HTTP `403` via `wp_die()` for unauthorized manual update checks (`Ö-02`).
- **Accessibility & i18n:**
  - Added `role="tabpanel"` and dynamic `aria-selected` updates to admin settings tabs (`Ö-04`).
  - Completed all plugin strings in `gnn-terms-popup/languages/gnn-terms-popup.pot` and localized fallback legal-page notice (`Ö-03`).
- **WordPress 7.2 & UX:**
  - Replaced Dashicons font icon with an inline base64 SVG shield icon in `admin_menu()` (`İ-01`).
  - Clarified in admin settings that Hard Gate disables search engine indexing on gated pages (`İ-02`).

### [1.4.0] - 2026-11-18
- **Hard Gate & Cache Invariance (ADR-0009):**
  - Added optional Server-Side Hard Gate mode (`gate_mode`) on `template_redirect` to prevent underlying page HTML delivery until terms are accepted.
  - Removed server-side `$_COOKIE` suppression in Soft Overlay mode for full-page cache invariance.
  - Fixed `inList()` boundary-safe path matching so `/about` no longer matches `/about-us`.
- **Accessibility & Frontend:**
  - Re-queries visible focusable elements dynamically when expanding `#gnn-terms-full` and binds the `keydown` focus-trap listener only once.
  - Added `dvh` viewport fallback (`max-height: 90dvh` / `80dvh`) and `:focus-visible` styling.
- **Security, Updater & Compliance:**
  - Added `rel="noopener noreferrer"` on external Donate link, switched updater to `wp_safe_remote_get()`, sanitized GitHub release URLs with `esc_url_raw()`, resolved `$plugin_slug` dynamically via `plugin_basename()`, passed `false, false` to `get_plugin_data()`, and added `requires` / `tested` / `requires_php` metadata to `plugin_info()`.
  - Added `Requires at least: 5.0`, `Tested up to: 7.2`, `Requires PHP: 7.4`, `Domain Path: /languages`, `GNN_Terms_Popup::VERSION`, and `load_plugin_textdomain()` on `init` (priority 1).

### [1.3.16] - 2026-10-15
- **Repository Structure & Admin UI:**
  - Moved plugin source into `gnn-terms-popup/` subfolder and updated `.github/workflows/release.yml` to package `gnn-terms-popup/` directly.
  - Redesigned admin settings into a tabbed card-based UI with toggle switches, card-style radio rows for Display Scope, and popup enable/disable control; removed legacy System Info tab.

### [1.3.12] - 2026-08-02
- **Menu Alignment:**
  - Standardized the admin settings page position to `'79.105'` (quoted string literal) to align with GNN Product Family position registry guidelines.

### [1.3.11] - 2026-07-05
- **Security Audit Resolution:**
  - Resolved settings page links output escaping with `esc_url()` (AUD-001).
  - Wrapped dynamic page post contents in `wp_kses_post()` under `render_modal` to secure output (AUD-003).
  - Implemented transient sentinel cache protection for invalid tag responses in GitHub API updater (AUD-007).
  - Resolved validation checks for global `$wp_filesystem` instance (AUD-008).
- **Code Quality:**
  - Added default color fallback metrics for `sanitize_hex_color()` in settings validation (AUD-002).
  - Enqueued translation strings for option field details (AUD-004, AUD-005, AUD-006).
- **Documentation & Compliance:**
  - Rewrote and stylized `README.md` to correct administrative settings paths and document cookie specifications.
  - Added official GNU GPLv2 `LICENSE` file to the root directory for licensing compliance.

### [1.3.10] - 2026-07-04
- **Security Audit & Packaging Fixes:**
  - Standardized Javascript variable injections using `json_encode()` to prevent output-context issues (SEC-01, SEC-02).
  - Sanitized `$_COOKIE` and `$_GET` inputs before verification checks (SEC-03, SEC-04).
  - Corrected script registration pattern for `gnn-admin-js` using standard `wp_register_script()` and `wp_enqueue_script()` calls (QA-01).
  - Fixed translations to use escaping functions (`esc_html__`) consistently (QA-02).
  - Implemented transient caching sentinel object for remote release fetch failures in updater (QA-04).
  - Excluded development dot directories (`.memory-bank`, `.agents`, `.archive`, `.specs`, `.tasks`) from release zip packaging in GitHub Actions.

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
- Last Commit Hash: `4a70e3554fb8d7b9d732bc5a35a8101259b8ea4e`
- Active Branch: `main`
- Worktree Status: `clean` (v1.4.1 tagged and pushed to `origin` with `php -l` passing on `gnn-terms-popup/gnn-terms-popup.php` and `gnn-terms-popup/inc/updater.php`)
