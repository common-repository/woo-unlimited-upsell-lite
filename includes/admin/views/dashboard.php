<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
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

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

<div id="dashboard-widgets-wrap">
  <div id="dashboard-widgets" class="metabox-holder">
    <div id="postbox-container-1" class="postbox-container">
      <div id="normal-sortables" class="meta-box-sortables ui-sortable">
        <?php include_once('_widgets_most_triggered_offers_all_time.php');?>
      </div>  
    </div>
    <div id="postbox-container-1" class="postbox-container">
      <div id="normal-sortables" class="meta-box-sortables ui-sortable">
      <?php include_once('_widgets_most_popular_products_all_time.php');?>
      </div>  
    </div>
  </div>
  </div>

</div>
