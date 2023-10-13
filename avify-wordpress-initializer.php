<?php

if (!defined('ABSPATH')) exit;

/**
 * Plugin Name: Avify
 * Plugin URI:
 * Description: Connect your WooCommerce account to Avify and send all your orders to one centralized inventory.
 * Version: 1.2.0
 * Author: Avify
 * Author URI: https://avify.com/
 * Text Domain: avify-wordpress
 * Domain Path: /languages
 * Requires at least: 5.6
 * Tested up to: 6.3.1
 * Requires PHP: 7.0
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/** Loads the Avify Plugin. */
function init_avify()
{
    /** Avify Gateway */
    if (!class_exists('WC_Payment_Gateway')) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Avify error: You need to install WooCommerce in order to run Avify');
        }

        /**
         * Outputs an admin notice that WooCommerce needs to be installed.
         */
        function avify_payments_admin_missing_woocommerce()
        {
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <?php
                    printf(
                        wp_kses(
                            __(
                                'Your Avify installation failed. You need to install WooCommerce in order to run Avify.',
                                'avify-wordpress'
                            ),
                            array(
                                'a' => array(
                                    'href' => array(),
                                    'target' => array(),
                                    'rel' => array(),
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

    function add_avify_payments_gateway($methods)
    {
        $methods[] = 'WC_Avify_Payments_Gateway';
        return $methods;
    }
    add_filter('woocommerce_payment_gateways', 'add_avify_payments_gateway');

    /** Avify Shipping */
    include_once('avify-shipping.php');

    /** Avify Custom Options */
    include_once('avify-custom-options.php');

    /** Avify Checkout */
    include_once('avify-checkout.php');
    require_once dirname(__FILE__) . '/includes/checkout.php';
}
add_action('plugins_loaded', 'init_avify', 0);

/** Provides the following action links to the plugin: settings page. */
function avify_payments_action_links($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=avify-settings') . '">' . __('Settings', 'avify-wordpress') . '</a>',
    );
    return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'avify_payments_action_links');

/** Set up plugin localization */
function load_avify_wordpress_textdomain()
{
    load_plugin_textdomain('avify-wordpress', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'load_avify_wordpress_textdomain');

function clear_wc_shipping_rates_cache()
{
    $packages = WC()->cart->get_shipping_packages();
    foreach ($packages as $key => $value) {
        $shipping_session = "shipping_for_package_$key";
        unset(WC()->session->$shipping_session);
    }
}
add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache');

/** Set up plugin admin settings */
function admin_avify_settings() {
    $page = 'avify-settings';
    add_menu_page(
        'Avify',
        'Avify',
        'manage_options',
        $page,
        function () use ($page) {
            echo '<div class="wrap">';
            echo '<h2>Avify</h2>';
            echo '<form method="post" action="options.php">';
                settings_fields($page);
                do_settings_sections($page);
                submit_button('Guardar');
            echo '</form>';
            echo '</div>';
        },
        'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzkiIGhlaWdodD0iNzkiIHZpZXdCb3g9IjAgMCA3OSA3OSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTY5LjYzODMgMjguNzQzNEw2OS4zMTA4IDI4LjU3NTRMNjkuMTY4NCAyOC45MTQyQzY4LjQ1NjUgMzAuNTk3MSA2Ny41OTM3IDMyLjM1MTMgNjYuNTk5OSAzNC4xMjgyTDY2LjQyNjIgMzQuNDM1OEw2Ni43NDIzIDM0LjU5OEM3MC4zNjQ0IDM2LjQ1NDcgNzEuNjU0NCAzOC4yOTE0IDcxLjY1NDQgMzkuMzkwNkM3MS42NTQ0IDQwLjQ5MjYgNzAuMzY0NCA0Mi4zMzIyIDY2Ljc0MjMgNDQuMTkxN0M2Ni4wNjE3IDQ0LjU0MTkgNjUuMzA5OSA0NC44ODM2IDY0LjUwMTIgNDUuMjEzOUM2My44MDM1IDQ0LjAwOTQgNjMuMDU0NiA0Mi44MDQ4IDYyLjI3NDQgNDEuNjM3M0w2Mi4wMDM4IDQxLjIzM0w2MS43MTYyIDQxLjYyNkM2MC40OTQ2IDQzLjI4OSA1OS4yMDE4IDQ0LjkyMDYgNTcuODc3NiA0Ni40ODExTDU3LjcxODIgNDYuNjY5MUw1Ny44NDkyIDQ2Ljg3OThDNjIuODA0IDU0LjkzIDYzLjc0OTQgNjAuNzkwNCA2Mi4xMjM0IDYyLjQxMzVDNjAuNDk3NCA2NC4wMzk1IDU0LjYzNzEgNjMuMDkxMiA0Ni41ODY5IDU4LjEzOTNDNDUuOTIwNSA1Ny43MzIgNDUuMjIyOSA1Ny4yODIxIDQ0LjUwODEgNTYuODAwOEM0NS4wOTQ3IDU2LjMwODIgNDUuNzA3IDU1Ljc3ODYgNDYuMzY3NiA1NS4xODkxQzQ3Ljg0ODQgNTMuODgyMSA0OS4zMjYzIDUyLjQ5MjQgNTAuNzY0MyA1MS4wNTQ0QzUyLjIwNTIgNDkuNjEzNSA1My41OTQ4IDQ4LjEzNTYgNTQuODk5IDQ2LjY2MDVDNTUuNTk5NiA0NS44Nzc0IDU2LjIyMDMgNDUuMTU0MSA1Ni44MDEyIDQ0LjQ1MDhDNTguMTE2OCA0Mi44Nzg5IDU5LjM4OTcgNDEuMjQ0NCA2MC41ODU3IDM5LjU4N0M2MS4xODY2IDM4Ljc1MjcgNjEuNzQxOSAzNy45NTI2IDYyLjI3NzIgMzcuMTQzOEM2My4xMTQ0IDM1Ljg5MDkgNjMuOTE0NiAzNC42MDA5IDY0LjY1MjEgMzMuMzA4MUM2NS42OTQzIDMxLjQ2ODUgNjYuNTc5OSAyOS42ODU5IDY3LjI4MDQgMjguMDExNUM3MC4zMjE3IDIwLjc2MTUgNzAuMTM5NSAxNS4xMzQ2IDY2Ljc0NTEgMTEuNzQzMUM2My40MDQ5IDguNDAyODcgNTcuODkxOSA4LjE3NTEgNTAuNzkyOCAxMS4wNzY4QzUwLjQ3NjcgMTAuMzI3OSA1MC4xMjY1IDkuNTc4OTUgNDkuNzUzNCA4Ljg0NzEyQzQ2Ljg4MDIgMy4yNDg3MiA0My4xOTgyIDAuMjkwMDM5IDM5LjEwMDUgMC4yOTAwMzlDMzUgMC4yOTAwMzkgMzEuMzE4IDMuMjQ4NzIgMjguNDUzMyA4Ljg0NzEyTDI4LjI4NTMgOS4xNzQ1N0wyOC42MjQyIDkuMzE2OTVDMzAuMzA3MSAxMC4wMjg4IDMyLjA2MTIgMTAuODkxNyAzMy44MzgxIDExLjg4NTVMMzQuMTQ1NyAxMi4wNTY0TDM0LjMwOCAxMS43NDMxQzM2LjE2NzUgOC4xMjA5NiAzOC4wMDEzIDYuODMwOTkgMzkuMTAwNSA2LjgzMDk5QzQwLjIwMjUgNi44MzA5OSA0Mi4wNDIxIDguMTIwOTYgNDMuOTAxNiAxMS43NDMxQzQ0LjI1MTggMTIuNDI2NSA0NC41OTY0IDEzLjE4MTIgNDQuOTIzOSAxMy45ODQyQzQzLjcxNjUgMTQuNjg0NyA0Mi41MTIgMTUuNDMwOCA0MS4zNDczIDE2LjIxMTFMNDAuOTQyOSAxNi40ODE2TDQxLjMzNTkgMTYuNzY5MkM0Mi45OTMyIDE3Ljk4OCA0NC42Mjc3IDE5LjI3NzkgNDYuMTkxMSAyMC42MDc4TDQ2LjM3OSAyMC43NjcyTDQ2LjU4OTcgMjAuNjM2MkM1NC42MzcxIDE1LjY4NDIgNjAuNDk3NCAxNC43NDE2IDYyLjEyMzQgMTYuMzY3NkM2My43NDY2IDE3Ljk5MDggNjIuODAxMiAyMy44NDgzIDU3Ljg0OTIgMzEuODk1NkM1Ny40MzYzIDMyLjU2NzcgNTYuOTk3NyAzMy4yNTExIDU2LjUxMDggMzMuOTc0NEM1Ni4wMTgyIDMzLjM4NzggNTUuNDg4NSAzMi43NzU2IDU0Ljg5OSAzMi4xMTQ5QzUzLjU5NDggMzAuNjM3IDUyLjIwMjQgMjkuMTU5MSA1MC43NjQzIDI3LjcxODJDNDkuMzIzNCAyNi4yNzczIDQ3Ljg0NTUgMjQuODg3NyA0Ni4zNzA1IDIzLjU4MzVDNDUuNTc4OCAyMi44NzczIDQ0Ljg1NTUgMjIuMjUzNyA0NC4xNjA3IDIxLjY4MTNDNDIuNTg4OCAyMC4zNjU3IDQwLjk1NDMgMTkuMDkyOCAzOS4yOTcgMTcuODk2OEMzOC40NTcgMTcuMjkwMyAzNy42NTY4IDE2LjczNzkgMzYuODUzOCAxNi4yMDU0QzM1LjYwMzcgMTUuMzcxIDM0LjMxMzcgMTQuNTcwOCAzMy4wMTggMTMuODMwNEMzMS4yMDk4IDEyLjgwMjQgMjkuNDMgMTEuOTE2OCAyNy43MjcyIDExLjIwMjFDMjAuNDcxNSA4LjE2MDg1IDE0Ljg0NDYgOC4zNDU5MSAxMS40NTMxIDExLjczNzRDOC4xMDk5OSAxNS4wODA1IDcuODgyMTggMjAuNTkzNSAxMC43ODY3IDI3LjY5MjZDMTAuMDIwNyAyOC4wMiA5LjI3MTgxIDI4LjM3MDQgOC41NTcwNiAyOC43Mzc3QzIuOTU4NjcgMzEuNjE2NiAwIDM1LjI5ODYgMCAzOS4zOTA2QzAgNDMuNDkxMSAyLjk1ODY3IDQ3LjE3MzEgOC41NTcwNiA1MC4wMzc4TDguODg0NTQgNTAuMjAyOUw5LjAyNjkyIDQ5Ljg2NjlDOS43NTMwNiA0OC4xNTU1IDEwLjYxNTkgNDYuNDA0MiAxMS41OTU1IDQ0LjY2MTVMMTEuNzY5MiA0NC4zNTExTDExLjQ1MzEgNDQuMTg4OEM3LjgzMDkyIDQyLjMzMjIgNi41NDA5NiA0MC40OTI2IDYuNTQwOTYgMzkuMzg3N0M2LjU0MDk2IDM4LjI4ODUgNy44MzA5MiAzNi40NTE4IDExLjQ1MzEgMzQuNTkyM0MxMi4xMjIzIDM0LjI0NDkgMTIuODc0IDMzLjg5NzUgMTMuNjkxMyAzMy41NjQ0QzE0LjM5MTggMzQuNzc0NiAxNS4xMzc5IDM1Ljk3NjMgMTUuOTE4MSAzNy4xNDFMMTYuMTg4NyAzNy41NDUzTDE2LjQ3NjMgMzcuMTUyM0MxNy42OTc5IDM1LjQ5MjIgMTguOTg3OSAzMy44NTc3IDIwLjMxNDggMzIuMjk0NEwyMC40NzQzIDMyLjEwNjRMMjAuMzQzMyAzMS44OTU2QzE1LjM5MTMgMjMuODU0IDE0LjQ0ODggMTcuOTk2NSAxNi4wNzQ3IDE2LjM2NzZDMTcuNzAzNiAxNC43Mzg4IDIzLjU2MTEgMTUuNjgxNCAzMS42MDI4IDIwLjYzNjJDMzIuMjY2MyAyMS4wNDM0IDMyLjk2MzkgMjEuNDkzNCAzMy42ODE1IDIxLjk3NDZDMzMuMDk0OSAyMi40NjQ0IDMyLjQ4NTUgMjIuOTk0IDMxLjgyMiAyMy41ODYzQzMwLjM0OTggMjQuODg0OSAyOC44NjkxIDI2LjI3NzMgMjcuNDI1MyAyNy43MjExQzI1Ljk4NDQgMjkuMTYyIDI0LjU5NDggMzAuNjM5OSAyMy4yOTA2IDMyLjExNDlDMjIuNTgxNSAzMi45MDk0IDIxLjk2MDggMzMuNjMyNyAyMS4zODg0IDM0LjMyNDdDMjAuMDc4NSAzNS44OTA5IDE4LjgwNTYgMzcuNTI1NCAxNy42MDM5IDM5LjE4ODRDMTcuMDA1OSA0MC4wMTk5IDE2LjQ1MDYgNDAuODIwMSAxNS45MTI0IDQxLjYzMTdDMTUuMDc4MSA0Mi44ODE4IDE0LjI3NzkgNDQuMTcxNyAxMy41Mzc1IDQ1LjQ2NzRDMTIuNDk1MyA0Ny4zMDcgMTEuNjA5NyA0OS4wODk1IDEwLjkwOTIgNTAuNzYzOUM3Ljg2Nzk0IDU4LjAxNjggOC4wNTMwNCA2My42NDM3IDExLjQ0NDUgNjcuMDMyM0MxNC43ODc2IDcwLjM3NTQgMjAuMzAwNiA3MC42MDMyIDI3LjQwMjUgNjcuNjk4NkMyNy43MTg2IDY4LjQ0NDcgMjguMDY2IDY5LjE5MzYgMjguNDQxOSA2OS45MjgzQzMxLjMwNjYgNzUuNTI2NyAzNC45ODg2IDc4LjQ4NTQgMzkuMDg5MSA3OC40ODU0QzQxLjU1NTEgNzguNDg1NCA0My44NzAzIDc3LjQyMDQgNDUuOTY4OSA3NS4zMTg4QzQ3LjM0NDMgNzMuOTQzNCA0OC42MTE1IDcyLjEyOTUgNDkuNzM2MyA2OS45MjgzTDQ5LjkwMTUgNjkuNjAwOUw0OS41NjU1IDY5LjQ1ODVDNDcuODU0MSA2OC43MzI0IDQ2LjEwMjggNjcuODY5NSA0NC4zNiA2Ni44OUw0NC4wNTI1IDY2LjcxNjJMNDMuODkwMiA2Ny4wMzIzQzQyLjAzMzYgNzAuNjUxNiA0MC4xOTQgNzEuOTQxNiAzOS4wODkxIDcxLjk0MTZDMzcuOTg3MSA3MS45NDE2IDM2LjE1MDQgNzAuNjUxNiAzNC4yOTM3IDY3LjAyOTVDMzMuOTQ2MyA2Ni4zNjAzIDMzLjU5ODkgNjUuNjA4NSAzMy4yNjU4IDY0Ljc5MTJDMzQuNDc2IDY0LjA5MDcgMzUuNjc3NyA2My4zNDE4IDM2Ljg0MjQgNjIuNTY0NEwzNy4yNDY3IDYyLjI5MzlMMzYuODUzOCA2Mi4wMDYzQzM1LjE5MzYgNjAuNzg3NSAzMy41NTkxIDU5LjQ5NDcgMzEuOTk4NiA1OC4xNjc3TDMxLjgxMDYgNTguMDA4MkwzMS41OTk5IDU4LjEzOTNDMjMuNTUyNiA2My4wOTEyIDE3LjY5NzkgNjQuMDM2NyAxNi4wNzE5IDYyLjQxMzVDMTQuNDQzMSA2MC43ODQ3IDE1LjM4ODUgNTQuOTI3MSAyMC4zMzc2IDQ2Ljg3OThDMjAuNzczMyA0Ni4xNzM2IDIxLjIyMzIgNDUuNDc1OSAyMS42NzYgNDQuODAxQzIyLjE3NDMgNDUuMzkzMyAyMi43MDQgNDYuMDA1NiAyMy4yODc3IDQ2LjY2MDVDMjQuNTkxOSA0OC4xMzg0IDI1Ljk4NDQgNDkuNjE2MyAyNy40MjI1IDUxLjA1NzJDMjguODYwNSA1Mi40OTUzIDMwLjMzODQgNTMuODg0OSAzMS44MTYzIDU1LjE5MkMzMi42MTA4IDU1LjkwMzkgMzMuMzM0MSA1Ni41MjQ2IDM0LjAyNjEgNTcuMDk0MUMzNS41ODk0IDU4LjQwNCAzNy4yMjY4IDU5LjY3NjkgMzguODg5OCA2MC44Nzg2QzM5LjcyNyA2MS40Nzk1IDQwLjUyNzIgNjIuMDM0NyA0MS4zMzMgNjIuNTcwMUM0Mi41ODAzIDYzLjQwNDQgNDMuODcwMyA2NC4yMDE4IDQ1LjE2ODggNjQuOTQ1QzQ3LjAxMTIgNjUuOTkwMSA0OC43OTA5IDY2Ljg3MjggNTAuNDY1MyA2Ny41NzM0QzU3LjcxODIgNzAuNjE0NiA2My4zNDUxIDcwLjQyOTUgNjYuNzMzNyA2Ny4wMzhDNzAuMDc0IDYzLjY5NzggNzAuMzA0NiA1OC4xODQ4IDY3LjQgNTEuMDg1N0M2OC4xNjg5IDUwLjc2MTEgNjguOTE3OCA1MC40MTA4IDY5LjYyNjkgNTAuMDQwNkM3MS44MzA5IDQ4LjkxODYgNzMuNjQ3NyA0Ny42NTE1IDc1LjAyNTkgNDYuMjczMkM3Ny4xMjE4IDQ0LjE3NzQgNzguMTg2OCA0MS44NjUyIDc4LjE4NjggMzkuMzk5MUM3OC4xOTUzIDM1LjI5ODYgNzUuMjM2NyAzMS42MTY2IDY5LjYzODMgMjguNzQzNFpNNTAuNTUwNyAzOS4zOTA2QzUwLjU1MDcgNDUuNzA2NiA0NS40MTM3IDUwLjg0MzYgMzkuMDk3NyA1MC44NDM2QzMyLjc4MTcgNTAuODQzNiAyNy42NDQ2IDQ1LjcwNjYgMjcuNjQ0NiAzOS4zOTA2QzI3LjY0NDYgMzMuMDc0NiAzMi43ODE3IDI3LjkzNzUgMzkuMDk3NyAyNy45Mzc1QzQ1LjQxMzcgMjcuOTM3NSA1MC41NTA3IDMzLjA3NzQgNTAuNTUwNyAzOS4zOTA2WiIgZmlsbD0iIzI3MTc0NCIvPgo8L3N2Zz4K'
    );
}
add_action('admin_menu', 'admin_avify_settings');

function register_avify_settings() {
    $page = 'avify-settings';
    register_setting($page, 'avify-settings-options');

    $section = 'avify-settings-general';
    add_settings_section(
        $section,
        'General',
        function() {echo '';},
        $page
    );

    $field = 'avify_enable_checkout';
    add_settings_field(
        $field, 'Habilitar avify checkout',
        function () use ($field) {
            $options = get_option('avify-settings-options');
            $checked = isset($options[$field]) ? checked('on', $options[$field], false) : '';
            echo "<input type='checkbox' name='avify-settings-options[$field]' $checked />";
        },
        $page, $section
    );

    $field = 'avify_attachment_required';
    add_settings_field(
        $field, 'Comprobante de pago requerido',
        function () use ($field) {
            $options = get_option('avify-settings-options');
            $checked = isset($options[$field]) ? checked('on', $options[$field], false) : '';
            echo "<input type='checkbox' name='avify-settings-options[$field]' $checked />";
            echo "<p>Debes instalar el plugin 'Checkout Files Upload for WooCommerce' para un correcto funcionamiento</p>";
        },
        $page, $section
    );

    $field = 'avify_show_electronic_invoice';
    add_settings_field(
        $field, 'Mostrar formulario de factura electrónica',
        function () use ($field) {
            $options = get_option('avify-settings-options');
            $checked = isset($options[$field]) ? checked('on', $options[$field], false) : '';
            echo "<input type='checkbox' name='avify-settings-options[$field]' $checked />";
            echo "<p>Debes configurar los custom fields requeridos usando el plugin 'Checkout Field Editor for WooCommerce'</p>";
        },
        $page, $section
    );
}
add_action('admin_init', 'register_avify_settings');