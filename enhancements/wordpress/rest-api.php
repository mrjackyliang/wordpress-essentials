<?php
namespace Essentials\Enhancements\WordPress;

use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Rest API.
 *
 * @since 1.0.0
 */
class RestApi {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove author information from pages.
		add_filter( 'oembed_response_data', array( $this, 'remove_author_from_pages' ), 10, 2 );
	}

	/**
	 * Remove author information from pages.
	 *
	 * @param array $data The response data.
	 * @param WP_Post $post The post object.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function remove_author_from_pages( array $data, WP_Post $post): array {
		$post_type = $post->to_array()[ 'post_type' ];

		if ( $post_type === 'page' ) {
			unset( $data[ 'author_name' ] );
			unset( $data[ 'author_url' ] );
		}

		return $data;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new RestApi();
