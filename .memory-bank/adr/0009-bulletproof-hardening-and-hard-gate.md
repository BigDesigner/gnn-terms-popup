# ADR 0009: Bulletproof Hardening and Server-Side Hard Gate

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-10-15
- **Category**: Auth
- **Supersedes**: None
- **Superseded By**: None

## Context
A comprehensive architectural audit of the terms acceptance popup revealed several bypass vectors and operational failure modes:
1. **Client-Side Soft Overlay Bypass:** The modal is enqueued in `wp_footer` after the full page content is already rendered. Users can inspect the DOM via DevTools, disable JavaScript, or use element blockers (AdBlock/uBlock) to consume site content without accepting terms.
2. **Page Cache Poisoning:** In `render_modal()`, the server skips outputting the modal if `$_COOKIE['gnn_terms_accepted']` is present or if the user is an admin. If a reverse proxy or caching plugin (Cloudflare, WP Rocket, LiteSpeed) caches this response, first-time visitors receive the cached HTML lacking the modal markup entirely.
3. **Subpath False Positives:** The URL scope evaluation used `p.indexOf(item) === 0`, causing prefix collisions (e.g., scoping `/about` inadvertently matched `/about-us` and `/about-team`).
4. **Hardcoded Plugin Slug:** `inc/updater.php` hardcoded `gnn-terms-popup/gnn-terms-popup.php`, causing updater failures and broken directory relocations if the directory was renamed.

## Decision
1. **Dual Enforcement Strategy (Soft Overlay vs. Server-Side Hard Gate):**
   - Provide an optional configurable "Hard Gate" mode in admin settings.
   - When Hard Gate is active, hook into `template_redirect` with high priority. If terms are not accepted, immediately halt standard theme template loading and render a standalone, isolated terms acceptance template (with `status_header(403)` or clean barrier view), ensuring zero underlying site content reaches the client.
   - When Soft Overlay is active, maintain the existing inline modal behavior for standard consent banners.
2. **Cache-Safe DOM Invariance:**
   - In Soft Overlay mode, render the modal container unconditionally in the HTML DOM (defaulting to hidden state `display:none`). Rely strictly on client-side JS evaluation of the cookie to trigger `showModal()`. Never conditionally omit modal HTML on the server based on cookie presence, preventing cache engines from storing modals-free pages for unauthenticated visitors.
3. **Boundary-Safe Path Matching:**
   - Enforce exact match or delimited directory boundary matching:
     `item === p || p.startsWith(item + '/')` (and handling root `/` accurately).
4. **Dynamic Plugin Slug and Path Resolution:**
   - Refactor `inc/updater.php` to derive the plugin slug dynamically using `plugin_basename(dirname(__DIR__) . '/gnn-terms-popup.php')` instead of a static string.

## Consequences
- **Security & Compliance:** Regulatory and strict compliance sites (e.g., NDA, age verification, legally binding terms) can enable Hard Gate to prevent any data leakage prior to consent.
- **Cache Compatibility:** Static full-page caching works transparently without serving broken or missing modal states.
- **Path Precision:** Scope targeting is isolated to designated paths and their actual sub-routes, eliminating unexpected overlays on sibling slugs.
- **Portability:** The updater and installation handlers withstand custom directory renames.

## Evidence
- Security & Architectural Audit report (2026-10-15).
- Source references: `gnn-terms-popup/gnn-terms-popup.php` (lines 519-528, 668-670) and `gnn-terms-popup/inc/updater.php` (line 25).
