<?php

/**
 * Interface for options helper classes
 */

interface Iki_Toolkit_IOptions_Helper {

	/**
	 * Get theme option
	 *
	 * @param $option_name
	 * @param $default_value
	 * @param $get_original_value
	 *
	 * @return mixed
	 */
	public function get_option( $option_name, $default_value, $get_original_value );

	/**
	 * Get customizer option
	 *
	 * @param $option_name
	 * @param $default_value
	 *
	 * @return mixed
	 */
	public function get_customizer_option( $option_name, $default_value );

	/**
	 * Get term option
	 *
	 * @param $term_id
	 * @param $taxonomy
	 * @param null $option_name
	 * @param null $default_value
	 * @param null $get_original_value
	 *
	 * @return mixed
	 */
	public function get_term_option( $term_id, $taxonomy, $option_name = null, $default_value = null, $get_original_value = null );

	/**
	 * Get post option
	 *
	 * @param null $post_id
	 * @param null $option_name
	 * @param null $default_value
	 * @param null $get_original_value
	 *
	 * @return mixed
	 */
	public function get_post_option( $post_id = null, $option_name = null, $default_value = null, $get_original_value = null );

}