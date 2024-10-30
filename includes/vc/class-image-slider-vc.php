<?php

/**
 * Class for creating image slider shortcode
 */
class Iki_Image_Slider_VC {


	protected $base = 'iki_image_slider_vc';
	private static $id = 0;

	protected $grid_data;
	protected $atts;

	/**@var Iki_Slider_Options_VC $slider_options */
	protected $slider_options;

	/**
	 * Iki_Image_Slider_VC constructor.
	 */
	public function __construct() {

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

		$this->slider_options = new Iki_Slider_Options_VC();

		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Create backend options
	 * @return array
	 */
	public function vc_backend_settings() {

		$general = array(

			array(
				'type'        => 'attach_images',
				'heading'     => __( 'Images', 'iki-toolkit' ),
				'param_name'  => 'images',
				'value'       => '',
				'description' => __( 'Select images from media library', 'iki-toolkit' ),
				'dependency'  => array(
					'element' => 'source',
					'value'   => 'media_library',
				),
			),
			array(
				'heading'    => esc_html__( 'Image orientation', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "image_orientation",
				"value"      => array(
					__( 'Square', 'iki-toolkit' )    => 'square',
					__( 'Portrait', 'iki-toolkit' )  => 'portrait',
					__( 'Landscape', 'iki-toolkit' ) => 'landscape',
				)
			),
			array(
				"type"        => "textfield",
				"admin_label" => true,
				"heading"     => __( "Image size", 'iki-toolkit' ),
				"param_name"  => "image_size",
				"value"       => '',
				"description" => __( 'Choose image size, leave empty for default value', 'iki-toolkit' ),
			)
		);

		$image_click_opt = array(
			'heading'    => esc_html__( 'Image click', 'iki-toolkit' ),
			'type'       => 'dropdown',
			'param_name' => 'image_click',
			"value"      => array(
				__( 'None', 'iki-toolkit' )                => 'none',
				__( 'Link to large image', 'iki-toolkit' ) => 'large_image',
				__( 'Custom link', 'iki-toolkit' )         => 'custom_link',
			),
		);

		if ( class_exists( 'Iki_Theme' ) ) {
			$image_click_opt['value'][ __( 'Open Lightbox', 'iki-toolkit' ) ] = 'lightbox';
		}

		$general[] = $image_click_opt;

		$general[] = array(
			'type'        => 'exploded_textarea',
			'param_name'  => 'image_links',
			'admin_label' => true,
			'value'       => '',
			'heading'     => esc_html__( 'Custom links', 'iki-toolkit' ),
			'description' => esc_html__( 'Enter links for each image (Note: divide links with linebreaks (Enter)).', 'iki-toolkit' ),
		);


		$general[] = array(
			"type"        => "textfield",
			"admin_label" => true,
			"heading"     => __( "Extra class name", 'iki-toolkit' ),
			"param_name"  => "html_class",
			"value"       => '',
			"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
		);


		$slider_options = $this->slider_options->get_slider_options( __( 'Slider options', 'iki-toolkit' ) );
		$slider_design  = $this->slider_options->get_slider_design_options( __( 'Slider design', 'iki-toolkit' ) );

		$params = array_merge( $general,
			$slider_options,
			$slider_design
		);

		return array(
			"name"     => __( 'Image Slider', 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/image-slider.png',
			"params"   => $params
		);

	}


	public function do_shortcode( $atts ) {

		if ( is_null( $this->slider_options ) ) {

			$this->slider_options = new Iki_Slider_Options_VC();
		}

		$atts = $this->set_default_attributes( $atts );

		$dynamic_id = self::generate_id();

		ob_start();

		if ( is_array( $atts['images'] ) ) {
			$iterator = 0;
			foreach ( $atts['images'] as $image ) {


				$path       = '';
				$image_html = Iki_Toolkit_Utils::image_as_css_bg( $image, $atts['image_size'], array( 'iki-vc-p-img-bg' ) );

				$image_link = '';


				if ( 'lightbox' == $atts['image_click'] ) {

					$image_link = iki_toolkit_lightbox_btn( $image, 'large' );
				} elseif ( 'large_image' == $atts['image_click'] ) {

					$image_link = iki_toolkit_image_attachment_link( $image );

				} elseif ( 'custom_link' == $atts['image_click'] ) {

					if ( ! empty( $atts['image_links'] ) ) {

						if ( isset( $atts['image_links'][ $iterator ] ) ) {

							$image_link = iki_toolkit_image_custom_link( $atts['image_links'][ $iterator ] );
						}
					}
				}


				$path_to_include = apply_filters( 'iki_toolkit_image_slider_vc', $path, $atts );
				iki_toolkit_get_template( $path_to_include, array(
					'link'     => $image_link,
					'image'    => $image_html,
					'iterator' => $iterator,
					'atts'     => $atts,
				) );

				$iterator = $iterator + 1;
			}
		}

		$image_thumbs = ob_get_contents();
		ob_end_clean();

		$slider_html = $this->slider_options->build_slider( $atts, $image_thumbs );
		$classes     = array();
		$classes[]   = 'iki-slider-click-' . $atts['image_click'];
		$classes     = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );

		$result = sprintf( '<div class="iki-slider-vc-wrap iki-img-slider-vc iki-img-slider-vc-%2$s %3$s">%1$s</div>',
			$slider_html,
			$dynamic_id,
			$classes );

		return $result;
	}

	/**
	 * Set default shortcode data
	 *
	 * @param $atts
	 *
	 * @return array|mixed
	 */
	public function set_default_attributes( $atts ) {

		$r = array(
			'images'            => '',
			'image_orientation' => 'square',
			'image_click'       => 'none',
			'html_class'        => '',
			'image_links'       => '',
			'image_size'        => ''
		);

		$atts = is_array( $atts ) ? $atts : array();
		$atts = array_merge( $r, $atts );

		if ( ! empty( $atts['images'] ) ) {

			$atts['images'] = explode( ',', $atts['images'] );


			if ( ! empty( $atts['image_links'] ) ) {

				$atts['image_links'] = explode( ',', $atts['image_links'] );
			}
		}

		if ( empty( $atts['image_size'] ) ) {
			$atts['image_size'] = iki_toolkit_vc_image_orientation_to_size( $atts['image_orientation'] );
		}

		$atts = $this->slider_options->set_slider_attributes( $atts );

		return $atts;

	}
}

new Iki_Image_Slider_VC();
