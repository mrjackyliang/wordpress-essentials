<?php
namespace Essentials\Features\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Single admin.
 *
 * @since 1.0.0
 */
class SingleAdmin {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Register menus.
		add_action( 'admin_menu', array( $this, 'register_menus' ) );

		// Register sections.
		add_action( 'admin_init', array( $this, 'register_sections' ) );

		// Register fields.
		add_action( 'admin_init', array( $this, 'register_fields' ) );

		// Sanitize options.
		add_filter( 'sanitize_option_essentials', array( $this, 'sanitize_options' ) );
	}

	/**
	 * [Helper function] Page.
	 *
	 * @since 1.0.0
	 */
	function page() {
		$title     = esc_html_x( 'WordPress Essentials', 'brand', 'essentials' );
		$admin_url = admin_url( 'options.php' );

		?>

		<div class="wrap">
			<h2><?php echo $title; ?></h2>
			<form method="post" action="<?php echo $admin_url; ?>">
				<?php settings_fields( 'essentials' ); ?>
				<table class="form-table">
					<?php do_settings_sections( 'essentials-general' ); ?>
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
			'essentials-general',
			'essentials-general'
		);
	}

	/**
	 * [Helper function] Page ➜ General ➜ Utilities.
	 *
	 * @since 1.0.0
	 */
	function page_general_utilities() {
		$essentials                 = get_option( 'essentials', [ 'remove_src_version' => 0, 'search_only_posts' => 0 ] );
		$title                      = esc_html__( 'Utilities', 'essentials' );
		$remove_src_version_checked = checked( $essentials[ 'remove_src_version' ], 1, false );
		$search_only_posts_checked  = checked( $essentials[ 'search_only_posts' ], 1, false );
		$remove_src_version_title   = esc_html__( 'Remove version number from website styles and scripts', 'essentials' );
		$search_only_posts_title    = esc_html__( 'Show only posts in search results', 'essentials' );

		?>

		<fieldset>
			<legend class="screen-reader-text"><?php echo $title; ?></legend>
			<label for="remove-src-version">
				<input type="checkbox" name="essentials[remove_src_version]" id="remove-src-version" value="1" <?php echo $remove_src_version_checked; ?>>
				<?php echo $remove_src_version_title; ?>
			</label>
			<br />
			<label for="search-only-posts">
				<input type="checkbox" name="essentials[search_only_posts]" id="search-only-posts" value="1" <?php echo $search_only_posts_checked; ?>>
				<?php echo $search_only_posts_title; ?>
			</label>
		</fieldset>

		<?php

	}

	/**
	 * Register settings.
	 *
	 * @since 1.0.0
	 */
	function register_settings() {
		register_setting(
			'essentials',
			'essentials'
		);
	}

	/**
	 * Register menus.
	 *
	 * @since 1.0.0
	 */
	function register_menus() {
		add_options_page(
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
			'manage_options',
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
			'essentials-general'
		);
	}

	/**
	 * Register fields.
	 *
	 * @since 1.0.0
	 */
	function register_fields() {
		// General ➜ Utilities.
		add_settings_field(
			'utilities',
			esc_html__(
				'Utilities',
				'essentials'
			),
			array( $this, 'page_general_utilities' ),
			'essentials-general',
			'essentials-general',
		);
	}

	/**
	 * Sanitize options.
	 *
	 * @param array $input Settings input.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function sanitize_options( array $input ): array {
		$valid_input[ 'remove_src_version' ] = sanitize_text_field( $input[ 'remove_src_version' ] );
		$valid_input[ 'search_only_posts' ]  = sanitize_text_field( $input[ 'search_only_posts' ] );

		return $valid_input;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new SingleAdmin();
