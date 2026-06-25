# ADR 0004: UI/UX Strategy

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
When a user is forced to accept terms, redirecting them to a `/terms/` page breaks their navigation path, causes high bounce rates, and disrupts the checkout or onboarding process. We need a way to let them read the terms immediately without leaving the page.

## Decision
Design the modal to contain a collapsed inline panel. When the user clicks the "Read Terms" button, the modal dynamically expands to show the legal text inline with smooth scrolling.

## Consequences
- Keeps the user on the current page, boosting conversion and acceptance rates.
- Keeps the main popup compact and non-intrusive on load, while allowing full compliance reading when requested.

## Evidence
- CSS styling for `.gnn-terms-full` with height toggles.
- JS event handling to toggle `aria-expanded` and visibility classes on `#gnn-read`.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
