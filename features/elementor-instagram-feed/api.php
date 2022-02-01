<?php
namespace Essentials\Features\Widgets\Instagram;

use Exception;
use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Instagram Api.
 *
 * @since 1.0.0
 */
class Api {
	/**
	 * The API endpoint.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	const API_URL = 'https://graph.facebook.com/v3.3/';

	/**
	 * The user ID.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $_user_id;

	/**
	 * The user access token.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $_access_token;

	/**
	 * Start things up.
	 *
	 * @param array $config Instagram configuration data.
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException Credentials are incomplete.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $config ) {
		if ( empty( $config[ 'user_id' ] ) || empty( $config[ 'access_token' ] ) ) {
			throw new InvalidArgumentException( _x( 'Credentials are incomplete', 'api error', 'essentials' ) );
		}

		// Set the configuration data.
		$this->setUserId( $config[ 'user_id' ] );
		$this->setAccessToken( $config[ 'access_token' ] );
	}

	/**
	 * Set user id.
	 *
	 * @param string $id User Id.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setUserId( $id ) {
		$this->_user_id = $id;
	}

	/**
	 * Get user id.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getUserId() {
		return $this->_user_id;
	}

	/**
	 * Set access token.
	 *
	 * @param string $token Access token.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setAccessToken( $token ) {
		$this->_access_token = $token;
	}

	/**
	 * Get access token.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getAccessToken() {
		return $this->_access_token;
	}

	/**
	 * The request maker.
	 *
	 * @param string $resource API resource path.
	 *
	 * @return string
	 *
	 * @throws Exception Error response from the Instagram API.
	 *
	 * @since 1.0.0
	 */
	protected function makeRequest( $resource ) {
		// Build the request url.
		$url = self::API_URL . $resource . '&access_token=' . $this->getAccessToken();

		// Using WordPress to retrieve request url.
		$get = wp_remote_get( $url );

		// Extract the "body", then convert it into array.
		$body = json_decode( wp_remote_retrieve_body( $get ), true );

		// Checks if response is an error.
		if ( $get[ 'response' ][ 'code' ] !== 200 ) {
			$message    = $body[ 'error' ][ 'message' ];
			$no_message = _x( 'Unknown error or response', 'api error', 'essentials' );
			$error      = ( $body[ 'error' ][ 'message' ] ) ? $message : $no_message;

			throw new Exception( $error );
		}

		// Extract the "data" part of the array.
		return $body[ 'data' ];
	}

	/**
	 * Get request.
	 *
	 * array[ 'request' ]     string API request type.
	 * array[ 'feed_count' ]  int    Number of posts.
	 * array[ 'link_target' ] string Link target type.
	 * array[ 'image_style' ] string Image tag or background image.
	 * array[ 'cache' ]       string Cache tag.
	 *
	 * @param array $options Instagram settings (see above).
	 *
	 * @return string
	 *
	 * @throws Exception Request type is invalid.
	 * @throws InvalidArgumentException Request type is invalid.
	 *
	 * @since 1.0.0
	 */
	public function getRequest( $options ) {
		switch ( $options[ 'request' ] ) {
			// If request type is "feed".
			case 'feed':
				$output = $this->getFeed( $options[ 'feed_count' ], $options[ 'link_target' ], $options[ 'image_style' ], $options[ 'cache' ] );
				break;

			// If request type is miscellaneous.
			default:
				throw new InvalidArgumentException( _x( 'Request type is invalid', 'api error', 'essentials' ) );
				break;
		}

		return $output;
	}

	/**
	 * Get feed.
	 *
	 * @param int $feed_count Number of posts.
	 * @param string $link_target Link target type.
	 * @param string $image_style Image tag or background image.
	 * @param string $cache Cache tag.
	 *
	 * @return string
	 *
	 * @throws InvalidArgumentException Feed count must be greater than 0.
	 * @throws InvalidArgumentException Link target is invalid.
	 * @throws InvalidArgumentException Image style is invalid.
	 * @throws Exception Request type is invalid.
	 *
	 * @since 1.0.0
	 */
	public function getFeed( $feed_count, $link_target, $image_style, $cache ) {
		$link_target_options = array( '_self', '_blank' );
		$image_style_options = array( 'image', 'background' );

		// Check if feed count is less than one.
		if ( $feed_count < 1 ) {
			throw new InvalidArgumentException( _x( 'Feed count must be greater than 0', 'api error', 'essentials' ) );
		}

		// Check if link target is valid.
		if ( ! in_array( $link_target, $link_target_options ) ) {
			throw new InvalidArgumentException( _x( 'Link target is invalid', 'api error', 'essentials' ) );
		}

		// Check if image style is valid.
		if ( ! in_array( $image_style, $image_style_options ) ) {
			throw new InvalidArgumentException( _x( 'Image style is invalid', 'api error', 'essentials' ) );
		}

		// If cache does not exist.
		if ( false === get_transient( $cache . '_instagram_feed' ) ) {
			// Get feed data.
			$fields = 'media_url,permalink,thumbnail_url,media_type,caption';
			$limit  = '100';
			$feed   = $this->makeRequest( $this->getUserId() . '/media?fields=' . $fields . '&limit=' . $limit );

			// Serialize then encode data (preserve special chars).
			$protected = base64_encode( serialize( $feed ) );

			// Reuse the data for 15 mins (900 seconds).
			set_transient( $cache . '_instagram_feed', $protected, 900 );
		}

		// Get the cached data.
		$raw_data = get_transient( $cache . '_instagram_feed' );

		// Decode then unserialize data.
		$data = unserialize( base64_decode( $raw_data ) );

		// Starts a new output.
		$output = '<ul class="instagram-feed instagram-feed-' . $cache . ' essentials-widget">';

		// Resets the feed count.
		$count = 0;

		// Loop though data.
		foreach ( $data as $media ) {
			// Feed count check.
			if ( $count ++ >= $feed_count ) {
				break;
			}

			// The data variables.
			$media_url     = ! empty( $media[ 'media_url' ] ) ? $media[ 'media_url' ] : '';
			$thumbnail_url = ! empty( $media[ 'thumbnail_url' ] ) ? $media[ 'thumbnail_url' ] : '';
			$permalink     = ! empty( $media[ 'permalink' ] ) ? $media[ 'permalink' ] : '';
			$media_type    = ! empty( $media[ 'media_type' ] ) ? $media[ 'media_type' ] : '';
			$caption       = ! empty( $media[ 'caption' ] ) ? $media[ 'caption' ] : '';

			// Determine media type.
			switch ( $media_type ) {
				case 'VIDEO':
					$source = $thumbnail_url;
					break;
				case 'IMAGE':
				case 'CAROUSEL_ALBUM':
				default:
					$source = $media_url;
					break;
			}

			// Image tag or background image.
			switch ( $image_style ) {
				case 'background':
					$content = "<span style=\"background-image: url('$source');\" role=\"img\" aria-label=\"$caption\"></span>";
					break;
				case 'image':
				default:
					$content = "<img src=\"$source\" alt=\"$caption\" />";
					break;
			}

			// The formatted data, concatenates $output.
			$output .= "<li class=\"photo\">";
			$output .= "<a href=\"$permalink\" target=\"$link_target\">";
			$output .= $content;
			$output .= "</a>";
			$output .= "</li>";
		}

		// Ends output.
		$output .= '</ul>';

		return $output;
	}
}
