<?php


/**
 * Class that represents a particular row in a grid
 */
class Iki_Row_Mixed extends Iki_Row {


	protected $open_sections;
	protected $supports_grid_fill = false;

	protected $cell_classes = array();

	/**
	 * Iki_Row_Mixed constructor.
	 *
	 * @param Iki_Grid $grid
	 * @param Iki_Grid_Row_Data $row_data
	 * @param Iki_Grid_Data $grid_data
	 */
	public function __construct( Iki_Grid $grid, Iki_Grid_Row_Data $row_data, Iki_Grid_Data $grid_data ) {

		$this->open_sections = array();
		parent::__construct( $grid, $row_data, $grid_data );
		$this->name = $this->row_data->name;
	}

	/**
	 * Calculate cell capacity
	 */
	protected function calculate_cell_capacity() {
		$this->cell_capacity = count( $this->row_data->orientation );
	}


	/**
	 * Setup classes for the row
	 * @return array|mixed
	 */
	protected function get_row_classes() {

		$rowClasses = parent::get_row_classes();

		$rowClasses[] = 'row-name-' . $this->row_data->name;
		$rowClasses[] = ( $this->row_data->condensed ) ? 'row-condensed' : '';
		$rowClasses[] = 'iki-row-cells-mixed-' . $this->row_data->cells;

		return $rowClasses;
	}


	/**
	 * Get row data
	 * @return array|mixed
	 */
	protected function _get_data() {

		$data = parent::_get_data();

		$data['data-iki-name']      = $this->row_data->name;
		$data['data-iki-condensed'] = ( $this->row_data->condensed ) ? 1 : 0;

		return $data;

	}

	/**
	 * Prepare data for row cell
	 * @return Iki_Grid_Cell
	 */
	public function prepare_cell() {

		parent::prepare_cell();

		if ( is_array( $this->row_data->orientation ) ) {
			$orientation = $this->row_data->orientation[ $this->used_cells ];
		} else {
			$orientation = $this->row_data->orientation;
		}

		$this->current_cell->set_orientation( $orientation );

		return $this->current_cell;
	}

	/**
	 * Get cell classes
	 *
	 * @param Iki_Grid_Cell $cell
	 *
	 * @return array
	 */
	protected function get_cell_classes( Iki_Grid_Cell $cell ) {

		$classes = parent::get_cell_classes( $cell );

		if ( isset( $this->cell_classes[ $this->used_cells ] ) ) {
			$classes[] = $this->cell_classes[ $this->used_cells ];
		}

		return $classes;
	}

	/**
	 * Close the row
	 * @return string
	 */
	public function close() {

		//close all open wrappers
		$html = '';
		foreach ( $this->open_sections as $section ) {
			$html .= '</div>';
		}

		$html .= parent::close();

		return $html;
	}


	/**
	 * Open row section
	 *
	 * @param $section_id
	 * @param $capacity
	 * @param array $classes
	 *
	 * @return string
	 */
	protected function open_section( $section_id, $capacity, $classes = array() ) {


		$this->open_sections[ $section_id ] = true;

		$classes[] = 'iki-section-' . $section_id;
		$classes[] = 'iki-thumb-section';
		$classes   = join( ' ', $classes );

		return sprintf( '<div data-iki-section-capacity="%1$s" class=" %2$s">', $capacity, $classes );
	}

	/**
	 * Close row section
	 *
	 * @param $section_id
	 *
	 * @return string
	 */
	protected function close_section( $section_id ) {

		$html = '';
		if ( isset( $this->open_sections[ $section_id ] ) ) {

			unset( $this->open_sections[ $section_id ] );
			$html = '</div>';
		}

		return $html;

	}


}