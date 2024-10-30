<?php
/**
 * Hero section partial for single post woocommerce products
 */
$iki_hero_data = iki_toolkit()->get_hero_section();

if ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] ) { ?>
	<header class="entry-header">
		<?php
		the_title( '<h1 class="entry-title">', '</h1>' );
		iki_toolkit_post_subtitle( '<h3 class="entry-subtitle">', '</h3>' );
		?>
	</header>
	<?php
}
iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data );

?>
