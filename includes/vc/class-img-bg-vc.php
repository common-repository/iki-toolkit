<?php

/**
 * Class for creating title shortcode
 */
class Iki_Image_Bg_VC {


	protected $base = 'iki_img_bg_vc';
	protected $design_group_name;


	/**
	 * Iki_Title_VC constructor.
	 */
	public function __construct() {

		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );
		$this->design_group_name = __( 'Colors', 'iki-toolkit' );
	}


	/**
	 * Register WPBakery page builder backend shortcode options generator
	 */
	public function register_for_lean_map() {
		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Create shortcode options
	 * @return array
	 */
	public function vc_backend_settings() {

		$params = array(

			array(
				'type'        => 'attach_image',
				'heading'     => __( 'Image', 'iki-toolkit' ),
				'param_name'  => 'image',
				'value'       => '',
				'description' => __( 'Select images from media library', 'iki-toolkit' ),
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
				"description" => __( 'Choose image size, leave empty for default value (depends on image orientation)', 'iki-toolkit' ),
			),
			array(
				'heading'    => esc_html__( 'Background size ', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => 'bg_size',
				"value"      => array(
					__( 'Cover', 'iki-toolkit' )   => 'cover',
					__( 'Contain', 'iki-toolkit' ) => 'contain',
					__( 'Auto', 'iki-toolkit' )    => 'auto',
				),
			),
			array(
				'heading'    => esc_html__( 'Background position ', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => 'bg_position',
				"value"      => array(
					__( 'Top Left', 'iki-toolkit' )      => 'top left',
					__( 'Top Center', 'iki-toolkit' )    => 'top center',
					__( 'Top Right', 'iki-toolkit' )     => 'top right',
					__( 'Left Center', 'iki-toolkit' )   => 'left center',
					__( 'Center Center', 'iki-toolkit' ) => 'center center',
					__( 'Right Center', 'iki-toolkit' )  => 'right center',
					__( 'Bottom Left', 'iki-toolkit' )   => 'bottom left',
					__( 'Bottom Center', 'iki-toolkit' ) => 'bottom center',
					__( 'Bottom Right', 'iki-toolkit' )  => 'bottom right',
				),
			),
			array(
				'heading'    => esc_html__( 'Background repeat ', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => 'bg_repeat',
				"value"      => array(
					__( 'No repeat', 'iki-toolkit' ) => 'no-repeat',
					__( 'Repeat', 'iki-toolkit' )    => 'repeat',
					__( 'Repeat X', 'iki-toolkit' )  => 'repeat-x',
					__( 'Repeat Y', 'iki-toolkit' )  => 'repeat-y'
				),
			),
			array(
				'heading'    => esc_html__( 'Background attachment ', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => 'bg_attachment',
				"value"      => array(
					__( 'Scroll', 'iki-toolkit' ) => 'scroll',
					__( 'Fixed', 'iki-toolkit' )  => 'fixed'
				),
			),
			array(
				"type"       => "checkbox",
				"heading"    => __( "Animate on hover", 'iki-toolkit' ),
				"param_name" => 'animate_on_hover',
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
			),
			array(
				'heading'    => esc_html__( 'Animation', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => 'animation',
				"value"      => array(
					__( 'Scale up', 'iki-toolkit' )   => 'scale-up',
				),
				'dependency' => array( 'element' => 'animate_on_hover', 'value' => '1' ),
			),
			array(
				'heading'     => esc_html__( 'Border radius ', 'iki-toolkit' ),
				'type'        => 'textfield',
				'param_name'  => 'border_radius',
				'description' => __( 'Use values together with units (10px or 50%)', 'iki-toolkit' ),
				"value"       => '',
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

		$design = array(
			array(
				'type'        => 'colorpicker',
				'param_name'  => 'overlay_color',
				'description' => __( 'Color to be shown above the image', 'iki-toolkit' ),
				'heading'     => esc_html__( 'Overlay color', 'iki-toolkit' ),
				'group'       => $this->design_group_name
			)
		);


		$params = array_merge( $params, $design );

		return array(
			"name"     => __( 'Image as background', 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/title.png',
			"params"   => $params
		);
	}

	/**
	 * Print the shortcode
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function do_shortcode( $atts ) {

		$atts                      = is_array( $atts ) ? $atts : array();
		$atts['bg_size']           = isset( $atts['bg_size'] ) ? $atts['bg_size'] : 'cover';
		$atts['bg_position']       = isset( $atts['bg_position'] ) ? $atts['bg_position'] : 'top left';
		$atts['bg_scroll']         = isset( $atts['bg_scroll'] ) ? $atts['bg_scroll'] : 'scroll';
		$atts['bg_repeat']         = isset( $atts['bg_repeat'] ) ? $atts['bg_repeat'] : 'no-repeat';
		$atts['image_orientation'] = isset( $atts['image_orientation'] ) ? $atts['image_orientation'] : 'square';
		$atts['overlay_color']     = isset( $atts['overlay_color'] ) ? $atts['overlay_color'] : 'none';
		$atts['animate_on_hover']  = isset( $atts['animate_on_hover'] );
		$atts['animation']         = isset( $atts['animation'] ) ? $atts['animation'] : 'scale-up';
		$atts['border_radius']     = isset( $atts['border_radius'] ) ? $atts['border_radius'] : '';
		$atts['html_class']        = isset( $atts['html_class'] ) ? $atts['html_class'] : '';

		if ( ! isset( $atts['image'] ) || empty( $atts['image'] ) ) {
			return '';

		}
		$default_img_size = 'grid_2_square';

		if ( ! isset( $atts['image_size'] ) || empty( $atts['image_size'] ) ) {
			switch ( $atts['image_orientation'] ) {
				case 'portrait':
					$default_img_size = 'grid_2_portrait';
					break;
				case 'landscape':
					$default_img_size = 'grid_2_landscape';
					break;
			}
		}

		$atts['image_size'] = $default_img_size;

		$img_src = wp_get_attachment_image_src( $atts['image'], $default_img_size );

		if ( empty( $img_src ) ) {
			return '';
		}

		$overlay = '';
		if ( 'none' !== $atts['overlay_color'] ) {
			$overlay = sprintf( '<div class="iki-vc-img-overlay" style="background-color:%1$s;"></div>',
				$atts['overlay_color']
			);
		}

		$img_src = esc_url( $img_src[0] );

		$image_styles = sprintf( 'style="background-size:%1$s;background-position:%2$s;background-attachment:%3$s;background-repeat:%4$s;background-image:%5$s;"',
			$atts['bg_size'],
			$atts['bg_position'],
			$atts['bg_scroll'],
			$atts['bg_repeat'],
			'url(' . $img_src . ')'
		);

		//setup border radius
		$border_radius = '';
		if ( ! empty( $atts['border_radius'] ) ) {
			$border_radius = sprintf( 'style="border-radius:%1$s"', $atts['border_radius'] );
		}

		$image = sprintf( '<div class="iki-vc-img-bg" %1$s></div>', $image_styles );


		$html_class = '';

		if ( ! empty( $atts['html_class'] ) ) {
			$html_class = explode( ' ', $atts['html_class'] );
			$html_class = Iki_Toolkit_Utils::sanitize_html_class_array( $html_class );
		}

		if ( $atts['animate_on_hover'] ) {
			$html_class .= ' iki-vc-img-' . sanitize_html_class( $atts['animation'] );
		}

		//construct HTML structure
		$r = sprintf( '<div class="iki-vc-img-bg-wrap %5$s" ><div class="embed-responsive %1$s" %4$s >%2$s %3$s</div></div>',
			'embed-responsive-' . $atts['image_orientation'],
			$image,
			$overlay,
			$border_radius,
			$html_class
		);

		return $r;
	}

}

new Iki_Image_Bg_VC();