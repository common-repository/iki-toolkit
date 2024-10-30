<?php


/**
 * Register iki content block widget
 */
function register_iki_content_block_widget() {
	register_widget( 'Iki_Content_Block_Widget' );
}

add_action( 'widgets_init', 'register_iki_content_block_widget' );

/**
 * Class that handles the creation of Content block widget
 */
class Iki_Content_Block_Widget extends WP_Widget {

	/**
	 * Iki_Content_Block_Widget constructor.
	 * @inheritdoc
	 */
	public function __construct() {
		$widget_ops = array( 'description' => __( 'Displays Content block in a widget', 'iki-toolkit' ) );
		parent::__construct( 'iki_content_block', __( 'Iki Themes Content Block Widget', 'iki-toolkit' ), $widget_ops );
	}

	/**
	 * @inheritdoc
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'full_width' => '' ) );
		$title    = $instance['title'];
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
				<input class="widefat"
					   id="<?php echo $this->get_field_id( 'title' ); ?>"
					   name="<?php echo $this->get_field_name( 'title' ); ?>"
					   type="text"
					   value="<?php echo esc_attr( $title ); ?>"/></label>
		</p>
		<?php

		$postID = ''; // Initialize the variable
		if ( isset( $instance['custom_post_id'] ) ) {
			$postID = esc_attr( $instance['custom_post_id'] );
		};
		?>

		<p>
			<label
				for="<?php echo $this->get_field_id( 'custom_post_id' ); ?>"> <?php echo __( 'Content Block to Display:', 'iki-toolkit' ) ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'custom_post_id' ); ?>"
						name="<?php echo $this->get_field_name( 'custom_post_id' ); ?>">
					<?php
					$args = array(
						'post_type'        => 'iki_content_block',
						'suppress_filters' => 0,
						'numberposts'      => - 1,
						'order'            => 'ASC',
						'tax_query'        => array(
							array(
								'taxonomy' => 'iki_content_block_cat',
								'field'    => 'slug',
								'terms'    => array(
									'widget',
									'global'
								)
							)
						)
					);

					$content_blocks = get_posts( $args );
					if ( $content_blocks ) {
						foreach ( $content_blocks as $content_block ) : setup_postdata( $content_block );
							echo '<option value="' . $content_block->ID . '"';
							if ( $postID == $content_block->ID ) {
								echo ' selected';
							};
							echo '>' . $content_block->post_title . '</option>';
						endforeach;
					} else {
						echo '<option value="">' . __( 'No content blocks available', 'iki-toolkit' ) . '</option>';
					};
					?>
				</select>
			</label>
		</p>
		<p><label for="<?php echo $this->get_field_id( 'full_width' ); ?>"><?php _e( 'Make widget full width:' ); ?>
				<input class="widefat"
					   id="<?php echo $this->get_field_id( 'full_width' ); ?>"
					   name="<?php echo $this->get_field_name( 'full_width' ); ?>"
					   type="checkbox"
					<?php checked( $instance['full_width'], 'on' ) ?>
				/></label>
		</p>
		<p>
			<?php
			echo '<a href="post.php?post=' . $postID . '&action=edit">' . __( 'Edit selected content block', 'iki-toolkit' ) . '</a>';
			?>
		</p>
	<?php } //end form

	/**@inheritdoc
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;
		$instance['custom_post_id'] = strip_tags( $new_instance['custom_post_id'] );
		$new_instance               = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance['title']          = sanitize_text_field( $new_instance['title'] );

		//this option wasn't here from the start
		if ( isset( $new_instance['full_width'] ) ) {
			$instance['full_width'] = sanitize_text_field( $new_instance['full_width'] );
		}

		return $instance;
	}

	/**@inheritdoc
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$post_id = ( $instance['custom_post_id'] != '' ) ? esc_attr( $instance['custom_post_id'] ) : __( 'Find', 'iki-toolkit' );

		$id = $post_id;

		$position = $args['id'];

		$id = apply_filters( 'iki_toolkit_print_content_block_id', $id, $position );

		if ( empty( $id ) ) {
			return;
		}

		$classes = apply_filters( 'iki_toolkit_content_block_class', array(), $id, $position );

		$cb_content = Iki_CB_Factory::get_instance()->content_block( $id, false );

		if ( ! empty( $cb_content ) ) {
			$classes[] = 'iki-block-' . $id;
			$classes   = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );

			do_action( 'iki_toolkit_content_block_before', $id, $position );
			printf( '<div class="iki-content-block iki-cb-%2$s %1$s">', $classes, esc_attr( $position ) );
			do_action( 'iki_toolkit_content_block_start', $id, $position );
			echo $cb_content;
			do_action( 'iki_toolkit_content_block_end', $id, $position );
			echo '</div>';
			do_action( 'iki_toolkit_content_block_after', $id, $position );
		}

		echo $args['after_widget'];
	}


}