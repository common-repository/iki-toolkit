<?php

/**
 * Class that handles creation of options for "Social Profiles" section
 */
class Iki_Social_Profiles_Admin_Section extends Iki_Admin_Options_Section {

	public function __construct( $index = 10 ) {
		parent::__construct( $index );
		$this->name                 = 'social_options';
		$this->option_name          = 'iki_toolkit_social_profiles';
		$this->title                = __( 'Social Profiles', 'iki-toolkit' );
		$this->settings_sections_id = 'iki_toolkit_social_settings_section';

		$this->default_options = array();
	}

	/**
	 * Section description
	 */
	public function section_description() {

		echo '<p>' . __( 'Setup global links to social profiles on various social networks.', 'iki-toolkit' ) . '</p>';
	}

	/** Print section option fields
	 *
	 * @param $data
	 */
	public function print_social_service_option( $data ) {


		// first, we read the social options collection
		$options = get_option( $this->option_name );

		// next, we need to make sure the element is defined in the options. if not, we'll set an empty string.
		$url = '';

		if ( isset( $options[ $data['service'] ] ) ) {
			$url = esc_url( $options[ $data['service'] ] );
		}

		// render the output
		printf( '<input type="text" id="%1$s" name="%3$s[%1$s]" value="%2$s" />',
			$data['service'],
			$url,
			$this->option_name );

	}

	/**
	 * Generate html output for options
	 */
	protected function setup_option_fields() {

		add_settings_field(
			'twitter',
			'Twitter',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'twitter'
			)
		);
		add_settings_field(
			'facebook',
			'Facebook',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'facebook'
			)
		);
		add_settings_field(
			'instagram',
			'Instagram',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'instagram'
			)
		);
		add_settings_field(
			'pinterest',
			'Pinterest',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'pinterest'
			)
		);
		add_settings_field(
			'flickr',
			'Flickr',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'flickr'
			)
		);
		add_settings_field(
			'500px',
			'500Px',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => '500px'
			)
		);
		add_settings_field(
			'dribbble',
			'Dribbble',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'dribbble'
			)
		);
		add_settings_field(
			'linkedin',
			'LinkedIn',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'linkedin'
			)
		);
		add_settings_field(
			'vk',
			'VK',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'vk'
			)
		);
		add_settings_field(
			'weibo',
			'Weibo',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'weibo'
			)
		);
		add_settings_field(
			'reddit',
			'Reddit',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'reddit'
			)
		);
		add_settings_field(
			'tumblr',
			'Tumblr',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'tumblr'
			)
		);
		add_settings_field(
			'myspace',
			'MySpace',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'myspace'
			)
		);
		add_settings_field(
			'github',
			'Github',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'github'
			)
		);

		add_settings_field(
			'bitbucket',
			'bitbucket',
			array( $this, 'print_social_service_option' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'service' => 'bitbucket'
			)
		);
	}


	/**
	 * Sanitization callback for the social options. Since each of the social options are text inputs,
	 * this function loops through the incoming option and strips all tags and slashes from the value
	 * before serializing it.
	 *
	 * @params    $input    The unsanitized collection of options.
	 *
	 * @returns            The collection of sanitized values.
	 */
	public function sanitize_options( $input ) {

		// Define the array for the updated options
		$output = array();

		// Loop through each of the options sanitizing the data
		foreach ( $input as $key => $val ) {

			if ( isset ( $input[ $key ] ) ) {
				$output[ $key ] = esc_url_raw( strip_tags( stripslashes( $input[ $key ] ) ) );
			} // end if

		} // end foreach

		// Return the new collection
		return apply_filters( 'iki_toolkit_sanitize_social_options', $output, $input );

	} // end sandbox_theme_sanitize_social_options

	/** Set default options
	 * @return mixed|void
	 */
	public function default_options() {
		if ( false == get_option( $this->option_name ) ) {
			add_option( $this->option_name,
				apply_filters( $this->option_name . '_defaults', $this->default_options ) );
		} // end if
	}
}
