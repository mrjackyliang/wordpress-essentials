<?php
namespace Essentials\Features\WhiteLabel;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Feed.
 *
 * @since 1.0.0
 */
class Feed {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Change "generator" tag.
		add_filter( 'the_generator', array( $this, 'change_xhtml_generator' ) );

		// Disable text auto-conversion to emoji.
		add_filter( 'option_use_smilies', array($this, 'disable_auto_convert_emoji') );
	}

	/**
	 * Change "generator" tag.
	 *
	 * @since 1.0.0
	 */
	function change_xhtml_generator(): string {
		return '';
	}

	/**
	 * Disable text auto-conversion to emoji.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	function disable_auto_convert_emoji(): bool {
		return false;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Feed();
