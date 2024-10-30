<?php

/**
 * Class for creating blog post listing shortcode options
 */
class Iki_Post_Options_VC {

	protected $prefix;
	protected $partial_filter = 'iki_toolkit_post_listing_vc';

	protected $media_queries = array(

		'small'  => '480px',
		'medium' => '600px',
		'large'  => '992px'
	);

	protected $font_sizes = array(
		'small'  => '12px',
		'medium' => '14px',
		'large'  => '14px'
	);

	private static $id = 0;
	protected $custom_id = 0;


	/**
	 * Generate unique id for every shortcode that is printed
	 * @return int
	 */
	private static function generate_id() {

		self::$id ++;

		return self::$id;
	}

	/**
	 * Iki_Post_Options_VC constructor.
	 *
	 * @param string $prefix
	 * @param string $partial_suffix
	 */
	public function __construct( $prefix = '', $partial_suffix = '' ) {

		$this->prefix = $prefix;
		if ( ! empty( $partial_suffix ) ) {
			$this->partial_filter = $this->partial_filter . $partial_suffix;
		}

	}

	/**
	 * Get media query for font sizes
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_media_query_font_size_options( $group_name ) {

		return iki_toolkit_vc_get_mq_font_sizes_options( $this->prefix, $group_name, $this->media_queries, $this->font_sizes );
	}

	/**
	 * Get design options
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_design_options( $group_name ) {

		return array(
			array(
				'heading'    => esc_html__( 'Taxonomy color source', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "{$this->prefix}tax_color_source",
				"value"      => array(
					__( 'Custom', 'iki-toolkit' )     => 'custom',
					__( 'From theme', 'iki-toolkit' ) => 'from_theme'
				),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => "{$this->prefix}tax_color",
				'heading'    => esc_html__( 'Taxonomy text', 'iki-toolkit' ),
				'dependency' => array( 'element' => "{$this->prefix}tax_color_source", 'value' => 'custom' ),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => "{$this->prefix}tax_color_bg",
				'heading'    => esc_html__( 'Taxonomy background', 'iki-toolkit' ),
				'dependency' => array( 'element' => "{$this->prefix}tax_color_source", 'value' => 'custom' ),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => "{$this->prefix}title_color",
				'heading'    => esc_html__( 'Title', 'iki-toolkit' ),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => "{$this->prefix}title_color_hover",
				'heading'    => esc_html__( 'Title hover', 'iki-toolkit' ),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => "{$this->prefix}text_color",
				'heading'    => esc_html__( 'Text', 'iki-toolkit' ),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => "{$this->prefix}bg_color",
				'heading'    => esc_html__( 'Background', 'iki-toolkit' ),
				'group'      => $group_name
			)
		);
	}

	/**
	 * Get query options
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_query_options( $group_name ) {

		return array(

			array(
				"type"       => "loop",
				"heading"    => __( "Posts query", 'iki-toolkit' ),
				"param_name" => "posts_query",
				'value'      => '',
				'settings'   => array(),
				'group'      => $group_name

			),
			array(
				"type"       => "checkbox",
				"heading"    => __( "Remove sticky posts", 'iki-toolkit' ),
				"param_name" => "remove_sticky_posts",
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'      => $group_name
			),

		);
	}

	/**
	 * Get layout options
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_layout_options( $group_name ) {

		return array(
			array(
				'heading'    => esc_html__( 'Post layout', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "{$this->prefix}post_layout",
				"value"      => array(
					__( 'With Image', 'iki-toolkit' ) => 'with_image',
					__( 'No image', 'iki-toolkit' )   => 'no_image',
				),
				'group'      => $group_name
			),
			array(
				'heading'    => esc_html__( 'Image layout', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "{$this->prefix}post_image_layout",
				"value"      => array(
					__( 'On the side', 'iki-toolkit' )      => 'side',
					__( 'Full width image', 'iki-toolkit' ) => 'full_width',
				),
				'dependency' => array( 'element' => "{$this->prefix}post_layout", 'value' => 'with_image' ),
				'group'      => $group_name
			),
			array(
				'heading'    => esc_html__( 'Post title location', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "{$this->prefix}post_title_location",
				"value"      => array(
					__( 'Inside image', 'iki-toolkit' ) => 'inside',
					__( 'Below image', 'iki-toolkit' )  => 'below',
					__( 'Above image', 'iki-toolkit' )  => 'above',
				),
				'dependency' => array( 'element' => "{$this->prefix}post_image_layout", 'value' => 'full_width' ),
				'group'      => $group_name
			),
			array(
				'heading'    => esc_html__( 'Post title location', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "{$this->prefix}post_title_location_2",
				"value"      => array(
					__( 'Besides image', 'iki-toolkit' ) => 'on_the_side',
					__( 'Above image', 'iki-toolkit' )   => 'above',
				),
				'dependency' => array( 'element' => "{$this->prefix}post_image_layout", 'value' => 'side' ),
				'group'      => $group_name
			),

			array(
				"type"        => "textfield",
				"admin_label" => true,
				"heading"     => __( 'Image size', 'iki-toolkit' ),
				'param_name'  => "{$this->prefix}post_image_size",
				"value"       => '',
				"description" => __( 'Choose image size, leave empty for default value', 'iki-toolkit' ),
				'dependency'  => array( 'element' => "{$this->prefix}post_layout", 'value' => 'with_image' ),
				'group'       => $group_name
			),
			array(
				'heading'    => esc_html__( 'Image orientation', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "{$this->prefix}image_orientation",
				"value"      => array(
					__( 'Square', 'iki-toolkit' )                => 'square',
					__( 'Portrait', 'iki-toolkit' )              => 'portrait',
					__( 'Landscape', 'iki-toolkit' )             => 'landscape',
					__( 'Landscape - very thin', 'iki-toolkit' ) => 'landscape_stripe',
				),
				'dependency' => array( 'element' => "{$this->prefix}post_layout", 'value' => 'with_image' ),
				'group'      => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( "Show post subtitle", 'iki-toolkit' ),
				'description' => __( 'If post subtitle is available', 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "{$this->prefix}post_subtitle",
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( "Show post excerpt", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "{$this->prefix}post_excerpt",
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( "Show post date", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "{$this->prefix}post_date",
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( "Show number of comments", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "{$this->prefix}post_comments",
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( "Show taxonomy", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "{$this->prefix}post_categories",
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"        => "textfield",
				"heading"     => __( 'Taxonomy', 'iki-toolkit' ),
				'param_name'  => "{$this->prefix}post_tax_name",
				"value"       => '',
				"description" => __( 'Choose taxonomy (default is "category" for post. Use "iki_portfolio_cat" for portfolio. Use "iki_team_member_cat" for team members', 'iki-toolkit' ),
				'dependency'  => array( 'element' => "{$this->prefix}post_categories", 'value' => '1' ),
				'group'       => $group_name
			),
		);
	}


	/**
	 * Set shortcode attributes
	 *
	 * @param $atts
	 *
	 * @return array
	 */
	public function set_attributes( $atts ) {

		$r = array(
			"{$this->prefix}post_layout"           => 'with_image',
			"{$this->prefix}post_image_layout"     => 'side',
			"{$this->prefix}post_title_location"   => 'inside',
			"{$this->prefix}post_tax_name"         => 'category',
			"{$this->prefix}post_title_location_2" => 'on_the_side',
			"{$this->prefix}image_orientation"     => 'square',
			"{$this->prefix}title_color"           => '',
			"{$this->prefix}title_color_hover"     => '',
			"{$this->prefix}text_color"            => '',
			"{$this->prefix}bg_color"              => '',

		);


		$atts = array_merge( $r, $atts );

		$atts["{$this->prefix}post_excerpt"] = isset( $atts["{$this->prefix}post_excerpt"] );

		$atts["{$this->prefix}post_date"] = isset( $atts["{$this->prefix}post_date"] );

		$atts["{$this->prefix}post_comments"] = isset( $atts["{$this->prefix}post_comments"] );

		$atts["{$this->prefix}post_subtitle"] = isset( $atts["{$this->prefix}post_subtitle"] );

		$atts["{$this->prefix}post_image_size"] = isset( $atts["{$this->prefix}post_image_size"] ) ? trim( $atts["{$this->prefix}post_image_size"] ) : "";


		$atts["{$this->prefix}tax_color_source"] = isset( $atts["{$this->prefix}tax_color_source"] ) ? $atts["{$this->prefix}tax_color_source"] : 'custom';


		$atts["{$this->prefix}post_categories"] = isset( $atts["{$this->prefix}post_categories"] );

		$atts['remove_sticky_posts'] = isset( $atts['remove_sticky_posts'] );

		iki_toolkit_vc_set_media_query_atts( $atts, $this->font_sizes, $this->prefix );

		return $atts;

	}

	/**
	 * Get css for colors
	 *
	 * @param $wrapper
	 * @param $atts
	 *
	 * @return string CSS
	 */
	public function get_css_colors( $wrapper, $atts ) {

		$r = '';

		if ( ! empty( $atts["{$this->prefix}title_color"] ) ) {

			$r .= sprintf( '%1$s .iki-vc-post-title a,
			%1$s .iki-vc-post-subtitle a{color:%2$s;}',
				$wrapper,
				$atts["{$this->prefix}title_color"] );

		}

		if ( ! empty( $atts["{$this->prefix}title_color_hover"] ) ) {

			$r .= sprintf( '%1$s .iki-vc-post-title a:hover,
			%1$s .iki-vc-post-subtitle a:hover{color:%2$s;}',
				$wrapper,
				$atts["{$this->prefix}title_color_hover"] );

		}

		if ( ! empty( $atts["{$this->prefix}bg_color"] ) ) {

			$r .= sprintf( '%1$s .iki-vc-post {background-color:%2$s;}',
				$wrapper,
				$atts["{$this->prefix}bg_color"] );

		}

		if ( ! empty( $atts["{$this->prefix}text_color"] ) ) {

			$r .= sprintf( '%1$s .iki-vc-post {color:%2$s;}',
				$wrapper,
				$atts["{$this->prefix}text_color"] );

			$r .= sprintf( '%1$s .iki-vc-p-ts-wrap-inside {color:%2$s;}',
				$wrapper,
				$atts["{$this->prefix}text_color"] );
		}

		return $r;

	}


	/**
	 * Get layout options
	 *
	 * @param $atts
	 * @param string $param_prefix
	 *
	 * @return array
	 */
	public function get_layout_params( $atts, $param_prefix = '' ) {

		if ( ! $param_prefix ) {
			$param_prefix = $this->prefix;
		}

		$r = array(
			'post_layout'       => $atts["{$param_prefix}post_layout"],
			'image_layout'      => $atts["{$param_prefix}post_image_layout"],
			'post_excerpt'      => $atts["{$param_prefix}post_excerpt"],
			'post_comments'     => $atts["{$param_prefix}post_comments"],
			'post_date'         => $atts["{$param_prefix}post_date"],
			'post_subtitle'     => $atts["{$param_prefix}post_subtitle"],
			'categories'        => $atts["{$param_prefix}post_categories"],
			'tax_name'          => trim( $atts["{$param_prefix}post_tax_name"] ),
			'image_orientation' => $atts["{$param_prefix}image_orientation"]
		);

		$r['image_size'] = ( Iki_Toolkit_Utils::has_image_size( $atts["{$param_prefix}post_image_size"] ) ) ? $atts["{$param_prefix}post_image_size"] : '';

		if ( 'full_width' == $atts["{$param_prefix}post_image_layout"] ) {

			$r['title_location'] = $atts["{$param_prefix}post_title_location"];

		} else {

			$r['title_location'] = $atts["{$param_prefix}post_title_location_2"];

		}

		//if show categories
		return $r;
	}

	/**
	 * Build posts for shortcode
	 *
	 * @param $query
	 * @param $atts
	 * @param null $limit_query
	 * @param bool $wrap_it
	 * @param array $classes classes for the wrapper
	 *
	 * @return string
	 */
	public function build_posts( $query, $atts, $limit_query = null, $wrap_it = true, $classes = array() ) {

		$r = '';

		$this->custom_id = self::generate_id();
		$query_counter   = 0;
		/**@var WP_Query $wp_query */
		$wp_query = $query[1];

		$others_layout = $this->get_layout_params( $atts );

		if ( $wp_query->post_count ) {

			while ( $wp_query->have_posts() ) {

				if ( $limit_query && $query_counter >= $limit_query ) {
					break;
				}

				$wp_query->the_post();

				$r .= $this->_build_post( $others_layout, $atts );

				$query_counter = $query_counter + 1;
			}

			if ( $wrap_it ) {
				$r = $this->build_wrapper( $atts, $r, $classes );
			}

			wp_reset_postdata();


		}

		return $r;

	}

	/**
	 * Build wrapper html for shortcode
	 *
	 * @param $atts
	 * @param string $content
	 * @param array $extra_classes additional classes for the wrapper
	 *
	 * @return string
	 */
	public function build_wrapper( $atts, $content = '', $extra_classes = array() ) {

		$others_layout = $this->get_layout_params( $atts );
		$classes       = $this->create_layout_classes( $others_layout, array( $atts['html_class'] ) );

		$extra_classes = Iki_Toolkit_Utils::sanitize_html_class_array( $extra_classes );
		$classes       .= $extra_classes;

		$css = $this->print_css( $this->custom_id, $atts );

		$r = sprintf( '<div class="%1$s">%2$s</div>%3$s', $classes, $content, $css );

		return $r;
	}

	/**
	 * Print custom css
	 *
	 * @param $custom_id
	 * @param $atts
	 *
	 * @return null|string|string[]
	 */
	protected function print_css( $custom_id, $atts ) {

		$r = '';
		$c = '';

		$c .= $this->get_custom_media_query_css( '.iki-vc-post-list-' . $custom_id, $atts );
		$c .= $this->get_css_colors( '.iki-vc-post-list-' . $custom_id, $atts );

		if ( ! empty( $c ) ) {
			$r = sprintf( '<style>%1$s</style>', $c );
			$r = preg_replace( '/\s+/', ' ', $r );
		}

		return $r;
	}

	/**
	 * Build post shortcode html elements
	 *
	 * @param $layout_params
	 * @param $atts
	 *
	 * @return string
	 */
	protected function _build_post( $layout_params, $atts ) {

		global $post;
		$post_link  = get_post_permalink();
		$excerpt    = ( $layout_params['post_excerpt'] ) ? get_the_excerpt() : '';
		$comments   = ( $layout_params['post_comments'] ) ? get_comments_number() : '';
		$video_sign = iki_toolkit_vc_maybe_print_video_sign( $post->ID );

		if ( '0' === $comments ) {
			$comments = '';
		} elseif ( ! empty( $comments ) ) {
			$comments = sprintf( '<a href="%1$s#comments" class="iki-icon-comment" >%2$s</span></a>',
				$post_link,
				$comments );
		}

		$date     = ( $layout_params['post_date'] ) ? get_the_date() : '';
		$title    = Iki_Toolkit_Utils::wrap_in_link( $post_link, get_the_title() . $video_sign );
		$subtitle = ( function_exists( 'iki_post_subtitle' ) && $layout_params['post_subtitle'] ) ? iki_post_subtitle( '', '', false ) : '';

		if ( $subtitle ) {
			$subtitle = Iki_Toolkit_Utils::wrap_in_link( $post_link, $subtitle );
		}

		//side image
		$image_size = $layout_params['image_size'];
		if ( empty( $image_size ) ) {
			$image_size = iki_toolkit_vc_image_orientation_to_size( $layout_params['image_orientation'] );
		}

		$image_html        = '';
		$should_have_image = false;
		$path              = '';
		//we have the image
		if ( 'side' == $layout_params['image_layout'] ) {

			if ( 'with_image' == $layout_params['post_layout'] ) {

				$should_have_image = true;
			}

		} else {
			//full width image
			$should_have_image = true;
		}

		if ( $should_have_image ) {
			$image_html = Iki_Toolkit_Utils::image_as_css_bg( get_post_thumbnail_id(), $image_size, array( 'iki-vc-p-img-bg' ) );
		}

		$path_to_include = apply_filters( $this->partial_filter, $path, $layout_params, $atts );

		ob_start();

		iki_toolkit_get_template(
			$path_to_include,
			array(
				'link'     => $post_link,
				'image'    => $image_html,
				'title'    => $title,
				'subtitle' => $subtitle,
				'excerpt'  => $excerpt,
				'date'     => $date,
				'comments' => $comments,
				'layout'   => $layout_params,
				'atts'     => $atts,
				'prefix'   => $this->prefix
			) );

		$r = ob_get_contents();
		ob_end_clean();

		return $r;
	}

	/**
	 * Create layout classes for posts
	 *
	 * @param $layout
	 * @param null $classes
	 *
	 * @return string
	 */
	public function create_layout_classes( $layout, $classes = null ) {

		$r = array(
			'iki-vc-post-list',
			'iki-vc-post-list-' . $this->custom_id,
			'iki-vc-p-image-' . $layout['image_layout'],
			'iki-vc-p-layout-' . $layout['post_layout'],
			'iki-vc-p-title-' . $layout['title_location']

		);

		$r[] = ( $layout['post_comments'] ) ? 'iki-vc-p-com' : '';
		$r[] = ( $layout['post_date'] ) ? 'iki-vc-p-date' : '';
		$r[] = ( $layout['post_subtitle'] ) ? 'iki-vc-p-subtitle' : '';
		$r[] = ( $layout['post_excerpt'] ) ? 'iki-vc-p-exc' : '';

		if ( ! empty( $classes ) ) {
			$r = array_merge( $r, $classes );
		}

		return Iki_Toolkit_Utils::sanitize_html_class_array( $r );
	}

	/**
	 * Get media query for fonts
	 *
	 * @param $wrapper
	 * @param $atts
	 *
	 * @return string
	 */
	public function get_custom_media_query_css( $wrapper, $atts ) {

		$prefix = $this->prefix;

		$r          = '';
		$font_small = ( empty( $atts["{$prefix}font_size_small"] ) ) ? $this->font_sizes['small'] : $atts["{$prefix}font_size_small"];

		$font_medium = ( empty( $atts["{$prefix}font_size_medium"] ) ) ? $this->font_sizes['medium'] : $atts["{$prefix}font_size_medium"];
		$font_large  = ( empty( $atts["{$prefix}font_size_large"] ) ) ? $this->font_sizes['large'] : $atts["{$prefix}font_size_large"];


		if ( $atts["{$prefix}custom_font_size"] ) {

			//print new font sizes
			$r .= $this->get_custom_font_size_media_query_css( $wrapper,
				$this->media_queries['small'],
				$font_small
			);

			$r .= $this->get_custom_font_size_media_query_css( $wrapper,
				$this->media_queries['medium'],
				$font_medium
			);

			$r .= $this->get_custom_font_size_media_query_css( $wrapper,
				$this->media_queries['large'],
				$font_large
			);
		}

		return $r;
	}

	/**
	 * Get particular media query as string
	 *
	 * @param $wrapper
	 * @param $media_size
	 * @param $font_size
	 *
	 * @return string Complete media query as string
	 */
	public function get_custom_font_size_media_query_css( $wrapper, $media_size, $font_size ) {

		return sprintf( '@media all and (min-width:%1$s) {
				%3$s {
					font-size: %2$s!important;
					} }'
			, $media_size
			, $font_size
			, $wrapper );
	}
}