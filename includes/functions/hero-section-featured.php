<?php

add_filter( 'iki_hero_section_data', '_filter_iki_featured_post_hero_section' );
add_action( 'pre_get_posts', 'iki_toolkit_modify_main_query' );


/**
 * Optionally remove featured post from the main query
 *
 * @param $query WP_Query wp query object
 *
 * @return mixed
 */
function iki_toolkit_modify_main_query( $query ) {


	$is_ajax = wp_doing_ajax();

	if ( ! is_admin()
	     && ! $is_ajax
	     && ! isset( $GLOBALS['iki_toolkit']['main_query_processed'] )
	     && current_theme_supports( 'iki-toolkit-featured-posts' ) ) {

		if ( $query->is_main_query() ) {

			$GLOBALS['iki_toolkit']['main_query_processed'] = true;

			$custom_archives = array( 'iki_team_member', 'iki_portfolio' );
			//'post__not_in'
			$customizer    = false;
			$options_key   = 'blog';
			$hit           = false;
			$hero_data     = false;
			$posts_key     = null;
			$term_id       = null;
			$term_name     = null;
			$cookie_suffix = '';
			$shop          = false;
			$page          = false;

			if ( $query->is_home() ) {

				$posts_key     = "iki_tk_feat_blog";
				$customizer    = true;
				$options_key   = 'blog';
				$hit           = true;
				$cookie_suffix = 'post';

			} elseif ( is_page() ) {

				$posts_key     = "iki_tk_feat_blog";
				$hit           = true;
				$term_id       = get_queried_object_id();//page id
				$page          = true;
				$cookie_suffix = 'page_' . $term_id;

			} elseif ( $query->is_category ) {

				$posts_key      = 'iki_tk_feat_blog';
				$options_key    = 'category';
				$customizer_key = 'category';
				$term_name      = $query->query['category_name'];
				$cat_obj        = get_category_by_slug( $term_name );
				if ( $cat_obj ) {
					$hit           = true;
					$term_id       = $cat_obj->cat_ID;
					$cookie_suffix = 'cat_' . $term_name;
				}

			} elseif ( class_exists( 'WooCommerce' ) && is_shop() ) {
				$posts_key     = 'iki_tk_feat_product';
				$hit           = true;
				$term_id       = wc_get_page_id( 'shop' );
				$cookie_suffix = 'shop_' . $term_id;
				$shop          = true;
			} elseif ( $query->is_post_type_archive ) {//post type archive archive
				//from customizer
				if ( in_array( $query->query['post_type'], $custom_archives ) ) {

					$hit           = true;
					$post_type     = $query->query['post_type'];
					$posts_key     = "iki_tk_feat_{$post_type}";
					$customizer    = true;
					$options_key   = 'archive_' . $post_type;
					$cookie_suffix = 'arch_' . $post_type;
				}


			} elseif ( $query->is_tax ) { // custom post type category

				if ( isset( $query->query['iki_portfolio_cat'] ) ) {

					$posts_key      = 'iki_tk_feat_iki_portfolio';
					$options_key    = 'iki_portfolio_cat';
					$customizer_key = 'archive_iki_portfolio';
					$term_name      = $query->query['iki_portfolio_cat'];
					/**@var WP_Term $term_obj */
					$term_obj = get_term_by( 'name', $term_name, $options_key );
					if ( $term_obj ) {
						$hit           = true;
						$term_id       = $term_obj->term_id;
						$cookie_suffix = 'p_cat_' . $term_id;
					}

				} elseif ( isset( $query->query['iki_team_member_cat'] ) ) {

					$posts_key      = 'iki_tk_feat_iki_team_member';
					$options_key    = 'iki_team_member_cat';
					$customizer_key = 'archive_iki_team_member';
					$term_name      = $query->query['iki_team_member_cat'];

					/**@var WP_Term $term_obj */
					$term_obj = get_term_by( 'name', $term_name, $options_key );
					if ( $term_obj ) {
						$hit           = true;
						$term_id       = $term_obj->term_id;
						$cookie_suffix = 't_cat_' . $term_id;
					}
				} elseif ( isset( $query->query['product_cat'] ) ) {

					$posts_key      = 'iki_tk_feat_product';
					$customizer_key = 'product_cat_hero_section';
					$options_key    = 'product_cat';
					$term_name      = $query->query['product_cat'];
					/**@var WP_Term $term_obj */
					$term_obj = get_term_by( 'name', $term_name, $options_key );
					if ( $term_obj ) {
						$hit           = true;
						$term_id       = $term_obj->term_id;
						$cookie_suffix = 'shop_c_' . $term_id;
					}
				}
			}
			if ( $hit ) {
				//do the processing
				//1. check for hero section
				if ( $customizer ) {
					//check from customizer
					$use_hero_section = Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_customizer_option( "{$options_key}_use_hero_section", false ) );
					if ( $use_hero_section ) {
						$hero_data = iki_toolkit()->get_customizer_option( "{$options_key}_hero_section", false );
					}
				} elseif ( $shop ) {

					$use_hero_section = Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_post_option( $term_id, 'use_hero_section', 'disabled' ) );

					if ( $use_hero_section ) {
						$hero_data = iki_toolkit()->get_post_option( $term_id, 'hero_section', false );
					}

				} elseif ( $page ) {
					//page post type
					$use_hero_section = Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_post_option( $term_id, 'use_hero_section', 'disabled' ) );

					if ( $use_hero_section ) {
						$hero_data = iki_toolkit()->get_post_option( $term_id, 'hero_section', false );
					}

				} else {
					//archive pages
					$override = iki_toolkit()->get_term_option( $term_id, $options_key, 'override_hero_section', 'disabled' );
					if ( 'enabled' == $override ) {
						// use local
						$use_hero_section = iki_toolkit()->get_term_option( $term_id, $options_key, 'use_hero_section', false );
						if ( 'enabled' == $use_hero_section ) {
							$hero_data = iki_toolkit()->get_term_option( $term_id, $options_key, 'hero_section', false );
						}
					} else {
						//check global from customizer
						$use_hero_section = iki_toolkit()->get_customizer_option( "{$customizer_key}_use_hero_section", false );
						if ( 'enabled' == $use_hero_section ) {
							$hero_data = iki_toolkit()->get_customizer_option( "{$customizer_key}_hero_section", false );
						}
					}

				}

				if ( $hero_data && isset( $hero_data['featured_posts_enabled'] ) && 'enabled' == $hero_data['featured_posts_enabled'] ) {


					//maybe switch post key (page can show any posts key (portfolio, team, post, product)
					if ( $page && isset( $hero_data['featured_posts_source'] ) && 'enabled' == $hero_data['toolkit_featured_posts'] ) {
						$posts_key = 'iki_tk_feat_' . $hero_data['featured_posts_source'];
					}

					$post_data = iki_toolkit_hero_section_featured_posts(
						$hero_data,
						$posts_key,
						$term_id,
						$term_name,
						$options_key,
						$cookie_suffix );

					// put in toolkit
					if ( $post_data ) {

						$GLOBALS['iki_toolkit']['data']['featured_post_cookie'] = 'iki_feat_' . $cookie_suffix;
						iki_toolkit()->set_featured_posts_data( $post_data );

						$feat_opts = get_option( $posts_key );
						// check if remove from query
						if ( $feat_opts && isset( $feat_opts['remove_from_query'] ) && 'on' == $feat_opts['remove_from_query'] ) {
							$query->query_vars['post__not_in'][] = $post_data['id'];
						}
					}
				}

			}
		}
	}

	return $query;
}


/**
 * Setup featured post options
 *
 * @param array $hero_data We extract featured post options from here
 * @param string $posts_key toolkit options key
 * @param null $tax_id taxonomy id
 * @param null $tax_name taxonomy name
 * @param null $taxonomy taxonomy slug
 * @param string $cookie_suffix suffix to check for the cookie
 *
 * @return array|bool
 */
function iki_toolkit_hero_section_featured_posts(
	$hero_data,
	$posts_key,
	$tax_id = null,
	$tax_name = null,
	$taxonomy = null,
	$cookie_suffix = ''
) {


	if ( isset( $hero_data['featured_posts_enabled'] ) && 'enabled' == $hero_data['featured_posts_enabled'] ) {

		$source = false;

		if ( 'enabled' == $hero_data['toolkit_featured_posts'] ) {
			$opt = get_option( $posts_key );
			if ( isset( $opt['posts'] ) ) {
				$source = $opt['posts'];
			}
		} elseif ( ! empty( $hero_data['specific_featured_posts'] ) ) {

			$feat_posts = explode( ',', trim( $hero_data['specific_featured_posts'] ) );
			if ( ! empty( $feat_posts ) ) {

				$feat_posts = array_filter( $feat_posts, function ( $v ) {
					return trim( $v );
				} );
				$source     = array_values( $feat_posts );
			}
		}

		if ( ! empty( $source )
		     && $tax_id
		     && isset( $hero_data['onyl_same_taxonomy'] )
		     && 'enabled' == $hero_data['only_same_taxonomy'] ) {

			$source_filter = array();
			foreach ( $source as $s ) {
				$terms = get_the_terms( $s, $taxonomy );
				foreach ( $terms as $term ) {

					if ( $term->term_id == $tax_id ) {
						$source_filter[] = $s;
						break;
					}
				}
			}
			//it will be empty if we haven't found the same taxonomy
			$source = $source_filter;
		}

		if ( ! empty( $source ) ) {

			if ( isset( $_COOKIE[ 'iki_feat_' . $cookie_suffix ] ) && count( $source ) > 1 ) {

				$skip_id    = $_COOKIE[ 'iki_feat_' . $cookie_suffix ];
				$skip_index = array_search( $skip_id, $source );

				if ( $skip_index !== false ) {

					$post_id = iki_toolkit_get_next_featured_post( $source, $skip_index );

				} else {
					//problem with skip index , just get the random value
					$post_id = iki_toolkit_get_random_featured_post( $source );
				}

			} else {
				$post_id = iki_toolkit_get_random_featured_post( $source );
			}
		}

		if ( ! empty( $post_id ) ) {

			$has_hero_section = iki_toolkit()->get_post_option( $post_id, 'use_hero_section', 'disabled' );

			$post_data = array(
				'id'               => $post_id,
				'type'             => get_post_type( $post_id ),
				'has_hero_section' => Iki_Toolkit_Utils::string_to_boolean( $has_hero_section )
			);

			$GLOBALS['iki_toolkit']['data']['featured_post_skip'] = $post_id;

			return $post_data;
		}
	}

	return false;

}

/**
 * Get next featured id
 *
 * @param array $source Source to search for next post
 * @param int $current_index Current index from source
 *
 * @return bool|int false if no published posts in source , otherwise post id
 */
function iki_toolkit_get_next_featured_post( $source, $current_index ) {


	$source_length = count( $source );

	$ira = ( $current_index + 1 == $source_length ) ? 0 : $current_index + 1;

	$from_start = ( 0 == $ira );

	for ( $i = $ira; $i < $source_length; $i ++ ) {

		$post_id = $source[ $i ];
		if ( 'publish' == get_post_status( $post_id ) ) {
			return $post_id;
		}

	}

	if ( ! $from_start ) {
		for ( $i = 0; $i < $ira; $i ++ ) {

			$post_id = $source[ $i ];
			if ( 'publish' == get_post_status( $post_id ) ) {
				return $post_id;
			}
		}
	}

	return false;

}

/**
 * Get random post from source array
 *
 * @param array $source array to search for posts
 * @param int $try How many tries to get published source
 *
 * @return bool
 */
function iki_toolkit_get_random_featured_post( $source, $try = 5 ) {
	for ( $i = 0; $i < $try; $i ++ ) {

		//TODO - fix duplicate random values
		$post_id = $source[ array_rand( $source ) ];
		if ( 'publish' == get_post_status( $post_id ) ) {
			return $post_id;
		}
	}

	return false;
}


if ( ! function_exists( 'iki_print_featured_post_sign' ) ) {


	/**
	 * Print featured sign for featured posts inside hero section
	 *
	 * @param string $text Text to be printed
	 * @param null $post_id featured post ID
	 * @param bool $echo to echo or return as string
	 *
	 * @return string|null
	 */

	function iki_print_featured_post_sign( $text, $post_id = null, $echo = true ) {
		$text = apply_filters( 'iki_hero_section_featured_post_sign_text', $text );
		if ( $post_id ) {
			$custom_text = iki_toolkit()->get_post_option( $post_id, 'featured_sign_text', '' );
			$custom_text = trim( $custom_text );
			if ( ! empty( $custom_text ) ) {
				$text = $custom_text;
			}
		}

		$r = sprintf( '<div class="iki-featured-notify"><span>%1$s</span></div>', $text );

		if ( $echo ) {
			echo $r;

			return null;
		}

		return $r;
	}
}

if ( ! function_exists( '_filter_iki_featured_post_hero_section' ) ) {
	/**
	 * If we have featured post , modify hero section data to include
	 * featured post hero section options
	 *
	 * @param array $hero_data Hero section options
	 *
	 * @return array|mixed Modified hero section options
	 */
	function _filter_iki_featured_post_hero_section( $hero_data ) {

		$featured_post = iki_toolkit()->get_featured_posts_data();

		if ( $featured_post ) {
			//parse post's hero section data , and optionally override some of the parents hero section data
			$featured_post_hero_data = iki_toolkit_get_post_hero_section_data( $featured_post['type'], $featured_post['id'] );
			$featured_post_hero_data = iki_override_featured_hero_section( $featured_post_hero_data, $hero_data, $featured_post['id'] );

			$featured_post_hero_data['featured_post'] = $featured_post;

			return $featured_post_hero_data;
		}

		return $hero_data;
	}
}


if ( ! function_exists( 'iki_override_featured_hero_section' ) ) {

	/** If we have featured post data , override default hero section content
	 *
	 * @param $hero_section_data
	 * @param $parent_data
	 * @param $post_id
	 *
	 * @return mixed
	 */
	function iki_override_featured_hero_section( $hero_section_data, $parent_data, $post_id ) {

		$rm_video_bg             = iki_toolkit()->get_post_option( $post_id, 'featured_rm_video_bg', true );
		$rm_title                = iki_toolkit()->get_post_option( $post_id, 'featured_rm_title', false );
		$rm_subtitle             = iki_toolkit()->get_post_option( $post_id, 'featured_rm_subtitle', false );
		$rm_image_bg             = iki_toolkit()->get_post_option( $post_id, 'featured_rm_image_bg', false );
		$rm_custom_content       = iki_toolkit()->get_post_option( $post_id, 'featured_rm_custom_content', 'disabled' );
		$row_separator           = iki_toolkit()->get_post_option( $post_id, 'featured_row_separator', 'from_parent' );
		$override_content_layout = iki_toolkit()->get_post_option( $post_id, 'featured_override_content_layout', 'enabled' );
		$override_hs_layout      = iki_toolkit()->get_post_option( $post_id, 'featured_override_hs_layout', 'disabled' );
		$rm_custom_colors        = iki_toolkit()->get_post_option( $post_id, 'featured_rm_custom_colors', false );
		$add_read_more_link      = iki_toolkit()->get_post_option( $post_id, 'featured_add_link', false );
		$featured_sign_colors    = iki_toolkit()->get_post_option( $post_id, 'featured_sign_colors', 'disabled' );
		$text_above_title        = trim( iki_toolkit()->get_post_option( $post_id, 'featured_text_above_title', '' ) );

		$hero_section_data['remove_title']    = $rm_title;
		$hero_section_data['remove_subtitle'] = $rm_subtitle;


		if ( ! empty( $text_above_title ) ) {
			$hero_section_data['text_above_title'] = $text_above_title;
		}

		if ( $add_read_more_link ) {
			$hero_section_data['add_read_more_link'] = true;
		}

		if ( 'enabled' == $featured_sign_colors ) {
			$hero_section_data['featured_sign_colors'] = array(
				'bg_color'   => iki_toolkit()->get_post_option( $post_id, 'feat_sign_bg_color', 'rgba(0,0,0,0.5)' ),
				'text_color' => iki_toolkit()->get_post_option( $post_id, 'feat_sign_text_color', '#ffffff' )

			);
		}

		if ( $rm_video_bg ) {
			//remove video background
			unset( $hero_section_data['video_background'] );

			if ( isset( $parent_data['video_background'] ) ) {
				$hero_section_data['video_background'] = $parent_data['video_background'];
			}
		}

		if ( $rm_image_bg ) {

			unset( $hero_section_data['background'] );
			unset( $hero_section_data['overlay'] );

			if ( isset( $parent_data['background'] ) ) {
				$hero_section_data['background'] = $parent_data['background'];
			}

			if ( isset( $parent_data['overlay'] ) ) {
				$hero_section_data['overlay'] = $parent_data['overlay'];
			}
		}

		if ( 'enabled' == $rm_custom_content ) {

			$use_post_excerp = iki_toolkit()->get_post_option( $post_id, 'featured_use_post_excerp', false );
			if ( $use_post_excerp ) {

				$post_excerp = apply_filters( 'the_excerpt', get_the_excerpt( $post_id ) );

				if ( ! $add_read_more_link ) {
					$post_excerp .= sprintf( '<div class="post-readmore"><div class="link-wrapper"> <a href="%1$s">%2$s<span class="iki-icon iki-icon-readmore iki-icon-right"></span></a></div></div>',
						get_permalink( $post_id ),
						esc_html( __( 'Read more ', 'iki-toolkit' ) )
					);
				}

				$hero_section_data['custom_content'] = array(
					'remove_spacing'       => false,
					'content_custom_width' => '',
					'type'                 => 'excerp',
					'payload'              => $post_excerp,
					'background'           => false,
					'content_width'        => '3'
				);
			} else {
				unset( $hero_section_data['custom_content'] );
			}
		} elseif ( isset( $hero_section_data['custom_content'] ) &&
		           'featured_image' == $hero_section_data['custom_content']['type'] ) {
			//modify featured image content to use featured image from post id.
			$hero_section_data['custom_content']['post_id'] = $post_id;

		}


		if ( 'keep' != $row_separator ) {
			//remove the separator
			unset( $hero_section_data['separator'] );
			if ( 'from_parent' == $row_separator ) {
				//get it from parent
				//check if parent is enabled
				if ( isset( $parent_data['separator'] ) ) {
					$hero_section_data['separator'] = $parent_data['separator'];
				}
			}
		}

		if ( 'enabled' == $override_content_layout ) {

			$horizontal_alignment = iki_toolkit()->get_post_option( $post_id, 'featured_horizontal_alignment', 'from_parent' );
			$vertical_alignment   = iki_toolkit()->get_post_option( $post_id, 'featured_vertical_alignment', 'from_parent' );

			if ( 'from_parent' == $horizontal_alignment ) {
				$horizontal_alignment = $parent_data['layout']['horizontal_aligment'];
			}

			if ( 'from_parent' == $vertical_alignment ) {
				$vertical_alignment = $parent_data['layout']['vertical_aligment'];
			}

		} else {

			$horizontal_alignment = $parent_data['layout']['horizontal_aligment'];
			$vertical_alignment   = $parent_data['layout']['vertical_aligment'];

		}

		$hero_section_data['layout']['horizontal_aligment'] = $horizontal_alignment;
		$hero_section_data['layout']['vertical_aligment']   = $vertical_alignment;

		if ( 'enabled' == $override_hs_layout ) {

			//get custom featured width and height
			$hero_height = iki_toolkit()->get_post_option( $post_id, 'featured_hs_height', 'from_parent' );
			$hero_width  = iki_toolkit()->get_post_option( $post_id, 'featured_hs_width', 'from_parent' );

			//maybe get it from parent after all
			if ( 'from_parent' == $hero_height ) {
				$hero_height = $parent_data['layout']['height'];
			}

			if ( 'from_parent' == $hero_width ) {
				$hero_width = $parent_data['layout']['width_fixed'];
			} else {
				$hero_width = Iki_Toolkit_Utils::string_to_boolean( $hero_width, 'fixed' );
			}


		} else {
			//get it from parent
			$hero_height = $parent_data['layout']['height'];
			$hero_width  = $parent_data['layout']['width_fixed'];
		}

		$hero_section_data['layout']['height']      = $hero_height;
		$hero_section_data['layout']['width_fixed'] = $hero_width;


		$hero_section_data['layout']['title_inside'] = true;

		if ( $rm_custom_colors ) {
			unset( $hero_section_data['custom_colors'] );
		}

		return $hero_section_data;
	}
}
