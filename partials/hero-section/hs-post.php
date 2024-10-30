<?php
/**
 * Hero section content template for single posts.
 */
global $post;

$iki_hero_data         = iki_toolkit()->get_hero_section();
$iki_title_inside_hero = ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] );

$iki_show_post_update_time = iki_toolkit()->get_post_option( $post->ID, 'show_update_time', 'enabled' );

if ( $iki_title_inside_hero ) {
	?>
	<div class="entry-meta entry-meta-top">
		<?php
		iki_toolkit_categories( '<span class="iki-content-meta iki-categories">', '</span>', '  ' );
		?>
	</div><!--entry-meta-top-->
	<?php

	the_title( '<h1 class="entry-title">', '</h1>' );
	iki_toolkit_post_subtitle( '<h3 class="entry-subtitle">', '</h3>' );
}

if ( ! post_password_required() ) {
	iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
}

if ( $iki_title_inside_hero ) { ?>
	<div class="entry-meta entry-meta-bottom">
		<?php
		iki_toolkit_author_avatar();
		iki_toolkit_print_author_name();
		iki_toolkit_print_post_date( Iki_Toolkit_Utils::string_to_boolean( $iki_show_post_update_time ) );
		iki_toolkit_print_comments_number( sprintf( '<span class="iki-content-meta"><a href="#comments" title="%1$s" class="iki-icon-comment- tooltip-js">',
			esc_html( __( 'Go to comments section', 'iki-toolkit' ) ) ),
			'</a></span>' );
		iki_edit_post_link();
		?>
	</div><!-- .entry-meta -->
	<?php
	iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data );
}
?>