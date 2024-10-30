<?php

/**
 * Class that represents cell in a grid
 */
class Iki_Grid_Cell {

	protected $asset_orientation = '';

	protected $position_in_row = '';

	protected $row_number = '';

	protected $row_cells = '';

	protected $row_type = '';

	public $data = array();

	/**
	 * Get cell data
	 * @return array
	 */
	public function getData() {

		return array(
			'asset_orientation' => $this->asset_orientation,
			'position_in_row'   => $this->position_in_row,
			'row_number'        => $this->row_number,
			'row_total_cells'   => $this->row_cells,
			'row_type'          => $this->row_type
		);
	}

	/**
	 * Get orientation
	 * @return string
	 */
	public function get_orientation() {

		return $this->asset_orientation;
	}

	/**
	 * Set orientation
	 *
	 * @param $orientation
	 */
	public function set_orientation( $orientation ) {
		$this->asset_orientation = $orientation;
	}

	/**
	 * Get position in row
	 * @return string
	 */
	public function get_position_in_row() {
		return $this->position_in_row;
	}

	/**
	 * Set position in row
	 *
	 * @param $pos
	 */
	public function set_position_in_row( $pos ) {
		$this->position_in_row = $pos;
	}

	/**
	 * Get row number
	 * @return string
	 */
	public function get_row_number() {
		return $this->row_number;
	}

	/**
	 * Set row number
	 *
	 * @param $row_num
	 */
	public function set_row_number( $row_num ) {
		$this->row_number = $row_num;
	}

	/**
	 * Get row cells
	 * @return string
	 */
	public function get_row_cells() {
		return $this->row_cells;
	}

	/**
	 * Set row cells
	 *
	 * @param $num
	 */
	public function set_row_cells( $num ) {
		$this->row_cells = $num;
	}

	/**
	 * Get row type
	 * @return string
	 */
	public function get_row_type() {
		return $this->row_type;
	}

	/**
	 * Set row type
	 *
	 * @param $type
	 */
	public function set_row_type( $type ) {
		$this->row_type = $type;
	}
}