<?php
/**
 *  Hero section partial for search page
 */
global $wp_query;
$iki_hero_data = iki_toolkit()->get_hero_section();

if ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] ) {

	iki_toolkit_get_template( 'hero-section/hs-search-result.php' );

}

iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
