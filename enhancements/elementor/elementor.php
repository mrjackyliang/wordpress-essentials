<?php
namespace Essentials\Enhancements\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor.
 *
 * @since 1.0.0
 */
class Elementor {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove dashboard overview.
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_overview' ), 30 );
	}

	/**
	 * Remove dashboard overview.
	 *
	 * @since 1.0.0
	 */
	function remove_dashboard_overview() {
		remove_meta_box( 'e-dashboard-overview', 'dashboard', 'side' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Elementor();
