<?php
namespace Essentials\Features\ElementorMyAccount;

use Elementor\Elements_Manager;
use Elementor\Widgets_Manager;
use Essentials\Features\ElementorMyAccount\Widget as Widget;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor My Account.
 *
 * @since 1.0.0
 */
class ElementorMyAccount {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Register add-on.
		add_action( 'elementor/widgets/register', array( $this, 'register_add_on' ) );

		// Register category.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
	}

	/**
	 * Register add-on.
	 *
	 * @param Widgets_Manager $widgets_manager The widgets manager.
	 *
	 * @since 1.0.0
	 */
	function register_add_on( Widgets_Manager $widgets_manager ) {
		require_once( 'widget.php' );

		$widgets_manager->register( new Widget() );
	}

	/**
	 * Register category.
	 *
	 * @param Elements_Manager $elements_manager Elements manager instance.
	 *
	 * @since 1.0.0
	 */
	function register_category( Elements_Manager $elements_manager ) {
		$is_registered = array_key_exists( 'essentials', $elements_manager->get_categories() );

		if ( ! $is_registered ) {
			$elements_manager->add_category(
				'essentials',
				[
					'title' => esc_html_x( 'Essentials', 'brand', 'essentials' ),
					'icon'  => 'eicon-flash',
				]
			);
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new ElementorMyAccount();
