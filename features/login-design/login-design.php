<?php
namespace Essentials\Features\LoginDesign;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Login design.
 *
 * @since 1.0.0
 */
class LoginDesign {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Login message.
		add_action( 'login_message', array( $this, 'login_message' ), 20 );

		// Remove login link separator.
		add_action( 'login_link_separator', array( $this, 'remove_login_link_separator' ) );

		// Remove login shake.
		add_action( 'login_footer', array( $this, 'remove_login_shake' ) );

		// Style.
		add_action( 'login_enqueue_scripts', array( $this, 'style' ) );
	}

	/**
	 * Login message.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function login_message(): string {
		$request_uri                 = $_SERVER[ 'REQUEST_URI' ];
		$is_register_page            = preg_match( '/^\/wp-login\.php\?(action=(register)|checkemail=(registered))/', $request_uri ) > 0;
		$is_lost_password_page       = preg_match( '/^\/wp-login\.php\?(action=((lost|retrieve)password)|checkemail=(confirm))/', $request_uri ) > 0;
		$is_reset_password_page      = preg_match( '/^\/wp-login\.php\?(action=(resetpass|rp))/', $request_uri ) > 0;
		$is_confirm_admin_email_page = preg_match( '/^\/wp-login\.php\?(action=(confirm_admin_email))/', $request_uri ) > 0;

		if ( $is_register_page ) {
			$title       = esc_html__( 'Register', 'essentials' );
			$description = sprintf(
				esc_html__(
					'Your %s account is where you get access to designated content. Register today to get access.',
					'essentials'
				),
				get_bloginfo( 'blogname' )
			);
		} elseif ( $is_lost_password_page ) {
			$title       = esc_html__( 'Lost Password', 'essentials' );
			$description = esc_html__( 'Forgot your password? Reset it by entering your credentials below.', 'essentials' );
		} elseif ( $is_reset_password_page ) {
			$title       = esc_html__( 'Reset Password', 'essentials' );
			$description = esc_html__( 'Reminder to protect your account by choosing a strong password. Or better yet, let us generate one for you!', 'essentials' );
		} elseif ( $is_confirm_admin_email_page ) {
			$title       = esc_html__( 'Security Check', 'essentials' );
			$description = esc_html__( 'Reminder to protect your account by verifying that the administration email still exists.', 'essentials' );
		} else {
			$title       = esc_html__( 'Login', 'essentials' );
			$description = sprintf(
				esc_html__(
					'Manage your %s account here. Browse the community, get access to designated content, and more.',
					'essentials'
				),
				get_bloginfo( 'blogname' )
			);
		}

		$login_message = '<h3 class="sub-title">' . esc_html__( 'Account', 'essentials' ) . '</h3>';
		$login_message .= '<h2 class="title">' . $title . '</h2>';
		$login_message .= '<p class="page-description">' . $description . '</p>';

		return $login_message;
	}

	/**
	 * Remove login link separator.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function remove_login_link_separator(): string {
		return '';
	}

	/**
	 * Remove login shake.
	 *
	 * @since 1.0.0
	 */
	function remove_login_shake() {
		remove_action( 'login_footer', 'wp_shake_js', 20 );
	}

	/**
	 * Style.
	 *
	 * @since 1.0.0
	 */
	function style() {
		wp_enqueue_style(
			'essentials-login-page',
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
new LoginDesign();
