<?php
/**
 * Dashboard Widget - Most triggered offers all time
 *
 * @package   WooCommerce_Unlimited_Upsell_Admin
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
?>

<div class="postbox">
  <h2 class="hndle"><span><?php esc_attr_e('Most triggered offers (all time)', 'woocommerce-unlimited-upsell');?></span></h2>
  <div class="inside">
    <canvas class="wooup-widget-canvas" data-widget_type="most_triggered_offers" id="wooup-widget-most-triggered-offers" width="400" height="400"></canvas>
  </div><!-- .inside -->
</div><!-- .postbox -->