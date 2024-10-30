<?php


/**
 * Class that represents a particular row in a grid
 */
class Iki_Row_Mixed_3 extends Iki_Row_Mixed {


	/**
	 * Print cell
	 * @inheritdoc
	 */
	public function print_cell() {

		if ( $this->used_cells == 0 ) {

			echo $this->open_section( 1, 2, array( 'iki-land-2' ) );
		}
		if ( $this->used_cells == 2 ) {

			echo $this->open_section( 2, 4, array( 'iki-sq-4' ) );
		}

		parent::print_cell();


		if ( $this->used_cells == 2 ) {

			echo $this->close_section( 1 );
		}

		if ( $this->used_cells == 6 ) {

			echo $this->close_section( 2 );
		}

	}

}