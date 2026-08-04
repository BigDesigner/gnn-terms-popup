<?php
/**
 * Plugin Name: GNN Terms Popup
 * Description: One-time Terms acceptance popup with admin settings and inline expanding Legal text (no redirect).
 * Version: 1.3.14
 * Author: BigDesigner
 * Author URI: https://github.com/BigDesigner
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gnn-terms-popup
 */

if (!defined('ABSPATH')) exit;

// Include the GitHub updater
if (file_exists(plugin_dir_path(__FILE__) . 'inc/updater.php')) {
    require_once plugin_dir_path(__FILE__) . 'inc/updater.php';
}

class GNN_Terms_Popup {
  const OPT_KEY = 'gnn_terms_popup_options';
  const COOKIE  = 'gnn_terms_accepted';

  public function __construct() {
    register_activation_hook(__FILE__, [$this, 'activate_defaults']);
    add_action('admin_menu', [$this, 'admin_menu']);
    add_action('admin_init', [$this, 'register_settings']);

    // Admin editor assets fix for Visual <-> Text toggle
    add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_editor_assets']);

    add_action('wp_footer',          [$this, 'render_modal']);

    // Add plugin action links
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'plugin_action_links']);
  }

  /* ---------------- Defaults / Activation ---------------- */

  public function get_defaults() {
    return [
      'enabled'          => 1,   // master on/off switch for the popup
      'title'            => __('Terms & Conditions', 'gnn-terms-popup'),
      'intro_body'       => __("Please Review Our Terms of Service. By clicking 'I Agree' or continuing to use this site, you acknowledge that you have read, understood, and agree to be bound by the Terms and Conditions and our Privacy Policy. This agreement governs your use of [Your Website/Company] services. If you do not accept these terms, please discontinue use of this Site.", 'gnn-terms-popup'),
      'accept_label'     => __('I Agree', 'gnn-terms-popup'),
      'read_label'       => __('Read Terms', 'gnn-terms-popup'),
      'cookie_days'      => 365,
      'skip_admins'      => 1,   // do not show to admins
      'show_everywhere'  => 1,   // show on all public pages
      'include_paths'    => '',  // if show_everywhere = 0, comma-separated paths
      // Legal content source:
      'legal_source'     => 'page', // 'page' or 'custom'
      'legal_page_slug'  => 'legal',
      'full_legal'       => "Full legal text goes here...",
      // Appearance:
      'primary_color'    => '#fdb813',
      'secondary_color'  => '#000000',
      'style_hash'       => time() // initial hash
    ];
  }

  public function activate_defaults() {
    $opts = get_option(self::OPT_KEY);
    if (!$opts || !is_array($opts)) {
      update_option(self::OPT_KEY, $this->get_defaults());
    }
  }

  /* ---------------- Admin UI ---------------- */

  public function admin_menu() {
    add_menu_page(
      'GNN Terms Popup',
      'GNN Terms Popup',
      'manage_options',
      'gnn-terms-popup',
      [$this, 'settings_page'],
      'dashicons-shield',
      '79.105'
    );
  }

  public function plugin_action_links($links) {
    $donate_link = '<a href="https://buymeacoffee.com/bigdesigner" target="_blank" style="font-weight:bold; color:#d63638;">' . esc_html__('Donate', 'gnn-terms-popup') . '</a>';
    $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=gnn-terms-popup')) . '">' . esc_html__('Settings', 'gnn-terms-popup') . '</a>';
    $update_url = wp_nonce_url(admin_url('plugins.php?gnn_terms_check_update=1'), 'gnn_terms_manual_update');
    $update_link = '<a href="' . esc_url($update_url) . '">' . esc_html__('Check Updates', 'gnn-terms-popup') . '</a>';

    $new_links = array(
        'donate'   => $donate_link,
        'settings' => $settings_link,
        'updates'  => $update_link,
    );

    $links = array_merge($new_links, $links);

    return $links;
  }

  public function register_settings() {
    register_setting(self::OPT_KEY, self::OPT_KEY, [$this, 'sanitize']);

    // General tab
    add_settings_section('gnn_general', '', '__return_false', 'gnn-terms-popup');
    add_settings_field('enabled', esc_html__('Popup Enabled', 'gnn-terms-popup'), [$this, 'field_enabled'], 'gnn-terms-popup', 'gnn_general');
    add_settings_field('title', esc_html__('Title', 'gnn-terms-popup'), [$this, 'field_title'], 'gnn-terms-popup', 'gnn_general');
    add_settings_field('intro_body', esc_html__('Intro Text (shown by default)', 'gnn-terms-popup'), [$this, 'field_intro_body'], 'gnn-terms-popup', 'gnn_general');
    add_settings_field('accept_label', esc_html__('Accept Button Label', 'gnn-terms-popup'), [$this, 'field_accept'], 'gnn-terms-popup', 'gnn_general');
    add_settings_field('read_label', esc_html__('Read Terms Button Label', 'gnn-terms-popup'), [$this, 'field_read'], 'gnn-terms-popup', 'gnn_general');
    add_settings_field('cookie_days', esc_html__('Cookie Lifetime (days)', 'gnn-terms-popup'), [$this, 'field_days'], 'gnn-terms-popup', 'gnn_general');
    add_settings_field('skip_admins', esc_html__('Skip for Admins', 'gnn-terms-popup'), [$this, 'field_skip'], 'gnn-terms-popup', 'gnn_general');

    // Legal tab
    add_settings_section('gnn_legal', '', '__return_false', 'gnn-terms-popup');
    add_settings_field('legal_source', esc_html__('Legal Content Source', 'gnn-terms-popup'), [$this, 'field_legal_source'], 'gnn-terms-popup', 'gnn_legal');
    add_settings_field('legal_page_slug', esc_html__('If Source = Page: Page Slug', 'gnn-terms-popup'), [$this, 'field_legal_slug'], 'gnn-terms-popup', 'gnn_legal');
    add_settings_field('full_legal', esc_html__('If Source = Custom: Full Legal Text', 'gnn-terms-popup'), [$this, 'field_full_legal'], 'gnn-terms-popup', 'gnn_legal');

    // Scope tab
    add_settings_section('gnn_scope', '', '__return_false', 'gnn-terms-popup');
    add_settings_field('show_scope', esc_html__('Scope', 'gnn-terms-popup'), [$this, 'field_scope'], 'gnn-terms-popup', 'gnn_scope');

    // Appearance tab
    add_settings_section('gnn_style', '', '__return_false', 'gnn-terms-popup');
    add_settings_field('primary_color', esc_html__('Primary Color (Yellow)', 'gnn-terms-popup'), [$this, 'field_color_primary'], 'gnn-terms-popup', 'gnn_style');
    add_settings_field('secondary_color', esc_html__('Secondary Color (Black)', 'gnn-terms-popup'), [$this, 'field_color_secondary'], 'gnn-terms-popup', 'gnn_style');
  }

  public function sanitize($input) {
    if (!is_array($input)) {
      return $this->get_defaults();
    }
    $d = $this->get_defaults();
    $out = [];

    $out['enabled']      = !empty($input['enabled']) ? 1 : 0;

    $out['title']        = isset($input['title']) ? wp_kses_post($input['title']) : $d['title'];

    $allowed_post = wp_kses_allowed_html('post'); // allow basic formatting
    $out['intro_body']   = isset($input['intro_body']) ? wp_kses($input['intro_body'], $allowed_post) : $d['intro_body'];

    $legal_source = isset($input['legal_source']) && in_array($input['legal_source'], ['page','custom'], true) ? $input['legal_source'] : 'page';
    $out['legal_source'] = $legal_source;

    $slug = isset($input['legal_page_slug']) ? sanitize_title($input['legal_page_slug']) : $d['legal_page_slug'];
    $out['legal_page_slug'] = $slug ? $slug : 'legal';

    $out['full_legal'] = isset($input['full_legal']) ? wp_kses($input['full_legal'], $allowed_post) : $d['full_legal'];

    $out['accept_label'] = isset($input['accept_label']) ? sanitize_text_field($input['accept_label']) : $d['accept_label'];
    $out['read_label']   = isset($input['read_label']) ? sanitize_text_field($input['read_label'])   : $d['read_label'];

    $days = isset($input['cookie_days']) ? intval($input['cookie_days']) : $d['cookie_days'];
    $out['cookie_days']  = ($days > 0 && $days <= 3650) ? $days : $d['cookie_days'];

    $out['skip_admins']  = !empty($input['skip_admins']) ? 1 : 0;

    $show_everywhere = !empty($input['show_everywhere']) ? 1 : 0;
    $out['show_everywhere'] = $show_everywhere;

    $inc = isset($input['include_paths']) ? sanitize_text_field($input['include_paths']) : '';
    if (!$show_everywhere) {
      $paths = array_filter(array_map(function($p){
        $p = trim($p);
        if ($p === '') return '';
        if ($p[0] !== '/') $p = '/'.$p;
        return rtrim($p, '/');
      }, explode(',', $inc)));
      $inc = implode(', ', array_unique($paths));
    } else {
      $inc = '';
    }
    $out['include_paths'] = $inc;

    $out['primary_color']   = isset($input['primary_color']) ? (sanitize_hex_color($input['primary_color']) ?? $d['primary_color']) : $d['primary_color'];
    $out['secondary_color'] = isset($input['secondary_color']) ? (sanitize_hex_color($input['secondary_color']) ?? $d['secondary_color']) : $d['secondary_color'];
    $out['style_hash']      = time(); // update hash on save

    return $out;
  }

  public function settings_page() {
    if (!current_user_can('manage_options')) return;

    if (!function_exists('get_plugin_data')) {
      require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    $plugin_data = get_plugin_data(__FILE__);

    $tabs = [
      'general' => esc_html__('General', 'gnn-terms-popup'),
      'legal'   => esc_html__('Legal Content', 'gnn-terms-popup'),
      'scope'   => esc_html__('Display Scope', 'gnn-terms-popup'),
      'style'   => esc_html__('Appearance', 'gnn-terms-popup'),
    ];
    ?>
    <div class="wrap gnn-admin-wrap">
      <h1><?php esc_html_e('GNN Terms Popup', 'gnn-terms-popup'); ?></h1>
      <p class="gnn-version-note"><?php printf(esc_html__('Version %s', 'gnn-terms-popup'), esc_html($plugin_data['Version'])); ?></p>

      <div class="gnn-tabs-nav" role="tablist">
        <?php foreach ($tabs as $key => $label) : ?>
          <button type="button" class="gnn-tab-btn" data-tab="<?php echo esc_attr($key); ?>" role="tab"><?php echo $label; ?></button>
        <?php endforeach; ?>
      </div>

      <form method="post" action="options.php">
        <?php settings_fields(self::OPT_KEY); ?>

        <div class="gnn-tab-panel" data-panel="general">
          <div class="gnn-card">
            <table class="form-table gnn-form-table" role="presentation"><tbody>
              <?php do_settings_fields('gnn-terms-popup', 'gnn_general'); ?>
            </tbody></table>
          </div>
        </div>

        <div class="gnn-tab-panel" data-panel="legal">
          <div class="gnn-card">
            <p class="gnn-card-hint"><?php esc_html_e('If Legal Content Source = Page, the plugin loads the page with that slug and shows it inline when the user clicks "Read Terms". Choose Custom to manage the legal text directly here.', 'gnn-terms-popup'); ?></p>
            <table class="form-table gnn-form-table" role="presentation"><tbody>
              <?php do_settings_fields('gnn-terms-popup', 'gnn_legal'); ?>
            </tbody></table>
          </div>
        </div>

        <div class="gnn-tab-panel" data-panel="scope">
          <div class="gnn-card">
            <table class="form-table gnn-form-table" role="presentation"><tbody>
              <?php do_settings_fields('gnn-terms-popup', 'gnn_scope'); ?>
            </tbody></table>
          </div>
        </div>

        <div class="gnn-tab-panel" data-panel="style">
          <div class="gnn-card">
            <table class="form-table gnn-form-table" role="presentation"><tbody>
              <?php do_settings_fields('gnn-terms-popup', 'gnn_style'); ?>
            </tbody></table>
          </div>
        </div>

        <?php submit_button(esc_html__('Save Changes', 'gnn-terms-popup')); ?>
      </form>
    </div>

    <style>
      .gnn-admin-wrap{max-width:900px}
      .gnn-admin-wrap .gnn-version-note{color:#787c82;margin-top:-4px}
      .gnn-admin-wrap .gnn-tabs-nav{display:flex;flex-wrap:wrap;gap:6px;margin:16px 0 0;border-bottom:1px solid #dcdcde;padding-bottom:0}
      .gnn-admin-wrap .gnn-tab-btn{
        background:#fff;border:1px solid #dcdcde;border-bottom:none;border-radius:8px 8px 0 0;
        padding:10px 16px;font-weight:600;cursor:pointer;color:#50575e;
      }
      .gnn-admin-wrap .gnn-tab-btn:hover{color:#1d2327}
      .gnn-admin-wrap .gnn-tab-btn.is-active{background:#2271b1;color:#fff;border-color:#2271b1}
      .gnn-admin-wrap .gnn-tab-panel{display:none}
      .gnn-admin-wrap .gnn-tab-panel.is-active{display:block}
      .gnn-admin-wrap .gnn-card{
        background:#fff;border:1px solid #dcdcde;border-top:none;border-radius:0 0 8px 8px;
        padding:8px 24px 24px;box-shadow:0 1px 2px rgba(0,0,0,.04);margin-bottom:20px;
      }
      .gnn-admin-wrap .gnn-card-hint{color:#50575e}

      /* Field rows: label stacked above the control, full-width fields */
      .gnn-admin-wrap .gnn-form-table{border-collapse:separate;width:100%}
      .gnn-admin-wrap .gnn-form-table tr{display:block}
      .gnn-admin-wrap .gnn-form-table th,
      .gnn-admin-wrap .gnn-form-table td{
        display:block;width:100%;padding:0;border:none;
      }
      .gnn-admin-wrap .gnn-form-table th{
        padding-top:20px;padding-bottom:6px;font-weight:600;color:#1d2327;
      }
      .gnn-admin-wrap .gnn-form-table td{padding-bottom:4px}
      .gnn-admin-wrap .gnn-form-table td .description{margin-top:6px;color:#787c82}
      .gnn-admin-wrap .gnn-form-table input[type="text"],
      .gnn-admin-wrap .gnn-form-table input[type="number"],
      .gnn-admin-wrap .gnn-form-table select{
        width:100%;max-width:520px;
      }
      .gnn-admin-wrap .gnn-form-table .wp-editor-wrap{max-width:820px}

      /* Toggle switch */
      .gnn-switch{position:relative;display:inline-flex;align-items:center;gap:10px;cursor:pointer;user-select:none}
      .gnn-switch input{position:absolute;opacity:0;width:0;height:0}
      .gnn-switch .gnn-switch-track{
        width:42px;height:24px;border-radius:999px;background:#c3c4c7;
        transition:background .15s ease;flex-shrink:0;position:relative;
      }
      .gnn-switch .gnn-switch-track::after{
        content:'';position:absolute;top:2px;left:2px;width:20px;height:20px;
        border-radius:50%;background:#fff;transition:transform .15s ease;
        box-shadow:0 1px 2px rgba(0,0,0,.3);
      }
      .gnn-switch input:checked + .gnn-switch-track{background:#2271b1}
      .gnn-switch input:checked + .gnn-switch-track::after{transform:translateX(18px)}
      .gnn-switch input:focus-visible + .gnn-switch-track{outline:2px solid #2271b1;outline-offset:2px}
      .gnn-switch .gnn-switch-label{font-weight:600}
    </style>

    <script>
    (function(){
      var STORAGE_KEY = 'gnn_terms_popup_active_tab';
      var btns = document.querySelectorAll('.gnn-tab-btn');
      var panels = document.querySelectorAll('.gnn-tab-panel');

      function activate(tab){
        btns.forEach(function(b){ b.classList.toggle('is-active', b.dataset.tab === tab); });
        panels.forEach(function(p){ p.classList.toggle('is-active', p.dataset.panel === tab); });
        try { localStorage.setItem(STORAGE_KEY, tab); } catch(e){}
      }

      btns.forEach(function(b){
        b.addEventListener('click', function(){ activate(b.dataset.tab); });
      });

      var saved = null;
      try { saved = localStorage.getItem(STORAGE_KEY); } catch(e){}
      var initial = (saved && document.querySelector('.gnn-tab-panel[data-panel="'+saved+'"]')) ? saved : 'general';
      activate(initial);
    })();
    </script>
    <?php
  }

  /* ---- Field renderers ---- */

  public function field_title() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="text" name="%s[title]" value="%s" class="regular-text" style="width:520px">',
      esc_attr(self::OPT_KEY),
      esc_attr($o['title'] ?? '')
    );
  }

  public function field_intro_body() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    $content = $o['intro_body'] ?? '';
    wp_editor($content, 'gnn_terms_popup_intro', [
      'textarea_name' => self::OPT_KEY.'[intro_body]',
      'textarea_rows' => 8,
      'media_buttons' => false,
      'teeny'         => true,
      'quicktags'     => true, // allow Visual/Text toggle
    ]);
    echo '<p class="description">' . esc_html__('Shown by default in the popup above the buttons.', 'gnn-terms-popup') . '</p>';
  }

  public function field_legal_source() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    $val = $o['legal_source'] ?? 'page';
    ?>
    <select name="<?php echo esc_attr(self::OPT_KEY); ?>[legal_source]">
      <option value="page" <?php selected($val, 'page'); ?>>Page (pull content by slug)</option>
      <option value="custom" <?php selected($val, 'custom'); ?>>Custom (use editor below)</option>
    </select>
    <?php
  }

  public function field_legal_slug() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="text" name="%s[legal_page_slug]" value="%s" class="regular-text" placeholder="legal" />',
      esc_attr(self::OPT_KEY),
      esc_attr($o['legal_page_slug'] ?? 'legal')
    );
    echo '<p class="description">' . sprintf(esc_html__('Enter the slug of your Legal page (e.g., %s).', 'gnn-terms-popup'), '<code>legal</code>') . '</p>';
  }

  public function field_full_legal() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    $content = $o['full_legal'] ?? '';
    wp_editor($content, 'gnn_terms_popup_full', [
      'textarea_name' => self::OPT_KEY.'[full_legal]',
      'textarea_rows' => 14,
      'media_buttons' => false,
      'teeny'         => false,
      'quicktags'     => true,
    ]);
    echo '<p class="description">' . sprintf(esc_html__('This is used if %s is set to %s.', 'gnn-terms-popup'), '<strong>' . esc_html__('Legal Content Source', 'gnn-terms-popup') . '</strong>', '<code>Custom</code>') . '</p>';
  }

  public function field_accept() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="text" name="%s[accept_label]" value="%s" class="regular-text">',
      esc_attr(self::OPT_KEY),
      esc_attr($o['accept_label'] ?? 'I Agree')
    );
  }

  public function field_read() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="text" name="%s[read_label]" value="%s" class="regular-text">',
      esc_attr(self::OPT_KEY),
      esc_attr($o['read_label'] ?? 'Read Terms')
    );
  }

  public function field_days() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="number" min="1" max="3650" name="%s[cookie_days]" value="%d" class="small-text"> ' . esc_html__('days', 'gnn-terms-popup'),
      esc_attr(self::OPT_KEY),
      intval($o['cookie_days'] ?? 365)
    );
  }

  public function field_skip() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    $checked = !empty($o['skip_admins']) ? 'checked' : '';
    printf(
      '<label class="gnn-switch"><input type="checkbox" name="%s[skip_admins]" value="1" %s><span class="gnn-switch-track"></span><span class="gnn-switch-label">%s</span></label>',
      esc_attr(self::OPT_KEY),
      esc_attr($checked),
      esc_html__('Do not show to administrators', 'gnn-terms-popup')
    );
  }

  public function field_enabled() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    $checked = !empty($o['enabled']) ? 'checked' : '';
    printf(
      '<label class="gnn-switch"><input type="checkbox" name="%s[enabled]" value="1" %s><span class="gnn-switch-track"></span><span class="gnn-switch-label">%s</span></label>',
      esc_attr(self::OPT_KEY),
      esc_attr($checked),
      esc_html__('Show the popup on the site', 'gnn-terms-popup')
    );
    echo '<p class="description">' . esc_html__('Turn off to disable the popup everywhere without deactivating the plugin.', 'gnn-terms-popup') . '</p>';
  }

  public function field_scope() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    ?>
    <label>
      <input type="radio" name="<?php echo esc_attr(self::OPT_KEY); ?>[show_everywhere]" value="1" <?php checked(1, intval($o['show_everywhere'] ?? 1)); ?>>
      <?php esc_html_e('Show on all public pages', 'gnn-terms-popup'); ?>
    </label>
    <br>
    <label>
      <input type="radio" name="<?php echo esc_attr(self::OPT_KEY); ?>[show_everywhere]" value="0" <?php checked(0, intval($o['show_everywhere'] ?? 1)); ?>>
      <?php esc_html_e('Only on these paths (comma-separated)', 'gnn-terms-popup'); ?>
    </label>
    <br>
    <input type="text" name="<?php echo esc_attr(self::OPT_KEY); ?>[include_paths]" value="<?php echo esc_attr($o['include_paths'] ?? ''); ?>" class="regular-text" style="width:520px" placeholder="/ , /about , /services">
    <p class="description"><?php printf(esc_html__('Use site-relative paths. Example: %s, %s, %s.', 'gnn-terms-popup'), '<code>/</code>', '<code>/prices</code>', '<code>/contact</code>'); ?></p>
    <?php
  }

  public function field_color_primary() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="text" name="%s[primary_color]" value="%s" class="gnn-color-picker">',
      esc_attr(self::OPT_KEY),
      esc_attr($o['primary_color'] ?? '#fdb813')
    );
  }

  public function field_color_secondary() {
    $o = get_option(self::OPT_KEY, $this->get_defaults());
    printf(
      '<input type="text" name="%s[secondary_color]" value="%s" class="gnn-color-picker">',
      esc_attr(self::OPT_KEY),
      esc_attr($o['secondary_color'] ?? '#000000')
    );
  }

  /* ---------------- Admin Editor Assets (Fix) ---------------- */

  public function enqueue_admin_editor_assets($hook) {
    // Only on our settings page
    if ($hook !== 'toplevel_page_gnn-terms-popup') return;

    if (function_exists('wp_enqueue_editor')) {
      wp_enqueue_editor(); // loads TinyMCE/QuickTags and dependencies
    }

    // Ensure shortcode script exists (prevents u.shortcode undefined)
    wp_enqueue_script('shortcode');

    // Classic editor assets for better compatibility
    wp_enqueue_script('editor');
    wp_enqueue_style('editor-buttons');

    // Color Picker
    wp_enqueue_style('wp-color-picker');
    wp_register_script('gnn-admin-js', '', ['wp-color-picker'], false, true);
    wp_enqueue_script('gnn-admin-js');
    wp_add_inline_script('gnn-admin-js', "jQuery(document).ready(function($){ $('.gnn-color-picker').wpColorPicker(); });");
  }

  private function should_skip($o) {
    // skip if cookie set
    if (isset($_COOKIE[self::COOKIE]) && sanitize_text_field(wp_unslash($_COOKIE[self::COOKIE])) === 'yes') return true;

    // skip for admins if opted
    if (!empty($o['skip_admins']) && current_user_can('manage_options')) return true;

    // otherwise enqueue and decide on client side for scope
    return false;
  }

  /* ---------------- Render ---------------- */

  private function get_legal_html($o) {
    // Prefer 'page' source if selected and page exists, otherwise fall back to 'custom'.
    $allowed = wp_kses_allowed_html('post');

    $source = $o['legal_source'] ?? 'page';
    if ($source === 'page') {
      $slug = $o['legal_page_slug'] ?? 'legal';
      $page = $slug ? get_page_by_path($slug) : null;

      if ($page instanceof WP_Post) {
        $html = wp_kses_post(apply_filters('the_content', $page->post_content));
        if (trim(wp_strip_all_tags($html)) !== '') {
          return $html; // page content OK
        }
        // page boşsa custom'a düş
      }

      // Fallback: custom
      $custom = isset($o['full_legal']) ? wp_kses($o['full_legal'], $allowed) : '';
      if (trim(wp_strip_all_tags($custom)) !== '') {
        return wpautop($custom);
      }

      // Son çare: bilgilendir
      return '<p class="gnn-small">Legal page not found for slug: <code>' . esc_html($slug) . '</code> and no custom legal text provided.</p>';
    }

    // Source = custom
    $custom = isset($o['full_legal']) ? wp_kses($o['full_legal'], $allowed) : '';
    return wpautop($custom);
  }

  public function render_modal() {
    if (is_admin()) return;

    $o = get_option(self::OPT_KEY, $this->get_defaults());
    if (empty($o['enabled'])) return;
    if ($this->should_skip($o)) return;

    $title        = $o['title'] ?? '';
    $intro_body   = wpautop($o['intro_body'] ?? '');
    $accept_label = esc_html($o['accept_label'] ?? 'I Agree');
    $read_label   = esc_html($o['read_label'] ?? 'Read Terms');

    $legal_html   = $this->get_legal_html($o);

    // CSS
    $primary   = $o['primary_color']   ?? '#fdb813';
    $secondary = $o['secondary_color'] ?? '#000000';

    // JS variables
    $cookie_name   = self::COOKIE;
    $days          = intval($o['cookie_days']);
    $skip_admins   = json_encode(!empty($o['skip_admins']) && current_user_can('manage_options'));
    $showEverywhere= json_encode(!empty($o['show_everywhere']));
    $includePaths  = json_encode(array_map('trim', array_filter(explode(',', $o['include_paths'] ?? ''))));
    ?>
    <style>
      :root {
        --gnn-primary: <?php echo esc_html($primary); ?>;
        --gnn-secondary: <?php echo esc_html($secondary); ?>;
      }
      .gnn-terms-overlay{
        position:fixed;inset:0;background:rgba(0,0,0,.5);
        display:none;align-items:center;justify-content:center;
        padding-top:0;
        z-index:99999
      }
      .gnn-terms-modal{
        max-width:780px;width:92%;
        background:var(--gnn-primary);color:var(--gnn-secondary);
        border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.2);
        padding:24px;max-height:90vh;overflow:auto;
        -webkit-overflow-scrolling:touch;
      }
      .gnn-terms-modal h2{margin:0 0 8px 0;color:var(--gnn-secondary)}
      .gnn-terms-modal p{margin:.4rem 0;color:var(--gnn-secondary)}
      .gnn-terms-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
      .gnn-btn{
        display:inline-block;padding:10px 14px;border-radius:10px;
        border:1px solid var(--gnn-secondary);background:var(--gnn-secondary);color:var(--gnn-primary);cursor:pointer
      }
      .gnn-btn.secondary{background:var(--gnn-primary);color:var(--gnn-secondary);border:1px solid var(--gnn-secondary)}
      .gnn-small{font-size:13px;color:var(--gnn-secondary)}
      body.gnn-no-scroll{overflow:hidden}
      .gnn-terms-full{
        display:none;max-height:0;overflow:hidden;transition:max-height .5s ease
      }
      @media (prefers-reduced-motion: reduce){
        .gnn-terms-full{transition:none}
      }
      .gnn-terms-full.open{
        display:block !important;max-height:80vh;overflow-y:auto;
        margin-top:12px;padding-top:12px;border-top:1px solid var(--gnn-secondary);
        -webkit-overflow-scrolling:touch;
      }
    </style>

    <div class="gnn-terms-overlay" role="dialog" aria-modal="true" aria-labelledby="gnn-terms-title">
      <div class="gnn-terms-modal">
        <h2 id="gnn-terms-title"><?php echo esc_html($title); ?></h2>
        <div class="gnn-body"><?php echo wp_kses_post($intro_body); ?></div>

        <div class="gnn-terms-actions">
          <button id="gnn-accept" class="gnn-btn" type="button" aria-label="<?php echo esc_attr($accept_label); ?>"><?php echo esc_html($accept_label); ?></button>
          <button id="gnn-read" class="gnn-btn secondary" type="button" aria-expanded="false" aria-controls="gnn-terms-full"><?php echo esc_html($read_label); ?></button>
        </div>

        <div id="gnn-terms-full" class="gnn-terms-full" aria-hidden="true" tabindex="-1">
          <?php echo wp_kses_post($legal_html); ?>
        </div>

        <p class="gnn-small"><?php esc_html_e('You must accept to continue using this site.', 'gnn-terms-popup'); ?></p>
        <noscript><p><?php esc_html_e('Please enable JavaScript to proceed.', 'gnn-terms-popup'); ?></p></noscript>
      </div>
    </div>

    <script>
    (function(){
      if (<?php echo $skip_admins; ?>) return;

      function getCookie(name){
        return document.cookie.split('; ').find(r=>r.startsWith(name+'='))?.split('=')[1];
      }
      function setCookie(name, value, days){
        var d=new Date(); d.setTime(d.getTime()+days*24*60*60*1000);
        var c = name+'='+value+';expires='+d.toUTCString()+';path=/;SameSite=Lax';
        if (location.protocol==='https:') c += ';Secure';
        document.cookie = c;
      }
      function inList(list, p){
        for (var i=0;i<list.length;i++){
          var item=list[i];
          if (!item) continue;
          if (item[0] !== '/') item = '/'+item;
          item = item.replace(/\/+$/,'');
          if (p === item || p.indexOf(item) === 0) return true;
        }
        return false;
      }
      function pathNow(){
        var p = window.location.pathname.replace(/\/+$/,'');
        if (p==='') p='/';
        return p;
      }
      function showModal(){
        var ov=document.querySelector('.gnn-terms-overlay');
        if(!ov) return;
        ov.style.display='flex';
        document.body.classList.add('gnn-no-scroll');
        var first = document.getElementById('gnn-accept');
        if (first) first.focus();
        // focus trap
        var focusables=ov.querySelectorAll('a,button');
        var i=0;
        ov.addEventListener('keydown',function(e){
          if(e.key==='Tab'){
            e.preventDefault();
            i=(i+ (e.shiftKey?-1:1) + focusables.length)%focusables.length;
            focusables[i].focus();
          }
        });
      }
      function hideModal(){
        var ov=document.querySelector('.gnn-terms-overlay');
        if(!ov) return;
        ov.style.display='none';
        document.body.classList.remove('gnn-no-scroll');
      }

      var cookie = getCookie('<?php echo esc_js($cookie_name); ?>');
      var showEverywhere = <?php echo $showEverywhere; ?>;
      var include = <?php echo $includePaths; ?>;
      if (!cookie) {
        if (showEverywhere || inList(include, pathNow())) {
          showModal();
        }
      }

      document.addEventListener('click', function(e){
        if(e.target && e.target.id==='gnn-accept'){
          setCookie('<?php echo esc_js($cookie_name); ?>', 'yes', <?php echo $days; ?>);
          hideModal();
        }
        if(e.target && e.target.id==='gnn-read'){
          var btn  = e.target;
          var modal = document.querySelector('.gnn-terms-modal');
          var full = document.getElementById('gnn-terms-full');
          if (full) {
            if (!full.classList.contains('open')) {
              full.style.display='block';
              var _ = full.scrollHeight; // reflow
            }
            full.classList.toggle('open');
            var isOpen = full.classList.contains('open');
            full.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (isOpen) {
              full.scrollTop = 0;
              if (modal) modal.scrollTop = 0;
              try { full.focus({preventScroll:true}); } catch(e){}
            }
          }
        }
      });
    })();
    </script>
    <?php
  }
}

new GNN_Terms_Popup();