# Specs: Bootstrap & Setup

This document provides setup, bootstrapping, and execution instructions for the project.

## 1. Stack Prerequisites
* **WordPress Core:** `[Inferred]` WordPress 5.0+ (requires native Block Editor / modern settings API support).
* **PHP Environment:** `[Inferred]` PHP 7.4 or later is recommended.
* **Database:** `[Verified]` MySQL/MariaDB database backing the WordPress site. Uses standard `wp_options` table.

## 2. Package & Dependency Management
* **Package Manager:** `[Verified]` None. The plugin utilizes WordPress native APIs and does not include Composer or Node/npm packages.
* **Installation:** `[Inferred]`
  1. Extract the release zip or clone this repository under the `wp-content/plugins/gnn-terms-popup` folder of your WordPress installation.
  2. Activate the plugin through the 'Plugins' menu in the WordPress Admin dashboard.

## 3. Build & Development Servers
* **Build Step:** `[Verified]` None required. The plugin uses vanilla CSS and JavaScript which are enqueued inline/locally.
* **Development Server:** `[Inferred]` Runs within any standard local WordPress hosting tool (e.g. LocalWP, DDEV, XAMPP, or a Dockerized WordPress stack).

## 4. CI/CD Pipelines
* **Pipeline Provider:** `[Verified]` GitHub Actions.
* **Workflow File:** `[Verified]` [.github/workflows/release.yml](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/.github/workflows/release.yml)
* **Pipeline Name:** Manual Release.
* **Trigger Conditions:** `[Verified]` Manual trigger via `workflow_dispatch` (optional input: `version_override`).
* **Workflow Jobs:**
  - `build-and-release`: Checks out code, extracts version, runs `rsync` to package clean production assets (excluding dev tools and AI documentation), zips it, and publishes a new GitHub Release with the ZIP attached.

## 5. Deployment Topology
* **Target Environment:** `[Verified]` Self-hosted WordPress instances or WordPress VIP/Cloud environments.
* **Updates distribution:** `[Verified]` Distributed directly via GitHub Releases. The plugin includes a local updater engine ([updater.php](file:///c:/Users/bigde/Documents/Project/gnn-terms-popup/inc/updater.php)) checking releases.

## 6. Suggested Validation Commands
* **PHP Syntax Validation:** `[Inferred]`
  - `php -l gnn-terms-popup.php`
  - `php -l inc/updater.php`
