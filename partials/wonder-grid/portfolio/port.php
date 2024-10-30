<?php
/**
 * Portfolio post grid partial.
 * Default partial for portfolio post inside grid.
 */
global $post;

/**@var Iki_Grid_Cell_Image $iki_cell_image */
$iki_cell_image     = new Iki_Grid_Cell_Image( get_post_thumbnail_id(), iki_toolkit_maybe_lazy_load_grid_thumb() );
$iki_cell_classes   = iki_toolkit_set_cell_image_classes( $iki_cell_image, array(), $post->ID );
$iki_terms          = Iki_Toolkit_Utils::get_term_name_and_link( $post->ID, 'iki_portfolio_cat', 2 );
$iki_post_permalink = esc_attr( get_permalink() );
?>
<div <?php printf( 'class="%1$s"', Iki_Toolkit_Utils::sanitize_html_class_array( $iki_cell_classes ) ) ?>>
	<a class="iki-link-wrap" href="<?php echo $iki_post_permalink ?>"></a>
	<?php $iki_cell_image->the_content(); ?>
	<div class="iki-cell-info-wrap">
		<a class="iki-cell-link" href="<?php echo $iki_post_permalink ?>">
			<h4 class="iki-cell-title"><?php echo the_title(); ?></h4>
		</a>
		<?php if ( ! empty( $iki_terms ) ) { ?>
			<div class="iki-cell-term-wrap">
				<?php
				foreach ( $iki_terms as $iki_term ) { ?>
					<a href="<?php echo esc_url( $iki_term['link'] ); ?>"
					   class="iki-cell-term"><?php echo esc_html( $iki_term['name'] ); ?></a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>
