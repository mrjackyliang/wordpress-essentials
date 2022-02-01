<?php
namespace Essentials\Features\WooCommerce\Gateways\PayByPhone;

use WC_Payment_Gateway;
use WC_Order;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gateway class.
 *
 * @since 1.0.0
 */
final class Gateway extends WC_Payment_Gateway {
	/**
	 * The gateway token.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $token;

	/**
	 * The plugin url.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin path.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $plugin_path;

	/**
	 * The plugin version.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * User instructions.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	public $instructions;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->token       = 'pay_by_phone';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = ESSENTIALS_VERSION;

		$this->id                 = 'pay_by_phone';
		$this->method_title       = esc_html__( 'Pay by Phone', 'essentials' );
		$this->method_description = sprintf(
			esc_html__(
				'%s allows customers to discuss payment details over the phone.',
				'woocommerce'
			),
			esc_html__(
				'Pay by Phone',
				'essentials'
			)
		);

		$this->has_fields = true;

		$this->init_form_fields();
		$this->init_settings();

		$this->title        = $this->settings[ 'title' ];
		$this->description  = $this->settings[ 'description' ];
		$this->instructions = $this->settings[ 'instructions' ];

		$this->icon = $this->plugin_url . 'gateway-icon.png';

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'instructions' ) );
	}

	/**
	 * Initialize form fields.
	 *
	 * @since 1.0.0
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'      => array(
				'title'    => esc_html__(
					'Enable/Disable',
					'essentials'
				),
				'type'     => 'checkbox',
				'label'    => sprintf(
					'%1$s %2$s',
					esc_html__(
						'Enable this to accept',
						'essentials'
					),
					esc_html__(
						'Pay by Phone',
						'essentials'
					)
				),
				'default'  => 'no',
				'desc_tip' => true,
			),
			'title'        => array(
				'title'       => esc_html__(
					'Title',
					'essentials'
				),
				'type'        => 'text',
				'description' => esc_html__(
					'This controls the title which the user sees during checkout.',
					'essentials'
				),
				'default'     => esc_html__(
					'Pay by Phone',
					'essentials'
				),
				'desc_tip'    => true,
			),
			'description'  => array(
				'title'       => esc_html__(
					'Description',
					'essentials'
				),
				'type'        => 'textarea',
				'description' => esc_html__(
					'This controls the description which the user sees during checkout.',
					'essentials'
				),
				'default'     => sprintf(
					esc_html__(
						'%s; after placing your order, please contact customer service to discuss payment details over the phone.',
						'essentials'
					),
					esc_html__(
						'Pay by Phone',
						'essentials'
					)
				),
				'desc_tip'    => true,
			),
			'instructions' => array(
				'title'       => esc_html__(
					'Instructions',
					'essentials'
				),
				'type'        => 'textarea',
				'description' => esc_html__(
					'This controls the instructions after the order has been received.',
					'essentials'
				),
				'default'     => esc_html__(
					'To complete payment for your order, please contact customer service as soon as possible.',
					'essentials'
				),
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Register admin options.
	 *
	 * @since 1.0.0
	 */
	public function admin_options() {
		parent::admin_options();
	}

	/**
	 * Register payment fields.
	 *
	 * @since 1.0.0
	 */
	public function payment_fields() {
		if ( $this->description ) {
			echo wpautop( wptexturize( $this->description ) );
		}
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		$order->update_status( 'on-hold', esc_html__( 'Awaiting payment', 'essentials' ) );

		wc_reduce_stock_levels( $order_id );

		WC()->cart->empty_cart();

		// Return thank you redirect.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Display instructions.
	 *
	 * @since 1.0.0
	 */
	public function instructions() {
		echo $this->instructions != '' ? wpautop( $this->instructions ) : '';
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'essentials' ), ESSENTIALS_VERSION );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'essentials' ), ESSENTIALS_VERSION );
	}
}
