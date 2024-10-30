<?php
/**
 * Partial for "read more" button inside featured post hero section
 */
?>
<div class="iki-feat-read-more <?php echo Iki_Toolkit_Utils::sanitize_html_class_array( $classes ); ?>">
	<?php
	printf( '<a href="%1$s">%2$s</a>',
		esc_url( get_permalink( $post_id ) ),
		esc_html( $link_text ) );
	?>
</div>
