<?php
/**
 * WooCommerce Unlimited Upsell.
 *
 * @package   WooCommerce_Unlimited_Upsell_Admin_Pages
 * @author    KungWoo
 * @license   GPL-2.0+
 * @link      http://kungwoo.com
 * @copyright 2016 KungWoo
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");
/*-----------------------------------------*/

class WooCommerce_Unlimited_Upsell_Admin_Pages
{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = array();

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
        return;
        } */

        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin               = WooCommerce_Unlimited_Upsell::get_instance();
        $this->plugin_slug    = $plugin->get_plugin_slug();
        $this->plugin_version = $plugin->get_plugin_version();

        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_css_js'));

        // Add the plugin admin pages and menu items.
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));

    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance()
    {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
        return;
        } */

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific CSS and JS.
     *
     * @since     1.0.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_css_js()
    {

        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }

        $screen = get_current_screen();

        // Main Plugin Page
        if ($this->plugin_screen_hook_suffix['dashboard'] == $screen->id) {
            /* Admin Styles */
            wp_enqueue_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/admin.css', __FILE__), array(), $this->plugin_version);

            wp_register_script($this->plugin_slug . '-chart-js', plugins_url('assets/vendor/chart.bundle.min.js', __FILE__), array('jquery'), $this->plugin_version);
            // Main Admin JS Script
            wp_register_script($this->plugin_slug . '-dashboard-js', plugins_url('assets/js/dashboard.js', __FILE__), array('jquery', $this->plugin_slug . '-chart-js'), $this->plugin_version);

            wp_enqueue_script($this->plugin_slug . '-chart-js');
            wp_enqueue_script($this->plugin_slug . '-dashboard-js');

            $js_options = array( 
              'ajax' => 
                array(
                  'url' => admin_url( 'admin-ajax.php' ),
                  'nonce' => wp_create_nonce( 'wooup_admin_ajax_request' ),
                ),
              'e' =>
                array(
                  'loading' => esc_attr__('Loading…', 'woocommerce-unlimited-upsell'),
                  'no_data' => esc_attr__('Sorry, no data to show', 'woocommerce-unlimited-upsell'),
                )
            );

            wp_localize_script( $this->plugin_slug . '-dashboard-js', 'WooUpSettings', $js_options );

        }

    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *        For reference: http://codex.wordpress.org/Roles_and_Capabilities
         *
         */

        // Base 64 encoded SVG image.
        $menu_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIgICB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgICB4bWxuczpzb2RpcG9kaT0iaHR0cDovL3NvZGlwb2RpLnNvdXJjZWZvcmdlLm5ldC9EVEQvc29kaXBvZGktMC5kdGQiICAgeG1sbnM6aW5rc2NhcGU9Imh0dHA6Ly93d3cuaW5rc2NhcGUub3JnL25hbWVzcGFjZXMvaW5rc2NhcGUiICAgdmVyc2lvbj0iMS4xIiAgIHg9IjBweCIgICB5PSIwcHgiICAgdmlld0JveD0iMCAwIDEwMCAxMDAiICAgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwIDEwMCIgICB4bWw6c3BhY2U9InByZXNlcnZlIiAgIGlkPSJzdmc0MTM2IiAgIGlua3NjYXBlOnZlcnNpb249IjAuOTEgcjEzNzI1IiAgIHNvZGlwb2RpOmRvY25hbWU9InUtaWNvbi5zdmciPjxtZXRhZGF0YSAgICAgaWQ9Im1ldGFkYXRhNDE0NiI+PHJkZjpSREY+PGNjOldvcmsgICAgICAgICByZGY6YWJvdXQ9IiI+PGRjOmZvcm1hdD5pbWFnZS9zdmcreG1sPC9kYzpmb3JtYXQ+PGRjOnR5cGUgICAgICAgICAgIHJkZjpyZXNvdXJjZT0iaHR0cDovL3B1cmwub3JnL2RjL2RjbWl0eXBlL1N0aWxsSW1hZ2UiIC8+PC9jYzpXb3JrPjwvcmRmOlJERj48L21ldGFkYXRhPjxkZWZzICAgICBpZD0iZGVmczQxNDQiIC8+PHNvZGlwb2RpOm5hbWVkdmlldyAgICAgcGFnZWNvbG9yPSIjZmZmZmZmIiAgICAgYm9yZGVyY29sb3I9IiM2NjY2NjYiICAgICBib3JkZXJvcGFjaXR5PSIxIiAgICAgb2JqZWN0dG9sZXJhbmNlPSIxMCIgICAgIGdyaWR0b2xlcmFuY2U9IjEwIiAgICAgZ3VpZGV0b2xlcmFuY2U9IjEwIiAgICAgaW5rc2NhcGU6cGFnZW9wYWNpdHk9IjAiICAgICBpbmtzY2FwZTpwYWdlc2hhZG93PSIyIiAgICAgaW5rc2NhcGU6d2luZG93LXdpZHRoPSIxMzY2IiAgICAgaW5rc2NhcGU6d2luZG93LWhlaWdodD0iNzE2IiAgICAgaWQ9Im5hbWVkdmlldzQxNDIiICAgICBzaG93Z3JpZD0iZmFsc2UiICAgICBpbmtzY2FwZTp6b29tPSIzLjg2Nzg3NDEiICAgICBpbmtzY2FwZTpjeD0iMjIuMTQ4NDMyIiAgICAgaW5rc2NhcGU6Y3k9IjczLjc1NzkwNSIgICAgIGlua3NjYXBlOndpbmRvdy14PSItOCIgICAgIGlua3NjYXBlOndpbmRvdy15PSItOCIgICAgIGlua3NjYXBlOndpbmRvdy1tYXhpbWl6ZWQ9IjEiICAgICBpbmtzY2FwZTpjdXJyZW50LWxheWVyPSJnNDEzOCIgLz48ZyAgICAgaWQ9Imc0MTM4IiAgICAgdHJhbnNmb3JtPSJtYXRyaXgoMSwwLDAsLTEsMy44MTczNzA0ZS02LDEwMCkiPjxwYXRoICAgc3R5bGU9ImZpbGw6IzAwMDAwMDtmaWxsLXJ1bGU6ZXZlbm9kZDtzdHJva2U6IzAwMDAwMDtzdHJva2Utd2lkdGg6MXB4O3N0cm9rZS1saW5lY2FwOmJ1dHQ7c3Ryb2tlLWxpbmVqb2luOm1pdGVyO3N0cm9rZS1vcGFjaXR5OjEiICAgICAgIGQ9Ik0gMjguMjI4LDEwMCA1MC4yODcsNzAuMjI0IGMgMS40MjQsLTEuOTUyIDEuNDEsLTQuMjMyIC0wLjE0NywtNS43OSAtMS41NTcsLTEuNTU4IC0zLjg4OSwtMS4xMTggLTYuMjkxLDAuMTQ0IEwgMzksNjYuOTUgMzksMzguNjEyIGMgMCwtOS42MzEgNy44NzEsLTE3LjQ2NSAxNy41LC0xNy40NjUgOS42MjksMCAxNy41LDcuODM0IDE3LjUsMTcuNDY1IEwgNzQsOTcgOTUsOTcgOTUsMzguNjEyIEMgOTUsMTcuMzIxIDc3Ljc5MSwwIDU2LjUsMCAzNS4yMSwwIDE4LDE3LjMyMSAxOCwzOC42MTIgbCAwLDI4LjMzNyAtNS4xMjEsLTIuMzcyIEMgMTAuNDc3LDYzLjMxNSA3Ljk2MSw2Mi44NzYgNi40MDQsNjQuNDMzIDQuODQ1LDY1Ljk5MSA0Ljc4OSw2OC4yNzEgNi4yMTIsNzAuMjIzIEwgMjguMjI4LDEwMCBaIiAgICAgICBpZD0icGF0aDQxNDAiICAgICAgIGlua3NjYXBlOmNvbm5lY3Rvci1jdXJ2YXR1cmU9IjAiIC8+PC9nPjwvc3ZnPg==';

        $this->plugin_screen_hook_suffix['dashboard'] = add_menu_page(
            __('Upsell Lite', 'woocommerce-unlimited-upsell'),
            __('Upsell Lite', 'woocommerce-unlimited-upsell'),
            'manage_options',
            $this->plugin_slug . '-dashboard',
            null,
            $menu_icon,
            '55.89'
        );

        $this->plugin_screen_hook_suffix['dashboard'] = add_submenu_page(
            $this->plugin_slug . '-dashboard',
            __('Dashboard', 'woocommerce-unlimited-upsell'),
            __('Dashboard', 'woocommerce-unlimited-upsell'),
            'manage_options',
            $this->plugin_slug . '-dashboard',
            array($this, 'display_plugin_page_dashboard')
        );

    }

    /**
     * Render the main page for this plugin.
     *
     * @since    1.0.0
     */
    public function display_plugin_page_dashboard()
    {
        include_once 'views/dashboard.php';
    }

}