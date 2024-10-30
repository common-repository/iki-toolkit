<?php

/**Helper class for communicating with flickr API */
class Iki_Flickr_Helper extends Iki_Abstract_Service_Helper {


	/**
	 * Iki_Flickr_Helper constructor.
	 *
	 * @param $post_id
	 */
	public function __construct( $post_id ) {
		parent::__construct( $post_id );

		$this->api  = new Iki_Flickr_Api( null );
		$this->name = 'flickr';
	}


	/** Get default options
	 * @return array
	 */
	public function get_options() {

		if ( ! function_exists( 'iki_theme' ) ) {
			return;
		}
		parent::get_options();

		$start_page      = 1;//fixed for now.
		$show_stream     = true;
		$images_per_page = 25;
		$username        = '';
		$photoset_id     = '';
		$service_data    = iki_toolkit()->get_instance()->get_post_option( $this->post_id, 'external_service', null );
		$high_resolution = false;

		if ( $service_data && 'flickr' === $service_data['service'] ) {

			$service_data = $service_data[ $service_data['service'] ];

			$username = $service_data['username'];
			$username = trim( $username );

			if ( empty( $username ) ) {
				$username = 'asdjfhalkshfjlakjhdsfasdfasd';//fake username forces error in frontend.
			}

			$photoset_id = $service_data['photoset_id'];
			$photoset_id = trim( $photoset_id );

			if ( ! empty( $photoset_id ) ) {
				$show_stream = false;
			}
			$high_resolution = Iki_Toolkit_Utils::string_to_boolean( $service_data['high_resolution'] );

		}


		if ( $show_stream ) {
			//show stream
			$user_images = $this->api->get_user_photos( array(
				'user_id' => $username
			) );

		} else {
			// show photoset
			$user_images = $this->api->get_photoset_photos( array(
				'photoset_id' => $photoset_id
			) );
		}

		$this->options = array(
			'token'          => $this->api->get_token(),
			'userName'       => $username,
			'userImages'     => $user_images,
			'startPage'      => $start_page,
			'imagesPerPage'  => $images_per_page,
			'disablePaging'  => false,
			'showStream'     => $show_stream,
			'highResolution' => $high_resolution

		);

		return $this->options;
	}
}