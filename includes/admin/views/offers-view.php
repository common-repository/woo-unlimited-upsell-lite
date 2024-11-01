<?php
/**
 * WooCommerce Unlimited Upsell.
 *
 * @package   WooCommerce_Unlimited_Upsell_List
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

    <h1>
        <?php echo esc_html(get_admin_page_title()); ?>
        <a href="<?php echo admin_url('admin.php?page=' . $this->plugin_slug . '-offer-edit') ?>" class="page-title-action"><?php esc_attr_e('Add Offer', 'woocommerce-unlimited-upsell');?></a>
    </h1>


    <form id="woocommerce-unlimited-upsell-filter" method="post">

        <input type="hidden" name="page" value="<?php echo (int) $_REQUEST['page'] ?>">

        <?php $wooup_list_offers->display();?>

    </form>

</div>
