# ADR 0007: GitHub Actions Release Workflow

* **Status:** Accepted
* **Confidence:** Verified
* **Author:** BigDesigner

## Context
Deploying releases manually is error-prone. We need a secure, automated way to package production-ready releases of the WordPress plugin, excluding development tools, testing scripts, and internal AI documentation, and publish them to GitHub.

## Decision
Utilize a GitHub Actions workflow triggered manually via `workflow_dispatch` to package the clean production build into a ZIP archive and create a GitHub Release.

## Consequences
- Dev files (such as `.git`, `.github`, `memory-bank`, `.memory-bank`, markdown documentations) are automatically excluded via `rsync`.
- Versions are either automatically parsed from the plugin file header or specified by user override input.
- Releases are consistently zipped and distributed directly from the repository's Releases page.

## Evidence
- Workflow file `.github/workflows/release.yml` exists and implements `workflow_dispatch` and `softprops/action-gh-release@v3`.

## Related Files
- [.github/workflows/release.yml](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/.github/workflows/release.yml)
