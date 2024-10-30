<?php
/**
 *Image grid shortcode partial
 */

/**@var Iki_Asset_Grid $iki_grid */
$iki_grid        = Iki_Grids::get_instance()->get_active_grid();
$iki_custom_data = $iki_grid->get_custom_data();
$iki_grid_data   = $iki_grid->get_grid_data();

/**@var Iki_Grid_Cell $iki_grid_cell */
$iki_grid_cell = $iki_grid->get_current_cell();
$iki_image_id  = $iki_grid_cell->data['asset_id'];

/**@var Iki_Grid_Cell_Image $iki_cell_image */
$iki_cell_image     = new Iki_Grid_Cell_Image( $iki_grid_cell->data['asset_id'], false );
$iki_link_classes   = array( 'iki-cell-wrap', 'iki-cell-wrap-img-vc' );
$iki_link_classes[] = ( $iki_cell_image->has_image() ) ? 'iki-cell-image' : 'iki-cell-no-image';

?>
<div <?php printf( 'class="%1$s"', Iki_Toolkit_Utils::sanitize_html_class_array( $iki_link_classes ) ) ?>>
	<?php

	$iki_cell_image->the_content();

	if ( 'lightbox' === $iki_custom_data['image_click'] ) {
		echo iki_toolkit_lightbox_btn( $iki_image_id, 'large' );
	} elseif ( 'large_image' === $iki_custom_data['image_click'] ) {
		echo iki_grid_image_attachment_link( $iki_image_id );
	} elseif ( 'custom_link' == $iki_custom_data['image_click'] ) {
		if ( isset( $iki_custom_data['image_links'][ $iki_grid_data->cell_iterator - 1 ] ) ) {
			echo iki_grid_image_custom_link( $iki_custom_data['image_links'][ $iki_grid_data->cell_iterator - 1 ] );
		}
	}
	?>
</div>
