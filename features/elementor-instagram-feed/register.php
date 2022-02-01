<?php
namespace Essentials\Features\Widgets\Instagram;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register widget.
 *
 * @since 1.0.0
 */
class Register {
	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 */
	static Register $instance;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		self::$instance = $this;

		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
	}

	/**
	 * Register widget.
	 *
	 * @since 1.0.0
	 */
	function register_widget() {
		require_once( 'widget.php' );

		register_widget( __NAMESPACE__ . '\Widget' );
	}

	/**
	 * Add styles.
	 *
	 * @since 1.0.0
	 */
	function styles() {
		wp_enqueue_style(
			'essentials-elementor-instagram-feed',
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
new Register();
