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

class GNN_Terms_Popup_Updater
{
    /**
     * GitHub repository owner/name.
     */
    private $repo = 'BigDesigner/gnn-terms-popup';

    /**
     * Plugin slug (directory name/main file).
     */
    private $plugin_slug = 'gnn-terms-popup/gnn-terms-popup.php';

    /**
     * Transient key for caching the GitHub API response.
     */
    private $transient_key = 'gnn_terms_github_update_check';

    /**
     * Cache duration (12 hours).
     */
    private $cache_duration = 43200;

    /**
     * Initialize hooks.
     */
    public function __construct()
    {
        // Add filters for the update pipeline
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);

        // Admin hooks
        add_action('admin_init', array($this, 'handle_manual_check'));
        add_action('load-update-core.php', array($this, 'clear_cache'));
    }

    /**
     * Get the current plugin version.
     */
    private function get_local_version()
    {
        if (!function_exists('get_plugin_data')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->plugin_slug);
        return $plugin_data['Version'];
    }

    /**
     * Fetch the latest release data from GitHub API.
     */
    private function get_remote_release()
    {
        $cached = get_transient($this->transient_key);
        if (false !== $cached) {
            return $cached;
        }

        $url = sprintf('https://api.github.com/repos/%s/releases/latest', $this->repo);
        $args = array(
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            ),
            'timeout' => 10,
        );

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            set_transient($this->transient_key, false, 300); // Cache failure for 5 mins
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response));

        if (empty($body) || !isset($body->tag_name)) {
            return false;
        }

        $remote_version = ltrim($body->tag_name, 'v');
        $download_url = '';

        if (!empty($body->assets)) {
            foreach ($body->assets as $asset) {
                if (strpos($asset->name, '.zip') !== false) {
                    $download_url = $asset->browser_download_url;
                    break;
                }
            }
        }

        if (empty($download_url)) {
            $download_url = $body->zipball_url;
        }

        $release_data = (object) array(
            'version'      => $remote_version,
            'download_url' => $download_url,
            'changelog'    => isset($body->body) ? $body->body : '',
            'published_at' => isset($body->published_at) ? $body->published_at : '',
            'html_url'     => isset($body->html_url) ? $body->html_url : '',
        );

        set_transient($this->transient_key, $release_data, $this->cache_duration);

        return $release_data;
    }

    /**
     * Hook into the plugin update check.
     */
    public function check_for_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $release = $this->get_remote_release();
        if (false === $release) {
            return $transient;
        }

        $local_version = $this->get_local_version();

        if (version_compare($release->version, $local_version, '>')) {
            $obj = new stdClass();
            $obj->slug        = 'gnn-terms-popup';
            $obj->plugin      = $this->plugin_slug;
            $obj->new_version = $release->version;
            $obj->url         = $release->html_url;
            $obj->package     = $release->download_url;
            
            $transient->response[$this->plugin_slug] = $obj;
        }

        return $transient;
    }

    /**
     * Provide plugin information for the update details popup.
     */
    public function plugin_info($result, $action, $args)
    {
        if ('plugin_information' !== $action || 'gnn-terms-popup' !== $args->slug) {
            return $result;
        }

        $release = $this->get_remote_release();
        if (false === $release) {
            return $result;
        }

        return (object) array(
            'name'          => 'GNN Terms Popup',
            'slug'          => 'gnn-terms-popup',
            'version'       => $release->version,
            'author'        => 'BigDesigner',
            'download_link' => $release->download_url,
            'sections'      => array(
                'description' => 'One-time Terms acceptance popup with admin settings and inline expanding Legal text.',
                'changelog'   => nl2br(esc_html($release->changelog)),
            ),
        );
    }

    /**
     * Fix directory renaming after update.
     */
    public function after_install($response, $hook_extra, $result)
    {
        if (!isset($hook_extra['plugin']) || $hook_extra['plugin'] !== $this->plugin_slug) {
            return $result;
        }

        global $wp_filesystem;
        
        // Use the actual folder name where the plugin is located
        $plugin_dir = WP_PLUGIN_DIR . '/' . dirname($this->plugin_slug);
        
        // Ensure the directory is correctly named if it changed during extraction
        if ($result['destination'] !== $plugin_dir) {
            $wp_filesystem->move($result['destination'], $plugin_dir);
            $result['destination'] = $plugin_dir;
        }

        delete_transient($this->transient_key);
        return $result;
    }

    /**
     * Manual check handler.
     */
    public function handle_manual_check()
    {
        if (isset($_GET['gnn_terms_check_update']) && '1' === $_GET['gnn_terms_check_update']) {
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'gnn_terms_manual_update')) {
                wp_die(esc_html__('Security check failed.', 'gnn-terms-popup'));
            }

            if (current_user_can('update_plugins')) {
                delete_transient($this->transient_key);
                delete_site_transient('update_plugins');
                
                // Redirect to the core updates page to show the result
                wp_safe_redirect(admin_url('update-core.php?force-check=1'));
                exit;
            }
        }
    }

    /**
     * Clear cache on core updates page.
     */
    public function clear_cache()
    {
        delete_transient($this->transient_key);
    }
}

// Initialize
new GNN_Terms_Popup_Updater();
