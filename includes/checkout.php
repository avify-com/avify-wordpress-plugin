<?php
function avify_checkout_shortcode($atts) {
    ob_start();
    require_once dirname(__FILE__) . '/../templates/checkout.php';
    return ob_get_clean();
}
add_shortcode('avify_checkout', 'avify_checkout_shortcode');