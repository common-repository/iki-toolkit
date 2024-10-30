<?php

/*Abstract API class for external api requests*/

abstract class Iki_Abstract_External_API {

	protected $access_token = null;

	public abstract function get_token();

	public function __construct( $access_token = null ) {
		$this->access_token = $access_token;
	}

	/** Helper method when request comes from ajax callback
	 *
	 * @param $method string Method to call
	 * @param $data array Data to pass
	 *
	 * @return mixed bool Return actuall result
	 */
	public function get_data( $method, $data ) {

		$r = false;
		if ( method_exists( get_class( $this ), $method ) ) {
			$r = $this->$method( $data );
		}

		return $r;
	}


	/** Wrapper for better json decode
	 *
	 * @param $jsonp
	 * @param bool $assoc
	 *
	 * @return array|mixed|object
	 * @link http://stackoverflow.com/questions/5081557/extract-jsonp-resultset-in-php
	 */
	function jsonp_decode( $jsonp, $assoc = false ) { // PHP 5.3 adds depth as third parameter to json_decode
		if ( $jsonp[0] !== '[' && $jsonp[0] !== '{' ) { // we have JSONP
			$jsonp = substr( $jsonp, strpos( $jsonp, '(' ) );
		}

		return json_decode( trim( $jsonp, '();' ), $assoc );
	}

	public abstract function get_user( $data );

	/** Update transient list
	 *
	 * @param string $transient_key Actuall transient key
	 * @param string $prefix
	 */
	protected function update_transient_list( $transient_key, $prefix = 'iki_tk' ) {

		Iki_Toolkit_Utils::update_transient_list( $transient_key, $prefix );
	}
}