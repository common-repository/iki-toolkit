<?php

/**
 * Class for creating image grid shortcode
 */
class Iki_Image_Grid_VC {


	protected $base = 'iki_image_grid_vc';
	private static $id = 0;

	protected $grid_data;
	protected $atts;

	/**
	 * Iki_Image_Grid_VC constructor.
	 */
	public function __construct() {

		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );
	}

	/**
	 * Generate unique id for every shortcode that is printed
	 * @return int
	 */
	private static function generate_id() {

		self::$id ++;

		return self::$id;
	}

	/**
	 * Register WPBakery page builder backend shortcode options generator
	 */
	public function register_for_lean_map() {
		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Create backend options
	 * @return array
	 */
	public function vc_backend_settings() {

		$general = array(

			array(
				'type'        => 'attach_images',
				'heading'     => __( 'Images', 'iki-toolkit' ),
				'param_name'  => 'images',
				'value'       => '',
				'description' => __( 'Select images from media library', 'iki-toolkit' ),
				'dependency'  => array(
					'element' => 'source',
					'value'   => 'media_library',
				),
			),
		);

		$image_click_opt = array(
			'heading'    => esc_html__( 'Image click', 'iki-toolkit' ),
			'type'       => 'dropdown',
			'param_name' => 'image_click',
			"value"      => array(
				__( 'None', 'iki-toolkit' )                => 'none',
				__( 'Link to large image', 'iki-toolkit' ) => 'large_image',
				__( 'Custom link', 'iki-toolkit' )         => 'custom_link',
			),
		);

		if ( class_exists( 'Iki_Theme' ) ) {
			$image_click_opt['value'][ __( 'Open Lightbox', 'iki-toolkit' ) ] = 'lightbox';
		}

		$general[] = $image_click_opt;

		$general[] = array(
			'type'        => 'exploded_textarea',
			'param_name'  => 'image_links',
			'admin_label' => true,
			'value'       => '',
			'heading'     => esc_html__( 'Custom links', 'iki-toolkit' ),
			'description' => esc_html__( 'Enter links for each image (Note: divide links with linebreaks (Enter)).', 'iki-toolkit' ),
			'dependency'  => array( 'element' => 'image_click', 'value' => 'custom_link' ),
		);

		// grid
		$general[] = array(
			"type"        => "dropdown",
			"admin_label" => true,
			"heading"     => __( "Grid", 'iki-toolkit' ),
			"param_name"  => "grid_id",
			"value"       => Iki_Wonder_Grid_VC_Helper::get_wonder_grid_posts( '' ),
		);

		$general[] = array(
			"type"        => "textfield",
			"admin_label" => true,
			"heading"     => __( "Extra class name", 'iki-toolkit' ),
			"param_name"  => "html_class",
			"value"       => '',
			"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
		);


		$params = array_merge( $general );

		return array(
			"name"     => __( 'Image Grid', 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/image-grid.png',
			"params"   => $params
		);

	}


	/**
	 * Print the shortcode
	 *
	 * @param $atts
	 *
	 * @return string
	 */
	public function do_shortcode( $atts ) {

		$result = '';
		$atts   = array_merge( $this->set_defaults(), $atts );

		$dynamic_id = self::generate_id();

		if ( ! empty( $atts['images'] ) ) {

			$aggregate = explode( ',', $atts['images'] );

			if ( ! empty( $atts['image_links'] ) ) {

				$atts['image_links'] = explode( ',', $atts['image_links'] );
			}

			$this->atts       = $atts;
			$this->atts['id'] = $dynamic_id;

			$grid_data              = Iki_Toolkit_Utils::get_grid_options( $atts['grid_id'], 'vc' );
			$grid_data['grid_rows'] = $this->parse_grid_rows( $grid_data['grid_rows'], $grid_data['condensed'] );
			$this->grid_data        = $grid_data;

			$GLOBALS['iki_toolkit']['flags']['printing_grid'] = true;
			add_filter( 'iki_grid_cell_class', array( $this, 'grid_thumb_class' ), 10, 3 );
			add_filter( 'iki_grid_class', array( $this, 'grid_wrapper_class' ), 10, 2 );
			add_filter( 'iki_grid_setup_custom_data', array( $this, 'grid_extra_data_cb' ), 1, 2 );


			$fillGrid = ( isset( $grid_data['fill_grid'] ) ) ? $grid_data['fill_grid'] : false;

			$grid = new Iki_Asset_Grid(
				$this->grid_data['grid_rows'],
				null,//$this->default_row,
				$atts['grid_id'],//grid id
				true,//use grid wrapper
				$fillGrid,
				'vc'//grid location
			);
			$grid->set_html_id( 'vc-img-' . $dynamic_id );

			ob_start();

			$grid->print_grid( $aggregate, null, true, 0, array(
				'iki-grid-img-vc-' . $dynamic_id,
				'iki-grid-img-vc',
				$atts['html_class']
			) );
			$result = ob_get_contents();
			ob_end_clean();

			$GLOBALS['iki_toolkit']['flags']['printing_grid'] = false;

			remove_filter( 'iki_grid_cell_class', array( $this, 'grid_thumb_class' ) );
			remove_filter( 'iki_grid_class', array( $this, 'grid_wrapper_class' ) );
			remove_filter( 'iki_grid_setup_custom_data', array( $this, 'grid_extra_data_cb' ) );

			return $result;

		}


		return $result;
	}

	/**
	 * Always force condensed on mixed rows when the grid is condensed itself.
	 *
	 * @param $rows
	 * @param $condensed
	 *
	 * @return array
	 */
	protected function parse_grid_rows( $rows, $condensed ) {

		$a = array();
		foreach ( $rows as &$row ) {

			//always force condensed on mixed rows when the grid is condensed itself.
			if ( $row['type'] == 'mixed' && $condensed ) {
				$row['condensed'] = true;
			}
			array_push( $a, new Iki_Grid_Row_Data( $row ) );
		}

		return $a;
	}

	/** Setup design for the grid
	 *
	 * @param $data
	 * @param Iki_Grid $grid
	 *
	 * @return mixed
	 */

	public function grid_extra_data_cb( $data, Iki_Grid $grid ) {

		if ( 'vc' == $grid->get_location() ) {

			$data['design']      = $this->atts['design'];
			$data['image_click'] = $this->atts['image_click'];
			if ( is_array( $this->atts['image_links'] ) ) {
				$data['image_links'] = $this->atts['image_links'];
			}
		}

		return $data;
	}

	/**
	 * Setup grid thumb classes
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
	 * Setup grid wrapper classes
	 *
	 * @param $classes
	 * @param Iki_Grid $grid
	 *
	 * @return array
	 */
	public function grid_wrapper_class( $classes, Iki_Grid $grid ) {

		if ( 'vc' == $grid->get_location() ) { //respond only to your grid

			$extra_data = $grid->get_custom_data();

			$classes[] = ( $this->grid_data['condensed'] ) ? 'iki-grid-condensed' : 'iki-grid-spaced';
			$classes[] = 'iki-location-vc iki-grid-image-vc';
			if ( isset( $extra_data['image_click'] ) ) {

				$classes[] = 'iki-grid-vc-click-' . $extra_data['image_click'];
			}

		}

		return $classes;
	}

	/**
	 * Get default shortcode data
	 * @return array
	 */
	public function set_defaults() {
		return array(
			'images'      => array(),
			'html_class'  => '',
			'grid_id'     => 'portrait_4',
			'design'      => 'default',
			'image_click' => 'none',
			'image_links' => ''
		);
	}
}

new Iki_Image_Grid_VC();
