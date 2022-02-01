<?php
namespace Essentials\Enhancements\WordPress;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin.
 *
 * @since 1.0.0
 */
class Admin {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove "&nbsp;" character.
		add_action( 'content_save_pre', array( $this, 'remove_nbsp_character' ) );

		// Enforce admin color scheme.
		add_action( 'admin_init', array( $this, 'enforce_color_scheme' ), 20 );

		// Disable color scheme picker.
		add_action( 'admin_init', array( $this, 'disable_color_scheme_picker' ) );

		if ( is_multisite() ) {
			// Disable "Delete Blog" menu.
			add_action( 'admin_menu', array( $this, 'disable_delete_blog_menu' ) );

			// Prevent "Delete Blog" page access.
			add_action( 'admin_init', array( $this, 'prevent_delete_blog_page_access' ) );
		}

		// Disable empty "Tools" menu.
		add_action( 'admin_menu', array( $this, 'disable_empty_tools_menu' ) );

		// Prevent empty "Tools" page access.
		add_action( 'admin_init', array( $this, 'prevent_empty_tools_page_access' ) );
	}

	/**
	 * Remove "&nbsp;" character.
	 *
	 * @param string $value Value of the post field.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function remove_nbsp_character( string $value ): string {
		return preg_replace( '/&nbsp;/', '', $value );
	}

	/**
	 * Enforce admin color scheme.
	 *
	 * @since 1.0.0
	 */
	function enforce_color_scheme() {
		global $_wp_admin_css_colors;

		$current_user_id = get_current_user_id();
		$admin_color     = get_user_meta( $current_user_id, 'admin_color', true );

		// Prefer custom design.
		if ( array_key_exists( 'essentials', $_wp_admin_css_colors ) ) {
			if ( $admin_color !== 'essentials' ) {
				update_user_meta( $current_user_id, 'admin_color', 'essentials' );
			}
		} else {
			if ( $admin_color !== 'default' ) {
				update_user_meta( $current_user_id, 'admin_color', 'default' );
			}
		}
	}

	/**
	 * Disable color scheme picker.
	 *
	 * @since 1.0.0
	 */
	function disable_color_scheme_picker() {
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
	}

	/**
	 * Disable "Delete Blog" menu.
	 *
	 * @since 1.0.0
	 */
	function disable_delete_blog_menu() {
		remove_submenu_page( 'tools.php', 'ms-delete-site.php' );
	}

	/**
	 * Prevent "Delete Blog" page access.
	 *
	 * @since 1.0.0
	 */
	function prevent_delete_blog_page_access() {
		if ( preg_match( '/\/wp-admin\/ms-delete-site\.php/', $_SERVER[ 'REQUEST_URI' ] ) ) {
			wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
		}
	}

	/**
	 * Disable empty "Tools" menu.
	 *
	 * @since 1.0.0
	 */
	function disable_empty_tools_menu() {
		if ( ! has_action( 'tool_box' ) && ! current_user_can( 'import' ) ) {
			remove_menu_page( 'tools.php' );
		}
	}

	/**
	 * Prevent empty "Tools" page access.
	 *
	 * @since 1.0.0
	 */
	function prevent_empty_tools_page_access() {
		if (
			preg_match( '/\/wp-admin\/tools\.php/', $_SERVER[ 'REQUEST_URI' ] )
			&& ! has_action( 'tool_box' )
			&& ! current_user_can( 'import' )
		) {
			wp_die( __( 'Sorry, you are not allowed to access this page.' ), 403 );
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Admin();
