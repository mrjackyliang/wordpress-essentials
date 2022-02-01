<?php
namespace Essentials\Enhancements\WordPress;

use Exception;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Login.
 *
 * @since 1.0.0
 */
class Login {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Login error messages.
		add_filter( 'wp_login_errors', array( $this, 'custom_wp_login_errors' ) );

		// WooCommerce login error messages.
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_filter( 'woocommerce_process_login_errors', array( $this, 'custom_woocommerce_process_login_errors' ), 10, 3 );
		}
	}

	/**
	 * Login error messages.
	 *
	 * @param WP_Error $errors The WP_Error object.
	 *
	 * @return WP_Error
	 *
	 * @since 1.0.0
	 */
	function custom_wp_login_errors( WP_Error $errors ): WP_Error {
		$request_uri = $_SERVER[ 'REQUEST_URI' ];

		if ( preg_match( '/^\/wp-login\.php\?(action=(register|(lost|retrieve)password))/', $request_uri ) === 0 ) {
			$error_types = [
				'invalid_username',
				'invalid_email',
				'incorrect_password'
			];

			foreach ( $error_types as $error_type ) {
				if ( ! empty( $errors->errors[ $error_type ] ) ) {
					$errors->remove( $error_type );
					$errors->add(
						$error_type,
						wp_kses(
							__(
								'<strong>Error</strong>: The username, email address, or password is incorrect.',
								'essentials'
							),
							array(
								'strong' => array(),
							)
						),
					);
				}
			}
		}

		return $errors;
	}

	/**
	 * WooCommerce login error messages.
	 *
	 * @param WP_Error $validation_error The WP_Error object.
	 * @param string $username The username.
	 * @param string $password The password (plaintext - NOT encrypted).
	 *
	 * @return WP_Error
	 *
	 * @throws Exception
	 *
	 * @since 1.0.0
	 */
	function custom_woocommerce_process_login_errors( WP_Error $validation_error, string $username, string $password ): WP_Error {
		$user_auth   = wp_authenticate( $username, $password );
		$user_errors = $user_auth->errors;
		$error_types = [
			'invalid_username',
			'invalid_email',
			'incorrect_password'
		];

		foreach ( $error_types as $error_type ) {
			if ( ! empty( $user_errors[ $error_type ] ) ) {
				throw new Exception(
					wp_kses(
						__(
							'<strong>Error</strong>: The username, email address, or password is incorrect.',
							'essentials'
						),
						array(
							'strong' => array(),
						)
					)
				);
			}
		}

		return $validation_error;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Login();
