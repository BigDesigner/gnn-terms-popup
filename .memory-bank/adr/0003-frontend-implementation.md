# ADR 0003: Frontend Implementation

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
First-page-load performance is critical for user retention. If the modal blocks rendering or relies on heavy external libraries, it will degrade SEO and load speeds. Additionally, WordPress themes use varying versions of libraries like jQuery, leading to potential dependency conflicts.

## Decision
Build the frontend popup exclusively with Vanilla JavaScript and Vanilla CSS. Output assets by printing style and script tags directly inline inside `render_modal` in `wp_footer`. This avoids issues where caching, minification, or optimization plugins (e.g. WP Rocket, LiteSpeed Cache) strip or drop enqueued virtual assets that do not have physical source files.

## Consequences
- Fast initial page rendering with zero external HTTP requests for stylesheets or JS scripts.
- Highly resilient and theme-independent implementation.
- Avoids jQuery version conflicts.
- Guarantees asset availability regardless of site-level caching/minification optimization setups.

## Evidence
- Custom CSS and JS are printed inline inside the `render_modal` method in `gnn-terms-popup.php`.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
