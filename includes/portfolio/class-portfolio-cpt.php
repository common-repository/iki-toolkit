<?php

/**
 * Class to handle registration of "iki_portfolio" custom post type.
 */
class Iki_Portfolio_CPT {

	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'init_cpt' ), 20 );
	}


	/**
	 * Initilize iki_portfolio CPT
	 */
	public function init_cpt() {

		if ( get_theme_support( 'iki-toolkit-portfolio-cpt' ) ) {

			$this->iki_register_cpt();
			$this->iki_create_cpt_taxonomies();
			$this->add_post_excerp_box();

		}
	}

	/***********************************************************************************************/
	/* Create portfolio CPT*/
	/***********************************************************************************************/

	/**
	 * Register CPT
	 */
	public function iki_register_cpt() {

		$cpt_labels = array(
			'name'               => __( 'Portfolio', 'iki-toolkit' ),
			'singular_name'      => __( 'Portfolio', 'iki-toolkit' ),
			'add_new'            => __( 'Add New', 'iki-toolkit' ),
			'add_new_item'       => __( 'Add New', 'iki-toolkit' ),
			'edit_item'          => __( 'Edit Portfolio', 'iki-toolkit' ),
			'new_item'           => __( 'New Portfolio Item', 'iki-toolkit' ),
			'all_items'          => __( 'All Portfolio items', 'iki-toolkit' ),
			'view_item'          => __( 'View item', 'iki-toolkit' ),
			'search_items'       => __( 'Search Portfolio Items', 'iki-toolkit' ),
			'not_found'          => __( 'No portfolio items found', 'iki-toolkit' ),
			'not_found_in_trash' => __( 'No portfolio items found in trash', 'iki-toolkit' ),
			'menu_name'          => 'Portfolio'
		);

		$cpt_args = array(
			'labels'              => $cpt_labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'hiearchical'         => true,
			'supports'            => array(
				'title',
				'editor',
				'thumbnail',
				'post-formats',
				'custom-fields'
			),
			'taxonomies'          => array( 'iki_portfolio_cat', 'iki_portfolio_tag' ),
			'rewrite'             => array( 'slug' => 'portfolio' )

		);
		register_post_type( 'iki_portfolio', $cpt_args );

	}
	/***********************************************************************************************/
	/* Create Portfolio Taxonomy  */
	/***********************************************************************************************/

	/**
	 * Register taxonomy
	 */
	public function iki_create_cpt_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$category_labels = array(
			'name'              => __( 'Portfolio Categories', 'iki-toolkit' ),
			'singular_name'     => __( 'Portfolio Category', 'iki-toolkit' ),
			'search_items'      => __( 'Search Portfolio Categories', 'iki-toolkit' ),
			'all_items'         => __( 'All Portfolio Categories', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Portfolio Category', 'iki-toolkit' ),
			'update_item'       => __( 'Update Portfolio Category', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Portfolio Category', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Portfolio Category', 'iki-toolkit' ),
			'menu_name'         => __( 'Categories', 'iki-toolkit' ),
		);
		$category_args   = array(
			'hierarchical'      => true,
			'labels'            => $category_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'portfolio-section' ),

		);

		register_taxonomy( 'iki_portfolio_cat', array( 'iki_portfolio' ), $category_args );

		$tag_labels = array(
			'name'              => __( 'Portfolio Tags', 'iki-toolkit' ),
			'singular_name'     => __( 'Portfolio Tag', 'iki-toolkit' ),
			'search_items'      => __( 'Search Tags', 'iki-toolkit' ),
			'all_items'         => __( 'All Tags', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Portfolio Tag', 'iki-toolkit' ),
			'update_item'       => __( 'Update Portfolio Tag', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Portfolio Tag', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Portfolio Tag', 'iki-toolkit' ),
			'menu_name'         => __( 'Tags', 'iki-toolkit' ),
		);
		$tag_args   = array(
			'hierarchical'      => false,
			'labels'            => $tag_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'portfolio-skills' ),

		);

		register_taxonomy( 'iki_portfolio_tag', array( 'iki_portfolio' ), $tag_args );

	}

	/**
	 * Add support for excerp metabox in admin
	 */
	public function add_post_excerp_box() {
		add_post_type_support( 'iki_portfolio', 'excerpt' );
	}
}

new Iki_Portfolio_CPT();
new Iki_Custom_Taxonomy_Filter( 'iki_portfolio', 'iki_portfolio_cat', esc_html__( 'All Categories', 'iki-toolkit' ) );
