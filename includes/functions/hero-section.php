<?php

add_filter( 'iki_toolkit_hero_section_html_data_attr', '_filter_iki_toolkit_hero_section_data_attr' );
add_filter( 'iki_toolkit_print_inline_css', '_action_iki_toolkit_hero_section_custom_css' );
add_filter( 'iki_toolkit_hero_section_class', '_filter_iki_toolkit_hero_section_class' );

add_action( 'iki_hero_section_end', '_action_iki_toolkit_hero_section_scroll_down_indicator', 100 );
add_action( 'iki_hero_section_end', '_action_iki_toolkit_hero_section_separator', 100 );
add_action( 'iki_hero_section_start', '_action_iki_toolkit_hero_section_background_setup', 10 );
add_action( 'iki_hero_section_content_start', 'iki_toolkit_print_hero_section_content' );

/**
 * Parse hero section data.
 *  Gets "raw" data from admin, and parses the data to be used inside modules,
 * and optionally javascript.
 *
 * @param $data
 *
 * @return array
 */
if ( ! function_exists( 'iki_toolkit_parse_hero_section_data' ) ) {

	function iki_toolkit_parse_hero_section_data( $data ) {

		$mobile_device = wp_is_mobile();

		$r = array();

		if ( isset( $data['custom_social_enabled'] ) ) {

			if ( 'enabled' == $data['custom_social_enabled'] ) {
				$r['social_design'] = $data['social_design'];
			}

		} else {
			if ( isset( $data['social_design'] ) ) {
				$r['social_design'] = $data['social_design'];
			}
		}

		$r['use_social_icons'] = true;
		if ( isset( $data['use_social_icons'] ) && 'disabled' == $data['use_social_icons'] ) {
			$r['use_social_icons'] = false;
		}

		if ( isset( $data['separator_enabled'] ) && 'enabled' == $data['separator_enabled'] ) {

			$width    = $data['separator_width'];
			$position = $data['separator_position'];

			$r['separator'] = array(
				'design'   => $data['separator_design'],
				'position' => $position,
				'width'    => $width
			);

		}

		if ( isset( $data['custom_colors_enabled'] ) && 'enabled' == $data['custom_colors_enabled'] ) {

			$r['custom_colors'] = array(
				'text_color'       => $data['text_color'],
				'link_color'       => $data['link_color'],
				'link_color_hover' => $data['link_color_hover']
			);

			if ( isset( $data['remove_text_shadow'] ) ) {
				$r['custom_colors']['remove_text_shadow'] = $data['remove_text_shadow'];
			}
		}

		if ( 'enabled' == $data['custom_layout'] ) {

			$r['layout'] = array(
				'height'                    => isset( $data['height'] ) ? $data['height'] : 'normal',
				// new option fallback
				'width_fixed'               => Iki_Toolkit_Utils::string_to_boolean( $data['width_fixed'] ),
				'horizontal_aligment'       => $data['horizontal_aligment'],
				'vertical_aligment'         => $data['vertical_aligment'],
				'scroll_indicator'          => Iki_Toolkit_Utils::string_to_boolean( $data['scroll_indicator'] ),
				'scroll_indicator_position' => $data['scroll_indicator_position'],
				'title_inside'              => Iki_Toolkit_Utils::string_to_boolean( $data['title_inside'] )
			);

		}
		if ( 'enabled' == $data['custom_content_enabled'] ) {

			$parsed_content = iki_toolkit_parse_custom_content_data( $data['custom_content'] );

			$custom_content_data = $parsed_content['custom_content'];//$custom_content[$chosen_content];


			if ( 'none' != $parsed_content['custom_content'] ) {


				$custom_content_data['background'] = Iki_Toolkit_Utils::string_to_boolean( $data['content_background'] );

				$custom_content_data['content_custom_width'] = $data['content_custom_width'];
				$custom_content_data['content_width']        = $data['content_width'];
				$custom_content_data['remove_spacing']       = Iki_Toolkit_Utils::string_to_boolean( $data['content_remove_spacing'] );

				$r['custom_content'] = $custom_content_data;
			}
		}
		if ( Iki_Toolkit_Utils::string_to_boolean( $data['background_enabled'] ) ) {

			$srcs = array();

			$r['background'] = array(
				'generate_blur'  => Iki_Toolkit_Utils::string_to_boolean( $data['generate_blur'] ),
				'permanent_blur' => Iki_Toolkit_Utils::string_to_boolean( $data['permanent_blur'] ),
				'blur_strength'  => isset( $data['blur_strength'] ) ? $data['blur_strength'] : 15,
				'repeat'         => $data['background_repeat'],
				'size'           => $data['background_size'],
				'color'          => $data['background_color'],
				'position'       => $data['background_position'],
				'attachment'     => $data['background_attachment']
			);

			if ( ! empty( $data['background_url'] ) ) {
				$id = $data['background_url']['attachment_id'];

				$bg_src_size = apply_filters( 'iki_filter_hero_section_image_src_size', 'large' );
				$landscape   = wp_get_attachment_image_src( $id, $bg_src_size );

				if ( $landscape ) {
					$srcs['large'] = $landscape[0];
				} else {
					$medium = wp_get_attachment_image_src( $id, 'medium' );
					if ( $medium ) {
						$srcs['medium'] = $medium[0];
					}

				}

				//get the thumb only if generate blur is true
				if ( $data['generate_blur'] ) {
					$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );
					if ( $thumb ) {
						$srcs['thumbnail'] = $thumb[0];
					}
				}
			}
			$r['background']['srcs'] = $srcs;
			$export['background']    = $r['background'];
		}


		if ( Iki_Toolkit_Utils::string_to_boolean( $data['overlay_enabled'] ) ) {
			$r['overlay'] = array(
				'color_1'       => $data['gradient_color_1'],
				'color_2'       => $data['gradient_color_2'],
				'color_1_start' => $data['gradient_c_1_start'],
				'color_2_start' => $data['gradient_c_2_start'],
				'orientation'   => $data['gradient_orientation'],
			);
		}

		if ( Iki_Toolkit_Utils::string_to_boolean( $data['video_background_enabled'] ) &&
		     ! empty( $data['video_background_url'] ) &&
		     ! $mobile_device
		) {
			$r['video_background'] = array(
				'quality'         => 'medium',// from meta
				'videoURL'        => $data['video_background_url'],
				'showControls'    => false,
				'mute'            => true,
				'containment'     => 'self',
				'stopMovieOnBlur' => false,
				'loop'            => true
			);
			if ( 'none' !== $data['video_background_pattern'] ) {

				$pattern_src = IKI_TOOLKIT_ROOT_URL . 'images/front/pattern-overlays/' . $data['video_background_pattern'];

				$r['video_background']['pattern'] = $pattern_src;
			}
		}

		return $r;
	}
}


/**
 * Get the appropriate hero section data, depending on the currently active page
 */
function iki_toolkit_setup_hero_section_data() {

	$r = null;

	$location_info   = iki_toolkit()->get_location_info();
	$theme_sections  = array( 'blog', 'not_found', 'search' );
	$custom_archives = array( 'iki_team_member', 'iki_portfolio' );

	if ( 'post' == $location_info['location'] ) {
		//get data for any post
		$r = iki_toolkit_get_post_hero_section_data( $location_info['type'], $location_info['id'] );

	} elseif ( 'archive' == $location_info['location'] ) {
		// get data for custom  archive page
		if ( in_array( $location_info['type'], $custom_archives ) ) {

			$r = iki_toolkit_get_hero_section_by_location_data( 'archive_' . $location_info['type'] );

		} else {
			// get data for wordpress default archive pages
			$r = iki_toolkit_get_archive_hero_section_data( $location_info['type'], $location_info['id'] );

		}

	} elseif ( in_array( $location_info['location'], $theme_sections ) ) {
		//get data for other locations ( 404, index, not-found, blog etc..)
		$r = iki_toolkit_get_hero_section_by_location_data( $location_info['location'] );
	} else {
		//default hero section design
		$r = iki_toolkit_get_theme_hero_section_data();
	}

	$r = apply_filters( 'iki_hero_section_data', $r );

	return $r;
}


/**
 * Get default (hard coded) hero section data
 * @return array
 */
function iki_toolkit_get_default_hero_section_data() {

	return array(
		//disabled default options
		'custom_content_enabled'    => 'disabled',
		'video_background_enabled'  => 'disabled',
		//custom text colors
		'custom_text_colors'        => 'disabled',
		//background
		'background_enabled'        => 'disabled',
		'generate_blur'             => false,
		'permanent_blur'            => false,
		'blur_strength'             => 15,//normal
		'background_attachment'     => 'fixed',
		'background_url'            => '',
		'background_position'       => 'top left',
		'background_repeat'         => 'no-repeat',
		'background_size'           => 'cover',
		'background_color'          => '#ffffff',
		//gradient
		'overlay_enabled'           => 'disabled',
		'gradient_c_1_start'        => 0,
		'gradient_c_2_start'        => 100,
		'gradient_color_1'          => 'rgba(0,0,0,1)',
		'gradient_color_2'          => 'rgba(0,0,0,8)',
		'gradient_orientation'      => 'top',
		// layout
		'custom_layout'             => 'enabled',
		'height'                    => 'normal',
		'width_fixed'               => 'disabled',
		'horizontal_aligment'       => 'center',
		'vertical_aligment'         => 'center',
		'scroll_indicator'          => 'disabled',
		'scroll_indicator_position' => 'right',
		'title_inside'              => 'enabled',
		//separator
		'separator_enabled'         => 'disabled',
		'separator_design'          => 'tilt-left-s',
		'separator_position'        => 'relative',
		'separator_width'           => 'full'
	);
}

/**
 * Wrapper function to get default hero section options
 * Uses a combination of hard coded values and customizer options
 * @return array
 */
function iki_toolkit_get_theme_hero_section_data() {

	if ( ! isset( $GLOBALS['iki_toolkit']['data']['default_parsed_hero_section'] ) ) {
		//default (global) hero section data
		$default_hs_data = iki_toolkit_get_default_hero_section_data();

		//GET theme (global) hero section options
		$theme_hs_options = iki_toolkit()->get_customizer_option( 'theme_hero_section', array() );
		$theme_hs_options = array_replace_recursive( $default_hs_data, $theme_hs_options );

		$GLOBALS['iki_toolkit']['data']['default_parsed_hero_section'] = iki_toolkit_parse_hero_section_data( $theme_hs_options );
	}

	return $GLOBALS['iki_toolkit']['data']['default_parsed_hero_section'];
}


/**
 * Get hero section data by $type ( we get the type from location)
 *
 * @param $type
 *
 * @return array
 */
function iki_toolkit_get_hero_section_options_by_type( $type ) {

	$default_hs_data = iki_toolkit_get_default_hero_section_data();

	//default options (already parsed) -global options for all
	$theme_hs_options = iki_toolkit_get_theme_hero_section_data();

	//GET hero section for type (default for type)
	$posts_hs_options = iki_toolkit()->get_customizer_option( "{$type}_hero_section", false );
	if ( ! $posts_hs_options ) {
		return $theme_hs_options;
	} else {
		// parse data
		$posts_hs_options = array_replace_recursive( $default_hs_data, $posts_hs_options );
		$posts_hs_options = iki_toolkit_parse_hero_section_data( $posts_hs_options );

		// merge with theme (global) hero section options
		return array_replace_recursive( $theme_hs_options, $posts_hs_options );
	}

}


/**
 * Get hero data for archive location
 *
 * @param $type string location
 * @param $id string | int location id  (category id , tag id etc..)
 *
 * @return array
 */
function iki_toolkit_get_archive_hero_section_data( $type, $id ) {

	//default hero section data for single
	$default_hs_data = iki_toolkit_get_default_hero_section_data();

	//get default hero section options for categories , tags...
	switch ( $type ) {
		case 'iki_team_member_cat':
		case 'iki_team_member_tag':
			$arhive = 'archive_iki_team_member';
			break;

		case 'iki_portfolio_cat':
		case 'iki_portfolio_tag':
			$arhive = 'archive_iki_portfolio';
			break;
		case 'product_cat':
			$arhive = 'product_cat';
			break;
		default:
			$arhive = 'category';

	}
	$posts_hs_options = iki_toolkit_get_hero_section_options_by_type( $arhive );


	// GET hero section for current (single) term
	$custom_post_hs_options = iki_toolkit()->get_term_option( $id, $type, "hero_section", false );
	if ( ! $custom_post_hs_options ) {
		return $posts_hs_options;
	} else {
		// parse data
		$custom_post_hs_options = array_replace_recursive( $default_hs_data, $custom_post_hs_options );
		$custom_post_hs_options = iki_toolkit_parse_hero_section_data( $custom_post_hs_options );

		//final merge
		// merge default post options and single post option
		// result is ready to be used throughout the theme
		return array_replace_recursive( $posts_hs_options, $custom_post_hs_options );
	}

}

/**
 * Get hero section data for single post (any post type)
 *
 * @param $type string post type
 * @param $id string|int post id
 *
 * @return array
 */
function iki_toolkit_get_post_hero_section_data( $type, $id ) {

	//get default hero section options
	$posts_hs_options = iki_toolkit_get_hero_section_options_by_type( $type );

	// GET hero section for current (single) post
	$custom_post_hs_options = iki_toolkit()->get_post_option( $id, 'hero_section', false );
	// parse data
	if ( ! $custom_post_hs_options ) {
		return $posts_hs_options;
	} else {
		$custom_post_hs_options = iki_toolkit_parse_hero_section_data( $custom_post_hs_options );
		//final merge
		// merge default post options and single post option
		// result is ready to be used throughout the theme
		return array_replace_recursive( $posts_hs_options, $custom_post_hs_options );
	}


}


/**
 * Get hero data strictly by location  (blog ,404, search etc from customizer)
 *
 * @param $location string location
 *
 * @return array
 */
function iki_toolkit_get_hero_section_by_location_data( $location ) {

	//get custom blog 404 etc options
	$location_options = iki_toolkit()->get_customizer_option( "{$location}_hero_section", false );


	if ( ! $location_options ) {

		//theme hero section options (default)
		$location_options = iki_toolkit_get_theme_hero_section_data();

	} else {
		// parse data
		$location_options = iki_toolkit_parse_hero_section_data( $location_options );
		//theme hero section options (default)
		$theme_hs_options = iki_toolkit_get_theme_hero_section_data();

		$location_options = array_replace_recursive( $theme_hs_options, $location_options );
	}

	return $location_options;

}


/**
 * Setup hero section classes
 *
 * @param string $class
 */
function iki_toolkit_hero_section_class( $class = '' ) {
	$c = explode( ' ', $class );
	$c = apply_filters( 'iki_toolkit_hero_section_class', $c );
	$c = Iki_Toolkit_Utils::sanitize_html_class_array( $c );
	printf( ' class="%1$s" ', $c );

}

/**
 * Setup html data attribute for hero section element
 *
 * @param array $data
 */
function iki_toolkit_hero_section_data( $data = array() ) {

	$data = apply_filters( 'iki_toolkit_hero_section_html_data_attr', $data );

	if ( ! empty( $data ) ) {
		echo ' data-iki-hero=' . json_encode( $data );
	}

}

/**
 * Setup data for hero section - featured post
 *
 * @param $data
 *
 * @return mixed
 */
function _filter_iki_toolkit_hero_section_data_attr( $data ) {

	if ( isset( $GLOBALS['iki_toolkit']['data']['featured_post_cookie'] ) &&
	     isset( $GLOBALS['iki_toolkit']['data']['featured_post_skip'] ) ) {
		$data['feat_c'] = $GLOBALS['iki_toolkit']['data']['featured_post_cookie'];
		$data['feat_s'] = $GLOBALS['iki_toolkit']['data']['featured_post_skip'];
	}


	return $data;
}


if ( ! function_exists( '_action_iki_toolkit_hero_section_scroll_down_indicator' ) ) {
	/**
	 * Setup scroll down indicator button inside hero section
	 */
	function _action_iki_toolkit_hero_section_scroll_down_indicator() {

		$hero_layout = iki_toolkit()->get_hero_section();
		$hero_layout = $hero_layout['layout'];

		if ( 'full' == $hero_layout['height'] && $hero_layout['scroll_indicator'] && ! wp_is_mobile() ) {
			// position
			$position = $hero_layout['scroll_indicator_position'];

			if ( 'left' !== $position ) {
				//we have right or center
				$position = 'iki-pos-bottom-' . $position;
			}

			printf( '<div id="iki-scroll-down-indicator"  class="iki-scroll-down-indicator %1$s">
			<a href="#" title="%2$s" class="tooltip-js iki-icon"><span class="iki-indicator-txt">%3$s</span><span class="iki-icon-down" ></span></a>
			</div>',
				sanitize_html_class( $position ),
				sanitize_text_field( __( 'Scroll down', 'iki-toolkit' ) ),
				__( 'Scroll down', 'iki-toolkit' )
			);

		}
	}
}


/**
 * Setup hero section row separator svg element
 */
function _action_iki_toolkit_hero_section_separator() {

	$hero_data = iki_toolkit()->get_hero_section();

	if ( isset( $hero_data['separator'] ) && 'none' != $hero_data['separator']['design'] ) {

		$separator_data = $hero_data['separator'];
		$classes        = array();
		$classes[]      = ( 'absolute' == $separator_data['position'] ) ? 'iki-separator-abs' : '';
		$classes[]      = ( 'fixed' == $separator_data['width'] ) ? 'iki-separator-fixed' : '';
		$separator      = new Iki_Svg_Separator();
		echo $separator->get_separator( $separator_data['design'], $classes );
	}
}


if ( ! function_exists( 'iki_toolkit_print_hero_section_custom_content' ) ) {
	/**
	 * Setup custom content inside hero section
	 *
	 * @param $data null|array Data for producing custom content elements.
	 */
	function iki_toolkit_print_hero_section_custom_content( $data = null ) {

		global $post;

		if ( $post && post_password_required( $post->ID ) ) {
			return;
		}

		if ( ! is_null( $data ) ) {

			//custom content can be passed as a nested variable
			$data = ( isset( $data['custom_content'] ) ) ? $data['custom_content'] : $data;

			//bail immediately if there is no "type" property for custom content
			if ( isset( $data['type'] ) ) {

				$type                = null;
				$identification_data = iki_toolkit()->get_location_info();
				$identification      = $identification_data['id'];

				if ( ! $identification ) {
					$identification = $identification_data['location'];
				}

				$type = $data['type'];

				if ( 'none' == $type ) {
					return;
				}

				$classes = array( 'iki-custom-content-wrap' );
				if ( isset( $data['background'] ) && $data['background'] ) {

					$classes[] = 'iki-hs-background';
				}

				$style_attr = '';
				$data_attr  = array();
				if ( isset( $data['content_width'] ) ) {

					global $content_width;

					$hs_content_width     = $data['content_width'];
					$custom_width         = $data['content_custom_width'];
					$custom_content_width = '';
					switch ( $hs_content_width ) {

						case 'default' :
							$custom_content_width = $content_width . 'px';
							break;
						case 'custom':
							$custom_content_width = $custom_width;
							break;
						case '1':
							$custom_content_width = '850px';
							break;
						case '2':
							$custom_content_width = '650px';
							break;
						case '3':
							$custom_content_width = '500px';


					}

					if ( ! empty( $custom_content_width ) ) {

						$style_attr = sprintf( 'style="max-width:%1$s;"', esc_attr( $custom_content_width ) );

					}
				}

				//setup data
				if ( 'multiple_images' == $type ) {
					$data_attr['data-iki-animation'] = $data['animation'];
					$classes[]                       = 'iki-' . $type;
				}

				printf( '<div class="%1$s" %2$s %3$s >',
					Iki_Toolkit_Utils::sanitize_html_class_array( $classes ),
					$style_attr,
					Iki_Toolkit_Utils::array_to_html_attr( $data_attr ) );

				if ( 'multiple_images' == $type ) {

					$shortcode = '';
					if ( ! empty( $data['images'] ) ) {
						$ids = 'ids="';
						foreach ( $data['images'] as $image ) {
							$ids .= $image['attachment_id'] . ',';
						}
						$ids .= '"';

						$columns   = 'columns="' . $data['columns'] . '"';
						$link      = 'link="file"';
						$shortcode = sprintf( '[gallery %1$s %2$s %3$s ]', $columns, $link, $ids );
					}

					if ( ! empty( $shortcode ) ) {
						echo do_shortcode( $shortcode );
					}

				} elseif ( 'image' == $type ) {

					$image_id = null;
					$size     = $data['size'];

					if ( ! empty( $data['id']['attachment_id'] ) ) {
						$image_id = $data['id']['attachment_id'];
					}

					echo iki_featured_image_single( $size, $image_id );

				} elseif ( 'featured_image' == $type ) {

					$size = $data['size'];
					$p_id = null;
					if ( isset( $data['post_id'] ) ) {

						$p_id = $data['post_id'];

					} elseif ( ! is_null( $post ) ) {
						$p_id = $post->ID;
					}

					$image_id = get_post_thumbnail_id( $p_id );
					echo iki_featured_image_single( $size, $image_id );

				} elseif ( 'oembed' == $type ) {

					$oembed_url = $data['payload'];
					if ( ! empty( $oembed_url ) ) {

						$oembed = Iki_Toolkit_Utils::get_oembed_for_non_posts( $identification, $oembed_url );
						if ( isset( $data['oembed_orientation'] ) && 'default' != $data['oembed_orientation'] ) {

							$orientation = 'embed-responsive-' . $data['oembed_orientation'];
							$r           = sprintf( '<div class="fitvidsignore embed-responsive %1$s"> %2$s</div>',
								$orientation,
								$oembed );

						} else {
							$r = $oembed;
						}

						echo $r;
					}
				} elseif ( 'html' == $type || 'excerp' == $type ) {

					$html = $data['payload'];
					echo $html;

				} elseif ( 'content_block' == $type ) {

					$block_id = $data['id'];
					iki_print_content_block( $block_id, array(), 'custom_content' );

				} elseif ( 'wp_editor' == $type ) {

					$editor_content = $data['payload'];

					if ( ! empty( $editor_content ) ) {

						$editor_content = apply_filters( 'the_content', $editor_content );

						echo '<div class="iki-cc-wp-editor">';
						echo $editor_content;
						echo '</div>';
					}

				} elseif ( 'rev_slider' == $type ) {
					if ( ! empty( $data['alias'] ) ) {
						if ( shortcode_exists( "rev_slider" ) ) {
							putRevSlider( $data['alias'] );
						}

					}
				} elseif ( 'asset_grid' == $type ) {

					iki_toolkit_setup_asset_grid( $data );

				}

				echo '</div>';
			}
		}
	}
}

if ( ! function_exists( '_action_iki_toolkit_hero_section_background_setup' ) ) {
	/**
	 * Setup background for hero section
	 */
	function _action_iki_toolkit_hero_section_background_setup() {
		$hero_data = iki_toolkit()->get_hero_section();
		$img_el    = '';

		$background_attributes = '';

		if ( isset( $hero_data['background'] ) && ! empty( $hero_data['background']['srcs'] ) ) {

			$background_data = $hero_data['background'];

			$background_attributes = array(
				'generate_blur'  => $background_data['generate_blur'],
				'permanent_blur' => $background_data['permanent_blur'],
				'srcs'           => $background_data['srcs'],
				'blur_strength'  => $background_data['blur_strength'],
			);

			$background_attributes = 'data-iki-background=' . json_encode( $background_attributes );
			if ( $background_data['generate_blur'] && ! empty( $background_data['srcs']['thumbnail'] ) ) {
				$img_el = sprintf( '<img id="iki-hero-thumb" class="iki-hero-thumb" src="%1$s" >',
					esc_url( $background_data['srcs']['thumbnail'] ) );
			}

		}

		printf( '<div id="iki-hero-bg" class="iki-hero-bg" %2$s>%1$s</div>',
			$img_el,
			$background_attributes );

		if ( isset( $hero_data['video_background'] ) && ! empty( $hero_data['video_background']['videoURL'] ) ) {

			//https://github.com/pupunzi/jquery.mb.YTPlayer/wiki
			$video_data = json_encode( $hero_data['video_background'] );
			printf( '<div id="iki-hero-video-bg" class="iki-hero-video-bg" data-property=\'%1$s\'></div>', $video_data );

		}

		if ( isset( $hero_data['overlay'] ) ) {

			printf( '<div id="iki-hero-overlay" class="iki-hero-overlay"></div>' );
		}
	}
}


if ( ! function_exists( 'iki_toolkit_print_hero_bg_css' ) ) {
	/**
	 * Print inline css for the hero section background,
	 * and gradient overlay
	 *
	 * @param $hero_data array Holds data needed to create custom css
	 *
	 * @return string
	 */
	function iki_toolkit_print_hero_bg_css( $hero_data ) {
		$css = '';
		$url = '';

		if ( isset( $hero_data['overlay'] ) ) {
			$css .= Iki_Toolkit_Utils::construct_css_gradient( '.iki-hero-overlay', $hero_data['overlay'] );
		}

		if ( isset( $hero_data['background'] ) ) {
			$background_data = $hero_data['background'];


			$css .= '.iki-hero-bg { ';

			if ( ! empty( $background_data['srcs'] ) ) {


				// only generate background-url CSS if we are not generating blur effect
				// because in case of blur effect we are using javascript to add background image.
				if ( ! $background_data['generate_blur'] ) {
					if ( isset( $background_data['srcs']['large'] ) ) {
						$url = $background_data['srcs']['large'];
					} elseif ( isset( $background_data['srcs']['medium'] ) ) {
						$url = $background_data['srcs']['medium'];
					}
				}
				if ( ! empty( $url ) ) {
					// if blur is false, we don't load background image via js, so do it here
					if ( ! $background_data['generate_blur'] ) {
						$css .= ' background-image:url("' . $url . '");';
					}
				} else {
					$css .= 'background-image:none;';
				}

				// we have background, so setup positioning always (doesn't matter if using blur or not)
				$css .= ' background-size:' . $background_data['size'] . ';'
				        . ' background-repeat: ' . $background_data['repeat'] . ';'
				        . ' background-position:' . $background_data['position'] . ';'
				        . ' background-attachment:' . $background_data['attachment'] . ';';

			}

			$css .= ' background-color:' . $background_data['color'] . ';';
			if ( empty( $url ) ) {
				$css .= 'background-image:none;';
			}
			$css .= ' }';

		}

		return $css;
	}
}


if ( ! function_exists( 'iki_toolkit_print_hero_section_custom_colors' ) ) {
	/**
	 * Print custom css for hero section custom text colors
	 *
	 * @param $hero_data array Data needed to create custom text colors
	 *
	 * @return string
	 */
	function iki_toolkit_print_hero_section_custom_colors( $hero_data ) {

		$css = '';


		if ( isset( $hero_data['custom_colors'] ) ) {
			$text_color = $hero_data['custom_colors'];
			$css        .= sprintf( '.iki-hero-section,
 .iki-hero-section .page-title,
 .iki-hero-section .entry-title,
 .iki-hero-section .entry-title a,
 .iki-hero-section .entry-title a:hover,
 .iki-hero-section .entry-title a:focus,
 .iki-hero-section .entry-subtitle,
 .iki-hero-section .entry-meta,
 .iki-hero-section .entry-meta a,
 .iki-hero-section .entry-meta a:hover,
 .iki-featured-notify,
 .iki-hero-section .taxonomy-description {color:%1$s;}', $text_color['text_color'] );
			$css        .= sprintf( '.iki-hero-section a { color:%1$s;}', $text_color['link_color'] );
			$css        .= sprintf( '.iki-hero-section a:hover { color:%1$s;}', $text_color['link_color_hover'] );

			$css .= '.iki-featured-notify {'
			        . 'border-bottom-color:' . $text_color['text_color'] . ';'
			        . '}';

			if ( isset( $hero_data['add_read_more_link'] ) ) {

				//only if read more link is present
				$css .= sprintf( '.iki-feat-read-more a,
				.iki-feat-read-more a:hover,
				.iki-feat-read-more a:focus{
				color:%1$s;
				}', $text_color['text_color'] );
				$css .= sprintf( '.iki-feat-read-more a:after{
			background-color:%1$s;
			}', $text_color['text_color'] );

			}
		}
		if ( isset( $hero_data['featured_sign_colors'] ) ) {

			$css .= sprintf( '.iki-featured-notify{
			color:%1$s;
			background-color:%2$s;
			}',
				$hero_data['featured_sign_colors']['text_color'],
				$hero_data['featured_sign_colors']['bg_color'] );

		}

		return $css;
	}
}

if ( ! function_exists( '_action_iki_toolkit_hero_section_custom_css' ) ) {

	/**
	 * Generate custom css for hero section
	 *
	 * @param $css
	 *
	 * @return array
	 */
	function _action_iki_toolkit_hero_section_custom_css( $css ) {

		$hero_data = iki_toolkit()->get_hero_section();

		if ( $hero_data && defined( 'FW' ) ) {

			$css[] = iki_toolkit_print_hero_bg_css( $hero_data );
			$css[] = iki_toolkit_print_hero_section_custom_colors( $hero_data );

		}

		return $css;
	}
}

if ( ! function_exists( '_filter_iki_toolkit_hero_section_class' ) ) {
	/**
	 * Setup appropriate hero section classes
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	function _filter_iki_toolkit_hero_section_class( $classes ) {

		$hero_layout = iki_toolkit()->get_hero_section();
		$hero_layout = $hero_layout['layout'];
		//return early if there is no hero section
		if ( ! $hero_layout ) {
			return $classes;
		}
		$hero_data = iki_toolkit()->get_hero_section();

		$classes[] = 'iki-hs-' . $hero_layout['horizontal_aligment'] . '-' . $hero_layout['vertical_aligment'];

		$classes[] = 'iki-hs-v-' . $hero_layout['vertical_aligment'];
		$classes[] = 'iki-hs-h-' . $hero_layout['horizontal_aligment'];

		$classes[] = ( isset( $hero_data['background']['generate_blur'] ) &&
		               $hero_data['background']['generate_blur'] ) ? 'iki-hs-blur' : '';

		if ( ! empty( $hero_data['custom_content'] ) ) {

			$custom_content_data = $hero_data['custom_content'];

			$classes[] = 'iki-hs-content-' . $custom_content_data['type'];
			$classes[] = ( $custom_content_data['remove_spacing'] ) ? 'iki-hs-rm-spacing' : '';
			$classes[] = ( $custom_content_data['background'] ) ? 'iki-hs-bg' : '';
		}

		if ( ! $hero_data['layout']['title_inside'] ) {
			$classes[] = 'iki-hs-t-outside';
		}

		if ( isset( $hero_data['custom_colors'] ) ) {

			if ( isset( $hero_data['custom_colors']['remove_text_shadow'] )
			     && $hero_data['custom_colors']['remove_text_shadow'] ) {
				$classes[] = 'iki-hs-no-shadow';
			}

		}

		if ( isset( $hero_data['featured_post'] ) ) {
			$classes[] = 'iki-hs-featured';
			$classes[] = 'iki-hs-featured-' . $hero_data['featured_post']['type'];
			$classes[] = 'iki-hs-featured-' . $hero_data['featured_post']['id'];
		}

		return $classes;
	}
}


/**
 * Setup hero content classes
 *
 * @param $class
 */
function iki_toolkit_hero_content_class( $class ) {
	$classes = array( $class );
	$classes = apply_filters( 'iki_hero_content_class', $classes );
	$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );
	echo 'class="' . $classes . '"';
}

if ( ! function_exists( 'iki_toolkit_parse_custom_content_data' ) ) {

	/**
	 * Parse data for custom content to be used by javascript
	 *
	 * @param $data
	 *
	 * @return array
	 */
	function iki_toolkit_parse_custom_content_data( $data ) {

		$r      = array();
		$export = array();

		$content_type = $data['chosen_content'];

		$export['type'] = $content_type;

		if ( 'none' != $content_type ) {

			$custom_content_data = $data[ $content_type ];

			if ( 'gallery' == $content_type ) {

				$export['gallery'] = array(
					'animation' => $custom_content_data['animation']
				);

			}

		}

		$custom_content_data['type'] = $content_type;
		$r['custom_content']         = $custom_content_data;
		$r['export']                 = $export;

		return $r;

	}
}
if ( ! function_exists( 'iki_toolkit_extract_custom_content' ) ) {
	/**
	 * Helper function for custom content
	 *
	 * @param $data
	 *
	 * @return null
	 */
	function iki_toolkit_extract_custom_content( $data ) {
		if ( ! empty( $data['custom_content'] ) ) {
			return $data['custom_content'];
		}

		return null;
	}
}

if ( ! function_exists( 'iki_print_page_info' ) ) {
	/**
	 * Print hero section content, with appropriate modules
	 */
	function iki_toolkit_print_hero_section_content() {

		$name          = 'post';
		$location_data = iki_toolkit()->get_location_info();
		$iki_hero_data = iki_toolkit()->get_hero_section();

		$have_featured_post = isset( $iki_hero_data['featured_post'] ) ? true : false;

		if ( $have_featured_post ) {
			$name = 'post-featured';//regular post categories
			$type = $iki_hero_data['featured_post']['type'];

			switch ( $type ) {
				case 'iki_portfolio':
					$name = 'portfolio-featured';
					break;
				case 'iki_team_member':
					$name = 'team-featured';
					break;
				case 'product':
					$name = 'product-featured';
					break;
				case 'post':
					$name = 'post-featured';
			}
		} else {

			if ( 'archive' == $location_data['location'] ) {
				$name = 'archive';
			} elseif ( 'iki_portfolio' == $location_data['type'] ) {
				$name = 'portfolio';
			} elseif ( 'iki_team_member' == $location_data['type'] ) {
				$name = 'team-member';
			} elseif ( 'page' == $location_data['type'] ) {
				$name = 'page';
			} elseif ( 'blog' == $location_data['location'] ) {
				$name = ( $have_featured_post ) ? 'post-featured' : 'blog';
			} elseif ( 'not_found' == $location_data['location'] ) {
				$name = 'not-found';
			} elseif ( 'search' == $location_data['location'] ) {
				$name = 'search';
			} elseif ( 'product' == $location_data['type'] ) {
				$name = 'product';
			} elseif ( 'shop' == $location_data['type'] ) {
				$name = 'shop';
			}
		}

		iki_toolkit_get_template( '/hero-section/hs-' . $name . '.php' );
	}
}


/**
 * Maybe print hero section social icons
 *
 * @param array|null $iki_hero_data Hero data
 * @param bool $echo echo result
 *
 * @return string
 */
function iki_toolkit_maybe_print_hero_section_social_icons( $iki_hero_data, $echo = true ) {

	$r = '';
	if ( $iki_hero_data ) {

		$iki_title_inside_hero = $iki_hero_data['layout']['title_inside'];

		if ( $iki_title_inside_hero && $iki_hero_data['use_social_icons'] ) {

			$iki_icons_design = isset( $iki_hero_data['social_design'] ) ? $iki_hero_data['social_design'] : null;

			$r = iki_toolkit_print_share_icons( $iki_icons_design, null, true, 'iki-hs-sharing' );
		}

	}
	if ( $echo ) {
		echo $r;
	} else {
		return $r;
	}

}

if ( ! function_exists( 'iki_toolkit_print_hero_section_read_more_link' ) ) {
	/**
	 * Print read more link for featured posts in hero section
	 *
	 * @param string|int $post_id id of the post to link to
	 * @param string $link_text Text for the read more link
	 */
	function iki_toolkit_print_hero_section_read_more_link( $post_id, $link_text ) {

		$link_text = apply_filters( 'iki_hero_section_featured_post_read_more_link_text', $link_text, $post_id );

		$data = array(
			'link_text' => $link_text,
			'post_id'   => $post_id,
			'classes'   => array()
		);


		$design            = iki_toolkit()->get_post_option( $post_id, 'featured_link_design', 'hollow' );
		$data['classes'][] = 'iki-' . $design;

		if ( 'default' != $design && 'hollow' != $design ) {
			//if not default - always add iki-hollow
			$data['classes'][] = 'iki-hollow';
		}
		iki_toolkit_get_template( 'hero-section/read-more-btn.php', $data );
	}
}


/**
 * Get and print hero section template
 */
function iki_toolkit_print_hero_section() {

	iki_toolkit_get_template( 'hero-section/hero-layout.php' );

}

if ( ! function_exists( 'iki_toolkit_print_featured_title_and_subtitle' ) ) {
	/**
	 * Print title and subtitle for featured post
	 *
	 * @param int $id post id
	 */
	function iki_toolkit_print_featured_title_and_subtitle( $id ) {

		$hero_data     = iki_toolkit()->get_hero_section();
		$post_title    = get_the_title( $id );
		$post_subtitle = iki_toolkit_post_subtitle( '', '', false, $id );
		$post_link     = esc_attr( get_permalink( $id ) );
		$spacer        = '<span class="iki-hs-spacer"></span>';

		if ( isset( $hero_data['text_above_title'] ) ) {
			printf( '<p class="iki-hs-above-title">%1$s</p>', $hero_data['text_above_title'] );
		}
		if ( $post_link && ! $hero_data['remove_title'] && ! empty( $post_title ) ) {
			printf( '<h1 class="entry-title"><a href="%2$s">%1$s</a></h1>', esc_html( $post_title ), $post_link );
		} else {
			echo $spacer;
		}

		if ( $post_link && ! $hero_data['remove_subtitle'] && ! empty( $post_subtitle ) ) {
			printf( '<h3 class="entry-subtitle"><a href="%2$s">%1$s</a></h3>', $post_subtitle, $post_link );
		} else {
			echo $spacer;
		}
	}
}

if ( ! function_exists( 'iki_toolkit_print_featured_post_sign' ) ) {


	/**
	 * Print featured sign for featured posts inside hero section
	 *
	 * @param string $text Text to be printed
	 * @param null $post_id featured post ID
	 * @param bool $echo to echo or return as string
	 *
	 * @return string|null
	 */

	function iki_toolkit_print_featured_post_sign( $text, $post_id = null, $echo = true ) {

		$text = apply_filters( 'iki_hero_section_featured_post_sign_text', $text );

		if ( $post_id ) {
			$custom_text = iki_toolkit()->get_post_option( $post_id, 'featured_sign_text', '' );
			$custom_text = trim( $custom_text );
			if ( ! empty( $custom_text ) ) {
				$text = $custom_text;
			}
		}

		$r = sprintf( '<div class="iki-featured-notify"><span>%1$s</span></div>', sanitize_text_field( $text ) );

		if ( $echo ) {
			echo $r;

			return null;
		}

		return $r;
	}
}
