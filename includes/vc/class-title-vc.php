<?php

/**
 * Class for creating title shortcode
 */
class Iki_Title_VC {


	protected $base = 'iki_title_vc';
	private static $id = 0;
	protected $design_group_name;

	private $media_queries = array(

		'small'  => '480px',
		'medium' => '600px',
		'large'  => '992px'
	);

	/**
	 * Iki_Title_VC constructor.
	 */
	public function __construct() {

		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );
		$this->design_group_name = __( 'Design', 'iki-toolkit' );
	}


	/**
	 * Register WPBakery page builder backend shortcode options generator
	 */
	public function register_for_lean_map() {
		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
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
	 * Create shortcode options
	 * @return array
	 */
	public function vc_backend_settings() {

		$general = array(
			array(
				'type'        => 'textfield',
				'param_name'  => 'title_text',
				'admin_label' => true,
				'value'       => '',
				'heading'     => esc_html__( 'Title', 'iki-toolkit' ),
			),

			array(
				'heading'    => esc_html__( 'Title HTML tag', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => 'title_tag',
				"value"      => array(
					__( 'H2', 'iki-toolkit' ) => 'h2',
					__( 'H1', 'iki-toolkit' ) => 'h1',
					__( 'H3', 'iki-toolkit' ) => 'h3',
					__( 'H4', 'iki-toolkit' ) => 'h4',
					__( 'H5', 'iki-toolkit' ) => 'h5',
					__( 'H6', 'iki-toolkit' ) => 'h6',
				),
			),
			array(
				'type'       => 'vc_link',
				'param_name' => 'title_link',
				'value'      => '',
				'heading'    => esc_html__( 'Title link', 'iki-toolkit' ),
				'dependency' => array( 'element' => 'title_text', 'not_empty' => true )
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
				'type'       => 'colorpicker',
				'param_name' => 'title_color',
				'heading'    => esc_html__( 'Title color', 'iki-toolkit' ),
				'dependency' => array( 'element' => 'title_text', 'not_empty' => true ),
				'group'      => $this->design_group_name
			),
			array(
				'type'       => 'colorpicker',
				'param_name' => 'title_color_bg',
				'heading'    => esc_html__( 'Title background', 'iki-toolkit' ),
				'dependency' => array( 'element' => 'title_text', 'not_empty' => true ),
				'group'      => $this->design_group_name
			),
			array(
				'heading'    => esc_html__( 'Title Design', 'iki-toolkit' ),
				'type'       => 'dropdown',
				'param_name' => "title_design",
				"value"      => array(
					__( 'Default', 'iki-toolkit' )                => 'default',
					__( 'Small width border', 'iki-toolkit' )     => 'border_small',
					__( 'Full width border', 'iki-toolkit' )      => 'border_full',
					__( 'Full width background ', 'iki-toolkit' ) => 'bg_full',
				),
				'dependency' => array( 'element' => 'title_text', 'not_empty' => true ),
				'group'      => $this->design_group_name
			),
		);

		$media_query = iki_toolkit_vc_get_mq_font_sizes_options( '', __( 'Media query', 'iki-toolkit' ) );

		$params = array_merge( $general, $design, $media_query );

		return array(
			"name"     => __( 'Title Link', 'iki-toolkit' ),
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

		$atts                 = is_array( $atts ) ? $atts : array();
		$atts['title_design'] = isset( $atts['title_design'] ) ? $atts['title_design'] : 'default';//nothing
		$atts['title_tag']    = isset( $atts['title_tag'] ) ? $atts['title_tag'] : 'h2';
		$atts['title_text']   = isset( $atts['title_text'] ) ? $atts['title_text'] : 'lorem ipsum';
		$atts['html_class']   = isset( $atts['html_class'] ) ? $atts['html_class'] : '';

		iki_toolkit_vc_set_media_query_atts( $atts, null, '' );

		$title = $this->print_title( $atts );

		return $title;
	}

	/**
	 * Print title element
	 *
	 * @internal
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	protected function print_title( $atts ) {

		$r          = '';
		$current_id = self::generate_id();
		$html_class = '';
		if ( ! empty( $atts['title_text'] ) ) {

			if ( ! empty( $atts['html_class'] ) ) {
				$html_class = explode( ' ', $atts['html_class'] );
				$html_class = Iki_Toolkit_Utils::sanitize_html_class_array( $html_class );
			}

			$r = '<span class="iki-vc-title">' . esc_html( $atts['title_text'] ) . '</span>';

			if ( ! empty( $atts['title_link'] ) && '|||' != $atts['title_link'] ) {

				$link   = iki_toolkit_vc_get_custom_link_attributes( $atts['title_link'] );
				$link[] = 'class="iki-vc-title-link tooltip-js"';
				$r      = iki_toolkit_vc_build_link_tag( $link, $r );
			} else {
				$r = '<div class="iki-vc-title-link">' . $r . '</div>';
			}

			$title_design = 'iki-vc-title-' . sanitize_html_class( $atts['title_design'] );

			$css = $this->print_css( $current_id, $atts );

			$r = sprintf( '<%5$s class="iki-vc-title-id-%3$s iki-vc-title-wrap %2$s %6$s">%1$s %4$s</%5$s>',
				$r,
				$title_design,
				$current_id,
				$css,
				$atts['title_tag'],
				$html_class
			);
		}

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
		if ( ! empty( $atts['title_text'] ) ) {

			if ( ! empty( $atts['title_color'] ) ) {

				$c .= sprintf( '.iki-vc-title-id-%1$s .iki-vc-title-link,
				 .iki-vc-title-id-%1$s .iki-vc-title-link:hover ,
				   .iki-vc-title-id-%1$s .iki-vc-title-link:focus  { %2$s }',
					$custom_id,
					'color:' . $atts['title_color'] . ';' );

			}

			if ( ! empty( $atts['title_color_bg'] ) ) {

				$c .= sprintf( '.iki-vc-title-id-%1$s .iki-vc-title-link:before { %2$s }',
					$custom_id,
					'background:' . $atts['title_color_bg'] . ';' );

			}

			if ( $atts['custom_font_size'] ) {

				// media query
				$c .= $this->get_custom_font_size_media_query_css( $this->media_queries['small'],
					$atts['font_size_small'],
					$custom_id );

				$c .= $this->get_custom_font_size_media_query_css( $this->media_queries['medium'],
					$atts['font_size_medium'],
					$custom_id );

				$c .= $this->get_custom_font_size_media_query_css( $this->media_queries['large'],
					$atts['font_size_large'],
					$custom_id );
			}
		}


		if ( ! empty( $c ) ) {
			$r = sprintf( '<style>%1$s</style>', $c );
			$r = preg_replace( '/\s+/', ' ', $r );
		}

		return $r;
	}

	/**
	 * Generate custom font size media query
	 *
	 * @param $media_size
	 * @param $font_size
	 * @param $cust_id
	 *
	 * @return string
	 */
	protected function get_custom_font_size_media_query_css( $media_size, $font_size, $cust_id ) {
		return sprintf( '@media all and (min-width:%1$s) {
				.iki-vc-title-id-%3$s {
					font-size: %2$s!important;
					} }'
			, $media_size
			, $font_size
			, $cust_id );
	}

}

new Iki_Title_VC();