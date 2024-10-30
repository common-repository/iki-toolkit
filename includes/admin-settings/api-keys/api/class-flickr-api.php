<?php

/*Handle flickr api key requests*/

class Iki_Flickr_Api extends Iki_Abstract_External_API {

	private $end_points = array(
		'get_user'            => 'https://api.flickr.com/services/rest/?method=flickr.people.getInfo',
		'get_user_photos'     => 'https://api.flickr.com/services/rest/?method=flickr.people.getPublicPhotos',
		'get_photoset_info'   => 'https://api.flickr.com/services/rest/?method=flickr.photosets.getInfo',
		'get_photoset_photos' => 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos',
		'find_by_username'    => 'https://api.flickr.com/services/rest/?method=flickr.people.find_by_username'
	);

	private $transient_keys = array(
		'get_user'            => 'ikif==id==',
		'get_user_photos'     => 'ikifstream_==id==',
		'get_photoset_info'   => 'ikifseti==id==',
		'get_photoset_photos' => 'ikifset==id==',
		'find_by_username'    => 'ikifu==id==' // id gets transformed to username
	);

	/**
	 * Iki_Flickr_Api constructor.
	 *
	 * @param null $access_token
	 */
	public function __construct( $access_token = null ) {

		parent::__construct( $access_token );
	}


	/** Setup transient for transient key
	 *
	 * @param $transient_key
	 *
	 * @return string
	 */
	protected function setup_transient( $transient_key ) {
		//max transient key is 45 characters
		//http://www.barrykooij.com/maximum-option-transient-key-length/
		$transient_key = $transient_key . $this->get_token();
		$transient_key = substr( $transient_key, 0, 44 );

		return $transient_key;
	}

	/** Find by username
	 *
	 * @param $data
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	public function find_by_username( $data ) {

		return $this->handle_request( $data, $this->end_points['find_by_username'], $this->setup_transient( $this->transient_keys['find_by_username'] ) );
	}

	/** Get user data
	 *
	 * @param $data
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	public function get_user( $data ) {

		if ( isset( $data['username'] ) ) {
			$data['user_id'] = $data['username'];
			unset( $data['username'] );
		}

		return $this->handle_request( $data, $this->end_points['get_user'], $this->setup_transient( $this->transient_keys['get_user'] ) );

	}

	/** Get user photos
	 *
	 * @param $data
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	public function get_user_photos( $data ) {

		return $this->handle_request( $data,
			$this->end_points['get_user_photos'],
			$this->setup_transient( $this->transient_keys['get_user_photos'] ) );
	}

	/** Get photoset data
	 *
	 * @param $data
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	public function get_photoset_info( $data ) {

		return $this->handle_request( $data,
			$this->end_points['get_photoset_info'],
			$this->setup_transient( $this->transient_keys['get_photoset_info'] ) );
	}

	/** Get photoset photos
	 *
	 * @param $data
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	public function get_photoset_photos( $data ) {

		return $this->handle_request( $data,
			$this->end_points['get_photoset_photos'],
			$this->setup_transient( $this->transient_keys['get_photoset_photos'] ) );
	}

	/** Fire the request ( maybe get it from cache )
	 *
	 * @param $data
	 * @param $end_point
	 * @param $transient_keys
	 *
	 * @return array|bool|int|mixed|object|string
	 */
	private function handle_request( $data, $end_point, $transient_keys ) {

		$r = 0;

		if ( isset( $data['cache'] ) && 'disabled' == $data['cache'] ) {
			$cache_data = false;
		} else {
			$cache_data = true;
		}
		$id = isset( $data['user_id'] ) ? $data['user_id'] : null;// check for user id

		if ( ! $id ) {
			$id = isset( $data['photoset_id'] ) ? $data['photoset_id'] : null;
		}

		if ( ! $id ) {
			$id = isset( $data['username'] ) ? $data['username'] : null;
		}
		if ( $id ) {
			unset( $data['cache'] );
			$data['per_page']       = 500;//harcode to 500 (max value)
			$data['current_page']   = 1;//hard code
			$data['media']          = 'photos'; // hardcode it to photos.
			$data['nojsoncallback'] = '1';
			$data['format']         = 'json';

			$transient_keys = str_replace( '==id==', $id, $transient_keys );

			$transient_keys = md5( $transient_keys );
			$transient_keys = substr( $transient_keys, 0, 44 );

			$dataCache = get_transient( $transient_keys );

			if ( $dataCache && $cache_data ) {
				$r = $dataCache;
			} else {
				$apiUrl = $this->construct_api_url( $data, $end_point );


				$response = wp_remote_get( $apiUrl, array(
					'decompress' => false,
					'timeout'    => 15
				) );

				if ( is_wp_error( $response ) ) {
					return $response;
				}

				$r = wp_remote_retrieve_body( $response );
				if ( is_wp_error( $r ) ) {
					return 0;
				}

				$r = json_decode( $r, true );

				if ( $r['stat'] !== 'fail' && $cache_data ) {

					$expiration = apply_filters( 'iki_external_service_transient_expiration', HOUR_IN_SECONDS * 24, 'flickr' );
					set_transient( $transient_keys, $r, $expiration );
					$this->update_transient_list( $transient_keys );

					global $post;
					if ( isset( $post ) && ! defined( 'DOING_AJAX' ) ) {
						add_post_meta( $post->ID, 'ext_trans_key', $transient_keys );
					}

				}
			}

		}

		return $r;
	}

	/** Construct url for the API
	 *
	 * @param $replace
	 * @param $end_point
	 *
	 * @return string
	 */
	public function construct_api_url( $replace, $end_point ) {

		$s = '&api_key=' . $this->get_token();
		foreach ( $replace as $key => $value ) {

			$s .= '&' . $key . '=' . $value;
		}

		return $end_point . $s;

	}

	/** Wrap json in js callback
	 *
	 * @param $data
	 *
	 * @return string
	 */
	protected function wrap_json( $data ) {
		return 'jsoncallback(' . $data . ');';
	}

	/** Get the service token.
	 * @return mixed|null
	 */
	public function get_token() {

		if ( is_null( $this->access_token ) ) {
			$token    = '';
			$api_keys = get_option( 'iki_toolkit_api_keys' );
			if ( $api_keys && isset( $api_keys['flickr_api_key'] ) ) {

				$token = $api_keys['flickr_api_key'];
			}

			$this->access_token = $token;
		}

		return $this->access_token;
	}
}