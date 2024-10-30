<?php

/**
 * Class for creating blog post slider shortcode
 */

class Iki_Post_Slider_VC {

	protected $base = 'iki_post_slider_vc';
	private static $id = 0;
	private $custom_id = 0;

	protected $design_group_name = '';
	protected $first_post_group_name = '';
	protected $post_query_group_name = '';
	protected $font_size_first_group_name = '';
	protected $slider_options_group_name = '';

	/**@var Iki_Post_Options_VC $post_options */
	protected $post_options;

	/**@var Iki_Slider_Options_VC $slider_options */
	protected $slider_options;


	/**
	 * Iki_Post_Slider_VC constructor.
	 */
	public function __construct() {


		$this->design_group_name          = esc_html__( 'Post colors', 'iki-toolkit' );
		$this->first_post_group_name      = esc_html__( 'Post layout', 'iki-toolkit' );
		$this->post_query_group_name      = esc_html__( 'Post query', 'iki-toolkit' );
		$this->font_size_first_group_name = esc_html__( 'Font sizes post', 'iki-toolkit' );
		$this->slider_options_group_name  = esc_html__( 'Slider options', 'iki-toolkit' );


		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );
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

		$this->post_options   = new Iki_Post_Options_VC( '', '_slider' );
		$this->slider_options = new Iki_Slider_Options_VC();

		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Create shortcode options
	 * @return array wpbakery shortcode settings array
	 */
	public function vc_backend_settings() {

		$general = array(

			array(
				"type"        => "textfield",
				"admin_label" => true,
				"heading"     => __( "Extra class name", 'iki-toolkit' ),
				"param_name"  => "html_class",
				"value"       => '',
				"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
			)
		);

		$first_post = $this->post_options->get_layout_options( $this->first_post_group_name );

		$posts_query = $this->post_options->get_query_options( $this->post_query_group_name );

		//font sizes
		$font_sizes_first = $this->post_options->get_media_query_font_size_options( $this->font_size_first_group_name );

		$design = $this->post_options->get_design_options( $this->design_group_name );

		$slider_options            = $this->slider_options->get_slider_options( $this->slider_options_group_name );
		$slider_options_responsive = $this->slider_options->get_slider_responsive_options( __( 'Slider Responsive Options', 'iki-toolkit' ) );
		$slider_design             = $this->slider_options->get_slider_design_options( __( 'Slider Colors', 'iki-toolkit' ) );

		$params = array_merge(
			$general,
			$posts_query,
			$first_post,
			$font_sizes_first,
			$design,
			$slider_options,
			$slider_options_responsive,
			$slider_design
		);


		return array(
			"name"     => __( 'Post Slider', 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/post-slider.png',
			"params"   => $params
		);
	}


	/**
	 * Create and print shortcode html
	 *
	 * @param $atts array shortcode attributes
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts ) {


		$this->post_options   = new Iki_Post_Options_VC( '', '_slider' );
		$this->slider_options = new Iki_Slider_Options_VC();

		if ( empty( $atts ) || ! function_exists( 'vc_build_loop_query' ) ) {
			return false;
		}

		$this->custom_id = self::generate_id();
		$current_id      = $this->custom_id;

		$atts = $this->set_default_attributes( $atts );

		$sticky_posts_ids = ( $atts['remove_sticky_posts'] ) ? get_option( "sticky_posts" ) : null;

		$posts_wrapper_html = '';

		if ( ! empty( $atts['posts_query'] ) ) {

			$query = vc_build_loop_query( $atts['posts_query'], $sticky_posts_ids );


			if ( is_array( $query ) ) {

				//build posts
				//clone attributes because html_class is not applicable to the posts
				//html_class should only be added to the slider wrapper
				$cloned_atts               = array_merge_recursive( array(), $atts );
				$cloned_atts['html_class'] = '';
				$posts_html                = $this->post_options->build_posts( $query, $cloned_atts, null, false );

				//build slider and put posts inside
				$slider_html = $this->slider_options->build_slider( $atts, $posts_html );

				//get posts wrapper, and put slider inside
				$posts_wrapper_html = $this->post_options->build_wrapper( $cloned_atts, $slider_html );

			}

			return sprintf( '<div class="iki-slider-vc-wrap iki-post-slider-vc iki-post-slider-vc-%2$s %3$s ">%1$s</div>',
				$posts_wrapper_html,
				$current_id,
				$atts['html_class']
			);
		}

		return false;
	}

	protected function set_default_attributes( $atts ) {

		$atts = $this->post_options->set_attributes( $atts );
		$atts = $this->slider_options->set_slider_attributes( $atts );

		if ( isset( $atts['html_class'] ) ) {
			$atts['html_class'] = explode( ' ', $atts['html_class'] );
			$atts['html_class'] = Iki_Toolkit_Utils::sanitize_html_class_array( $atts['html_class'] );
		} else {
			$atts['html_class'] = '';
		}

		return $atts;

	}


}

new Iki_Post_Slider_VC();
