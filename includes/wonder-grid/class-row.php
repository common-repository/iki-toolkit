<?php

/**
 * Abstract class that represents a row in a grid.
 */
abstract class Iki_Row {

	/**@var Iki_Grid_Cell $current_cell */
	public $current_cell;
	public $is_open = false;

	/**@var Iki_Grid_Row_Data $row_data */
	protected $row_data;
	/**@var Iki_Grid_Data $grid_data */
	public $grid_data;

	/**@var Iki_Grid $grid */
	protected $grid;
	public $cell_capacity;
	public $used_cells = 0;

	protected $type;

	protected $name;
	protected $supports_grid_fill = true;

	protected $cell_classes = array();

	/**
	 * Iki_Row constructor.
	 *
	 * @param Iki_Grid $grid
	 * @param Iki_Grid_Row_Data $row_data
	 * @param Iki_Grid_Data $grid_data
	 */
	public function __construct( Iki_Grid $grid, Iki_Grid_Row_Data $row_data, Iki_Grid_Data $grid_data ) {

		$this->row_data  = $row_data;
		$this->grid_data = $grid_data;
		$this->grid      = $grid;

		//empty cells is total number of cells that is supported.
		$this->type = $this->row_data->type;
		$this->calculate_cell_capacity();
		$this->calculate_used_cells();

	}

	/**
	 * Return row data
	 * @return Iki_Grid_Row_Data
	 */
	public function get_row_data() {
		return $this->row_data;
	}

	/**
	 * Get how many cells can fit in a row
	 */
	protected function calculate_cell_capacity() {
		$this->cell_capacity = (int) $this->row_data->cells;
	}

	/**
	 * Get Row name
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get row type
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Calculate how many cells are already spent
	 */
	protected function calculate_used_cells() {
		if ( $this->row_data->missing_cells > 0 ) {
			$this->used_cells = $this->cell_capacity - $this->row_data->missing_cells;

		}

	}

	/**
	 * Check if row has empty cells
	 * @return bool
	 */
	public function has_empty_cells() {

		return $this->cell_capacity > $this->used_cells;
	}

	/**
	 * Open row
	 * @return string
	 */
	public function open() {

		$this->is_open = true;
		//recursively sanitize html class attribute (sanitize_html_class)
		$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $this->get_row_classes() );
		// build and sanitize custom html attributes
		$data = Iki_Toolkit_Utils::array_to_html_attr( $this->_get_data() );

		$row_html = '<div class="%1$s" %2$s >';

		return sprintf( $row_html, $classes, $data );

	}

	/**
	 * Calculate row empty cells
	 * @return int
	 */
	protected function calculate_empty_cells() {

		if ( $this->grid_data->total_cells - $this->grid_data->cell_iterator < $this->cell_capacity - $this->used_cells ) {
			$empty = $this->row_data->cells - ( $this->grid_data->total_cells - $this->grid_data->cell_iterator );
		} else {

			if ( $this->grid_data->cell_iterator + $this->cell_capacity > $this->grid_data->total_cells ) {

				if ( $this->grid_data->fill_grid && $this->supports_grid_fill ) {

					$empty = $this->grid_data->total_cells - $this->grid_data->cell_iterator;
				} else {

					// don't fill the grid
					$empty = ( $this->cell_capacity - $this->used_cells );
				}
			} else {

				$empty = 0;
			}

		}

		return $empty;
	}

	/**
	 * Return empty cells
	 * @return mixed
	 */
	public function get_empty_cells() {

		return $this->cell_capacity - $this->used_cells;
	}


	/**
	 * Get row classes
	 * @return array|mixed
	 */
	protected function get_row_classes() {

		$row_classes = array( 'iki-grid-row' );

		$row_classes[] = 'iki-row-type-' . $this->row_data->type;

		$row_classes = apply_filters( 'iki_grid_row_class', $row_classes, $this->grid_data, $this->row_data );

		return $row_classes;

	}

	/**
	 * Get row data
	 * @return array|mixed
	 */
	protected function _get_data() {

		$empty = $this->calculate_empty_cells();

		$data = array(
			'data-iki-type'  => $this->row_data->type,
			'data-iki-empty' => $empty,
			'data-iki-cells' => $this->row_data->cells
		);

		$data = apply_filters( 'iki_grid_get_row_data', $data, $this );

		return $data;

	}

	/**
	 * Return row data
	 * @return Iki_Grid_Row_Data
	 */
	public function get_data() {
		return $this->row_data;
	}

	/**
	 * Close the row
	 * @return string
	 */
	public function close() {

		$this->is_open = false;

		return '</div>';
	}

	/**
	 * Setup cell properties
	 * @return Iki_Grid_Cell
	 */
	public function prepare_cell() {

		$this->current_cell = new Iki_Grid_Cell();
		$this->current_cell->set_position_in_row( $this->used_cells + 1 );
		$this->current_cell->set_row_number( $this->grid_data->current_row_num );
		$this->current_cell->set_row_cells( $this->row_data->cells );
		$this->current_cell->set_row_type( $this->row_data->type );

		return $this->current_cell;
	}

	/**
	 * Print row cell
	 */
	public function print_cell() {
		echo $this->open_cell( $this->current_cell );
		echo $this->grid->get_item_template();
		echo $this->close_cell();

		$this->used_cells ++;

	}

	/**
	 * Return current cell
	 * @return Iki_Grid_Cell
	 */
	public function get_current_cell() {
		return $this->current_cell;
	}

	/**
	 * Open new cell
	 *
	 * @param Iki_Grid_Cell $currentCell
	 *
	 * @return string
	 */
	protected function open_cell( Iki_Grid_Cell $currentCell ) {


		//recursively sanitize html class attribute (sanitize_html_class)
		$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $this->get_cell_classes( $currentCell ) );

		$cellData = $this->get_cell_data( $currentCell );

		$data = apply_filters( 'iki_grid_cell_data', array(), $this->grid_data->getData(), $this->row_data->get_data(), $currentCell->getData() );

		$data = array_merge( $data, $cellData );

		$data = Iki_Toolkit_Utils::array_to_html_attr( $data );

		$html = '<div class="%1$s" %2$s >';

		return sprintf( $html, $classes, $data );
	}

	/**
	 * Get cell classes (fire up a cell class filter)
	 *
	 * @param Iki_Grid_Cell $cell
	 *
	 * @return array
	 */
	protected function get_cell_classes( Iki_Grid_Cell $cell ) {

		$classes = array();

		$classes   = apply_filters( 'iki_grid_cell_class', $classes, $this->row_data, $cell );
		$classes[] = 'iki-grid-thumb';

		return $classes;
	}

	/**
	 * Get Cell attribute data
	 *
	 * @param Iki_Grid_Cell $cell
	 *
	 * @return array
	 */
	protected function get_cell_data( Iki_Grid_Cell $cell ) {

		$data = array(
			'data-iki-num'         => $cell->get_position_in_row(),
			'data-iki-orientation' => $cell->get_orientation(),
		);

		return $data;
	}

	/**
	 * Close currently opened cell
	 * @return string
	 */
	protected function close_cell() {
		return '</div>';

	}


}