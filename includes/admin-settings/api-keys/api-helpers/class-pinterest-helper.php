<?php


/**
 * Helper class for communicating with  Pinterest API class
 */
class Iki_Pinterest_Helper extends Iki_Abstract_Service_Helper {


	protected $show_latest;
	protected $board_name;

	public function __construct( $post_id ) {
		parent::__construct( $post_id );
		$this->api  = new Iki_Pinterest_API( null );
		$this->name = 'pinterest';
	}

	/** @inheritdoc */
	public function get_options() {

		parent::get_options();

		if ( ! function_exists( 'iki_theme' ) ) {
			return;
		}
		$username          = '';
		$images_per_page   = 20;//pinterest xml is max 25 (22?)
		$this->show_latest = true;

		$service_data = iki_toolkit()->get_instance()->get_post_option( $this->post_id, 'external_service', null );


		if ( $service_data && 'pinterest' === $service_data['service'] ) {

			$service_data = $service_data[ $service_data['service'] ];
			$username     = $service_data['username'];
			$username     = trim( $username );
			if ( empty( $username ) ) {
				$username = 'asdjfhalkshfjlakjhdsflakjhds';//fake username forces error in frontend.
			}

			$board_name       = $service_data['boardname'];
			$this->board_name = trim( $board_name );
			if ( ! empty( $this->board_name ) ) {
				$this->show_latest = false;
			}

		}


		if ( $this->show_latest ) {
			//show stream
			$user_images = $this->api->get_user_latest_pins( array(
				'user' => $username
			) );

		} else {

			// show board
			$user_images = $this->api->get_user_board( array(
				'user'      => $username,
				'boardname' => $this->board_name
			) );
		}


		$this->options = array(
			'token'         => $this->api->get_token(),
			'userName'      => $username,
			'userImages'    => $user_images,
			'show_latest'   => $this->show_latest,
			'imagesPerPage' => $images_per_page

		);

		return $this->options;
	}

	/**
	 * @param $data
	 *
	 * @return bool|mixed
	 */
	public function get_user( $data ) {
		if ( $this->show_latest ) {
			//get user profile link
			return $this->api->get_user_link( $data['username'] );
		} elseif ( $this->board_name ) {
			// get board link
			return $this->api->get_board_link( $data['username'], $this->board_name );
		}

		return false;

	}
}