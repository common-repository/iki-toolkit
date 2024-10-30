<?php
/**
 * Portfolio single hero section content template
 */
global $post;
$iki_hero_data         = iki_toolkit()->get_hero_section();
$iki_title_inside_hero = ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] );

if ( $iki_title_inside_hero ) {
	the_title( '<h1 class="entry-title">', '</h1>' );
	iki_toolkit_post_subtitle( '<h3 class="entry-subtitle">', '</h3>' );
}
if ( ! post_password_required() ) {
	iki_toolkit_print_hero_section_custom_content( iki_toolkit_extract_custom_content( $iki_hero_data ) );
}
iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data );
