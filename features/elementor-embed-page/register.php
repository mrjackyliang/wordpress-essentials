<?php
namespace Essentials\Features\Shortcodes\EmbedPageContent;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Embed page content.
 *
 * @since 1.0.0
 */
class Register {
	/**
	 * Class instance.
	 *
	 * @since 1.0.0
	 */
	static Register $instance;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		self::$instance = $this;

		// Add shortcode.
		add_shortcode( 'embed_page_content', array( $this, 'shortcode' ) );
	}

	/**
	 * Shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @param string $content Shortcode content (if any).
	 * @param string $shortcode_tag Name of the shortcode.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function shortcode( array $atts, string $content, string $shortcode_tag ): string {
		$attributes = shortcode_atts(
			array(
				'path' => '',
			),
			$atts,
			$shortcode_tag
		);

		if ( esc_attr( $attributes[ 'path' ] ) ) {
			$post = get_page_by_path( $attributes[ 'path' ] );

			if ( $post ) {
				$content = apply_filters( 'the_content', $post->post_content );
			} else {
				$content = esc_html__( 'Cannot embed page content. The path is invalid.', 'essentials' );
			}
		}

		return $content;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Register();
