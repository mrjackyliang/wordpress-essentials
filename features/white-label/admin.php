<?php
namespace Essentials\Features\WhiteLabel;

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
		// Custom admin title.
		add_filter( 'admin_title', array( $this, 'custom_admin_title' ), 10, 2 );

		// Remove help tabs.
		add_filter( 'admin_head', array( $this, 'remove_help_tabs' ), 1000, 3 );

		// Random inspiration quotes.
		add_filter( 'update_right_now_text', array( $this, 'random_inspiration_quotes' ) );

		// Remove welcome panel.
		add_action( 'admin_init', array( $this, 'remove_welcome_panel' ) );

		// Remove "WordPress Events and News" meta box.
		add_action( 'wp_dashboard_setup', array( $this, 'remove_events_meta_box' ) );

		// Remove meta widget.
		add_action( 'widgets_init', array( $this, 'remove_meta_widget' ) );

		// Footer text left side.
		add_filter( 'admin_footer_text', array( $this, 'footer_text_left' ) );

		// Footer text right side.
		add_filter( 'update_footer', array( $this, 'footer_text_right' ), 20 );
	}

	/**
	 * Custom admin title.
	 *
	 * @param string $admin_title The page title, with extra context added.
	 * @param string $title The original page title.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function custom_admin_title( string $admin_title, string $title ): string {
		if ( __return_false() ) {
			return $admin_title;
		}

		if ( is_network_admin() ) {
			return get_bloginfo( 'name' ) . ' - ' . esc_html__( 'Network Admin: ', 'essentials' ) . $title;
		}

		return get_bloginfo( 'name' ) . ' - ' . $title;
	}

	/**
	 * Remove help tabs.
	 *
	 * @since 1.0.0
	 */
	function remove_help_tabs() {
		$screen = get_current_screen();
		$screen->remove_help_tabs();
	}

	/**
	 * Random inspiration quotes.
	 *
	 * @since 1.0.0
	 */
	function random_inspiration_quotes(): string {
		$quotes = [
			'Change the world by being yourself. – <strong>Amy Poehler</strong>',
			'Every moment is a fresh beginning. – <strong>T.S Eliot</strong>',
			'Never regret anything that made you smile. – <strong>Mark Twain</strong>',
			'Everything you can imagine is real. – <strong>Pablo Picasso</strong>',
			'Simplicity is the ultimate sophistication. – <strong>Leonardo da Vinci</strong>',
			'Whatever you do, do it well. – <strong>Walt Disney</strong>',
			'What we think, we become. – <strong>Buddha</strong>',
			'All limitations are self-imposed. – <strong>Oliver Wendell Holmes</strong>',
			'Tough times never last but tough people do. – <strong>Robert H. Schiuller</strong>',
			'Problems are not stop signs, they are guidelines. – <strong>Robert H. Schiuller</strong>'
		];

		return $quotes[ array_rand( $quotes ) ];
	}

	/**
	 * Remove welcome panel.
	 *
	 * @since 1.0.0
	 */
	function remove_welcome_panel() {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	/**
	 * Remove "WordPress Events and News" meta box.
	 *
	 * @since 1.0.0
	 */
	function remove_events_meta_box() {
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	}

	/**
	 * Remove meta widget.
	 *
	 * @since 1.0.0
	 */
	function remove_meta_widget() {
		unregister_widget( 'WP_Widget_Meta' );
	}

	/**
	 * Footer text left side.
	 *
	 * @since 1.0.0
	 */
	function footer_text_left(): string {
		$style = 'font-style: normal;';
		$text  = sprintf(
			esc_html__(
				'Copyright &copy; %1$s My Company LLC. All Rights Reserved.',
				'essentials'
			),
			date( 'Y' )
		);

		return '<span id="footer-thankyou" style="' . $style . '">' . $text . '</span>';
	}

	/**
	 * Footer text right side.
	 *
	 * @since 1.0.0
	 */
	function footer_text_right(): string {
		$style = 'font-style: normal;';
		$text  = esc_html__(
			'Since 2020',
			'essentials'
		);

		return '<span style="' . $style . '">' . $text . '</span>';
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Admin();
