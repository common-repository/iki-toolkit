<?php


/**
 * Return the ID of the author that you want to assosiate with team member post.
 * So later authors posts can be seen as team member posts.
 * @return mixed
 */
function iki_toolkit_get_team_blog_author_id( $post_id ) {

	$blog_author_id = iki_toolkit()->get_post_option( $post_id, 'author_connection', false );

	return apply_filters( 'iki_team_member_authors_connection', $blog_author_id );
}


/**
 * Get team member contacts on various social networks.
 *
 * @param string $design Design for contact elements
 *
 * @return string html of contact elements
 */
function iki_toolkit_get_team_member_contacts( $design ) {

	global $post;

	$enabled_contacts = Iki_Toolkit_Utils::get_available_social_profiles();
	$user_contacts    = array();
	$link_title       = _x( 'on ', 'Title for the link to author social profile on some service', 'iki-toolkit' );

	$team_member_contacts = iki_toolkit()->get_post_option( $post->ID, 'tm_contacts', false );

	if ( $team_member_contacts ) {
		foreach ( $enabled_contacts as $key => $value ) {

			if ( isset( $team_member_contacts[ $key ] ) ) {

				$team_member_contacts[ $key ] = trim( $team_member_contacts[ $key ] );
				if ( ! empty( $team_member_contacts[ $key ] ) ) {
					$user_contacts[ $key ] = $team_member_contacts[ $key ];
				}
			}
		}

		if ( ! empty( $user_contacts ) ) {

			return iki_toolkit_print_social_profiles( $user_contacts, $post->post_title . ' ' . $link_title, $design, false );
		}
	}
}

if ( ! function_exists( 'iki_toolkit_print_team_member_contacts' ) ) {

	/**
	 * Print team member contacts
	 *
	 * @param $in_hero_section boolean   if we are printing inside hero section
	 * @param string $before string  Content before
	 * @param string $after Content after
	 */
	function iki_toolkit_print_team_member_contacts( $in_hero_section = false, $before = '', $after = '' ) {

		global $post;
		$iki_tm_social_design = null;
		if ( ! $in_hero_section ) {

			if ( 'enabled' == iki_toolkit()->get_customizer_option( 'iki_team_member_enable_contacts_design', 'disabled' ) ) {

				$iki_tm_social_design = iki_toolkit()->get_customizer_option( 'iki_team_member_contacts_design', false );
			}
		} else {
			$hero_data = iki_toolkit()->get_hero_section();
			if ( $hero_data ) {
				if ( $hero_data['use_social_icons'] ) {
					$iki_tm_social_design = isset( $hero_data['social_design'] ) ? $hero_data['social_design'] : null;
				}
			}
		}

		$iki_author_connection   = iki_toolkit()->get_post_option( $post->ID, 'author_connection', false );
		$use_author_contact_data = iki_toolkit()->get_post_option( $post->ID, 'use_author_contact_data', 'enabled' );
		if ( $iki_author_connection && 'enabled' == $use_author_contact_data ) {
			// get author contact data.
			$iki_userContacts = iki_toolkit_get_user_contacts( $iki_author_connection, get_the_title(), $iki_tm_social_design );
		} else {
			//get custom post data
			$iki_userContacts = iki_toolkit_get_team_member_contacts( $iki_tm_social_design );
		}
		if ( ! empty( $iki_userContacts ) ) {
			do_action( 'iki_team_member_contacts_before' ); ?>
			<div class="iki-tm-contacts iki-hs-sharing ">
				<?php

				$iki_my_profiles_text = trim( iki_toolkit()->get_post_option( $post->ID,
					'my_social_profiles_text' ) );

				if ( empty( $iki_my_profiles_text ) ) {
					$iki_my_profiles_text = __( 'My social Profiles', 'iki-toolkit' );
				}
				echo wp_kses_post( $before );
				echo wp_kses_post( sprintf( '<p class="iki-my-profiles">%1$s</p>', sanitize_text_field( $iki_my_profiles_text ) ) );
				echo $iki_userContacts;
				echo wp_kses_post( $after );
				?>
			</div>
			<?php do_action( 'iki_team_member_contacts_after' );
		}
	}
}
