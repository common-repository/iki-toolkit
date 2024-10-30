<?php


/**
 * Class for creating blog posts slider shortcode
 */
class Iki_Slider_Options_VC {

	private static $id = 0;
	protected $custom_id = 0;

	public function __construct() {
		$this->custom_id = self::generate_id();

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
	 * Get slider options
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_slider_options( $group_name ) {

		return array(
			array(
				"type"       => "checkbox",
				"heading"    => __( "Show slider arrows", 'iki-toolkit' ),
				"param_name" => "slider_arrows",
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'      => $group_name
			),
			array(
				"type"       => "checkbox",
				"heading"    => __( "Place arrows outside of slider", 'iki-toolkit' ),
				"param_name" => "slider_arrows_outside",
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'dependency' => array( 'element' => 'slider_arrows', 'value' => '1' ),
				'group'      => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( "Slider center mode", 'iki-toolkit' ),
				"param_name"  => "slider_center_mode",
				'description' => __( 'One slider will always be in the center of slider', 'iki-toolkit' ),
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"       => "checkbox",
				"heading"    => __( 'Use "fade" animation', 'iki-toolkit' ),
				"param_name" => "slider_fade",
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'      => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( 'Use adaptive height', 'iki-toolkit' ),
				"param_name"  => 'slider_adaptive_height',
				'description' => __( 'Enables adaptive height for single slide horizontal carousels.', 'iki-toolkit' ),
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( 'No space between slides', 'iki-toolkit' ),
				"param_name"  => 'slider_no_spacing',
				'description' => __( 'This option might not look nice depending on the slider design.', 'iki-toolkit' ),
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
			array(
				"type"       => "checkbox",
				"heading"    => __( 'Use "dots" navigation', 'iki-toolkit' ),
				"param_name" => "slider_dots",
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'      => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( 'Slider has "infinite" scrolling', 'iki-toolkit' ),
				"param_name"  => "slider_infinite",
				'description' => __( 'Slider can slide in one direction indefinetely', 'iki-toolkit' ),
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),

			array(
				"type"        => "textfield",
				"heading"     => __( 'Number of visible slides', 'iki-toolkit' ),
				"param_name"  => "slider_slides_to_show",
				'description' => __( 'How many slides are visible at any moment', 'iki-toolkit' ),
				"value"       => '1',
				'group'       => $group_name
			),
			array(
				"type"       => "textfield",
				"heading"    => __( 'Number of slides to scroll at once', 'iki-toolkit' ),
				"param_name" => "slider_slides_to_scroll",
				"value"      => '1',
				'group'      => $group_name
			),
			array(
				"type"        => "checkbox",
				"heading"     => __( 'Remove autoplay', 'iki-toolkit' ),
				"param_name"  => "slider_remove_autoplay",
				'description' => __( 'Slider won\'t be automatically animated', 'iki-toolkit' ),
				"value"       => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'       => $group_name
			),
		);
	}

	/**
	 * Get slider responsive options
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_slider_responsive_options( $group_name ) {

		return array(
			array(
				"type"       => "textfield",
				"heading"    => __( 'Number of visible slides on EXTRA SMALL screen (up to 700px)', 'iki-toolkit' ),
				"param_name" => "slider_slides_to_show_extra_small",
				"value"      => '1',
				'group'      => $group_name
			),
			array(
				"type"       => "textfield",
				"heading"    => __( 'Number of visible slides on SMALL SCREEN (up to 992px)', 'iki-toolkit' ),
				"param_name" => "slider_slides_to_show_small",
				"value"      => '2',
				'group'      => $group_name
			),
			array(
				"type"        => "textfield",
				"heading"     => __( 'Number of visible slides on MEDIUM screen (up to 1200px)', 'iki-toolkit' ),
				"param_name"  => "slider_slides_to_show_medium",
				'description' => __( 'Leave empty for default value from "Number of visible slides"', 'iki-toolkit' ),
				"value"       => '',
				'group'       => $group_name
			),
			array(
				"type"       => "textfield",
				"heading"    => __( 'Number of slides to scroll at once on EXTRA SMALL screen', 'iki-toolkit' ),
				"param_name" => "slider_slides_to_scroll_extra_small",
				"value"      => '1',
				'group'      => $group_name
			),
			array(
				"type"        => "textfield",
				"heading"     => __( 'Number of slides to scroll at once on SMALL SCREEN', 'iki-toolkit' ),
				"param_name"  => "slider_slides_to_scroll_small",
				'description' => __( 'Leave empty for default value from "Number of slides to scroll at once"', 'iki-toolkit' ),
				"value"       => '',
				'group'       => $group_name
			),
			array(
				"type"        => "textfield",
				"heading"     => __( 'Number of slides to scroll at once on MEDIUM screen', 'iki-toolkit' ),
				"param_name"  => "slider_slides_to_scroll_medium",
				'description' => __( 'Leave empty for default value from "Number of slides to scroll at once"', 'iki-toolkit' ),
				"value"       => '',
				'group'       => $group_name
			)
		);
	}

	/**
	 * Get slider design options
	 *
	 * @param $group_name
	 *
	 * @return array
	 */
	public function get_slider_design_options( $group_name ) {
		return array(
			array(
				'type'       => 'colorpicker',
				'param_name' => 'slider_arrows_color',
				'heading'    => esc_html__( 'Arrows', 'iki-toolkit' ),
				'group'      => $group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => 'slider_dots_color',
				'heading'    => esc_html__( 'Dots', 'iki-toolkit' ),
				'group'      => $group_name
			)
		);
	}

	/**
	 * Set default shortcode attributes
	 *
	 * @param $atts
	 *
	 * @return mixed
	 */
	public function set_slider_attributes( $atts ) {


		//default slider attributes
		$atts['slider_arrows']          = isset( $atts['slider_arrows'] );
		$atts['slider_arrows_outside']  = isset( $atts['slider_arrows_outside'] );
		$atts['slider_center_mode']     = isset( $atts['slider_center_mode'] );
		$atts['slider_fade']            = isset( $atts['slider_fade'] );
		$atts['slider_dots']            = isset( $atts['slider_dots'] );
		$atts['slider_adaptive_height'] = isset( $atts['slider_adaptive_height'] );
		$atts['slider_no_spacing']      = isset( $atts['slider_no_spacing'] );
		$atts['slider_infinite']        = isset( $atts['slider_infinite'] );
		$atts['slider_remove_autoplay'] = isset( $atts['slider_remove_autoplay'] );
		$atts['slider_arrows_color']    = isset( $atts['slider_arrows_color'] ) ? $atts['slider_arrows_color'] : '';
		$atts['slider_dots_color']      = isset( $atts['slider_dots_color'] ) ? $atts['slider_dots_color'] : '';


		//need these type of check because slick slider can't handle numberic strings and spaces, and will crash
		$atts['slider_slides_to_scroll'] = isset( $atts['slider_slides_to_scroll'] ) ? (int) trim( $atts['slider_slides_to_scroll'] ) : 1;
		$atts['slider_slides_to_scroll'] = is_int( $atts['slider_slides_to_scroll'] ) ? $atts['slider_slides_to_scroll'] : 1;


		$atts['slider_slides_to_show'] = isset( $atts['slider_slides_to_show'] ) ? (int) trim( $atts['slider_slides_to_show'] ) : 1;
		$atts['slider_slides_to_show'] = is_int( $atts['slider_slides_to_show'] ) ? $atts['slider_slides_to_show'] : 1;

		//Extra small
		$atts['slider_slides_to_show_extra_small'] = isset( $atts['slider_slides_to_show_extra_small'] ) ? (int) trim( $atts['slider_slides_to_show_extra_small'] ) : 1;

		$atts['slider_slides_to_show_extra_small'] = is_int( $atts['slider_slides_to_show_extra_small'] ) ? $atts['slider_slides_to_show_extra_small'] : 1;


		$atts['slider_slides_to_scroll_extra_small'] = isset( $atts['slider_slides_to_scroll_extra_small'] ) ? (int) trim( $atts['slider_slides_to_scroll_extra_small'] ) : 1;

		$atts['slider_slides_to_scroll_extra_small'] = is_int( $atts['slider_slides_to_scroll_extra_small'] ) ? $atts['slider_slides_to_scroll_extra_small'] : 1;


		//Small
		$atts['slider_slides_to_show_small'] = isset( $atts['slider_slides_to_show_small'] ) ? (int) trim( $atts['slider_slides_to_show_small'] ) : 2;

		$atts['slider_slides_to_show_small'] = is_int( $atts['slider_slides_to_show_small'] ) ? $atts['slider_slides_to_show_small'] : 2;

		$atts['slider_slides_to_scroll_small'] = isset( $atts['slider_slides_to_scroll_small'] ) ? (int) trim( $atts['slider_slides_to_scroll_small'] ) : $atts['slider_slides_to_scroll'];

		$atts['slider_slides_to_scroll_small'] = is_int( $atts['slider_slides_to_scroll_small'] ) ? $atts['slider_slides_to_scroll_small'] : $atts['slider_slides_to_scroll'];

		//Medium
		$atts['slider_slides_to_show_medium'] = isset( $atts['slider_slides_to_show_medium'] ) ? (int) trim( $atts['slider_slides_to_show_medium'] ) : $atts['slider_slides_to_show'];

		$atts['slider_slides_to_show_medium'] = is_int( $atts['slider_slides_to_show_medium'] ) ? $atts['slider_slides_to_show_medium'] : $atts['slider_slides_to_show'];

		$atts['slider_slides_to_scroll_medium'] = isset( $atts['slider_slides_to_scroll_medium'] ) ? (int) trim( $atts['slider_slides_to_scroll_medium'] ) : $atts['slider_slides_to_scroll'];

		$atts['slider_slides_to_scroll_medium'] = is_int( $atts['slider_slides_to_scroll_medium'] ) ? $atts['slider_slides_to_scroll_medium'] : $atts['slider_slides_to_scroll'];

		return $atts;
	}

	/**
	 * Setup slick slider data
	 *
	 * @param $atts
	 * @param bool $encode
	 *
	 * @return array
	 */
	public function get_slick_data( $atts, $encode = true ) {

		$r = array(
			'slidesToShow'   => max( $atts['slider_slides_to_show'], 1 ),
			'slidesToScroll' => max( $atts['slider_slides_to_scroll'], 1 ),
			'autoplay'       => ! $atts['slider_remove_autoplay'],
		);

		if ( is_rtl() ) {
			$r['rtl'] = true;
		}

		//need these type of check on responsive because slick slider can't handle numberic strings and spaces, and will crash
		$r['responsive'] = array(
			array(
				//everything below this width (up to this width) will have this number of slides
				'breakpoint' => 420, //default always present
				'settings'   => array(
					'slidesToShow'   => 1,
					'slidesToScroll' => 1
				)
			),
			array(
				'breakpoint' => 700,
				'settings'   => array(
					'slidesToShow'   => $atts['slider_slides_to_show_extra_small'],
					'slidesToScroll' => $atts['slider_slides_to_scroll_extra_small']
				)
			),
			array(
				'breakpoint' => 992, //small
				'settings'   => array(
					'slidesToShow'   => $atts['slider_slides_to_show_small'],
					'slidesToScroll' => $atts['slider_slides_to_scroll_small']
				),
			),
			array(
				'breakpoint' => 1200, //medium
				'settings'   => array(
					'slidesToShow'   => $atts['slider_slides_to_show_medium'],
					'slidesToScroll' => $atts['slider_slides_to_scroll_medium'],
				)
			)
		);

		// only print non default slick slider options
		if ( $atts['slider_center_mode'] ) {
			$r['centerMode'] = true;
		}

		if ( ! $atts['slider_arrows'] ) {
			$r['arrows'] = false;
		}

		if ( $atts['slider_dots'] ) {
			$r['dots'] = true;
		}

		if ( ! $atts['slider_infinite'] ) {
			$r['infinite'] = false;
		}

		if ( $atts['slider_adaptive_height'] ) {
			$r['adaptiveHeight'] = true;
		}

		if ( $atts['slider_fade'] ) {
			$r['fade'] = true;
		}

		if ( $encode ) {
			$r = json_encode( $r );
		}

		return $r;
	}

	/**
	 * Set slider html classes
	 *
	 * @param $atts
	 * @param $id
	 *
	 * @return array
	 */
	public function set_slider_html_classes( $atts, $id ) {

		$classes   = array( 'iki-slick-slider' );
		$classes[] = 'iki-slick-slider-' . $id;
		if ( $atts['slider_arrows'] && $atts['slider_arrows_outside'] ) {
			$classes[] = 'iki-vc-arr-o';
		}

		if ( $atts['slider_center_mode'] ) {
			$classes[] = 'iki-vc-center-mode';
		}

		if ( $atts['slider_no_spacing'] ) {
			$classes[] = 'iki-slider-rm-space';
		}

		return $classes;
	}

	/**
	 * Get custom css as string
	 *
	 * @param $atts
	 * @param $id
	 *
	 * @return string
	 */
	public function get_custom_css( $atts, $id ) {

		$r = '';

		if ( ! empty( $atts['slider_arrows_color'] ) ) {
			$r = sprintf( '.iki-slick-slider-%1$s .slick-next:before,
			.iki-slick-slider-%1$s .slick-prev:before
			{color:%2$s;}',
				$id,
				$atts['slider_arrows_color']
			);
		}


		if ( ! empty( $atts['slider_dots_color'] ) ) {

			$r .= sprintf( '.iki-slick-slider-%1$s .slick-dots li.slick-active button,
			.iki-slick-slider-%1$s .slick-dots li button:hover,
			.iki-slick-slider-%1$s .slick-dots li button:focus
			{background-color:%2$s;}',
				$id,
				$atts['slider_dots_color']
			);

			$r .= sprintf( '.iki-slick-slider-%1$s .slick-dots li button {border-color:%2$s;}',
				$id,
				$atts['slider_dots_color']
			);
		}

		return $r;


	}

	/**
	 * Build slider html element
	 *
	 * @param $atts
	 * @param $inner_content
	 *
	 * @return string
	 */
	public function build_slider( $atts, $inner_content ) {

		$slick_slider_data = $this->get_slick_data( $atts );

		$classes = $this->set_slider_html_classes( $atts, $this->custom_id );
		$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );

		$dir = is_rtl() ? 'dir="rtl"' : '';

		$c = $this->get_custom_css( $atts, $this->custom_id );

		if ( ! empty( $c ) ) {
			$c = sprintf( '<style>%1$s</style>', $c );
			$c = preg_replace( '/\s+/', ' ', $c );
		}

		$r = sprintf( '<div %4$s data-slick=\'%3$s\' class="%1$s">%2$s %5$s</div>',
			$classes,
			$inner_content,
			$slick_slider_data,
			$dir,
			$c
		);


		return $r;
	}
}