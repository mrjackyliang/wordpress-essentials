<?php
namespace Essentials\Enhancements\WordPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Site.
 *
 * @since 1.0.0
 */
class Site {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Redirect multisite signup when registration is disabled.
		if ( is_multisite() ) {
			add_action( 'signup_header', array( $this, 'redirect_multisite_signup' ) );
		}

		// Replace "#" links with "javascript:void(0);".
		add_filter( 'walker_nav_menu_start_el', array( $this, 'replace_hash_with_javascript_void' ), 1000, 1 );
	}

	/**
	 * Redirect multisite signup when registration is disabled.
	 *
	 * @since 1.0.0
	 */
	function redirect_multisite_signup() {
		$registration = get_site_option( 'registration', 'none' );

		if ( $registration === 'none' && ! current_user_can( 'manage_network' ) ) {
			wp_safe_redirect( site_url() );
			die();
		}
	}

	/**
	 * Replace "#" links with "javascript:void(0);".
	 *
	 * @param string $item_output The menu item's starting HTML output.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function replace_hash_with_javascript_void( string $item_output ): string {
		// Links with double quotes.
		if ( strpos( $item_output, 'href="#"' ) ) {
			$item_output = str_replace( 'href="#"', 'href="javascript:void(0);"', $item_output );
		}

		// Links with single quotes.
		if ( strpos( $item_output, 'href=\'#\'' ) ) {
			$item_output = str_replace( 'href=\'#\'', 'href=\'javascript:void(0);\'', $item_output );
		}

		return $item_output;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Site();
