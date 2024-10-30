<?php
/**
 * Hero section partial for single team member page
 */
$iki_hero_data = iki_toolkit()->get_hero_section();

if ( $iki_hero_data && $iki_hero_data['layout']['title_inside'] ) { ?>
	<header class="entry-header">
		<?php
		the_title( '<h1 class="entry-title">', '</h1>' );
		iki_toolkit_post_subtitle( '<h3 class="entry-subtitle">', '</h3>' );
		if ( apply_filters( 'iki_show_team_member_contacts', true ) && $iki_hero_data['use_social_icons'] ) {
			iki_toolkit_print_team_member_contacts( true );
		}
		?>
	</header>
	<?php
}

if ( ! post_password_required() ) {
	iki_toolkit_print_hero_section_custom_content( $iki_hero_data );
}
?>
