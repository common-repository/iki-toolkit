<?php
/**
 * Hero section partial used for page templates
 */
global $post;

$iki_hero_data         = iki_toolkit()->get_hero_section();
$iki_title_inside_hero = ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] );
$iki_show_page_title   = apply_filters( 'iki_show_main_page_title', true );
$iki_location_info     = iki_toolkit()->get_location_info();

if ( $iki_title_inside_hero && $iki_show_page_title ) {

	if ( class_exists( 'WooCommerce', false ) && iki_toolkit_is_woocommerce_page() ) {
		//woocommerce page
		iki_toolkit_woo_the_page_title( $iki_location_info['id'] );
		iki_toolkit_woo_the_page_subtitle( $iki_location_info['id'] );
	} else {
		//regular page
		the_title( '<h1 class="entry-title">', '</h1>' );
		iki_toolkit_post_subtitle( '<h3 class="entry-subtitle">', '</h3>' );
	}

}
iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
if ( $iki_show_page_title ) {
	iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data );
}

