<?php
/**
 * Hero section partial used for blog
 */
$iki_hero_data = iki_toolkit()->get_hero_section();

if ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] ) {
	iki_toolkit_blog_title();
	iki_toolkit_blog_tagline();
}

iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
