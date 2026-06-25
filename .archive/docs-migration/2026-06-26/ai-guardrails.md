# AI Agent Zero-Tolerance Coding Standard (Plugin Edition)

> **MANDATORY READING.** Every AI agent working on GNN plugins MUST read and internalize
> this document BEFORE writing code. Violations are UNACCEPTABLE.
> The human owner should NEVER have to catch security leaks or prefixing errors.

---

## ABSOLUTE RULES — NO EXCEPTIONS

### 1. Global Prefixing — ALWAYS
Every function, global variable, and CSS class MUST be prefixed with `gnn_terms_` (or the project-specific prefix).

```php
/* ❌ FORBIDDEN — might collide with other plugins */
function render_popup() { ... }

/* ✅ REQUIRED */
function gnn_terms_render_popup() { ... }
```

### 2. Sanitization and Escaping — DEFENSE IN DEPTH
Never trust user input or API responses.
- **Input:** Use `sanitize_text_field()`, `absint()`, `esc_url_raw()`, etc.
- **Output:** Use `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.

```php
/* ❌ DANGEROUS — raw output */
echo $data['content'];

/* ✅ SECURE */
echo wp_kses_post($data['content']);
```

### 3. Nonce Verification — MANDATORY
Every form submission, AJAX request, or admin action MUST verify a nonce.

```php
/* ✅ REQUIRED in processing logic */
if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'gnn_terms_nonce' ) ) {
    wp_die( 'Security check failed' );
}
```

### 4. Permission Checks — MANDATORY
Every admin-facing function MUST check if the current user has permission.

```php
/* ✅ REQUIRED before any admin action */
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}
```

### 5. SQL Preparation — MANDATORY
Never execute raw queries with variables. Always use `$wpdb->prepare()`.

```php
/* ❌ DANGEROUS */
$wpdb->get_results("SELECT * FROM $table WHERE id = $id");

/* ✅ SECURE */
$wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
```

### 6. Theme Compatibility — ZERO CONFLICT CSS
The plugin MUST work on any theme (Black, White, Colorful).
- **Colors:** Never hardcode text colors as `#000` or `#fff`. Use `color: inherit;` or CSS variables.
- **Backgrounds:** Use semi-transparent backgrounds (rgba) or glassmorphism (`backdrop-filter`).

```css
/* ✅ REQUIRED — works on both dark and light themes */
.gnn-terms-popup-container {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    color: inherit;
    border: 1px solid rgba(128, 128, 128, 0.2);
}
```

### 7. ABSPATH Guard — FILE SECURITY
Every PHP file MUST start with the ABSPATH check to prevent direct access.

```php
<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
```

---

## MANDATORY PRE-COMMIT VERIFICATION

Before ANY commit, the AI agent MUST verify:

1. **Prefix Audit:** Search for functions/classes and ensure they all have the `gnn_terms_` or `GNN_Terms_` prefix.
2. **Security Audit:** Verify `esc_html`, `wp_kses_post`, etc., are used on ALL outputs and `sanitize_` on ALL inputs.
3. **Nonce Audit:** Ensure all `POST` or `GET` actions are protected by nonces.
4. **Capability Audit:** Verify `current_user_can()` is used in admin functions.
5. **SQL Audit:** Verify all custom queries (if any) use `$wpdb->prepare()`.
6. **Localization Audit:** Ensure ALL strings are wrapped in `__()` or `_e()`.

---

## THE STANDARD

> **If a human has to find a security flaw or a naming collision, you have failed.**
> Write code that is isolated, secure, and performs optimally.
> Zero tolerance. Zero excuses.
