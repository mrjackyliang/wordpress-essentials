<?php
namespace Essentials\Enhancements\GravityForms;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gravity Forms.
 *
 * @since 1.0.0
 */
class GravityForms {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove forms dashboard.
		add_action( 'wp_dashboard_setup', array( $this, 'remove_forms_dashboard' ) );
	}

	/**
	 * Remove forms dashboard.
	 *
	 * @since 1.0.0
	 */
	function remove_forms_dashboard() {
		remove_meta_box( 'rg_forms_dashboard', 'dashboard', 'side' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new GravityForms();
