<?php
namespace Essentials\Enhancements\WordPress;

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
		// Add thumbnails to feed via "description" and "content:encoded" tags.
		add_filter( 'the_excerpt_rss', array( $this, 'feed_post_thumbnail' ) );
		add_filter( 'the_content_feed', array( $this, 'feed_post_thumbnail' ) );

		if ( is_multisite() ) {
			// Enable "Post via email" user interface.
			add_filter( 'enable_post_by_email_configuration', array( $this, 'enable_post_via_email_ui' ) );

			// Enable "Post via email" options.
			add_filter( 'allowed_options', array( $this, 'enable_post_via_email_options' ) );

			// Enable "Update Services" user interface.
			add_filter( 'enable_update_services_configuration', array( $this, 'enable_update_services_ui' ) );

			// Enable "Update Services" options.
			add_filter( 'allowed_options', array( $this, 'enable_update_services_options' ) );
		}
	}

	/**
	 * Add thumbnails to feed via "description" and "content:encoded" tags.
	 *
	 * @param string $content Content displayed in feed.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	function feed_post_thumbnail( string $content ): string {
		global $post;

		if ( has_post_thumbnail( $post->ID ) ) {
			$content = '<p>' . get_the_post_thumbnail( $post->ID ) . '</p>' . $content;
		}

		return $content;
	}

	/**
	 * Enable "Post via email" user interface.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	function enable_post_via_email_ui(): bool {
		return true;
	}

	/**
	 * Enable "Post via email" options.
	 *
	 * @param array $allowed_options The allowed options list.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function enable_post_via_email_options( array $allowed_options ): array {
		$options = [
			'default_email_category',
			'mailserver_url',
			'mailserver_port',
			'mailserver_login',
			'mailserver_pass'
		];

		foreach ( $options as $option ) {
			if ( ! in_array( $option, $allowed_options[ 'writing' ] ) ) {
				$allowed_options[ 'writing' ][] = $option;
			}
		}

		return $allowed_options;
	}

	/**
	 * Enable "Update Services" user interface.
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	function enable_update_services_ui(): bool {
		return true;
	}

	/**
	 * Enable "Update Services" options.
	 *
	 * @param array $allowed_options The allowed options list.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function enable_update_services_options( array $allowed_options ): array {
		$options = [
			'ping_sites'
		];

		foreach ( $options as $option ) {
			if ( ! in_array( $option, $allowed_options[ 'writing' ] ) ) {
				$allowed_options[ 'writing' ][] = $option;
			}
		}

		return $allowed_options;
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new Feed();
