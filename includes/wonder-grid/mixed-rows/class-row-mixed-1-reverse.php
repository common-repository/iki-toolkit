<?php

/**
 * Class that represents a particular row in a grid
 */
class Iki_Row_Mixed_1_Reverse extends Iki_Row_Mixed {


	/**
	 * @inheritdoc
	 */
	public function print_cell() {

		if ( $this->used_cells == 0 ) {

			echo $this->open_section( 1, 2, array( 'iki-land-2' ) );
		}
		if ( $this->used_cells == 2 ) {
			echo $this->open_section( 2, 1, array( 'iki-sq-1' ) );
		}


		parent::print_cell();


		if ( $this->used_cells == 2 ) {

			echo $this->close_section( 1 );

		}

		if ( $this->used_cells == 3 ) {
			echo $this->close_section( 2 );
		}

	}

}