# ADR 0010: Unified GNN GitHub Updater Standard

- **Status**: Accepted
- **Confidence**: Verified
- **Date**: 2026-11-21
- **Category**: Maintainability & Security
- **Supersedes**: None
- **Superseded By**: None

## Context
The previous `GNN_Terms_Popup_Updater` implementation in `gnn-terms-popup/inc/updater.php` had several gaps compared to the Unified GNN GitHub Updater standard:
1. No HTML/HEAD fallback when `api.github.com` is rate-limited or unreachable.
2. No SSRF HTTPS host allowlist validation on release/download URLs, no semver tag validation, and `get_local_version()` did not prefer `GNN_Terms_Popup::VERSION`.
3. `check_for_update()` did not guard `!is_object($transient)`, did not initialize `$transient->response`/`$transient->no_update` as arrays, omitted `id`/`requires`/`tested`/`requires_php`, and did not populate `no_update`.
4. `after_install()` did not hook `upgrader_pre_install` to preserve and restore plugin activation state, did not pass `$overwrite = true` to `$wp_filesystem->move()`, did not normalize paths with `wp_normalize_path()`, did not guard `is_wp_error($response)`/`is_wp_error($result)`, and did not call `wp_clean_plugins_cache(true)`.
5. `handle_manual_check()` lacked `403` response status on nonce failure `wp_die()` and passed `403` as the title argument instead of `['response' => 403]` on permission failure.

## Decision
Standardize `GNN_Terms_Popup_Updater` (`gnn-terms-popup/inc/updater.php`) on the Unified GNN GitHub Updater architecture while preserving existing identifiers (`gnn_terms_github_update_check`, `gnn_terms_check_update`, `gnn_terms_manual_update`):
1. Use `GNN_Terms_Popup::VERSION` in `get_local_version()` with `get_plugin_data()` fallback.
2. Add `upgrader_pre_install` (`before_install`) to record `$this->was_active` before files are replaced, and restore activation + `wp_clean_plugins_cache(true)` in `after_install()`.
3. Add `wp_remote_head()` (`redirection => 0`) + `/releases/expanded_assets/{tag}` HTML scraping fallback when the GitHub REST API fails or is rate-limited.
4. Enforce semver tag validation, `.zip` suffix preference, SSRF HTTPS host allowlist (`sanitize_github_url()`), and 300s negative cache sentinel `(object)['failed' => true]`.
5. Populate `response` and `no_update` cleanly in `check_for_update()`, normalize filesystem paths in `after_install()`, fix `wp_die(..., '', ['response' => 403])`, and keep `clear_cache()` scoped strictly to `$this->transient_key`.

## Verification
- `php -l gnn-terms-popup/inc/updater.php` passed with zero syntax errors.


## Amendment (2026-11-25): Disk-First Local Version Detection
- **Root Cause:** Reading in-memory version constants (`GNN_*_VERSION` / `GNN_Terms_Popup::VERSION`) first inside `get_local_version()` returned the pre-upgrade version when WordPress fired `pre_set_site_transient_update_plugins` within the same PHP request right after overwriting plugin files, causing the update banner to persist until a second update run.
- **Resolution:** `get_local_version()` now reads the live `Version` header directly from `WP_PLUGIN_DIR . '/' . $this->plugin_basename` via `clearstatcache( true, $main_file )` and `get_file_data()` first, falling back to in-memory constants only if the file is unavailable.
