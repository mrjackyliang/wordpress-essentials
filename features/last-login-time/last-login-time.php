<?php
namespace Essentials\Features\LastLoginTime;

use DateTime;
use DateTimeZone;
use Exception;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Last login time.
 *
 * @since 1.0.0
 */
class LastLoginTime {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Capture time on login.
		add_action( 'wp_login', array( $this, 'capture_time' ), 10, 2 );

		// Users column.
		add_filter( 'manage_users_columns', array( $this, 'users_column' ) );
		if ( is_multisite() ) {
			add_filter( 'wpmu_users_columns', array( $this, 'users_column' ) );
		}

		// Users column information.
		add_action( 'manage_users_custom_column', array( $this, 'users_column_information' ), 10, 3 );
	}

	/**
	 * Capture time on login.
	 *
	 * @param string $user_login Username.
	 * @param WP_User $user WP_User object of the logged-in user.
	 *
	 * @since 1.0.0
	 */
	function capture_time( string $user_login, WP_User $user ) {
		$user_id    = $user->ID;
		$user_email = $user->user_email;

		error_log( 'WordPress Essentials: ' . $user_login . ' (id: ' . $user_id . ', email: ' . $user_email . ') logged in at ' . current_time( 'c', true ) );

		update_user_meta( $user_id, 'last_login_time', current_time( 'mysql', true ) );
	}

	/**
	 * Users column.
	 *
	 * @param array $columns An array of user columns.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function users_column( array $columns ): array {
		$columns[ 'last_login' ] = esc_html__( 'Last login', 'essentials' );

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
	 * @throws Exception
	 *
	 * @since 1.0.0
	 */
	function users_column_information( string $output, string $column_name, int $user_id ): string {
		if ( 'last_login' == $column_name ) {
			$output          = esc_html__( 'Never logged in', 'essentials' );
			$last_login_time = get_user_meta( $user_id, 'last_login_time', true );

			// If last login time exists.
			if ( ! empty( $last_login_time ) ) {
				$date_time_gmt   = new DateTime( $last_login_time, new DateTimeZone( 'GMT' ) );
				$date_time_local = $date_time_gmt->setTimezone( wp_timezone() );

				$output = $date_time_local->format( 'F j, Y g:i A T' );
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
new LastLoginTime();
