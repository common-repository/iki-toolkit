<?php

/**
 * Handle ajax checks for valid external sevice profiles , galleries , and api keys.
 */
class Iki_External_Api_Data_Check {

	/**
	 *Check if the nonce is valid and we have all the data required to make the API calls
	 */
	public function iki_check_external_data() {

		check_ajax_referer( 'iki-admin-nonce-check' );

		$r = 0;
		if ( isset( $_POST['service'] ) && isset( $_POST['method'] ) && isset( $_POST['data'] ) ) {
			$r = $this::get_data( $_POST['service'], $_POST['method'], $_POST['data'] );
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
	public function get_data( $service, $method, $data ) {

		$r = 0;
		if ( $service == 'flickr' ) {

			$r = $this->handle_flickr_check( $method, $data );

		} elseif ( 'pinterest' == $service ) {

			$r = $this->handle_pinterest_check( $method, $data );

		}

		return $r;
	}

	/**
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|string
	 */
	protected function handle_flickr_check( $method, $data ) {

		$api_key = ( isset( $data['api_key'] ) ? $data['api_key'] : null );

		$instance = new Iki_Flickr_Api( $api_key );
		$token    = $instance->get_token( $method, $data );
		$message  = '';
		if ( empty( $token ) ) {
			$resp = array(
				'status'  => 'failure',
				'message' => __( 'Failure: API token for Flickr service is not set.
				 Please setup your access token via settings->iki toolkit(plugin)->API keys', 'iki-toolkit' )
			);

		} else {

			$r        = $instance->get_data( $method, $data );
			$wp_error = is_wp_error( $r );

			if ( $wp_error || 'fail' == $r['stat'] || 0 == $r ) {

				$resp = array(
					'status' => 'failure'
				);

				if ( $wp_error ) {

					$message = json_encode( $r->get_error_messages() );

				} elseif ( 100 == $r['code'] ) {
					// api key fail.
					$message = __( 'Failure: Flickr API Key is incorrect, please check your key.', 'iki-toolkit' );

				} elseif ( 1 == $r['code'] || 2 == $r['code'] ) {

					if ( 'get_user' == $method ) {
						//user not found
						$message = sprintf( __( 'Failure: user "%1$s" not found', 'iki-toolkit' ), $data['user_id'] );

					} else {
						//photoset not found

						$message = sprintf( __( 'Failure: photoset "%1$s" not found', 'iki-toolkit' ), $data['photoset_id'] );
					}
				}

				$resp['message'] = $message;
			} else {
				//success

				$resp = array(
					'status' => 'success'
				);

				if ( 'get_user' == $method ) {

					$message = sprintf( __( 'Success: user "%1$s" found.', 'iki-toolkit' ), $data['user_id'] );

				} else {
					//photoset

					$message = sprintf( __( 'Success: photoset "%1$s" found.', 'iki-toolkit' ), $data['photoset_id'] );

				}

				$resp['message'] = $message;
			}
		}

		$r = json_encode( $resp );

		return $r;
	}

	/**
	 * @param $method
	 * @param $data
	 *
	 * @return mixed|string
	 */
	protected function handle_pinterest_check( $method, $data ) {

		$instance = new Iki_Pinterest_API();

		$r    = $instance->get_data( $method, $data );
		$resp = $r;

		if ( isset( $r['@attributes'] ) ) {

			if ( 'get_user_latest_pins' == $method ) {

				$resp = array(
					'status'  => 'success',
					'message' => sprintf( __( 'Success: user  "%1$s" found', 'iki-toolkit' ), $data['user'] )

				);
			} elseif ( 'get_user_board' == $method ) {

				$resp = array(
					'status'  => 'success',
					'message' => sprintf( __( 'Success: "%1$s" board found.', 'iki-toolkit' ), $data['boardname'] )
				);
			}
		} else {

			//failure
			if ( 0 == $r ) {

				$message = __( 'Server error or service API unavailable', 'iki-toolkit' );
			} else {
				$message = $r['message'];
			}
			$resp = array(
				'status'  => 'failure',
				'message' => $message
			);
		}

		$r = json_encode( $resp );

		return $r;
	}

	/**
	 * Delete external data transient cache
	 */
	public function iki_delete_ext_cache() {

		check_ajax_referer( 'iki-admin-nonce-check' );

		if ( isset( $_POST['post_id'] ) ) {
			//get post meta
			$trans_key = get_post_meta( $_POST['post_id'], 'ext_trans_key', false );
			if ( $trans_key ) {
				foreach ( $trans_key as $item ) {
					delete_transient( $item );
				}
			}
		}

		return false;
	}

	/**
	 * Register ajax callbacks
	 */
	public function register_ajax_callbacks() {
		add_action( 'wp_ajax_iki_check_external_data', array(
			$this,
			'iki_check_external_data'
		) );
		add_action( 'wp_ajax_iki_delete_ext_cache', array(
			$this,
			'iki_delete_ext_cache'
		) );
	}
}

