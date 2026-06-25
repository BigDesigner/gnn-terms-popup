# ADR 0005: Design Philosophy

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
The plugin needs to align with the core GNN brand identity across websites. Additionally, the popup should look professional on any theme—whether light, dark, or multi-colored—without clash or broken text visibility.

## Decision
Utilize the standardized GNN high-contrast brand palette: Yellow (`#fdb813`) and Black (`#000000`). Design the CSS using color inheritance (`color: inherit;`) and translucent backgrounds (`rgba(..., 0.05)`) with glassmorphic blurs (`backdrop-filter`) to integrate seamlessly with the hosting theme's background.

## Consequences
- Reinforces consistent GNN visual branding.
- Ensures text remains readable regardless of whether the site runs on a dark theme or light theme.
- High visual appeal with modern glassmorphism.

## Evidence
- Primary color default is `#fdb813` and secondary is `#000000` in the plugin options.
- CSS uses backdrop-filter, flexbox, and relative color declarations.

## Related Files
- [gnn-terms-popup.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/gnn-terms-popup.php)
