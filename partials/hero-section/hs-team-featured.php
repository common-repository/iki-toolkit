<?php
/**
 * Hero section partial for featured team members posts
 */

$iki_hero_data        = iki_toolkit()->get_hero_section();
$iki_featured_post_id = $iki_hero_data['featured_post']['id'];
$iki_post_title       = get_the_title( $iki_featured_post_id );

iki_toolkit_print_featured_post_sign( _x( 'Featured Team Member', 'Featured team member post', 'iki-toolkit' ), $iki_featured_post_id );
iki_toolkit_print_featured_title_and_subtitle( $iki_featured_post_id );
iki_toolkit_print_hero_section_custom_content( $iki_hero_data );

if ( isset( $iki_hero_data['add_read_more_link'] ) ) {
	iki_toolkit_print_hero_section_read_more_link( $iki_featured_post_id, _x( 'Read More', 'Featured team member post read more link', 'iki-toolkit' )
	);
}


