<?php

/**
 * Class that handles getting grids and their options
 */
class Iki_Grids {

	/**@var $active_grid Iki_IGrid */
	public $active_grid = null;


	/**@var $active_grid_manager Iki_Grid_Manager */
	protected $active_grid_manager = null;

	private static $class = null;
	protected $default_id = '1234567890';

	protected $default_row = null;
	protected $original_row = null;


	protected $original_grid = null;
	protected $default_grid = array();

	protected $original_options;
	protected $default_options;

	protected $premade_grids;

	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}


	/**
	 * Iki_Grids constructor.
	 */
	public function __construct() {

		require( plugin_dir_path( __FILE__ ) . 'mixed-row-data.php' );
		$json = iki_mixed_row_data_structure();

		$this->_create_premade_grids();

		$this->original_options = array(
			'default_row' => $this->default_row,
			'condensed'   => false,
			'fill_grid'   => false,// grid should always look filled
		);

		$this->default_options = $this->original_options;

		$mixeRowsReordered = array();
		foreach ( $json['mixed'] as $row ) {

			$row['type']                       = 'mixed';
			$mixeRowsReordered[ $row['name'] ] = $row;

		}


		$json['mixed']       = $mixeRowsReordered;
		$this->availableRows = $json;

	}

	/**
	 * Create default "premade" grids
	 */
	protected function _create_premade_grids() {

		//default grid
		$this->original_grid = array();
		$empty_row           = array(
			'cells'       => 4,
			'orientation' => 'portrait',
			'type'        => 'classic',
		);


		$square_row_2 = array(
			'cells'       => 2,
			'orientation' => 'square',
			'type'        => 'classic',
		);

		$square_row_3 = array(
			'cells'       => 3,
			'orientation' => 'square',
			'type'        => 'classic',
		);


		// square qrid
		$square_row_4 = array(
			'cells'       => 4,
			'orientation' => 'square',
			'type'        => 'classic',
		);
		// portfolio grid
		$portrait_row_2 = array(
			'cells'       => 2,
			'orientation' => 'portrait',
			'type'        => 'classic',
		);

		$portrait_row_3 = array(
			'cells'       => 3,
			'orientation' => 'portrait',
			'type'        => 'classic',
		);

		$portrait_row_4 = array(
			'cells'       => 4,
			'orientation' => 'portrait',
			'type'        => 'classic',
		);

		$landscape_row_1 = array(
			'cells'       => 1,
			'orientation' => 'landscape',
			'type'        => 'classic',
		);
		$landscape_row_2 = array(
			'cells'       => 2,
			'orientation' => 'landscape',
			'type'        => 'classic',
		);

		$landscape_row_3 = array(
			'cells'       => 3,
			'orientation' => 'landscape',
			'type'        => 'classic',
		);

		$landscape_row_4 = array(
			'cells'       => 4,
			'orientation' => 'landscape',
			'type'        => 'classic',
		);


		$default_data              = array();
		$default_data['fill_grid'] = false;
		$default_data['condensed'] = false;
		$default_data['classes']   = array();

		$default_data_object = new ArrayObject( $default_data );

		$condensed_data              = $default_data_object->getArrayCopy();
		$condensed_data['condensed'] = true;


		$this->premade_grids = array(
			'square_2'    => array(
				'rows' => array(),
				'data' => $default_data
			),
			'square_3'    => array(
				'rows' => array(),
				'data' => $default_data
			),
			'square_4'    => array(
				'rows' => array(),
				'data' => $default_data
			),
			'portrait_2'  => array(
				'rows' => array(),
				'data' => $default_data
			),
			'portrait_3'  => array(
				'rows' => array(),
				'data' => $default_data
			),
			'portrait_4'  => array(
				'rows' => array(),
				'data' => $default_data
			),
			'landscape_1' => array(
				'rows' => array(),
				'data' => $default_data
			),
			'landscape_2' => array(
				'rows' => array(),
				'data' => $default_data
			),
			'landscape_3' => array(
				'rows' => array(),
				'data' => $default_data
			),
			'landscape_4' => array(
				'rows' => array(),
				'data' => $default_data
			),

			'square_condensed_2' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'square_condensed_3' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'square_condensed_4' => array(
				'rows' => array(),
				'data' => $condensed_data
			),

			'portrait_condensed_2' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'portrait_condensed_3' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'portrait_condensed_4' => array(
				'rows' => array(),
				'data' => $condensed_data
			),

			'landscape_condensed_1' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'landscape_condensed_2' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'landscape_condensed_3' => array(
				'rows' => array(),
				'data' => $condensed_data
			),
			'landscape_condensed_4' => array(
				'rows' => array(),
				'data' => $condensed_data
			)
		);

		for ( $i = 1; $i <= 30; $i ++ ) {

			array_push( $this->original_grid, $empty_row );

			array_push( $this->premade_grids['square_2']['rows'], $square_row_2 );
			array_push( $this->premade_grids['square_3']['rows'], $square_row_3 );
			array_push( $this->premade_grids['square_4']['rows'], $square_row_4 );

			array_push( $this->premade_grids['portrait_2']['rows'], $portrait_row_2 );
			array_push( $this->premade_grids['portrait_3']['rows'], $portrait_row_3 );
			array_push( $this->premade_grids['portrait_4']['rows'], $portrait_row_4 );

			array_push( $this->premade_grids['landscape_1']['rows'], $landscape_row_1 );
			array_push( $this->premade_grids['landscape_2']['rows'], $landscape_row_2 );
			array_push( $this->premade_grids['landscape_3']['rows'], $landscape_row_3 );
			array_push( $this->premade_grids['landscape_4']['rows'], $landscape_row_4 );

			array_push( $this->premade_grids['square_condensed_2']['rows'], $square_row_2 );
			array_push( $this->premade_grids['square_condensed_3']['rows'], $square_row_3 );
			array_push( $this->premade_grids['square_condensed_4']['rows'], $square_row_4 );

			array_push( $this->premade_grids['portrait_condensed_2']['rows'], $portrait_row_2 );
			array_push( $this->premade_grids['portrait_condensed_3']['rows'], $portrait_row_3 );
			array_push( $this->premade_grids['portrait_condensed_4']['rows'], $portrait_row_4 );

			array_push( $this->premade_grids['landscape_condensed_1']['rows'], $landscape_row_1 );
			array_push( $this->premade_grids['landscape_condensed_2']['rows'], $landscape_row_2 );
			array_push( $this->premade_grids['landscape_condensed_3']['rows'], $landscape_row_3 );
			array_push( $this->premade_grids['landscape_condensed_4']['rows'], $landscape_row_4 );

		}


		$this->default_grid = $this->original_grid;

		//two mixed rows -default - landscape
		$iki_b_mixed = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_mixed( 'mixed-1' ),
				Iki_Row_Payload::get_mixed( 'mixed-1-reverse' ),
				//add landscape
			)
		);
		//two mixed rows and portrait -  default landscape
		$iki_b_mixed_2             = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_mixed( 'mixed-1' ),
				Iki_Row_Payload::get_mixed( 'mixed-1-reverse' ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				//add landscape
			)
		);
		$iki_b_mixed_2_default_row = Iki_Row_Payload::get_classic( Iki_Row_Payload::LANDSCAPE, Iki_Row_Payload::C_1 );

		//mixed two rows + default portrait
		$iki_b_mixed_3             = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_mixed( 'mixed-1' ),
				Iki_Row_Payload::get_mixed( 'mixed-1-reverse' ),
				//add potrait
			)
		);
		$iki_b_mixed_3_default_row = Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 );

		//mixed two rows + default landscape
		$iki_b_mixed_4             = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_mixed( 'mixed-1' ),
				Iki_Row_Payload::get_mixed( 'mixed-1-reverse' ),
				//add landscape
			)
		);
		$iki_b_mixed_4_default_row = Iki_Row_Payload::get_classic( Iki_Row_Payload::LANDSCAPE, Iki_Row_Payload::C_1 );
		//only potrait
		$iki_b_mixed_5 = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 )
			)
		);


		//only landscape
		$iki_b_mixed_6 = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_classic( Iki_Row_Payload::LANDSCAPE, Iki_Row_Payload::C_1 ),
			)
		);

		//only landscape
		//landscape alt - no zigzag
		$iki_b_mixed_7 = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_classic( Iki_Row_Payload::LANDSCAPE, Iki_Row_Payload::C_1 ),
			)
		);

		//mixed -portarit - mixed reverse  - portrait
		$iki_b_mixed_8 = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_mixed( 'mixed-1' ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				Iki_Row_Payload::get_mixed( 'mixed-1-reverse' ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				//add landscape
			)
		);
		//portrait - landscape
		$iki_b_mixed_9 = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::LANDSCAPE, Iki_Row_Payload::C_1 )
			)
		);

		//portrait - portrait - landscape
		$iki_b_mixed_10 = array(
			'data' => array(
				'fill_grid' => false,
				'condensed' => false,
				'classes'   => array(),
			),
			'rows' => array(
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::PORTRAIT, Iki_Row_Payload::C_4 ),
				Iki_Row_Payload::get_classic( Iki_Row_Payload::LANDSCAPE, Iki_Row_Payload::C_1 )
			)
		);
		//push default rows to grids that need them.
		for ( $i = 1; $i < 100; $i ++ ) {
			array_push( $iki_b_mixed_2['rows'], $iki_b_mixed_2_default_row );
			array_push( $iki_b_mixed_3['rows'], $iki_b_mixed_3_default_row );
			array_push( $iki_b_mixed_4['rows'], $iki_b_mixed_4_default_row );
		}

		//match the key from "_filter_iki_admin_options_premade_grids" hook.
		$this->premade_grids['iki_b-mixed']       = $iki_b_mixed;
		$this->premade_grids['iki_b-mixed-2']     = $iki_b_mixed_2;
		$this->premade_grids['iki_b-mixed-3']     = $iki_b_mixed_3;
		$this->premade_grids['iki_b-mixed-4']     = $iki_b_mixed_4;
		$this->premade_grids['iki_b-mixed-5']     = $iki_b_mixed_8;
		$this->premade_grids['iki_b-port']        = $iki_b_mixed_5;
		$this->premade_grids['iki_b-port-land']   = $iki_b_mixed_9;
		$this->premade_grids['iki_b-port-land-2'] = $iki_b_mixed_10;
		$this->premade_grids['iki_b-land']        = $iki_b_mixed_6;
		$this->premade_grids['iki_b-land-2']      = $iki_b_mixed_7;

		$this->premade_grids = apply_filters( 'iki_toolkit_wonder_grid_premade_grids', $this->premade_grids );
	}

	/**
	 * Get premade grids
	 * @return mixed
	 */
	public function get_premade_grids() {

		return $this->premade_grids;

	}

	/**
	 * Get default grid id
	 * @return string
	 */
	public function get_default_grid_id() {
		return $this->default_id;
	}

	/**
	 * Set default grid id
	 *
	 * @param $grid
	 */
	public function set_default_grid_id( $grid ) {

		$this->default_grid = $grid;
	}

	/** Get default data
	 * @return array
	 */
	public function get_default_grid() {

		return $this->default_grid;
	}

	/**
	 * Reset default grid
	 */
	public function reset_default_grid() {

		$this->default_grid = $this->original_grid;
	}

	/**
	 * Set default grid options
	 *
	 * @param $options
	 */
	public function set_grid_default_options( $options ) {
		$this->default_options = $options;
	}

	/**
	 * Reset default grid options
	 */
	public function reset_grid_default_options() {

		$this->default_options = $this->original_options;
	}

	/**
	 * Get grid default options
	 * @return array
	 */
	public function get_grid_default_options() {

		return $this->default_options;

	}

	/**
	 * Find grid by id
	 *
	 * @param $id
	 *
	 * @return array
	 */
	public function find( $id ) {


		$grid_defaults = $this->default_options;
		$using_premade = false;

		$grid_options = array();

		// check if $id is for premade grid.
		if ( isset( $this->premade_grids[ $id ] ) ) {

			$using_premade = true;

			$grid_rows = $this->premade_grids[ $id ]['rows'];

			$premade_data = new ArrayObject( $this->premade_grids[ $id ]['data'] );

			$premade_data['classes'][] = 'iki-pm-grid';

			$grid_options = $premade_data->getArrayCopy();

		} else {
			$grid_rows = $this->get_grid_rows( $id );
		}

		if ( $id != $this->default_id && ! $using_premade ) {

			$grid_options['fill_grid'] = (bool) get_post_meta( $id, 'iki_fill_grid', true );
			$grid_options['condensed'] = (bool) get_post_meta( $id, 'iki_grid_condensed', true );

			$grid_options['classes'] = get_post_meta( $id, 'iki_grid_classes', true );
		}

		$grid_options['id']        = $id;
		$grid_options['grid_rows'] = $grid_rows;

		$grid_options = wp_parse_args( $grid_options, $grid_defaults );

		return $grid_options;
	}

	/**
	 * Get currently active grid
	 * @return Iki_IGrid
	 */
	public function get_active_grid() {
		return $this->active_grid;
	}


	/**
	 * Set active grid
	 *
	 * @param $grid
	 */
	public function set_active_grid( $grid ) {
		$this->active_grid = $grid;
	}

	/**
	 * Get rows of the grid
	 *
	 * @param null $id grid id
	 *
	 * @return array|mixed|object
	 */
	public function get_grid_rows( $id = null ) {

		try {
			$gridData = json_decode( wp_unslash( get_post_meta( $id, 'iki_grid_data', true ) ), true );
		} catch ( Exception $e ) {

		}

		if ( ! empty( $gridData ) ) {
			foreach ( $gridData as $key => &$value ) {

				if ( $value['type'] == 'mixed' ) {
					if ( isset( $this->availableRows['mixed'][ $value['name'] ] ) ) {
						$value['orientation'] = $this->availableRows['mixed'][ $value['name'] ]['orientation'];
					}
				}
			}

			return $gridData;

		} else {
			//empty grid has been saved
			return $this->default_grid;

		}
	}

	/**
	 * Get available rows for the grid
	 * @return mixed
	 */
	public function get_available_rows() {
		return $this->availableRows;
	}

	/**
	 * Get a mixed row by row name
	 *
	 * @param $rowName
	 *
	 * @return bool
	 */
	public function get_mixed_row_by_name( $rowName ) {
		if ( isset( $this->availableRows['mixed'][ $rowName ] ) ) {
			return $this->availableRows['mixed'][ $rowName ];
		} else {
			return false;
		}
	}
}