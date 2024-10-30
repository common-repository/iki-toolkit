<?php
/**
 *
 * Template for "search" block that is available for full screen panels.
 */

$iki_search_panel_form = iki_toolkit()->get_customizer_option( 'fs_panel_search_form', 'blog' );
?>
<div
	class="search-ui search-wrapper <?php printf( 'iki-fs-search-size-%1$s', esc_attr( $fs_panel_search_el_size ) ); ?>">
	<div class="search-form-wrapper">
		<?php
		if ( 'blog' == $iki_search_panel_form ) {
			get_search_form();
		} else {
			get_product_search_form();
		}
		?>
	</div>
	<div class="icon-wrapper">
		<span class=" iki-icon-search"></span>
	</div>
</div>