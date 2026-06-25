# Project Standards & Linting Protocols

## 1. Naming Conventions
- **Files:** `lowercase-kebab-case.php`
- **Classes:** `PascalCase` (prefixed with `GNN_Terms_`)
- **Functions:** `snake_case` (prefixed with `gnn_terms_`)
- **Variables:** `snake_case`
- **CSS Classes:** BEM methodology or simple prefixing (`gnn-terms-`)
- **Text Domain:** `gnn-terms-popup`

## 2. Code Quality
- **PHP:** PSR-12 compliance.
- **JS:** Standard JS with ES6+ features.
- **CSS:** Vanilla CSS with custom properties (CSS variables). **Priority:** Zero-conflict theme compatibility (Dark/Light mode support via inheritance).
- **Design:** Standardized GNN Brand Colors: Yellow (#fdb813) and Black (#000000).

## 3. Documentation
- All functions/methods SHOULD have PHPDoc blocks.
- Major logic blocks require inline comments explaining the "why", not just the "how".

## 4. Plugin Architecture
- **Safety:** All files must include `if ( ! defined( 'ABSPATH' ) ) exit;` guard.
- **Hooks:** Use appropriate hooks (`admin_menu`, `admin_init`, `wp_enqueue_scripts`, `wp_footer`).
- **Global Namespace:** Avoid polluting the global namespace; use prefixes or classes for EVERYTHING.
- **Cleanup:** Ensure all options are removed upon uninstallation via `uninstall.php` (if applicable).

## 5. Settings & UX Standards
- **Settings API:** Use the native WordPress Settings API for all plugin options.
- **Validation:** All settings MUST have a `sanitize_callback` (e.g., `wp_kses_post`, `sanitize_title`).
- **UI/UX:** Settings pages should follow WordPress Admin UI patterns but use GNN-branded interactive elements where appropriate.

## 6. Security & Documentation Research
- **Official Sources:** The WordPress Developer Resources (Plugin Handbook) are the primary sources of truth.
- **Security Protocols:**
    - All input must be sanitized (`sanitize_text_field`, `wp_kses`, etc.).
    - All output must be escaped (`esc_html`, `esc_attr`, `wp_kses_post`).
    - Nonces MUST be used for all settings updates.
    - Permission checks (`current_user_can('manage_options')`) must be performed before any admin action.

## 7. Consultative & Mentorship Approach
- **Proactive Suggestions:** Evaluate if a better, more modern, or more user-friendly way exists.
- **Decision Support:** Present better ways to the USER before implementation.

## 8. Performance & Optimization
- **Asset Enqueuing:** Only enqueue styles/scripts when necessary (check cookie/scope).
- **No Dependencies:** Avoid 3rd party libraries. Use vanilla JS/CSS for the modal.

## 9. Development Integrity & Verification
- **Verification Methods:** PHP lint (`php -l`), CSS validation, and functional testing.
- **Atomic Commits:** Each commit must represent a single, verified change.

## 10. Localization (i18n)
- All user-facing strings MUST be translatable using `__()`, `_e()`, etc.
- Text domain `gnn-terms-popup` must be used consistently.

## 11. Changelog Management
- **Format:** Follow the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) standard.
- **Update Rule:** Every version bump in the main plugin file header MUST have a corresponding entry in `CHANGELOG.md`.
