<?php

/**
 * Class that handles grid backend application assets and saving data
 */
class Iki_Wonder_Grid_Admin {

	protected $post_slug = 'iki_wonder_grid';
	const MAX_NUM_VISIBLE = 8;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'on_admin_init' ), 1000 );
		add_action( 'save_post_' . $this->post_slug, array( $this, 'save_post_meta' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {

		$screen = get_current_screen();
		if ( 'iki_wonder_grid' == $screen->id ) {

			global $post;
			require( plugin_dir_path( __FILE__ ) . '../mixed-row-data.php' );
			$json_row_structure = iki_mixed_row_data_structure();
			$json_row_structure = json_encode( $json_row_structure );
			wp_localize_script( 'jquery', 'ikiAvailableMixedRows', $json_row_structure );

			$json_grid_data = ( get_post_meta( $post->ID, 'iki_grid_data' ) );
			wp_localize_script( 'jquery', 'ikiGridMetaData', $json_grid_data );

			wp_enqueue_script( 'iki-wonder-grid-admin', IKI_TOOLKIT_ROOT_URL . 'js/admin/wondergrid-app.min.js',
				array(
					'jquery',
					'backbone'
				),
				false,
				true );
			wp_enqueue_style( 'iki-wonder-grid-admin', IKI_TOOLKIT_ROOT_URL . 'css/admin/admin-wonder-grid.min.css' );
		}
	}

	/**
	 * Respond to admin_init hook
	 */
	public function on_admin_init() {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
	}


	/** Save post data
	 *
	 * @param $post_id
	 * @param $post
	 * @param $update
	 */
	public function save_post_meta( $post_id, $post, $update ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['meta_box_nonce'], 'iki_meta_box_nonce' ) ) {
			return;
		}


		if ( isset( $_REQUEST['iki_grid_data'] ) && ! empty( $_REQUEST['iki_grid_data'] ) ) {

			//iki_grid_data should be json.
			$valid_json = json_decode( wp_unslash( $_REQUEST['iki_grid_data'] ), true );
			if ( $valid_json ) {
				$grid_data = $_REQUEST['iki_grid_data'];
				update_post_meta( $post_id, 'iki_grid_data', $grid_data );
			}

		}

		if ( isset( $_REQUEST['iki_grid_classes'] ) ) {
			$classes = sanitize_text_field( $_REQUEST['iki_grid_classes'] );
			update_post_meta( $post_id, 'iki_grid_classes', $classes );
		}


		$fill_grid = 0;

		if ( isset( $_REQUEST['iki_fill_grid'] ) ) {
			$fill_grid = 1;
		}
		update_post_meta( $post_id, 'iki_fill_grid', $fill_grid );

		$grid_condensed = 0;
		if ( isset( $_REQUEST['iki_grid_condensed'] ) ) {
			$grid_condensed = 1;
		}
		update_post_meta( $post_id, 'iki_grid_condensed', $grid_condensed );
	}

	/**
	 * Add metaboxes on the page
	 */
	public function add_metaboxes() {


		add_meta_box( "demo-meta-box",
			__( 'Grid Construction', 'iki-toolkit' ),
			array(
				$this,
				"tab_metabox_markup"
			),
			"iki_wonder_grid",
			"normal",
			"high",
			null );

		add_meta_box( "grid-options",
			__( 'Grid Options', 'iki-toolkit' ),
			array(
				$this,
				"grid_options_metabox"
			),
			"iki_wonder_grid",
			"side",
			"default" );

	}

	/**
	 * Grid options metabox markup
	 */
	public function grid_options_metabox() {
		global $post;
		?>
		<div class="iki-post-options iki-admin-column iki-pos-sidebar iki-admin-column-right">
			<?php $grid_condensed = get_post_meta( $post->ID, 'iki_grid_condensed', true ); ?>
			<div class="iki-ui-input">
				<label for="iki_grid_condensed">
					<input type="checkbox" id="iki_grid_condensed"
						   name="iki_grid_condensed" <?php esc_attr_e( checked( $grid_condensed, 1 ) ) ?>>
					<?php _e( 'No gaps between cells', 'iki-toolkit' ); ?>
				</label>

				<div>
					<small><?php _e( 'If checked, there will be no gaps between cells in the grid.', 'iki-toolkit' ); ?></small>
				</div>
			</div>

			<div class="iki-ui-input">
				<label for="iki_grid_classes">
					<?php _e( 'Grid classes', 'iki-toolkit' ); ?>
					<?php
					$extra_classes = get_post_meta( $post->ID, 'iki_grid_classes', true );
					$extra_classes = esc_attr( $extra_classes );
					?>
					<input type="text" name="iki_grid_classes" value="<?php echo $extra_classes ?> "
						   id="iki_grid_classes">
				</label>
				<small><?php _e( 'Extra classes to be added to grid (use space for separation)', 'iki-toolkit' ); ?></small>
			</div>
			<div class="iki-ui-input">
				<?php $fill_grid = get_post_meta( $post->ID, 'iki_fill_grid', true ); ?>
				<label for="iki_fill_grid">
					<input type="checkbox" id="iki_fill_grid"
						   name="iki_fill_grid" <?php esc_attr_e( checked( $fill_grid, 1 ) ) ?>>
					<?php _e( 'Fill Grid', 'iki-toolkit' ); ?>
				</label>

				<div>
					<small><?php _e( 'If there is not enough posts or assets to fill all the columns (cells) in the last row, grid will try to modify columns in the last row, so there are no empty columns at the end.', 'iki-toolkit' ); ?></small>
				</div>
				<div>
					<small><?php _e( 'For example, if last row has 4 columns but 2 columns are left empty, grid will modify that row to have only two columns, so it doesn\'t appear empty.', 'iki-toolkit' ); ?></small>
					<br>
					<small><?php _e( 'Please note that this only works if the last row is not a "Mixed row".', 'iki-toolkit' ); ?></small>
				</div>

			</div>
		</div>
		<?php

	}

	/**
	 * Metabox html
	 *
	 * @param $object
	 */
	public function tab_metabox_markup( $object ) {

		global $post;

		wp_nonce_field( 'iki_meta_box_nonce', 'meta_box_nonce' );
		?>
		<div class="iki-metabox-wrap">

			<div class=" iki-row-ui iki-grid-info">
				<p><?php esc_html_e( 'Total cells in grid : ', 'iki-toolkit' ); ?><span id="iki-total-cells"></span></p>
			</div>
			<div id="iki-grid-wrapper" class="iki-grid-wrapper">
				<div class="iki-insert-new-ui-wrap">
					<div class="iki-row-insert-ui">
						<p><?php esc_html_e( 'Insert new', 'iki-toolkit' ); ?>
							<button type="button"
									class="iki-new-row-btn iki-new-classic button-secondary"><?php _e( 'Classic Row', 'iki-toolkit' ) ?></button>
						</p>
						<p>
							<?php esc_html_e( 'or insert new', 'iki-toolkit' ) ?>
							<button type="button"
									class="iki-new-row-btn iki-new-mixed button-secondary"><?php _e( 'Mixed Row', 'iki-toolkit' ) ?></button>
						</p>

					</div>
				</div>
			</div>
		</div>
		<!--.iki-metabox-wrap-->
		<script id="iki-classic-row-tpl" type="text/template">
			<div class="iki-row-ui">
				<a href="#" title="<?php esc_html_e( 'Options for this type of row', 'iki-toolkit' ); ?>"
				   class="button-secondary iki-options iki-icon-docs"><?php _e( 'Options', 'iki-toolkit' ); ?></a>
				<a href="#" title="<?php esc_html_e( 'Duplicate row', 'iki-toolkit' ); ?>"
				   class=" button-secondary iki-duplicate iki-icon-docs"><?php _e( 'Duplicate', 'iki-toolkit' ); ?></a>
				<a href="#" title="<?php esc_html_e( 'Remove row', 'iki-toolkit' ); ?>"
				   class=" button-secondary iki-remove iki-icon-trash-empty"><?php esc_html_e( 'Remove', 'iki-toolkit' ); ?></a>

				<div class="iki-classic-row-ui">
					<p><?php esc_html_e( 'Orientation :', 'iki-toolkit' ); ?>
						<button type="button"
								value="portrait"
								class="iki-orientation-btn iki-o-portrait button-secondary"><?php _e( 'Portrait', 'iki-toolkit' ) ?></button>
						<button type="button"
								value="landscape"
								class="iki-orientation-btn iki-o-landscape button-secondary"><?php _e( 'Landscape', 'iki-toolkit' ) ?></button>
						<button type="button"
								value="square"
								class="iki-orientation-btn iki-o-square button-secondary"><?php _e( 'Square', 'iki-toolkit' ) ?></button>
					</p>
					<p><?php esc_html_e( 'Columns :', 'iki-toolkit' ); ?>
						<button type="button" value="1" class="iki-cell-btn button-secondary">1</button>
						<button type="button" value="2" class="iki-cell-btn button-secondary">2</button>
						<button type="button" value="3" class="iki-cell-btn button-secondary">3</button>
						<button type="button" value="4" class="iki-cell-btn button-secondary">4</button>
					</p>
					<div class="bottom-ui-wrap">
						<p>
							<button type="button"
									class="iki-close-options button-secondary"><?php _e( 'Close Panel', 'iki-toolkit' ); ?></button>
						</p>
					</div>
				</div>
			</div>

			<div class="iki-grid-row iki-row-type-classic iki-row-orientation-square iki-row-cells-1 ">
				<div class="iki-grid-thumb">
					<div class="iki-cell"></div>
				</div>
			</div>
		</script>
		<script id="iki-mixed-row-tpl" type="text/template">
			<div class="iki-row-ui">
				<a href="#" title="<?php esc_html_e( 'Options for this type of row', 'iki-toolkit' ); ?>"
				   class="button-secondary iki-options iki-icon-docs"><?php _e( 'Options', 'iki-toolkit' ); ?></a>
				<a href="#" title="<?php esc_html_e( 'Duplicate row', 'iki-toolkit' ); ?>"
				   class=" button-secondary iki-duplicate iki-icon-docs"><?php _e( 'Duplicate', 'iki-toolkit' ); ?></a>
				<a href="#" title="<?php esc_html_e( 'Remove row', 'iki-toolkit' ); ?>"
				   class=" button-secondary iki-remove iki-icon-trash-empty"><?php _e( 'Remove', 'iki-toolkit' ); ?></a>

				<div class="iki-classic-row-ui">
					<p><?php esc_html_e( 'Available Mixed Rows', 'iki-toolkit' ); ?></p>
					<ul class="mixed-row-btn-wrap">
						<li data-iki-row="mixed-1" class="mixed-row-btn"><img
								src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/mixed-1.png'; ?>"
								alt="mixed row interpation"/></li>
						<li data-iki-row="mixed-1-reverse" class="mixed-row-btn"><img
								src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/mixed-1-reverse.png'; ?>"
								alt="mixed row interpation"/></li>
						<li data-iki-row="mixed-2" class="mixed-row-btn"><img
								src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/mixed-2.png'; ?>"
								alt="mixed row interpation"/></li>
						<li data-iki-row="mixed-2-reverse" class="mixed-row-btn"><img
								src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/mixed-2-reverse.png'; ?>"
								alt="mixed row interpation"/></li>
						<li data-iki-row="mixed-3" class="mixed-row-btn"><img
								src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/mixed-3.png'; ?>"
								alt="mixed row interpation"/></li>

					</ul>
					<div class="bottom-ui-wrap">
						<p>
							<button type="button"
									class="iki-close-options button-secondary"><?php esc_html_e( 'Close Panel', 'iki-toolkit' ); ?></button>
						</p>
					</div>
				</div>
			</div>

			<div class="iki-grid-row iki-row-type-mixed">
				<img class="iki-current-row" src="" alt=""/>
			</div>
		</script>
		<input type="hidden" name="iki_grid_data" id="iki_grid_data">
		<?php

	}
}


new Iki_Wonder_Grid_Admin();