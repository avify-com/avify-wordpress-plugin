<?php

use App\Utils\Curl;

/**
 * Check if WooCommerce is active
 */
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    if (!function_exists('avify_log')) {
        function avify_log($entry, $mode = 'a', $file = 'avify')
        {
            // Get WordPress uploads directory.
            $upload_dir = wp_upload_dir();
            $upload_dir = $upload_dir['basedir'];
            // If the entry is array, json_encode.
            if (is_array($entry)) {
                $entry = json_encode($entry);
            }
            // Write the log file.
            $file = $upload_dir . '/wc-logs/' . $file . date("Y-m-d") . '.log';
            $file = fopen($file, $mode);
            $bytes = fwrite($file, current_time('mysql') . " : " . $entry . "\n");
            fclose($file);
            return $bytes;
        }
    }

    if (!function_exists('create_avify_quote')) {
        function create_avify_quote($AVIFY_URL, $AVIFY_SHOP_ID)
        {
            avify_log('create_avify_quote...');
            WC()->session->set('avify_quote_' . WC()->session->get('avify_cart_uuid'), 'loading');
            $responseHeaders = [];
            $avifyQuoteCreate = Curl::post(
                $AVIFY_URL . "/rest/V1/guest-carts",
                ['Content-Type: application/json'],
                json_encode([]), $responseHeaders
            );

            if ($avifyQuoteCreate['success']) {
                if(isset($responseHeaders['set-cookie'][0])) {
                    $avifyQuoteId = $avifyQuoteCreate['data'];
                    WC()->session->set('avify_quote_' . WC()->session->get('avify_cart_uuid'), $avifyQuoteId);
                    WC()->session->set('avify_shop_' . WC()->session->get('avify_cart_uuid'), $AVIFY_SHOP_ID);
                    WC()->session->set('avify_quote_cookie_' . WC()->session->get('avify_cart_uuid'), $responseHeaders['set-cookie'][0]);
                    return $avifyQuoteId;
                }
            }

            WC()->session->set('avify_quote_' . WC()->session->get('avify_cart_uuid'), NULL);
            return false;
        }
    }

    function avify_deliveries_init()
    {
        if (!class_exists('WC_Avify_Deliveries')) {
            class WC_Avify_Deliveries extends WC_Shipping_Method
            {
                /**
                 * Constructor for your shipping class
                 *
                 * @access public
                 * @return void
                 */
                public function __construct($instance_id = 0)
                {
                    parent::__construct($instance_id);
                    $this->id = 'avfdeliveries'; // ID for your shipping method. Should be unique.
                    $this->instance_id = absint($instance_id);
                    $this->title = __('Avify Deliveries');  // Title shown in admin
                    $this->method_title = __('Avify Deliveries');  // Title shown in admin
                    $this->method_description = __('All deliveries in one plugin'); // Description shown in admin
                    $this->tax_status = 'none';
                    $this->enabled = "yes"; // This can be added as an setting but for this example its forced enabled
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings',
                        'instance-settings-modal',
                    );
                    $this->init();
                }

                /**
                 * Init your settings
                 *
                 * @access public
                 * @return void
                 */
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
                    $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                /**
                 * calculate_shipping function.
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping($package = array())
                {
                    $isCheckout = (is_checkout() || is_cart());
                    if ($isCheckout) {
                        avify_log('---------------------------------------------------');
                        $AVIFY_URL = $this->get_option('avify_url');
                        $AVIFY_SHOP_ID = $this->get_option('avify_shop_id');
                        $sessionUUID = WC()->session->get('avify_session_uuid');
                        if(is_null($sessionUUID)) {
                            $sessionUUID = uniqid();
                            WC()->session->set('avify_session_uuid', $sessionUUID);
                        }
                        $avifyCookie = WC()->session->get('avify_cookie_' . $sessionUUID);
                        $cart = WC()->cart;

                        if (!isset($package['destination'])) {
                            return;
                        }

                        $latitude = isset($_POST['lpac_latitude']) ? sanitize_text_field($_POST['lpac_latitude']) : 0.00;
                        $longitude = isset($_POST['lpac_longitude']) ? sanitize_text_field($_POST['lpac_longitude']) : 0.00;
                        $fields = isset($_POST['post_data']) ? sanitize_text_field($_POST['post_data']) : null;
                        if ($fields) {
                            $fields = explode('&', $fields);
                            foreach ($fields as $field) {
                                $field = explode('=', $field);
                                if (count($field) == 2) {
                                    if ($field[0] == 'lpac_latitude') {
                                        $latitude = $field[1];
                                    }
                                    if ($field[0] == 'lpac_longitude') {
                                        $longitude = $field[1];
                                    }
                                }
                            }
                        }

                        $address = $package['destination'];
                        $responseHeaders = [];
                        $avifyRates = Curl::post(
                            $AVIFY_URL . "/rest/V1/avify/wordpress/hook/shipping/{$AVIFY_SHOP_ID}",
                            [
                                "Cookie: " . $avifyCookie ?: "",
                                'Content-Type: application/json'
                            ],
                            json_encode(array_merge($address, [
                                "items" => $cart->get_cart(),
                                "country_id" => $address['country'],
                                "weight" => wc_get_weight( $cart->get_cart_contents_weight(), 'g' ),
                                "latitude" => $latitude,
                                "longitude" => $longitude,
	                            //"currency" => get_woocommerce_currency(),
                            ])), $responseHeaders
                        );
                        avify_log(json_encode($avifyRates));
                        if (!isset($avifyRates['data'])) {
                            avify_log('no rates found on avify.');
                            avify_log($avifyRates);
                            return;
                        }
                        if(isset($responseHeaders['set-cookie'][0])) {
                            WC()->session->set('avify_cookie_' . $sessionUUID, $responseHeaders['set-cookie'][0]);
                        }

                        $rates = [];
                        foreach ($avifyRates['data'] as $avifyRate) {
                            if ($avifyRate['available']) {
                                avify_log("{$avifyRate['carrier_code']}_{$avifyRate['method_code']} : {$avifyRate['amount']}");
                                if ($avifyRate['carrier_code'] === 'flatrate') {
                                    $title = $avifyRate['method_title'];
                                } else {
                                    $title = explode('|', $avifyRate['carrier_title']);
                                    $title = $title[0];
                                    $title = $title . ' - ' . $avifyRate['method_title'];
                                }
                                $rates[] = [
                                    "id" => "avfdeliveries-{$avifyRate['carrier_code']}{$avifyRate['method_code']}",
                                    "label" => $title,
                                    "cost" => $avifyRate['amount'],
                                    "package" => $package,
                                    "meta_data" => [
                                        "avify_rate_id" => "{$avifyRate['carrier_code']}_{$avifyRate['method_code']}"
                                    ]
                                ];
                            }
                        }

                        foreach ($rates as $rate) {
                            $this->add_rate($rate);
                        }
                    }
                }

                /**
                 * Init form fields.
                 */
                public function init_form_fields()
                {
                    $this->instance_form_fields = array(
                        'title' => array(
                            'title' => __('Title', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                            'default' => __('Avify Deliveries', 'woocommerce'),
                            'desc_tip' => true
                        ),
                        'avify_shop_id' => array(
                            'title' => __('Avify Shop ID', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('Avify Shop UUID', 'woocommerce'),
                            'default' => null,
                            'desc_tip' => true
                        ),
                        'avify_url' => array(
                            'title' => __('Avify URL', 'woocommerce'),
                            'type' => 'text',
                            'description' => __('Avify Base URL', 'woocommerce'),
                            'default' => '',
                            'desc_tip' => true
                        )
                    );
                }
            }
        }
    }
    add_action('woocommerce_shipping_init', 'avify_deliveries_init');

    function add_avify_deliveries($methods)
    {
        $methods['avfdeliveries'] = 'WC_Avify_Deliveries';
        return $methods;
    }
    add_filter('woocommerce_shipping_methods', 'add_avify_deliveries');

    function save_order_avify_meta($order_id)
    {
        avify_log(WC()->session->get('chosen_shipping_methods'));

        //Load order
        if (!$order_id) return;
        //$order = wc_get_order($order_id);
        $wooCartKey = WC()->session->get('avify_cart_uuid');

        //Order meta
        $avifyQuoteId = WC()->session->get('avify_quote_' . $wooCartKey);
        $avifyShopId = WC()->session->get('avify_shop_' . $wooCartKey);
        update_post_meta($order_id, 'avify_quote_id', $avifyQuoteId);
        update_post_meta($order_id, 'avify_shop_id', $avifyShopId);

        //Clear
        WC()->session->set('avify_quote_' . $wooCartKey, NULL);
        WC()->session->set('avify_shop_' . $wooCartKey, NULL);
        WC()->session->set('avify_local_quote_' . $wooCartKey, NULL);
        WC()->session->set('avify_cart_uuid', NULL);
    }
    add_action('woocommerce_checkout_update_order_meta', 'save_order_avify_meta', 10, 1);
}
