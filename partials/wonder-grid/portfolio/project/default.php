<?php
/**
 * Grid portfolio project partial.
 */

/**@var Iki_Asset_Grid $iki_grid */
$iki_grid = Iki_Grids::get_instance()->get_active_grid();

$iki_grid_extra_data = $iki_grid->get_custom_data();
$iki_lightbox_design = ( isset( $iki_grid_extra_data['lightbox_design'] ) ) ? $iki_grid_extra_data['lightbox_design'] : 'symbol';

/**@var Iki_Grid_Cell $iki_grid_cell */
$iki_grid_cell = $iki_grid->get_current_cell();

//maybe lazy load grid thumbs
$iki_lazy_load = defined( 'DOING_AJAX' ) && DOING_AJAX;

/**@var Iki_Grid_Cell_Image $iki_cell_image */
$iki_cell_image   = new Iki_Grid_Cell_Image( $iki_grid_cell->data['asset_id'], $iki_lazy_load );
$iki_cell_classes = iki_toolkit_set_cell_image_classes( $iki_cell_image );

?>
<div <?php printf( 'class="%1$s"', Iki_Toolkit_Utils::sanitize_html_class_array( $iki_cell_classes ) ) ?>>
	<?php

	$iki_cell_image->the_content();
	echo iki_toolkit_lightbox_btn( $iki_grid_cell->data['asset_id'], 'large', array(), $iki_lightbox_design );
	?>
</div>
