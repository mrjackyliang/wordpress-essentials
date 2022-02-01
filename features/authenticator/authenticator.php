<?php
namespace Essentials\Features\Authenticator;

use Essentials\Features\Authenticator\Base32 as Base32;
use WP_User;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Authenticator.
 *
 * @since 1.0.0
 */
class Authenticator {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		require_once( 'base32.php' );

		// Login form.
		add_action( 'login_form', array( $this, 'login_form' ) );

		// WooCommerce login form.
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'woocommerce_login_form', array( $this, 'woocommerce_login_form' ) );
		}

		// Authenticate token.
		add_filter( 'authenticate', array( $this, 'authenticate_token' ), 20, 2 );

		// Refresh secret.
		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_authenticator', array( $this, 'refresh_secret' ) );
		}

		// "Profile" options.
		add_action( 'profile_personal_options', array( $this, 'profile_options' ) );

		// Update "Profile" options.
		add_action( 'personal_options_update', array( $this, 'profile_options_update' ) );

		// "Edit user profile" options.
		add_action( 'edit_user_profile', array( $this, 'edit_user_options' ) );

		// Update "Edit user profile" options.
		add_action( 'edit_user_profile_update', array( $this, 'edit_user_options_update' ) );

		// Users column.
		add_filter( 'manage_users_columns', array( $this, 'users_column' ) );
		if ( is_multisite() ) {
			add_filter( 'wpmu_users_columns', array( $this, 'users_column' ) );
		}

		// Users column information.
		add_action( 'manage_users_custom_column', array( $this, 'users_column_information' ), 10, 3 );
	}

	/**
	 * [Helper function] Verify token.
	 *
	 * Returns the login time slot or false.
	 *
	 * @param string $secret_key User secret key.
	 * @param string $the_token Token by the user attempting to log in.
	 * @param string $last_time_slot Last successful login time.
	 *
	 * @return bool|int
	 *
	 * @since 1.0.0
	 */
	function verify_token( string $secret_key, string $the_token, string $last_time_slot ): bool|int {
		// Count digits.
		if ( strlen( $the_token ) != 6 ) {
			return false;
		} else {
			$the_token = intval( $the_token );
		}

		$first_count = - 1;
		$last_count  = 1;

		$tm = floor( time() / 30 );

		$secret_key = Base32::decode( $secret_key );

		for ( $i = $first_count; $i <= $last_count; $i ++ ) {
			// Pack time into binary string.
			$time = chr( 0 ) . chr( 0 ) . chr( 0 ) . chr( 0 ) . pack( 'N*', $tm + $i );

			// Hash it with users secret key.
			$hmac = hash_hmac( 'SHA1', $time, $secret_key, true );

			// Use last nipple of result as index/offset.
			$offset = ord( substr( $hmac, - 1 ) ) & 0x0F;

			// Grab 4 bytes of the result.
			$hash_part = substr( $hmac, $offset, 4 );

			// Unpack binary value.
			$value = unpack( "N", $hash_part );
			$value = $value[ 1 ];

			// Only 32 bits.
			$value = $value & 0x7FFFFFFF;
			$value = $value % 1000000;

			if ( $value === $the_token ) {
				// Check for replay (man-in-the-middle) attack.
				if ( $last_time_slot >= ( $tm + $i ) ) {
					error_log( 'WordPress Essentials: Possible man-in-the-middle attack. Two login attempts were made on the same account within a 30-second period.' );

					return false;
				}

				// Return login time slot.
				return $tm + $i;
			}
		}

		return false;
	}

	/**
	 * [Helper function] Create secret.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function create_secret(): string {
		$chars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
		$secret = '';

		for ( $i = 0; $i < 16; $i ++ ) {
			$secret .= substr( $chars, wp_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		return $secret;
	}

	/**
	 * Login form.
	 *
	 * @since 1.0.0
	 */
	function login_form() {
		$title = esc_html__( 'Verification Code', 'essentials' );

		?>

		<p>
			<label for="user_token"><?php echo $title; ?></label>
			<input type="tel" name="tok" id="user_token" class="input" value="" size="20" placeholder="******" maxlength="6" autocapitalize="off" />
		</p>

		<?php

	}

	/**
	 * WooCommerce login form.
	 *
	 * @since 1.0.0
	 */
	function woocommerce_login_form() {
		$title = esc_html__( 'Verification Code', 'essentials' );

		?>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="user_token"><?php echo $title; ?></label>
			<input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="tok" id="user_token" placeholder="******" maxlength="6" />
		</p>

		<?php

	}

	/**
	 * Authenticate token.
	 *
	 * @param WP_Error|WP_User|null $user WP_User if the user is authenticated. WP_Error or null otherwise.
	 * @param string $username Username or email address.
	 *
	 * @return WP_Error|WP_User|null
	 *
	 * @since 1.0.0
	 */
	function authenticate_token( WP_Error|WP_User|null $user, string $username = '' ): WP_Error|WP_User|null {
		// Store latest result of login process.
		$user_state = $user;

		// Get information on user.
		$user_info = get_user_by( 'login', $username );

		// Invalid token error message.
		$error_message = new WP_Error(
			'authenticator_invalid_token',
			wp_kses(
				__(
					'<strong>Error</strong>: The username, email address, password, or verification code is incorrect.',
					'essentials'
				),
				array(
					'strong' => array(),
				)
			)
		);

		// Check if user has authenticator enabled.
		if ( isset( $user_info->ID ) && trim( get_user_option( 'authenticator_enabled', $user_info->ID ) ) == 'enabled' ) {
			// Get the users secret.
			$authenticator_secret = trim( get_user_option( 'authenticator_secret', $user_info->ID ) );

			// Get the token entered by the user trying to log in.
			$token = trim( $_POST[ 'tok' ] );

			// When was the last successful login performed.
			$last_time_slot = trim( get_user_option( 'authenticator_last_time_slot', $user_info->ID ) );

			// Check if token is correct.
			if ( $time_slot = $this->verify_token( $authenticator_secret, $token, $last_time_slot ) ) {
				update_user_option( $user_info->ID, 'authenticator_last_time_slot', $time_slot, true );

				return $user_state;
			} else {
				return $error_message;
			}
		} else {
			// If user has authenticator disabled and "tok" is not empty.
			if ( ! empty( $_POST[ 'tok' ] ) ) {
				return $error_message;
			} else {
				return $user_state;
			}
		}
	}

	/**
	 * Refresh secret.
	 *
	 * @since 1.0.0
	 */
	function refresh_secret() {
		// Some AJAX security.
		check_ajax_referer( 'authenticator', 'nonce' );

		// Prepare secret response.
		$secret   = $this->create_secret();
		$response = array( 'secret' => $secret );

		// Response.
		header( 'Content-Type: application/json' );
		echo json_encode( $response );

		// Required to return a proper result.
		die();
	}

	/**
	 * "Profile" options.
	 *
	 * @since 1.0.0
	 */
	function profile_options() {
		global $user_id;

		$authenticator_user_info = get_userdata( $user_id );
		$authenticator_enabled   = trim( get_user_option( 'authenticator_enabled', $user_id ) );
		$authenticator_secret    = trim( get_user_option( 'authenticator_secret', $user_id ) );

		// Generate a secret if empty.
		if ( $authenticator_secret == '' ) {
			$authenticator_secret = $this->create_secret();
		}

		wp_enqueue_style(
			'essentials-authenticator',
			plugins_url(
				'style.css',
				__FILE__
			),
			array(),
			ESSENTIALS_VERSION
		);

		?>

		<h3><?php esc_html_e( 'Two-factor authentication', 'essentials' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable', 'essentials' ); ?></th>
				<td>
					<label for="authenticator_enabled">
						<input name="authenticator_enabled" type="checkbox" id="authenticator_enabled" <?php checked( $authenticator_enabled, 'enabled', true ); ?> />
						<?php esc_html_e( 'Enable two-factor authentication', 'essentials' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="authenticator_qr_info"><?php esc_html_e( 'Your QR Code', 'essentials' ); ?></label>
				</th>
				<td>
					<div id="authenticator_qr_info">
						<div id="authenticator_qr_code"></div>
					</div>
					<input type="hidden" name="authenticator_secret" id="authenticator_secret" value="<?php echo $authenticator_secret; ?>" class="regular-text" />
					<p class="description">
						<?php
						echo wp_kses(
							__(
								'Use the <a href="https://support.google.com/accounts/answer/1066447" target="_blank">Google Authenticator</a> app to scan code.',
								'essentials'
							),
							array(
								'a' => array(
									'href'   => array(),
									'target' => array(),
								),
							)
						);
						?>
						<?php
						echo sprintf(
							'<a href="javascript:void(0);" id="authenticator_refresh" title="%1$s">%1$s</a>',
							esc_html_x(
								'Refresh code',
								'authenticator',
								'essentials'
							)
						);
						?>
					</p>
				</td>
			</tr>
			<script type="text/javascript">
              const authenticator_nonce = '<?php echo wp_create_nonce( 'authenticator' ); ?>';

              jQuery(function ($) {
                const site_name = '<?php echo urlencode( ( is_multisite() ? get_site_option( 'site_name' ) : get_option( 'blogname' ) ) ); ?>';
                const authenticator_qr_code = $('#authenticator_qr_code');
                const authenticator_secret = $('#authenticator_secret');

                // Generate QR code with existing secret.
                const qrcode = "otpauth://totp/<?php echo $authenticator_user_info->user_email; ?>" + "?secret=" + authenticator_secret.val() + "&issuer=" + site_name;
                const link = "<?php echo ESSENTIALS_URL . 'libraries/php-qrcode/index.php'; ?>?text=" + encodeURIComponent(qrcode) + "&size=25&margin=0";

                authenticator_qr_code.html("<img src=\"" + link + "\" alt=\"<?php esc_attr_e( 'Your QR Code', 'essentials' ) ?>\" />");

                // Generates a new QR Code.
                $('#authenticator_refresh').on('click', function () {
                  let data = {
                    'action': 'authenticator',
                    'nonce': authenticator_nonce,
                  };

                  $.post(ajaxurl, data, function (response) {
                    const secret = response['secret'];
                    const qrcode = "otpauth://totp/<?php echo $authenticator_user_info->user_email; ?>" + "?secret=" + secret + "&issuer=" + site_name;
                    const link = "<?php echo ESSENTIALS_URL . 'libraries/php-qrcode/index.php'; ?>?text=" + encodeURIComponent(qrcode) + "&size=25&margin=0";

                    authenticator_qr_code.html("<img src=\"" + link + "\" alt=\"<?php esc_attr_e( 'Your QR Code', 'essentials' ) ?>\" />");
                    authenticator_secret.val(secret);
                  });
                });
              });
			</script>
		</table>

		<?php

		submit_button( esc_html__( 'Update Profile', 'essentials' ), 'primary', 'submit-authenticator', true );
	}

	/**
	 * Update "Profile" options.
	 *
	 * @since 1.0.0
	 */
	function profile_options_update() {
		global $user_id;

		$authenticator_enabled = empty( $_POST[ 'authenticator_enabled' ] ) ? 'disabled' : 'enabled';
		$authenticator_secret  = trim( $_POST[ 'authenticator_secret' ] );

		// Update checkbox settings.
		update_user_option( $user_id, 'authenticator_enabled', $authenticator_enabled, true );

		// Update two-factor settings.
		if ( $authenticator_enabled == 'disabled' ) {
			update_user_option( $user_id, 'authenticator_secret', '', true );
		} else {
			update_user_option( $user_id, 'authenticator_secret', $authenticator_secret, true );
		}
	}

	/**
	 * "Edit user profile" options.
	 *
	 * @since 1.0.0
	 */
	function edit_user_options() {
		global $user_id;

		$authenticator_enabled = trim( get_user_option( 'authenticator_enabled', $user_id ) );

		?>

		<h3><?php esc_html_e( 'Two-factor authentication', 'essentials' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Enable', 'essentials' ); ?></th>
				<td>
					<label for="authenticator_enabled">
						<input name="authenticator_enabled" type="checkbox" id="authenticator_enabled" <?php checked( $authenticator_enabled, 'enabled', true ); ?> />
						<?php esc_html_e( 'Enable two-factor authentication', 'essentials' ); ?>
					</label>
				</td>
			</tr>
		</table>

		<?php

	}

	/**
	 * Update "Edit user profile" options.
	 *
	 * @since 1.0.0
	 */
	function edit_user_options_update() {
		global $user_id;

		$authenticator_enabled = empty( $_POST[ 'authenticator_enabled' ] ) ? 'disabled' : 'enabled';

		// Checkbox settings.
		update_user_option( $user_id, 'authenticator_enabled', $authenticator_enabled, true );

		// When disabled, clear secret key.
		if ( $authenticator_enabled == 'disabled' ) {
			update_user_option( $user_id, 'authenticator_secret', '', true );
		}
	}

	/**
	 * Users column.
	 *
	 * @param array $columns An array of column name => label.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function users_column( array $columns ): array {
		$columns[ 'authenticator' ] = esc_html__( 'Two-factor authentication', 'essentials' );

		return $columns;
	}

	/**
	 * Users column information.
	 *
	 * @param string $output Custom column output. Default empty.
	 * @param string $column_name Column name.
	 * @param int $user_id ID of the currently-listed user.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function users_column_information( string $output, string $column_name, int $user_id ): string {
		if ( 'authenticator' == $column_name ) {
			$output                = esc_html__( 'Not available', 'essentials' );
			$authenticator_enabled = get_user_meta( $user_id, 'authenticator_enabled', true );

			if ( 'enabled' === $authenticator_enabled ) {
				$output = '<span style="color: #39b54a;">' . esc_html__( 'Active', 'essentials' ) . '</span>';
			} elseif ( 'disabled' === $authenticator_enabled ) {
				$output = '<span style="color: #d72b3f;">' . esc_html__( 'Inactive', 'essentials' ) . '</span>';
			}
		}

		return $output;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Authenticator();
