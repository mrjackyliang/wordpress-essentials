<?php
namespace Essentials\Features\WooCommerce\Gateways\GiltCity;

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
		$this->token       = 'gilt_city';
		$this->plugin_url  = plugin_dir_url( __FILE__ );
		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->version     = ESSENTIALS_VERSION;

		$this->id                 = 'gilt_city';
		$this->method_title       = esc_html_x( 'Gilt City', 'brand', 'essentials' );
		$this->method_description = sprintf(
			esc_html__(
				'%1$s allows customers to redeem vouchers purchased through the %1$s website.',
				'woocommerce'
			),
			esc_html_x(
				'Gilt City',
				'brand',
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
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'display_order_information' ) );
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
					esc_html_x(
						'Gilt City',
						'brand',
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
				'default'     => esc_html_x(
					'Gilt City',
					'brand',
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
						'Pay using %s vouchers; after placing your order, we will redeem your voucher manually and confirm your order.',
						'essentials'
					),
					esc_html_x(
						'Gilt City',
						'brand',
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
				'default'     => sprintf(
					esc_html__(
						'We will verify your order details and redeem your %s voucher. If there are any order issues, we will attempt to contact you.',
						'essentials'
					),
					esc_html_x(
						'Gilt City',
						'brand',
						'essentials'
					)
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

		?>

		<fieldset>
			<div class="form-row form-row-first">
				<label for="gilt_city_voucher">
					<?php _e( 'Voucher', 'essentials' ); ?>
					<span class="required">*</span>
				</label>
				<input type="text"
					   class="input-text"
					   id="gilt_city_voucher"
					   name="gilt_city_voucher"
					   maxlength="10"
					   placeholder="A1B2C3D4E5"
				/>
			</div>
			<div class="form-row form-row-last">
				<label for="gilt_city_pin">
					<?php _e( 'PIN', 'essentials' ); ?>
					<span class="required">*</span>
				</label>
				<input type="password"
					   class="input-text"
					   id="gilt_city_pin"
					   name="gilt_city_pin"
					   maxlength="4"
					   placeholder="1234"
				/>
			</div>
			<div class="clear"></div>
		</fieldset>

		<?php

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

		$gilt_city_voucher = $this->get_post( 'gilt_city_voucher' );
		$gilt_city_pin     = $this->get_post( 'gilt_city_pin' );

		if ( isset( $gilt_city_voucher ) && isset( $gilt_city_pin ) ) {
			update_post_meta( $order_id, '_gilt_city_voucher', esc_attr( $gilt_city_voucher ) );
			update_post_meta( $order_id, '_gilt_city_pin', esc_attr( $gilt_city_pin ) );
		}

		$order->update_status( 'on-hold', esc_html__( 'Awaiting voucher redemption', 'essentials' ) );

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
	 * Display order information.
	 *
	 * @param WC_Order $order Order data array.
	 *
	 * @since 1.0.0
	 */
	public function display_order_information( $order ) {
		if ( $order->get_payment_method() === 'gilt_city' ) {
			$output            = '';
			$title             = esc_html_x( 'Gilt City', 'brand', 'essentials' );
			$gilt_city_voucher = get_post_meta( $order->get_order_number(), '_gilt_city_voucher', true );
			$gilt_city_pin     = get_post_meta( $order->get_order_number(), '_gilt_city_pin', true );

			if ( $gilt_city_voucher != '' && $gilt_city_pin != '' ) {
				$output .= '<p class="form-field form-field-wide">';
				$output .= '<label>' . $title . ': </label>';
				$output .= '<span style="color: #333; font-size: 20px;">' . $gilt_city_voucher . ' (#' . $gilt_city_pin . ')</span>';
				$output .= '</p>';
			}

			echo $output;
		}
	}

	/**
	 * Retrieve post value.
	 *
	 * @param string $name The post value.
	 *
	 * @return string|null
	 *
	 * @since 1.0.0
	 */
	private function get_post( $name ) {
		if ( isset( $_POST[ $name ] ) ) {
			return $_POST[ $name ];
		}

		return null;
	}

	/**
	 * Validate fields.
	 *
	 * @since 1.0.0
	 */
	public function validate_fields() {
		$gilt_city_voucher = $this->get_post( 'gilt_city_voucher' );
		$gilt_city_pin     = $this->get_post( 'gilt_city_pin' );

		// Verifies the voucher format.
		if ( ! preg_match( '/^[A-Z0-9]{6,10}$/', $gilt_city_voucher ) ) {
			wc_add_notice(
				sprintf(
					esc_html__(
						'Invalid %s voucher. Please re-enter your voucher again.',
						'essentials'
					),
					esc_html_x(
						'Gilt City',
						'brand',
						'essentials'
					)
				),
				$notice_type = 'error'
			);
		}

		// Verifies the PIN format.
		if ( ! preg_match( '/^[0-9]{4}$/', $gilt_city_pin ) ) {
			wc_add_notice(
				sprintf(
					esc_html__(
						'Invalid %s voucher PIN. Please re-enter your voucher PIN again.',
						'essentials'
					),
					esc_html_x(
						'Gilt City',
						'brand',
						'essentials'
					)
				),
				$notice_type = 'error'
			);
		}

		// If fields are filled.
		if (
			preg_match( '/^[A-Z0-9]{6,10}$/', $gilt_city_voucher ) &&
			preg_match( '/^[0-9]{4}$/', $gilt_city_pin )
		) {
			return true;
		}

		return false;
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
