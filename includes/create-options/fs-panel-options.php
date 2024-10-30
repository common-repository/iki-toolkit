<?php

add_filter( 'iki_customizer_options', '_filter_iki_toolkit_add_customizer_fs_panel_options', 10, 2 );
add_filter( 'iki_customizer_section_options', '_filter_iki_toolkit_customizer_colors', 10, 2 );
add_action( 'customize_register', '_action_iki_toolkit_customizer_live_options' );
add_filter( 'iki_toolkit_fs_panel_colors', 'iki_add_wp_bakery_colors_to_fs_panels', 10, 2 );


/** Add panel color options to customizer options data
 *
 * @param $data
 * @param $section
 *
 * @return mixed
 */
function _filter_iki_toolkit_customizer_colors( $data, $section ) {

	if ( 'colors' == $section && current_theme_supports( 'iki-toolkit-fs-panels' ) ) {

		$panel_colors    = $GLOBALS['iki_toolkit_admin']['colors']['fs_panel'];
		$custom_elements = $GLOBALS['iki_toolkit_admin']['colors']['custom_fs_elements'];

		$iki_fs_panel_1 = new Iki_Admin_Options_FS_Panels( 1, $panel_colors );
		$iki_fs_panel_1 = $iki_fs_panel_1->generate_fs_panel_colors( __( 'Full Screen Panel 1', 'iki-toolkit' ) );
		$iki_fs_panel_1 = apply_filters( 'iki_toolkit_fs_panel_colors', $iki_fs_panel_1, 1 );

		$iki_fs_panel_2 = new Iki_Admin_Options_FS_Panels( 2, $panel_colors );
		$iki_fs_panel_2 = $iki_fs_panel_2->generate_fs_panel_colors( __( 'Full Screen Panel 2', 'iki-toolkit' ) );
		$iki_fs_panel_2 = apply_filters( 'iki_toolkit_fs_panel_colors', $iki_fs_panel_2, 2 );

		$fs_panel_search = iki_cust_search_panel_color( $panel_colors, $custom_elements );
		$fs_panel_search = apply_filters( 'iki_toolkit_fs_panel_colors', $fs_panel_search, 'search' );

		$data['fs_panel_search_colors'] = $fs_panel_search;
		$data['fs_panel_1_colors']      = $iki_fs_panel_1;
		$data['fs_panel_2_colors']      = $iki_fs_panel_2;
	}

	return $data;
}

/**
 * Add additional options to iki-toolkit FS panels
 * We are adding options for creating colors for wp backery elements
 *
 * @param array $data panel options
 * @param string|int $panel_id panel id
 *
 * @return mixed
 */
function iki_add_wp_bakery_colors_to_fs_panels( $data, $panel_id ) {

	if ( class_exists( 'Vc_Manager' ) ) {
		$data['options']["fs_panel_{$panel_id}_vc_colors"] = Iki_Admin_Options::get_instance()->custom_vc_colors();
	}

	return $data;
}


/** Customizer full screen panel options
 * @return array
 */
function iki_cust_fs_panels_options() {


	$iki_fs_panel_1 = new Iki_Admin_Options_FS_Panels( 1 );
	$iki_fs_panel_1 = $iki_fs_panel_1->generate_fs_panel_options( __( 'Full Screen Panel 1', 'iki-toolkit' ) );
	$iki_fs_panel_1 = apply_filters( 'iki_toolkit_fs_panel_options', $iki_fs_panel_1, 1 );

	$iki_fs_panel_2 = new Iki_Admin_Options_FS_Panels( 2 );
	$iki_fs_panel_2 = $iki_fs_panel_2->generate_fs_panel_options( __( 'Full Screen Panel 2', 'iki-toolkit' ) );
	$iki_fs_panel_2 = apply_filters( 'iki_toolkit_fs_panel_options', $iki_fs_panel_2, 2 );

	/*==search-panel*/
	$fs_panel_search = iki_cust_search_panel_options();
	$fs_panel_search = apply_filters( 'iki_toolkit_fs_panel_options', $fs_panel_search, 'search' );

	return array(
		'panel_search' => $fs_panel_search,
		'panel_1'      => $iki_fs_panel_1,
		'panel_2'      => $iki_fs_panel_2
	);
}


/** Add panel options to customizer options
 *
 * @param $options
 * @param $admin_data
 *
 * @return mixed
 */
function _filter_iki_toolkit_add_customizer_fs_panel_options( $options, $admin_data ) {

	if ( current_theme_supports( 'iki-toolkit-fs-panels' ) ) {

		$fs_panels = iki_cust_fs_panels_options();

		$options['fs_panel_1']      = $fs_panels['panel_1'];
		$options['fs_panel_2']      = $fs_panels['panel_2'];
		$options['fs_panel_search'] = $fs_panels['panel_search'];

	}

	return $options;
}


/** Setup search panel options
 * @return array
 */
function iki_cust_search_panel_options() {

	/**
	 * Setup Full screen search panel
	 * Combine and shuffle some options from full screen panel options
	 */

	$iki_fs_panel_search = new Iki_Admin_Options_FS_Panels( 'search' );

	$iki_theme_search_opts = $iki_fs_panel_search->generate_fs_panel_options( __( 'Search panel', 'iki-toolkit' ) );

	//remove changing the icon for search panel
	unset( $iki_theme_search_opts['options']['fs_panel_search_btn_options']['popup-options']['icon'] );

	//icon is enabled by default
	$iki_theme_search_opts['options']['fs_panel_search_btn_options']['popup-options']['icon_enabled']['value'] = 'enabled';
	$iki_theme_search_opts['options']['fs_panel_search_btn_options']['popup-options']['icon_enabled']['label'] = __( 'Add search icon', 'iki-toolkit' );
	$iki_theme_search_opts['options']['fs_panel_search_btn_options']['popup-options']['icon_size']['value']    = 'l';

	$iki_fs_search_element_options = $iki_fs_panel_search->get_custom_search_element();

	$iki_theme_search_opts['options'] = array_merge( $iki_theme_search_opts['options'], $iki_fs_search_element_options );

	unset( $iki_theme_search_opts['options']['fs_panel_search_custom_element'] );

	$iki_theme_search_opts['options']['fs_panel_search_enabled']['attr']['data-iki-for'] = "fs_panel_search_anim_in,
						fs_panel_search_anim_out,
						fs_panel_search_content_width,
						fs_panel_search_content_align,
						fs_panel_search_content_block_top,
						fs_panel_search_content_block_bottom,
						fs_panel_search_form,
						fs_panel_search_el_focus,
						fs_panel_search_el_size,
						fs_panel_search_launch_btn,
						fs_panel_search_in_mobile,
						fs_panel_search_btn_options,
						fs_panel_search_btn_enabled";

	//search panel is enabled by default
	$iki_theme_search_opts['options']['fs_panel_search_enabled']['value'] = 'enabled';
	$iki_theme_search_opts['options']['fs_panel_search_btn_enabled']['value'] = 'enabled';


	if ( class_exists( 'WooCommerce' ) ) {
		$iki_theme_search_opts['options']['fs_panel_search_form'] = array(
			'type'    => 'select',
			'value'   => 'blog',
			'label'   => __( 'Search form', 'iki-toolkit' ),
			'desc'    => __( 'Choose between searching the blog or searching Woocommerce products', 'iki-toolkit' ),
			'choices' => array(
				'blog'        => __( 'Blog Search', 'iki-toolkit' ),
				'woocommerce' => __( 'Product Search', 'iki-toolkit' ),
			),
			'inline'  => false,
		);
	}

	return $iki_theme_search_opts;
}

/** Setup custom search panel options
 *
 * @param $panel_colors
 * @param $element_colors
 *
 * @return array
 */
function iki_cust_search_panel_color( $panel_colors, $element_colors ) {

	$iki_fs_panel_search = new Iki_Admin_Options_FS_Panels( 'search', $panel_colors );

	$iki_theme_search_opts = $iki_fs_panel_search->generate_fs_panel_colors( __( 'Search panel', 'iki-toolkit' ) );

	$colors = $element_colors['search'];

	$iki_fs_search_element_options = array(
		"fs_panel_search_fake"                 => array(
			'type'  => 'html',
			'value' => '',
			'label' => __( 'Search element colors', 'iki-toolkit' ),
			'desc'  => false,
			'html'  => ''
		),
		"sass_fs_panel_search_el_bg_color"     => array(
			'type'  => 'rgba-color-picker',
			'value' => $colors['fs_panel_search_bg_color'],
			'label' => __( 'Search Background color', 'iki-toolkit' ),
		),
		"sass_fs_panel_search_el_color"        => array(
			'type'  => 'color-picker',
			'value' => $colors['fs_panel_search_color'],
			'label' => __( 'Text color', 'iki-toolkit' ),
		),
		"sass_fs_panel_search_el_ph_color"     => array(
			'type'  => 'color-picker',
			'value' => $colors['fs_panel_search_ph_color'],
			'label' => __( 'Placeholder text color', 'iki-toolkit' ),
		),
		"sass_fs_panel_search_el_border_color" => array(
			'type'  => 'rgba-color-picker',
			'value' => $colors['fs_panel_search_border_bottom_color'],
			'label' => __( 'Border bottom color', 'iki-toolkit' ),
		)
	);


	$iki_theme_search_opts['options'] = array_merge_recursive( $iki_theme_search_opts['options'], $iki_fs_search_element_options );

	$iki_theme_search_opts['options']['fs_panel_search_colors_enabled'] = array(
		'type'         => 'switch',
		'value'        => 'disabled',
		'label'        => __( 'Use custom colors', 'iki-toolkit' ),
		'attr'         => array(
			'data-iki-switch'  => 1,
			'data-iki-for'     => "
							sass_fs_panel_search_close_btn_color,
							sass_fs_panel_search_close_btn_bg_color,
							sass_fs_panel_search_color,
							sass_fs_panel_search_link_color,
							sass_fs_panel_search_link_color_hover,
							sass_fs_panel_search_color_bg,
							fs_panel_search_url_bg,
							sass_fs_panel_search_size_bg,
							sass_fs_panel_search_position_bg,
							sass_fs_panel_search_repeat_bg,
							fs_panel_search_blur_bg,
							sass_fs_panel_search_el_color,
							sass_fs_panel_search_el_ph_color,
							sass_fs_panel_search_el_bg_color,
							sass_fs_panel_search_overlay_bg_color,
							sass_fs_panel_search_el_border_color,
							fs_panel_search_fake,
							sass_fs_panel_search_title_color,
							fs_panel_search_color_launch_btn,
							fs_panel_search_vc_colors",
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
		)
	);

	return $iki_theme_search_opts;
}

/** Setup postMessage transport for certain panel options
 * @return array
 */

function _action_iki_toolkit_customizer_live_options( $wp_customize ) {

	if ( defined( 'FW' ) && current_theme_supports( 'iki-toolkit-fs-panels' ) ) {

		$wp_customize->get_setting( 'fw_options[fs_panel_search_el_focus]' )->transport             = 'postMessage';
		$wp_customize->get_setting( 'fw_options[fs_panel_search_el_size]' )->transport              = 'postMessage';
		$wp_customize->get_setting( 'fw_options[sass_fs_panel_search_el_bg_color]' )->transport     = 'postMessage';
		$wp_customize->get_setting( 'fw_options[sass_fs_panel_search_el_border_color]' )->transport = 'postMessage';
		$wp_customize->get_setting( 'fw_options[sass_fs_panel_search_el_color]' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'fw_options[sass_fs_panel_search_el_ph_color]' )->transport     = 'postMessage';

	}
}

