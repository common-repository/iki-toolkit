<?php

/**Abstract class for external service helpers*/
class Iki_Abstract_Service_Helper {

	protected $options;
	/**@var Iki_Abstract_External_API $api */
	protected $api;
	protected $name;

	protected $post_id;

	/**
	 * Iki_Abstract_Service_Helper constructor.
	 *
	 * @param $post_id
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/** Get service name
	 * @return mixed
	 */
	public function get_service_name() {
		return $this->name;
	}

	/**
	 * Get options for particular service
	 */
	public function get_options() {

	}

	/**
	 * @param $data
	 *
	 * @return mixed
	 */
	public function get_user( $data ) {

		return $this->api->get_user( $data );
	}
}