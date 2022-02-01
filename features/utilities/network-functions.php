<?php
namespace Essentials\Features\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Network functions.
 *
 * @since 1.0.0
 */
class NetworkFunctions {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		$essentials_network = get_site_option( 'essentials_network' );
		$network_message    = ! empty( $essentials_network[ 'message' ] ) && $essentials_network[ 'message' ] != '';

		// Network message.
		if ( $network_message ) {
			add_action( 'admin_notices', array( $this, 'network_message' ) );
			add_action( 'network_admin_notices', array( $this, 'network_message' ) );
		}
	}

	/**
	 * Network-wide message.
	 *
	 * @since 1.0.0
	 */
	function network_message() {
		$message_before = '<div class="notice"><p>';
		$message        = get_site_option( 'essentials_network' )[ 'message' ];
		$message_after  = '</p></div>';

		echo $message_before . $message . $message_after;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new NetworkFunctions();
