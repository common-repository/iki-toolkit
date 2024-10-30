<?php

/**
 * Class for creating filter dropdown for custom post type taxonomies in admin
 */

class Iki_Custom_Taxonomy_Filter {

	protected $taxonomy;
	protected $post_type;
	protected $show_all_text;

	/**
	 * Iki_Custom_Taxonomy_Filter constructor.
	 *
	 * @param $post_type string Custom post type
	 * @param $taxonomy string Custom post type taxonomy
	 * @param $show_all_text string Text to show as default "show all terms"
	 */
	public function __construct( $post_type, $taxonomy, $show_all_text ) {
		$this->taxonomy      = $taxonomy;
		$this->post_type     = $post_type;
		$this->show_all_text = $show_all_text;


		add_action( 'restrict_manage_posts', array( $this, 'filter_taxonomy' ), 10, 2 );
	}

	/**
	 * Generate HTML structure for the dropdown filter
	 *
	 * @param $post_type
	 */
	public function filter_taxonomy( $post_type ) {

		// Apply this only on a specific post type
		if ( $this->post_type !== $post_type ) {
			return;
		}
		// an array of all the taxonomyies you want to display. Use the taxonomy name or slug
		$taxonomies = array( $this->taxonomy );

		// must set this to the post type you want the filter(s) displayed on
		foreach ( $taxonomies as $tax_slug ) {
			$terms = get_terms( $tax_slug );
			if ( count( $terms ) > 0 ) {
				echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
				echo "<option value=''>" . $this->show_all_text . "</option>";
				foreach ( $terms as $term ) {
					echo '<option value=' . $term->slug, isset( $_GET[ $tax_slug ] ) && $_GET[ $tax_slug ] == $term->slug ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
				}
				echo "</select>";
			}
		}
	}
}