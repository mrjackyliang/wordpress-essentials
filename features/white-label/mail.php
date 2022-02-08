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
		add_filter( 'wp_mail_from', array( $this, 'replace_default_email_address' ), 10, 1 );

		// Replace default email name.
		add_filter( 'wp_mail_from_name', array( $this, 'replace_default_email_name' ), 10, 1 );
	}

	/**
	 * Replace default email address.
	 *
	 * @param string $from_email Email address to send from.
	 *
	 * @return false|mixed|void
	 *
	 * @since 1.0.0
	 */
	function replace_default_email_address( string $from_email ) {
		$site_name   = wp_parse_url( network_home_url(), PHP_URL_HOST );
		$domain_name = ( str_starts_with( $site_name, 'www.' ) ) ? substr( $site_name, 4 ) : $site_name;

		if ( $from_email === 'wordpress@' . $domain_name ) {
			if ( is_multisite() ) {
				return get_site_option( 'admin_email' );
			}

			return get_option( 'admin_email' );
		}

		return $from_email;
	}

	/**
	 * Replace default email name.
	 *
	 * @param string $from_name Name associated with the "from" email address.
	 *
	 * @return false|mixed|void
	 *
	 * @since 1.0.0
	 */
	function replace_default_email_name( string $from_name ) {
		if ( $from_name === 'WordPress' ) {
			if ( is_multisite() ) {
				return get_site_option( 'site_name' );
			}

			return get_option( 'blogname' );
		}

		return $from_name;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Mail();
