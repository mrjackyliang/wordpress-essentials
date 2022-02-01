<?php
namespace Essentials\Enhancements\WPEngine;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP Engine.
 *
 * @since 1.0.0
 */
class WPEngine {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove admin bar menu.
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_menu' ) );

		// Remove menu if not Network Admin.
		if ( is_multisite() ) {
			add_action( 'admin_menu', array( $this, 'remove_menu' ), 20 );
		}

		// Remove news feed.
		add_action( 'wp_dashboard_setup', array( $this, 'remove_news_feed' ) );

		// Remove widget.
		add_action( 'widgets_init', array( $this, 'remove_widget' ), 0 );

		// Remove heartbeat throttle.
		add_action( 'init', array( $this, 'remove_heartbeat_throttle' ) );
	}

	/**
	 * Remove admin bar menu.
	 *
	 * @since 1.0.0
	 */
	function remove_admin_bar_menu() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'wpengine_adminbar' );
	}

	/**
	 * Remove menu if not Network Admin.
	 *
	 * @since 1.0.0
	 */
	function remove_menu() {
		if ( ! is_network_admin() ) {
			remove_menu_page( 'wpengine-common' );
		}
	}

	/**
	 * Remove news feed.
	 *
	 * @since 1.0.0
	 */
	function remove_news_feed() {
		remove_meta_box( 'wpe_dify_news_feed', 'dashboard', 'side' );
	}

	/**
	 * Remove widget.
	 *
	 * @since 1.0.0
	 */
	function remove_widget() {
		remove_action( 'widgets_init', 'wpe_register_powered_by_widget' );
	}

	/**
	 * Remove heartbeat throttle.
	 *
	 * @since 1.0.0
	 */
	function remove_heartbeat_throttle() {
		remove_action( 'init', array( 'WPE_Heartbeat_Throttle', 'check_heartbeat_allowed' ) );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new WPEngine();
