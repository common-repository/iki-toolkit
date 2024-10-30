<?php

/**
 * Class for creating grid options
 */
class Iki_Toolkit_Admin_Grid_Options {


	private static $class = null;

	protected $wonder_grids = null;
	protected $wonder_grid_dropdown;
	protected $blog_grid_templates = array();

	protected $wonder_grid_query = array(
		'post_type'        => 'iki_wonder_grid',
		'suppress_filters' => 0,
		'numberposts'      => - 1,
		'orderby'          => array(
			'date' => 'DESC'
		)
	);

	/** Get class instance
	 * @return Iki_Toolkit_Admin_Grid_Options
	 */

	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}


	/** Get all created wonder grid posts
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_wonder_grid_post_option( $additional_data = null ) {

		$grids = self::get_instance()->get_wonder_grid_posts();
		$r     = array(
			'type'    => 'select',
			'value'   => 'portrait_4',
			'label'   => __( 'Grid', 'iki-toolkit' ),
			'help'    => __( 'Please note that premade "[blog]" grids have special design. "Design" dropdown option below has no affect when any "[blog]" grid is used.', 'iki-toolkit' ),
			'choices' => $grids
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}


	/** Query all wondergrid posts
	 * @return array wondergrid posts
	 */
	public function get_wonder_grid_posts() {

		if ( is_null( $this->wonder_grids ) ) {
			//get them for the first time
			$this->wonder_grid_dropdown = array();
			$this->wonder_grids         = get_posts( $this->wonder_grid_query );
			if ( $this->wonder_grids ) {
				foreach ( $this->wonder_grids as $grid ) {
					$grid->post_title                        = trim( $grid->post_title );
					$title                                   = ( empty( $grid->post_title ) ) ? _( '( no title )', 'iki-toolkit' ) : $grid->post_title;
					$this->wonder_grid_dropdown[ $grid->ID ] = $title;
				}
			}

			$this->wonder_grid_dropdown = $this->wonder_grid_dropdown + $this->get_premade_grids();
		}

		return $this->wonder_grid_dropdown;
	}


	/** Create and return premade grids
	 * @return array premade grids for the options
	 */
	public function get_premade_grids() {
		$r = array(
			'square_2' => __( '[ Square - 2 ]', 'iki-toolkit' ),
			'square_3' => __( '[ Square - 3 ]', 'iki-toolkit' ),
			'square_4' => __( '[ Square - 4 ]', 'iki-toolkit' ),

			'square_condensed_2' => __( '[ Square condensed - 2 ]', 'iki-toolkit' ),
			'square_condensed_3' => __( '[ Square condensed - 3 ]', 'iki-toolkit' ),
			'square_condensed_4' => __( '[ Square condensed - 4 ]', 'iki-toolkit' ),

			'portrait_2' => __( '[ Portrait - 2 ]', 'iki-toolkit' ),
			'portrait_3' => __( '[ Portrait - 3 ]', 'iki-toolkit' ),
			'portrait_4' => __( '[ Portrait - 4 ]', 'iki-toolkit' ),

			'portrait_condensed_2' => __( '[ Portrait condensed - 2 ]', 'iki-toolkit' ),
			'portrait_condensed_3' => __( '[ Portrait condensed - 3 ]', 'iki-toolkit' ),
			'portrait_condensed_4' => __( '[ Portrait condensed - 4 ]', 'iki-toolkit' ),

			'landscape_1' => __( '[ Landscape - 1 ]', 'iki-toolkit' ),
			'landscape_2' => __( '[ Landscape - 2 ]', 'iki-toolkit' ),
			'landscape_3' => __( '[ Landscape - 3 ]', 'iki-toolkit' ),
			'landscape_4' => __( '[ Landscape - 4 ]', 'iki-toolkit' ),

			'landscape_condensed_1' => __( '[ Landscape Condensed - 1 ]', 'iki-toolkit' ),
			'landscape_condensed_2' => __( '[ Landscape Condensed - 2 ]', 'iki-toolkit' ),
			'landscape_condensed_3' => __( '[ Landscape Condensed - 3 ]', 'iki-toolkit' ),
			'landscape_condensed_4' => __( '[ Landscape Condensed - 4 ]', 'iki-toolkit' ),

			'iki_b-mixed'       => __( '[ Blog mixed ]', 'iki-toolkit' ),
			'iki_b-mixed-2'     => __( '[ Blog mixed v2 ]', 'iki-toolkit' ),
			'iki_b-mixed-5'     => __( '[ Blog mixed v3 ]', 'iki-toolkit' ),
			'iki_b-mixed-3'     => __( '[ Blog mixed and portrait ]', 'iki-toolkit' ),
			'iki_b-mixed-4'     => __( '[ Blog mixed and landscape ]', 'iki-toolkit' ),
			'iki_b-port'        => __( '[ Blog portrait ]', 'iki-toolkit' ),
			'iki_b-port-land'   => __( '[ Blog portrait and landscape ]', 'iki-toolkit' ),
			'iki_b-port-land-2' => __( '[ Blog portrait and landscape v2 ]', 'iki-toolkit' ),
			'iki_b-land'        => __( '[ Blog landscape zig zag ]', 'iki-toolkit' ),
			'iki_b-land-2'      => __( '[ Blog landscape no zigzag ]', 'iki-toolkit' )//no zig zag.

		);

		$r = apply_filters( 'iki_toolkit_admin_options_premade_grids', $r );

		return $r;
	}

	/** Get blog grid design
	 * @return array
	 */
	public function get_blog_design() {

		return array(
			'default'    => __( 'Default', 'iki-toolkit' ),
			'default-v2' => __( 'With border', 'iki-toolkit' ),
			'default-v3' => __( 'Rounded corners', 'iki-toolkit' ),
		);

	}

	/** Get team design
	 * @return array
	 */
	public function get_team_design() {
		return array(
			'team'    => __( 'Default', 'iki-toolkit' ),
			'team-v2' => __( 'With border', 'iki-toolkit' ),
			'team-v3' => __( 'Rounded corners', 'iki-toolkit' )
		);
	}

	/** Get portfolio design
	 * @return array
	 */
	public function get_portfolio_design() {

		return array(
			'port'    => __( 'Default', 'iki-toolkit' ),
			'port-v2' => __( 'With border', 'iki-toolkit' ),
			'port-v3' => __( 'Rounded corners', 'iki-toolkit' )
		);
	}

	/** Get portfolio project design
	 * @return array
	 */
	public function get_portfolio_project_design() {

		return array(
			'port'    => __( 'Default', 'iki-toolkit' ),
			'port-v2' => __( 'With border', 'iki-toolkit' ),
			'port-v3' => __( 'Rounded corners', 'iki-toolkit' )
		);
	}
}