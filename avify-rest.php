<?php

class WC_Avify_Rest {
	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'avify/v1';

	/**
	 *
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Check whether a given request has permission to read webhook deliveries.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_Error|boolean
	 */
	public function get_items_permissions_check( $request ) {
		// Wordfence plugin is causing a blocking error, disable for now
		/* if ( ! $this->perform_basic_authentication() ) {
			return new WP_Error( 'woocommerce_rest_cannot_view', __( 'Sorry, you cannot list resources.', 'woocommerce' ), array( 'status' => rest_authorization_required_code() ) );
		}*/
		return true;
	}

	/**
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/product/categories/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'avify_v1_products_categories' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/attachment',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => array( $this, 'avify_v1_attachment' ),
					// 'permission_callback' => array( $this, 'get_items_permissions_check' ),
				),
				'schema' => array( $this, 'get_public_item_schema' ),
			)
		);
	}

	//

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function avify_v1_products_categories( WP_REST_Request $request ) {
		$product_id = $request->get_param( 'id' );
		$categories = wp_get_post_terms( $product_id, 'product_cat' );
		if ( empty( $categories ) || is_wp_error( $categories ) ) {
			return new WP_Error( 'no_categories', 'No se encontraron categorías para este producto', [ 'status' => 404 ] );
		}

		$category_tree = [];
		foreach ( $categories as $category ) {
			$category_tree = array_merge( $category_tree, $this->avify_get_category_parents( $category ) );
		}
		$category_tree = array_values( array_unique( $category_tree, SORT_REGULAR ) );

		return rest_ensure_response( $category_tree );
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function avify_v1_attachment( WP_REST_Request $request ) {
		//$attachment_id = $request->get_param( 'id' );
		$jsonRequest = $request->get_json_params();
		$attachment_id = $jsonRequest['id'] ?? '';
		$src         = wp_get_attachment_url( $attachment_id );

		return rest_ensure_response( [
			'src' => $src
		] );
	}

	//

	/**
	 * Return the user data for the given consumer_key.
	 *
	 * @param string $consumer_key Consumer key.
	 * @return stdClass
	 */
	private function get_user_data_by_consumer_key( $consumer_key ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"
			SELECT key_id, user_id, permissions, consumer_key, consumer_secret, nonces
			FROM {$wpdb->prefix}woocommerce_api_keys
			WHERE consumer_key = %s
		",
				wc_api_hash( sanitize_text_field( $consumer_key ) )
			)
		);
	}

	/**
	 * @return false|mixed
	 */
	private function perform_basic_authentication() {
		$consumer_key      = '';
		$consumer_secret   = '';

		// If the $_GET parameters are present, use those first.
		if ( ! empty( $_GET['consumer_key'] ) && ! empty( $_GET['consumer_secret'] ) ) { // WPCS: CSRF ok.
			$consumer_key    = $_GET['consumer_key']; // WPCS: CSRF ok, sanitization ok.
			$consumer_secret = $_GET['consumer_secret']; // WPCS: CSRF ok, sanitization ok.
		}

		// If the above is not present, we will do full basic auth.
		if ( ! $consumer_key && ! empty( $_SERVER['PHP_AUTH_USER'] ) && ! empty( $_SERVER['PHP_AUTH_PW'] ) ) {
			$consumer_key    = $_SERVER['PHP_AUTH_USER']; // WPCS: CSRF ok, sanitization ok.
			$consumer_secret = $_SERVER['PHP_AUTH_PW']; // WPCS: CSRF ok, sanitization ok.
		}

		// Stop if don't have any key.
		if ( ! $consumer_key || ! $consumer_secret ) {
			return false;
		}

		// Get user data.
		$user = $this->get_user_data_by_consumer_key( $consumer_key );
		if ( empty( $user ) ) {
			return false;
		}

		// Validate user secret.
		if ( ! hash_equals( $user->consumer_secret, $consumer_secret ) ) { // @codingStandardsIgnoreLine
			return false;
		}

		return $user->user_id;
	}

	/**
	 * @param $category
	 *
	 * @return array
	 */
	private function avify_get_category_parents( $category ) {
		$hierarchy = [];
		while ( $category ) {
			$hierarchy[] = [
				'id'          => $category->term_id,
				'name'        => $category->name,
				'slug'        => $category->slug,
				'parent'      => $category->parent,
				'description' => $category->description,
			];
			if ( $category->parent == 0 ) {
				break;
			}
			$category = get_term( $category->parent, 'product_cat' );
		}

		return array_reverse( $hierarchy );
	}
}

new WC_Avify_Rest();