<?php

/**
 * Represents classic row in a grid.
 */
class Iki_Row_Classic extends Iki_Row {


	/**
	 * Iki_Row_Classic constructor.
	 *
	 * @param Iki_Grid $grid
	 * @param Iki_Grid_Row_Data $row_data
	 * @param Iki_Grid_Data $grid_data
	 */
	public function __construct( Iki_Grid $grid, Iki_Grid_Row_Data $row_data, Iki_Grid_Data $grid_data ) {

		parent::__construct( $grid, $row_data, $grid_data );
		if ( $this->supports_grid_fill ) {
			$this->handle_fill_grid( $this->grid_data->fill_grid );
		}

		$this->cell_capacity = $this->row_data->cells;

		$this->name = $this->type . '_' . $this->cell_capacity . '_' . $this->row_data->orientation;
	}

	/**
	 * Handle if the grid needs to be filled at the end.
	 *
	 * @param $fill_grid
	 */
	protected function handle_fill_grid( $fill_grid ) {


		if ( $fill_grid ) {

			$this->row_data->cells = ( $this->grid_data->cell_iterator + $this->row_data->cells > $this->grid_data->total_cells ) ? $this->grid_data->total_cells - $this->grid_data->cell_iterator : $this->row_data->cells;


			if ( $this->row_data->cells == 1 ) {
				$this->row_data->orientation = 'landscape';
			}
		}
	}


	/**
	 * @inheritdoc
	 * @return array|mixed
	 */
	protected function get_row_classes() {

		$row_classes = parent::get_row_classes();

		$row_classes[] = 'iki-row-orientation-' . $this->row_data->orientation;
		$row_classes[] = 'iki-row-cells-' . $this->row_data->cells;

		return $row_classes;
	}


	/**@inheritdoc
	 * @return array|mixed
	 */
	protected function _get_data() {

		$data                         = parent::_get_data();
		$data['data-iki-orientation'] = $this->row_data->orientation;

		return $data;

	}


	/**@inheritdoc
	 * @return Iki_Grid_Cell
	 */
	public function prepare_cell() {

		parent::prepare_cell();

		$this->current_cell->set_orientation( $this->row_data->orientation );

		return $this->current_cell;
	}

}