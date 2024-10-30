<?php

/**
 * Abstract class for creating plugin sections
 */
abstract class Iki_Admin_Options_Section {
	public $title;
	protected $index;
	public $name;//used as tab name
	public $option_name;
	public $default_options;
	public $wrap_class = '';
	protected $settings_sections_id;

	public function __construct( $index = 10 ) {
		$this->index = $index;
		add_filter( 'iki_toolkit_settings_sections', array( $this, 'register_iki_toolkit_section' ), $this->index );
		add_action( 'admin_init', array( $this, 'initialize_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_section_scripts' ) );
	}

	/** Add underscore script dependancy
	 *
	 * @param $hook
	 */
	public function add_section_scripts( $hook ) {
		if ( 'settings_page_iki_toolkit_options' == $hook ) {
			wp_enqueue_script( 'underscore' );
		}
	}

	/** setup option fields
	 * @return mixed
	 */
	abstract protected function setup_option_fields();

	/** Setup section description
	 * @return mixed
	 */
	abstract public function section_description();

	/** setup default options
	 * @return mixed
	 */
	abstract public function default_options();

	/** Sanitize options
	 *
	 * @param $input array to be sanitized
	 *
	 * @return mixed  array sanitized options
	 */
	abstract public function sanitize_options( $input );

	/**
	 * Setup settings fields
	 */
	public function settings_fields() {
		settings_fields( $this->option_name );
	}

	/**
	 * setup settings sections
	 */
	public function do_settings_sections() {
		do_settings_sections( $this->option_name );
	}


	/** Respond to section registration request by Iki_Admin_Settings class
	 *
	 * @param $sections
	 *
	 * @return mixed
	 */
	public function register_iki_toolkit_section( $sections ) {
		array_push( $sections, $this );

		return $sections;
	}

	/**
	 * Initialize the settings options
	 */
	function initialize_options() {


		add_settings_section(
			$this->settings_sections_id,            // ID used to identify this section and with which to register options
			'',
			array( $this, 'section_description' ),
			$this->option_name // Page on which to add this section of options
		);

		$this->setup_option_fields();

		register_setting(
			$this->option_name,
			$this->option_name,
			array( $this, 'sanitize_options' )

		);

	}


}

