<?php

/**
 * Creates a grid from wp query
 */
class Iki_Post_Grid extends Iki_Grid {

	/**@var WP_Query $this ->aggregate */
	protected $aggregate;

	/**
	 * Iki_Post_Grid constructor.
	 *
	 * @param $grid_rows
	 * @param null $default_row
	 * @param null $id
	 * @param bool $use_grid_wrapper
	 * @param bool $fill_grid
	 * @param string $grid_location
	 */
	public function __construct( $grid_rows, $default_row, $id, $use_grid_wrapper, $fill_grid, $grid_location ) {
		parent::__construct( $grid_rows, $default_row, $id, $use_grid_wrapper, $fill_grid, $grid_location );
		add_filter( 'iki_grid_get_row_data', array( $this, 'get_row_data' ), 10, 2 );
	}

	/**
	 * @inheritdoc
	 * @return bool|mixed
	 */
	protected function item_iterator() {

		return $this->aggregate->have_posts();
	}

	/**
	 * @inheritdoc
	 */
	protected function loop_iteration_start() {
		$this->aggregate->the_post();
	}

	/**
	 * @inheritdoc
	 * @return int|mixed
	 */
	protected function get_total_cells() {
		return $this->aggregate->post_count - ( $this->cell_offset );
	}

	/**
	 * Get row data
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function get_row_data( $data ) {
		return $data;
	}
}

