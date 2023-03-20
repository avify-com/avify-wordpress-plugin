<?php

if (!defined('ABSPATH')) exit;

/**
 * Plugin Name: Avify
 * Plugin URI:
 * Description: Connect your WooCommerce account to Avify and send all your orders to one centralized inventory.
 * Version: 1.0.8
 * Author: Avify
 * Author URI: https://avify.com/
 * Text Domain: avify-payments
 * Domain Path: /languages/
 * Requires at least: 5.6
 * Tested up to: 6.1.1
 * Requires PHP: 7.0
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * Loads the Avify Plugin.
 */
function init_avify() {
    /** Avify Gateway */
    if (!class_exists('WC_Payment_Gateway')) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Avify error: You need to install WooCommerce in order to run Avify');
        }

        /**
         * Outputs an admin notice that WooCommerce needs to be installed.
         */
        function avify_payments_admin_missing_woocommerce() {
?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php
                    printf(
                        wp_kses(
                            __(
                                'Your Avify installation failed. You need to install <a href="%1$s" target="_blank" rel="noopener noreferrer">WooCommerce</a> in order to run Avify.',
                                'avify-payments'
                            ),
                            array(
                                'a' => array(
                                    'href'   => array(),
                                    'target' => array(),
                                    'rel'    => array(),
                                ),
                            )
                        ),
                        'https://wordpress.org/plugins/woocommerce/'
                    );
                    ?>
                </p>
            </div>
<?php
        }
        add_action('admin_notices', 'avify_payments_admin_missing_woocommerce');
        return;
    }
    include_once('avify-payments-gateway.php');

    function add_avify_payments_gateway($methods) {
        $methods[] = 'WC_Avify_Payments_Gateway';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'add_avify_payments_gateway');

    /** Avify Shipping */
    include_once('avify-shipping.php');

    /** Avify Custom Options */
    include_once('avify-custom-options.php');
}
add_action('plugins_loaded', 'init_avify', 0);

/**
 * Provides the following action links to the plugin: settings page.
 */
function avify_payments_action_links($links) {
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout') . '">' . __('Settings', 'avify-payments') . '</a>',
    );
    return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'avify_payments_action_links');

/**
 * Set up plugin localization.
 */
function load_avify_payments_textdomain() {
    load_plugin_textdomain('avify-payments', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'load_avify_payments_textdomain');
