<?php

/**
 * Class for creating share icons shortcode
 */
class Iki_Share_Icons_VC {

	protected $share_services = array();
	protected $base = 'iki_share_buttons_vc';

	/**
	 * Iki_Share_Icons_VC constructor.
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
	 * @return array|null
	 */
	public function vc_backend_settings() {

		$this->share_services = iki_toolkit_get_share_services();

		if ( ! empty( $this->share_services ) ) {

			$serviceData = array();

			foreach ( $this->share_services as $service => $url ) {

				$serviceData[ $service ] = $service;

			}


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
					"type"        => "checkbox",
					"heading"     => __( "Services", 'iki-toolkit' ),
					'admin_label' => true,
					"param_name"  => "services",
					"value"       => $serviceData,
					"description" => __( "Check services that you wish to be shown.", 'iki-toolkit' )
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

			$design_group = iki_toolkit_vc_icons_design_options( __( 'Design', 'iki-toolkit' ) );
			$vc_params = array_merge( $vc_params, $design_group );

			return array(
				"name"     => __( "Share Buttons", 'iki-toolkit' ),
				"base"     => $this->base,
				"category" => __( "Iki Themes", 'iki-toolkit' ),
				'icon'     => plugin_dir_url( __FILE__ ) . 'icons/social-icons.png',
				"params"   => $vc_params
			);
		}

	}

	/**
	 * Create and print shortcode
	 *
	 * @param $atts array shortcode attributes
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts ) {

		if ( ! is_array( $atts ) ) {
			return false;
		}

		$atts                 = iki_toolkit_normalize_vc_icon_data( $atts );
		$atts                 = iki_toolkit_parse_post_sharing_design( $atts );
		$this->share_services = iki_toolkit_get_share_services();

		$atts['html_class'] = isset( $atts['html_class'] ) ? $atts['html_class'] : '';
		$html_class         = '';

		$aligment = ( isset( $atts['aligment'] ) ) ? $atts['aligment'] : '';


		$services_empty = empty( $atts['services'] );

		if ( ! $services_empty ) {

			$service_arr = explode( ',', $atts['services'] );

			$services = array();
			foreach ( $service_arr as $service ) {

				$services[ $service ] = $this->share_services[ $service ];

			}


			if ( ! empty( $atts['html_class'] ) ) {
				$html_class = explode( ' ', $atts['html_class'] );
				$html_class = Iki_Toolkit_Utils::sanitize_html_class_array( $html_class );
			}

			$html_class .= ' ' . sanitize_html_class( $aligment );

			$result = sprintf( '<div class="%1$s  ">', $html_class );
			$result .= iki_toolkit_print_share_icons( $atts, $services, false );
			$result .= '</div>';

			return $result;

		} else {
			return '';
		}
	}

}

new Iki_Share_Icons_VC();
