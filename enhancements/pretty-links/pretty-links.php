<?php
namespace Essentials\Enhancements\PrettyLinks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Pretty Links.
 *
 * @since 1.0.0
 */
class PrettyLinks {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove quick add.
		add_action( 'wp_dashboard_setup', array( $this, 'remove_quick_add' ), 20 );
	}

	/**
	 * Remove quick add.
	 *
	 * @since 1.0.0
	 */
	function remove_quick_add() {
		remove_meta_box( 'prli_dashboard_widget', 'dashboard', 'side' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new PrettyLinks();
