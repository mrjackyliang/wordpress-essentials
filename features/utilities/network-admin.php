<?php
namespace Essentials\Features\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Network admin.
 *
 * @since 1.0.0
 */
class NetworkAdmin {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		if ( is_multisite() ) {
			// Register settings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Register menus.
			add_action( 'network_admin_menu', array( $this, 'register_menus' ) );

			// Register sections.
			add_action( 'admin_init', array( $this, 'register_sections' ) );

			// Register fields.
			add_action( 'admin_init', array( $this, 'register_fields' ) );

			// Update settings.
			add_action( 'network_admin_edit_essentials', array( $this, 'update_settings' ) );
		}
	}

	/**
	 * [Helper function] Page.
	 *
	 * @since 1.0.0
	 */
	function page() {
		$title = esc_html_x( 'WordPress Essentials', 'brand', 'essentials' );

		?>

		<?php if ( isset( $_GET[ 'updated' ] ) ) : ?>
			<div id="message" class="updated notice is-dismissible">
				<p><?php esc_html_e( 'Settings saved.' ); ?></p>
			</div>
		<?php endif; ?>
		<div class="wrap">
			<h2><?php echo $title; ?></h2>
			<form method="post" action="<?php echo network_admin_url( 'edit.php?action=essentials' ) ?>">
				<?php settings_fields( 'essentials-network' ); ?>
				<table class="form-table">
					<?php do_settings_sections( 'essentials-network-general' ); ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>

		<?php

	}

	/**
	 * [Helper function] Page ➜ General.
	 *
	 * @since 1.0.0
	 */
	function page_general() {
		do_settings_fields(
			'essentials-network-general',
			'essentials-network-general'
		);
	}

	/**
	 * [Helper function] Page ➜ General ➜ Message.
	 *
	 * @since 1.0.0
	 */
	function page_general_message() {
		$essentials         = get_site_option( 'essentials_network', [ 'message' => '' ] );
		$essentials_message = esc_attr( $essentials[ 'message' ] );

		?>

		<input name="essentials-network[message]" type="text" id="message" aria-describedby="message-description" class="large-text" value="<?php echo $essentials_message; ?>" size="45" />
		<p class="description" id="message-description">
			<?php echo wp_kses(
				__(
					'Set a message for every site on this network. Only <code>a</code>, <code>em</code>, <code>i</code>, <code>b</code>, and <code>strong</code> tags are allowed.',
					'essentials'
				),
				array(
					'code' => array()
				)
			); ?>
		</p>

		<?php

	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 */
	function register_settings() {
		register_setting(
			'essentials-network',
			'essentials-network'
		);
	}

	/**
	 * Register menus.
	 *
	 * @since 1.0.0
	 */
	function register_menus() {
		add_submenu_page(
			'settings.php',
			esc_html_x(
				'WordPress Essentials',
				'brand',
				'essentials'
			),
			esc_html_x(
				'Essentials',
				'brand',
				'essentials'
			),
			'manage_network_options',
			'essentials',
			array( $this, 'page' )
		);
	}

	/**
	 * Register sections.
	 *
	 * @since 1.0.0
	 */
	function register_sections() {
		// General.
		add_settings_section(
			'general',
			esc_html__(
				'General',
				'essentials'
			),
			array( $this, 'page_general' ),
			'essentials-network-general'
		);
	}

	/**
	 * Register fields.
	 *
	 * @since 1.0.0
	 */
	function register_fields() {
		// General ➜ Message.
		add_settings_field(
			'message',
			esc_html__(
				'Network message',
				'essentials'
			),
			array( $this, 'page_general_message' ),
			'essentials-network-general',
			'essentials-network-general',
			array(
				'label_for' => 'message'
			)
		);
	}

	/**
	 * Update settings.
	 *
	 * @since 1.0.0
	 */
	function update_settings() {
		$message = $_POST[ 'essentials-network' ][ 'message' ];

		// "-options" suffix must be set or check will fail.
		check_admin_referer( 'essentials-network-options' );

		// Sanitize the input.
		$valid_input[ 'message' ] = stripslashes(
			wp_kses(
				$message,
				array(
					'a'      => array(
						'href' => array(),
					),
					'em'     => array(),
					'i'      => array(),
					'b'      => array(),
					'strong' => array(),
				)
			)
		);

		// Save the input.
		update_site_option( 'essentials_network', $valid_input );

		// Redirect back to settings page.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => 'essentials',
					'updated' => 'true',
				),
				network_admin_url( 'settings.php' )
			)
		);

		// Required to return a proper result.
		die();
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new NetworkAdmin();
