<?php

/*Handle callback from ajax*/

class Iki_External_Service_Callbacks {

	/**
	 *Check if the nonce is valid and we have all the data required to make the API calls
	 */
	public static function iki_external_api_callback() {

		if ( ! check_ajax_referer( 'iki_nonce', false, false ) ) {
			die( 'Security check' );
		}


		$r = 0;
		if ( isset( $_POST['service'] ) && isset( $_POST['method'] ) && isset( $_POST['data'] ) ) {
			$r = self::get_data( $_POST['service'], $_POST['method'], $_POST['data'] );
		}

		if ( $r !== false ) {
			wp_send_json( $r );

		} else {
			die( "0" );
		}
	}

	/**
	 * Pass data to appropriate service
	 *
	 * @param $service
	 * @param $method
	 * @param $data
	 *
	 * @return int|mixed
	 */
	public static function get_data( $service, $method, $data ) {

		$r = 0;
		if ( $service == 'flickr' ) {
			$instance = new Iki_Flickr_Api();
			$r        = $instance->get_data( $method, $data );
		} elseif ( 'pinterest' == $service ) {
			$instance = new Iki_Pinterest_API();
			$r        = $instance->get_data( $method, $data );
		}

		return $r;
	}

	/**
	 * Register ajax callbacks
	 */
	public static function register_ajax_callbacks() {
		add_action( 'wp_ajax_iki_external_api', array(
			'Iki_External_Service_Callbacks',
			'iki_external_api_callback'
		) );
		add_action( 'wp_ajax_nopriv_iki_external_api', array(
			'Iki_External_Service_Callbacks',
			'iki_external_api_callback'
		) );

	}
}

Iki_External_Service_Callbacks::register_ajax_callbacks();

