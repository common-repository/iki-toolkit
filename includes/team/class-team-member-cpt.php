<?php

/**
 * Class for registering "iki_team_member" custom post type
 */
class Iki_Team_Member_CPT {


	/**
	 * Iki_Team_Member_CPT constructor.
	 *
	 */
	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'init_cpt' ), 20 );
	}


	public function init_cpt() {

		if ( get_theme_support( 'iki-toolkit-team-member-cpt' ) ) {

			$this->iki_register_cpt();
			$this->iki_create_cpt_taxonomies();
			$this->add_post_excerp_box();

		}
	}

	/***********************************************************************************************/
	/* Create custom team member type  */
	/***********************************************************************************************/

	/**
	 * Register CPT
	 */
	public function iki_register_cpt() {
		$labels = array(
			'name'               => __( 'Team Member', 'iki-toolkit' ),
			'singular_name'      => __( 'Team Member', 'iki-toolkit' ),
			'add_new'            => __( 'Add New', 'iki-toolkit' ),
			'add_new_item'       => __( 'Add New', 'iki-toolkit' ),
			'edit_item'          => __( 'Edit Team Member', 'iki-toolkit' ),
			'new_item'           => __( 'New Team Member', 'iki-toolkit' ),
			'all_items'          => __( 'All Team Members', 'iki-toolkit' ),
			'view_item'          => __( 'View Team Member', 'iki-toolkit' ),
			'search_items'       => __( 'Search Team Members', 'iki-toolkit' ),
			'not_found'          => __( 'No team members found', 'iki-toolkit' ),
			'not_found_in_trash' => __( 'No team members found in trash', 'iki-toolkit' ),
			'menu_name'          => 'Team Members'
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => true,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'taxonomies'          => array( 'iki_team_member_cat', 'iki_team_member_tag' ),
			'rewrite'             => array( 'slug' => 'team-members' )
		);
		register_post_type( 'iki_team_member', $args );

	}
	/***********************************************************************************************/
	/* Create Taxonomy  */
	/***********************************************************************************************/


	/**
	 * Register taxonomies
	 */
	public function iki_create_cpt_taxonomies() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => __( 'Team Member Categories', 'iki-toolkit' ),
			'singular_name'     => __( 'Team Member Category', 'iki-toolkit' ),
			'search_items'      => __( 'Search Team Member Categories', 'iki-toolkit' ),
			'all_items'         => __( 'All Team Member Categories', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Team Member Category', 'iki-toolkit' ),
			'update_item'       => __( 'Update Team Member Category', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Team Member Category', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Team Member Category Category', 'iki-toolkit' ),
			'menu_name'         => __( 'Categories', 'iki-toolkit' ),
		);
		$args   = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'team' ),

		);

		register_taxonomy( 'iki_team_member_cat', array( 'iki_team_member' ), $args );

		$labels = array(
			'name'              => __( ' Tean Member Tag', 'iki-toolkit' ),
			'singular_name'     => __( 'Team Member Tags', 'iki-toolkit' ),
			'search_items'      => __( 'Search Tags', 'iki-toolkit' ),
			'all_items'         => __( 'All Tags', 'iki-toolkit' ),
			'parent_item'       => null, //__( 'Parent Genre','iki-toolkit' ),
			'parent_item_colon' => null, //__( 'Parent Genre:','iki-toolkit' ),
			'edit_item'         => __( 'Edit Team Member Tag', 'iki-toolkit' ),
			'update_item'       => __( 'Update Team Member Tag', 'iki-toolkit' ),
			'add_new_item'      => __( 'Add New Team Member Tag', 'iki-toolkit' ),
			'new_item_name'     => __( 'New Team Member Tag', 'iki-toolkit' ),
			'menu_name'         => __( 'Tags', 'iki-toolkit' ),
		);
		$args   = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'position' ),

		);

		register_taxonomy( 'iki_team_member_tag', array( 'iki_team_member' ), $args );
	}

	/**
	 * Add support for excerp metabox in admin
	 */
	public function add_post_excerp_box() {
		add_post_type_support( 'iki_team_member', 'excerpt' );
	}
}

new Iki_Team_Member_CPT();
new Iki_Custom_Taxonomy_Filter( 'iki_team_member', 'iki_team_member_cat', esc_html__( 'All Categories', 'iki-toolkit' ) );
