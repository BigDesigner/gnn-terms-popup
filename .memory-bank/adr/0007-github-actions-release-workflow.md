# ADR 0007: GitHub Actions Release Workflow

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
Deploying releases manually is error-prone. We need a secure, automated way to package production-ready releases of the WordPress plugin, excluding development tools, testing scripts, and internal AI documentation, and publish them to GitHub.

## Decision
Utilize a GitHub Actions workflow triggered manually via `workflow_dispatch` to package the clean production build into a ZIP archive and create a GitHub Release.

## Consequences
- Plugin source lives under `gnn-terms-popup/`, and `rsync --exclude='*.md'` packages only production plugin files into `gnn-terms-popup-<version>.zip`, automatically excluding `.git`, `.github`, `.memory-bank`, `.specs`, `.tasks`, `.agents`, and root markdown documentation.
- Versions are either automatically parsed from `gnn-terms-popup/gnn-terms-popup.php` or specified by user override input.
- Releases are consistently zipped and distributed directly from the repository's Releases page.

## Evidence
- Workflow file `.github/workflows/release.yml` exists and implements `workflow_dispatch` and `softprops/action-gh-release@v3`.

## Related Files
- [release.yml](file:///c:/Users/bigde/.antigravity/gnn-terms-popup/.github/workflows/release.yml)
