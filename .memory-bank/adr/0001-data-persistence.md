# ADR 0001: Data Persistence

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
We need a mechanism to track whether a visitor has accepted the terms and conditions. The popup needs to appear once per user until accepted. tying this to a database record for non-authenticated guests adds unnecessary query load on every page visit.

## Decision
Use a browser cookie named `gnn_terms_accepted` to store the user's acceptance status.

## Consequences
- No database query overhead is introduced on the frontend for tracking guest user acceptance.
- The cookie lifetime is configurable via the WordPress settings (default: 365 days).
- Cleared browser cookies or private browsing sessions will cause the modal to reappear.

## Evidence
- `const COOKIE = 'gnn_terms_accepted';` in `gnn-terms-popup.php`.
- Javascript cookie setter logic in `gnn-terms-popup.php`.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
