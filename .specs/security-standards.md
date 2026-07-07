# Security and Dependency Standards

This document establishes the definitive security, dependency management, and input validation contract for the GNN Terms Popup plugin. All code changes and additions must strictly adhere to these standards.

---

## 1. Ecosystem-Specific Mitigations (WordPress)

As a WordPress plugin, the codebase must mitigate common security vectors using native WordPress API functions:

### 1.1 Direct File Access Prevention
*   Every PHP file must start with an absolute path check to prevent direct execution:
    ```php
    if (!defined('ABSPATH')) {
        exit;
    }
    ```

### 1.2 Cross-Site Request Forgery (CSRF) Mitigation
*   All administrative actions, settings page saves, and manual processes must be validated using WordPress nonces:
    *   Use `settings_fields()` in forms to output default Settings API security nonces.
    *   Verify manual action nonces with `wp_verify_nonce()` and sanitize the source tokens.
    *   Generate secure link URLs using `wp_nonce_url()`.

### 1.3 Cross-Site Scripting (XSS) Mitigation
*   All data displayed on the screen or injected into scripts must be escaped right before rendering (late escaping):
    *   Use `esc_html()` for plain text output.
    *   Use `esc_attr()` for outputting into HTML attributes.
    *   Use `esc_url()` for URLs, including `admin_url()`.
    *   Use `wp_kses_post()` or `wp_kses()` with strict allowed tags arrays for rendering raw HTML (such as dynamic legal content).
    *   Use `json_encode()` (or `esc_js()`) for injecting PHP values into `<script>` context block variables.

---

## 2. Input Validation & Sanitization

All incoming request parameters, inputs, cookies, and options must be validated and sanitized before evaluation, database persistence, or comparisons:

*   **Cookies:** Standardize cookie reading. Always fetch and process cookie data through:
    ```php
    sanitize_text_field(wp_unslash($_COOKIE['cookie_name']))
    ```
*   **Query Parameters:** Sanitize all query strings from `$_GET` and request variables from `$_POST` using `sanitize_text_field(wp_unslash(...))` or standard typecast parameters.
*   **Settings Form Fields:**
    *   Hex color properties must be validated via `sanitize_hex_color()` with logical default fallbacks (e.g. `sanitize_hex_color($input) ?? $default`).
    *   Slugs must be processed via `sanitize_title()`.
    *   Integer boundaries (such as cookie days expiration) must be cast to `intval()` and range-checked (e.g. `1` to `3650`).

---

## 3. Dependency Management Rules

*   **Latest Stable Versions:** Always favor the latest stable versions of dependencies, WordPress APIs, and external SDKs.
*   **Deprecated Features:** Eliminate deprecated functions and hooks immediately. Code must not generate notice logs or rely on legacy functions (e.g., use standard script enqueuing pipelines instead of manual loading hacks).
*   **Automated Updates:** The updater class (`inc/updater.php`) must secure the remote tag lookup pipeline and handle lookup failures gracefully using transient sentinel caching.

---

## 4. Authentication & Authorization

*   **Capability Validation:**
    *   Access to the administrative settings interface must be limited to users possessing the `manage_options` capability.
    *   Access to trigger updates or manual checks must be restricted to users possessing the `update_plugins` capability.
    *   Always verify capabilities using `current_user_can()` right before executing administrative settings logic.
