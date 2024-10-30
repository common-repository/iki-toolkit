<?php

/**
 * Class that implements logic that drives the plugin.
 */
class Iki_Toolkit {

	private static $class = null;

	public static $VER = '1.1.20';

	//template that is used by wordpress
	protected $page_template;

	/**@var Iki_Toolkit_Utils $toolkit_utils */
	public $toolkit_utils;

	/**@var Iki_IOptions_Helper $option_helper */
	protected $option_helper;

	/*Active location data*/
	protected $location_info;

	/*Hero section data*/
	protected $hero_section = null;

	/*Featured post data*/
	protected $featured_posts = null;

	/*chosen share services*/
	protected $chosen_share_services = array();

	/*default share services*/
	protected $default_chose_share_services = array();

	/** Plugin uses singleton pattern, always return the same instance.
	 * @return Iki_Toolkit|null
	 */
	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( IKI_TOOLKIT_ROOT );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'iki_toolkit_template_path', 'partials/' );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {


		$GLOBALS['iki_toolkit'] = array(
			'data'    => array(),
			'flags'   => array(
				'wp_customizer_active' => is_customize_preview(),
				'printing_grid'        => false
			),
			'exports' => array()
		);

		$admin_data                         = iki_toolkit_admin_data();
		$this->default_chose_share_services = $admin_data['default_share_services'];

		$GLOBALS['iki_toolkit']['exports']['flags'] = array(
			'wp_customizer_active' => is_customize_preview()
		);

		$this->toolkit_utils = new Iki_Toolkit_Utils();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_only_javascript' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ), 9 );
		add_action( 'wp_enqueue_scripts', array( $this, 'late_enqueue_public_scripts' ), 1000 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_css' ) );
		add_action( 'customize_preview_init', array( $this, '_action_iki_customizer_live_options_preview' ) );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'iki_customizer_controls' ) );

		add_filter( 'template_include', array( $this, 'template_include' ), 1000000 );

		//use get_header hook to start collecting meta options from the theme
		add_action( 'get_header', array( $this, 'determine_active_location' ), 9 );
		add_action( 'get_header', array( $this, 'setup_options' ), 9 );
		add_filter( 'body_class', array( $this, 'body_class' ) );
		$this->pre_init();
		do_action( 'iki_toolkit_init', $this );
		$this->post_init();


	}


	/**
	 * Get chosen share services
	 * @return array
	 */
	protected function get_chosen_share_services() {

		$r      = array();
		$chosen = $this->get_customizer_option( 'share_services_popup', false );
		if ( $chosen ) {
			$chosen = $chosen['default_share'];
		} else {
			$chosen = $this->default_chose_share_services;
		}
		$admin_data = iki_toolkit_admin_data();
		foreach ( $chosen as $service => $enabled ) {

			if ( $enabled == 1 ) {
				if ( isset( $admin_data['available_share_services'][ $service ] ) ) {
					$r[ $service ] = $admin_data['available_share_services'][ $service ];
				}
			}
		}

		return $r;

	}

	/**
	 * @return array
	 */
	public function get_share_services() {
		return $this->chosen_share_services;
	}

	/**
	 * Get plugin options
	 */
	public function setup_options() {

		if ( current_theme_supports( 'iki-toolkit-hero-section' ) ) {
			$use_hero_section = apply_filters( 'iki_toolkit_use_hero_section', false );
			if ( $use_hero_section ) {
				$this->hero_section = iki_toolkit_setup_hero_section_data();
			}
		}

		$this->chosen_share_services = $this->get_chosen_share_services();
	}

	/**
	 * Return hero section options by reference
	 * @return null
	 */
	public function &get_hero_section() {
		return $this->hero_section;
	}

	/**
	 * Setup body element classes
	 *
	 * @param $classes
	 *
	 * @return array
	 */
	public function body_class( $classes ) {

		$hero_data = $this->hero_section;
		if ( $hero_data ) {
			$classes[] = ( $hero_data['layout']['width_fixed'] ) ? 'iki-hs-fixed' : '';
			$classes[] = 'iki-hs-height-' . $hero_data['layout']['height'];
		}

		return $classes;
	}

	/**
	 * Enqueue scripts for customizer functionality
	 */
	public function iki_customizer_controls() {

		if ( defined( 'FW' ) ) {

			wp_enqueue_script( 'iki-toolkit-customizer-controls', plugin_dir_url( __FILE__ ) . '../js/admin/customizer-controls.min.js',
				array( 'jquery' ),
				false,
				true );

		}

	}

	/**
	 * Add script for customizer
	 */
	public function _action_iki_customizer_live_options_preview() {
		if ( defined( 'FW' ) && defined( 'IKI_TOOLKIT_ROOT' ) ) {

			wp_enqueue_script( 'iki-toolkit-customizer', plugin_dir_url( __FILE__ ) . '../js/admin/customizer.min.js',
				array(
					'jquery',
					'customize-preview'
				),
				false,
				true );
		}
	}

	/**
	 * After setup theme
	 */
	public function init_options_helper() {

		if ( class_exists( '_Fw' ) ) {
			$this->set_options_helper( new Iki_Toolkit_Unyson_Options_Helper() );
		} else {
			$this->set_options_helper( new Iki_Toolkit_Options_Helper( '' ) );
		}
	}

	/**
	 * Setup appropriate options helper depending if unyson plugin is present
	 *
	 * @param object $option_helper
	 */
	public function set_options_helper( $option_helper ) {
		$this->option_helper = $option_helper;
	}

	/**
	 * Get options helper
	 * @return Iki_IOptions_Helper
	 */
	public function get_options_helper() {
		return $this->option_helper;
	}

	/**
	 * Wrapper function to get theme option created by unyson
	 *
	 * @param null|string $optionName option to get
	 * @param null|mixed $defaultValue what to return if there is no value in database
	 * @param null $getOriginalValue get the value from the options file
	 *
	 * @return mixed
	 */
	public function get_theme_option( $optionName = null, $defaultValue = null, $getOriginalValue = null ) {

		return $this->option_helper->get_option( $optionName, $defaultValue, $getOriginalValue );
	}

	/**
	 * Get option from customizer - created by unyson
	 *
	 * @param null|string $optionName option to get
	 * @param null|mixed $defaultValue value to return if there is no database value
	 *
	 * @return mixed
	 */
	public function get_customizer_option( $optionName = null, $defaultValue = null ) {

		return $this->option_helper->get_customizer_option( $optionName, $defaultValue );

	}

	/**
	 * Get term option created by unyson
	 *
	 * @param $term_id
	 * @param $taxonomy
	 * @param null $option_id
	 * @param null $default_value
	 * @param null $get_original_value
	 *
	 * @return mixed
	 */
	public function get_term_option( $term_id, $taxonomy, $option_id = null, $default_value = null, $get_original_value = null ) {
		return $this->option_helper->get_term_option( $term_id, $taxonomy, $option_id, $default_value, $get_original_value );
	}

	/**
	 * Get post option created by unyson
	 *
	 * @param null $post_id
	 * @param null $option_id
	 * @param null $default_value
	 * @param null $get_original_value
	 *
	 * @return mixed
	 */
	public function get_post_option( $post_id = null, $option_id = null, $default_value = null, $get_original_value = null ) {
		return $this->option_helper->get_post_option( $post_id, $option_id, $default_value, $get_original_value );
	}

	/**
	 * Enqueue appropriate front end scripts
	 */
	public function enqueue_public_scripts() {

		$file_url = plugin_dir_url( __FILE__ );
		global $post;

		//javascript for external-service template
		if ( 'page-external.php' == get_page_template_slug() && current_theme_supports( 'iki-toolkit-external-services' ) ) {
			wp_enqueue_script( 'iki-toolkit-external-app-js', $file_url . '../js/external-app.min.js', array(
				'jquery'
			), null, true );
		}

		//enqueue video background script only when it's actually used.
		if ( $this->hero_section && isset( $this->hero_section['video_background'] ) && ! wp_is_mobile() ) {
			wp_enqueue_script( 'iki-yt-background', $file_url . '../js/jquery.mb.YTPlayer.min.js', array( 'jquery' ), false, true );
		}

		wp_enqueue_script( 'iki-toolkit-main', $file_url . '../js/main.min.js',
			array( 'jquery' ),
			false,
			true );


		wp_enqueue_script( 'iki-toolkit-vendor', $file_url . '../js/vendor.min.js',
			array( 'jquery', 'iki-toolkit-main' ),
			false,
			true );

		wp_enqueue_style( 'iki-toolkit-main', $file_url . '../css/public/main.min.css' );

		if ( is_rtl() ) {
			wp_enqueue_style( 'iki-toolkit-main-rtl', $file_url . '../css/public/main-rtl.min.css',
				array( 'iki-toolkit-main' ) );
		}

		$GLOBALS['iki_toolkit']['exports']['adminUrl']  = admin_url( 'admin-ajax.php' );
		$GLOBALS['iki_toolkit']['exports']['iki_nonce'] = wp_create_nonce( 'iki_nonce' );
		$GLOBALS['iki_toolkit']['exports']['post_id']   = isset( $post ) ? $post->ID : false;

		$r = apply_filters( 'iki_toolkit_exports', $GLOBALS['iki_toolkit']['exports'] );
		wp_localize_script( 'iki-toolkit-main', 'ikiToolkitExports', $r );


		$customCss = apply_filters( 'iki_toolkit_print_inline_css', array() );
		wp_add_inline_style( 'iki-toolkit-main', join( '  ', $customCss ) );
	}

	/**
	 * Late enqueue public scripts
	 * We are enqueueing late becase we need to determine if some theme/plugin scripts are enqueued
	 */
	public function late_enqueue_public_scripts() {

		//always include grid css because of the shortcodes
		$file_url = plugin_dir_url( __FILE__ );
		//grid css

		$grid_deps     = array();
		$rtl_grid_deps = array();

		if ( wp_style_is( 'iki-main' ) ) {
			$grid_deps[]     = 'iki-main';
			$rtl_grid_deps[] = 'iki-main';
		}
		if ( wp_style_is( 'iki-main-color' ) ) {
			$grid_deps[]     = 'iki-main-color';
			$rtl_grid_deps[] = 'iki-main-color';
		}
		wp_enqueue_style( 'iki-grid', $file_url . '../css/public/wonder-grid.min.css', $grid_deps );

		if ( is_rtl() ) {
			//grid css
			wp_enqueue_style( 'iki-grid-rtl', $file_url . '../css/public/wonder-grid-rtl.min.css', $rtl_grid_deps );
		}
	}

	/**
	 * Enqueue admin css
	 */
	public function enqueue_admin_css() {
		if ( ! is_customize_preview() ) {
			wp_enqueue_style( 'admin-iki-toolkit', plugin_dir_url( __FILE__ ) . '../css/admin/admin-iki-toolkit.min.css' );
		}
	}

	/**
	 * Pre init hook
	 */
	protected function pre_init() {
		do_action( 'iki_toolkit_pre_init', $this );

	}


	/**
	 * Post init hook
	 */
	protected function post_init() {
		do_action( 'iki_toolkit_post_init', $this );

	}

	/**
	 * Enqueue admin javascript
	 */
	public function enqueue_admin_only_javascript() {

		global $post;
		$post_id = ( $post ) ? $post->ID : false;

		wp_enqueue_script( 'iki-admin-settings', IKI_TOOLKIT_ROOT_URL . 'js/admin/admin-settings.min.js',
			array( 'jquery' ),
			false,
			true );

		$admin_exports = array(
			'translations' => array(),
			'wpNonce'      => wp_create_nonce( 'iki-admin-nonce-check' ),
			'post'         => array(
				'id' => $post_id
			)

		);

		$admin_exports = apply_filters( 'iki_toolkit_exports', $admin_exports );
		wp_localize_script( 'iki-admin-settings', 'ikiToolkitExports', $admin_exports );

	}


	/**
	 * Create an array that holds all the location data that is used all throughout the theme.
	 *  there is  "location" (blog,archive,post etc..)
	 *  "type" - to be used in combination with "location" e.g location=archive and type=category
	 *  or location=post and type=iki_portfolio
	 */
	public function determine_active_location() {

		$this->location_info = iki_toolkit_get_active_location();
	}


	/**
	 * Getu currently active template
	 * @return mixed
	 */
	public function get_active_template() {
		return $this->page_template;
	}

	/**
	 * Get currently active location
	 * @return mixed
	 */
	public function get_location_info() {
		return $this->location_info;
	}


	/**
	 * Force set location info
	 *
	 * @param $data
	 */
	public function force_set_location_info(
		$data
	) {
		// ajax fix
		$data['id']   = ( 'false' == $data['id'] ) ? false : $data['id'];
		$data['type'] = ( 'false' == $data['type'] ) ? false : $data['type'];

		$this->location_info = $data;
	}

	/** Store template chosen by wordpress
	 *
	 * @param $template
	 *
	 * @return string Template name
	 *
	 */
	public function template_include(
		$template
	) {

		$template_filename = wp_basename( $template );

		$this->page_template = $template_filename;

		return $template;
	}

	/** Set featured posts data
	 *
	 * @param array $data Data to be used for featured posts
	 */
	public function set_featured_posts_data( $data ) {
		$this->featured_posts = $data;
	}

	/** Get featured posts data
	 * @return array|null featured posts data
	 */
	public function get_featured_posts_data() {
		return $this->featured_posts;
	}

	/**
	 * Set default plugin options, if we have theme default options , replace them
	 */
	public function set_default_options() {
		$plugin_data = iki_toolkit_admin_data();
		if ( isset( $GLOBALS['iki_admin_data'] ) ) {
			$plugin_data = array_replace_recursive( $plugin_data, $GLOBALS['iki_admin_data'] );
		}
		$GLOBALS['iki_toolkit_admin'] = $plugin_data;
	}

}


