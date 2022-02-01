<?php
namespace Essentials\Enhancements\Memberful;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Memberful.
 *
 * @since 1.0.0
 */
class Memberful {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Disable filter admin toolbar.
		add_action( 'init', array( $this, 'disable_filter_admin_toolbar' ), 0 );

		// Disable redirect members home.
		add_action( 'admin_init', array( $this, 'disable_redirect_members_home' ), 0 );
	}

	/**
	 * Disable filter admin toolbar.
	 *
	 * @since 1.0.0
	 */
	function disable_filter_admin_toolbar() {
		remove_action( 'init', 'filter_admin_toolbar' );

		// Reset the option.
		if ( get_option( 'memberful_hide_admin_toolbar' ) !== false ) {
			update_option( 'memberful_hide_admin_toolbar', false );
		}
	}

	/**
	 * Disable redirect members home.
	 *
	 * @since 1.0.0
	 */
	function disable_redirect_members_home() {
		remove_action( 'admin_init', 'redirect_members_home' );

		// Reset the option.
		if ( get_option( 'memberful_block_dashboard_access' ) !== false ) {
			update_option( 'memberful_block_dashboard_access', false );
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Memberful();
