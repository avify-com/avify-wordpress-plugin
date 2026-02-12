<?php
function load_avify_checkout_scripts() {
    $options = get_option('avify-settings-options');
    if (($options['avify_enable_checkout'] ?? '') === 'on' && is_checkout()) {
        $v = '1.3.8';
        wp_enqueue_script('avify-checkout', plugin_dir_url( __FILE__ ) . '/assets/avify-checkout.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-resizable'), $v);
        wp_enqueue_style('avify-checkout', plugin_dir_url( __FILE__ ) . '/assets/avify-checkout.css', false, $v);

        // intl-tel-input library (country code dropdown for phone field)
        wp_enqueue_style('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/css/intlTelInput.min.css', array(), '25.3.1');
        wp_enqueue_script('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@25.3.1/build/js/intlTelInput.min.js', array(), '25.3.1', true);
        wp_enqueue_style('avify-phone-intl-overrides', plugin_dir_url( __FILE__ ) . '/assets/avify-phone-intl-overrides.css', array('intl-tel-input'), $v);
        wp_enqueue_script('avify-phone-intl', plugin_dir_url( __FILE__ ) . '/assets/avify-phone-intl.js', array('intl-tel-input'), $v, true);
        $base_country = function_exists('WC') ? WC()->countries->get_base_country() : 'CR';
        wp_localize_script('avify-phone-intl', 'avfPhoneIntl', array(
            'country' => strtolower($base_country),
        ));
    }
}
add_action('wp_enqueue_scripts', 'load_avify_checkout_scripts');