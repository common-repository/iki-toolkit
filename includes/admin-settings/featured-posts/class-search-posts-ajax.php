<?php

/*Handle callback from ajax*/

class Iki_Search_Posts_Ajax {

	/**
	 *Check if the nonce is valid and perform wp query search
	 */
	public static function search_posts_callback() {

		if ( ! check_ajax_referer( 'iki-admin-nonce-check' ) ) {
			die( 'Security check' );
		}

		$search    = $_POST['search'];
		$time      = $_POST['time'];
		$post_type = $_POST['post_type'];

		$query = new WP_Query( array(
			's'              => $search,
			'posts_per_page' => 15,
			'post_type'      => $post_type
		) );

		$r = array(
			'time'   => $time,
			'posts'  => array(),
			'status' => 'not_found'
		);

		if ( $query ) {
			if ( $query->post_count != 0 ) {

				$r['status'] = 'found';

				foreach ( $query->posts as $post ) {
					$r['posts'][] = array(
						'title'          => $post->post_title,
						'id'             => $post->ID,
						'edit_post_link' => get_edit_post_link( $post->ID )
					);
				}

			}
		} else {
			$r = $query;
		}

		wp_send_json( $r );
	}

	/**
	 * Register ajax callbacks
	 */
	public static function register_ajax_callbacks() {
		add_action( 'wp_ajax_iki_search_posts', array(
			'Iki_Search_Posts_Ajax',
			'search_posts_callback'
		) );
	}
}

Iki_Search_Posts_Ajax::register_ajax_callbacks();

