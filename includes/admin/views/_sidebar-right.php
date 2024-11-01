<?php
/**
 * Right sidebar for settings page
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

<!-- Upgrade Sidebar Start --> 
<!-- Promo -->
<div id="postbox-container-1" class="postbox-container sidebar-right">
  
    <div class="meta-box-sortables">
        <div class="postbox">
          <h3 class="wp-ui-primary"><span><?php _e( 'Need More Upsell Conditions?', 'default' ); ?></span></h3>   
            <div class="inside">
                <div>
                 <p class="last-blurb centered">
					<h4><center><?php _e( "Additional options that you'll get with WooCommerce Ultimate Upsell Pro", 'default' ); ?></center></h4>
				</p>

				<ul>
					<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Make the upsell offer when cart total is in a specific price range.', 'default' ); ?></li>
					<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Make the upsell offer available between specific date ranges.', 'default' ); ?></li>
					<li><div class="dashicons dashicons-yes"></div> <?php _e( 'Make the upsell offer based on the quantity of items in the cart.', 'default' ); ?></li>
          <li><div class="dashicons dashicons-yes"></div> <?php _e( 'Make the upsell offer based on the category of the items in the cart.', 'default' ); ?></li>
				</ul>
              </div>
                <div class="centered">
					       <center><a href="http://kungwoo.com/?utm_source=wooup&utm_medium=plugin_sidebar&utm_campaign=Need_more_upsell_options"
					   class="button-primary button-large" target="_blank">
						<?php _e( 'Upgrade to Pro Now', 'default' ); ?></a></center>
				     </div>
            </div>
        </div>
    </div>
  
  <!-- Other Data -->
  <div class="meta-box-sortables">
        <div class="postbox">
            <h3><span><?php esc_attr_e('Useful Links', 'woocommerce-unlimited-upsell');?></span></h3>
            <div class="inside">
                <div>
                    <ul>
                        <li><a class="no-underline" target="_blank" href="http://kungwoo.com?utm_source=wooup&utm_medium=plugin_sidebar&utm_campaign=useful_links"><span class="dashicons dashicons-admin-plugins"></span> <?php esc_attr_e('Plugin Homepage', 'woocommerce-unlimited-upsell');?></a></li>
                        <li><a class="no-underline" target="_blank" href="http://kungwoo.com/documentation?utm_source=wooup&utm_medium=plugin_sidebar&utm_campaign=useful_links"><span class="dashicons dashicons-book"></span> <?php esc_attr_e('Documentation', 'woocommerce-unlimited-upsell');?></a></li>
                        <li><a class="no-underline" target="_blank" href="http://kungwoo.com/support?utm_source=wooup&utm_medium=plugin_sidebar&utm_campaign=useful_links"><span class="dashicons dashicons-sos"></span> <?php esc_attr_e('Support', 'woocommerce-unlimited-upsell');?></a></li>
                    </ul>
                </div>
                <div class="sidebar-footer">
                    © <?php echo date('Y'); ?> <a class="no-underline text-highlighted" href="http://kungwoo.com?utm_source=wooup&utm_medium=plugin_sidebar&utm_campaign=useful_links" title="KungWoo" target="_blank">KungWoo</a>
                </div>
            </div>
        </div>
    </div>
  
</div>