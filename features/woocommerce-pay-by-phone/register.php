<?php
namespace Essentials\Features\WooCommerce\Gateways\PayByPhone;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Register gateway.
 *
 * @since 1.0.0
 */
class Register {
	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 */
	static $instance;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		self::$instance = $this;

		add_action( 'plugins_loaded', array( $this, 'initialize' ) );
	}

	/**
	 * If WooCommerce is enabled.
	 *
	 * @since 1.0.0
	 */
	function initialize() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			return;
		}

		add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
	}

	/**
	 * Register gateway.
	 *
	 * @param array $methods Payment methods.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function register_gateway( $methods ) {
		require_once( 'gateway.php' );

		// Register the gateway.
		$methods[] = __NAMESPACE__ . '\Gateway';

		return $methods;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Register();
