<?php

/**
 * Options for full screen panels
 * Premade options for full screen panels via unyson
 * Sanitisation of all the data in this class is done by Unyson framework itself.
 */
class Iki_Admin_Options_FS_Panels {

	public $colors;

	public $background_options;
	protected $index;

	/**
	 * Iki_Admin_Options_FS_Panels constructor.
	 *
	 * @param $index
	 * @param null $colors
	 * @param null $background_option
	 */
	public function __construct( $index = '', $colors = null, $background_option = null ) {

		$this->index  = $index;
		$this->colors = $colors;

		if ( ! $background_option ) {
			$this->background_options = Iki_Toolkit_Admin_Options::get_instance()->default_background_options();
		}
		add_action( 'customize_register', array( $this, '_action_customizer_live_fw_options' ) );

	}

	/** Options for the full screen panels
	 *
	 * @param $title string for the option
	 *
	 * @return array
	 */
	public function generate_fs_panel_options( $title ) {

		$index = $this->index;

		$r = array(
			"title"   => $title,
			'options' => array(
				"fs_panel_{$index}_enabled"              => array(
					'type'         => 'switch',
					'value'        => 'disabled',
					'label'        => __( 'Use full screen panel', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "fs_panel_{$index}_anim_in,
						fs_panel_{$index}_anim_out,
						fs_panel_{$index}_content_width,
						fs_panel_{$index}_content_align,
						fs_panel_{$index}_content_block_top,
						fs_panel_{$index}_content_block_bottom,
						fs_panel_{$index}_custom_element,
						fs_panel_{$index}_launch_btn,
						fs_panel_{$index}_btn_enabled,
						fs_panel_{$index}_btn_options
						",
						'data-iki-test'    => 'enabled',
						'data-iki-refresh' => 'alwaysRefresh',
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
				),
				"fs_panel_{$index}_anim_in"              => array(
					'type'    => 'select',
					'value'   => 'transition.slideUpIn',
					'label'   => __( 'Panel animation IN', 'iki-toolkit' ),
					'choices' => Iki_Toolkit_Velocity_Options::get_instance()->get_velocity_animations_in( false )
				),
				"fs_panel_{$index}_anim_out"             => array(
					'type'    => 'select',
					'value'   => 'transition.slideDownOut',
					'label'   => __( 'Panel animation OUT', 'iki-toolkit' ),
					'choices' => Iki_Toolkit_Velocity_Options::get_instance()->get_velocity_animations_out( false )
				),
				"fs_panel_{$index}_launch_btn"           => array(
					'type'  => 'html',
					'value' => '',
					'label' => false,
					'desc'  => false,
					'html'  => '<button value="iki-fs-panel-' . $index . '" type="button" class="button iki-fs-up-demo">' . __( 'Launch Panel', 'iki-toolkit' ) . '</button>'
				),
				"fs_panel_{$index}_btn_enabled"          => array(
					'type'         => 'switch',
					'value'        => 'disabled',
					'label'        => __( 'Show button in main menu', 'iki-toolkit' ),
					'desc'         => __( 'Add button to main menu to show the panel', 'iki-toolkit' ),
					'help'         => __( 'You can also launch this panel by creating regular menu button in "Appearance -> Menus" you just need to add a special class to the menu item you create. Please refer to theme documentation to see the names of the classes. Bonus: you can also add the same class to any HTML element on the page and it will launch the panel when clicked.', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "fs_panel_{$index}_btn_options",
						'data-iki-test'    => 'enabled',
						'data-iki-refresh' => 'alwaysRefresh',
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
				),
				"fs_panel_{$index}_btn_options"          => $this->button_options(),
				"fs_panel_{$index}_content_width"        => array(
					'type'    => 'radio',
					'value'   => 'middle',
					'label'   => __( 'Content width', 'iki-toolkit' ),
					'desc'    => __( 'Maximum width of the content inside the panel.', 'iki-toolkit' ),
					'choices' => array(
						'fixed' => __( 'Theme width', 'iki-toolkit' ),
						'full'  => __( 'Browser width', 'iki-toolkit' )
					)
				),
				"fs_panel_{$index}_content_align"        => array(
					'type'    => 'radio',
					'value'   => 'middle',
					'label'   => __( 'Content alignment', 'iki-toolkit' ),
					'desc'    => __( 'Vertical aligment of the content inside the panel.', 'iki-toolkit' ),
					'help'    => __( 'This effect will only be visible if the content height is noticably smaller than the height of the browser window.', 'iki-toolkit' ),
					'choices' => array(
						'middle' => __( 'Middle', 'iki-toolkit' ),
						'top'    => __( 'Top', 'iki-toolkit' )
					)
				),
				"fs_panel_{$index}_content_block_top"    => Iki_Toolkit_Admin_Options::get_instance()->get_content_block_top_option( null, true, 'fs_panel' ),
				"fs_panel_{$index}_content_block_bottom" => Iki_Toolkit_Admin_Options::get_instance()->get_content_block_bottom_option( null, true, 'fs_panel' ),
			)
		);

		if ( ! get_theme_support( 'iki-velocity-one' ) ) {
			//remove animation options if there is no velocity support
			unset( $r['options']["fs_panel_{$index}_anim_in"] );
			unset( $r['options']["fs_panel_{$index}_anim_out"] );
		}

		return $r;
	}

	/** Setup customizer transport
	 *
	 * @param $wp_customize WP_Customize_Manager
	 */
	public function _action_customizer_live_fw_options( $wp_customize ) {

		$options = $GLOBALS['iki_customizer_options'];

		if ( class_exists( 'Iki_Sass_Compiler', false ) && isset( $options ) ) {


			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_close_btn_color]' )->transport = 'postMessage';

			if ( get_theme_support( 'iki-velocity-one' ) ) {
				$wp_customize->get_setting( 'fw_options[fs_panel_' . $this->index . '_anim_in]' )->transport  = 'postMessage';
				$wp_customize->get_setting( 'fw_options[fs_panel_' . $this->index . '_anim_out]' )->transport = 'postMessage';
			}


			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_size_bg]' )->transport     = 'postMessage';
			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_position_bg]' )->transport = 'postMessage';
			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_repeat_bg]' )->transport   = 'postMessage';
			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_color_bg]' )->transport    = 'postMessage';

			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_close_btn_bg_color]' )->transport = 'postMessage';

			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_color]' )->transport            = 'postMessage';
			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_title_color]' )->transport      = 'postMessage';
			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_link_color]' )->transport       = 'postMessage';
			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_link_color_hover]' )->transport = 'postMessage';


			$wp_customize->get_setting( 'fw_options[sass_fs_panel_' . $this->index . '_overlay_bg_color]' )->transport = 'postMessage';


			$wp_customize->get_setting( 'fw_options[fs_panel_' . $this->index . '_content_width]' )->transport = 'postMessage';
			$wp_customize->get_setting( 'fw_options[fs_panel_' . $this->index . '_content_align]' )->transport = 'postMessage';

		}

	}


	/** Get custom search element options
	 * @return array
	 */
	public function get_custom_search_element() {


		return array(
			'fs_panel_search_el_focus' => array(
				'type'         => 'switch',
				'value'        => 'enabled',
				'label'        => __( 'Focus search field', 'iki-toolkit' ),
				'desc'         => __( 'When panel opens immediately focus the search field.', 'iki-toolkit' ),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'No', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Yes', 'iki-toolkit' ),
				)
			),
			'fs_panel_search_el_size'  => array(
				'type'    => 'select',
				'value'   => 'normal',
				'label'   => __( 'Element size', 'iki-toolkit' ),
				'choices' => array(
					'small'  => __( 'Small', 'iki-toolkit' ),
					'normal' => __( 'Normal', 'iki-toolkit' )
				)
			),
		);
	}

	/** Generate fs panel colors
	 *
	 * @param $title
	 *
	 * @return array
	 */
	public function generate_fs_panel_colors( $title ) {

		$index  = $this->index;
		$colors = $this->colors;

		return array(
			'title'   => $title,
			'options' => array(
				"fs_panel_{$index}_colors_enabled"          => array(
					'type'         => 'switch',
					'value'        => 'disabled',
					'label'        => __( 'Use custom colors', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "sass_fs_panel_{$index}_close_btn_color,
								sass_fs_panel_{$index}_close_btn_bg_color,
								sass_fs_panel_{$index}_color,
								sass_fs_panel_{$index}_title_color,
								sass_fs_panel_{$index}_link_color,
								sass_fs_panel_{$index}_link_color_hover,
								sass_fs_panel_{$index}_color_bg,
								sass_fs_panel_{$index}_size_bg,
								sass_fs_panel_{$index}_position_bg,
								sass_fs_panel_{$index}_repeat_bg,
								fs_panel_{$index}_url_bg,
								fs_panel_{$index}_blur_bg,
								sass_fs_panel_{$index}_overlay_bg_color,
								fs_panel_{$index}_color_launch_btn,
								fs_panel_{$index}_vc_colors",
						'data-iki-test'    => 'enabled',
						'data-iki-refresh' => 'alwaysRefresh',
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
				),
				"fs_panel_{$index}_color_launch_btn"        => array(
					'type'  => 'html',
					'value' => '',
					'label' => false,
					'desc'  => false,
					'html'  => '<button value="iki-fs-panel-' . $index . '" type="button" class="button iki-fs-up-demo">' . __( 'Launch Panel', 'iki-toolkit' ) . '</button>'
				),
				"sass_fs_panel_{$index}_color_bg"           => array(
					'type'  => 'rgba-color-picker',
					'value' => $colors['bg_color'],
					'label' => __( 'Background', 'iki-toolkit' ),
				),
				"sass_fs_panel_{$index}_color"              => array(
					'type'  => 'color-picker',
					'value' => $colors['color'],
					'label' => __( 'Text', 'iki-toolkit' ),
				),
				"sass_fs_panel_{$index}_link_color"         => array(
					'type'  => 'color-picker',
					'value' => $colors['color'],
					'label' => __( 'Link', 'iki-toolkit' ),
				),
				"sass_fs_panel_{$index}_title_color"        => array(
					'type'  => 'color-picker',
					'value' => $colors['title_color'],
					'label' => __( 'Title text', 'iki-toolkit' ),
				),
				"sass_fs_panel_{$index}_link_color_hover"   => array(
					'type'  => 'color-picker',
					'value' => $colors['color'],
					'label' => __( 'Link on hover', 'iki-toolkit' ),
				),
				"fs_panel_{$index}_url_bg"                  => $this->background_options['url'],
				"sass_fs_panel_{$index}_size_bg"            => $this->background_options['size'],
				"sass_fs_panel_{$index}_position_bg"        => $this->background_options['position'],
				"sass_fs_panel_{$index}_repeat_bg"          => $this->background_options['repeat'],
				"fs_panel_{$index}_blur_bg"                 => array(
					'type'         => 'switch',
					'value'        => 'disabled',
					'label'        => __( 'Blur background image', 'iki-toolkit' ),
					'desc'         => __( 'Implement blur effect on background image', 'iki-toolkit' ),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
				),
				"sass_fs_panel_{$index}_overlay_bg_color"   => array(
					'type'  => 'rgba-color-picker',
					'value' => $colors['overlay_bg_color'],
					'label' => __( 'Background overlay color', 'iki-toolkit' ),
					'desc'  => __( 'This color goes over the background image.', 'iki-toolkit' ),
				),
				"sass_fs_panel_{$index}_close_btn_color"    => array(
					'type'  => 'color-picker',
					'value' => $colors['close_btn_color'],
					'label' => __( 'Close button symbol', 'iki-toolkit' ),
				),
				"sass_fs_panel_{$index}_close_btn_bg_color" => array(
					'type'  => 'rgba-color-picker',
					'value' => $colors['close_btn_bg_color'],
					'label' => __( 'Close button background', 'iki-toolkit' ),
				),
			)
		);
	}

	/** Get panel button options
	 * @return array
	 */
	protected function button_options() {
		$r = array(
			'type'          => 'popup',
			'value'         => array(),
			'label'         => __( 'Setup menu button', 'iki-toolkit' ),
			'desc'          => false,
			'popup-title'   => __( 'Panel menu button options', 'iki-toolkit' ),
			'button'        => __( 'Open', 'iki-toolkit' ),
			'size'          => 'small', // small, medium, large
			'popup-options' => array(
				'text'          => array(
					'type'  => 'text',
					'value' => sprintf( __( 'Panel %1$s', 'iki-toolkit' ), $this->index ),
					'label' => __( 'Button text', 'iki-toolkit' ),
				),
				'tooltip_text'  => array(
					'type'  => 'text',
					'value' => __( 'Tooltip text', 'iki-toolkit' ),
					'label' => __( 'Tooltip text', 'iki-toolkit' ),
				),
				'icon_enabled'  => array(
					'type'         => 'switch',
					'value'        => 'disabled',
					'label'        => __( 'Add icon', 'iki-toolkit' ),
					'desc'         => __( 'Add icon to the menu button', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "icon,icon_size",
						'data-iki-test'    => 'enabled',
						'data-iki-refresh' => '',
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
				),
				'icon'          => array(
					'type'  => 'icon',
					'value' => 'fa-smile-o',
					'label' => __( 'Icon', 'iki-toolkit' ),
				),
				"icon_size"     => array(
					'type'    => 'select',
					'value'   => 's',
					'label'   => __( 'Icon size', 'iki-toolkit' ),
					'choices' => array(
						's'  => __( 'Small', 'iki-toolkit' ),
						'm'  => __( 'Medium', 'iki-toolkit' ),
						'l'  => __( 'Large', 'iki-toolkit' ),
						'xl' => __( 'Extra large (XL)', 'iki-toolkit' )
					)
				),
				"float"         => array(
					'type'    => 'select',
					'value'   => 'left',
					'label'   => __( 'Float position', 'iki-toolkit' ),
					'desc'    => __( 'Float the button to the left or right of the menu', 'iki-toolkit' ),
					'choices' => array(
						'left'  => __( 'Left', 'iki-toolkit' ),
						'right' => __( 'Right', 'iki-toolkit' )
					)
				),
				'stamp_enabled' => array(
					'type'         => 'switch',
					'value'        => 'disabled',
					'label'        => __( 'Add stamp', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "stamp_options",
						'data-iki-test'    => 'enabled',
						'data-iki-refresh' => '',
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
				),
				'stamp_options' => array(
					'type'    => 'group',
					'options' => array(
						'stamp_text'       => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp text', 'iki-toolkit' ),
						),
						'stamp_pos_top'    => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp position top', 'iki-toolkit' ),
							'desc'  => __( 'Please provide a value together with the unit (px,%). Leave empty for default.', 'iki-toolkit' )
						),
						'stamp_pos_left'   => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp position left', 'iki-toolkit' ),
							'desc'  => __( 'Please provide a value together with the unit (px,%). Leave empty for default.', 'iki-toolkit' )
						),
						'stamp_pos_bottom' => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp position bottom', 'iki-toolkit' ),
							'desc'  => __( 'Please provide a value together with the unit (px,%). Leave empty for default.', 'iki-toolkit' )
						),
						'stamp_pos_right'  => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp position right', 'iki-toolkit' ),
							'desc'  => __( 'Please provide a value together with the unit (px,%). Leave empty for default.', 'iki-toolkit' )
						),
						'stamp_rotation'   => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp rotation', 'iki-toolkit' ),
							'desc'  => __( 'Rotation of the stamp (optional) 0-360.Leave empty for default.', 'iki-toolkit' )
						),
						'stamp_width'      => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Stamp width', 'iki-toolkit' ),
							'desc'  => __( 'Please provide a value together with the unit (px,%). Leave empty for default.', 'iki-toolkit' )
						),
						'z_index'          => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Menu z index position', 'iki-toolkit' ),
							'help'  => __( 'Assign Z-index value (number - integer) to the button that has a stamp , so if the stamp is outside of the button , it won\'t be overlapped by some other top level button menu.', 'iki-toolkit' )
						),
						'stamp_animation'  => array(
							'type'    => 'select',
							'label'   => __( 'Stamp animation', 'iki-toolkit' ),
							'value'   => 'none',
							'choices' => array(
								'none'    => __( 'No animation', 'iki-toolkit' ),
								'pulse'   => __( 'Pulse', 'iki-toolkit' ),
								'pulse-2' => __( 'Pulse Alternative', 'iki-toolkit' ),
								'swing'   => __( 'Swing', 'iki-toolkit' ),
								'swing-2' => __( 'Swing Alternative', 'iki-toolkit' ),
							)
						),
					)
				)
			)
		);

		return $r;
	}
}
