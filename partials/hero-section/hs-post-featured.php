<?php
/**
 * Hero section content template for featured blog posts
 */

$iki_hero_data             = iki_toolkit()->get_hero_section();
$iki_featured_post_id      = $iki_hero_data['featured_post']['id'];
$iki_post_title            = get_the_title( $iki_featured_post_id );
$iki_show_post_update_time = iki_toolkit()->get_post_option( $iki_featured_post_id, 'show_update_time', 'enabled' );

iki_toolkit_print_featured_post_sign( _x( 'Featured Story', 'Featured blog post story', 'iki-toolkit' ), $iki_featured_post_id );
iki_toolkit_print_featured_title_and_subtitle( $iki_featured_post_id );
iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
?>
<div class="entry-meta entry-meta-bottom">
	<?php
	iki_toolkit_author_avatar( 40, $iki_featured_post_id );
	iki_toolkit_print_author_name( $iki_featured_post_id );
	iki_toolkit_print_post_date( Iki_Toolkit_Utils::string_to_boolean( $iki_show_post_update_time ), $iki_featured_post_id );
	iki_toolkit_edit_post_link();
	?>
</div><!-- .entry-meta -->
<?php

if ( isset( $iki_hero_data['add_read_more_link'] ) ) {
	iki_toolkit_print_hero_section_read_more_link( $iki_featured_post_id, _x( 'Read More', 'Featured blog post read more link', 'iki-toolkit' )
	);
}
?>
