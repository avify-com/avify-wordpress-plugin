<?php

/**
 * @param $recipient string
 * @param $order WC_Order
 * @param $email WC_Email_New_Order
 *
 * @return string
 */
function avify_clean_order_email_recipient( $recipient, $order, $email ) {
	if ( ! $order || ! is_a( $order, 'WC_Order' ) ) return $recipient;
	if ($order->meta_exists("_avify_order_id")) {
		avify_log("stop email notification => {$order->get_id()} | {$order->get_meta('_avify_order_id')} | $recipient");
		$recipient = '';
	}
	return $recipient;
}
add_filter( 'woocommerce_email_recipient_new_order', 'avify_clean_order_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_on_hold_order', 'avify_clean_order_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'avify_clean_order_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_pending_order', 'avify_clean_order_email_recipient', 10, 3 );

/**
 * @param $enabled boolean
 * @param $email WC_Email_New_Order
 *
 * @return boolean
 */
function avify_disable_new_email($enabled, $email) {
	if ($email->id === 'new_order') {
		$order = $email->object;
		if ($order->meta_exists("_avify_order_id")) {
			avify_log("stop email notification => {$order->get_id()} | {$order->get_meta('_avify_order_id')}");
			return false;
		}
	}
	return $enabled;
}
add_filter('woocommerce_email_enabled_new_order', 'avify_disable_new_email', 10, 2);

/**
 * Blocks setting the "refunded" status via REST API on orders paid with Tilopay,
 * since the Tilopay plugin triggers a real money refund on that status change.
 *
 * @param $order WC_Order
 * @param $request WP_REST_Request
 * @param $creating boolean
 *
 * @return WC_Order|WP_Error
 */
function avify_block_external_tilopay_refunds( $order, $request, $creating ) {
	if ( ! $creating && $request->get_param( 'status' ) === 'refunded' ) {
		$existing_order = wc_get_order( $order->get_id() );
		if ( $existing_order && $existing_order->get_payment_method() === 'tilopay' ) {
			avify_log( "stop external refund (tilopay) => {$order->get_id()}" );
			return new WP_Error(
				'refund_blocked',
				'Orden pagada con Tilopay NO puede ser reembolsada desde sistemas externos.',
				array( 'status' => 403 )
			);
		}
	}
	return $order;
}
add_filter( 'woocommerce_rest_pre_insert_shop_order_object', 'avify_block_external_tilopay_refunds', 10, 3 );

/**
 * @param $can boolean
 * @param $order WC_Order
 *
 * @return mixed
 */
function avify_can_reduce_order_stock($can, $order) {
	if ($order->meta_exists("_avify_order_id")) {
		avify_log("stop stock reduction => {$order->get_id()} | {$order->get_meta('_avify_order_id')}");
		return false;
	}
	return $can;
};
add_filter( 'woocommerce_can_reduce_order_stock','avify_can_reduce_order_stock', 10, 2 );