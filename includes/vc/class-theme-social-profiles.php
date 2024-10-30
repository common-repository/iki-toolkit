<?php

/**
 * Class for creating  theme social profiles shortcode
 * It is connected to the theme so it only works it the theme supports it.
 * Otherwise it won't show up in frontend
 */
class Iki_Theme_Social_Profiles_VC {

	protected static $theme_social_profiles = null;
	protected $vc_present = false;
	protected $social_profiles = array();
	protected $base = 'iki_theme_social_profiles_vc';

	/**
	 * Iki_Theme_Social_Profiles_VC constructor.
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
	 * Get theme supported social profiles
	 * @return mixed|null
	 */
	public static function get_theme_social_profiles() {

		if ( ! self::$theme_social_profiles ) {

			self::$theme_social_profiles = get_option( 'iki_toolkit_social_profiles' );

		}

		return self::$theme_social_profiles;
	}

	/**
	 * Create shortcode options
	 * @return array|null
	 */
	public function vc_backend_settings() {

		$this->social_profiles = $this->get_social_profiles();
		$vc_params             = array();

		if ( $this->social_profiles ) {

			$service_data = array();

			foreach ( $this->social_profiles as $service => $url ) {

				if ( ! empty( $url ) ) {
					$service_data[ $service ] = $service;
				}
			}


			$vc_params = array(
				array(
					"type"        => "dropdown",
					'admin_label' => true,
					"heading"     => __( "Icon aligment", 'iki-toolkit' ),
					"param_name"  => "aligment",
					"value"       => array(
						__( 'Left', 'iki-toolkit' )   => 'iki-left',
						__( 'Right', 'iki-toolkit' )  => 'iki-right',
						__( 'Center', 'iki-toolkit' ) => 'iki-center',
					)
				),
				array(
					"type"        => "textfield",
					'admin_label' => true,
					"heading"     => __( "Tooltip text", 'iki-toolkit' ),
					"param_name"  => "tooltip_text",
					"value"       => '',
					"description" => __( 'Note : service name is appended at the end of the tooltip text  ', 'iki-toolkit' )
				),
				array(
					"type"        => "checkbox",
					'admin_label' => true,
					"heading"     => __( "Select Services", 'iki-toolkit' ),
					"param_name"  => "services",
					"value"       => $service_data,
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

		}

		$design_group = iki_toolkit_vc_icons_design_options( __( 'Design', 'iki-toolkit' ) );
		$vc_params    = array_merge( $vc_params, $design_group );

		return array(
			"name"     => __( "Theme Social Profiles", 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/social-theme-icons.png',
			"params"   => $vc_params
		);

	}

	/**
	 * Get social profiles that are set via the active theme
	 * @return bool
	 */
	protected function get_social_profiles() {

		return get_option( 'iki_toolkit_social_profiles' );

	}

	/**
	 * Print the shortcode
	 *
	 * @param $atts array shortcode attributes
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts ) {

		if ( ! is_array( $atts ) ) {
			return false;
		}
		$atts               = iki_toolkit_normalize_vc_icon_data( $atts );
		$atts               = iki_toolkit_parse_post_sharing_design( $atts );
		$atts['html_class'] = isset( $atts['html_class'] ) ? $atts['html_class'] : '';

		$tooltip_text = ( isset( $atts['tooltip_text'] ) ) ? $atts['tooltip_text'] : '';
		$aligment     = ( isset( $atts['aligment'] ) ) ? $atts['aligment'] : '';

		$social_profiles = self::get_theme_social_profiles();

		if ( ! empty( $social_profiles ) ) {

			$service_arr = explode( ',', $atts['services'] );

			$services = array();
			foreach ( $service_arr as $service ) {

				if ( ! empty( $social_profiles[ $service ] ) ) {
					$services[ $service ] = $social_profiles[ $service ];
				}
			}

			$html_class = '';

			if ( ! empty( $atts['html_class'] ) ) {
				$html_class = explode( ' ', $atts['html_class'] );
				$html_class = Iki_Toolkit_Utils::sanitize_html_class_array( $html_class );
			}

			$html_class .= ' ' . sanitize_html_class( $aligment );

			$result = sprintf( '<div class="%1$s">', $html_class );
			$result .= iki_toolkit_print_social_profiles( $services, esc_html( $tooltip_text ), $atts, false );
			$result .= '</div>';

			return $result;
		} else {
			return '';
		}
	}
}

new Iki_Theme_Social_Profiles_VC();
