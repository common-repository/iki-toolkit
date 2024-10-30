<?php

/*Class that handles loading content from Pinterest*/

class Iki_Pinterest_API extends Iki_Abstract_External_API {

	protected $error_construct;
	private $end_points = array(
		'get_user_latest_pins' => 'https://pinterest.com/==user==/feed.rss',
		'get_user_board'       => 'https://pinterest.com/==user==/==boardname==.rss'

	);

	private $transient_keys = array(
		'get_user_latest_pins' => 'iki_pi_u_l_==user==',
		'get_user_board'       => 'iki_pi_u_b_==user==_==boardname=='
	);

	protected $user_link = 'https://www.pinterest.com/==user==/';
	protected $board_link = 'https://www.pinterest.com/==user==/==boardname==/';

	/** Get board link
	 *
	 * @param $username
	 * @param $board_link
	 *
	 * @return mixed
	 */
	public function get_board_link( $username, $board_link ) {

		$user = str_replace( '==user==', $username, $this->board_link );

		return str_replace( '==boardname==', $board_link, $user );

	}

	/** Get link to the user
	 *
	 * @param $username
	 *
	 * @return mixed
	 */
	public function get_user_link( $username ) {
		return str_replace( '==user==', $username, $this->user_link );
	}


	/**
	 * Iki_Pinterest_API constructor.
	 *
	 * @param null $access_token
	 */
	public function __construct( $access_token = null ) {
		parent::__construct( $access_token );
		$this->error_construct = array(
			'message' => __( 'Error connecting to pinterest server', 'iki-toolkit' ),
			'status'  => 'failure'
		);
	}

	/** Noop method
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	public function get_user( $data ) {
		return false;
	}

	/** Get latest pins from the user
	 *
	 * @param $data
	 *
	 * @return array
	 */
	public function get_user_latest_pins( $data ) {

		$t = $this->setup_transient( $data, $this->transient_keys['get_user_latest_pins'] );

		$r = $this->handle_request( $data, $this->end_points['get_user_latest_pins'], $t );

		$r = $this->maybe_handle_error_msg( $r, sprintf( __( 'Failure: "%1$s" user not found.', 'iki-toolkit' ), $data['user'] ) );

		return $r;
	}

	/**
	 * Maybe handle error message
	 *
	 * @param $r string | array | WP_Error Original response
	 * @param $msg string error message
	 *
	 * @return $r array Error response
	 */
	protected function maybe_handle_error_msg( &$r, $msg ) {


		if ( ! is_array( $r ) ) {
			if ( is_string( $r ) ) {
				$r = array(
					'message' => $msg,
					'status'  => 'failure'
				);
			} elseif ( is_wp_error( $r ) ) {
				$r = array(
					'status'  => 'failure',
					'message' => json_encode( $r->get_error_messages() )
				);
			} else {

				$r = $this->error_construct;
			}
		}

		return $r;
	}

	/** Get user board
	 *
	 * @param $data array
	 *
	 * @return array|int|mixed|object|SimpleXMLElement|string
	 */
	public function get_user_board( $data ) {

		$t = $this->setup_transient( $data, $this->transient_keys['get_user_board'] );
		$r = $this->handle_request( $data, $this->end_points['get_user_board'], $t );
		$r = $this->maybe_handle_error_msg( $r, sprintf( __( 'Failure: "%1$s" board not found.', 'iki-toolkit' ), $data['boardname'] ) );

		return $r;
	}

	/**
	 * @param $data
	 * @param $transient_key
	 *
	 * @return mixed
	 */
	private function setup_transient( $data, $transient_key ) {

		if ( isset( $data['user'] ) ) {
			$transient_key = str_replace( '==user==', $data['user'], $transient_key );

		}
		if ( isset( $data['boardname'] ) ) {
			$transient_key = str_replace( '==boardname==', $data['boardname'], $transient_key );
		}

		return $transient_key;
	}

	/**
	 * @param $data
	 * @param $end_point
	 * @param $transient_key
	 *
	 * @return array|int|mixed|object|SimpleXMLElement|string
	 */
	private function handle_request( $data, $end_point, $transient_key ) {

		// check transient
		$r = 0;

		if ( isset( $data['user'] ) ) {

			if ( isset( $data['cache'] ) && 'disabled' == $data['cache'] ) {
				$cache_data = false;
			} else {
				$cache_data = true;
			}

			$transient_key = md5( $transient_key );
			$dataCache     = get_transient( $transient_key );

			if ( $dataCache ) {
				$r = $dataCache;

			} else {
				$apiUrl = $this->construct_api_url( $data, $end_point );

				$response = wp_remote_get( $apiUrl, array(
					'timeout' => 15
				) );

				if ( is_wp_error( $response ) ) {
					return $response;
				}

				$r                 = wp_remote_retrieve_body( $response );
				$original_response = $r;

				if ( is_wp_error( $r ) ) {
					return $response;
				}

				libxml_use_internal_errors( true );
				$r = simplexml_load_string( $r );
				libxml_clear_errors();

				if ( $r ) {
					$r = json_encode( $r );
					if ( $r ) {

						$r = json_decode( $r, true );
					}
					if ( ! is_null( $r ) ) {

						if ( $cache_data ) {

							$expiration = apply_filters( 'iki_external_service_transient_expiration', HOUR_IN_SECONDS * 24, 'pinterest' );
							set_transient( $transient_key, $r, $expiration );
							$this->update_transient_list( $transient_key );
							global $post;
							if ( isset( $post ) && ! defined( 'DOING_AJAX' ) ) {
								add_post_meta( $post->ID, 'ext_trans_key', $transient_key );
							}
						}
					}
				} else {
					if ( stripos( $original_response, '<?xml version' ) === false ) {
						$r = '';
					}

				}
			}

		}

		return $r;
	}

	/** Construct api url
	 *
	 * @param $data
	 * @param $end_point
	 *
	 * @return mixed
	 */
	public function construct_api_url( $data, $end_point ) {
		$end_point = $this->setup_transient( $data, $end_point );

		return $end_point;
	}

	/** Get api token stub. Pinterest doesn't need token.
	 * @return string
	 */
	public function get_token() {
		return 'pinterest_token_stub';
	}
}