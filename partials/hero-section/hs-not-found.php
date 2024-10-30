<?php
/**
 * Hero section content template for not found page.
 */
$iki_hero_data = iki_toolkit()->get_hero_section();

if ( $iki_hero_data['layout']['title_inside'] ) {
	printf( '<h1 class="entry-title">%1$s</h1>', esc_html( __( 'Oops! Page Not Found.', 'iki-toolkit' ) ) );


}

iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
