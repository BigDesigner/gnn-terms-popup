# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.12] - 2026-08-02
### Changed
- **Menu Alignment:** Registered the admin settings menu at position `'79.105'` to align with the GNN Product Family position registry guidelines.

## [1.3.11] - 2026-07-05
### Fixed
- **Security Hardening:** Escaped settings page links with `esc_url()` (AUD-001) and wrapped dynamic page post contents in `wp_kses_post()` under `render_modal` (AUD-003).
- **Code Quality:** Added default color fallback metrics for `sanitize_hex_color()` in settings validation (AUD-002), enqueued translations for option field details (AUD-004, AUD-005, AUD-006), enqueued transient sentinel cache protection for invalid tag name responses in GitHub API updater (AUD-007), and added validation checks for global `$wp_filesystem` instance (AUD-008).
- **Documentation & Compliance:** Rewrote and stylized `README.md` to reflect the sidebar menu location and cookie specifications. Added official GNU GPLv2 `LICENSE` file for compliance.

## [1.3.10] - 2026-07-04
### Fixed
- **Security Hardening:** Standardized JS variable injections using `json_encode()` (SEC-01, SEC-02), sanitized `$_COOKIE` and `$_GET` values prior to evaluation (SEC-03, SEC-04), and resolved translation escaping checks (QA-02).
- **Code Quality:** Enqueued `gnn-admin-js` using standard WordPress `wp_register_script()` / `wp_enqueue_script()` pattern (QA-01) and resolved cache-miss vs cache-failure ambiguities in GitHub updater class (QA-04).
- **Release Packaging:** Excluded the new dot directories (`.memory-bank`, `.agents`, `.archive`, `.specs`, `.tasks`) from the production build ZIP in the GitHub Actions release workflow.

## [1.3.9] - 2026-06-26
### Fixed
- **Security Hardening:** Implemented a type validation check (`is_array()`) inside the Settings API sanitizer callback to reject malformed or invalid inputs, preventing potential type confusion warnings.

## [1.3.8] - 2026-06-26
### Fixed
- **CSS/JS Load Stability:** Consolidated CSS styling and Javascript logic blocks directly inline inside `render_modal` within the footer output. This resolves modal visibility bugs and load errors on production sites utilizing CSS/JS minification or caching optimization plugins (e.g. WP Rocket, LiteSpeed Cache).

## [1.3.7] - 2026-06-26
### Added
- **Sidebar Admin Menu:** Relocated the plugin settings page from the default "Settings" submenu to a top-level menu page in the main WordPress admin sidebar using a custom shield icon (`dashicons-shield`).

## [1.3.6] - 2026-06-26
### Changed
- **Security Hardening (Defense in Depth):** Added native escaping functions (`esc_html__`, `esc_html`, `esc_attr`, `esc_html_e`, `esc_attr_e`) across settings forms and rendering functions to prevent translation-based XSS exploits.
- **i18n & Localization:** Wrapped missing settings titles, button labels, and descriptions in translation hooks for improved multilingual support.

## [1.3.5] - 2026-05-07
### Changed
- **License:** Updated to GPLv2 or later for better compatibility and future-proofing.

## [1.3.4] - 2026-05-07
### Changed
- **Metadata:** Updated Author URI to point to official GitHub profile.

## [1.3.3] - 2026-05-07
### Fixed
- **Plugin Action Links:** Removed duplicate "Check this plugin" link that was conflicting with global plugin hooks.

## [1.3.2] - 2026-05-07
### Added
- **GitHub Updater:** Integrated a professional GitHub-based update mechanism (`inc/updater.php`).
- **Plugin Action Links:** Added custom links to the plugins page (Donate, Settings, Check Updates, Check this plugin).
- **System Info Card:** Added a system status card to the admin settings page for easier debugging and version tracking.

## [1.3.1] - 2026-05-06
### Added
- **Localization (i18n):** Added full support for translations via text domain `gnn-terms-popup`.
### Changed
- **Hardening:** Implemented "Defense in Depth" by re-sanitizing output using `wp_kses_post()` for intro and legal text.
- **Security Audit:** Completed full security audit (Status: PASS).

## [1.3.0] - 2026-05-06
### Added
- **Legal Content Source Toggle:** Choice between pulling content from a WordPress Page slug or entering Custom Text.
- **Dynamic Scope Control:** Ability to show the popup on all pages or only on specific relative paths.
- **Improved UX:** Added smooth scroll and auto-focus for the legal text section.
- **Focus Trap:** Enhanced accessibility by trapping focus within the modal when open.
### Changed
- Standardized colors to #fdb813 (yellow) and #000000 (black).
- Optimized cookie handling logic for HTTPS/SameSite compliance.
### Fixed
- Fixed iOS-specific scrolling issues in the modal.
- Resolved conflict with admin visual/text editor toggle.

## [1.2.0] - 2026-04-15
### Added
- **Admin Settings Page:** Comprehensive settings panel under Settings -> GNN Terms Popup.
- **Custom Labels:** Configurable "Accept" and "Read" button labels.
- **Cookie Lifetime:** Configurable number of days for acceptance persistence.
### Changed
- Improved frontend modal styling for mobile responsiveness.

## [1.0.0] - 2026-04-01
### Added
- Initial release of GNN Terms Popup.
- Core modal functionality and basic cookie persistence.
