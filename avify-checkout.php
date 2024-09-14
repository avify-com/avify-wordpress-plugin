<?php
function load_avify_checkout_scripts() {
    $options = get_option('avify-settings-options');
    if (($options['avify_enable_checkout'] ?? '') === 'on' && is_checkout()) {
        $v = '1.3.1';
        wp_enqueue_script('avify-checkout', plugin_dir_url( __FILE__ ) . '/assets/avify-checkout.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-resizable'), $v);
        wp_enqueue_style('avify-checkout', plugin_dir_url( __FILE__ ) . '/assets/avify-checkout.css', false, $v);
    }
}
add_action('wp_enqueue_scripts', 'load_avify_checkout_scripts');