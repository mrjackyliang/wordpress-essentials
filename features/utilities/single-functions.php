<?php
namespace Essentials\Features\Settings;

use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Single functions.
 *
 * @since 1.0.0
 */
class SingleFunctions {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$essentials         = get_option( 'essentials' );
		$remove_src_version = ! empty( $essentials[ 'remove_src_version' ] ) && $essentials[ 'remove_src_version' ] == 1;
		$search_only_posts  = ! empty( $essentials[ 'search_only_posts' ] ) && $essentials[ 'search_only_posts' ] == 1;

		// Remove version from styles and scripts.
		if ( $remove_src_version ) {
			add_filter( 'style_loader_src', array( $this, 'remove_src_version' ), 9999 );
			add_filter( 'script_loader_src', array( $this, 'remove_src_version' ), 9999 );
		}

		// Show only posts in search.
		if ( $search_only_posts ) {
			add_action( 'pre_get_posts', array( $this, 'search_only_posts' ) );
		}
	}

	/**
	 * Remove version from styles and scripts.
	 *
	 * @param string $src Enqueued source URL.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function remove_src_version( string $src ): string {
		if ( strpos( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}

	/**
	 * Show only posts in search.
	 *
	 * @param string $query The search query.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function search_only_posts( string $query ) {
		/**
		 * @var WP_Query $query
		 */
		if ( $query->is_main_query() && is_search() && ! is_admin() ) {
			// Set post type.
			$query->set( 'post_type', 'post' );
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new SingleFunctions();
