<?php
namespace Essentials\Features\WhiteLabel;

use WP_Admin_Bar;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin bar.
 *
 * @since 1.0.0
 */
class AdminBar {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Change "Howdy" text.
		add_action( 'admin_bar_menu', array( $this, 'change_wp_howdy' ) );

		// Remove WordPress logo.
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_wp_logo' ) );

		// Change "My Sites" blavatar.
		if ( is_multisite() ) {
			add_action( 'admin_head', array( $this, 'change_my_sites_blavatar' ) );
			add_action( 'wp_head', array( $this, 'change_my_sites_blavatar' ) );
		}
	}

	/**
	 * Change "Howdy" text.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar The WP_Admin_Bar instance, passed by reference.
	 *
	 * @since 1.0.0
	 */
	function change_wp_howdy( WP_Admin_Bar $wp_admin_bar ) {
		$user_id      = get_current_user_id();
		$profile_url  = get_edit_profile_url( $user_id );
		$current_user = wp_get_current_user();

		if ( 0 !== $user_id ) {
			$avatar       = get_avatar( $user_id, 28 );
			$welcome_back = sprintf( esc_html__( 'Welcome back, %1$s', 'essentials' ), $current_user->display_name );
			$class        = empty( $avatar ) ? '' : 'with-avatar';

			$wp_admin_bar->add_menu(
				array(
					'id'     => 'my-account',
					'parent' => 'top-secondary',
					'title'  => $welcome_back . $avatar,
					'href'   => $profile_url,
					'meta'   => array(
						'class' => $class,
					),
				)
			);
		}
	}

	/**
	 * Remove WordPress logo.
	 *
	 * @since 1.0.0
	 */
	function remove_wp_logo() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'wp-logo' );
	}

	/**
	 * Change "My Sites" blavatar.
	 *
	 * @since 1.0.0
	 */
	function change_my_sites_blavatar() {
		if ( is_admin_bar_showing() ) {
			echo '<style>#wpadminbar .quicklinks li div.blavatar:before { content: \'\f319\'; }</style>';
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new AdminBar();
