<?php
namespace Essentials\Enhancements\Akismet;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Akismet.
 *
 * @since 1.0.0
 */
class Akismet {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove widget.
		add_action( 'widgets_init', array( $this, 'remove_widget' ), 0 );

		// Remove right now stats.
		add_action( 'init', array( $this, 'remove_rightnow_stats' ) );
	}

	/**
	 * Remove widget.
	 *
	 * @since 1.0.0
	 */
	function remove_widget() {
		remove_action( 'widgets_init', 'akismet_register_widgets' );
	}

	/**
	 * Remove right now stats.
	 *
	 * @since 1.0.0
	 */
	function remove_rightnow_stats() {
		remove_action( 'rightnow_end', array( 'Akismet_Admin', 'rightnow_stats' ) );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Akismet();
