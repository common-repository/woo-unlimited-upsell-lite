<?php
/**
 * WooCommerce Unlimited Upsell.
 *
 * @package   WooCommerce_Unlimited_Upsell_DB
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

/**
 * Setup custom DB tables
 */
class WooCommerce_Unlimited_Upsell_DB
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
     * Custom table name, WPDB prefix will be added later
     *
     * @since    1.0.0
     *
     * @var      array
     */
    private static $db_tables = array(
      'offers'        =>  'wooup_offers',
      'stats'         =>  'wooup_stats',
      'view_stats'    =>  'wooup_view_stats',
    );

    /**
     * Option name for DB version
     *
     * @since    1.0.0
     *
     * @var      array
     */
    private static $db_option_name = 'wooup_db_ver';

    /**
     * DB version
     *
     * @since    1.0.0
     *
     * @var      array
     */
    private static $db_version = '1.0';

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

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

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::db_setup();
                }

                restore_current_blog();

            } else {
                self::db_setup();
            }

        } else {
            self::db_setup();
        }

    }

    /**
     * Setup Database
     *
     * @since     1.0.0
     */
    private static function db_setup()
    {
        if (get_site_option(self::$db_option_name) != self::$db_version) {

            global $wpdb;

            foreach (self::$db_tables as $entity => $table_name) {
              self::create_table($entity);
            }

            update_option(self::$db_option_name, self::$db_version);

        }
    }


    /**
     * Create table
     *
     * @since     1.0.0
     */
    private static function create_table($entity)
    {
        global $wpdb;
        $table_name      = self::get_table_name($entity);
        $charset_collate = $wpdb->get_charset_collate();
        // Current file dir
        $cur_dir = plugin_dir_path(__FILE__);
        // Filename which contains SQL query to create table
        $filename = 'sql/table_' . $entity . '.php';
        // Include and execute SQL query
        if ($table_name != '' && file_exists($cur_dir . $filename)) {
            include_once $filename;
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            // sql variable is defined in $filename file
            dbDelta($sql);
            return true;
        }
        return false;
    }


    /**
     * Check if DB custom tables needs to be updated
     *
     * @since     1.0.0
     */
    public static function db_check()
    {

        if (get_site_option(self::$db_option_name) != self::$db_version) {
            self::db_setup();
        }

    }

    /**
     * Get table name by entity (table_key)
     *
     * @since     1.0.0
     */
    public static function get_table_name($entity)
    {
        global $wpdb;
        $tables = self::$db_tables;
        if (isset($tables[$entity])) {
            return $wpdb->prefix . $tables[$entity];
        }
        die('Wrong table name: ' . $entity);
    }

    /**
     * Get tables
     *
     * @since     1.0.0
     */
    public static function get_tables(){
      return self::$db_tables;
    }

}