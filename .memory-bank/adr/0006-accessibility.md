# ADR 0006: Accessibility (A11y)

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
WordPress plugins must be accessible to all users, including those using assistive technologies or keyboard-only navigation. A modal that is overlaying the page can cause issues if keyboard focus can "escape" back to the main document behind it, or if it isn't properly announced by screen readers.

## Decision
Equip the modal with ARIA roles (`role="dialog"`, `aria-modal="true"`, `aria-hidden`, `aria-expanded`) and implement a Javascript focus trap that forces the tab focus loop to remain inside the active modal until it is accepted.

## Consequences
- The plugin conforms to modern Web Content Accessibility Guidelines (WCAG).
- Screen readers correctly recognize and describe the modal contents.
- Prevent keyboard users from interacting with covered background links.

## Evidence
- Dialog attributes printed in PHP output for the modal element.
- Tab focus trapping JS logic enqueued in the frontend footer output.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
