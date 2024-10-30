<?php

add_filter( 'iki_grid_cell_partial', '_filter_iki_toolkit_grid_cell_partial', 10, 2 );
add_filter( 'the_title', '_filter_iki_ajax_private_title', 10, 2 );
add_filter( 'iki_grid_image_size', '_filter_iki_change_grid_image_size' );

if ( ! function_exists( '_filter_iki_toolkit_grid_cell_partial' ) ) {
	/**
	 * Return the appropriate template for the grid cell
	 *
	 * @param $template
	 * @param Iki_Grid $grid
	 *
	 * @return mixed
	 */
	function _filter_iki_toolkit_grid_cell_partial( $template, Iki_Grid $grid ) {

		$location_info    = iki_toolkit()->get_location_info();
		$grid_data        = $grid->get_custom_data();//get the design from here.
		$custom_blog_grid = false;
		$design           = 'default';

		//Wordpress Gutenberg plugin fix
		if ( is_null( $location_info ) ) {
			return $template;
		}
		if ( strpos( $grid_data['id'], 'iki_b-' ) !== false ) {
			$custom_blog_grid = true;
			$design           = 'blog-special';
		}

		//visual composer grids
		if ( 'vc' == $grid->get_location() ) {

			//image grid
			if ( $grid instanceof Iki_Asset_Grid ) {

				$template = 'wonder-grid/vc/asset-grid.php';

			} else {
				// post grid
				if ( ! $custom_blog_grid ) {
					$design = iki_toolkit_strip_cell_template_version( $grid_data['design'] );
				}

				if ( 'post' == $grid_data['post_type'] ) {

					$template = 'wonder-grid/post/' . $design . '.php';

				} elseif ( 'iki_portfolio' == $grid_data['post_type'] ) {

					$template = 'wonder-grid/portfolio/' . $design . '.php';

				} elseif ( 'iki_team_member' == $grid_data['post_type'] ) {

					$template = 'wonder-grid/team/' . $design . '.php';
				}
			}

		} else {
			if ( 'post' == $location_info['location'] ) {
				//post single location
				if ( 'iki_portfolio' == $location_info['type'] ) {
					//single portfolio post
					if ( $grid instanceof Iki_Asset_Grid ) {
						$template = 'wonder-grid/portfolio/project/default.php';
					} elseif ( 'similar_posts' == $grid_data['location'] ) {
						if ( ! $custom_blog_grid ) {

							$design = iki_toolkit()->get_post_option( $location_info['id'], 'similar_grid_template', 'port' );
							$design = iki_toolkit_strip_cell_template_version( $design );
						}
						$template = 'wonder-grid/portfolio/' . $design . '.php';
					}
				} else {
					if ( ! $custom_blog_grid ) {
						//default for other post types.
						$design = iki_toolkit()->get_post_option( $location_info['id'], 'similar_grid_template', 'default' );
						$design = iki_toolkit_strip_cell_template_version( $design );
					}
					$template = 'wonder-grid/post/' . $design . '.php';
				}

			} elseif ( 'author' == $location_info['type'] ) {
				//author page.
				if ( ! $custom_blog_grid ) {
					$design = iki_toolkit_strip_cell_template_version( $grid_data['design'] );
				}

				$template = 'wonder-grid/post/' . $design . '.php';

			} elseif ( 'blog' == $location_info['location'] ) {
				//main grid scripts.
				if ( isset( $grid_data['design'] ) ) {
					if ( ! $custom_blog_grid ) {
						$design = iki_toolkit_strip_cell_template_version( $grid_data['design'] );
					}
					$template = 'wonder-grid/post/' . $design . '.php';
				}
			} elseif ( 'archive' === $location_info['location'] ) {
				if ( false !== strpos( $location_info['type'], 'iki_portfolio' ) ) {

					if ( ! $custom_blog_grid ) {

						$design = iki_toolkit_strip_cell_template_version( $grid_data['design'] );
					}
					$template = 'wonder-grid/portfolio/' . $design . '.php';

				} elseif ( false !== strpos( $location_info['type'], 'iki_team_member' ) ) {
					if ( ! $custom_blog_grid ) {
						$design = iki_toolkit_strip_cell_template_version( $grid_data['design'] );
					}
					$template = 'wonder-grid/team/' . $design . '.php';
				} else {
					//regular post archive

					if ( ! $custom_blog_grid ) {
						$design = iki_toolkit_strip_cell_template_version( $grid_data['design'] );
					}
					$template = 'wonder-grid/post/' . $design . '.php';
				}
			}
		}

		//fallback
		if ( empty( $template ) ) {
			$template = 'wonder-grid/post/default.php';
		}

		return $template;
	}
}


/**
 * Check for alternative versions (v-1, v-2, v-3)
 * strip the versions and load just the template
 *
 * @param $haystack
 *
 * @return bool string template name without version, or false otherwise
 */
function iki_toolkit_strip_cell_template_version( $haystack ) {

	$has_alt = preg_match( '/(.+)(\-v[0-9]$)/', $haystack, $alt_version );

	if ( 1 == $has_alt ) {
		if ( isset( $alt_version[1] ) ) {
			$haystack = $alt_version[1];

		}
	}

	return $haystack;
}

/**
 * Print blog grid
 *
 * @param $query
 * @param Iki_Grid_Manager $grid_manager
 * @param array $js_data
 *
 * @return mixed;
 *
 */

function iki_toolkit_print_grid( WP_Query $query, Iki_Grid_Manager $grid_manager, $js_data = array() ) {

	if ( ! isset( $GLOBALS['iki_toolkit'] ) ) {
		return false;
	}

	$current_page = $query->get( 'paged', '' );

	if ( $query->max_num_pages > 1 ) {

		if ( ! $current_page ) {
			$current_page = 1;
		}

	}
	$js_data['current'] = $current_page;
	$js_data['total']   = $query->max_num_pages;

	$grid_manager->add_export_data( $js_data );

	return $grid_manager->print_grid( $query );
}


/**
 * For "mixed" grids when on blog page , or single post, use different size images.
 *
 * @param $img_size
 *
 * @return string
 */
function _filter_iki_grid_cell_image_size_for_premade_mixed( $img_size ) {


	/**@var Iki_Grid $grid */
	$grid = Iki_Grids::get_instance()->get_active_grid();

	$location_info = iki_toolkit()->get_location_info();

	$is_mixed = ( strpos( $grid->get_id(), 'mixed' ) !== false ) ? true : false;

	if ( $is_mixed && ( 'blog' == $location_info['type'] || 'post' == $location_info['type'] ) ) {

		$img_size = 'grid_2_square';
		/**@var Iki_Grid_Cell $grid_cell */
		$grid_cell        = $grid->get_current_cell();
		$cell_orientation = $grid_cell->get_orientation();

		/**@var Iki_Row $grid_row */
		$grid_row = $grid->get_current_row();

		/**@var Iki_Grid_Row_Data $row_data */
		$row_data = $grid_row->get_data();

		if ( 'square' == $cell_orientation ) {

			$img_size = 'grid_2_landscape';
		}
		if ( 'classic' === $row_data->type ) {
			if ( 'portrait' == $row_data->orientation ) {
				$img_size = 'grid_2_square';
			} else {
				$img_size = 'grid_2_' . $row_data->orientation;
			}
		}
	}

	return $img_size;
}

add_filter( 'iki_grid_image_size', '_filter_iki_grid_cell_image_size_for_premade_mixed' );


/**
 * Filter for post title if we are doing ajax pagination and user is logged in
 *  If the post is "private" add private word at the beginning of the title
 *
 * @param $title
 * @param $id
 *
 * @return string
 */
function _filter_iki_ajax_private_title( $title, $id ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		if ( isset( $GLOBALS['iki_theme'] )
		     && isset( $GLOBALS['iki_theme']['flags']['ajax_user_is_admin'] )
		     && $GLOBALS['iki_theme']['flags']['ajax_user_is_admin'] ) {
			global $post;
			if ( 'private' === $post->post_status ) {
				$title = esc_html__( 'Private: ', 'iki-toolkit' ) . $title;
			}
		}
	}

	return $title;
}


if ( ! function_exists( 'iki_grid_image_attachment_link' ) ) {

	/** Get grid image attachment link
	 *
	 * @param string|int $image_id image id
	 * @param string $classes string additional classes
	 *
	 * @return string
	 */
	function iki_grid_image_attachment_link( $image_id, $classes = '' ) {

		$result = '';

		$img_link = get_permalink( $image_id );
		if ( $img_link ) {
			$img_link = sprintf( '<a class="iki-grid-img-link" href="%1$s"></a>', esc_url( $img_link ) );
			$result   = sprintf( '<div class="iki-lb-btn %2$s">%1$s<span class="iki-popup-btn iki-view-larger iki-icon-link"></span></div>',
				$img_link,
				sanitize_html_class( $classes ) );
		}


		return $result;
	}

}


if ( ! function_exists( 'iki_grid_image_custom_link' ) ) {

	/** Get grid image custom link ( WPBakery visual composer ) shortcode
	 *
	 * @param string $link link to be wrapped
	 * @param string $classes additional classes
	 *
	 * @return string
	 */
	function iki_grid_image_custom_link( $link, $classes = '' ) {

		$img_link = sprintf( '<a class="iki-grid-img-link" href="%1$s"></a>', esc_url( $link ) );

		$result = sprintf( '<div class="iki-lb-btn %2$s">%1$s<span class="iki-popup-btn iki-view-larger iki-icon-link"></span></div>',
			$img_link,
			sanitize_html_class( $classes ) );


		return $result;
	}

}


/**
 * Print external service grid
 */
function iki_toolkit_print_external_service_grid() {

	$service = Iki_Toolkit_Ext_Service_Front_End::$service_name;

	$external_grid_class = apply_filters( 'iki_toolkit_external_service_grid_class', array(
		'iki-external-images',
		'iki-location-main'
	) );

	printf( '<div class="%1$s">', Iki_Toolkit_Utils::sanitize_html_class_array( $external_grid_class ) );
	iki_toolkit_print_ext_placeholders();
	echo '</div>';

	iki_toolkit_get_template( "external-services/{$service}-thumb.php" );
}


/**
 * Print external service placeholders
 */
function iki_toolkit_print_ext_placeholders() {

	$template         = '<div class="iki-ext-placeholder iki-thumb-container"></div>';
	$placeholders_num = apply_filters( 'iki_toolkit_external_grid_placeholder_numbers', 16 );
	$r                = '';
	for ( $i = 1; $i <= $placeholders_num; $i ++ ) {

		$r .= $template;

	}
	echo $r;
}

/**
 * Determine if we should lazy load grid thumbs
 *  If the location of the grid is "similar posts" or we are doing ajax, then do the lazy load
 * @return bool true for lazy load
 */
function iki_toolkit_maybe_lazy_load_grid_thumb() {

	//determine if image thumbs should be "lazy" loaded
	/**@var Iki_Grid $active_grid */
	$active_grid      = Iki_Grids::get_instance()->get_active_grid();
	$active_grid_data = $active_grid->get_grid_data();

	$lazy_load = ( defined( 'DOING_AJAX' ) && DOING_AJAX )
	             || 'similar_posts' === $active_grid_data->location
	             || 'member_posts' === $active_grid_data->location;

	return $lazy_load;
}

/**
 * Setup grid cell classes
 *
 * @param Iki_Grid_Cell_Image $cell_image
 * @param array $classes array of additional classes
 * @param null $post_id
 *
 * @return array
 */
function iki_toolkit_set_cell_image_classes( Iki_Grid_Cell_Image $cell_image, $classes = array(), $post_id = null ) {

	$r = array(
		'iki-cell-wrap',
		$cell_image->has_image() ? 'iki-cell-image' : 'iki-cell-no-image'
	);

	if ( $post_id ) {
		$r[] = is_sticky( $post_id ) ? 'iki-cell-sticky' : '';
	}

	return array_merge( $r, $classes );
}


/**
 * Get HTML structure for grid post title
 *
 * @param null $id post id
 *
 * @return string html
 */
function iki_toolkit_grid_post_title( $id = null ) {
	if ( ! $id ) {
		global $post;
		$id = $post->ID;
	}

	$post_format = get_post_format( $id );
	$icon        = '';

	//only show video icon for post format grid
	if ( 'video' == $post_format ) {
		$icon = iki_toolkit_get_post_fromat_icon( $post_format );
	}

	if ( ! empty( $icon ) ) {
		$icon = sprintf( '<span class="%1$s"></span>', $icon );
	}

	return get_the_title( $id ) . $icon;
}


/**
 * Get class for post format
 *
 * @param string $format post format
 *
 * @return string icon class
 */
function iki_toolkit_get_post_fromat_icon( $format ) {

	$icon = '';
	if ( $format ) {

		switch ( $format ) {
			case 'video':
				$icon = 'iki-icon-video-2';
				break;
			case 'standard':
				$icon = '';
				break;
			default:
				$icon = 'iki-icon-' . $format;

		}
	}


	return $icon;
}


/**
 * Filter to change grid cell image size for particular design
 *
 * @param $image_size string image size
 *
 * @return string image size
 */
function _filter_iki_change_grid_image_size( $image_size ) {


	/**@var Iki_Grid $iki_grid */
	$grid = Iki_Grids::get_instance()->get_active_grid();

	if ( 'iki_b-land' === $grid->get_id() ) {

		$image_size = 'grid_2_landscape';
	}

	return $image_size;
}
