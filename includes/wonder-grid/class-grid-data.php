<?php

/**
 * Class that holds grid data for a particular grid
 */
class Iki_Grid_Data {

	//TODO make them private
	public $cell_iterator;
	public $cell_iterator_offset;
	public $current_row_num;
	public $total_cells;
	public $fill_grid = false;
	public $location;

	/**
	 * Iki_Grid_Data constructor.
	 *
	 * @param $data
	 */
	public function __construct( $data ) {

		$this->cell_iterator = (int) $data['cell_iterator'];
		$this->total_cells   = (int) $data['total_cells'];
		$this->fill_grid     = $data['fill_grid'];

		$this->cell_iterator_offset = (int) $data['cell_iterator_offset'];
		$this->current_row_num      = (int) $data['current_row_num'];
		$this->location             = $data['location'];
	}

	/**
	 * Get cell data
	 * @return array
	 */
	public function getData() {

		return array(
			'cell_iterator'        => $this->cell_iterator,
			'current_row_num'      => $this->current_row_num,
			'total_cells'          => $this->total_cells,
			'fill_grid'            => $this->fill_grid,
			'cell_iterator_offset' => $this->cell_iterator_offset,
			'location'             => $this->location
		);
	}
}