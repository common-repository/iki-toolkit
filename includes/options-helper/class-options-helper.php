<?php

/**
 * Wrapper classs for getting the options from the framework
 * If unyson framework is not installed it just returns default options
 */

class Iki_Toolkit_Options_Helper implements Iki_Toolkit_IOptions_Helper {

	protected $prefix = '';

	/**
	 * Iki_Options_Helper constructor.
	 *
	 * @param $prefix
	 */
	public function __construct( $prefix = '' ) {
		$this->prefix = $prefix;
	}

	/**
	 * @inheritdoc
	 */
	public function get_option( $option_name, $default_value = null, $get_original_value = null ) {
		return $default_value;
	}

	/**
	 * @inheritdoc
	 */
	public function get_customizer_option( $option_name, $default_value = null, $getOriginalValue = null ) {
		return $default_value;
	}

	/**
	 * @inheritdoc
	 */
	public function get_post_option( $post_id = null, $option_name = null, $default_value = null, $get_original_value = null ) {
		return $default_value;
	}

	/**
	 * @inheritdoc
	 */
	public function get_term_option( $term_id, $taxonomy, $option_name = null, $default_value = null, $get_original_value = null ) {
		return $default_value;
	}
}