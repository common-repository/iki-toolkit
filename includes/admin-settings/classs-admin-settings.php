<?php

/** Handles the creation of admin settings page and sections for this plugin*/
class Iki_Admin_Settings {

	protected $sections;

	public $section_name;

	public function __construct() {

		add_action( 'admin_menu', array( $this, 'setup_admin_menu' ) );
//		add_action( 'admin_enqueue_scripts', array( $this, 'add_settings_scripts' ) );
	}

	/**
	 * Enqueue scripts
	 */
	public function add_settings_scripts() {
//		wp_enqueue_style( 'iki-admin-settings', IKI_TOOLKIT_ROOT_URL . 'css/admin/admin-settings.min.css' );

	}

	/**
	 * Setup admin menu
	 */
	public function setup_admin_menu() {

		add_options_page(
			'Iki Toolkit Settings',// The title to be displayed in the browser window for this page.
			'Iki Toolkit',// The text to be displayed for this menu item
			'administrator',// Which type of users can see this menu item
			'iki_toolkit_options',// The unique ID - that is, the slug - for this menu item
			array( $this, 'display_settings' )
		);

		$this->sections = apply_filters( 'iki_toolkit_settings_sections', array() );
	}

	/** Create navigation tabs for settings page
	 *
	 * @param $active_tab
	 *
	 * @return string
	 */
	protected function create_navigation_tabs( $active_tab ) {

		$r = '<h2 class="nav-tab-wrapper">';
		/**@var Iki_Admin_Options_Section $section */
		foreach ( $this->sections as $section ) {

			$is_active = ( $section->name == $active_tab ) ? 'nav-tab-active' : '';
			$r         .= sprintf( '<a href="?page=iki_toolkit_options&tab=%1$s" class="nav-tab %2$s">%3$s</a>',
				$section->name, $is_active, $section->title );
		}

		$r .= '</h2>';

		return $r;
	}

	/** Create forms for currently active tab (section)
	 *
	 * @param $active_tab
	 */
	protected function create_options_form( $active_tab ) {

		$nonce = wp_create_nonce( 'iki-admin-nonce-check' );

		printf( '<div id="iki-ajax-nonce" class="iki-ajax-nonce hidden" data-iki-nonce="%1$s"></div>', $nonce );

		/**@var Iki_Admin_Options_Section $section */
		$active_section = $this->sections[0];

		if ( ! empty( $active_tab ) ) {

			foreach ( $this->sections as $section ) {
				if ( $section->name == $active_tab ) {
					$active_section = $section;
				}
			}

		}

		printf( '<form method="post" class="%1$s" action="options.php">', $active_section->wrap_class );

		$active_section->settings_fields();
		$active_section->do_settings_sections();

		submit_button();

		echo '</form>';

	}


	/**
	 * Create wrapper for plugin settings
	 */
	public function display_settings() {
		?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php _e( 'Iki Toolkit options', 'iki-toolkit' ); ?></h2>

			<?php
			$active_tab = '';
			if ( isset( $_GET['tab'] ) ) {
				$active_tab = $_GET['tab'];
			}

			echo $this->create_navigation_tabs( $active_tab );
			$this->create_options_form( $active_tab );
			?>


		</div><!-- /.wrap -->
		<?php
	}
}
