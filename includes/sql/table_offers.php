<?php
/**
 * WooCommerce Unlimited Upsell.
 * 
 * Offers table
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

// NOTE: Variables $table_name and $charset_collate are defined in WooCommerce_Unlimited_Upsell_DB::create_table() function

// SQL Query to be executed
$sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    name varchar(255) NULL,
    priority int(11) NULL,
    type varchar(255) NULL,
    conditions mediumtext NULL,
    products mediumtext NULL,
    options mediumtext NULL,
    is_active tinyint(1) DEFAULT 0 NOT NULL,
    PRIMARY KEY  (id)
 ) $charset_collate;";
