<?php
/**
 * Blog post Grid partial.
 * Default partial for blog post thumbs inside grid.
 */

global $post;
/**@var Iki_Grid $iki_grid */
$iki_grid = Iki_Grids::get_instance()->get_active_grid();
/**@var Iki_Row $iki_row */
$iki_row = $iki_grid->get_current_row();
/**@var Iki_Grid_Row_Data */
$iki_row_data       = $iki_row->get_row_data();
$iki_terms          = Iki_Toolkit_Utils::get_term_name_and_link( $post->ID, 'category' );
$iki_post_permalink = esc_attr( get_permalink() );

/**@var Iki_Grid_Cell_Image $iki_cell_image */
$iki_cell_image    = new Iki_Grid_Cell_Image( get_post_thumbnail_id(), iki_toolkit_maybe_lazy_load_grid_thumb() );
$iki_cell_classes  = iki_toolkit_set_cell_image_classes( $iki_cell_image, array( 'iki-cell-blog' ), $post->ID );
$iki_post_subtitle = sanitize_text_field( iki_toolkit_post_subtitle( '', '', false ) );

if ( ! empty( $iki_post_subtitle ) && 'classic' == $iki_row_data->type && 'portrait' == $iki_row_data->orientation ) {
	$iki_post_subtitle = Iki_Toolkit_Utils::truncate_string( $iki_post_subtitle, 25, " " );
}
?>
<div <?php printf( 'class="%1$s"', Iki_Toolkit_Utils::sanitize_html_class_array( $iki_cell_classes ) ) ?>>
	<a class="iki-link-wrap" href="<?php echo esc_attr( get_permalink() ); ?>"></a>
	<div class="iki-mixed-img-wrap">
		<?php $iki_cell_image->the_content(); ?>
	</div>
	<div class="iki-cell-info-wrap">
		<?php if ( ! empty( $iki_terms ) ) { ?>
			<div class="iki-cell-term-wrap">
				<a class="iki-cell-term"
					<?php echo Iki_Toolkit_Utils::get_taxonomy_colors( $iki_terms[0]['id'], 'category' ) ?>
				   href="<?php echo esc_url( $iki_terms[0]['link'] ); ?>">
					<?php echo esc_html( $iki_terms[0]['name'] ); ?>
				</a>
			</div>
		<?php } ?>
		<a class="iki-cell-link" href="<?php echo $iki_post_permalink ?>">
			<h4 class="iki-cell-title"><?php echo iki_toolkit_grid_post_title( $post->ID ) ?></h4>
			<?php
			if ( ! empty( $iki_post_subtitle ) ) { ?>
				<p class="iki-cell-subtitle">
					<?php echo $iki_post_subtitle; ?>
				</p>
			<?php } ?>
		</a>
		<span class="iki-cell-date"><?php echo esc_html( get_the_date() ); ?></span>
	</div>
</div>
