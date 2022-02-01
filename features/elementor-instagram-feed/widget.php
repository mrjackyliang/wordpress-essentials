<?php
namespace Essentials\Features\Widgets\Instagram;

use WP_Widget;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Instagram widget.
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
			'instagram',
			sprintf(
				esc_html_x(
					'%s Feed',
					'widget name',
					'essentials'
				),
				esc_html_x(
					'Instagram',
					'brand',
					'essentials'
				),
			),
			array(
				'classname'   => 'instagram',
				'description' => sprintf(
					esc_html__(
						'Entries from your %s feed.',
						'essentials'
					),
					esc_html_x(
						'Instagram',
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

		if ( ! class_exists( 'Instagram' ) ) {
			require_once( 'api.php' );
		}

		try {
			$instagram = new Api(
				array(
					'user_id'      => $instance[ 'user_id' ],
					'access_token' => $instance[ 'access_token' ],
				)
			);

			$output = $instagram->getRequest(
				array(
					'request'     => 'feed',
					'feed_count'  => $instance[ 'count' ],
					'link_target' => $instance[ 'link_target' ],
					'image_style' => $instance[ 'image_style' ],
					'cache'       => $instance[ 'cache' ],
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
		$title        = ! empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$count        = ! empty( $instance[ 'count' ] ) ? $instance[ 'count' ] : '5';
		$link_target  = ! empty( $instance[ 'link_target' ] ) ? $instance[ 'link_target' ] : '_self';
		$image_style  = ! empty( $instance[ 'image_style' ] ) ? $instance[ 'image_style' ] : 'image';
		$user_id      = ! empty( $instance[ 'user_id' ] ) ? $instance[ 'user_id' ] : '';
		$access_token = ! empty( $instance[ 'access_token' ] ) ? $instance[ 'access_token' ] : '';
		$cache        = ! empty( $instance[ 'cache' ] ) ? $instance[ 'cache' ] : substr( md5( rand() ), 0, 13 );

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
			<label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>">
				<?php esc_html_e( 'Number of images to show:', 'essentials' ); ?>
			</label>
			<input class="tiny-text"
				   id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>"
				   type="number"
				   step="1"
				   min="1"
				   max="100"
				   value="<?php echo esc_attr( $count ); ?>"
			/>
		</p>
		<hr />
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'link_target' ) ); ?>">
				<?php esc_html_e( 'Link opens in:', 'essentials' ); ?>
				<select id="<?php echo esc_attr( $this->get_field_id( 'link_target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_target' ) ); ?>">
					<option value="_self"<?php echo ( esc_attr( $link_target ) == '_self' ) ? ' selected="selected"' : ''; ?>>
						<?php esc_html_e( 'Current window or tab', 'essentials' ); ?>
					</option>
					<option value="_blank"<?php echo ( esc_attr( $link_target ) == '_blank' ) ? ' selected="selected"' : ''; ?>>
						<?php esc_html_e( 'New window or tab', 'essentials' ); ?>
					</option>
				</select>
			</label>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_style' ) ); ?>">
				<?php esc_html_e( 'Image displays as:', 'essentials' ); ?>
				<select id="<?php echo esc_attr( $this->get_field_id( 'image_style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_style' ) ); ?>">
					<option value="image"<?php echo ( esc_attr( $image_style ) == 'image' ) ? ' selected="selected"' : ''; ?>>
						<?php esc_html_e( 'Image tag', 'essentials' ); ?>
					</option>
					<option value="background"<?php echo ( esc_attr( $image_style ) == 'background' ) ? ' selected="selected"' : ''; ?>>
						<?php esc_html_e( 'Background image', 'essentials' ); ?>
					</option>
				</select>
			</label>
		</p>
		<hr />
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'user_id' ) ); ?>">
				<?php esc_attr_e( 'User ID:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'user_id' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'user_id' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $user_id ); ?>"
			/>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'access_token' ) ); ?>">
				<?php esc_attr_e( 'Access Token:', 'essentials' ); ?>
			</label>
			<input class="widefat"
				   id="<?php echo esc_attr( $this->get_field_id( 'access_token' ) ); ?>"
				   name="<?php echo esc_attr( $this->get_field_name( 'access_token' ) ); ?>"
				   type="text"
				   value="<?php echo esc_attr( $access_token ); ?>"
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
	function update( $new_instance, $old_instance ): array {
		$instance                   = array();
		$instance[ 'title' ]        = ( ! empty( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
		$instance[ 'count' ]        = ( ! empty( $new_instance[ 'count' ] ) ) ? strip_tags( $new_instance[ 'count' ] ) : '5';
		$instance[ 'link_target' ]  = ( ! empty( $new_instance[ 'link_target' ] ) ) ? strip_tags( $new_instance[ 'link_target' ] ) : '_self';
		$instance[ 'image_style' ]  = ( ! empty( $new_instance[ 'image_style' ] ) ) ? strip_tags( $new_instance[ 'image_style' ] ) : 'image';
		$instance[ 'user_id' ]      = ( ! empty( $new_instance[ 'user_id' ] ) ) ? strip_tags( $new_instance[ 'user_id' ] ) : '';
		$instance[ 'access_token' ] = ( ! empty( $new_instance[ 'access_token' ] ) ) ? strip_tags( $new_instance[ 'access_token' ] ) : '';
		$instance[ 'cache' ]        = ( ! empty( $new_instance[ 'cache' ] ) ) ? strip_tags( $new_instance[ 'cache' ] ) : substr( md5( rand() ), 0, 5 );

		return $instance;
	}
}
