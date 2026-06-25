# Architecture Notes: GNN Terms Popup

## System Design
GNN Terms Popup is a lightweight, one-time Terms & Conditions acceptance plugin for WordPress. It ensures legal compliance by presenting users with a mandatory acceptance modal upon their first visit, utilizing browser cookies for persistence.

## Data Flow
1. **Initialization:**
    - On every public page load (or specific paths), the plugin checks for the presence of the `gnn_terms_accepted` cookie.
    - If the cookie is missing and the user is not an administrator (optional), the popup logic is enqueued.
2. **Rendering:**
    - The modal is rendered in the `wp_footer` to ensure it doesn't block critical rendering paths.
    - Content (Title, Intro, Legal text) is fetched from plugin settings.
    - Legal text can be pulled dynamically from a WordPress page (via slug) or a custom field in the settings.
3. **Acceptance:**
    - When the user clicks "I Agree", a JavaScript function sets the `gnn_terms_accepted` cookie.
    - The cookie lifetime is configurable (default: 365 days).
    - The modal is hidden, and the `gnn-no-scroll` class is removed from the body.

## Key Components
- **Admin Settings API:** Centralized configuration for content, behavior, and appearance.
- **Dynamic Content Loader:** Logic to fetch legal content from either a specific page slug or a custom text block.
- **Frontend Controller:** Vanilla JS and CSS for the modal interface, ensuring zero external dependencies.
- **A11y Module:** Implements focus trapping and ARIA roles for accessibility compliance.

## Core Principles
1. **Visual Excellence:**
    - Uses a premium #fdb813 (yellow) and #000000 (black) color palette.
    - Clean, modern typography and smooth transitions.
2. **Performance:**
    - Minimal footprint: no jQuery required, no external fonts/libraries.
    - Inline CSS/JS to reduce HTTP requests.
3. **Reliability:**
    - Robust fallback: if a page slug is missing, it falls back to custom text or a helpful error message.
    - Native WordPress hooks used throughout for maximum compatibility.

## Security & Compliance
- **Input Sanitization:** All admin settings are sanitized via `wp_kses_post`, `sanitize_title`, and `sanitize_text_field`.
- **Capability Checks:** Admin pages are restricted to users with `manage_options` permissions.
- **Privacy:** Uses functional cookies only; compliant with standard privacy guidelines.
