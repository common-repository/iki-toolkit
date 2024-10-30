<?php
/**
 * Hero section content template for featured product posts
 */

$iki_hero_data        = iki_toolkit()->get_hero_section();
$iki_featured_post_id = $iki_hero_data['featured_post']['id'];

iki_toolkit_print_featured_post_sign( _x( 'New in shop', 'Featured product sign', 'iki-toolkit' ), $iki_featured_post_id );
iki_toolkit_print_featured_title_and_subtitle( $iki_featured_post_id );
iki_toolkit_print_hero_section_custom_content( $iki_hero_data );

if ( isset( $iki_hero_data['add_read_more_link'] ) ) {
	iki_toolkit_print_hero_section_read_more_link( $iki_featured_post_id,
		_x( 'Shop now', 'Featured portfolio post read more link', 'iki-toolkit' )
	);
}
