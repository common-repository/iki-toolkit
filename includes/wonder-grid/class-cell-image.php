<?php

/**
 * Class that produces output that holds the image for grid cell
 * */

class Iki_Grid_Cell_Image {

	protected $content;
	protected $has_image = false;
	protected $img_src = '';
	protected $img_id = '';
	protected $image_size = '';

	protected $lazy_load;
	protected $canvas_img_size = 'thumbnail';

	public function __construct( $img_id, $lazy_load = true ) {
		$this->lazy_load = $lazy_load;
		$this->get_image( $img_id );
	}

	/**
	 * Get image by id
	 *
	 * @param $img_id
	 */
	protected function get_image( $img_id ) {

		$img_src         = '';
		$thumbnail_image = '';
		$classes[]       = 'iki-cell-image iki-asset-holder';

		$classes = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );

		$img_id       = apply_filters( 'iki_grid_image_id', $img_id );
		$this->img_id = $img_id;

		$image_size       = apply_filters( 'iki_grid_image_size', $this->calculate_image_size() );
		$this->image_size = $image_size;

		$this->canvas_img_size = apply_filters( 'iki_grid_cell_canvas_size', $this->canvas_img_size );

		if ( ! empty( $img_id ) ) {

			$img_src       = wp_get_attachment_image_src( $img_id, $image_size );
			$img_src       = apply_filters( 'iki_grid_image_src', ( $img_src ) ? $img_src[0] : '', $image_size );
			$this->img_src = $img_src;

			if ( $this->lazy_load && ! empty( $img_src ) ) {

				$thumbnail_image = $this->iki_canvas_thumb( $img_id, $this->canvas_img_size, $img_src );
				$img_src         = '';

			} else {

				if ( ! empty( $img_src ) ) {

					$img_src = sprintf( 'style="background-image: url(\'%1$s\');"', $img_src );

				}
			}
		}

		$grid_image = sprintf( '<div class="%1$s" %2$s >%3$s</div>', $classes, $img_src, $thumbnail_image );

		$this->has_image = ! empty( $this->img_src );
		$this->content   = $grid_image;
	}


	/**
	 * Get the content
	 * @return mixed
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * Echo the content
	 */
	public function the_content() {
		echo $this->content;
	}

	/**
	 * Check if cell has image
	 * @return bool
	 */
	public function has_image() {
		return $this->has_image;
	}

	/**
	 * Get image src
	 * @return string
	 */
	public function get_img_src() {
		return $this->img_src;
	}

	/**
	 * Get image id
	 * @return string
	 */
	public function get_img_id() {
		return $this->img_id;
	}

	/**
	 * Get image size
	 * @return string
	 */
	public function get_image_size() {
		return $this->image_size;
	}

	/**
	 * Get the image size for the current cell in currently active grid
	 * (grid that is currently printing)
	 *
	 * @param Iki_Grid|null $grid
	 *
	 * @return string
	 */
	protected function calculate_image_size( Iki_Grid $grid = null ) {

		/**@var Iki_Grid $grid */
		$grid = ( isset( $grid ) ) ? $grid : Iki_Grids::get_instance()->get_active_grid();
		/**@var Iki_Grid_Cell $current_cell */
		$current_cell = $grid->get_current_cell();

		$asset_orientation = $current_cell->get_orientation();

		if ( $current_cell->get_row_cells() == 1 && $asset_orientation == 'landscape' ) {
			$image_size = 'grid_2_landscape_stripe';

		} else {
			$asset_size = 2;
			$image_size = 'grid_' . $asset_size . '_' . $asset_orientation;
		}

		return $image_size;
	}

	/**
	 * Create image that will be transformed in html5 canvas thumb for the grid cell.
	 *
	 * @param $img_id
	 * @param string $size
	 * @param string $image_src
	 *
	 * @return bool|string
	 */
	protected function iki_canvas_thumb( $img_id, $size = 'thumbnail', $image_src = '' ) {
		$thumbnail_src = wp_get_attachment_image_src( $img_id, $size );

		if ( $thumbnail_src ) {

			$thumbnail_image = sprintf( '<img class="iki-canvas-thumb iki-blur-holder" alt="" data-iki-img-src="%2$s" src="%1$s">',
				$thumbnail_src[0],
				$image_src );

			return $thumbnail_image;
		}

		return '';
	}
}
