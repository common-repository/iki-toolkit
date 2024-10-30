<?php

/**
 * Class to handle grid management in the theme
 * (theme specific)
 */
class Iki_Grid_Manager {

	/**@var Iki_Grid $grid */
	protected $grid;

	public $grid_rows;
	public $options;

	protected $grid_location = '';

	protected $use_grid_wrapper = true;
	protected $default_row;
	protected $grid_id;
	protected $extra_data = array();

	/**
	 * Iki_Grid_Manager constructor.
	 *
	 * @param $options
	 */
	public function __construct( $options ) {
		$this->options = $options;

		$this->grid_location        = ( isset( $options['location'] ) ) ? $options['location'] : 'main';
		$this->grid_id              = $options['id'];
		$this->use_grid_wrapper     = ( isset( $options['use_grid_wrapper'] ) ) ? $options['use_grid_wrapper'] : $this->use_grid_wrapper;
		$this->options['js_export'] = ( isset( $this->options['js_export'] ) ) ? $this->options['js_export'] : array();
		$this->grid_rows            = $this->parse_grid_rows( $this->options['grid_rows'] );
	}


	/**
	 * Add extra data to be passed to the grid
	 *
	 * @param string $key key for the array
	 * @param mixed $value anything can be passed
	 */
	public function add_custom_data( $key, $value ) {

		$this->extra_data[ $key ] = $value;
	}

	/**
	 * Add data that is going to be exported in html
	 *
	 * @param array $new_data
	 */
	public function add_export_data( $new_data ) {
		$this->options['js_export'] = array_replace_recursive( $this->options['js_export'], $new_data );
	}

	public function add_grid_classes( array $classes ) {
		if ( isset( $this->options['classes'] ) ) {
			$this->options['classes'] = array_merge( $this->options['classes'], $classes );
		} else {
			$this->options['classes'] = $classes;
		}
	}

	/**
	 * Get grid location
	 * @return string
	 */
	public function get_grid_location() {
		return $this->grid_location;
	}


	/**
	 * Parse grid rows
	 *
	 * @param $rows
	 *
	 * @return array
	 */
	protected function parse_grid_rows( $rows ) {
		$a = array();
		foreach ( $rows as &$row ) {

			//always force condensed on mixed rows when the grid is condensed itself.

			if ( $row['type'] == 'mixed' && $this->options['condensed'] ) {
				$row['condensed'] = true;
			}
			array_push( $a, new Iki_Grid_Row_Data( $row ) );
		}

		return $a;
	}

	/**
	 * Get grid thumb classes
	 *
	 * @param $classes
	 * @param $row_data
	 * @param $cell
	 *
	 * @return array
	 */
	public function grid_thumb_class( $classes, $row_data, $cell ) {

		$classes[] = 'iki-anim-root';

		return $classes;
	}

	/**
	 * Get grid data to be exported for javascript
	 *
	 * @param $grid_data
	 * @param Iki_Grid $grid
	 *
	 * @return array
	 */
	public function filter_grid_data_js_cb( $grid_data, Iki_Grid $grid ) {

		if ( $this->grid_location == $grid->get_location() ) {

			if ( isset( $this->options['js_export'] ) ) {

				$grid_data = array_merge_recursive( $grid_data, $this->options['js_export'] );
			}
		}

		return $grid_data;

	}

	/**
	 * Print the grid
	 *
	 * @param $aggregate
	 * @param null $offset
	 * @param int $break_after
	 *
	 * @return int
	 */
	public function print_grid( $aggregate, $offset = null, $break_after = 0 ) {

		if ( is_a( $aggregate, 'WP_Query' ) ) {
			$grid_class = 'Iki_Post_Grid';


		} else {
			$grid_class = 'Iki_Asset_Grid';
		}

		$print_grid_wrapper = true;

		if ( isset( $GLOBALS['iki_theme'] ) ) {

			$print_grid_wrapper = ! $GLOBALS['iki_theme']['flags']['ajax_pagination'];

		}

		$GLOBALS['iki_toolkit']['flags']['printing_grid'] = true;

		add_filter( 'iki_grid_cell_class', array( $this, 'grid_thumb_class' ), 10, 3 );
		add_filter( 'iki_grid_class', array( $this, 'grid_wrapper_class' ), 10, 2 );
		add_filter( 'iki_grid_data_js', array( $this, 'filter_grid_data_js_cb' ), 10, 2 );
		add_filter( 'iki_grid_setup_custom_data', array( $this, 'grid_extra_data_cb' ), 1, 2 );

		$fillGrid   = ( isset( $this->options['fill_grid'] ) ) ? $this->options['fill_grid'] : false;
		$this->grid = new $grid_class(
			$this->grid_rows,
			null,//$this->default_row,
			$this->grid_id,
			$this->use_grid_wrapper,
			$fillGrid,
			$this->grid_location
		);

		if ( isset( $offset['cell_offset'] ) ) {

			$this->grid->set_cell_offset( $offset['cell_offset'] );
		}

		$num_of_printed_cells = $this->grid->print_grid( $aggregate, $offset, $print_grid_wrapper, $break_after );

		$GLOBALS['iki_toolkit']['flags']['printing_grid'] = false;

		remove_filter( 'iki_grid_cell_class', array( $this, 'grid_thumb_class' ) );
		remove_filter( 'iki_grid_class', array( $this, 'grid_wrapper_class' ) );
		remove_filter( 'iki_grid_data_js', array( $this, 'filter_grid_data_js_cb' ), 10 );
		remove_filter( 'iki_grid_setup_custom_data', array( $this, 'grid_extra_data_cb' ) );

		return $num_of_printed_cells;
	}

	/**
	 * Get grid extra (custom data) to be associated with the grid
	 *
	 * @param $data
	 * @param Iki_Grid $grid
	 *
	 * @return mixed
	 */
	public function grid_extra_data_cb( $data, Iki_Grid $grid ) {
		if ( $this->grid_location == $grid->get_location() ) {
			if ( isset( $this->options['design'] ) ) {

				$data['design'] = $this->options['design'];
			}
			if ( isset( $this->options['location'] ) ) {
				$data['location'] = $this->options['location'];

			}
			if ( ! empty( $this->extra_data ) ) {
				$data = array_merge_recursive( $data, $this->extra_data );
			}
		}

		return $data;
	}

	/**
	 * Get grid wrapper classes
	 *
	 * @param $classes
	 * @param Iki_Grid $grid
	 *
	 * @return array
	 */
	public function grid_wrapper_class( $classes, Iki_Grid $grid ) {
		if ( $this->grid_location == $grid->get_location() ) { //respond only to your grid

			$extra_data = $grid->get_custom_data();
			$classes[]  = ( $this->options['condensed'] ) ? 'iki-grid-condensed' : 'iki-grid-spaced';
			$classes[]  = 'iki-location-' . $this->options['location'];

			//check if custom blog grid.
			if ( strpos( $extra_data['id'], 'iki_b-' ) !== false && ! is_a( $grid, 'Iki_Asset_Grid' ) ) {

				$classes[] = 'iki-blog-s';//iki blog special

			} elseif ( isset( $extra_data['design'] ) ) {

				$classes[] = 'iki-g-d-' . $extra_data['design'];

				$has_alt = preg_match( '/(.+)(\-v[0-9]$)/', $extra_data['design'], $alt_version );

				if ( 1 == $has_alt ) {
					if ( isset( $alt_version[1] ) ) {
						$classes[] = 'iki-g-d-' . $alt_version[1];

					}
				}
			}

			if ( isset( $this->options['classes'] ) ) {
				$classes = array_merge( $classes, $this->options['classes'] );
			}

		}

		return $classes;
	}

	/**
	 * Get the grid instance
	 * @return Iki_Grid
	 */
	public function get_grid() {
		return $this->grid;
	}
}