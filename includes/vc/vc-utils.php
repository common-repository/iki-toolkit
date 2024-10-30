<?php

add_filter( 'iki_toolkit_image_slider_vc', '_filter_iki_toolkit_vc_image_slider_partial', 10, 2 );
add_filter( 'iki_toolkit_post_listing_vc', '_filter_iki_toolkit_vc_post_partial', 10, 3 );
add_filter( 'iki_toolkit_post_listing_vc_slider', '_filter_iki_toolkit_vc_post_slider_partial', 10, 3 );
add_filter( 'iki_toolkit_vc_grid_design', '_filter_iki_toolkit_vc_grid_design', 10, 2 );
add_action( 'vc_load_iframe_jscss', '_action_iki_load_vc_front_end_files' );
/**
 * Get available social profile services (services are hard coded)
 * @return array
 */
function iki_toolkit_get_social_profiles() {

	return array(
		'facebook'   => '',
		'twitter'    => '',
		'linkedin'   => '',
		'instagram'  => '',
		'dribbble'   => '',
		'youtube'    => '',
		'pinterest'  => '',
		'flickr'     => '',
		'500px'      => '',
		'vk'         => '',
		'weibo'      => '',
		'reddit'     => '',
		'tumblr'     => '',
		'lastFM'     => '',
		'myspace'    => '',
		'github'     => '',
		'bitbucket'  => '',
		'behance'    => '',
		'vimeo'      => '',
		'flattr'     => '',
		'skype'      => '',
		'deviantart' => '',
		'soundcloud' => '',
		'mixcloud'   => ''
	);
}

/**
 * Get available share services (services are hard coded)
 * @return array
 */
function iki_toolkit_get_share_services() {

	return array(
		'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=',
		'twitter'   => 'https://twitter.com/intent/tweet?url=',
		'linkedin'  => 'https://www.linkedin.com/shareArticle?mini=true&url=',
		'vk'        => 'https://vk.com/share.php?url=',
		'weibo'     => 'https://service.weibo.com/staticjs/weiboshare.html?url=',
		'pinterest' => 'https://pinterest.com/pin/create/button?url=',
		'reddit'    => 'https://www.reddit.com/submit?url=',
//        'tumblr' => 'http://www.tumblr.com/share/link?url=',
		'tumblr'    => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=',
		'buffer'    => 'https://bufferapp.com/add?url=',
		'digg'      => 'https://digg.com/submit?phase=2&url='
	);
}

/**
 * Get default social icons design
 * @return array
 */
function iki_toolkit_get_default_social_design() {

	return array(
		'fg'            => '',
		'bg'            => '',
		'rounded'       => '0',
		'spread'        => '0',
		'design'        => 'dark',
		'chosen_design' => 'pre_made'
	);
}

/**
 * Print social icons
 *
 * @param $profiles
 * @param string $link_title
 * @param null $design
 * @param bool $echo
 *
 * @return string
 */
function iki_toolkit_print_social_profiles( $profiles, $link_title = '', $design = null, $echo = true ) {
	$r = iki_toolkit_build_service_links( $profiles, $link_title, $design );
	if ( $echo ) {
		echo $r;
	} else {
		return $r;
	}
}

/**
 * Build html for service / social links
 *
 * @param $profiles
 * @param string $link_title
 * @param null $design
 *
 * @return string
 */
function iki_toolkit_build_service_links( $profiles, $link_title = '', $design = null ) {

	$ul_classes = array( 'post-social-share ' );
	$li_style   = '';

	$number_of_profiles = count( $profiles );

	if ( ! $design ) {
		$design = iki_toolkit_get_default_social_design();
	}

	$design       = Iki_Toolkit_Utils::parse_post_sharing_design( $design );
	$ul_classes[] = $design['class'];

	if ( $design['spread'] == '1' ) {

		// do the spread.
		$design['rounded']   = '0';
		$ul_classes[]        = 'iki-spread-social';
		$single_spread_width = sprintf( 'width:%1$s%%;', round( 100 / $number_of_profiles, 4 ) );
		$li_style            = sprintf( 'style="%1$s"', $single_spread_width );

	}

	( $design['rounded'] == '1' ) ? $ul_classes[] = ' iki-round' : '';


	$r = '<ul class="' . Iki_Toolkit_Utils::sanitize_html_class_array( $ul_classes ) . '">';

	$fg = $design['fg'];
	$bg = $design['bg'];

	if ( ! empty( $fg ) ) {

		$color = 'color:' . $fg . ';';

		if ( 'custom_symbol' == $design['chosen_design'] ) {
			$color .= 'border-color:' . $fg . ';';
		}
		$fg = sprintf( 'style="%1$s;"', esc_attr( $color ) );
	}
	if ( ! empty( $bg ) ) {
		$bg = sprintf( 'style="background-color:%s;"', esc_attr( $bg ) );
	}


	foreach ( $profiles as $service => $url ) {

		$r .= sprintf( '<li class="iki-share-btn-wrap" %4$s data-iki-share="%1$s" ><span %3$s class="iki-sc-back iki-sc-%1$s %2$s"></span>',
			sanitize_html_class( $service ),
			sanitize_html_class( $design['class'] ),
			$bg,
			$li_style
		);

		$title = str_replace( '-', ' ', $service );
		$title = ucwords( $title );

		$r .= sprintf( '<a href="%1$s" target="_blank" %3$s title="%2$s" class="iki-share-btn"><i class="%4$s"></i></a>',
			esc_url( $url ),
			esc_attr( $link_title . ' ' . $title ),
			$fg,
			'iki-icon-' . $service
		);

		$r .= '</li>';

	}
	$r .= '</ul>';

	return $r;

}


/**
 * Parse data for social icons design
 *
 * @param null|array $design icon design data
 *
 * @return  array parsed design
 */
function iki_toolkit_parse_post_sharing_design( $design = null ) {


	if ( ! isset( $design ) ) {

		$design = iki_toolkit_get_default_social_design();

	} elseif ( isset( $design['chosen_design'] ) && isset( $design[ $design['chosen_design'] ] ) ) {

		$chosen = $design['chosen_design'];
		$design = $design[ $chosen ];

		$design['chosen_design'] = $chosen;

	}

	$design['fg']      = ( isset( $design['fg'] ) ) ? $design['fg'] : '';
	$design['bg']      = ( isset( $design['bg'] ) ) ? $design['bg'] : '';
	$design['spread']  = ( isset( $design['spread'] ) ) ? $design['spread'] : '';
	$design['rounded'] = ( isset( $design['rounded'] ) ) ? $design['rounded'] : '';


	$chosen          = isset( $design['chosen_design'] ) ? $design['chosen_design'] : 'custom';
	$design['class'] = isset( $design['class'] ) ? $design['class'] : '';
	if ( $chosen == 'custom_symbol' ) {


		$design['class'] = 'sc-custom-symbol';

	} elseif ( $chosen == 'pre_made' ) {
		$design['design'] = str_replace( 'classic-', '', $design['design'] );
		$design['class']  = 'sc-' . $design['design'];
	} elseif ( $chosen == 'custom_background' ) {

		$design['class'] = 'sc-custom-background';

	}

	return $design;
}

/**
 * Normalize icon data.
 *
 * @param $data
 *
 * @return mixed
 */
function iki_toolkit_normalize_vc_icon_data( $data ) {

	$data['chosen_design'] = ( isset( $data['chosen_design'] ) ) ? $data['chosen_design'] : 'pre_made';
	$data['fg']            = ( isset( $data['fg'] ) ) ? $data['fg'] : '';
	$data['bg']            = ( isset( $data['bg'] ) ) ? $data['bg'] : '';
	$data['design']        = ( isset( $data['design'] ) ) ? $data['design'] : 'dark';
	$data['spread']        = ( isset( $data['spread'] ) ) ? $data['spread'] : 0;
	$data['rounded']       = ( isset( $data['rounded'] ) ) ? $data['rounded'] : 0;

	return $data;
}

/**
 * Print share icons.
 *
 * @param null $design
 * @param null $services
 * @param bool $echo
 * @param string $class additional classes
 *
 * @return string
 */
function iki_toolkit_print_share_icons( $design = null, $services = null, $echo = true, $class = '' ) {
	global $wp;
	$design = iki_toolkit_parse_post_sharing_design( $design );

	$link_title = _x( 'Share on ', 'Text for the link sharing button ', 'iki-toolkit' );

	$current_url = esc_url( home_url( $wp->request ) );
	$share_links = array();

	if ( ! $services ) {
		$services = iki_toolkit()->get_share_services();
	}
	foreach ( $services as $service => $url ) {
		$share_links[ $service ] = $url . $current_url;
	}

	$class = ' post-sharing ';
	$r     = sprintf( '<div class="%1$s">', $class );
	$r     .= iki_toolkit_build_service_links( $share_links, $link_title, $design );
	$r     .= '</div>';

	if ( $echo ) {
		echo $r;
	} else {
		return $r;
	}

}


/**
 * Get custom link attributes
 *
 * @param $custom_link array - link parameters value
 * @param $custom_classes string - custom class value
 *
 * @return array
 */
function iki_toolkit_vc_get_custom_link_attributes( $custom_link = array(), $custom_classes = '' ) {
	$attributes = array();

	if ( ! empty( $custom_link ) ) {
		$link = function_exists( 'vc_build_link' ) ? vc_build_link( $custom_link ) : array();

		if ( ! empty( $link ) ) {
			if ( ! empty( $custom_classes ) ) {
				$attributes[] = 'class="' . esc_attr( $custom_classes ) . '"';
			}

			$attributes[] = 'href="' . esc_url( trim( $link['url'] ) ) . '"';

			if ( ! empty( $link['target'] ) ) {
				$attributes[] = 'target="' . esc_attr( trim( $link['target'] ) ) . '"';
			}

			if ( ! empty( $link['title'] ) ) {
				$attributes[] = 'title="' . esc_attr( trim( $link['title'] ) ) . '"';
			}

			if ( ! empty( $link['rel'] ) ) {
				$attributes[] = 'rel="' . esc_attr( trim( $link['rel'] ) ) . '"';
			}
		}
	}

	return $attributes;
}

/**
 * Build "link" html element from wpbakery page builder link params
 *
 * @param $tag_params array link parametheres
 * @param $text string link text
 *
 * @return string
 */
function iki_toolkit_vc_build_link_tag( $tag_params, $text ) {
	$r = '';

	if ( is_array( $tag_params ) ) {

		$r = implode( ' ', $tag_params );
		$r = sprintf( '<a %1$s>%2$s</a>', $r, $text );
	}

	return $r;
}


/**
 * Get media query font size options
 *
 * @param $prefix
 * @param $group_name
 * @param null $u_media_queries
 * @param null $u_font_sizes
 *
 * @return array
 */
function iki_toolkit_vc_get_mq_font_sizes_options( $prefix, $group_name, $u_media_queries = null, $u_font_sizes = null ) {

	$media_queries = array(
		'small'  => '480px',
		'medium' => '600px',
		'large'  => '992px'
	);

	$font_sizes = array(
		'small'  => '12px',
		'medium' => '14px',
		'large'  => '14px'
	);

	if ( $u_media_queries ) {
		$media_queries = array_merge( $media_queries, $u_media_queries );
	}

	if ( $u_font_sizes ) {
		$font_sizes = array_merge( $font_sizes, $u_font_sizes );
	}

	return array(

		array(
			"type"        => "textfield",
			"admin_label" => true,
			"heading"     => __( "Small screen", 'iki-toolkit' ),
			"param_name"  => "{$prefix}font_size_small",
			"value"       => $font_sizes['small'],
			"description" => sprintf( __( 'Active as long as the minimum screen width is %1$s ', 'iki-toolkit' ), $media_queries['small'] ),
			'group'       => "{$group_name}"

		),

		array(
			"type"        => "textfield",
			"admin_label" => true,
			"class"       => "",
			"heading"     => __( "Medium screen", 'iki-toolkit' ),
			"param_name"  => "{$prefix}font_size_medium",
			"value"       => $font_sizes['medium'],
			"description" => sprintf( __( 'Active as long as the minimum screen width is %1$s ', 'iki-toolkit' ), $media_queries['medium'] ),
			'group'       => "{$group_name}"

		),

		array(
			"type"        => "textfield",
			"heading"     => __( "Large screen", 'iki-toolkit' ),
			"param_name"  => "{$prefix}font_size_large",
			"admin_label" => true,
			"value"       => $font_sizes['large'],
			"description" => sprintf( __( 'Active as long as the minimum screen width is %1$s ', 'iki-toolkit' ), $media_queries['large'] ),
			'group'       => "{$group_name}"

		)
	);
}


/**
 * Set media query attributes for shortcodes
 *
 * @param $atts
 * @param $u_font_sizes
 * @param string $prefix
 */
function iki_toolkit_vc_set_media_query_atts( &$atts, $u_font_sizes, $prefix = '' ) {

	$custom_font_size = false;

	$font_sizes = array(
		'small'  => '12px',
		'medium' => '14px',
		'large'  => '14px'
	);


	if ( $u_font_sizes ) {
		$font_sizes = array_merge( $font_sizes, $u_font_sizes );
	}

	if ( isset( $atts["{$prefix}font_size_small"] ) && ! empty( $atts["{$prefix}font_size_small"] ) ) {
		$custom_font_size = true;
	} else {
		$atts["{$prefix}font_size_small"] = $font_sizes["small"];
	}

	if ( isset( $atts["{$prefix}font_size_medium"] ) && ! empty( $atts["{$prefix}font_size_medium"] ) ) {
		$custom_font_size = true;
	} else {

		$atts["{$prefix}font_size_medium"] = $font_sizes["medium"];
	}

	if ( isset( $atts["{$prefix}font_size_large"] ) && ! empty( $atts["{$prefix}font_size_large"] ) ) {
		$custom_font_size = true;
	} else {
		$atts["{$prefix}font_size_large"] = $font_sizes["large"];
	}


	$atts["{$prefix}custom_font_size"] = $custom_font_size;

}


/**
 * Try to get theme taxonomy colors
 * Only valid if theme supports custom taxonomy colors
 *
 * @param $term_id
 * @param $taxonomy
 * @param $atts
 * @param $layout
 * @param $prefix
 *
 * @return string
 */
function iki_toolkit_vc_get_taxonomy_colors( $term_id, $taxonomy, $atts, $layout, $prefix ) {

	$r = '';

	if ( $atts["{$prefix}post_categories"] ) {

		$tax_color    = '';
		$tax_color_bg = '';

		if ( $atts["{$prefix}tax_color_source"] && 'custom' == $atts["{$prefix}tax_color_source"] ) {

			// do custom css color source
			$tax_color = ( isset( $atts["{$prefix}tax_color"] ) ) ? 'color:' . $atts["{$prefix}tax_color"] . ';' : '';

			$tax_color_bg = ( isset( $atts["{$prefix}tax_color_bg"] ) ) ? 'background-color:' . $atts["{$prefix}tax_color_bg"] . ';' : '';

		} elseif ( $atts["{$prefix}tax_color_source"] && 'from_theme' == $atts["{$prefix}tax_color_source"] ) {

			$default_theme_color    = $GLOBALS['iki_toolkit_admin']['colors']['buttons']['color'];
			$default_theme_bg_color = $GLOBALS['iki_toolkit_admin']['colors']['buttons']['color_bg'];

			$tax_color = sprintf( 'color:%1$s;', iki_toolkit()->get_term_option( $term_id,
				$layout['tax_name'],
				'tax_color',
				$default_theme_color ) );

			$tax_color_bg = sprintf( 'background-color:%1$s;', iki_toolkit()->get_term_option( $term_id,
				$layout['tax_name'],
				'tax_color_bg',
				$default_theme_bg_color ) );

		}

		if ( ! empty( $tax_color ) || ! empty( $tax_color_bg ) ) {
			$r .= sprintf( 'style="%1$s %2$s"', sanitize_text_field( $tax_color ), sanitize_text_field( $tax_color_bg ) );
		}
	}

	return $r;
}

/**
 * Post post term html element as string
 *
 * @param $terms
 * @param $atts
 * @param $layout
 * @param $prefix
 */
function iki_toolkit_vc_the_post_listing_term( $terms, $atts, $layout, $prefix ) {

	if ( ! empty( $terms ) ) { ?>
		<div class="iki-vc-post-term-wrap">
			<a <?php echo iki_toolkit_vc_get_taxonomy_colors( $terms[0]['id'], $layout['tax_name'], $atts, $layout, $prefix ) ?>
				href="<?php echo esc_url( $terms[0]['link'] ); ?>"
				class="iki-vc-post-term"><?php echo esc_html( $terms[0]['name'] ); ?></a>
		</div>
	<?php }
}

/**
 * Maybe print video sign for the post
 *
 * @param $post_id
 *
 * @return string
 */
function iki_toolkit_vc_maybe_print_video_sign( $post_id ) {
	$r = '';
	if ( 'video' == get_post_format( $post_id ) ) {
		$r = '<span class="iki-vc-icon-video iki-icon-video-2"></span>';
	}

	return $r;
}


/**
 * Get image size in relation to orientation
 *
 * @param string $orientation image orientation
 *
 * @return string
 */
function iki_toolkit_vc_image_orientation_to_size( $orientation ) {


	switch ( $orientation ) {
		case 'portrait':
			$r = 'grid_2_portrait';
			break;
		case 'landscape':
			$r = 'grid_2_landscape';
			break;
		case 'landscape_stripe':
			$r = 'grid_2_landscape';
			break;
		default:
			$r = 'grid_2_square';
	}

	return $r;
}

function iki_toolkit_vc_get_responsive_class( $orientation ) {

	switch ( $orientation ) {
		case 'portrait':
			$r = 'portrait';
			break;
		case 'landscape':
			$r = 'landscape';
			break;
		case 'landscape_stripe':
			$r = 'landscape_stripe';
			break;
		default:
			$r = 'square';
	}

	return 'embed-responsive-' . $r;
}

/**
 * Print simple horizontal line (spacing)
 * @return string
 */
function iki_toolkit_vc_print_horizontal_line() {
	return '<div class="iki-vc-post-spacing"></div>';
}


/**
 * Get appropriate post slider partial
 *
 * @param $path
 * @param $layout
 * @param $atts
 *
 * @return string
 */
function _filter_iki_toolkit_vc_post_slider_partial( $path, $layout, $atts ) {

	global $post;

	$is_woo_product = ( $post && 'product' == $post->post_type );

	if ( 'side' == $layout['image_layout'] ) {

		if ( $is_woo_product ) {

			$path = 'vc/post-slider/post-slider-woo.php';
		} else {
			$path = 'vc/post-slider/post-slider.php';
		}

	} else {
		if ( $is_woo_product ) {

			$path = 'vc/post-slider/post-slider-full-image-woo.php';
		} else {
			$path = 'vc/post-slider/post-slider-full-image.php';
		}

	}

	return $path;
}

/**
 * Get appropriate post slider partial
 *
 * @param $path
 * @param $layout
 * @param $atts
 *
 * @return string
 */
function _filter_iki_toolkit_vc_post_partial( $path, $layout, $atts ) {

	if ( 'side' == $layout['image_layout'] ) {
		$path = 'vc/post-listing/post-listing.php';

	} else {

		$path = 'vc/post-listing/post-listing-full-image.php';

	}

	return $path;
}


/**
 * Get appropriate image slider partial
 *
 * @param $path
 * @param $atts
 *
 * @return string
 */
function _filter_iki_toolkit_vc_image_slider_partial( $path, $atts ) {

	$path = 'vc/image-slider.php';

	return $path;
}


/**
 * Create image html link string
 *
 * @param $link
 * @param string $classes
 *
 * @return string
 */
function iki_toolkit_image_custom_link( $link, $classes = '' ) {

	$link     = esc_url( $link );
	$img_link = sprintf( '<a class="iki-img-link" href="%1$s"></a>', $link );

	$result = sprintf( '<div class="iki-lb-btn %2$s">%1$s<span class="iki-popup-btn iki-view-larger iki-icon-link"></span></div>',
		$img_link,
		sanitize_html_class( $classes ) );


	return $result;
}

/**
 * Create image attachment html link string
 *
 * @param $image_id
 * @param string $classes
 *
 * @return string
 */
function iki_toolkit_image_attachment_link( $image_id, $classes = '' ) {

	$result = '';

	$img_link = get_permalink( $image_id );
	if ( $img_link ) {
		$result = iki_toolkit_image_custom_link( $img_link, $classes );
	}


	return $result;
}


/**
 * Create lightbox launch button html string
 *
 * @param $image_id
 * @param string $img_size
 * @param array $classes additionall classes
 * @param string $hover_element what element to print on hover (lightbox symbol or image caption)
 *
 * @return string
 */
function iki_toolkit_lightbox_btn( $image_id, $img_size = 'large', $classes = array(), $hover_element = 'symbol' ) {

	$result = '';
	if ( class_exists( 'Iki_Theme' ) ) {//lightbox javascript logic is in the theme

		$img_data = wp_get_attachment_image_src( $image_id, $img_size );

		$image_post = get_post( $image_id );

		$iki_image_caption = '';
		$iki_image_desc    = '';

		if ( $image_post ) {

			$iki_image_caption = $image_post->post_excerpt;
			$iki_image_desc    = $image_post->post_content;
		}

		$lightbox_data = sprintf( 'data-mfp-src="%1$s" data-iki-w="%2$s" data-iki-h="%3$s" data-iki-lightbox data-iki-caption="%4$s" data-iki-desc="%5$s"',
			esc_url( $img_data[0] ),
			esc_attr( $img_data[1] ),
			esc_attr( $img_data[2] ),
			esc_html( $iki_image_caption ),
			esc_html( $iki_image_desc )
		);

		$symbol_or_caption = '';
		if ( 'symbol' == $hover_element ) {

			$symbol_or_caption = '<span class="iki-popup-btn iki-view-larger iki-icon-eye"></span>';
		} else {
			//lets get the image caption for the hover element
			if ( ! empty( $iki_image_caption ) ) {
				$symbol_or_caption = '<span class="iki-img-lb-cap iki-view-larger">' . $iki_image_caption . '</span>';
			}
		}

		$result = sprintf( '<div class="iki-lb-btn %2$s" %1$s>%3$s</div>',
			$lightbox_data,
			Iki_Toolkit_Utils::sanitize_html_class_array( $classes ),
			$symbol_or_caption
		);

	}

	return $result;
}


/**
 * Enqueue script when front end WPBakery page builder is active
 */
function _action_iki_load_vc_front_end_files() {

	wp_enqueue_script( 'iki-toolkit-vc-front-edit', plugin_dir_url( __FILE__ ) . '../../js/admin/vc-front-edit.min.js',
		array( 'jquery' ),
		false,
		true );
}


if ( ! function_exists( 'iki_toolkit_vc_woo_rating_loop' ) ) {

	/**
	 * Get woocommerce product rating
	 *
	 * @param bool $echo
	 *
	 * @return string|void
	 */
	function iki_toolkit_vc_woo_rating_loop( $echo = true ) {

		global $product;

		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
			return;
		}

		$r = wp_kses_post( wc_get_rating_html( $product->get_average_rating() ) );

		if ( ! $echo ) {

			return $r;
		}
		echo $r;

	}
}

if ( ! function_exists( 'iki_toolkit_vc_woo_price_loop' ) ) {
	/**
	 * Get woocommerce price
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_woo_price_loop( $echo = true ) {

		global $product;
		$r = '';

		if ( $price_html = $product->get_price_html() ) {
			$r = sprintf( '<span class="price">%1$s</span>', wp_kses_post( $price_html ) );
		}
		if ( ! $echo ) {
			return $r;
		}

		echo $r;

	}
}

if ( ! function_exists( 'iki_toolkit_vc_woo_sale_flash' ) ) {
	/**
	 * Get woocommerce sale sign
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_woo_sale_flash( $echo = true ) {

		$animation = iki_toolkit()->get_customizer_option( 'shop_sale_animation', 'none' );

		$animation = ( 'none' == $animation ) ? '' : 'iki-anim-' . $animation;

		$r = sprintf( '<span class="onsale iki-woo-onsale %2$s"><span class="iki-stamp-text">%1$s</span></span>',
			esc_html__( 'Sale!', 'iki-toolkit' ),
			sanitize_html_class( $animation )
		);
		if ( ! $echo ) {
			return $r;
		}

		echo $r;
	}

}
if ( ! function_exists( 'iki_toolkit_vc_woo_price_rating_wrap_loop' ) ) {
	/**
	 * Get woocommerce price and rating inside loop
	 *
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_woo_price_rating_wrap_loop( $echo = true ) {

		$r = sprintf( '<div class="iki-vc-woo-pr-wrap">%1$s%2$s</div>',
			iki_toolkit_vc_woo_rating_loop( false ),
			iki_toolkit_vc_woo_price_loop( false )
		);

		if ( ! $echo ) {
			return $r;
		}
		echo $r;
	}
}


if ( ! function_exists( 'iki_toolkit_vc_post_subtitle' ) ) {
	/**
	 * Get post subtitle
	 *
	 * @param $subtitle
	 * @param null $classes
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_post_subtitle( $subtitle, $classes = null, $echo = true ) {
		$r = '';

		$classes = ( $classes ) ? Iki_Toolkit_Utils::sanitize_html_class_array( $classes ) : '';

		if ( ! empty( $subtitle ) ) {
			$r = sprintf( '<h6 class="iki-vc-post-subtitle %2$s">%1$s</h6>', wp_kses_post( $subtitle ), $classes );
		}
		if ( ! $echo ) {
			return $r;
		}
		echo $r;
	}
}
if ( ! function_exists( 'iki_toolkit_vc_post_title' ) ) {
	/**
	 * Get post title
	 *
	 * @param $title
	 * @param array $classes
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_post_title( $title, $classes = array(), $echo = true ) {

		global $post;

		$r = '';

		if ( ! empty( $title ) ) {

			$classes[] = 'iki-vc-post-title';

			$r = sprintf( '<h4 class="%2$s">%1$s</h4>',
				wp_kses_post( $title ),
				Iki_Toolkit_Utils::sanitize_html_class_array( $classes )
			);
		}
		if ( ! $echo ) {
			return $r;
		}
		echo $r;
	}
}


if ( ! function_exists( 'iki_toolkit_vc_post_date' ) ) {
	/**
	 * Get post date
	 *
	 * @param $date
	 * @param null $classes
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_post_date( $date, $classes = null, $echo = true ) {
		$r = '';
		if ( $classes ) {
			$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );
		}
		if ( ! empty( $date ) ) {
			$r = sprintf( '<span class="iki-vc-post-date %2$s">%1$s</span>', sanitize_text_field( $date ), $classes );
		}
		if ( ! $echo ) {
			return $r;
		}
		echo $r;
	}
}

if ( ! function_exists( 'iki_toolkit_vc_post_comments' ) ) {
	/**
	 * Get post comments
	 *
	 * @param $comments
	 * @param null $classes
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_post_comments( $comments, $classes = null, $echo = true ) {
		$r = '';

		$classes = ( $classes ) ? Iki_Toolkit_Utils::sanitize_html_class_array( $classes ) : '';
		if ( ! empty( $comments ) ) {
			$r = sprintf( '<span class="iki-icon-comment iki-vc-post-comm %2$s">%1$s</span>', sanitize_text_field( $comments ), $classes );
		}
		if ( ! $echo ) {
			return $r;
		}
		echo $r;
	}
}

if ( ! function_exists( 'iki_toolkit_vc_post_excerpt' ) ) {
	/**
	 * Get post excerpt
	 *
	 * @param $excerpt
	 * @param null $classes
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_post_excerpt( $excerpt, $classes = null, $echo = true ) {

		$r = '';

		$classes = ( $classes ) ? Iki_Toolkit_Utils::sanitize_html_class_array( $classes ) : '';

		if ( ! empty( $excerpt ) ) {
			$r = sprintf( '<div class="iki-vc-post-excp %2$s">%1$s</div>', wp_kses_post( $excerpt ), $classes );
		}
		if ( ! $echo ) {
			return $r;
		}
		echo $r;
	}
}
if ( ! function_exists( 'iki_toolkit_vc_woo_slide_btn' ) ) {
	/**
	 * Get woocommerce product page button inside slider
	 *
	 * @param $id
	 * @param array $classes
	 */
	function iki_toolkit_vc_woo_slide_btn( $id, $classes = array() ) {

		$classes[] = 'iki-btn iki-woo-slide-btn';
		$classes   = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );

		printf( '<a class="%3$s" href="%1$s">%2$s</a> ',
			get_post_permalink( $id ),
			esc_html__( 'More Details', 'iki-toolkit' ),
			$classes
		);
	}
}

if ( ! function_exists( 'iki_toolkit_vc_date_and_comments' ) ) {
	/**
	 * Print or return post date and comments
	 *
	 * @param $date
	 * @param $comments
	 * @param array $classes
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_vc_date_and_comments( $date, $comments, $classes = array(), $echo = true ) {

		$classes[] = 'iki-vc-post-date-comm';
		$classes   = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );

		if ( empty( $date ) && empty( $comments ) ) {
			return;
		}
		$r = sprintf( '<div class="%3$s">%1$s %2$s</div>',
			iki_toolkit_vc_post_date( $date, null, false ),
			iki_toolkit_vc_post_comments( $comments, null, false ),
			$classes
		);

		if ( ! $echo ) {
			return $r;
		}
		echo $r;

	}
}


/**
 * Filter to get the design for vc grid
 *
 * @param $data
 * @param $type
 *
 * @return array|null
 */
function _filter_iki_toolkit_vc_grid_design( $data, $type ) {

	switch ( $type ) {
		case 'post':
			$data = array_flip( Iki_Toolkit_Admin_Grid_Options::get_instance()->get_blog_design() );
			break;
		case'iki_portfolio':
			$data = array_flip( Iki_Toolkit_Admin_Grid_Options::get_instance()->get_portfolio_project_design() );
			break;
		case 'iki_team_member':

			$data = array_flip( Iki_Toolkit_Admin_Grid_Options::get_instance()->get_team_design() );
			break;
	}

	return $data;
}


/**
 * Options for shortcode design
 *
 * @param null $group_name
 *
 * @return array Array of options
 */
function iki_toolkit_vc_icons_design_options( $group_name = null ) {

	return array(

		array(
			"type"        => "dropdown",
			"heading"     => __( "Icon Design", 'iki-toolkit' ),
			'admin_label' => false,
			"param_name"  => "chosen_design",
			"value"       => array(
				__( 'Pre made', 'iki-toolkit' )                  => 'pre_made',
				__( 'Icon only color', 'iki-toolkit' )           => 'custom_symbol',
				__( 'Icon and background color', 'iki-toolkit' ) => 'custom_background',
			),
			"description" => __( "Select the design for the social buttons", 'iki-toolkit' ),
			'group'       => $group_name
		),
		array(
			"type"        => "dropdown",
			"heading"     => __( "Pre made design", 'iki-toolkit' ),
			'admin_label' => false,
			"param_name"  => "design",
			"value"       => array(
				__( 'Dark', 'iki-toolkit' )                  => 'classic-dark',
				__( 'Light', 'iki-toolkit' )                 => 'classic-light',
				__( 'Social Service Color ', 'iki-toolkit' ) => 'service',
			),
			"description" => __( "Select the design for the social buttons", 'iki-toolkit' ),
			'dependency'  => array( 'element' => 'chosen_design', 'value' => array( 'pre_made' ) ),
			'group'       => $group_name
		),
		array(
			'type'       => 'colorpicker',
			'param_name' => 'fg',
			'heading'    => esc_html__( 'Icon color', 'iki-toolkit' ),
			'dependency' => array(
				'element' => 'chosen_design',
				'value'   => array( 'custom_symbol', 'custom_background' )
			),
			'group'      => $group_name
		),
		array(
			'type'       => 'colorpicker',
			'param_name' => 'bg',
			'heading'    => esc_html__( 'Icon background', 'iki-toolkit' ),
			'dependency' => array( 'element' => 'chosen_design', 'value' => array( 'custom_background' ) ),
			'group'      => $group_name
		),
		array(
			"type"        => "checkbox",
			"heading"     => __( "Rounded icons", 'iki-toolkit' ),
			'admin_label' => false,
			"param_name"  => "rounded",
			"value"       => array(
				__( 'yes', 'iki-toolkit' ) => '1',
			),
			"description" => __( 'Please note that "rounded" icon and "spread" icon options are mutually exclusive', 'iki-toolkit' ),

			'group' => $group_name
		),
		array(
			"type"        => "checkbox",
			"heading"     => __( "Stretch icons", 'iki-toolkit' ),
			'admin_label' => false,
			"param_name"  => "spread",
			"value"       => array(
				__( 'yes', 'iki-toolkit' ) => '1',
			),
			"description" => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive', 'iki-toolkit' ),
			'group'       => $group_name
		)
	);

}

