# GNN Terms Popup

[![WordPress Version](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4+-8892BF.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-GPL%20v2%20or%20later-orange.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A lightweight, high-performance, and one-time mandatory Terms & Conditions popup modal plugin for WordPress. Designed with the premium GNN brand identity (#fdb813 & #000000) and strict accessibility compliance.

---

## Key Features

- **Mandatory Acceptance:** Blocks interaction until terms are accepted (with optional administrator skip).
- **Cookie-Based Persistence:** Stores acceptance status securely via browser cookies (`gnn_terms_accepted`).
- **Flexible Legal Source:** Load legal terms dynamically from a standard WordPress Page (via slug) or paste custom HTML text directly in settings.
- **Path-Specific Display Scope:** Restrict the popup to appear only on specific paths (e.g. `/` or `/shop`) or show it site-wide.
- **Accessibility & Focus Trap:** Full keyboard navigation support (`Tab`/`Shift+Tab` locking), ARIA attributes, and body scroll locking.
- **Auto-Updater:** Built-in seamless updates integration via GitHub Releases API.

---

## Installation

1. Download the plugin release ZIP file.
2. Upload the extracted folder to your `/wp-content/plugins/` directory or install the ZIP directly via **Plugins -> Add New**.
3. Activate the plugin through the **Plugins** menu in WordPress.
4. Configure settings under the **GNN Terms Popup** top-level menu in the main admin sidebar.

---

## Administration Settings

The plugin provides a dedicated settings dashboard in the WordPress Sidebar under **GNN Terms Popup** containing:
- **Modal Content:** Customize titles, intro body text, button labels, and cookie lifetime.
- **Legal Content Source:** Toggle between dynamic page fetching or custom rich-text editor inputs.
- **Display Scope:** Manage display paths or enforce site-wide popup triggers.
- **Appearance Customizer:** Change primary and secondary brand colors.
- **GitHub Updater Controls:** Check for new versions manually or view release tags.

---

## Technical Details

- **Acceptance Cookie:** `gnn_terms_accepted` set with `SameSite=Lax` and `Secure` (when operating on HTTPS).
- **Zero Dependencies:** Built entirely with native WordPress hooks, Vanilla JavaScript, and inline CSS layout structures to eliminate HTTP requests and prevent rendering blocks.
- **Changelog:** A detailed version history is maintained in [CHANGELOG.md](CHANGELOG.md).
- **AI/Developer Documentation:** Active development context and specifications are stored under `.memory-bank/` and `.tasks/`.
