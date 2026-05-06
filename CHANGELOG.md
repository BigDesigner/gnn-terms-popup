# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
