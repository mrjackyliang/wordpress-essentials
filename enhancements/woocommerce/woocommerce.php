<?php
namespace Essentials\Enhancements\WooCommerce;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce.
 *
 * @since 1.0.0
 */
class WooCommerce {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Remove custom fields from "shop_order" post types.
		add_action( 'admin_init', array( $this, 'remove_custom_fields' ) );

		// Remove suffix from Stripe payment request.
		add_filter( 'wc_stripe_payment_request_total_label_suffix', array( $this, 'remove_stripe_payment_request_suffix' ) );

		// Disable "My Account" redirect.
		add_action( 'init', array( $this, 'disable_my_account_redirect' ) );

		// Enable admin bar.
		add_action( 'plugins_loaded', array( $this, 'enable_admin_bar' ) );

		// Disable "Lost Password" redirect.
		add_action( 'init', array( $this, 'disable_lost_password_redirect' ) );
	}

	/**
	 * Remove custom fields from "shop_order" post types.
	 *
	 * @since 1.0.0
	 */
	function remove_custom_fields() {
		remove_meta_box( 'postcustom', 'shop_order', 'normal' );
	}

	/**
	 * Remove suffix from Stripe payment request.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function remove_stripe_payment_request_suffix(): string {
		return '';
	}

	/**
	 * Disable "My Account" redirect.
	 *
	 * @since 1.0.0
	 */
	function disable_my_account_redirect() {
		add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
	}

	/**
	 * Enable admin bar.
	 *
	 * @since 1.0.0
	 */
	function enable_admin_bar() {
		add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
	}

	/**
	 * Disable "Lost Password" redirect.
	 *
	 * @since 1.0.0
	 */
	function disable_lost_password_redirect() {
		remove_filter( 'lostpassword_url', 'wc_lostpassword_url' );
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new WooCommerce();
