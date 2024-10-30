<?php
/**
 * Hero section partial used for archive pages
 */
$iki_hero_data = iki_toolkit()->get_hero_section();
if ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] ) {
	iki_toolkit_print_term_info();
}

iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data );
