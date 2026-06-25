# Architectural Decisions: GNN Terms Popup

## 1. Data Persistence
- **Decision:** Use Browser Cookies (`gnn_terms_accepted`) for tracking acceptance.
- **Rationale:** Since acceptance is user-specific and shouldn't necessarily be tied to a WordPress user account (it applies to all visitors), cookies are the most efficient way to maintain state without unnecessary database overhead for every page load.

## 2. Content Source Flexibility
- **Decision:** Dual-source logic for Legal text (Page Slug or Custom Text).
- **Rationale:** Large organizations prefer managing legal documents as standard WordPress Pages (for SEO and general access), while smaller sites might prefer a quick "pasted" text block in the settings. Supporting both maximizes utility.

## 3. Frontend Implementation
- **Decision:** Vanilla JS and Inline CSS.
- **Rationale:** To ensure the plugin is lightweight and compatible with any theme, we avoid external dependencies like jQuery or large CSS frameworks. Inline assets reduce the number of HTTP requests, which is critical for "first-page-load" performance where the popup appears.

## 4. UI/UX Strategy
- **Decision:** Inline Expanding Legal Section.
- **Rationale:** Redirecting a user to a legal page just to read terms is disruptive. By expanding the legal text inside the modal, we keep the user on their current page, increasing the conversion/acceptance rate.

## 5. Design Philosophy
- **Decision:** GNN Brand Palette (#fdb813 & #000000).
- **Rationale:** Aligns with the professional, high-contrast GNN brand identity. The yellow/black combination ensures high visibility and a premium look.

## 6. Accessibility (A11y)
- **Decision:** Focus Trap and Modal ARIA roles.
- **Rationale:** Compliance with modern web standards is mandatory. A focus trap ensures keyboard users don't "tab out" of the modal into the background page while it's active.
