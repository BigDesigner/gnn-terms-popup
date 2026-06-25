# Specs: Constitution & Coding Standards

This document establishes the official engineering standards, style conventions, security rules, and agent behavior guidelines for this repository.

## 1. Naming Conventions
* **File Names:** All PHP files must use lowercase kebab-case naming (e.g. `lowercase-kebab-case.php`).
* **Classes:** Class names must use `PascalCase` and begin with the global brand prefix (e.g. `GNN_Terms_Popup`).
* **Functions:** Global or public class-less functions must use `snake_case` prefixed with `gnn_terms_` (e.g. `gnn_terms_render_popup`).
* **CSS Classes:** Class selectors must use `gnn-terms-` namespace (e.g. `.gnn-terms-popup-container`).
* **Text Domain:** All translatable strings must use the text domain `gnn-terms-popup`.

## 2. Code Quality & Formatting
* **PHP:** Strictly PSR-12 compliant coding style.
* **JavaScript:** Standard modern JavaScript (ES6+), avoiding external UI framework dependencies.
* **CSS:** Vanilla CSS utilizing CSS Custom Properties (Variables).
* **Theme Compatibility styling:** CSS must never hardcode base text colors or absolute light/dark background colors to prevent clashing. Utilize `color: inherit;`, semi-transparent values (`rgba`), and glassmorphism blurs (`backdrop-filter`) to inherit host theme colors naturally.

## 3. Zero-Tolerance Security Auditing
Every code modification must be verified against the following security checklist:
1. **ABSPATH Guard:** Ensure all PHP files exit if accessed directly.
2. **Sanitization:** Apply appropriate sanitizers (`sanitize_text_field()`, `absint()`, `esc_url_raw()`) on all settings inputs.
3. **Escaping:** Apply escaping (`esc_html()`, `esc_attr()`, `wp_kses_post()`) on all printed values.
4. **Nonces:** Ensure every settings update or AJAX handler validates WordPress nonces.
5. **Permissions:** Ensure all admin endpoints verify the user's capability via `current_user_can('manage_options')`.
6. **SQL Queries:** Prepare any raw database SQL statements using `$wpdb->prepare()`.

## 4. UI/UX & Accessibility (A11y) Standards
* **Page Redirection:** Avoid redirecting users to separate pages for legal documentation; expand details inline inside the modal context.
* **Focus Trapping:** Modal components must implement a keyboard focus trap forcing focus navigation to loop inside the overlay while active.
* **ARIA Roles:** Implement correct descriptive properties (`role="dialog"`, `aria-modal="true"`, `aria-hidden`, `aria-expanded`).

## 5. Versioning & Changelog
* **Semantic Versioning:** Adhere strictly to Semantic Versioning (`vMAJOR.MINOR.PATCH`).
* **Changelog Format:** Maintain `CHANGELOG.md` according to the *Keep a Changelog* standard.
* **Synchronicity Rule:** Any version bump in the main plugin file header (`gnn-terms-popup.php`) must have a corresponding, descriptive entry in the changelog.

## 6. AI Agent Behavior Guidelines
* **Consultative Support:** Proactively evaluate and suggest modern, native WordPress alternatives. Present optimization options to the human developer before implementing structural changes.
* **No Auto-Commits:** AI agents must never automatically commit or stage files. Prepare precise suggestions and allow the human developer to approve.
* **Context Preservation:** Maintain documentation integrity by updating memory bank tracking session logs during every coding sprint.
