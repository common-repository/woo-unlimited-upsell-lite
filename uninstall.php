<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   WooCommerce_Unlimited_Upsell
 * @author    KungWoo
 * @license   GPL-2.0+
 * @link      http://kungwoo.com
 * @copyright 2016 KungWoo
 */

// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'includes/class-woocommerce-unlimited-upsell-db.php';

global $wpdb;

if (is_multisite()) {

    $blogs = $wpdb->get_results("SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A);

    $is_delete_data = get_option('woocommerce_unlimited_upsell_delete_data', 0);
    if($is_delete_data == 1){
      $prefix = $wpdb->prefix;
      $tables = WooCommerce_Unlimited_Upsell_DB::get_tables();
      foreach($tables as $table_id=>$table_name){
        $wpdb->query('DROP TABLE IF EXISTS `' . $prefix . $table_name  . '`');
      }
    }
    delete_option('wooup_db_ver');
    delete_option('woocommerce_unlimited_upsell_delete_data');

    if ($blogs) {

        foreach ($blogs as $blog) {
            switch_to_blog($blog['blog_id']);

            $is_delete_data = get_option('woocommerce_unlimited_upsell_delete_data', 0);
            if($is_delete_data == 1){
              $prefix = $wpdb->prefix;
              $tables = WooCommerce_Unlimited_Upsell_DB::get_tables();
              foreach($tables as $table_id=>$table_name){
                $wpdb->query('DROP TABLE IF EXISTS `' . $prefix . $table_name  . '`');
              }
            }
            delete_option('wooup_db_ver');
            delete_option('woocommerce_unlimited_upsell_delete_data');

            restore_current_blog();
        }
    }

} else {

  $is_delete_data = get_option('woocommerce_unlimited_upsell_delete_data', 0);
  if($is_delete_data == 1){
    $prefix = $wpdb->prefix;
    $tables = WooCommerce_Unlimited_Upsell_DB::get_tables();
    foreach($tables as $table_id=>$table_name){
      $wpdb->query('DROP TABLE IF EXISTS `' . $prefix . $table_name  . '`');
    }
  }
  delete_option('wooup_db_ver');
  delete_option('woocommerce_unlimited_upsell_delete_data');

}
