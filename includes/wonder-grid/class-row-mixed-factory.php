<?php

/**
 * Creates and returns appropriate mixed row based on the data passed.
 */
class Iki_Row_Mixed_Factory implements Iki_IRow_Factory {

	/**
	 * Create a row depending on the data passed
	 *
	 * @param Iki_Grid $grid
	 * @param $row_data
	 * @param $grid_data
	 *
	 * @return Iki_Row_Mixed_1|Iki_Row_Mixed_1_Reverse|Iki_Row_Mixed_2|Iki_Row_Mixed_2_Reverse|Iki_Row_Mixed_3
	 */
	public static function get_row( Iki_Grid $grid, $row_data, $grid_data ) {


		$rowName = $row_data->name;

		switch ( $rowName ) {

			case 'mixed-1' :

				return new Iki_Row_Mixed_1( $grid, $row_data, $grid_data );
				break;

			case 'mixed-2' :

				return new Iki_Row_Mixed_2( $grid, $row_data, $grid_data );
				break;

			case 'mixed-3' :

				return new Iki_Row_Mixed_3( $grid, $row_data, $grid_data );
				break;

			case 'mixed-1-reverse' :

				return new Iki_Row_Mixed_1_Reverse( $grid, $row_data, $grid_data );
				break;

			case 'mixed-2-reverse' :

				return new Iki_Row_Mixed_2_Reverse( $grid, $row_data, $grid_data );
				break;
			default :
				//mixed-1 will be default
				return new Iki_Row_Mixed_1( $grid, $row_data, $grid_data );

		}

	}

}