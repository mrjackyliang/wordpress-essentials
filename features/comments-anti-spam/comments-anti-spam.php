<?php
namespace Essentials\Features\CommentsAntiSpam;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Comments anti-spam.
 *
 * @since 1.0.0
 */
class CommentsAntiSpam {
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Form fields.
		add_action( 'comment_form', array( $this, 'form_fields' ) );

		// Pre-process comment.
		if ( ! is_admin() ) {
			add_filter( 'preprocess_comment', array( $this, 'preprocess_comment' ), 0 );
		}

		// Script.
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
	}

	/**
	 * Form fields.
	 *
	 * @since 1.0.0
	 */
	function form_fields() {
		$form_fields = "<p style=\"display: none;\">";
		$form_fields .= "<input type=\"hidden\" name=\"session\" id=\"session\" value=\"" . wp_create_nonce( 'essentials_comments_anti_spam' ) . "\" />";
		$form_fields .= "<input type=\"hidden\" name=\"token\" id=\"token\" size=\"30\" value=\"\" />";
		$form_fields .= "</p>";
		$form_fields .= "<p style=\"display: none;\">";
		$form_fields .= "<input type=\"tel\" name=\"phone\" id=\"phone\" size=\"30\" value=\"\" />";
		$form_fields .= "</p>";

		echo $form_fields;
	}

	/**
	 * Pre-process comment.
	 *
	 * @param array $comment_data Comment data.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function preprocess_comment( array $comment_data ): array {
		$token = $_POST[ 'token' ];
		$phone = $_POST[ 'phone' ];

		/**
		 * Extracted comment data.
		 *
		 * @var $comment_post_ID - The post to which the comment will apply.
		 * @var $comment_author - (maybe empty).
		 * @var $comment_author_email - (maybe empty).
		 * @var $comment_author_url - (maybe empty).
		 * @var $comment_content - The text of the proposed comment.
		 * @var $comment_type - 'pingback', 'trackback', or empty for regular comments.
		 * @var $user_ID - (empty if not logged in).
		 *
		 * @since 1.0.0
		 */
		extract( $comment_data );

		if ( $comment_type != 'pingback' && $comment_type != 'trackback' ) {
			$error = '';

			// Check if session key is expired or wrong.
			if ( wp_verify_nonce( base64_decode( $token ), 'essentials_comments_anti_spam' ) === false ) {
				if ( empty( $token ) ) {
					$error = esc_html_x( 'JavaScript is required', 'comment spam', 'essentials' );
				} else {
					$error = esc_html_x( 'an invalid session was detected', 'comment spam', 'essentials' );
				}
			}

			// Check for honeypot field.
			if ( ! empty( $phone ) ) {
				$error = esc_html_x( 'of unknown reasons', 'comment spam', 'essentials' );
			}

			// If comment is marked with errors.
			if ( ! empty( $error ) ) {
				$error_message = sprintf(
					wp_kses(
						__(
							'<strong>ERROR</strong>: The comment could not be submitted because %s.'
						),
						array(
							'strong' => array(),
						)
					),
					$error
				);
				$back_button   = sprintf(
					wp_kses(
						__( /** @lang text */
							'<a href="%s">&laquo; Back</a>',
							'essentials'
						),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					'javascript:history.back()'
				);

				wp_die( '<div class="wp-die-message"><p>' . $error_message . '</p></div><p>' . $back_button . '</p>' );
			}
		}

		return $comment_data;
	}

	/**
	 * Script.
	 *
	 * @since 1.0.0
	 */
	function script() {
		if ( is_singular() && comments_open() ) {
			wp_enqueue_script(
				'essentials-comments-anti-spam',
				plugins_url(
					'script.js',
					__FILE__
				),
				array(
					'jquery'
				),
				ESSENTIALS_VERSION
			);
		}
	}
}

/**
 * Create new instance.
 *
 * @since 1.0.0
 */
new CommentsAntiSpam();
