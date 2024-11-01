<?php
/**
 * @package   WooCommerce_Unlimited_Upsell Lite
 * @author    KungWoo
 * @license   GPL-2.0+
 * @link      http://kungwoo.com
 * @copyright 2017 KungWoo
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Unlimited Upsell Lite
 * Plugin URI:        http://kungwoo.com/product/woocommerce-unlimited-upsell
 * Description:       WooCommerce Unlimited Upsell Lite gives you the ability to offer an upsell product at the point of checkout based on the contents of the customers shopping cart.
 * Version:           1.1.9
 * Author:            KungWoo
 * Author URI:        http://kungwoo.com
 * Text Domain:       woocommerce-unlimited-upsell-lite
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

/**
 *-----------------------------------------
 * Do not delete this line
 * Added for security reasons: http://codex.wordpress.org/Theme_Development#Template_Files
 *-----------------------------------------
 */
defined('ABSPATH') or die("Direct access to the script does not allowed");
/*-----------------------------------------*/

/*----------------------------------------------------------------------------*
 * * * ATTENTION! * * *
 * FOR DEVELOPMENT ONLY
 * SHOULD BE DISABLED ON PRODUCTION
 *----------------------------------------------------------------------------*/
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
/*----------------------------------------------------------------------------*/

/* Upgrade to Pro to plugin menu */

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wooup_plugin_action_links' );

function wooup_plugin_action_links( $links ) {
  $links[] = '<a href="http://www.kungwoo.com/support?utm_source=wordpress_org&utm_medium=plugin_upgrade_link&utm_campaign=wooup_lite" target="_blank">Help</a>'; 
  $links[] = '<a href="http://www.kungwoo.com?utm_source=wordpress_org&utm_medium=plugin_upgrade_link&utm_campaign=wooup_lite" target="_blank">Upgrade to Pro</a>';
   return $links;
}

/* Upgrade to Pro to admin menu */
/* Disabled due to poor positioning
function wooup_add_external_link_admin_submenu() {
    global $submenu;
    $upgradelink = 'http://kungwoo.com';
    $submenu['woocommerce_unlimited_upsell'][] = array( 'Upgrade', 'manage_options', $upgradelink);
}
add_action('admin_menu', 'wooup_add_external_link_admin_submenu');
*/

/*----------------------------------------------------------------------------*
 * Plugin Settings
 *----------------------------------------------------------------------------*/

/* ----- Plugin Module: Settings ----- */
require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-unlimited-upsell-settings.php';

register_activation_hook(__FILE__, array('WooCommerce_Unlimited_Upsell_Settings', 'activate'));
add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_Settings', 'get_instance'));
/* ----- Module End: Settings ----- */

/*----------------------------------------------------------------------------*
 * Custom DB Tables
 *----------------------------------------------------------------------------*/
/* ----- Plugin Module: Database ----- */
require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-unlimited-upsell-db.php';
register_activation_hook(__FILE__, array('WooCommerce_Unlimited_Upsell_DB', 'activate'));
add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_DB', 'db_check'));
/* ----- Module End: Database ----- */

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-unlimited-upsell.php';

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook(__FILE__, array('WooCommerce_Unlimited_Upsell', 'activate'));
register_deactivation_hook(__FILE__, array('WooCommerce_Unlimited_Upsell', 'deactivate'));

add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell', 'get_instance'));

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if (is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX)) {

    /* ----- Plugin Module: CRUD ----- */
    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-woocommerce-unlimited-upsell-admin-list-offers.php';
    /* ----- Module End: CRUD ----- */

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-woocommerce-unlimited-upsell-admin.php';
    add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_Admin', 'get_instance'));

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-woocommerce-unlimited-upsell-admin-pages.php';
    add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_Admin_Pages', 'get_instance'));

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-woocommerce-unlimited-upsell-admin-pages-offers.php';
    add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_Admin_Pages_Offers', 'get_instance'));

    require_once plugin_dir_path(__FILE__) . 'includes/admin/class-woocommerce-unlimited-upsell-admin-pages-settings.php';
    add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_Admin_Pages_Settings', 'get_instance'));

}

/*----------------------------------------------------------------------------*
 * Handle AJAX Calls
 *----------------------------------------------------------------------------*/

/* ----- Plugin Module: AJAX ----- */
require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-unlimited-upsell-ajax.php';
add_action('plugins_loaded', array('WooCommerce_Unlimited_Upsell_AJAX', 'get_instance'));
/* ----- Module End: AJAX ----- */
