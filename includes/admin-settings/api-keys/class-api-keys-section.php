<?php

/** Creates section for API Keys options */
class Iki_API_Keys_Admin_Section extends Iki_Admin_Options_Section {

	public function __construct( $index = 10 ) {
		parent::__construct( $index );
		$this->name                 = 'api_key_options';
		$this->option_name          = 'iki_toolkit_api_keys';
		$this->title                = __( 'API Keys', 'iki-toolkit' );
		$this->settings_sections_id = 'iki_toolkit_api_keys_section';

		$this->default_options = array(
			'flickr_api_key' => ''
		);

		$api_data_check = new Iki_External_Api_Data_Check();
		$api_data_check->register_ajax_callbacks();

		add_filter( 'iki_toolkit_exports', array( $this, 'export_translations' ) );
	}

	public function export_translations( $exports ) {
		return $exports;
	}

	/**
	 * Print section description
	 */
	public function section_description() {
		echo '<p>' . __( 'Setup your API keys for various online services', 'iki-toolkit' ) . '</p>';
	}


	/**
	 * Setup section option fields
	 */
	protected function setup_option_fields() {

		add_settings_field(
			'flickr_api_key',
			'Flickr API key',
			array( $this, 'print_api_key_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service'  => 'flickr_api_key',
				'btn_id'   => 'iki-test-flickr-api',
				'btn_text' => __( 'Test Flickr API key', 'iki-toolkit' )
			)
		);
	}

	/** Print section options
	 *
	 * @param $data array section data
	 */
	public function print_api_key_option( $data ) {


		// first, we read the social options collection
		$options = get_option( $this->option_name );

		// next, we need to make sure the element is defined in the options. if not, we'll set an empty string.
		$url = '';

		if ( isset( $options[ $data['service'] ] ) ) {
			$url = esc_html( $options[ $data['service'] ] );
		} // end if

		// render the output
		printf( '<div class="iki-api-test-wrap" id="%1$s">', $data['btn_id'] );
		printf( '<input type="text" id="iki-%1$s" name="%3$s[%1$s]" value="%2$s" />',
			$data['service'],
			$url,
			$this->option_name );

		echo $this->ext_api_key_test( $data['btn_id'], $data['btn_text'] );
		echo '</div>';
	}

	/** Generate html wrapper for every API Key option
	 *
	 * @param $btn_id string button id
	 * @param $btn_text string  button text
	 * @param string $success_text Success text (key is okay)
	 * @param string $fail_text Failure (key is wrong)
	 * @param string $timeout_text Server tiemout.
	 *
	 * @return string Generated html
	 */
	protected function ext_api_key_test( $btn_id, $btn_text, $success_text = '', $fail_text = '', $timeout_text = '' ) {
		$success_text = ( $success_text ) ? $success_text : __( 'API key is ok', 'iki-toolkit' );
		$fail_text    = ( $fail_text ) ? $fail_text : __( 'API key is wrong', 'iki-toolkit' );
		$timeout_text = ( $timeout_text ) ? $timeout_text : __( 'Server timeout out, please try again', 'iki-toolkit' );

		$r = sprintf( '<button class="button">%2$s</button>
			<span data-iki-success="%3$s" data-iki-failure="%4$s"  data-iki-timeout="%5$s" class="spinner"></span>
		<p class="updated notice hidden"></p>
		<p class="error notice hidden"></p>',
			$btn_id,
			$btn_text,
			$success_text,
			$fail_text,
			$timeout_text );

		return $r;
	}


	/** Sanitize api key options
	 *
	 * @param array $input data to sanitize
	 *
	 * @return mixed sanitized data
	 */
	public function sanitize_options( $input ) {

		// Define the array for the updated options
		$output = array();

		// Loop through each of the options sanitizing the data
		foreach ( $input as $key => $val ) {

			if ( isset ( $input[ $key ] ) ) {
				$output[ $key ] = sanitize_text_field( $input[ $key ] );
			} // end if

		} // end foreach

		// Return the new collection
		return apply_filters( 'iki_toolkit_sanitize_social_options', $output, $input );

	} // end sandbox_theme_sanitize_social_options

	public function default_options() {

		if ( false == get_option( $this->option_name ) ) {
			add_option( $this->option_name,
				apply_filters( $this->option_name . '_defaults', $this->default_options ) );
		} // end if

	}
}

