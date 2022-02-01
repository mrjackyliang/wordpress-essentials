<?php
namespace Essentials\Features\WhiteLabel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Site.
 *
 * @since 1.0.0
 */
class Site {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Disable "generator" tag.
		add_action( 'init', array( $this, 'disable_generator' ) );
	}

	/**
	 * Disable meta "generator".
	 *
	 * @since 1.0.0
	 */
	function disable_generator() {
		remove_action( 'wp_head', 'wp_generator' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Site();
