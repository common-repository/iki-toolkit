<?php

/**
 * Wrapper class around unyson methods for getting options
 */
class Iki_Toolkit_Unyson_Options_Helper implements Iki_Toolkit_IOptions_Helper {

	/**
	 * @inheritdoc
	 */
	public function get_option( $option_name, $default_value = null, $get_original_value = null ) {
		return fw_get_db_settings_option( $option_name, $default_value );
	}

	/**
	 * @inheritdoc
	 */
	public function get_term_option( $term_id, $taxonomy, $option_name = null, $default_value = null, $get_original_value = null ) {

		return fw_get_db_term_option( $term_id, $taxonomy, $option_name, $default_value );

	}

	/**
	 * @inheritdoc
	 */
	public function get_post_option( $post_id = null, $option_name = null, $default_value = null, $get_original_value = null ) {

		return fw_get_db_post_option( $post_id, $option_name, $default_value );
	}

	/**
	 * @inheritdoc
	 */
	public function get_customizer_option( $option_name, $default_value = null ) {

		return fw_get_db_customizer_option( $option_name, $default_value );
	}
}