<?php
/**
 * Hero section partial for single post woocommerce products
 */
$iki_hero_data         = iki_toolkit()->get_hero_section();
$iki_location_info     = iki_toolkit()->get_location_info();

$iki_title_inside_hero = ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] );

if ( $iki_title_inside_hero ) { ?>
	<header class="entry-header">
		<?php
		iki_toolkit_the_title( '<h1 class="entry-title">', '</h1>', true, $iki_location_info['id'] );
		iki_toolkit_post_subtitle( '<h3 class="entry-subtitle">', '</h3>', true, $iki_location_info['id'] );
		?>
	</header>
	<?php
}
iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data );

?>
