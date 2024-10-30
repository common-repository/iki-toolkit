<?php

/**
 * Class for creating social profiles shortcode
 */
class Iki_Social_Profiles_VC {

	protected $vc_present = false;

	protected $iki_theme_exists = false;

	protected $socialProfiles = array();
	protected $base = 'iki_social_profiles_vc';

	/**
	 * Iki_Social_Profiles_VC constructor.
	 */
	public function __construct() {

		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );

	}


	/**
	 * Register WPBakery page builder backend shortcode options generator
	 */
	public function register_for_lean_map() {
		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Create shortcode options
	 * @return array array of options
	 */
	public function vc_backend_settings() {

		$this->socialProfiles = iki_toolkit_get_social_profiles();

		$vc_params = array(
			array(
				"type"        => "dropdown",
				"heading"     => __( "Icon aligment", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "aligment",
				"value"       => array(
					__( 'Left', 'iki-toolkit' )   => 'iki-left',
					__( 'Right', 'iki-toolkit' )  => 'iki-right',
					__( 'Center', 'iki-toolkit' ) => 'iki-center',
				),
			),
			array(
				"type"        => "textfield",
				"heading"     => __( "Tooltitp Text", 'iki-toolkit' ),
				'admin_label' => true,
				"param_name"  => "tooltip_text",
				"value"       => '',
				"description" => __( 'Note : service name is appended at the end of the tooltip text  ', 'iki-toolkit' )
			),
			array(
				"type"        => "textfield",
				"admin_label" => false,
				"heading"     => __( "Extra class name", 'iki-toolkit' ),
				"param_name"  => "html_class",
				"value"       => '',
				"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
			)
		);


		foreach ( $this->socialProfiles as $service => $url ) {

			$heading = $service;
			array_push( $vc_params, array(
					"type"        => "textfield",
					'admin_label' => false,
					"heading"     => $heading,
					"param_name"  => $service,
					"value"       => '',
				)
			);
		}

		$design_group = iki_toolkit_vc_icons_design_options( __( 'Design', 'iki-toolkit' ) );
		$vc_params    = array_merge( $vc_params, $design_group );

		return array(
			"name"     => __( "Social Profiles Buttons", 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/social-profiles.png',
			"params"   => $vc_params
		);

	}

	/**
	 * Print shortcode
	 *
	 * @param $atts array shortcode attributes
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts ) {

		if ( ! is_array( $atts ) ) {
			return false;
		}
		$html_class         = '';
		$atts               = iki_toolkit_normalize_vc_icon_data( $atts );
		$atts               = iki_toolkit_parse_post_sharing_design( $atts );
		$atts['html_class'] = isset( $atts['html_class'] ) ? $atts['html_class'] : '';

		$this->socialProfiles = iki_toolkit_get_social_profiles();

		$tooltip_text = ( isset( $atts['tooltip_text'] ) ) ? $atts['tooltip_text'] : '';
		$aligment     = ( isset( $atts['aligment'] ) ) ? $atts['aligment'] : '';

		$services = array();
		foreach ( $this->socialProfiles as $service => $url ) {

			if ( isset( $atts[ $service ] ) ) {

				$services[ $service ] = $atts[ $service ];
			}
		}

		if ( ! empty( $atts['html_class'] ) ) {
			$html_class = explode( ' ', $atts['html_class'] );
			$html_class = Iki_Toolkit_Utils::sanitize_html_class_array( $html_class );
		}

		$html_class .= ' ' . sanitize_html_class( $aligment );

		$result = sprintf( '<div class="%1$s">', $html_class );
		$result .= iki_toolkit_print_social_profiles( $services, esc_html( $tooltip_text ), $atts, false );
		$result .= '</div>';

		return $result;
	}
}

new Iki_Social_Profiles_VC();