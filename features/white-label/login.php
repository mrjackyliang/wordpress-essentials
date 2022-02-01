<?php
namespace Essentials\Features\WhiteLabel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Login.
 *
 * @since 1.0.0
 */
class Login {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Custom login title.
		add_filter( 'login_title', array( $this, 'custom_login_title' ), 10, 2 );

		// Custom login header url.
		add_filter( 'login_headerurl', array( $this, 'custom_login_header_url' ) );

		// Custom login header text.
		add_filter( 'login_headertext', array( $this, 'custom_login_header_text' ) );
	}

	/**
	 * Custom login title.
	 *
	 * @param string $login_title The page title, with extra context added.
	 * @param string $title The original page title.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function custom_login_title( string $login_title, string $title ): string {
		if ( __return_false() ) {
			return $login_title;
		}

		return get_bloginfo( 'name', 'display' ) . ' - ' . $title;
	}

	/**
	 * Custom login header url.
	 *
	 * @since 1.0.0
	 */
	function custom_login_header_url() {
		if ( is_multisite() ) {
			return network_home_url();
		}

		return site_url();
	}

	/**
	 * Custom login header text.
	 *
	 * @since 1.0.0
	 */
	function custom_login_header_text() {
		if ( is_multisite() ) {
			return get_network()->site_name;
		}

		return get_bloginfo( 'name', 'display' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Login();
