<?php
namespace Essentials\Features\ElementorMyAccount;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Widget.
 *
 * @since 1.0.0
 */
class Widget extends Widget_Base {
	/**
	 * Get name.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function get_name(): string {
		return 'my-account';
	}

	/**
	 * Get title.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function get_title(): string {
		return esc_html__( 'My Account', 'essentials' );
	}

	/**
	 * Get icon.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function get_icon(): string {
		return 'eicon-my-account';
	}

	/**
	 * Get custom help url.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function get_custom_help_url(): string {
		return 'https://github.com/mrjackyliang/wordpress-essentials/issues/new/choose';
	}

	/**
	 * Get categories.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function get_categories(): array {
		return [ 'essentials' ];
	}

	/**
	 * Get keywords.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function get_keywords(): array {
		return [ 'account', 'login', 'memberful', 'essentials' ];
	}

	/**
	 * Get style depends.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function get_style_depends(): array {
		wp_register_style(
			'essentials-elementor-my-account',
			plugins_url(
				'style.css',
				__FILE__
			)
		);

		return [
			'essentials-elementor-my-account'
		];
	}

	/**
	 * Register controls.
	 *
	 * @since 1.0.0
	 */
	function register_controls() {
		$nav_menus       = wp_get_nav_menus();
		$available_menus = [
			'' => '— ' . esc_html__( 'Select', 'essentials' ) . ' —',
		];

		// Pre-fill nav menus.
		foreach ( $nav_menus as $nav_menu ) {
			$available_menus[ $nav_menu->name ] = $nav_menu->name;
		}

		// General.
		$this->start_controls_section(
			'general',
			[
				'tab'   => Controls_Manager::TAB_CONTENT,
				'label' => esc_html__( 'General', 'essentials' ),
			]
		);

		// General ➜ Default Label.
		$this->add_control(
			'general_default_label',
			[
				'type'        => Controls_Manager::TEXT,
				'label'       => esc_html__( 'Default Label', 'essentials' ),
				'placeholder' => esc_html__( 'My Account', 'essentials' ),
			]
		);

		// General ➜ Account Label Max Length.
		$this->add_control(
			'general_label_max_length',
			[
				'type'  => Controls_Manager::NUMBER,
				'label' => esc_html__( 'Label Max Length', 'essentials' ),
				'min' => '2',
				'max' => '100',
				'step' => '1'
			]
		);

		// Avatar ➜ Divider.
		$this->add_control(
			'general_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		// General ➜ Logged-In Menus ➜ Repeater.
		$general_logged_in_menus_repeater = new Repeater();

		// General ➜ Logged-In Menus ➜ Repeater ➜ Display Title.
		$general_logged_in_menus_repeater->add_control(
			'display_title',
			[
				'type'  => Controls_Manager::TEXT,
				'label' => esc_html__( 'Display Title', 'essentials' ),
			],
		);

		// General ➜ Logged-In Menus ➜ Repeater ➜ Menu.
		$general_logged_in_menus_repeater->add_control(
			'menu',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Menu', 'essentials' ),
				'options' => $available_menus,
			]
		);

		// General ➜ Logged-In Menus.
		$this->add_control(
			'general_logged_in_menus',
			[
				'type'        => Controls_Manager::REPEATER,
				'label'       => esc_html__( 'Logged-In Menus', 'essentials' ),
				'fields'      => $general_logged_in_menus_repeater->get_controls(),
				'default'     => [
					[
						'display_title' => '',
						'menu'          => ''
					]
				],
				'title_field' => '{{{ display_title || menu }}}'
			]
		);

		// General.
		$this->end_controls_section();

		// Only if Memberful plugin is active.
		if ( ESSENTIALS_MEMBERFUL_ACTIVE ) {
			// Memberful.
			$this->start_controls_section(
				'memberful',
				[
					'tab'   => Controls_Manager::TAB_CONTENT,
					'label' => esc_html_x( 'Memberful', 'brand', 'essentials' ),
				]
			);

			// Memberful ➜ Login and Logout via Memberful.
			$this->add_control(
				'memberful_login_logout',
				[
					'type'         => Controls_Manager::SWITCHER,
					'label'        => esc_html__( 'Login and Logout via Memberful', 'essentials' ),
					'label_on'     => esc_html__( 'On', 'essentials' ),
					'label_off'    => esc_html__( 'Off', 'essentials' ),
					'return_value' => 'on',
				]
			);

			// Memberful ➜ Show Register Link.
			$this->add_control(
				'memberful_register_link',
				[
					'type'         => Controls_Manager::SWITCHER,
					'label'        => esc_html__( 'Show Register Link', 'essentials' ),
					'label_on'     => esc_html__( 'Show', 'essentials' ),
					'label_off'    => esc_html__( 'Hide', 'essentials' ),
					'return_value' => 'show'
				]
			);

			// Memberful ➜ Show Discord Authorize Link.
			$this->add_control(
				'memberful_discord_authorize_link',
				[
					'type'         => Controls_Manager::SWITCHER,
					'label'        => esc_html__( 'Show Discord Authorize Link', 'essentials' ),
					'label_on'     => esc_html__( 'Show', 'essentials' ),
					'label_off'    => esc_html__( 'Hide', 'essentials' ),
					'return_value' => 'show'
				]
			);

			// Memberful.
			$this->end_controls_section();
		}

		// Display.
		$this->start_controls_section(
			'display',
			[
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Display', 'essentials' ),
			]
		);

		// Display ➜ Theme.
		$this->add_control(
			'display_theme',
			[
				'type'    => Controls_Manager::SELECT,
				'label'   => esc_html__( 'Theme', 'essentials' ),
				'options' => [
					'light' => esc_html__( 'Light', 'essentials' ),
					'dark'  => esc_html__( 'Dark', 'essentials' ),
				],
			]
		);

		// Display ➜ Typography.
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'display_typography',
				'selector' => '{{WRAPPER}} .my-account-widget',
			]
		);

		// Display ➜ Position.
		$this->add_control(
			'display_position',
			[
				'type'    => Controls_Manager::CHOOSE,
				'label'   => esc_html__( 'Position', 'essentials' ),
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'essentials' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'essentials' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'essentials' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		// Display.
		$this->end_controls_section();

		// Avatar.
		$this->start_controls_section(
			'avatar',
			[
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => esc_html__( 'Avatar', 'essentials' ),
			]
		);

		// Avatar ➜ Border.
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'avatar_border',
				'selector' => '{{WRAPPER}} .my-account-widget .my-account-profile-avatar img',
			]
		);

		// Avatar ➜ Border Radius.
		$this->add_responsive_control(
			'avatar_border_radius',
			[
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => esc_html__( 'Border Radius', 'essentials' ),
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .my-account-widget .my-account-profile-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Avatar ➜ Box Shadow.
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'avatar_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'essentials' ),
				'selector' => '{{WRAPPER}} .my-account-profile-avatar img',
			]
		);

		// Avatar ➜ Divider.
		$this->add_control(
			'avatar_divider',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		// Avatar ➜ Fallback Avatar.
		$this->add_control(
			'avatar_fallback_avatar',
			[
				'label' => esc_html__( 'Fallback Avatar', 'essentials' ),
				'type'  => Controls_Manager::MEDIA,
			]
		);

		// Avatar.
		$this->end_controls_section();
	}

	/**
	 * Render.
	 *
	 * @since 1.0.0
	 */
	function render() {
		$settings                         = $this->get_settings_for_display();
		$general_default_label            = ( ! empty( $settings[ 'general_default_label' ] ) ) ? $settings[ 'general_default_label' ] : esc_html__( 'My Account', 'essentials' );
		$general_label_max_length         = ( ! empty( $settings[ 'general_label_max_length' ] ) ) ? $settings[ 'general_label_max_length' ] : 10;
		$general_logged_in_menus          = ( ! empty( $settings[ 'general_logged_in_menus' ] ) ) ? $settings[ 'general_logged_in_menus' ] : [];
		$memberful_login_logout           = ( ! empty( $settings[ 'memberful_login_logout' ] ) ) ? $settings[ 'memberful_login_logout' ] : 'off';
		$memberful_register_link          = ( ! empty( $settings[ 'memberful_register_link' ] ) ) ? $settings[ 'memberful_register_link' ] : 'hide';
		$memberful_discord_authorize_link = ( ! empty( $settings[ 'memberful_discord_authorize_link' ] ) ) ? $settings[ 'memberful_discord_authorize_link' ] : 'hide';
		$display_theme                    = ( ! empty( $settings[ 'display_theme' ] ) ) ? $settings[ 'display_theme' ] : 'light';
		$display_position                 = ( ! empty( $settings[ 'display_position' ] ) ) ? $settings[ 'display_position' ] : 'left';
		$avatar_fallback_avatar_url       = ( ! empty( $settings[ 'avatar_fallback_avatar' ][ 'url' ] ) ) ? $settings[ 'avatar_fallback_avatar' ][ 'url' ] : '';

		?>

		<div id="my-account" class="my-account my-account-widget my-account-theme-<?php echo $display_theme; ?> my-account-position-<?php echo $display_position; ?>">
			<a href="javascript:void(0);" class="my-account-profile">
				<div class="my-account-profile-name">
					<span><?php
						if ( is_user_logged_in() ) {
							echo mb_strimwidth( wp_trim_words( wp_get_current_user()->display_name, 1, '' ), 0, $general_label_max_length, '…' );
						} else {
							echo mb_strimwidth( $general_default_label, 0, $general_label_max_length, '…' );
						}
						?></span>
				</div>
				<div class="my-account-profile-avatar">
					<?php
					if ( ! empty( $avatar_fallback_avatar_url ) ) {
						echo get_avatar( get_current_user_id(), 90, $avatar_fallback_avatar_url );
					} else {
						echo get_avatar( get_current_user_id(), 90, 'mystery' );
					}
					?>
				</div>
			</a>
			<div class="my-account-content">
				<div class="my-account-content-inner">
					<?php if ( is_user_logged_in() ) : ?>
						<?php if ( ! empty( $general_logged_in_menus ) ) : ?>
							<div class="my-account-menus">
								<?php foreach ( $general_logged_in_menus as $general_logged_in_menu ) : ?>
									<?php if ( ! empty( $general_logged_in_menu[ 'menu' ] ) ) : ?>
										<div class="my-account-menu">
											<?php if ( ! empty( $general_logged_in_menu[ 'display_title' ] ) ) : ?>
												<h2 class="my-account-menu-title">
													<?php echo $general_logged_in_menu[ 'display_title' ]; ?>
												</h2>
											<?php endif; ?>
											<?php wp_nav_menu(
												array(
													'menu'            => $general_logged_in_menu[ 'menu' ],
													'container'       => 'nav',
													'container_class' => 'my-account-menu-navigation',
													'fallback_cb'     => false,
													'depth'           => 1,
												)
											); ?>
											<hr class="my-account-menu-separator" />
										</div>
									<?php endif; ?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<nav class="footer-links">
							<ul>
								<?php if ( ESSENTIALS_MEMBERFUL_ACTIVE && $memberful_discord_authorize_link === 'show' ) : ?>
									<li class="footer-link footer-link-discord">
										<a href="<?php echo memberful_url( 'account/discord/authorize' ); ?>" target="_blank" rel="noopener">
											<?php esc_attr_e( 'Authorize Discord', 'essentials' ); ?>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ESSENTIALS_MEMBERFUL_ACTIVE && $memberful_login_logout === 'on' ) : ?>
									<li class="footer-link footer-link-logout">
										<a href="<?php echo memberful_sign_out_url(); ?>">
											<?php esc_attr_e( 'Logout', 'essentials' ); ?>
										</a>
									</li>
								<?php else: ?>
									<li class="footer-link footer-link-logout">
										<a href="<?php echo wp_logout_url( get_permalink() ); ?>">
											<?php esc_attr_e( 'Logout', 'essentials' ); ?>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</nav>
					<?php else: ?>
						<nav class="footer-links">
							<ul>
								<?php if ( ESSENTIALS_MEMBERFUL_ACTIVE && $memberful_login_logout === 'on' ) : ?>
									<li class="footer-link footer-link-login">
										<a href="<?php echo memberful_sign_in_url(); ?>">
											<?php esc_attr_e( 'Login', 'essentials' ); ?>
										</a>
									</li>
								<?php else: ?>
									<li class="footer-link footer-link-login">
										<a href="<?php echo wp_login_url( get_permalink() ); ?>">
											<?php esc_attr_e( 'Login', 'essentials' ); ?>
										</a>
									</li>
								<?php endif; ?>
								<?php if ( ESSENTIALS_MEMBERFUL_ACTIVE && $memberful_register_link === 'show' ) : ?>
									<li class="footer-link footer-link-register">
										<a href="<?php echo memberful_registration_page_url(); ?>">
											<?php esc_attr_e( 'Register', 'essentials' ); ?>
										</a>
									</li>
								<?php elseif ( get_option( 'users_can_register' ) == 1 ) : ?>
									<li class="footer-link footer-link-register">
										<a href="<?php echo wp_registration_url(); ?>">
											<?php esc_attr_e( 'Register', 'essentials' ); ?>
										</a>
									</li>
								<?php endif; ?>
							</ul>
						</nav>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php

	}
}
