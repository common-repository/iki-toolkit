<?php

/** Interface for grid row data*/
interface Iki_IGrid_Row_Data {
}

/**
 * Represents grid data for a row.
 */
class Iki_Grid_Row_Data implements Iki_IGrid_Row_Data {

	public $cells = 1;
	public $orientation = 'landscape';
	public $type = 'classic';
	public $missing_cells = 0;

	public $name = '';
	public $condensed = false;

	/**
	 * Iki_Grid_Row_Data constructor.
	 *
	 * @param null $payload
	 */
	public function __construct( $payload = null ) {

		if ( $payload ) {
			$this->type = $payload['type'];

			if ( $this->type == 'mixed' ) {
				$this->cells       = count( $payload['orientation'] );
				$this->orientation = $payload['orientation'];
				$this->name        = $payload['name'];

			} else {
				$this->orientation = $payload['orientation'];
				$this->cells       = (int) $payload['cells'];

			}

			if ( isset( $payload['condensed'] ) ) {
				$this->condensed = $payload['condensed'];
			}
			if ( isset( $payload['missing_cells'] ) ) {

				$this->missing_cells = $payload['missing_cells'];
			}
		}


	}

	/**
	 * Get grid data
	 * @return array
	 */
	public function get_data() {

		return array(
			'cells'         => $this->cells,
			'orientation'   => $this->orientation,
			'type'          => $this->type,
			'missing_cells' => $this->missing_cells,
			'condensed'     => $this->condensed,
			'name'          => $this->name
		);

	}
}