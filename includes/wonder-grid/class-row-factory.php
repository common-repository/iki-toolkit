<?php

/**
 * Interface for the row factory
 */
interface Iki_IRow_Factory {
	/**
	 * @param Iki_Grid $grid
	 * @param $row_data
	 * @param $grid_data
	 *
	 * @return mixed
	 */
	public static function get_row( Iki_Grid $grid, $row_data, $grid_data );
}

/**
 * Creates and returns appropriate row based on the data passed.
 */
class Iki_Row_Factory implements Iki_IRow_Factory {


	/**
	 * @param Iki_Grid $grid
	 * @param $row_data
	 * @param $grid_data
	 *
	 * @return Iki_Row_Classic|Iki_Row_Mixed_1|Iki_Row_Mixed_1_Reverse|Iki_Row_Mixed_2|Iki_Row_Mixed_2_Reverse|Iki_Row_Mixed_3
	 */
	public static function get_row( Iki_Grid $grid, $row_data, $grid_data ) {

		if ( $row_data->type === 'classic' ) {
			return new Iki_Row_Classic( $grid, $row_data, $grid_data );
		} else {
			return Iki_Row_Mixed_Factory::get_row( $grid, $row_data, $grid_data );
		}

	}

}