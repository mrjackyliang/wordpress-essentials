<?php
/*
Plugin Name: WordPress Essentials
Plugin URI: https://github.com/mrjackyliang/wordpress-essentials
Description: An all-in-one toolkit for WordPress websites containing various enhancements and features.
Version: 1.0.0
Author: Jacky Liang
Author URI: https://www.mrjackyliang.com/
Text-Domain: essentials
*/
namespace Essentials;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin.
 *
 * @since 1.0.0
 */
class Plugin {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Defines.
		define( 'ESSENTIALS_URL', plugin_dir_url( __FILE__ ) );
		define( 'ESSENTIALS_VERSION', '1.0.0' );

		// Conditions.
		define( 'ESSENTIALS_AKISMET_ACTIVE', is_plugin_active( 'akismet/akismet.php' ) );
		define( 'ESSENTIALS_ELEMENTOR_ACTIVE', is_plugin_active( 'elementor/elementor.php' ) );
		define( 'ESSENTIALS_GRAVITY_FORMS_ACTIVE', is_plugin_active( 'gravityforms/gravityforms.php' ) );
		define( 'ESSENTIALS_MEMBERFUL_ACTIVE', is_plugin_active( 'memberful-wp/memberful-wp.php' ) );
		define( 'ESSENTIALS_PRETTY_LINKS_ACTIVE', is_plugin_active( 'pretty-link/pretty-link.php' ) );
		define( 'ESSENTIALS_WOOCOMMERCE_ACTIVE', is_plugin_active( 'woocommerce/woocommerce.php' ) );
		define( 'ESSENTIALS_WPENGINE_ACTIVE', array_key_exists( 'mu-plugin.php', get_mu_plugins() ) );
		define( 'ESSENTIALS_YOAST_SEO_ACTIVE', is_plugin_active( 'wordpress-seo/wp-seo.php' ) );

		// Translate.
		$this->translate();

		// Enhancements.
		$this->enhancements();

		// Features.
		$this->features();
	}

	/**
	 * Translate.
	 *
	 * @since 1.0.0
	 */
	function translate() {
		add_action( 'plugins_loaded', function () {
			load_plugin_textdomain(
				'essentials',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		} );
	}

	/**
	 * Enhancements.
	 *
	 * @since 1.0.0
	 */
	function enhancements() {
		// Akismet.
		if ( ESSENTIALS_AKISMET_ACTIVE ) {
			include_once( 'enhancements/akismet/akismet.php' );
		}

		// Elementor.
		if ( ESSENTIALS_ELEMENTOR_ACTIVE ) {
			include_once( 'enhancements/elementor/elementor.php' ); // todo remove screen options selection
		}

		// Gravity Forms.
		if ( ESSENTIALS_GRAVITY_FORMS_ACTIVE ) {
			include_once( 'enhancements/gravity-forms/gravity-forms.php' );
		}

		// Memberful.
		if ( ESSENTIALS_MEMBERFUL_ACTIVE ) {
			include_once( 'enhancements/memberful/memberful.php' );
		}

		// Pretty Links.
		if ( ESSENTIALS_PRETTY_LINKS_ACTIVE ) {
			include_once( 'enhancements/pretty-links/pretty-links.php' ); // todo remove screen options selection
		}

		// WooCommerce.
		if ( ESSENTIALS_WOOCOMMERCE_ACTIVE ) {
			include_once( 'enhancements/woocommerce/woocommerce.php' );
		}

		// WordPress enhancements.
		include_once( 'enhancements/wordpress/admin.php' );
		include_once( 'enhancements/wordpress/feed.php' );
		include_once( 'enhancements/wordpress/login.php' );
		include_once( 'enhancements/wordpress/rest-api.php' );
		include_once( 'enhancements/wordpress/site.php' );

		// WP Engine.
		if ( ESSENTIALS_WPENGINE_ACTIVE ) {
			include_once( 'enhancements/wpengine/wpengine.php' );
		}

		// Yoast SEO.
		if ( ESSENTIALS_YOAST_SEO_ACTIVE ) {
			include_once( 'enhancements/yoast-seo/yoast-seo.php' );
		}
	}

	/**
	 * Features.
	 *
	 * @since 1.0.0
	 */
	function features() {
		// Admin design.
		include_once( 'features/admin-design/admin-design.php' ); // todo convert to dynamic styling

		// Authenticator.
		include_once( 'features/authenticator/authenticator.php' );

		// Comments anti-spam.
		include_once( 'features/comments-anti-spam/comments-anti-spam.php' );

		// Elementor.
		if ( ESSENTIALS_ELEMENTOR_ACTIVE ) {
			//include_once( 'features/elementor-embed-page/register.php' ); // todo convert to elementor
			//include_once( 'features/elementor-instagram-feed/register.php' ); // todo convert to elementor
			include_once( 'features/elementor-my-account/elementor-my-account.php' );
			//include_once( 'features/elementor-team-gallery/elementor-team-gallery.php' ); // todo create
			//include_once( 'features/elementor-twitter-feed/register.php' ); // todo convert to elementor
		}

		// Last login time.
		include_once( 'features/last-login-time/last-login-time.php' );

		// Login design.
		include_once( 'features/login-design/login-design.php' ); // todo convert to dynamic styling

		// Subscriber access.
		include_once( 'features/subscriber-access/subscriber-access.php' ); // todo work on settings configuration

		// Utilities.
		include_once( 'features/utilities/network-admin.php' ); // todo separate section outside of features
		include_once( 'features/utilities/network-functions.php' ); // todo separate section outside of features
		include_once( 'features/utilities/single-admin.php' ); // todo separate section outside of features
		include_once( 'features/utilities/single-functions.php' ); // todo separate section outside of features

		// White-label.
		include_once( 'features/white-label/admin.php' );
		include_once( 'features/white-label/admin-bar.php' );
		include_once( 'features/white-label/feed.php' );
		include_once( 'features/white-label/login.php' );
		include_once( 'features/white-label/mail.php' );
		include_once( 'features/white-label/site.php' );

		// WooCommerce.
		if ( ESSENTIALS_WOOCOMMERCE_ACTIVE ) {
			//include_once('features/woocommerce-gilt-city/register.php'); // todo update code
			//include_once('features/woocommerce-groupon/register.php'); // todo update code
			//include_once('features/woocommerce-livingsocial/register.php'); // todo update code
			//include_once('features/woocommerce-pay-by-phone/register.php'); // todo update code
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Plugin();
