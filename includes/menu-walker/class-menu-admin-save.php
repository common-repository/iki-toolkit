<?php

/**
 * Class for saving custom menu options
 */
class Iki_Menu_Admin_Save {

	function __construct() {

		// add custom menu fields to menu
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'iki_add_custom_nav_fields' ) );

		// save menu custom fields
		add_action( 'wp_update_nav_menu_item', array( $this, 'iki_update_custom_nav_fields' ), 10, 3 );

		// edit menu walker
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'iki_edit_walker' ), 10, 2 );


	}

	/**
	 * Add custom options to menu item
	 *
	 * @param $menu_item
	 *
	 * @return mixed
	 */
	function iki_add_custom_nav_fields( $menu_item ) {

		if ( get_theme_support( 'iki-toolkit-menu' ) ) {

			$menu_item->iki_icon_class           = get_post_meta( $menu_item->ID, '_menu_item_iki_icon_class', true );
			$menu_item->iki_menu_icon_size       = get_post_meta( $menu_item->ID, '_menu_item_iki_menu_icon_size', true );
			$menu_item->iki_has_new_stamp        = get_post_meta( $menu_item->ID, '_menu_item_iki_has_new_stamp', true );
			$menu_item->iki_stamp_text           = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_text', true );
			$menu_item->iki_stamp_pos_top        = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_pos_top', true );
			$menu_item->iki_stamp_pos_right      = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_pos_right', true );
			$menu_item->iki_stamp_pos_bottom     = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_pos_bottom', true );
			$menu_item->iki_stamp_pos_left       = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_pos_left', true );
			$menu_item->iki_stamp_width          = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_width', true );
			$menu_item->iki_stamp_rotation       = get_post_meta( $menu_item->ID, '_menu_item_iki_stamp_rotation', true );
			$menu_item->iki_content_block        = get_post_meta( $menu_item->ID, '_menu_item_iki_content_block', true );
			$menu_item->iki_menu_z_index         = get_post_meta( $menu_item->ID, '_menu_item_iki_menu_z_index', true );
			$menu_item->iki_dropdown_arrow       = get_post_meta( $menu_item->ID, '_menu_item_iki_dropdown_arrow', true );
			$menu_item->iki_menu_float           = get_post_meta( $menu_item->ID, '_menu_item_iki_menu_float', true );
			$menu_item->iki_menu_stamp_animation = get_post_meta( $menu_item->ID, '_menu_item_iki_menu_stamp_animation', true );

			$menu_item->iki_menu_cb_width      = get_post_meta( $menu_item->ID, '_menu_item_iki_menu_cb_width', true );
			$menu_item->iki_menu_cb_aligment   = get_post_meta( $menu_item->ID, '_menu_item_iki_menu_cb_aligment', true );
			$menu_item->iki_horizontal_columns = get_post_meta( $menu_item->ID, '_menu_item_iki_horizontal_columns', true );
		}

		return $menu_item;
	}

	/**
	 * Save menu custom fields
	 *
	 * @access      public
	 * @since       1.0
	 * @return      void
	 */
	function iki_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {


		if ( ! current_theme_supports( 'iki-toolkit-menu' ) ) {
			return;
		}
		$custom_menu_fields = array(
			"iki_icon_class",
			"iki_menu_icon_size",
			"iki_has_new_stamp",
			"iki_stamp_text",
			"iki_stamp_pos_top",
			"iki_stamp_pos_right",
			"iki_stamp_width",
			"iki_stamp_pos_bottom",
			"iki_stamp_pos_left",
			"iki_stamp_rotation",
			"iki_content_block",
			"iki_menu_z_index",
			'iki_dropdown_arrow',
			"iki_menu_float",
			'iki_menu_stamp_animation',
			'iki_menu_cb_width',
			'iki_menu_cb_aligment',
			'iki_horizontal_columns'

		);

		foreach ( $custom_menu_fields as $key ) {
			if ( ! isset( $_POST[ 'menu-item-' . $key ][ $menu_item_db_id ] ) ) {
				$_POST[ 'menu-item-' . $key ][ $menu_item_db_id ] = "";
			}

			$value = $_POST[ 'menu-item-' . $key ][ $menu_item_db_id ];
			update_post_meta( $menu_item_db_id, '_menu_item_' . $key, sanitize_text_field( $value ) );
		}

	}


	/** Switch menu walker
	 *
	 * @param $walker
	 * @param $menu_id
	 *
	 * @return string
	 */
	function iki_edit_walker( $walker, $menu_id ) {


		if ( current_theme_supports( 'iki-toolkit-menu' ) ) {
			$walker = 'Iki_Walker_Menu_Admin';
		}

		return $walker;
	}


}

new Iki_Menu_Admin_Save();

