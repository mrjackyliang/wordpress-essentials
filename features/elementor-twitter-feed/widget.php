<?php
namespace Essentials\Features\Widgets\Twitter;

use WP_Widget;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Twitter widget.
 *
 * @since 1.0.0
 */
class Widget extends WP_Widget {
	/**
	 * Start things up.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		parent::__construct(
			'twitter',
			sprintf(
				esc_html_x(
					'%s Feed',
					'widget name',
					'essentials'
				),
				esc_html_x(
					'Twitter',
					'brand',
					'essentials'
				),
			),
			array(
				'classname'   => 'twitter',
				'description' => sprintf(
					esc_html__(
						'Entries from your %s feed.',
						'essentials'
					),
					esc_html_x(
						'Twitter',
						'brand',
						'essentials'
					)
				),
			)
		);
	}

	/**
	 * Widget output.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function widget( $args, $instance ) {
		echo $args[ 'before_widget' ];

		if ( ! empty( $instance[ 'title' ] ) ) {
			echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ) . $args[ 'after_title' ];
		}

		if ( ! class_exists( 'Twitter' ) ) {
			require_once( 'api.php' );
		}

		try {
			$twitter = new Api(
				array(
					'oauth_access_token'        => $instance[ 'oauth_access_token' ],
					'oauth_access_token_secret' => $instance[ 'oauth_access_token_secret' ],
					'consumer_key'              => $instance[ 'consumer_key' ],
					'consumer_secret'           => $instance[ 'consumer_secret' ],
				)
			);

			$output = $twitter->getRequest(
				array(
					'request'          => 'tweets',
					'screenname'       => $instance[ 'screenname' ],
					'include_retweets' => $instance[ 'include_retweets' ],
					'feed_count'       => $instance[ 'count' ],
					'cache'            => $instance[ 'cache' ],
				)
			);

			echo $output;
		} catch ( Exception $e ) {
			echo $e->getMessage();
		}

		echo $args[ 'after_widget' ];
	}

	/**
	 * Output admin widget options form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return void
	 *
	 * @since 1.0.0
	 */
	function form( $instance ) {
		$title                     = ! empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$include_retweets          = ! empty( $instance[ 'include_retweets'] ) && $instance[ 'include_retweets' ] === true ? 'include' : 'exclude';
		$count                     = ! empty( $instance[ 'count' ] ) ? $instance[ 'count' ] : '5';
		$screenname                = ! empty( $instance[ 'screenname' ] ) ? $instance[ 'screenname' ] : '';
		$oauth_access_token        = ! empty( $instance[ 'oauth_access_token' ] ) ? $instance[ 'oauth_access_token' ] : '';
		$oauth_access_token_secret = ! empty( $instance[ 'oauth_access_token_secret' ] ) ? $instance[ 'oauth_access_token_secret' ] : '';
		$consumer_key              = ! empty( $instance[ 'consumer_key' ] ) ? $instance[ 'consumer_key' ] : '';
		$consumer_secret           = ! empty( $instance[ 'consumer_secret' ] ) ? $instance[ 'consumer_secret' ] : '';
		$cache                     = ! empty( $instance[ 'cache' ] ) ? $instance[ 'cache' ] : substr( md5( rand() ), 0, 13 );

		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
				<?php esc_html_e( 'Title:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $title ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'include_retweets' ) ); ?>">
				<?php esc_html_e( 'Retweets:', 'essentials' ); ?>
			</label>
			<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include_retweets' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include_retweets' ) ); ?>">
				<option value="exclude"<?php echo ( esc_attr( $include_retweets ) == 'exclude' ) ? ' selected="selected"' : ''; ?>>
					<?php esc_html_e( 'Exclude retweets', 'essentials' ); ?>
				</option>
				<option value="include"<?php echo ( esc_attr( $include_retweets ) == 'include' ) ? ' selected="selected"' : ''; ?>>
					<?php esc_html_e( 'Include retweets', 'essentials' ); ?>
				</option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of tweets to show:', 'essentials' ); ?>
			</label>
			<input class="tiny-text"
				   id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
				   type="number"
				   step="1"
				   min="1"
				   max="20"
				   value="<?php echo esc_attr( $count ); ?>"
			/>
		</p>
		<hr />
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'screenname' ) ); ?>">
				<?php esc_html_e( 'Screen name:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'screenname' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'screenname' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $screenname ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'oauth_access_token' ) ); ?>">
				<?php esc_html_e( 'OAuth Access Token:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'oauth_access_token' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'oauth_access_token' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $oauth_access_token ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'oauth_access_token_secret' ) ); ?>">
				<?php esc_html_e( 'OAuth Access Token Secret:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'oauth_access_token_secret' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'oauth_access_token_secret' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $oauth_access_token_secret ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'consumer_key' ) ); ?>">
				<?php esc_attr_e( 'Consumer Key:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'consumer_key' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'consumer_key' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $consumer_key ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'consumer_secret' ) ); ?>">
				<?php esc_attr_e( 'Consumer Secret:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'consumer_secret' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'consumer_secret' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $consumer_secret ); ?>"
			/>
		</p>
		<p>
			<em><?php esc_html_e( 'New changes may take up to 15 minutes to show on your site.', 'essentials' ); ?></em>
			<input name="<?php echo esc_attr( $this->get_field_name( 'cache' ) ); ?>"
				   type="hidden"
				   value="<?php echo esc_attr( $cache ); ?>"
			/>
		</p>

		<?php

	}

	/**
	 * Save widget options.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance                                = array();
		$instance[ 'title' ]                     = ( ! empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
		$instance[ 'include_retweets' ]          = ( ! empty( $new_instance[ 'include_retweets' ] ) && $new_instance[ 'include_retweets' ] === 'include' ) ? true : false;
		$instance[ 'count' ]                     = ( ! empty( $new_instance[ 'count' ] ) ) ? strip_tags( $new_instance[ 'count' ] ) : '5';
		$instance[ 'screenname' ]                = ( ! empty( $new_instance[ 'screenname' ] ) ) ? strip_tags( $new_instance[ 'screenname' ] ) : '';
		$instance[ 'oauth_access_token' ]        = ( ! empty( $new_instance[ 'oauth_access_token' ] ) ) ? strip_tags( $new_instance[ 'oauth_access_token' ] ) : '';
		$instance[ 'oauth_access_token_secret' ] = ( ! empty( $new_instance[ 'oauth_access_token_secret' ] ) ) ? strip_tags( $new_instance[ 'oauth_access_token_secret' ] ) : '';
		$instance[ 'consumer_key' ]              = ( ! empty( $new_instance[ 'consumer_key' ] ) ) ? strip_tags( $new_instance[ 'consumer_key' ] ) : '';
		$instance[ 'consumer_secret' ]           = ( ! empty( $new_instance[ 'consumer_secret' ] ) ) ? strip_tags( $new_instance[ 'consumer_secret' ] ) : '';
		$instance[ 'cache' ]                     = ( ! empty( $new_instance[ 'cache' ] ) ) ? strip_tags( $new_instance[ 'cache' ] ) : substr( md5( rand() ), 0, 5 );

		return $instance;
	}
}
