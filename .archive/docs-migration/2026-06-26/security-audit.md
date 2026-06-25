# Security Audit: GNN Terms Popup

## Threat Models & Mitigations

| Threat | Mitigation Strategy | Status |
|--------|---------------------|--------|
| **Cross-Site Scripting (XSS)** | Admin settings (Title, Intro, Legal Text) are sanitized using `wp_kses_post` and `wp_kses` with a strictly defined list of allowed HTML tags. | ✅ Pass |
| **Unauthorized Configuration** | Admin management is restricted to users with `manage_options` capability. `current_user_can` checks are performed before rendering the settings page. | ✅ Pass |
| **CSRF Attacks** | Settings updates are protected via standard WordPress Nonces in the Settings API. | ✅ Pass |
| **Direct File Access** | `ABSPATH` guard prevents direct execution of PHP files by external entities. | ✅ Pass |
| **Information Disclosure** | Data is handled using native WordPress `get_option` and `update_option`, ensuring standard database security. | ✅ Pass |

## Constraints & Rules
1. **Strict Sanitization:** Every input from the admin panel MUST be sanitized before storage. Use `wp_kses` for text blocks to allow formatting while preventing script injection.
2. **Capability Checks:** Always verify `manage_options` before allowing access to settings.
3. **Nonce Verification:** Ensure `settings_fields(self::OPT_KEY)` is used in the admin form to generate nonces.
4. **Cookie Security:** Cookies are set with `SameSite=Lax` and the `Secure` flag when on HTTPS.
5. **Output Escaping:** All dynamic content in the frontend modal is escaped using `esc_html`, `esc_attr`, or `wp_kses_post` as appropriate.
