<?php

/**
 * Class for handling the display of external service data in frontend
 */
class Iki_Toolkit_Ext_Service_Front_End {

	protected $_service_name;
	public static $service_name;
	protected $post_id;
	protected $ext_link_text = '';

	/** @var Iki_Abstract_Service_Helper */
	protected $service_helper;

	public function __construct( $service_name, $post_id ) {

		$this->_service_name = $service_name;
		$this->post_id       = $post_id;

	}

	public function init() {

		switch ( $this->_service_name ) {
			case 'flickr':
				$this->service_helper = new Iki_Flickr_Helper( $this->post_id );
				break;
			case 'pinterest' :
				$this->service_helper = new Iki_Pinterest_Helper( $this->post_id );
				break;
			default:
		}

		$r = '';
		switch ( $this->_service_name ) {
			case 'flickr':
				$r = esc_attr_x( 'View image on Flickr.com',
					'External service lightbox link text',
					'iki-toolkit' );
				break;
			case 'pinterest' :
				$r = esc_attr_x( 'View image on Pinterest.com',
					'External service lightbox link text',
					'iki-toolkit' );
				break;
			default:
		}

		self::$service_name  = $this->get_service_name();
		$this->ext_link_text = apply_filters( 'iki_external_service_lightbox_link_text', $r, $this->_service_name );
	}

	/** Get service helper
	 * @return Iki_Abstract_Service_Helper
	 */
	public function get_service_helper() {
		return $this->service_helper;
	}

	/** Get service name
	 * @return mixed
	 */
	public function get_service_name() {
		return $this->service_helper->get_service_name();
	}

	/** Get text for external links
	 * @return string
	 */
	public function get_ext_link_text() {
		return $this->ext_link_text;
	}

	/**
	 * Wrapper method for getting service options
	 */
	public function get_options() {
		return $this->service_helper->get_options();
	}

	/** Get service user data
	 *
	 * @param $username
	 *
	 * @return mixed
	 */
	public function get_user( $username ) {

		return $this->service_helper->get_user( array( 'username' => $username ) );
	}
}
