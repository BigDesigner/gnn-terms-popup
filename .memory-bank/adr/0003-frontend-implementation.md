# ADR 0003: Frontend Implementation

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
First-page-load performance is critical for user retention. If the modal blocks rendering or relies on heavy external libraries, it will degrade SEO and load speeds. Additionally, WordPress themes use varying versions of libraries like jQuery, leading to potential dependency conflicts.

## Decision
Build the frontend popup exclusively with Vanilla JavaScript and Vanilla CSS. Output assets inline or enqueued with cache-busting logic, ensuring no external dependencies are required.

## Consequences
- Fast initial page rendering with zero external HTTP requests for stylesheets or JS scripts.
- Highly resilient and theme-independent implementation.
- Avoids jQuery version conflicts.

## Evidence
- Custom CSS is enqueued and printed inside `gnn-terms-popup.php`.
- Frontend Javascript contains native DOM event listeners and vanilla cookie handling.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
