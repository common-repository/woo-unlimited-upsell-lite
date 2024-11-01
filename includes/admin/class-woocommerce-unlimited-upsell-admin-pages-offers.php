<?php
/**
 * WooCommerce Unlimited Upsell.
 *
 * @package   WooCommerce_Unlimited_Upsell_Admin_Pages_Offers
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

class WooCommerce_Unlimited_Upsell_Admin_Pages_Offers
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

        // Handle admin notices
        add_action( 'admin_notices', array($this, 'display_admin_notices') );

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

        /* Offers List */
        if ($this->plugin_screen_hook_suffix['offers_view'] == $screen->id) {
            /* Offers Styles */
            wp_enqueue_style($this->plugin_slug . '-admin-css', plugins_url('assets/css/admin.css', __FILE__), array(), $this->plugin_version);
        }

        /* Single Offer */
        if ($this->plugin_screen_hook_suffix['offer_edit'] == $screen->id) {

            /* WordPress scripts */
            wp_enqueue_script('jquery-ui-core', false, array('jquery'));
            wp_enqueue_script('jquery-ui-widget', false, array('jquery'));
            wp_enqueue_script('jquery-ui-mouse', false, array('jquery'));
            wp_enqueue_script('jquery-ui-draggable', false, array('jquery'));
            wp_enqueue_script('jquery-ui-droppable', false, array('jquery'));
            wp_enqueue_script('jquery-ui-sortable', false, array('jquery'));
            wp_enqueue_script('jquery-ui-datepicker', false, array('jquery'));


            /* Styles */
            // jQuery UI custom theme
            wp_enqueue_style($this->plugin_slug . '-jquery-ui-custom', plugins_url('assets/vendor/jquery-ui/jquery-ui.min.css', __FILE__), array(), $this->plugin_version);
            // Select2
            wp_enqueue_style($this->plugin_slug . '-select2', plugins_url('assets/vendor/select2/select2.css', __FILE__), array(), $this->plugin_version);
            // Offers Styles
            wp_enqueue_style($this->plugin_slug . '-admin-css', plugins_url('assets/css/admin.css', __FILE__), array($this->plugin_slug . '-jquery-ui-custom', $this->plugin_slug . '-select2'), $this->plugin_version);

            /* Scripts */
            wp_register_script($this->plugin_slug . '-tinymce', plugins_url('assets/vendor/tinymce/tinymce.min.js', __FILE__), array('jquery'), $this->plugin_version);
            // Select2
            wp_register_script($this->plugin_slug . '-select2', plugins_url('assets/vendor/select2/select2.min.js', __FILE__), array('jquery'), $this->plugin_version);
            // Offers JS 
            wp_register_script($this->plugin_slug . '-offers-js', plugins_url('assets/js/offers.js', __FILE__), array('jquery', 'jquery-ui-datepicker', $this->plugin_slug . '-tinymce', $this->plugin_slug . '-select2'), $this->plugin_version);

            wp_enqueue_script($this->plugin_slug . '-tinymce');
            wp_enqueue_script($this->plugin_slug . '-select2');
            wp_enqueue_script($this->plugin_slug . '-offers-js');

            $js_options = array( 
              'ajax' => 
                array(
                  'url' => admin_url( 'admin-ajax.php' ),
                  'nonce' => wp_create_nonce( 'wooup_admin_ajax_request' ),
                ),
              'e' =>
                array(
                  'search_for_product' => esc_attr__('Search for a product…', 'woocommerce-unlimited-upsell'),
                  'search_for_product_advanced' => esc_attr__('Add product…', 'woocommerce-unlimited-upsell'),
                )
            );

            wp_localize_script( $this->plugin_slug . '-offers-js', 'WooUpSettings', $js_options );
            
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

        /* ----- Plugin Module: CRUD ----- */
        // Example of custom pages (Entries View and Edit)

        $this->plugin_screen_hook_suffix['offers_view'] = add_submenu_page(
            $this->plugin_slug . '-dashboard',
            esc_attr__('Manage Upsell Offers', 'woocommerce-unlimited-upsell'),
            esc_attr__('Offers', 'woocommerce-unlimited-upsell'),
            'manage_options',
            $this->plugin_slug . '-offers-view',
            array($this, 'display_plugin_page_offers_view')
        );

        $this->plugin_screen_hook_suffix['offer_edit'] = add_submenu_page(
            $this->plugin_slug . '-dashboard',
            esc_attr__('Add Offer', 'woocommerce-unlimited-upsell'),
            esc_attr__('Add Offer', 'woocommerce-unlimited-upsell'),
            'manage_options',
            $this->plugin_slug . '-offer-edit',
            array($this, 'display_plugin_page_offer_edit')
        );
        /* ----- End Module: CRUD ----- */

    }

    /* ----- Plugin Module: CRUD ----- */
    /**
     * Render "Manage Entries" page
     *
     * @since    1.0.0
     */

    public function display_plugin_page_offers_view()
    {
        if (!current_user_can('manage_options')){ die('Error'); }

        if (isset($_GET['action']) && ($_GET['action'] == 'edit')) {
            $this->display_plugin_page_entry_edit();
        } else {
            $wooup_list_offers = new WooCommerce_Unlimited_Upsell_Admin_List_Offers();
            $wooup_list_offers->prepare_items();

            include_once 'views/offers-view.php';
        }
    }

    /**
     * Render "Add New / Edit" page
     *
     * @since    1.0.0
     */

    public function display_plugin_page_offer_edit()
    {
        if (!current_user_can('manage_options')) { 
            wp_die('Error');
        }

        global $wpdb;
        $id = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
        $message = array();
        
        $unl_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();

        if (isset($_POST['action']) && $_POST['action'] == 'update') {
          //  check_ajax_referer('nonce_id', 'nonce');
          $type = 'checkout'; // Hardcoded, new types will be added later
          $name = isset($_POST['name']) ? $unl_upsell_obj->sanitize($_POST['name']) : '';
          $priority = isset($_POST['priority']) ? (int) $_POST['priority'] : 0;
          $products = isset($_POST['products']) ? $unl_upsell_obj->sanitize($_POST['products']) : '';
          $is_active = isset($_POST['is_active']) ? 1 : 0;
          $conditions_arr = isset($_POST['conditions']) ? $unl_upsell_obj->sanitize($_POST['conditions']) : '';
          $conditions = empty($conditions_arr) ? '' : serialize($conditions_arr);
          $options_arr = isset($_POST['options']) ? $unl_upsell_obj->sanitize($_POST['options']) : '';
          $options = empty($options_arr) ? '' : serialize($options_arr);
          
          if ($id > 0) {
            $wpdb->update(
                WooCommerce_Unlimited_Upsell_DB::get_table_name('offers'),
                array(
                    'name' => $name,
                    'is_active' => $is_active,
                    'priority' => $priority,
                    'products' => $products,
                    'conditions' => $conditions,
                    'options' => $options,
                    'type' => $type
                ),
                array(
                    'id' => $id,
                ),
                array(
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                )
            );
            // Redirect to offer-edit page with offer id
            $url = admin_url('admin.php?page=' . $this->plugin_slug . '-offer-edit' . '&action=edit' 
                    . '&id=' . $id . '&msg=updated');
            $un_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();
            $un_upsell_obj->redirect($url);
          } else{
            // Add new offer
            $wpdb->insert(
                WooCommerce_Unlimited_Upsell_DB::get_table_name('offers'),
                array(
                    'name' => $name,
                    'is_active' => $is_active,
                    'priority' => $priority,
                    'products' => $products,
                    'conditions' => $conditions,
                    'options' => $options,
                    'type' => $type
                ),
                array(
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                )
            );
            // Redirect to offer-edit page with offer id
            $id = $wpdb->insert_id;
            $url = admin_url('admin.php?page=' 
                    . $this->plugin_slug . '-offer-edit' . '&action=edit' . '&id=' . $id . '&msg=created');
            $un_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();
            $un_upsell_obj->redirect($url);
          }
        } elseif ($id > 0) {
            $offer = $wpdb->get_row('SELECT * FROM ' 
                    . WooCommerce_Unlimited_Upsell_DB::get_table_name('offers') 
                    . ' WHERE id = ' . $id);

            $id           = $offer->id;
            $name         = $offer->name;
            $is_active    = $offer->is_active;
            $priority     = $offer->priority;
            $conditions   = unserialize($offer->conditions);
            $options      = unserialize($offer->options);
            $products     = $offer->products;
            
            $page_title = esc_attr__('Edit Offer', 'woocommerce-unlimited-upsell');
        } else {
            $page_title = esc_attr__('Add New Offer', 'woocommerce-unlimited-upsell');
        }

        include_once 'views/offer-edit.php';
    }
    /* ----- End Module: CRUD ----- */

    public function display_admin_notices()
    {
      if (!isset($this->plugin_screen_hook_suffix)) {
          return;
      }

      $screen = get_current_screen();
      if ( ($this->plugin_screen_hook_suffix['offer_edit'] == $screen->id) && (isset($_GET['msg']))) {
        switch($_GET['msg']){
          case 'updated':
            $message['msg'] = esc_attr__('Offer has been updated.', 'woocommerce-unlimited-upsell');
            $message['class'] = 'updated';
          break;
          case 'created':
            $message['msg'] = esc_attr__('Offer has been created.', 'woocommerce-unlimited-upsell');
            $message['class'] = 'updated';
          break;
        }
      }

      if ( ($this->plugin_screen_hook_suffix['offers_view'] == $screen->id) && (isset($_GET['action'])) ) {
        switch($_GET['action']){
          case 'delete':
            $message['msg'] = esc_attr__('Offer has been deleted.', 'woocommerce-unlimited-upsell');
            $message['class'] = 'updated';
          break;
          case 'bulk-delete':
            $message['msg'] = esc_attr__('Offers have been deleted.', 'woocommerce-unlimited-upsell');
            $message['class'] = 'updated';
          break;
        }
      }

      if(isset($message)){
        ?>
        <div class="<?php echo $message['class'];?> notice is-dismissible">
          <p><?php echo $message['msg'];?></p>
        </div>
        <?php 
      }
    }

}
