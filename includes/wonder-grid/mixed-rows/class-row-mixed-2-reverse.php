<?php


/**
 * Class that represents a particular row in a grid
 */
class Iki_Row_Mixed_2_Reverse extends Iki_Row_Mixed {

	/**
	 * Print cell
	 * @inheritdoc
	 */
	public function print_cell() {


		if ( $this->used_cells == 0 ) {

			echo $this->open_section( 1, 4, array( 'iki-sq-4' ) );

		}


		if ( $this->used_cells == 4 ) {
			echo $this->open_section( 2, 1, array( 'iki-sq-1' ) );

		}

		parent::print_cell();


		if ( $this->used_cells == 4 ) {
			echo $this->close_section( 1 );

		}

		if ( $this->used_cells == 5 ) {
			echo $this->close_section( 2 );

		}

	}

}