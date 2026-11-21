<?php
/**
 * GNN GitHub Plugin Updater for Terms Popup
 *
 * Checks the GitHub Releases API for new versions and integrates
 * with the WordPress plugin update system.
 *
 * @package GNN_Terms_Popup
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class GNN_Terms_Popup_Updater
 */
class GNN_Terms_Popup_Updater
{
    /**
     * GitHub repository owner/name.
     *
     * @var string
     */
    private $repo = 'BigDesigner/gnn-terms-popup';

    /**
     * Plugin directory slug.
     *
     * @var string
     */
    private $slug = 'gnn-terms-popup';

    /**
     * Plugin basename (directory/main-file.php).
     *
     * @var string
     */
    private $plugin_basename;

    /**
     * Transient key for caching the GitHub release response.
     *
     * @var string
     */
    private $transient_key = 'gnn_terms_github_update_check';

    /**
     * Cache duration (12 hours).
     *
     * @var int
     */
    private $cache_duration = 12 * HOUR_IN_SECONDS;

    /**
     * Whether the plugin was active prior to an update.
     *
     * @var bool
     */
    private $was_active = false;

    /**
     * Initialize hooks.
     */
    public function __construct()
    {
        $this->plugin_basename = plugin_basename(dirname(__DIR__) . '/gnn-terms-popup.php');

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_pre_install', array($this, 'before_install'), 10, 2);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);

        add_action('admin_init', array($this, 'handle_manual_check'));
        add_action('load-update-core.php', array($this, 'clear_cache'));
    }

    /**
     * Get the current local plugin version.
     *
     * @return string
     */
    private function get_local_version()
    {
        if (defined('GNN_Terms_Popup::VERSION')) {
            return (string) GNN_Terms_Popup::VERSION;
        }

        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $main_file   = WP_PLUGIN_DIR . '/' . $this->plugin_basename;
        $plugin_data = get_plugin_data($main_file, false, false);

        return !empty($plugin_data['Version']) ? (string) $plugin_data['Version'] : '0.0.0';
    }

    /**
     * Validate and sanitize a GitHub HTTPS URL against an SSRF allowlist.
     *
     * @param string $url Raw URL.
     * @return string Sanitized HTTPS URL or empty string if invalid.
     */
    private function sanitize_github_url($url)
    {
        $sanitized = esc_url_raw((string) $url, array('https'));
        if (empty($sanitized)) {
            return '';
        }

        $parsed = wp_parse_url($sanitized);
        if (empty($parsed['scheme']) || 'https' !== strtolower($parsed['scheme']) || empty($parsed['host'])) {
            return '';
        }

        $host = strtolower($parsed['host']);
        $allowed_hosts = array(
            'github.com',
            'api.github.com',
            'codeload.github.com',
            'objects.githubusercontent.com',
            'release-assets.githubusercontent.com',
        );

        $is_allowed = in_array($host, $allowed_hosts, true)
            || (strlen($host) > 22 && '.githubusercontent.com' === substr($host, -22));

        return $is_allowed ? $sanitized : '';
    }

    /**
     * Fetch the latest release data from GitHub API with HTML/HEAD fallback.
     *
     * @return object|false
     */
    private function get_remote_release()
    {
        $cached = get_transient($this->transient_key);
        if (false !== $cached) {
            if ('error' === $cached || (is_object($cached) && !empty($cached->failed))) {
                return false;
            }
            if (is_object($cached) && !empty($cached->version) && !empty($cached->download_url)) {
                return $cached;
            }
        }

        $release_data = $this->fetch_from_api();
        if (false === $release_data) {
            $release_data = $this->fetch_from_web_fallback();
        }

        if (false === $release_data) {
            set_transient($this->transient_key, (object) array('failed' => true), 300);
            return false;
        }

        set_transient($this->transient_key, $release_data, $this->cache_duration);

        return $release_data;
    }

    /**
     * Fetch latest release metadata via GitHub REST API.
     *
     * @return object|false
     */
    private function fetch_from_api()
    {
        $url  = sprintf('https://api.github.com/repos/%s/releases/latest', $this->repo);
        $args = array(
            'headers' => array(
                'Accept'     => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            ),
            'timeout' => 10,
        );

        $response = wp_remote_get($url, $args);
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response));
        if (empty($body) || !is_object($body) || empty($body->tag_name)) {
            return false;
        }

        $remote_version = sanitize_text_field(ltrim((string) $body->tag_name, 'vV'));
        if (!preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/', $remote_version)) {
            return false;
        }

        $download_url = '';
        if (!empty($body->assets) && is_array($body->assets)) {
            foreach ($body->assets as $asset) {
                if (!empty($asset->name) && !empty($asset->browser_download_url) && '.zip' === strtolower(substr((string) $asset->name, -4))) {
                    $download_url = $this->sanitize_github_url($asset->browser_download_url);
                    if (!empty($download_url)) {
                        break;
                    }
                }
            }
        }

        if (empty($download_url) && !empty($body->zipball_url)) {
            $download_url = $this->sanitize_github_url($body->zipball_url);
        }

        if (empty($download_url)) {
            return false;
        }

        return (object) array(
            'version'      => $remote_version,
            'download_url' => $download_url,
            'changelog'    => isset($body->body) ? (string) $body->body : '',
            'published_at' => isset($body->published_at) ? sanitize_text_field((string) $body->published_at) : '',
            'html_url'     => isset($body->html_url) ? $this->sanitize_github_url($body->html_url) : '',
        );
    }

    /**
     * Fallback release lookup when GitHub API is unavailable or rate-limited.
     *
     * @return object|false
     */
    private function fetch_from_web_fallback()
    {
        $user_agent = 'WordPress/' . get_bloginfo('version') . '; ' . home_url();
        $head       = wp_remote_head(
            sprintf('https://github.com/%s/releases/latest', $this->repo),
            array(
                'redirection' => 0,
                'timeout'     => 10,
                'headers'     => array('User-Agent' => $user_agent),
            )
        );

        if (is_wp_error($head)) {
            return false;
        }

        $location = (string) wp_remote_retrieve_header($head, 'location');
        if (!preg_match('#/releases/tag/([^/?\#]+)#', $location, $tag_match)) {
            return false;
        }

        $tag            = rawurldecode($tag_match[1]);
        $remote_version = sanitize_text_field(ltrim($tag, 'vV'));
        if (!preg_match('/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/', $remote_version)) {
            return false;
        }

        $download_url = '';
        $assets_url   = sprintf('https://github.com/%s/releases/expanded_assets/%s', $this->repo, rawurlencode($tag));
        $assets_resp  = wp_remote_get(
            $assets_url,
            array(
                'timeout' => 10,
                'headers' => array('User-Agent' => $user_agent),
            )
        );

        if (!is_wp_error($assets_resp) && 200 === wp_remote_retrieve_response_code($assets_resp)) {
            $html = (string) wp_remote_retrieve_body($assets_resp);
            if (preg_match_all('/href=["\']([^"\']+\/releases\/download\/[^"\']+\.zip)["\']/i', $html, $matches)) {
                foreach ($matches[1] as $href) {
                    $candidate = (0 === strpos($href, '/')) ? 'https://github.com' . $href : $href;
                    $candidate = $this->sanitize_github_url($candidate);
                    if (!empty($candidate)) {
                        $download_url = $candidate;
                        break;
                    }
                }
            }
        }

        if (empty($download_url)) {
            $download_url = $this->sanitize_github_url(
                sprintf('https://github.com/%s/archive/refs/tags/%s.zip', $this->repo, rawurlencode($tag))
            );
        }

        if (empty($download_url)) {
            return false;
        }

        return (object) array(
            'version'      => $remote_version,
            'download_url' => $download_url,
            'changelog'    => '',
            'published_at' => '',
            'html_url'     => $this->sanitize_github_url(sprintf('https://github.com/%s/releases/tag/%s', $this->repo, rawurlencode($tag))),
        );
    }

    /**
     * Hook into the WordPress plugin update transient.
     *
     * @param object $transient Update transient object.
     * @return object
     */
    public function check_for_update($transient)
    {
        if (empty($transient) || !is_object($transient) || empty($transient->checked)) {
            return $transient;
        }

        if (!isset($transient->response) || !is_array($transient->response)) {
            $transient->response = array();
        }
        if (!isset($transient->no_update) || !is_array($transient->no_update)) {
            $transient->no_update = array();
        }

        $release = $this->get_remote_release();
        if (false === $release) {
            return $transient;
        }

        $local_version = $this->get_local_version();

        if (version_compare($release->version, $local_version, '>')) {
            $transient->response[$this->plugin_basename] = (object) array(
                'id'           => 'https://github.com/' . $this->repo,
                'slug'         => $this->slug,
                'plugin'       => $this->plugin_basename,
                'new_version'  => $release->version,
                'url'          => $release->html_url,
                'package'      => $release->download_url,
                'requires'     => '5.0',
                'tested'       => '7.2',
                'requires_php' => '7.4',
            );
            unset($transient->no_update[$this->plugin_basename]);
        } else {
            unset($transient->response[$this->plugin_basename]);
            $transient->no_update[$this->plugin_basename] = (object) array(
                'id'          => 'https://github.com/' . $this->repo,
                'slug'        => $this->slug,
                'plugin'      => $this->plugin_basename,
                'new_version' => $local_version,
                'url'         => $release->html_url,
                'package'     => '',
            );
        }

        return $transient;
    }

    /**
     * Provide plugin information for the update details modal.
     *
     * @param false|object|array $result Result object or false.
     * @param string             $action API action.
     * @param object             $args   Request arguments.
     * @return false|object|array
     */
    public function plugin_info($result, $action, $args)
    {
        if ('plugin_information' !== $action || empty($args->slug) || $this->slug !== $args->slug) {
            return $result;
        }

        $release = $this->get_remote_release();
        if (false === $release) {
            return $result;
        }

        return (object) array(
            'name'          => 'GNN Terms Popup',
            'slug'          => $this->slug,
            'version'       => $release->version,
            'author'        => 'BigDesigner',
            'homepage'      => 'https://github.com/BigDesigner/gnn-terms-popup',
            'requires'      => '5.0',
            'tested'        => '7.2',
            'requires_php'  => '7.4',
            'last_updated'  => $release->published_at,
            'download_link' => $release->download_url,
            'sections'      => array(
                'description' => 'One-time Terms acceptance popup with admin settings and inline expanding Legal text.',
                'changelog'   => nl2br(esc_html($release->changelog)),
            ),
        );
    }

    /**
     * Record whether the plugin was active before the upgrader replaces files.
     *
     * @param bool|WP_Error $response   Installation response.
     * @param array         $hook_extra Extra arguments passed to hooked filters.
     * @return bool|WP_Error
     */
    public function before_install($response, $hook_extra)
    {
        if (isset($hook_extra['plugin']) && $hook_extra['plugin'] === $this->plugin_basename) {
            if (!function_exists('is_plugin_active')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $this->was_active = is_plugin_active($this->plugin_basename);
        }

        return $response;
    }

    /**
     * Normalize extracted plugin directory and restore activation state after install.
     *
     * @param bool|WP_Error  $response   Installation response.
     * @param array          $hook_extra Extra arguments passed to hooked filters.
     * @param array|WP_Error $result     Installation result data.
     * @return array|WP_Error
     */
    public function after_install($response, $hook_extra, $result)
    {
        if (is_wp_error($response)) {
            return $response;
        }
        if (is_wp_error($result) || !is_array($result)) {
            return $result;
        }
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_basename) {
            return $result;
        }

        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }

        $plugin_dir  = wp_normalize_path(untrailingslashit(WP_PLUGIN_DIR . '/' . $this->slug));
        $destination = !empty($result['destination']) ? wp_normalize_path(untrailingslashit($result['destination'])) : '';

        if (!empty($wp_filesystem) && '' !== $destination && $destination !== $plugin_dir && $wp_filesystem->exists($destination)) {
            if ($wp_filesystem->move($destination, $plugin_dir, true)) {
                $result['destination']      = trailingslashit($plugin_dir);
                $result['destination_name'] = $this->slug;
            }
        }

        wp_clean_plugins_cache(true);

        if ($this->was_active) {
            if (!function_exists('activate_plugin')) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            activate_plugin($this->plugin_basename);
            $this->was_active = false;
        }

        delete_transient($this->transient_key);

        return $result;
    }

    /**
     * Handle manual update check requests from the plugins list.
     */
    public function handle_manual_check()
    {
        if (isset($_GET['gnn_terms_check_update']) && '1' === sanitize_text_field(wp_unslash($_GET['gnn_terms_check_update']))) {
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'gnn_terms_manual_update')) {
                wp_die(esc_html__('Security check failed.', 'gnn-terms-popup'), '', array('response' => 403));
            }

            if (!current_user_can('update_plugins')) {
                wp_die(esc_html__('You do not have permission to perform this action.', 'gnn-terms-popup'), '', array('response' => 403));
            }

            delete_transient($this->transient_key);
            delete_site_transient('update_plugins');

            wp_safe_redirect(admin_url('update-core.php?force-check=1'));
            exit;
        }
    }

    /**
     * Clear updater cache when visiting the core updates screen.
     */
    public function clear_cache()
    {
        delete_transient($this->transient_key);
    }
}

new GNN_Terms_Popup_Updater();
