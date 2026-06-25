# Project Snapshot: GNN Terms Popup

## Project Status
- **Current Version:** 1.3.5
- **Author:** BigDesigner
- **Last Sync:** 2026-05-07
- **Status:** Production Ready / License Finalized

## Core Functionality
- One-time mandatory Terms & Conditions popup modal.
- Cookie-based acceptance persistence (`gnn_terms_accepted`).
- Dynamic legal content loading (WordPress Page Slug or Custom Text).
- Configurable display scope (Site-wide or specific paths).
- Admin settings panel for full content and behavior customization.
- Accessibility features (Focus trap, ARIA roles, responsive design).

## Active Components
- `gnn-terms-popup.php`: Main monolithic plugin file containing:
    - `GNN_Terms_Popup` class.
    - Admin Settings API integration.
    - Frontend hook management (`wp_footer`, `wp_enqueue_scripts`).
    - Inline Vanilla JS/CSS assets for maximum performance and compatibility.
    - Sanitization and validation logic.

## Recent Changes
- **v1.3.5:** (2026-05-07)
    - **License:** Updated to GPLv2 or later.
- **v1.3.4:** (2026-05-07)
    - **Meta:** Updated Author URI to GitHub profile.
- **v1.3.3:** (2026-05-07)
    - **Fix:** Removed duplicate action link to prevent conflicts with other GNN plugins.
- **v1.3.2:** (2026-05-07)
    - **Updater:** Integrated GitHub Releases updater logic.
    - **UX:** Added professional action links in the WordPress plugins list.
    - **Admin:** Added "GNN System Info" card to the settings page.
- **v1.3.1:** (2026-05-07)
    - **Security Hardening:** Added defense-in-depth output sanitization.
    - **Localization:** Implemented i18n support (text domain: `gnn-terms-popup`).
    - **Reorganization:** Consolidated all project documentation into the `memory-bank/` directory for better AI context management.
- **v1.3.0:** (2026-05-06)
    - Added Legal Content Source toggle (Page vs Custom).
    - Implemented Dynamic Scope Control (Path-based display).
    - Enhanced A11y with focus trapping and ARIA support.
    - Standardized GNN Brand Colors (#fdb813/Black).
- **v1.2.0:**
    - Initial settings panel and cookie configuration logic.

## Next Steps
- Implement localization (i18n) for multilingual support.
- Add "Scroll to bottom" requirement before enabling the "Agree" button.
- Integrate audit logging for terms acceptance (optional compliance feature).
