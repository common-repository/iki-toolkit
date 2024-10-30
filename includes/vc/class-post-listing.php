<?php

/**
 * Class for creating custom blog post listing shortcode
 */

class Iki_Post_Listing_VC {

	protected $base = 'iki_post_listing_vc';
	private static $id = 0;

	protected $design_group_name = '';
	protected $design_group_other_name = '';
	protected $first_post_group_name = '';
	protected $other_posts_group_name = '';
	protected $post_query_group_name = '';
	protected $font_size_first_group_name = '';
	protected $font_size_other_group_name = '';

	/**@var Iki_Post_Options_VC $post_options */
	protected $post_options;

	/**@var Iki_Post_Options_VC $post_options */
	protected $post_options_2;

	/**
	 * Iki_Post_Listing_VC constructor.
	 */
	public function __construct() {


		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );

		$this->design_group_name          = esc_html__( 'Design first post', 'iki-toolkit' );
		$this->design_group_other_name    = esc_html__( 'Design other posts', 'iki-toolkit' );
		$this->first_post_group_name      = esc_html__( 'First post', 'iki-toolkit' );
		$this->other_posts_group_name     = esc_html__( 'Other posts', 'iki-toolkit' );
		$this->post_query_group_name      = esc_html__( 'Post query', 'iki-toolkit' );
		$this->font_size_first_group_name = esc_html__( 'Font sizes first post', 'iki-toolkit' );
		$this->font_size_other_group_name = esc_html__( 'Font sizes other posts', 'iki-toolkit' );

	}

	/**
	 * Generate unique id for every shortcode that is printed
	 * @return int
	 */
	private static function generate_id() {

		self::$id ++;

		return self::$id;
	}

	/**
	 * Register WPBakery page builder backend shortcode options generator
	 */
	public function register_for_lean_map() {

		$this->post_options   = new Iki_Post_Options_VC( 'first_' );
		$this->post_options_2 = new Iki_Post_Options_VC( 'other_' );

		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Create backend options
	 * Fired before WPBakery page builder init
	 * @return array wpbakery shortcode settings array
	 */
	public function vc_backend_settings() {

		$general = array(

			array(
				"type"        => "checkbox",
				"heading"     => __( "Show horizontal line between posts", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "horizontal_line",
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				)
			),
			array(
				"type"        => "textfield",
				"admin_label" => true,
				"heading"     => __( "Extra class name", 'iki-toolkit' ),
				"param_name"  => "html_class",
				"value"       => '',
				"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
			)
		);

		$first_post  = $this->post_options->get_layout_options( $this->first_post_group_name );
		$other_posts = $this->post_options_2->get_layout_options( $this->other_posts_group_name );

		$posts_query = $this->post_options->get_query_options( $this->post_query_group_name );

		//font sizes
		$font_sizes_first = $this->post_options->get_media_query_font_size_options( $this->font_size_first_group_name );
		$font_sizes_other = $this->post_options_2->get_media_query_font_size_options( $this->font_size_other_group_name );

		$design_first = $this->post_options->get_design_options( $this->design_group_name );
		$design_other = $this->post_options_2->get_design_options( $this->design_group_other_name );

		$params = array_merge( $general,
			$first_post,
			$other_posts,
			$posts_query,
			$font_sizes_first,
			$font_sizes_other,
			$design_first,
			$design_other
		);


		return array(
			"name"     => __( 'Post Listing', 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/post-listing.png',
			"params"   => $params
		);
	}


	/**
	 * Print WPBakery page builder shortcode
	 *
	 * @param $atts array shortcode attributes
	 * @param $content string shortcode textarea_html content
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts, $content ) {

		if ( is_null( $this->post_options ) ) {

			$this->post_options   = new Iki_Post_Options_VC( 'first_' );
			$this->post_options_2 = new Iki_Post_Options_VC( 'other_' );
		}

		$current_id = self::generate_id();

		if ( empty( $atts ) || ! function_exists( 'vc_build_loop_query' ) ) {
			return false;
		}

		$atts = $this->set_default_attributes( $atts );

		$sticky_posts_ids = isset( $atts['remove_sticky_posts'] ) ? get_option( "sticky_posts" ) : null;


		$first_result  = '';
		$others_result = '';
		$title         = '';
		$css           = '';

		if ( ! empty( $atts['posts_query'] ) ) {

			$query = vc_build_loop_query( $atts['posts_query'], $sticky_posts_ids );

			if ( is_array( $query ) ) {

				$first_result = $this->post_options->build_posts( $query, $atts, 1 );

				/**@var WP_Query $wp_query */
				$wp_query = $query[1];
				if ( $wp_query->post_count > 1 ) {
					$others_result = $this->post_options_2->build_posts( $query, $atts, null, true, array( 'iki-vc-post-list-second' ) );
				}

			}

			return sprintf( '<div class="iki-vc-post-list-wrap iki-vc-post-list-wrap-%3$s %6$s">%4$s %1$s %2$s %5$s</div>',
				$first_result,
				$others_result,
				$current_id,
				$title,
				$css,
				$atts['html_class']
			);
		}

		return false;
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

		$c .= $this->post_options->get_custom_media_query_css( '.iki-vc-post-' . $custom_id . ' .iki-vc-first', $atts );
		$c .= $this->post_options->get_css_colors( '.iki-vc-post-' . $custom_id . ' .iki-vc-first', $atts );

		$c .= $this->post_options_2->get_custom_media_query_css( '.iki-vc-post-' . $custom_id . ' .iki-vc-other', $atts );
		$c .= $this->post_options_2->get_css_colors( '.iki-vc-post-' . $custom_id . ' .iki-vc-other', $atts );

		if ( ! empty( $c ) ) {
			$r = sprintf( '<style>%1$s</style>', $c );
			$r = preg_replace( '/\s+/', ' ', $r );
		}

		return $r;
	}

	/**
	 * Create appropriate css classes
	 *
	 * @param $layout
	 * @param null $classes
	 *
	 * @return string
	 */
	protected function create_layout_classes( $layout, $classes = null ) {

		$r = array(
			'iki-vc-post-list',
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
	 * Get post layout params
	 *
	 * @param $param_prefix
	 * @param $atts
	 *
	 * @return array
	 */
	protected function get_post_layout_params( $param_prefix, $atts ) {

		$r = array(
			'post_layout'     => $atts["{$param_prefix}_post_layout"],
			'image_layout'    => $atts["{$param_prefix}_post_image_layout"],
			'post_excerpt'    => $atts["{$param_prefix}_post_excerpt"],
			'post_comments'   => $atts["{$param_prefix}_post_comments"],
			'post_date'       => $atts["{$param_prefix}_post_date"],
			'post_subtitle'   => $atts["{$param_prefix}_post_subtitle"],
			'categories'      => $atts["{$param_prefix}_post_categories"],
			'tax_name'        => trim( $atts["{$param_prefix}_post_tax_name"] ),
			'horizontal_line' => $atts['horizontal_line']
		);

		$r['image_size'] = ( Iki_Toolkit_Utils::has_image_size( $atts["{$param_prefix}_post_image_size"] ) ) ? $atts["{$param_prefix}_post_image_size"] : '';

		if ( 'full_width' == $atts["{$param_prefix}_post_image_layout"] ) {

			$r['title_location'] = $atts["{$param_prefix}_post_title_location"];

		} else {

			$r['title_location'] = $atts["{$param_prefix}_post_title_location_2"];

		}

		return $r;
	}


	/**
	 * Set default shortcode attributes
	 *
	 * @param $atts
	 *
	 * @return array
	 */
	protected function set_default_attributes( $atts ) {


		$atts = $this->post_options->set_attributes( $atts );
		$atts = $this->post_options_2->set_attributes( $atts );


		if ( isset( $atts['html_class'] ) ) {
			$atts['html_class'] = explode( ' ', $atts['html_class'] );
			$atts['html_class'] = Iki_Toolkit_Utils::sanitize_html_class_array( $atts['html_class'] );
		} else {
			$atts['html_class'] = '';
		}

		$atts['horizontal_line'] = isset( $atts['horizontal_line'] );

		return $atts;

	}

}

new Iki_Post_Listing_VC();
