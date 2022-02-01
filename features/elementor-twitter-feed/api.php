<?php
namespace Essentials\Features\Widgets\Twitter;

use Exception;
use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Twitter Api.
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
	const API_URL = 'https://api.twitter.com/1.1/';

	/**
	 * The OAuth access token.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $_oauth_access_token;

	/**
	 * The OAuth access token secret.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $_oauth_access_token_secret;

	/**
	 * The consumer key.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $_consumer_key;

	/**
	 * The consumer secret.
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	private $_consumer_secret;

	/**
	 * Start things up.
	 *
	 * @param array $config Twitter configuration data.
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException Credentials are incomplete.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $config ) {
		if (
			empty( $config[ 'oauth_access_token' ] ) ||
			empty( $config[ 'oauth_access_token_secret' ] ) ||
			empty( $config[ 'consumer_key' ] ) ||
			empty( $config[ 'consumer_secret' ] )
		) {
			throw new InvalidArgumentException( _x( 'Credentials are incomplete', 'api error', 'essentials' ) );
		}

		// Set the configuration data.
		$this->setOAuthAccessToken( $config[ 'oauth_access_token' ] );
		$this->setOAuthAccessTokenSecret( $config[ 'oauth_access_token_secret' ] );
		$this->setConsumerKey( $config[ 'consumer_key' ] );
		$this->setConsumerSecret( $config[ 'consumer_secret' ] );
	}

	/**
	 * Set OAuth access token.
	 *
	 * @param string $token OAuth access token.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setOAuthAccessToken( $token ) {
		$this->_oauth_access_token = $token;
	}

	/**
	 * Get OAuth access token.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getOAuthAccessToken() {
		return $this->_oauth_access_token;
	}

	/**
	 * Set OAuth access token secret.
	 *
	 * @param string $secret OAuth access token secret.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setOAuthAccessTokenSecret( $secret ) {
		$this->_oauth_access_token_secret = $secret;
	}

	/**
	 * Get OAuth access token secret.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getOAuthAccessTokenSecret() {
		return $this->_oauth_access_token_secret;
	}

	/**
	 * Set consumer key.
	 *
	 * @param string $key Consumer key.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setConsumerKey( $key ) {
		$this->_consumer_key = $key;
	}

	/**
	 * Get consumer key.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getConsumerKey() {
		return $this->_consumer_key;
	}

	/**
	 * Set consumer secret.
	 *
	 * @param string $secret Consumer secret.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	public function setConsumerSecret( $secret ) {
		$this->_consumer_secret = $secret;
	}

	/**
	 * Get consumer key.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function getConsumerSecret() {
		return $this->_consumer_secret;
	}

	/**
	 * The request maker.
	 *
	 * @param string $resource API resource path.
	 * @param string $parameters Resource path arguments.
	 * @param array $headers Resource path headers.
	 *
	 * @return string
	 *
	 * @throws Exception Error response from the Twitter API.
	 *
	 * @since 1.0.0
	 */
	protected function makeRequest( $resource, $parameters, $headers ) {
		// Builds the OAuth authorization header.
		$header = $this->buildAuthorizationHeader( $headers );

		// Concatenate the resource url and parameters.
		$url = $resource . $parameters;

		// WordPress remote get arguments.
		$arguments = array(
			'headers'   => array( 'Authorization' => $header ),
			'sslverify' => false,
		);

		// Using WordPress to retrieve request url.
		$response = wp_remote_get( $url, $arguments );

		// Extract the "body", then convert it into array.
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		// Checks if response is an error.
		if ( $response[ 'response' ][ 'code' ] !== 200 ) {
			$message    = rtrim( $body[ 'errors' ][ '0' ][ 'message' ], '.' );
			$no_message = _x( 'Unknown error or response', 'api error', 'essentials' );
			$error      = ( $body[ 'errors' ][ '0' ][ 'message' ] ) ? $message : $no_message;

			throw new Exception( $error );
		}

		return $body;
	}

	/**
	 * Build authorization header.
	 *
	 * @param array $headers OAuth credentials.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function buildAuthorizationHeader( $headers ) {
		// Starts the header with "OAuth".
		$header = 'OAuth ';

		// Parameters that will be converted into string.
		$oauth_parameters = array();

		// Convert the array into "key=value" pair.
		foreach ( $headers as $key => $value ) {
			$oauth_parameters[] = $key . '="' . rawurlencode( $value ) . '"';
		}

		// Add the parameters into the $header.
		$header .= implode( ', ', $oauth_parameters );

		return $header;
	}

	/**
	 * Build OAuth credentials.
	 *
	 * @param string $request_url API resource path.
	 * @param string $parameters Resource path arguments.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function buildOAuth( $request_url, $parameters ) {
		// OAuth credentials.
		$credentials = array(
			'oauth_consumer_key'     => $this->getConsumerKey(),
			'oauth_nonce'            => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token'            => $this->getOAuthAccessToken(),
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0',
		);

		// Include parameters in OAuth credentials.
		if ( ! is_null( $parameters ) ) {
			$fields = str_replace( '?', '', explode( '&', $parameters ) );

			foreach ( $fields as $field ) {
				$split                      = explode( '=', $field );
				$credentials[ $split[ 0 ] ] = $split[ 1 ];
			}
		}

		// Build the "base string" for the OAuth signature.
		$signature = $this->buildOAuthSignatureBaseString( $request_url, $credentials );

		// Build the OAuth signature from the "base string".
		$credentials[ 'oauth_signature' ] = $this->buildOAuthSignature( $signature );

		return $credentials;
	}

	/**
	 * Build OAuth signature base string.
	 *
	 * @param string $request_url API resource path.
	 * @param array $parameters Resource path arguments.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function buildOAuthSignatureBaseString( $request_url, $parameters ) {
		// Pre-defined method.
		$method = 'GET';

		// Parameters that will be converted into string.
		$string_parameters = array();

		// Sort them alphabetically.
		ksort( $parameters );

		// Convert the array into "key=value" pair.
		foreach ( $parameters as $key => $value ) {
			$string_parameters[] = $key . '=' . $value;
		}

		return $method . '&' . rawurlencode( $request_url ) . '&' . rawurlencode( implode( '&', $string_parameters ) );
	}

	/**
	 * Build OAuth signature.
	 *
	 * @param string $data OAuth signature base string.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function buildOAuthSignature( $data ) {
		// Combine (or ampersand) the consumer secret and access token secret.
		$hash_key = rawurlencode( $this->getConsumerSecret() ) . '&' . rawurlencode( $this->getOAuthAccessTokenSecret() );

		// Encode the "$data" with the hash key.
		return base64_encode( hash_hmac( 'sha1', $data, $hash_key, true ) );
	}

	/**
	 * Get request.
	 *
	 * array[ 'request' ]          string API request type.
	 * array[ 'screenname' ]       string Screen name.
	 * array[ 'include_retweets' ] bool   Show retweets.
	 * array[ 'feed_count' ]       int    Number of posts.
	 * array[ 'cache' ]            string Cache tag.
	 *
	 * @param array $options Twitter settings (see above).
	 *
	 * @return string
	 *
	 * @throws InvalidArgumentException Request type is invalid.
	 * @throws Exception Error response from the Twitter API.
	 *
	 * @since 1.0.0
	 */
	public function getRequest( $options ) {
		switch ( $options[ 'request' ] ) {
			// If request type is "feed".
			case 'tweets':
				$output = $this->getTweets( $options[ 'screenname' ], $options[ 'include_retweets' ], $options[ 'feed_count' ], $options[ 'cache' ] );
				break;

			// If request type is miscellaneous.
			default:
				throw new InvalidArgumentException( _x( 'Request type is invalid', 'api error', 'essentials' ) );
				break;
		}

		return $output;
	}

	/**
	 * Get tweets.
	 *
	 * @param string $screenname Screen name.
	 * @param bool $include_retweets Show retweets.
	 * @param int $feed_count Number of posts.
	 * @param string $cache Cache tag.
	 *
	 * @return string
	 *
	 * @throws InvalidArgumentException Screen name is empty.
	 * @throws InvalidArgumentException "include_retweets" must be TRUE or FALSE.
	 * @throws InvalidArgumentException Tweet count must be greater than 0.
	 * @throws Exception Error response from the Twitter API.
	 *
	 * @since 1.0.0
	 */
	public function getTweets( $screenname, $include_retweets, $feed_count, $cache ) {
		// Check if screen name is empty.
		if ( empty( $screenname ) ) {
			throw new InvalidArgumentException( _x( 'Screen name is empty', 'api error', 'essentials' ) );
		}

		// Check if $include_retweets is boolean.
		if ( ! empty( $include_retweets ) && ! is_bool( $include_retweets ) ) {
			throw new InvalidArgumentException( _x( '"include_retweets" must be TRUE or FALSE', 'api error', 'essentials' ) );
		}

		// Check feed count.
		if ( $feed_count < 1 ) {
			throw new InvalidArgumentException( _x( 'Tweet count must be greater than 0', 'api error', 'essentials' ) );
		}

		// If cache does not exist.
		if ( false === get_transient( $cache . '_twitter_tweets' ) ) {
			// The requested resource.
			$resource = self::API_URL . 'statuses/user_timeline.json';

			// The required GET parameters.
			$parameters = '?screen_name=' . $screenname . '&include_rts=true';

			// Get the OAuth credentials.
			$oauth = $this->buildOAuth( $resource, $parameters );

			// Make the request.
			$request = $this->makeRequest( $resource, $parameters, $oauth );

			// Serialize then encode data (preserve special chars).
			$protected = base64_encode( serialize( $request ) );

			// Reuse the data for 15 mins (900 seconds).
			set_transient( $cache . '_twitter_tweets', $protected, 900 );
		}

		// Get the cached data.
		$raw_data = get_transient( $cache . '_twitter_tweets' );

		// Decode then unserialize data.
		$data = unserialize( base64_decode( $raw_data ) );

		// Starts a new output.
		$output = '<ul class="twitter-feed twitter-feed-' . $cache . ' essentials-widget">';

		// Resets the feed count.
		$count = 0;

		// Loop though data.
		foreach ( $data as $tweet ) {
			// The data variables.
			$text = $tweet[ 'text' ];

			// If retweets are included.
			if ( $include_retweets ) {
				// Replace the truncated retweet string with a complete retweet string.
				if ( ! empty( $tweet[ 'retweeted_status' ] ) ) {
					$retweet_head    = explode( ':', $tweet[ 'text' ] );
					$retweet_combine = $retweet_head[ '0' ] . ': ' . $tweet[ 'retweeted_status' ][ 'text' ];
					$text            = $retweet_combine;
				}
			} else {
				// Remove all retweets, including manual retweets.
				if ( strstr( $tweet[ 'text' ], 'RT @' ) == true ) {
					continue;
				}
			}

			// Feed count check.
			if ( $count ++ >= $feed_count ) {
				break;
			}

			// The formatted data, continues output.
			$output .= '<li class="tweet">' . $this->formatLinks( $text ) . '</li>';
		}

		// Finishes output.
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Format tweets.
	 *
	 * @param string $link Tweet text.
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	public function formatLinks( $link ) {
		$link = preg_replace(
			'@(https?://([-\w.]+)+(/([\w/_.]*(\?\S+)?(#\S+)?)?)?)@',
			/** @lang text */
			'<a href="$1" target="_blank">$1</a>',
			$link
		);
		$link = preg_replace(
			'/@(\w+)/',
			/** @lang text */
			'<a href="http://twitter.com/$1" target="_blank">@$1</a>',
			$link
		);
		$link = preg_replace(
			'/#(\w+)/',
			/** @lang text */
			'<a href="https://twitter.com/search?q=%23$1" target="_blank">#$1</a>',
			$link
		);
		$link = preg_replace(
			'/\$(\w+)/',
			/** @lang text */
			'<a href="https://twitter.com/search?q=%24$1" target="_blank">\$$1</a>',
			$link
		);

		return trim( $link );
	}
}
