# Specs: Boundary Conditions & Safety

This document defines the operating boundaries, security constraints, and safety guidelines for the plugin.

## 1. Security Constraints & Escaping
* **Input Sanitization:** `[Verified]` Admin settings inputs (Title, Intro Body, Legal Content) must be sanitized before storage using `wp_kses_post` or `wp_kses` with allowed HTML tag definitions.
* **Output Escaping:** `[Verified]` All dynamic variables printed in frontend templates or admin forms must be escaped using `esc_html`, `esc_attr`, `esc_html_e`, `esc_attr_e`, or `wp_kses_post` to mitigate Cross-Site Scripting (XSS) threats.
* **Direct Execution Prevention:** `[Verified]` Every PHP file must begin with an absolute path check to block direct external execution:
  ```php
  if ( ! defined( 'ABSPATH' ) ) {
      exit;
  }
  ```

## 2. Authentication & Authorization Assumptions
* **Access Capability:** `[Verified]` Admin configuration actions and forms must be restricted to users possessing the `manage_options` capability. Verified via:
  ```php
  if ( ! current_user_can( 'manage_options' ) ) {
      return;
  }
  ```
* **CSRF Mitigation:** `[Verified]` Settings submissions must be protected using native WordPress Nonces via `settings_fields()`.

## 3. Database & Storage Boundaries
* **Storage Engine:** `[Verified]` Standard WordPress options table (`wp_options`). No custom tables are created or modified.
* **SQL Query Safety:** `[Inferred]` While the plugin currently uses standard Options API wrappers (`get_option` / `update_option`), any future custom database query must strictly prepare inputs via `$wpdb->prepare()` to prevent SQL injection vulnerabilities.

## 4. Privacy & Cookie Compliance
* **functional Cookie:** `[Verified]` Accepts states are recorded in the client browser cookie named `gnn_terms_accepted`.
* **Cookie Configuration:** `[Verified]` The cookie duration defaults to 365 days.
* **Security Attributes:** `[Verified]` Cookies are configured with `SameSite=Lax` and the `Secure` flag when operating on HTTPS to prevent tracking misuse.
* **PII Restrictions:** `[Verified]` The cookie stores no Personally Identifiable Information (PII), only functional acceptance state.

## 5. Performance Budget
* **Library Dependencies:** `[Verified]` Strict zero external dependencies rule. Use of jQuery or heavy CSS frameworks is forbidden.
* **Asset Loading Footprint:** `[Verified]` Modal scripts and styles enqueued inline in the footer to prevent blocking page rendering.

## 6. Deployment & CI/CD Boundaries
* **CI Execution Scope:** `[Verified]` The release packaging workflow is restricted to manual execution (`workflow_dispatch`) and requires `contents: write` permissions on GitHub.
