<?php

add_filter( 'iki_toolkit_print_inline_css', '_filter_iki_toolkit_print_portfolio_single_css' );

/**
 * Print portfolio project
 *
 * @param $post_id
 */
function iki_toolkit_print_portfolio_project( $post_id ) {
	?>
	<div class="<?php echo iki_toolkit_project_wrap_class( $post_id, 'iki-project-wrap' ); ?>">
		<?php
		if ( ! post_password_required() ) {

			$project_layout = iki_toolkit_get_project_layout( $post_id );

			if ( 'bottom' == $project_layout['position'] ) {

				iki_toolkit_print_project_info( $post_id );
				iki_toolkit_print_project_asset( $post_id );

			} else {

				iki_toolkit_print_project_asset( $post_id );
				iki_toolkit_print_project_info( $post_id );
			}

		}
		?>
	</div>
	<?php
}

/**
 * Get project layout data
 *
 * @param $id int post ID
 *
 * @return array layout data
 */
function iki_toolkit_get_project_layout( $id ) {

	$r = array(
		'layout'   => 'horizontal',
		'position' => 'top'
	);

	$project_layout = iki_toolkit()->get_post_option( $id, 'project_layout', false );

	if ( $project_layout ) {
		$r['layout']   = $project_layout['chosen'];
		$project_data  = $project_layout[ $project_layout['chosen'] ];
		$r['position'] = $project_data['asset_position'];

		if ( 'vertical' == $r['layout'] ) {

			$r['asset_width'] = $project_data['asset_width'];

		}
	}

	return $r;
}

/**
 * Setup all required classes for project section, and fire filter to add additional classes
 *
 * @param string|int $post_id
 * @param string $class
 *
 * @return string
 */
function iki_toolkit_project_wrap_class( $post_id, $class = '' ) {

	$class = explode( ' ', $class );

	$project_layout_data = iki_toolkit_get_project_layout( $post_id );
	$layout              = $project_layout_data['layout'];
	$asset_position      = $project_layout_data['position'];

	$asset_data = iki_toolkit()->get_post_option( $post_id, 'project_content', false );
	if ( $asset_data ) {
		$class[] = 'iki-project-type-' . $asset_data['chosen_content'];
	}
	if ( post_password_required() ) {
		$layout         = 'horizontal';
		$asset_position = 'bottom';
	}
	$class[] = 'clearfix';
	$class[] = 'iki-project-layout-' . $layout;
	$class[] = 'iki-asset-' . $asset_position;

	$class = apply_filters( 'iki_project_wrapper_class', $class );

	return Iki_Toolkit_Utils::sanitize_html_class_array( $class );
}

if ( ! function_exists( 'iki_toolkit_print_project_info' ) ) {
	/**
	 * Print project info, basically print post content.
	 *
	 * @param string|int $post_id
	 */
	function iki_toolkit_print_project_info( $post_id ) {

		$content_location = iki_toolkit()->get_post_option( $post_id, 'post_content_location', 'above' );
		echo '<div class="iki-project-info">';
		if ( 'above' == $content_location ) {
			the_content();
		}
		echo '<div class="iki-project-data">';
		iki_toolkit_maybe_print_project_data_fields( $post_id );
		echo '</div>';
		if ( 'below' == $content_location ) {
			the_content();
		}
		echo '</div>';
	}
}
if ( ! function_exists( 'iki_toolkit_maybe_print_project_data_fields' ) ) {
	/**
	 * Print project data fields
	 *
	 * @param string|int $post_id
	 */
	function iki_toolkit_maybe_print_project_data_fields( $post_id ) {

		$client_info = iki_toolkit()->get_post_option( $post_id, 'client_info', false );
		if ( ! empty( $client_info ) ) {
			$client_info = trim( $client_info );
			iki_toolkit_icon_info_html(
				'user',
				apply_filters( 'iki_portfolio_project_client_info_title_text', __( 'Client', 'iki-toolkit' ) ),
				apply_filters( 'the_content', $client_info ) );
		}

		$skills = iki_toolkit()->get_post_option( $post_id, 'skills' );
		$skills = trim( $skills );


		$skills_content = '';
		$print_skills   = false;

		if ( ! empty( $skills ) ) {
			$print_skills   = true;
			$skills_content = apply_filters( 'the_content', $skills );
		}

		$remove_tags = iki_toolkit()->get_post_option( $post_id, 'remove_tags', 'disabled' );

		if ( 'disabled' == $remove_tags ) {
			$port_tags = Iki_Post_Utils::get_custom_term_links( $post_id,
				'iki_portfolio_tag',
				'<li class="iki-skill-tag">',
				'</li>' );

			if ( ! empty( $port_tags['terms'] ) ) {
				$print_skills   = true;
				$port_tags_html = sprintf( '<ul class="iki-port-list">%1$s</ul>', $port_tags['html'] );
				$skills_content .= $port_tags_html;
			}
		}

		if ( $print_skills ) {
			iki_toolkit_icon_info_html(
				'wrench',
				apply_filters( 'iki_portfolio_project_skills_title_text', __( 'Skills', 'iki-toolkit' ) ),
				$skills_content
			);
		}

		$project_url = iki_toolkit()->get_post_option( $post_id, 'project_url' );
		$project_url = trim( $project_url );
		if ( ! empty( $project_url ) ) {

			$project_url = esc_url( $project_url );
			$project_url = sprintf( '<a href="%1$s">%1$s</a>', $project_url );

			iki_toolkit_icon_info_html(
				'link',
				apply_filters( 'iki_portfolio_project_url_info_title_text', __( 'Project home', 'iki-toolkit' ) ),
				apply_filters( 'the_content', $project_url ) );
		}

		$remove_cateogires = iki_toolkit()->get_post_option( $post_id, 'remove_categories', 'disabled' );
		if ( 'disabled' == $remove_cateogires ) {

			$categories = Iki_Post_Utils::get_custom_term_links( $post_id,
				'iki_portfolio_cat',
				'<li class="iki-project-data-cat">', '</li>' );
			if ( ! empty( $categories['terms'] ) ) {
				$cat_num = count( $categories['terms'] );
				iki_toolkit_icon_info_html(
					'category',
					apply_filters( 'iki_portfolio_project_categories_title_text',
						_n( 'Category', 'Categories', $cat_num, 'iki-toolkit' )
					),
					sprintf( '<ul class="iki-port-list">%1$s</ul>', $categories['html'] )
				);
			}
		}


		$misc = iki_toolkit()->get_post_option( $post_id, 'misc_desc' );
		$misc = trim( $misc );
		if ( ! empty( $misc ) ) {
			echo '<div class="iki-vc-sec-wrap iki-vc-misc">';
			echo apply_filters( 'the_content', $misc );
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'iki_toolkit_print_project_asset' ) ) {

	/**
	 * Print chosen project asset (video , asset grid , feature image etc..)
	 *
	 * @param $id int post ID
	 */
	function iki_toolkit_print_project_asset( $id ) {

		$asset_data        = iki_toolkit()->get_post_option( $id, 'project_content', false );
		$password_required = post_password_required( $id );
		if ( $asset_data && ! $password_required ) {

			$asset_data                                           = iki_toolkit_parse_custom_content_data( $asset_data );
			$asset_data['custom_content']['content_width']        = 'full';
			$asset_data['custom_content']['content_custom_width'] = '';

			echo '<div class="iki-project-asset">';
			iki_toolkit_print_hero_section_custom_content( $asset_data );
			echo '</div>';
		}
	}
}

/**
 * Prints custom css required for custom assets width in case of vertical layout.
 *
 * @param $css
 *
 * @return array
 */
function _filter_iki_toolkit_print_portfolio_single_css( $css ) {

	global $post;
	$location_info = iki_toolkit()->get_location_info();
	//check if we are on single portfolio posts.
	if ( 'iki_portfolio' == $location_info['type'] && 'post' == $location_info['location'] ) {
		$layout            = iki_toolkit_get_project_layout( $post->ID );
		$password_required = post_password_required( $post->ID );
		if ( isset( $layout['asset_width'] ) && ! $password_required ) {

			$asset_width        = $layout['asset_width'];
			$project_info_width = 100 - (int) $asset_width;
			$css[]              = "@media (min-width: 768px){
				.iki-project-layout-vertical .iki-project-asset { width:{$asset_width}%;}
				.iki-project-layout-vertical .iki-project-info { width:{$project_info_width}%;}
				}";

		}
	}

	return $css;
}

if ( ! function_exists( 'iki_toolkit_setup_asset_grid' ) ) {

	/**
	 * Setup asset grid
	 *
	 * @param $data array Grid data.
	 */
	function iki_toolkit_setup_asset_grid( $data ) {

		$ajax_pagination = ( 'enabled' == $data['ajax_pagination'] );
		$use_ajax        = false;
		$assets_per_page = trim( $data['batch_image_load'] );

		$assets = Iki_Utils::extract_unyson_image_options_ids( $data['images'] );

		$total_assets   = count( $assets );
		$assets_to_load = $assets;

		$grid_options = array(

			'assets' => $assets,
			'total'  => $total_assets,

		);

		if ( $ajax_pagination ) {

			if ( ! empty( $assets_per_page ) && is_numeric( $assets_per_page ) ) {
				if ( count( $assets_to_load ) > $assets_per_page ) {

					$use_ajax                  = true;
					$assets_to_load            = array_slice( $assets, 0, $assets_per_page );
					$grid_options ['per_page'] = $assets_per_page;
				}
			}
		}

		$lightbox_design = isset( $data['lightbox_design'] ) ? $data['lightbox_design'] : 'symbol';

		/**@var Iki_Portfolio_Single_Module $location_module */
		$location_module = iki_theme()->get_location_module();
		/**@var Iki_Grid_Manager $grid_manager */
		$grid_manager = $location_module->get_grid_manager( 'project_grid' );
		$grid_manager->add_export_data( $grid_options );
		$grid_manager->add_custom_data( 'lightbox_design', $lightbox_design );
		$grid_manager->print_grid( $assets_to_load );

		if ( $use_ajax ) {
			iki_the_asset_grid_ajax_button();
		}
	}
}
