# ADR 0002: Content Source Flexibility

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
Administrators have differing workflows for publishing legal terms. Large organizations typically manage legal pages as standard WordPress Page posts for SEO, revisions, and central access. Smaller sites want a simple, direct custom text editor within the plugin settings to avoid creating extra page posts.

## Decision
Support a dual-source legal content delivery model:
1. Fetching content dynamically from a WordPress page using its slug (e.g. `legal`).
2. Fetching custom text entered into a Rich Text editor (TinyMCE) on the plugin's settings page.

## Consequences
- Maximizes usability for both enterprise and small-scale WordPress websites.
- Introduce fallback handling: if the page slug does not exist, the plugin falls back to custom text or a warning.

## Evidence
- Default settings specify `'legal_source' => 'page'` and `'legal_page_slug' => 'legal'`.
- Conditional rendering based on `legal_source` in the footer render logic.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
