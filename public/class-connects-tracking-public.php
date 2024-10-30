<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.connects.ch
 * @since      1.0.0
 *
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Connects_Tracking
 * @subpackage Connects_Tracking/public
 * @author     Marc Dätwyler <marc.daetwyler@connects.ch>
 */
class Connects_Tracking_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    protected $loader;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->loader = new Connects_Tracking_Loader;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Connects_Tracking_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Connects_Tracking_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/connects-tracking-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Connects_Tracking_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Connects_Tracking_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/connects-tracking-public.js', array('jquery'), $this->version, false);
    }

    /**
     * Fire Connects Profiling Tags
     *
     * @since    1.0.0
     */
    public function add_connects_profiling()
    {

        $connectsId = get_option('options_connects_id');
        $connectsProfilingEnabled = Connects_Tracking::get_is_profiling_enabled();
        $connectsConversionTrackingEnabled = Connects_Tracking::get_is_conversion_tracking_enabled();

        if ($connectsId === '' || !Connects_Tracking::is_woocommerce_active()) {
            return;
        }

        if ($connectsProfilingEnabled || $connectsConversionTrackingEnabled) { ?>

            <script type="text/javascript">
                /* <![CDATA[ */
                window._lea = window._lea || [];

                <?php

                global $post, $woocommerce;
                $post_id = $post->ID;
                $product = wc_get_product($post_id);

                $leaObject = array();
                $leaObject["id"] =  $connectsId;
                $leaObject["module"] = 'Profiling';
                $leaObject["event"] = 'PageView';


                if (is_product() && $connectsProfilingEnabled) {
                    $leaObject["event"] = 'ProductView';
                    $leaObject["productPrice"] = $product->get_price();
                    $leaObject["productId"] = $product->get_id();
                    $leaObject["productName"] = $product->get_name();
                    $leaObject["productCategory"] = $product->get_slug();
                }

                if (is_product_category() && $connectsProfilingEnabled) {
                    $leaObject["event"] = 'CategoryView';
                    $leaObject["categoryId"] = get_queried_object()->term_id;
                    $leaObject["categoryName"] = single_cat_title('', false);;
                }

                if (is_cart() && $connectsProfilingEnabled) {
                    $productArray = array();
                    $items = $woocommerce->cart->get_cart();
                    $i = 0;

                    foreach ($items as $item => $values) {
                        $product =  wc_get_product($values['data']->get_id());
                        $productArray[$i]['productId'] = $product->get_id();
                        $productArray[$i]['productName'] = $product->get_name();
                        $productArray[$i]['productPrice'] = $product->get_price();
                        $productArray[$i]['productQuantity'] = $values['quantity'];
                        $i++;
                    }

                    $leaObject["event"] = 'CartView';
                    $leaObject["currency"] = get_woocommerce_currency();
                    $leaObject["products"] = $productArray;
                }


                if (!is_wc_endpoint_url('order-received')) { ?>
                    // Start editable part
                    window._lea.push(<?= json_encode($leaObject) ?>);
                    // End editable part
                    (function(d) {
                        var s = d.createElement("script");
                        s.async = true;
                        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//tc.connects.ch/lila.js";
                        var a = d.getElementsByTagName("script")[0];
                        a.parentNode.insertBefore(s, a)
                    })(document)
                <?php } ?>

                /* ]]> */
            </script>
        <?php
        }
    }

    public function add_connects_conversion($orderId)
    {

        $order = new WC_Order($orderId);
        $orderValue  = $order->get_total();
        $coupons      = $order->get_coupon_codes();
        $currency      = $order->get_currency();
        $connectsId = get_option('options_connects_id');
        $oCategory = ((get_option('options_connects_ocategory', null) !== null) && get_option('options_connects_ocategory', null) !== '') ? get_option('options_connects_ocategory', null) : 'Sales';
        $connectsConversionEnabled = Connects_Tracking::get_is_conversion_tracking_enabled();


        if ($connectsId === '' || !Connects_Tracking::is_woocommerce_active() || !$connectsConversionEnabled) {
            return;
        }

        if (isset($coupons)) :
            foreach ($coupons as $coupon) :
                $voucher = $coupon;
                break;
            endforeach;
        endif;

        if (!isset($voucher)) {
            $voucher = 'undefined';
        }

        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            window._lea = window._lea || [];

            // Start editable part
            window._lea.push({
                id: '<?= $connectsId ?>',
                module: 'OrderTracking',
                event: 'Sale',
                site: 'checkout',
                oid: '<?= $orderId ?>',
                ovalue: <?= $orderValue ?>,
                ocurrency: '<?= $currency ?>',
                ocategory: '<?= $oCategory ?>',
                voucher: '<?= $voucher ?>'
            });
            // End editable part

            (function(d) {
                var s = d.createElement("script");
                s.async = true;
                s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//tc.connects.ch/lila.js";
                var a = d.getElementsByTagName("script")[0];
                a.parentNode.insertBefore(s, a)
            })(document);

            /* ]]> */
        </script>
<?php
    }

    public function create_first_party_cookie()
    {

        if (!Connects_Tracking::get_is_conversion_tracking_enabled() && !Connects_Tracking::get_is_profiling_enabled()) {
            return;
        }

        if (isset($_GET['lea_source'])) {
            $leaSource = $_GET['lea_source'];
            $expiresTs = time() + 60 * 24 * 3600; //Cookie und localstorage TTL 60 days as timestamp
            $expiresRFormat = date("r", $expiresTs);
            $cookieName = '';

            $splitArray = explode('X', $leaSource);
            $trackingType = substr($splitArray[1], 6, 1);

            switch ($trackingType) {
                case 'C':
                    $cookieName = 'lea_source';
                    break;
                case 'V':
                    $cookieName = 'lea_source_pv';
                    break;
            }

            // 1st Party Cookie schreiben
            header('Set-Cookie:' . $cookieName . '=' . $leaSource . '; expires=' . $expiresRFormat . '; path=/; domain=' . $this->getHost($_SERVER['SERVER_NAME']) . '; SameSite=None; Secure'); // using header because set_cookie() SameSite is not supported before PHP 7.3
        }
    }

    private function getHost($domain)
    {
        // $domain is eg. shop.multinet.ch, someshop.com or someshop.co.uk
        $parts = explode('.', $domain);
        $parts = array_reverse($parts);

        if (count($parts) >= 3) {
            // Has three parts or more, eg. ch.multinet.shop or uk.co.someshop
            if (preg_match('/^(com|edu|gv|ac|gov|net|mil|org|nom|co|name|info|biz)$/i', $parts[1])) {
                // Second part is a common SLD (eg. from co.ok): Put together in original order,
                // eg. return someshop.co.uk
                return $parts[2] . '.' . $parts[1] . '.' . $parts[0];
            }
        }
        // Return only the first to parts of the original domain, eg. multinet.ch or someshop.com
        return $parts[1] . '.' . $parts[0];
    }
}
