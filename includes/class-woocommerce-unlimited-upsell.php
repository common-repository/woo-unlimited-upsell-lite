<?php
/**
 * WooCommerce Unlimited Upsell.
 *
 * @package   WooCommerce_Unlimited_Upsell
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

class WooCommerce_Unlimited_Upsell
{

    /**
     * Plugin version name
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private static $VERSION_NAME = 'woocommerce_unlimited_upsell_version';

    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    private static $VERSION = '1.0.0';

    /**
     * Unique identifier for your plugin.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text. Its value should match the Text Domain file header in the main
     * plugin file.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    private static $PLUGIN_SLUG = 'woocommerce-unlimited-upsell';

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct()
    {

        // Load plugin text domain
        add_action('init', array($this, 'load_plugin_textdomain'));

        // Activate plugin when new blog is added
        add_action('wpmu_new_blog', array($this, 'activate_new_site'));

        // Load public-facing css and js
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles_and_scripts'));
        // Append Popup HTML
        add_action('wp_footer', array($this, 'get_upsell_offer'));
    }

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug()
    {
        return self::$PLUGIN_SLUG;
    }

    /**
     * Return the plugin version.
     *
     * @since    1.0.0
     *
     * @return    Plugin version variable.
     */
    public function get_plugin_version()
    {
        return self::$VERSION;
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
                    self::single_activate();
                }

                restore_current_blog();

            } else {
                self::single_activate();
            }

        } else {
            self::single_activate();
        }

    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_deactivate();

                }

                restore_current_blog();

            } else {
                self::single_deactivate();
            }

        } else {
            self::single_deactivate();
        }

    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    1.0.0
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site($blog_id)
    {

        if (1 !== did_action('wpmu_new_blog')) {
            return;
        }

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();

    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    1.0.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids()
    {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
            WHERE archived = '0' AND spam = '0'
            AND deleted = '0'";

        return $wpdb->get_col($sql);

    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    1.0.0
     */
    private static function single_activate()
    {
        update_option(self::$VERSION_NAME, self::$VERSION);

        // @TODO: Define activation functionality here
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    1.0.0
     */
    private static function single_deactivate()
    {
        // @TODO: Define deactivation functionality here
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain()
    {
        $domain = self::$PLUGIN_SLUG;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, basename(plugin_dir_path(dirname(__FILE__))) . '/languages/');
    }

    /**
     * A smart way to redirect to an URL even if headers were sent.
     * @param str $url
     */
    public function redirect($url)
    {
        if (headers_sent()) {
            //$url = esc_url($url);
            echo "<script>\n";
            echo "window.location.href = '$url';";
            echo "</script>\n";
        } else {
            wp_safe_redirect($url);
        }
        
        exit;
    }

    /**
     * A smart way to sanitize data: scalar or arrays
     * @param str/mixed $data
     */
    public function sanitize($inp_data)
    {
        if (is_scalar($inp_data)) {
            $data = sanitize_text_field($inp_data);
        } elseif (is_array($inp_data)) {
            $data = array();
            
            foreach ($inp_data as $key => $value) {
                $key = $this->sanitize($key);
                $data[$key] = $this->sanitize($value);
            }
        } else {
            throw new Exception(__METHOD__ . ' Invalid data passed for sanitization.');
        }
        
        return $data;
    }
    
    /**
     * Load public-facing css and js
     *
     * @since    1.0.0
     */
    public function enqueue_styles_and_scripts()
    {
        // global $woocommerce;
        // $cart_url = $woocommerce->cart->get_cart_url();
        // $checkout_url = $woocommerce->cart->get_checkout_url();

        // global $wp;
        // $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
        if (function_exists('is_cart') && is_cart()) {
            wp_enqueue_style($this->get_plugin_slug() . '-plugin-css', plugins_url('assets/css/style.css', __FILE__), array(), $this->get_plugin_version());
            wp_enqueue_script($this->get_plugin_slug() . '-modal', plugins_url('assets/js/jquery.wooup-modal.js', __FILE__), array('jquery'), $this->get_plugin_version(), true);
            wp_enqueue_script($this->get_plugin_slug() . '-app-js', plugins_url('assets/js/app.js', __FILE__), array('jquery', $this->get_plugin_slug() . '-modal'), $this->get_plugin_version(), true);

            $js_options = array(
                'ajax' => array(
                    'url'   => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('wooup_ajax_request'),
                ),
                'e' =>
                  array(
                    'not_available' => esc_attr__('Product not available…', 'woocommerce-unlimited-upsell'),
                    'adding' => esc_attr__('Adding…', 'woocommerce-unlimited-upsell'),
                    'added' => esc_attr__('Added!', 'woocommerce-unlimited-upsell'),
                 )
            );

            wp_localize_script($this->get_plugin_slug() . '-app-js', 'WooUpSettings', $js_options);
        }

    }

    /*
     * Parses a string by commas for product ids separated by a comma
     */
    public function parse_csv_line($csv_list) {
        $products = empty($csv_list) ? array() : explode(',', $csv_list);
        $products = array_map('trim', $products); // no spaces
        $products = array_filter($products); // rm empty ones
        $products = array_map('intval', $products); // make sure all items are ints
        $products = array_unique($products); // no dups
        
        return $products;
    }
    
    /**
     * Get uspell offer.
     * Business logic to determine which offer to display.
     *
     * @since    1.0.0
     */
    public function get_upsell_offer()
    {
        global $wpdb;

        if (is_cart()) {

          // Get variables required for offer conditions
          // Condition #1
          $cart_total = WC()->cart->total;
          // Condition #2
          $current_date = time(); 
          // Condition #3
          $cart_items = WC()->cart->get_cart();
          $cart_items_ids = array();
          foreach($cart_items as $uid => $item){
            $cart_items_ids[] = $item['product_id'];
          }
          // Condition #4
          $cart_qty = WC()->cart->get_cart_contents_count();

          // Get all active offers ordered by priority
          $offers = $wpdb->get_results( 'SELECT * FROM ' . WooCommerce_Unlimited_Upsell_DB::get_table_name('offers') . ' WHERE (`is_active`=1) AND (`type`="checkout") AND (`conditions` <> "") AND (`products` <> "") ORDER BY `priority` DESC, `id` ASC', OBJECT );
          
          // Loop throug offers and check for matched conditions
          foreach($offers as $offer){
            $conditions_total = 0; // Total amount of conditions
            $conditions_passed = 0; // Total amount of passed conditions
            $conditions = maybe_unserialize($offer->conditions);
            $offer_options = maybe_unserialize($offer->options);

            if(is_array($conditions)){ 
              foreach($conditions as $index => $condition){
                if(isset($condition['status']) && $condition['status']==1){
                  switch($index){
                    case 0: // Condition #1 (Cart total price range )
                      $price_min = (float)$condition['price_min'];
                      $price_max = (float)$condition['price_max'];

                      if( ( ($price_min > 0) ? ($cart_total >= $price_min) : true) && ( ($price_max > 0) ? ($cart_total <= $price_max) : true) ){
                        $conditions_passed++;
                      }
                    break;
                    case 1: // Condition #2 (Specific Dates)
                    $date_start = strtotime($condition['date_start'] . ' 00:00:00');
                    $date_end = strtotime($condition['date_end'] . ' 00:00:00');
                      if( ( ($date_start !='' ) ? ($current_date >= $date_start) : true) && ( ($date_end !='' ) ? ($current_date <= $date_end) : true) ){
                        $conditions_passed++;
                      }
                    break;
                    case 2: // Condition #3 (Specific products are in cart)
                      $products = $this->parse_csv_line($condition['products']);
                      $products_count = count($products);
                      $cart_items_intersect = array_intersect($cart_items_ids, $products);
                      $cart_items_intersect_count = count($cart_items_intersect);

                      if($condition['relation'] == 'any'){
                        if( $cart_items_intersect_count > 0 ){
                          $conditions_passed++;
                        }
                      }else{ // all
                        if( $cart_items_intersect_count == $products_count ){
                          $conditions_passed++;
                        }
                      }             
                    break;
                    case 3: // Condition #4 (Total cart items quantity)
                      $qty_min = (int) $condition['qty_min'];
                      $qty_max = (int) $condition['qty_max'];

                      if( ( ($qty_min > 0) ? ($cart_qty >= $qty_min) : true) && ( ($qty_max > 0) ? ($cart_qty <= $qty_max) : true) ){
                        $conditions_passed++;
                      }
                    break;
                  }

                  $conditions_total++;
                }
              }
              if($conditions_total == $conditions_passed){
                // Display first offer whose conditions have been matched
                $this->show_offer($offer->id);
                break; //Exit from foreach loop
              }
            }
          }

        }
    }

    /**
     * Show Offer
     *
     * Links:
     * https://webcache.googleusercontent.com/search?q=cache:jwNka39PrkgJ:https://snippets.webaware.com.au/snippets/woocommerce-add-to-cart-with-quantity-and-ajax/+&cd=4&hl=en&ct=clnk&gl=by
     * http://stackoverflow.com/questions/27270880/add-a-variation-to-cart-using-ajax-woocommerce-api
     * https://gist.github.com/simonlk/3967956
     *
     * @param    int    $offer_id    ID of offer.
     *
     * @since    1.0.0
     */
    private function show_offer($offer_id = 0)
    {
        global $wpdb;
        $offer_id = absint($offer_id);

        if ( $offer_id == 0){
          return false;
        }

        // Select offer
        $offer = $wpdb->get_row( 'SELECT * FROM ' 
                . WooCommerce_Unlimited_Upsell_DB::get_table_name('offers') 
                . ' WHERE (`is_active`=1) AND (`id`= ' . $offer_id . ') LIMIT 1', OBJECT );

        // List of offer products
        $offer_items       = $this->parse_csv_line($offer->products);
        $offer_items_count = count($offer_items);

        $offer_options = unserialize($offer->options);
        $offer_title = isset( $offer_options['title'] ) ? $offer_options['title'] : '';
        $offer_text = isset( $offer_options['text'] ) ? $offer_options['text'] : '';

        // Use Product Factory to get WooCommerce product object by passed product id
        // https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html
        $_pf = new WC_Product_Factory();
        ?>
        <div id="wooup-offer-wrapper" data-offer-id="<?php echo $offer_id; ?>" class="wooup-offer-wrapper wooup-hide wooup-products-<?php echo $offer_items_count; ?> wooup-offer-id-<?php echo $offer_id; ?>">
          <div class="wooup-offer-title"><?php echo $offer_title; ?></div>
          <div class="wooup-offer-text">
            <?php echo $offer_text; ?>
          </div>
          <div class="wooup-products-row">
            <?php
            foreach ($offer_items as $product_id) {
              $_product    = $_pf->get_product($product_id);
              $prodcut_url = get_permalink($product_id);
              ?>
            <div class="wooup-product-col" >
              <div class="wooup-product-image">
                <a href="<?php echo $prodcut_url; ?>" target="_blank"><?php echo $_product->get_image(); ?></a>
              </div>
              <div class="wooup-product-title">
                <a href="<?php echo $prodcut_url; ?>" target="_blank"><?php echo $_product->get_title(); ?></a>
              </div>
              <div class="wooup-product-msg"></div>
              <div class="wooup-product-price-wrapper" >
                <div class="wooup-product-price-original" >
                  <?php echo $_product->get_price_html(); ?>
                </div>
                <div class="wooup-product-price-variation" ></div>
              </div>

              <?php
                if ($_product->is_type('variable')) {
                  // https://docs.woocommerce.com/wc-apidocs/class-WC_Product_Variable.html
                  $variations     = $_product->get_available_variations();
                  $attributes     = $_product->get_variation_attributes();
                  $attribute_keys = array_keys($attributes);

                  ?>
              <div class="wooup-product-variations" data-product_variations="<?php echo htmlspecialchars(json_encode($variations)) ?>">

              <?php foreach ($attributes as $attribute_name => $options) {
                      ?>

                <div><label for="<?php echo sanitize_title($attribute_name); ?>"><?php echo wc_attribute_label($attribute_name); ?></label>
                </div>
                <div>
                  <select name="attribute_<?php echo $attribute_name; ?>" class="wooup-product-attribute">
                    <option value=""><?php esc_attr_e('Choose an option', 'woocommerce');?></option>
                    <?php foreach ($options as $option) {?>
                      <option value="<?php echo $option; ?>"><?php echo $option; ?></option>
                    <?php }?>
                  </select>
                  <?php
                  echo end($attribute_keys) === $attribute_name ? apply_filters('wooup_reset_variations', '<span class="wooup_reset_variations">' . esc_attr__('Clear', 'woocommerce') . '</span>') : '';
                      ?>
                </div>

              <?php }?>
              </div>
              <?php }?>
              <div class="wooup-product-actions">
                  <div class="quantity">
                  <input type="number" step="1" min="1" name="quantity" value="1" title="Qty" class="input-text wooup-product-qty text" size="4" pattern="[0-9]*" inputmode="numeric">
                  </div>
                  <button data-quantity="1" data-variation_id="" data-variation_data="" data-product_id="<?php echo $product_id; ?>"
                      class="button alt wooup-add-to-cart-btn" <?php echo ($_product->is_type('variable')) ? 'disabled' : ''; ?> >
                      <?php esc_attr_e('Add to cart', 'woocommerce');?>
                  </button>
              </div>
            </div>
            <?php } ?>

          </div><!-- .wooup-products-row -->
          <div class="wooup-offer-actions">
            <a href="<?php echo wc_get_checkout_url(); ?>" class="button alt wooup-checkout-btn"><?php esc_attr_e('Proceed to Checkout', 'woocommerce');?></a>
          </div>
        </div><!-- #wooup-offer-wrapper -->
        <?php
    }

}
