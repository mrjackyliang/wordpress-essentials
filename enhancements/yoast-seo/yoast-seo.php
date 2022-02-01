<?php
namespace Essentials\Enhancements\YoastSEO;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Yoast SEO.
 *
 * @since 1.0.0
 */
class YoastSEO {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove admin bar menu.
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_menu' ) );

		// Script.
		add_action( 'admin_enqueue_scripts', array( $this, 'script' ) );

		// Style.
		add_action( 'admin_enqueue_scripts', array( $this, 'style' ) );
	}

	/**
	 * Remove admin bar menu.
	 *
	 * @since 1.0.0
	 */
	function remove_admin_bar_menu() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'wpseo-menu' );
	}

	/**
	 * Script.
	 *
	 * @since 1.0.0
	 */
	function script() {
		wp_enqueue_script(
			'essentials-yoast-seo',
			plugins_url(
				'script.js',
				__FILE__
			),
			array(
				'jquery'
			),
			ESSENTIALS_VERSION
		);
	}

	/**
	 * Style.
	 *
	 * @since 1.0.0
	 */
	function style() {
		wp_enqueue_style(
			'essentials-yoast-seo',
			plugins_url(
				'style.css',
				__FILE__
			),
			array(),
			ESSENTIALS_VERSION
		);
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new YoastSEO();
