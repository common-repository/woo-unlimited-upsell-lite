<?php
/**
 * WooCommerce Unlimited Upsell.
 *
 * @package   WooCommerce_Unlimited_Upsell_AJAX
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
 * Handle AJAX calls
 */
class WooCommerce_Unlimited_Upsell_AJAX
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
     * Initialize the class
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        // Backend AJAX calls
        if (current_user_can('manage_options')) {
            // View/Edit Offer
            add_action('wp_ajax_wooup_admin_search_products', array($this, 'ajax_search_products'));
            add_action('wp_ajax_wooup_admin_get_product_titles', array($this, 'ajax_get_product_titles'));
            // Dashboard Widgets
            add_action('wp_ajax_wooup_admin_get_widget_data', array($this, 'ajax_get_widget_data'));
        }

        // Frontend AJAX calls
        // Add offer products to cart
        add_action('wp_ajax_wooup_add_to_cart', array($this, 'ajax_add_to_cart'));
        add_action('wp_ajax_nopriv_wooup_add_to_cart', array($this, 'ajax_add_to_cart'));
        // Offer view stats
        add_action('wp_ajax_wooup_add_offer_view', array($this, 'ajax_add_offer_view'));
        add_action('wp_ajax_nopriv_wooup_add_offer_view', array($this, 'ajax_add_offer_view'));

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
     * Handle AJAX: Search WooCommerce products
     *
     * This function is the AJAX call that does the search and returns a JSON array of the results in format:
     * array(
     *    array(
     *      'id' => <post_id>,
     *      'title' => <post_title>,
     *    )
     * )
     * 
     * Originally we did this as array( post_id => post_title ), but it turns out that browsers sort AJAX results like this by the numeric ID. So we have fixed the index of each item so that it gives items in the correct order in the select2 drop-down.
     *
     * @since    1.0.0
     */
    public function ajax_search_products()
    {
        check_ajax_referer('wooup_admin_ajax_request', 'nonce'); // Security check

        global $wpdb;

        $result = array();
        $un_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();
        $q = empty($_REQUEST['q']) ? '' : $un_upsell_obj->sanitize($_REQUEST['q']);
        $search = like_escape($q);

        //  $field_id = $_POST['s2ps_post_select_field_id'];
        $default_query = array(
            's'                => $search,
            'posts_per_page'   => -1,
            'paged'            => 1,
            'post_status'      => array('publish'),
            'post_type'        => 'product',
            'order'            => 'ASC',
            'orderby'          => 'title',
            'suppress_filters' => false,
        );

        //  $custom_query = self::$instances[$field_id]->get_addition_query_params();
        // $merged_query = array_merge($default_query, $custom_query);
        $products        = get_posts($default_query);

        // Return a JSON-encoded result.
        foreach ($products as $product) {
          $result[] = $this->get_product_result($product);
        }

        wp_send_json($result);
        die();
    }

    /**
     * Handle AJAX: Get product titles
     *
     *
     * @since    1.0.0
     */
    public function ajax_get_product_titles() {
      check_ajax_referer('wooup_admin_ajax_request', 'nonce'); // Security check
      $un_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();
        
      $result = array();
      $post_ids_str = empty($_REQUEST['post_ids']) ? array() : $_REQUEST['post_ids'];
      $post_ids_str = $un_upsell_obj->sanitize($post_ids_str);
      $post_ids = $un_upsell_obj->parse_csv_line($post_ids_str);
      
      if (is_array($post_ids) && ! empty($post_ids)) {
        foreach ($post_ids as $post_id) {
          $product = get_post($post_id);
          $result[] = $this->get_product_result($product);
        }
      }

      wp_send_json($result);
      die();
    }

    /**
     * Handle AJAX: Get Widget Data
     *
     * @since    1.0.0
     */
    public function ajax_get_widget_data()
    {
        check_ajax_referer('wooup_admin_ajax_request', 'nonce'); // Security check

        global $wpdb;

        $result = array();
        $result['datasets'] = array();

        $un_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();
        $widget_type = empty($_REQUEST['widget_type']) ? '' : $_REQUEST['widget_type'];
        $widget_type = $un_upsell_obj->sanitize($widget_type);

        $colorBorder = 'rgba(255, 206, 86, 1)';
        $colorBg = 'rgba(255, 206, 86, 0.2)';

        $colorBorderFeatured = 'rgba(54, 162, 235, 1)';
        $colorBgFeatured = 'rgba(54, 162, 235, 0.2)';

        switch($widget_type){
          case 'most_triggered_offers':
            // SELECT `name`, `offer_id`, count(`offer_id`) as `count` FROM `wooup_offers`,`wooup_view_stats` WHERE `wooup_offers`.`id` = `wooup_view_stats`.`offer_id` GROUP BY `offer_id` ORDER BY `count` DESC LIMIT 5
          
            $table_name_1 = WooCommerce_Unlimited_Upsell_DB::get_table_name('offers');
            $table_name_2 = WooCommerce_Unlimited_Upsell_DB::get_table_name('view_stats');
            $sql = 'SELECT `name`, `offer_id`, count(`offer_id`) as `count` FROM `' 
                    . $table_name_1 . '`, `' . $table_name_2 . '` WHERE `' 
                    . $table_name_1 . '`.`id` = `' . $table_name_2 . '`.`offer_id` '
                    . 'GROUP BY `offer_id` ORDER BY `count` DESC LIMIT 5';
            $widget_data = $wpdb->get_results( $sql, ARRAY_A );

            foreach ( $widget_data as $row ) 
            {
              $result['labels'][] = 'ID:' . $row['offer_id'] . ' - ' . $row['name'];
              $result['datasets'][0]['data'][] = $row['count'];
              $result['datasets'][0]['backgroundColor'][] = $colorBg;
              $result['datasets'][0]['borderColor'][] = $colorBorder;
            }

            $result['datasets'][0]['label'] = esc_attr__('# of views', 'woocommerce-unlimited-upsell');
            $result['datasets'][0]['borderWidth'] = 1;
            
          break;
          case 'most_popular_upsell_products':
            // SELECT `product_id`, (count(`product_id`)*`product_qty`) as `count` FROM `wooup_stats` GROUP BY `product_id` ORDER BY `count` DESC  LIMIT 5 

            $table_name = WooCommerce_Unlimited_Upsell_DB::get_table_name('stats');
            $sql = 'SELECT `product_id`, (count(`product_id`)*`product_qty`) as `count` FROM `' 
                    . $table_name . '` GROUP BY `product_id` ORDER BY `count` DESC  LIMIT 5 ';
            $widget_data = $wpdb->get_results( $sql, ARRAY_A );
            
            foreach ( $widget_data as $row ) 
            {
              $product = wc_get_product( $row['product_id'] );
              $result['labels'][] = 'ID:' . $row['product_id'] . ' - ' . $product->get_title();
              $result['datasets'][0]['data'][] = $row['count'];
              $result['datasets'][0]['backgroundColor'][] = $colorBg;
              $result['datasets'][0]['borderColor'][] = $colorBorder;
            }

            $result['datasets'][0]['label'] = esc_attr__('# of times added to cart', 'woocommerce-unlimited-upsell');
            $result['datasets'][0]['borderWidth'] = 1;

          break;
        }

        wp_send_json($result);
        die();
    }

    /**
     * Handle AJAX: Add products to cart
     *
     *
     * @since    1.0.0
     */
    public function ajax_add_to_cart()
    {
        check_ajax_referer('wooup_ajax_request', 'nonce'); // Security check

        global $wpdb;
        global $woocommerce;

        $un_upsell_obj = WooCommerce_Unlimited_Upsell::get_instance();
	
        $woocommerce->cart->maybe_set_cart_cookies(true);

        $offer_id = isset( $_POST['offer_id'] ) ? (int) $_POST['offer_id'] : 0;
        $product_id = isset( $_POST['product_id'] ) ? (int) $_POST['product_id'] : 0;
        $quantity = isset( $_POST['quantity'] ) ? (int) $_POST['quantity'] : 0;

        $variation_id = isset( $_POST['variation_id'] ) ? (int) $_POST['variation_id'] : 0;
        $variation_data = isset($_POST['variation_data']) ? (array) $_POST['variation_data'] : '';
        $variation_data = $un_upsell_obj->sanitize($variation_data);
      
        if ( $variation_id > 0) {
            $woocommerce->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data);
            $product = new WC_Product_Variation($variation_id);
            $product_price = $product->get_price();
        } else {
            $woocommerce->cart->add_to_cart($product_id, $quantity);
            $product = new WC_Product($product_id);
            $product_price = $product->get_price();
        }

        // Add stats
        $wpdb->insert(
            WooCommerce_Unlimited_Upsell_DB::get_table_name('stats'),
            array(
                'offer_id' => $offer_id,
                'product_id' => $product_id,
                'product_qty' => $quantity,
                'product_price' => $product_price,
                'variation_id' => $variation_id,
                'product_data' => serialize($variation_data),
            ),
            array(
                '%d',
                '%d',
                '%d',
                '%s',
                '%d',
                '%s',
            )
        );

        $woocommerce->cart->maybe_set_cart_cookies(true);

        $id = $wpdb->insert_id;
        $result = array($id);

        wp_send_json($result);

        //echo $woocommerce->cart->get_cart_contents_count();
        wp_die();
    }

    /**
     * Helper: return product search result
     *
     *
     * @since    1.0.0
     */
    private function get_product_result($product)
    {
      $product_title = $product->post_title;
      $id         = $product->ID;

      $result_title = '#' . $id . ' – ' . $product_title;

      return array(
            'id'    => $id,
            'title' => $result_title,
          );
    }

    /**
     * Handle AJAX: Add View offer stats
     *
     *
     * @since    1.0.0
     */
    public function ajax_add_offer_view()
    {
        check_ajax_referer('wooup_ajax_request', 'nonce'); // Security check
        global $wpdb;

        $offer_id = isset($_POST['offer_id']) ? (int) $_POST['offer_id'] : 0;
        $cur_date = date( 'Y-m-d H:i:s' );

        $wpdb->insert(
            WooCommerce_Unlimited_Upsell_DB::get_table_name('view_stats'),
            array(
                'offer_id' => $offer_id,
                'date' => $cur_date,
            ),
            array(
                '%d',
                '%s',
            )
        );

        $id = $wpdb->insert_id;
        $result = array($id);

        wp_send_json($result);
        wp_die();
    }
}
