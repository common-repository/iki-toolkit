<?php

/**
 * Registers wonder grid CPT
 */
class Iki_Wonder_Grid_CPT {

	/**
	 * Initialize the class
	 */
	public static function init() {

		add_action( 'after_setup_theme', array( 'Iki_Wonder_Grid_CPT', 'iki_register_cpt' ), 20 );
	}

	/**
	 * Register custom post type
	 */
	public static function iki_register_cpt() {
		$labels = array(
			'name'               => __( 'Wonder Grid', 'iki-toolkit' ),
			'singular_name'      => __( 'Wonder Grid', 'iki-toolkit' ),
			'add_new'            => __( 'Add new', 'iki-toolkit' ),
			'add_new_item'       => __( 'Add new', 'iki-toolkit' ),
			'edit_item'          => __( 'Edit grid', 'iki-toolkit' ),
			'new_item'           => __( 'New grid', 'iki-toolkit' ),
			'all_items'          => __( 'All grids', 'iki-toolkit' ),
			'view_item'          => __( 'View grid', 'iki-toolkit' ),
			'search_items'       => __( 'Search grids', 'iki-toolkit' ),
			'not_found'          => __( 'No grids found', 'iki-toolkit' ),
			'not_found_in_trash' => __( 'No grids found in trash', 'iki-toolkit' ),
			'menu_name'          => 'Wonder Grid'
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'show_ui'             => true,
			'supports'            => array( 'title' ),
			'rewrite'             => false,
		);
		register_post_type( 'iki_wonder_grid', $args );

	}
}

Iki_Wonder_Grid_CPT::init();