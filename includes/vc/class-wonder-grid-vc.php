<?php

/**
 * Class for creating wonder grid shortcode
 *
 */
class Iki_Wonder_Grid_VC {

	protected $base;
	protected $shortcode_name;
	private $atts;
	private $grid_data;

	private $font_sizes = array(
		'small'  => '12px',
		'medium' => '14px',
		'large'  => '14px'
	);

	private $media_queries = array(

		'small'  => '480px',
		'medium' => '600px',
		'large'  => '992px'
	);

	private static $id = 0;

	/**
	 * Iki_Wonder_Grid_VC constructor.
	 *
	 * @param $base string shortcode name
	 * @param $shortcode_name string shortcode name in admin
	 */
	public function __construct( $base, $shortcode_name ) {

		$this->base           = $base;
		$this->shortcode_name = $shortcode_name;

		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $base, array( $this, 'do_shortcode' ) );
	}


	/**
	 * Register WPBakery page builder backend shortcode options generator
	 */
	public function register_for_lean_map() {

		vc_lean_map( $this->base, array( $this, 'vc_backend_settings' ) );
	}

	/**
	 * Generate unique id for every grid that is printed
	 * @return int
	 */
	private static function generate_id() {

		self::$id ++;

		return self::$id;
	}


	/**
	 * Generate shortcode options
	 * @return array
	 */
	public function vc_backend_settings() {

		$new_vc_params = array();

		array_push( $new_vc_params,
			array(
				"type"        => "dropdown",
				"admin_label" => true,
				"heading"     => __( "Grid", 'iki-toolkit' ),
				"param_name"  => "grid_id",
				"value"       => Iki_Wonder_Grid_VC_Helper::get_wonder_grid_posts(),
			) );

		$query_settings = array(

			array(
				"type"       => "loop",
				"heading"    => __( "Posts query", 'iki-toolkit' ),
				"param_name" => "posts_query",
				'value'      => '',
				'settings'   => array(),
				'group'      => __( 'Post query', 'iki-toolkit' )

			),
			array(
				"type"       => "checkbox",
				"heading"    => __( "Remove sticky posts", 'iki-toolkit' ),
				"param_name" => "remove_sticky_posts",
				"value"      => array(
					__( 'yes', 'iki-toolkit' ) => '1',
				),
				'group'      => __( 'Post query', 'iki-toolkit' )
			),

		);

		$new_vc_params = array_merge( $new_vc_params, $query_settings );

		$post_type = array(
			"type"        => "dropdown",
			"admin_label" => true,
			"heading"     => __( "Post type", 'iki-toolkit' ),
			"param_name"  => "post_type",
			"value"       => array(
				__( 'Post', 'iki-toolkit' )        => 'post',
				__( 'Portfolio', 'iki-toolkit' )   => 'iki_portfolio',
				__( 'Team Member', 'iki-toolkit' ) => 'iki_team_member',
			),
			'group'       => __( 'Design', 'iki-toolkit' )
		);


		array_push( $new_vc_params, $post_type );

		$design_post = array(
			"type"        => "dropdown",
			"admin_label" => false,
			"heading"     => __( "Design", 'iki-toolkit' ),
			"param_name"  => "post_design",
			"value"       => Iki_Wonder_Grid_VC_Helper::get_grid_design( 'post' ),
			'description' => __( 'Please note that this field will not take effect if you choose any grid that starts with [Blog .....] ', 'iki-toolkit' ),
			'dependency'  => array( 'element' => "post_type", 'value' => 'post' ),
			'group'       => __( 'Design', 'iki-toolkit' )
		);

		array_push( $new_vc_params, $design_post );

		$design_portfolio = array(
			"type"        => "dropdown",
			"admin_label" => false,
			"heading"     => __( "Design", 'iki-toolkit' ),
			"param_name"  => "portfolio_design",
			"value"       => Iki_Wonder_Grid_VC_Helper::get_grid_design( 'iki_portfolio' ),
			'description' => __( 'Please note that this field will not take effect if you choose any grid that starts with [Blog .....] ', 'iki-toolkit' ),
			'dependency'  => array( 'element' => "post_type", 'value' => 'iki_portfolio' ),
			'group'       => __( 'Design', 'iki-toolkit' )
		);

		array_push( $new_vc_params, $design_portfolio );

		$design_team = array(
			"type"        => "dropdown",
			"admin_label" => false,
			"heading"     => __( "Design", 'iki-toolkit' ),
			"param_name"  => "team_member_design",
			"value"       => Iki_Wonder_Grid_VC_Helper::get_grid_design( 'iki_team_member' ),
			'description' => __( 'Please note that this field will not take effect if you choose any grid that starts with [Blog .....] ', 'iki-toolkit' ),
			'dependency'  => array( 'element' => "post_type", 'value' => 'iki_team_member' ),
			'group'       => __( 'Design', 'iki-toolkit' )
		);

		array_push( $new_vc_params, $design_team );

		array_push( $new_vc_params,
			array(
				"type"        => "textfield",
				"admin_label" => false,
				"heading"     => __( "Extra class name", 'iki-toolkit' ),
				"param_name"  => "html_class",
				"value"       => '',
				"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
			)
		);

		//font size group
		array_push( $new_vc_params,
			array(
				"type"        => "textfield",
				"admin_label" => true,
				"heading"     => __( "Small screen", 'iki-toolkit' ),
				"param_name"  => "font_size_small",
				"value"       => $this->font_sizes['small'],
				"description" => sprintf( __( 'Active as long as the minimum screen width is %1$s ', 'iki-toolkit' ), $this->media_queries['small'] ),
				'group'       => __( 'Font Size', 'iki-toolkit' )

			)
		);

		array_push( $new_vc_params,
			array(
				"type"        => "textfield",
				"admin_label" => true,
				"class"       => "",
				"heading"     => __( "Medium screen", 'iki-toolkit' ),
				"param_name"  => "font_size_medium",
				"value"       => $this->font_sizes['medium'],
				"description" => sprintf( __( 'Active as long as the minimum screen width is %1$s ', 'iki-toolkit' ), $this->media_queries['medium'] ),
				'group'       => __( 'Font Size', 'iki-toolkit' )

			)
		);

		array_push( $new_vc_params,
			array(
				"type"        => "textfield",
				"heading"     => __( "Large screen", 'iki-toolkit' ),
				"param_name"  => "font_size_large",
				"admin_label" => true,
				"value"       => $this->font_sizes['large'],
				"description" => sprintf( __( 'Active as long as the minimum screen width is %1$s ', 'iki-toolkit' ), $this->media_queries['large'] ),
				'group'       => __( 'Font Size', 'iki-toolkit' )

			)
		);

		return array(
			"name"     => $this->shortcode_name,
			"base"     => $this->base,
			"class"    => "iki-vc-grid",
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/wonder-grid.png',
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			"params"   => $new_vc_params
		);

	}

	/**
	 * Generate custom font size media query
	 *
	 * @param $media_size
	 * @param $font_size
	 * @param $grid_id
	 *
	 * @return string
	 */
	private static function get_custom_font_size_css( $media_size, $font_size, $grid_id ) {
		return sprintf( '@media all and (min-width:%1$s) {
				.iki-grid-vc-%3$s .iki-grid-thumb {
					font-size: %2$s!important;
					} }'
			, $media_size
			, $font_size
			, $grid_id );
	}

	/**
	 * Print the shortcode
	 *
	 * @param $atts array shortcode attributes
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts ) {

		if ( ! is_array( $atts ) ||
		     ! isset( $atts['grid_id'] ) ||
		     ! isset( $atts['posts_query'] )
		) {
			return false;
		}

		$atts['post_type'] = isset( $atts['post_type'] ) ? $atts['post_type'] : 'post';

		$atts['post_design']        = isset( $atts['post_design'] ) ? $atts['post_design'] : 'default';
		$atts['portfolio_design']   = isset( $atts['portfolio_design'] ) ? $atts['portfolio_design'] : 'port';
		$atts['team_member_design'] = isset( $atts['team_member_design'] ) ? $atts['team_member_design'] : 'team';

		if ( ! isset( $atts['html_class'] ) ) {
			$atts['html_class'] = '';
		}
		$custom_font_size = false;

		if ( isset( $atts['font_size_small'] ) ) {
			$custom_font_size = true;
		} else {
			$atts['font_size_small'] = $this->font_sizes['small'];
		}

		if ( isset( $atts['font_size_medium'] ) ) {
			$custom_font_size = true;
		} else {

			$atts['font_size_medium'] = $this->font_sizes['medium'];
		}

		if ( isset( $atts['font_size_large'] ) ) {
			$custom_font_size = true;
		} else {
			$atts['font_size_large'] = $this->font_sizes['large'];
		}

		$font_css   = '';
		$dynamic_id = self::generate_id();
		if ( $custom_font_size ) {
			//print new font sizes
			$font_css = sprintf( '<style>%1$s %2$s %3$s</style>',
				self::get_custom_font_size_css( $this->media_queries['small'], $atts['font_size_small'], $dynamic_id ),
				self::get_custom_font_size_css( $this->media_queries['medium'], $atts['font_size_medium'], $dynamic_id ),
				self::get_custom_font_size_css( $this->media_queries['large'], $atts['font_size_large'], $dynamic_id )
			);
			$font_css = preg_replace( '/\s+/', ' ', $font_css );
		}


		$sticky_posts_ids = isset( $atts['remove_sticky_posts'] ) ? get_option( "sticky_posts" ) : null;

		$this->atts = $atts;

		if ( ! empty( $atts['posts_query'] ) ) {

			$vc_query = '';

			if ( function_exists( 'vc_build_loop_query' ) ) {
				$vc_query = vc_build_loop_query( $atts['posts_query'], $sticky_posts_ids );
			}

			if ( ! is_array( $vc_query ) ) {
				return '';
			}
		}

		$grid_data              = Iki_Toolkit_Utils::get_grid_options( $atts['grid_id'], 'vc' );
		$grid_data['grid_rows'] = $this->parse_grid_rows( $grid_data['grid_rows'], $grid_data['condensed'] );
		$this->grid_data        = $grid_data;

		$GLOBALS['iki_toolkit']['flags']['printing_grid'] = true;

		add_filter( 'iki_grid_cell_class', array( $this, 'grid_thumb_class' ), 10, 3 );
		add_filter( 'iki_grid_class', array( $this, 'grid_wrapper_class' ), 10, 2 );
		add_filter( 'iki_grid_setup_custom_data', array( $this, 'grid_extra_data_cb' ), 1, 2 );


		$fillGrid = ( isset( $grid_data['fill_grid'] ) ) ? $grid_data['fill_grid'] : false;

		$grid = new Iki_Post_Grid(
			$this->grid_data['grid_rows'],
			null,//$this->default_row,
			$this->atts['grid_id'],//grid id
			true,//use grid wrapper
			$fillGrid,
			'vc'//grid location
		);
		$grid->set_html_id( 'vc-' . $dynamic_id );

		if ( isset( $atts['terms'] ) ) {

			$query['tax_query'] = array(
				array(
					'taxonomy' => $this->term,
					'field'    => 'slug',
					'terms'    => explode( ',', $atts['terms'] )
				)
			);
		}


		$aggregate = $vc_query[1];
		ob_start();
		echo $font_css;
		$grid->print_grid( $aggregate, null, true, 0, array( 'iki-grid-vc-' . $dynamic_id, $atts['html_class'] ) );
		$result = ob_get_contents();
		ob_end_clean();

		$GLOBALS['iki_toolkit']['flags']['printing_grid'] = false;
		remove_filter( 'iki_grid_cell_class', array( $this, 'grid_thumb_class' ) );
		remove_filter( 'iki_grid_class', array( $this, 'grid_wrapper_class' ) );
		remove_filter( 'iki_grid_setup_custom_data', array( $this, 'grid_extra_data_cb' ) );

		return $result;
	}

	/**
	 * Setup design for the grid
	 *
	 * @param $data
	 * @param Iki_Grid $grid
	 *
	 * @return mixed
	 */
	public function grid_extra_data_cb( $data, Iki_Grid $grid ) {

		if ( 'vc' == $grid->get_location() ) {


			$type   = $data['post_type'] = $this->atts['post_type'];
			$design = 'default';
			switch ( $type ) {

				case 'post':
					$design = $this->atts['post_design'];
					break;
				case 'iki_portfolio':
					$design = $this->atts['portfolio_design'];
					break;
				case 'iki_team_member':
					$design = $this->atts['team_member_design'];
					break;
			}

			$data['design'] = $design;

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
			$classes[] = 'iki-location-vc iki-grid-post-vc';

			$blog_grid_supported = ( 'post' == $this->atts['post_type'] );

			//check if custom blog grid.
			if ( $blog_grid_supported && strpos( $extra_data['id'], 'iki_b-' ) !== false ) {

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
		}

		return $classes;
	}
}


/**
 * Simple class helper for dealing with the grid shortcode
 */
class Iki_Wonder_Grid_VC_Helper {

	/**
	 * Get available wonder grids
	 *
	 * @param string $type for what post type
	 *
	 * @return array
	 */
	public static function get_wonder_grid_posts( $type = 'post' ) {

		return array_flip( Iki_Toolkit_Admin_Grid_Options::get_instance()->get_wonder_grid_posts() );
	}


	/**
	 * Get grid design
	 *
	 * @param $type
	 *
	 * @return mixed|void
	 */
	public static function get_grid_design( $type ) {


		$r = apply_filters( 'iki_toolkit_vc_grid_design', array(), $type );

		return $r;
	}

}

/* WPBakery visual builder fix
   If we are in front end always instantiate these class instances
*/
if ( isset( $_POST['action'] ) && 'vc_edit_form' == $_POST['action'] ) {
	new Iki_Wonder_Grid_VC(
		'iki_wonder_grid_vc',
		__( 'Wonder Grid', 'iki-toolkit' )
	);
}
