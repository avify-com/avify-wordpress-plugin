<?php
function avify_v1_attachment(WP_REST_Request $request) {
	$jsonRequest = $request->get_json_params();
	$src = wp_get_attachment_url($jsonRequest['id'] ?? '');
	return new WP_REST_Response([
		'src' => $src
	], 200);
}

add_action('rest_api_init', function () {
	register_rest_route('avify/v1', '/attachment', [
		'methods' => 'POST',
		'callback' => 'avify_v1_attachment',
	]);
});
