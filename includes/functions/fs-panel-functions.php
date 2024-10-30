<?php

add_action( 'wp_footer', '_action_iki_toolkit_print_fs_panels', 100 );
add_action( 'get_header', '_action_iki_toolkit_get_fs_panel_options' );
add_filter( 'iki_custom_panel_element_data', '_filter_toolkit_iki_custom_element_data', 10, 2 );
add_action( 'iki_print_panel_custom_element', '_action_iki_toolkit_print_panel_custom_element', 10, 2 );
add_filter( 'wp_nav_menu_objects', '_filter_iki_toolkit_add_fs_panel_buttons', 10, 2 );


/**
 * Setup export of all required data for full screen panel options to be used with javascript
 */
function _action_iki_toolkit_get_fs_panel_options() {

	if ( ! current_theme_supports( 'iki-toolkit-fs-panels' ) ) {
		return;
	}

	$get_options = apply_filters( 'iki_toolkit_get_fs_panel_options', true );

	if ( $get_options ) {

		// data to be exported for javascript
		$export_data = array();

		// global panel data
		$panel_data = array();

		//small hack in order to include "search" panel in iteration.
		$panel_iteration = array(
			'1'      => '',
			'2'      => '',
			'search' => '',
		);

		foreach ( $panel_iteration as $key => $value ) {

			$panel_key_prefix = 'fs_panel_' . $key;

			$default_enabled = 'disabled';

			if ( 'enabled' == iki_toolkit()->get_customizer_option( $panel_key_prefix . '_enabled', $default_enabled ) ) {

				$custom_colors = iki_toolkit()->get_customizer_option( "{$panel_key_prefix}_colors_enabled", 'enabled' );
				$custom_colors = Iki_Toolkit_Utils::string_to_boolean( $custom_colors );
				$current_panel = array(
					'enabled'       => true,
					'name'          => 'iki-fs-panel-' . $key,
					'number'        => $key,
					'animation_in'  => iki_toolkit()->get_customizer_option( $panel_key_prefix . '_anim_in', 'transition.slideUpIn' ),
					'animation_out' => iki_toolkit()->get_customizer_option( $panel_key_prefix . '_anim_out', 'transition.slideDownOut' ),
					'width'         => iki_toolkit()->get_customizer_option( $panel_key_prefix . '_content_width', 'fixed' ),
					'align'         => iki_toolkit()->get_customizer_option( $panel_key_prefix . '_content_align', 'middle' ),
					'button'        => false,
					'blur_bg'       => Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_customizer_option( $panel_key_prefix . '_blur_bg', 'disabled' ) )
				);

				$bg_url = iki_toolkit()->get_customizer_option( $panel_key_prefix . '_url_bg', '' );

				if ( $bg_url && $custom_colors ) {

					$bg_url = $bg_url['url'];

				}

				$current_panel['background_image']['url'] = $bg_url;

				//setup menu buttons
				$menu_btn_enabled = iki_toolkit()->get_customizer_option( $panel_key_prefix . '_btn_enabled', 'disabled' );

				if ( 'enabled' == $menu_btn_enabled ) {

					$btn_defaults = array(
						'text'             => sprintf( __( 'Panel %1$s ', 'iki-toolkit' ), $key ),
						'tooltip_text'     => __( 'Tooltip text', 'iki-toolkit' ),
						'icon'             => '',
						'icon_size'        => 's',
						'float'            => 'none',
						'icon_enabled'     => 'disabled',
						'stamp_text'       => '',
						'stamp_enabled'    => 'disabled',
						'stamp_pos_top'    => '10px',
						'stamp_pos_bottom' => '',
						'stamp_pos_left'   => '10px',
						'stamp_pos_right'  => '',
						'stamp_rotation'   => '45',
						'stamp_width'      => '50px',
						'z_index'          => '3',
						'stamp_animation'  => 'none'
					);

					$btn_opts = iki_toolkit()->get_customizer_option( $panel_key_prefix . '_btn_options', array() );

					if ( 'search' == $key ) {

						$btn_opts['icon'] = 'fa-search';//hardcode search icon

						if ( empty( $btn_opts ) ) {
							//default search panel button options
							$btn_defaults['float']        = 'right';
							$btn_defaults['text']         = __( 'Search', 'iki-toolkit' );
							$btn_defaults['tooltip_text'] = __( 'Search the site', 'iki-toolkit' );
							$btn_opts['icon_enabled']     = 'enabled';
							$btn_opts['icon_size']        = 'l';

						}

					}

					$btn_opts             = wp_parse_args( $btn_opts, $btn_defaults );
					$current_panel['btn'] = $btn_opts;

				}

				$current_export = array(
					'animationIn'  => $current_panel['animation_in'],
					'animationOut' => $current_panel['animation_out'],
					'bgUrl'        => $current_panel['background_image']['url'],
					'blurBg'       => $current_panel['blur_bg'],
					'name'         => $current_panel['name']
				);

				$topBlock                   = iki_toolkit()->get_customizer_option( $panel_key_prefix . '_content_block_top', false );
				$current_panel['block_top'] = $topBlock;

				$bottomBlock                   = iki_toolkit()->get_customizer_option( $panel_key_prefix . '_content_block_bottom', false );
				$current_panel['block_bottom'] = $bottomBlock;


				$current_panel['button'] = array(

					'text' => trim( iki_toolkit()->get_customizer_option( $panel_key_prefix . '_btn_text', sprintf( _x( 'Panel %1$s', 'Full screen panel default default button text ', 'iki-toolkit' ), $key ) ) ),

					'titleText' => trim( iki_toolkit()->get_customizer_option( $panel_key_prefix . '_btn_title_text', '' ) ),
					'icon'      => iki_toolkit()->get_customizer_option( $panel_key_prefix . '_btn_icon', '' ),
					'iconSize'  => iki_toolkit()->get_customizer_option( $panel_key_prefix . '_btn_icon_size', 'iki-font-m' )
				);

				if ( 'search' == $key ) {

					//hard code "search" panel icon and text
					$current_panel['button']['icon'] = 'iki-icon-search';
					if ( empty( $current_panel['button']['text'] ) ) {
						$current_panel['button']['text'] = _x( 'Search', 'menu search button text', 'iki-toolkit' );
					}

					if ( empty( $current_panel['button']['titleText'] ) ) {
						$current_panel['button']['titleText'] = _x( 'Search the site', 'menu search button title attribute', 'iki-toolkit' );
					}

					//hard code search panel to have "search" element
					$current_panel['custom_element'] = array(
						'name'                    => 'search',
						'fs_panel_search_el_size' => iki_toolkit()->get_customizer_option( "{$panel_key_prefix}_el_size", 'normal' )
					);
					//hard code export options for "search" element
					$current_export['customElement'] = array(
						'name'         => 'search',
						'focus_search' => iki_toolkit()->get_customizer_option( "{$panel_key_prefix}_el_focus", 'enabled' )
					);
				}


				$panel_data[ $panel_key_prefix ] = $current_panel;
				$export_data[]                   = $current_export;
			}
		}

		if ( ! empty( $panel_data ) ) {
			//globals
			$GLOBALS['iki_toolkit']['data']['fs_panels'] = $panel_data;
		}
		if ( ! empty( $export_data ) ) {
			//export for javascript
			$GLOBALS['iki_toolkit']['exports']['fs_panels'] = $export_data;

		}
	}

}


if ( ! function_exists( '_action_iki_toolkit_print_panel_custom_element' ) ) {
	/**
	 * Print panel custom element
	 *
	 * @param $name
	 * @param $data
	 */
	function _action_iki_toolkit_print_panel_custom_element( $name, $data ) {

		if ( 'search' == $name ) {
			iki_toolkit_get_template( 'fs-panels/custom-blocks/search.php', $data );
		}
	}
}

if ( ! function_exists( '_filter_toolkit_iki_custom_element_data' ) ) {
	/**
	 * Setup full screen panel custom element data for javacript export
	 *
	 * @param $element_name
	 * @param $data
	 *
	 * @return mixed
	 */
	function _filter_toolkit_iki_custom_element_data( $element_name, $data ) {
		if ( 'search' == $element_name ) {
			$data['export']['focus_search'] = $data['data']['fs_panel_search_el_focus'];
		}

		return $data;
	}
}


/**
 * Print all available full screen panels
 */
function _action_iki_toolkit_print_fs_panels() {

	//fix for wpbakery page builder front end editing
	if ( isset( $_POST['action'] ) || ! current_theme_supports( 'iki-toolkit-fs-panels' ) ) {
		return;
	}

	//if we have full screen panels print them.
	if ( isset( $GLOBALS['iki_toolkit']['data']['fs_panels'] ) ) {
		foreach ( $GLOBALS['iki_toolkit']['data']['fs_panels'] as $panelData => $panel ) {

			iki_toolkit_get_template( 'fs-panels/full-screen-panel.php', $panel );

		}

	}
}


/**
 * Add aditional menu buttons for full screen panels
 *
 * @param array $items menu items
 * @param array $menu_args menu arguments
 *
 * @return array menu items
 */
function _filter_iki_toolkit_add_fs_panel_buttons( $items, $menu_args ) {

	//primary menu location
	if ( 'primary' == $menu_args->theme_location && current_theme_supports( 'iki-toolkit-fs-panels' ) ) {


		if ( isset( $GLOBALS['iki_toolkit']['data']['fs_panels'] ) ) {


			//process rest of the panels
			foreach ( $GLOBALS['iki_toolkit']['data']['fs_panels'] as $panelData => $panel ) {

				if ( 'fs_panel_search' == $panelData ) {
					//search panel is processed later - skip it
					continue;
				}

				$item = iki_toolkit_setup_panel_menu_button( $items, $panel );
				iki_toolkit_push_menu_item( $items, $item, $panel );

			}
			if ( isset( $GLOBALS['iki_toolkit']['data']['fs_panels']['fs_panel_search'] ) ) {

				//first process search panel (so it's always on the right (if floated)
				//search is always enabled
				$panel = $GLOBALS['iki_toolkit']['data']['fs_panels']['fs_panel_search'];
				$item  = iki_toolkit_setup_panel_menu_button( $items, $panel );
				iki_toolkit_push_menu_item( $items, $item, $panel );
			}

		}
	}

	return $items;
}

/**
 * Optionally push menu item
 *
 * @param array $items array of menu items
 * @param array $item menu item
 * @param float the button to the left or right of the menu
 */
function iki_toolkit_push_menu_item( &$items, $item, $panel ) {

	if ( $item && 'right' == $panel['btn']['float'] ) {
		array_unshift( $items, $item );
	} elseif ( $item ) {
		$items[] = $item;
	}
}

/**
 * Setup menu button for new panel
 *
 * @param array $items menu items
 * @param array $panel full screen panel data
 *
 * @return array|bool
 */
function iki_toolkit_setup_panel_menu_button( $items, $panel ) {

	if ( isset( $panel['btn'] ) ) {

		$menu_index              = count( $items );
		$panel['btn']['classes'] = array(
			$panel['name'] . '-open'
		);

		return iki_toolkit_create_menu_item( clone $items[1], $menu_index + 1, $panel['btn'] );
	}

	return false;
}

/**
 * Create/modify additional menu item
 *
 * @param array $item item to be modified
 * @param int $menu_order menu number
 * @param array $opts button options
 *
 * @return array new item
 */
function iki_toolkit_create_menu_item( $item, $menu_order, $opts ) {

	$id = rand( 1000000, 2000000 );

	$item->title                 = $opts['text'];
	$item->post_title            = $opts['text'];
	$item->iki_menu_float        = ( 'none' != $opts['float'] ) ? $opts['float'] : '';
	$item->iki_menu_icon_size    = ( 'enabled' == $opts['icon_enabled'] ) ? $opts['icon_size'] : '';
	$item->iki_icon_class        = ( 'enabled' == $opts['icon_enabled'] ) ? $opts['icon'] : '';
	$item->post_name             = 'custom_item_' . $menu_order;
	$item->menu_order            = $menu_order;
	$item->current_item_ancestor = false;
	$item->current_item_parent   = false;
	$item->url                   = "#";
	$item->attr_title            = $opts['tooltip_text'];
	$item->classes[]             = Iki_Toolkit_Utils::sanitize_html_class_array( $opts['classes'] );
	$item->iki_content_block     = false;
	$item->iki_dropdown_arrow    = "";
	$item->post_parent           = '';
	$item->description           = '';

	$item->guid      = $id;
	$item->db_id     = $id;
	$item->ID        = $id;
	$item->object_id = $id;

	$rm_cl = array_search( 'current-menu-ancestor', $item->classes );
	if ( $rm_cl !== false ) {
		unset( $item->classes[ $rm_cl ] );
	}

	$rm_cl = array_search( 'menu-item-has-children', $item->classes );
	if ( $rm_cl !== false ) {
		unset( $item->classes[ $rm_cl ] );
	}

	$rm_cl = array_search( 'current-menu-item', $item->classes );
	if ( $rm_cl !== false ) {
		unset( $item->classes[ $rm_cl ] );
	}

	$rm_cl = array_search( 'current_page_item', $item->classes );
	if ( $rm_cl !== false ) {
		unset( $item->classes[ $rm_cl ] );
	}

	//setup stamp
	$item->iki_has_new_stamp        = ( 'enabled' == $opts['stamp_enabled'] ) ? 'true' : '';
	$item->iki_stamp_pos_top        = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_pos_top'] : '';
	$item->iki_stamp_pos_bottom     = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_pos_bottom'] : '';
	$item->iki_stamp_pos_left       = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_pos_left'] : '';
	$item->iki_stamp_pos_right      = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_pos_right'] : '';
	$item->iki_stamp_rotation       = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_rotation'] : '';
	$item->iki_stamp_width          = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_width'] : '';
	$item->iki_menu_z_index         = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['z_index'] : '';
	$item->iki_stamp_text           = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_text'] : '';
	$item->iki_menu_stamp_animation = ( 'enabled' == $opts['stamp_enabled'] ) ? $opts['stamp_animation'] : 'none';

	return $item;
}

