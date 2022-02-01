<?php
namespace Essentials\Features\SubscriberAccess;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Subscriber access.
 *
 * @since 1.0.0
 */
class SubscriberAccess {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Redirect dashboard to home.
		add_action( 'admin_init', array( $this, 'redirect_dashboard_to_home' ) );

		// Remove admin bar.
		add_action( 'after_setup_theme', array( $this, 'remove_admin_bar' ) );
	}

	/**
	 * Redirect dashboard to home.
	 *
	 * @since 1.0.0
	 */
	function redirect_dashboard_to_home() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( current_user_can( 'customer' ) ) {
			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Remove admin bar.
	 *
	 * @since 1.0.0
	 */
	function remove_admin_bar() {
		if ( current_user_can( 'customer' )) {
			show_admin_bar( false );
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new SubscriberAccess();
