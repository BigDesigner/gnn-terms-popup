# ADR 0006: Accessibility (A11y)

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
WordPress plugins must be accessible to all users, including those using assistive technologies or keyboard-only navigation. A modal that is overlaying the page can cause issues if keyboard focus can "escape" back to the main document behind it, or if it isn't properly announced by screen readers.

## Decision
Equip the modal with ARIA roles (`role="dialog"`, `aria-modal="true"`, `aria-hidden`, `aria-expanded`) and implement a JavaScript focus trap that forces the tab focus loop to remain inside the active modal until it is accepted (dynamically re-querying visible focusable elements when `#gnn-terms-full` expands, and binding the `keydown` listener once). In the admin settings UI, equip tabbed panels with `role="tabpanel"` and dynamically update `aria-selected` on tab switches.

## Consequences
- The plugin conforms to modern Web Content Accessibility Guidelines (WCAG).
- Screen readers correctly recognize and describe the modal contents and admin tab states.
- Prevents keyboard users from interacting with covered background links.

## Evidence
- Dialog attributes and admin `role="tabpanel"` / `aria-selected` logic in `gnn-terms-popup/gnn-terms-popup.php`.
- Dynamic tab focus trapping JS logic enqueued in the frontend footer output.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/.antigravity/gnn-terms-popup/gnn-terms-popup/gnn-terms-popup.php)
