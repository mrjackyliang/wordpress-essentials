<?php
namespace Essentials\Features\AdminDesign;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin design.
 *
 * @since 1.0.0
 */
class AdminDesign {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Add admin color scheme.
		add_action( 'admin_init', array( $this, 'add_admin_color_scheme' ) );
	}

	/**
	 * Add admin color scheme.
	 *
	 * @since 1.0.0
	 */
	function add_admin_color_scheme() {
		wp_admin_css_color(
			'essentials',
			esc_html_x(
				'WordPress Essentials',
				'brand',
				'essentials'
			),
			plugin_dir_url( __FILE__ ) . 'style.css',
			array( '#23282d', '#fff', '#d54e21', '#0073aa' )
		);
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new AdminDesign();
