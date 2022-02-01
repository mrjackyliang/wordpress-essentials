<?php
namespace Essentials\Features\WhiteLabel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Mail.
 *
 * @since 1.0.0
 */
class Mail {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Replace default email address.
		add_filter( 'wp_mail_from', array( $this, 'replace_default_email_address' ) );

		// Replace default email name.
		add_filter( 'wp_mail_from_name', array( $this, 'replace_default_email_name' ) );
	}

	/**
	 * Replace default email address.
	 *
	 * @since 1.0.0
	 */
	function replace_default_email_address() {
		if ( is_multisite() ) {
			return get_site_option( 'admin_email' );
		}

		return get_option( 'admin_email' );
	}

	/**
	 * Replace default email name.
	 *
	 * @since 1.0.0
	 */
	function replace_default_email_name() {
		if ( is_multisite() ) {
			return get_site_option( 'site_name' );
		}

		return get_option( 'blogname' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Mail();
