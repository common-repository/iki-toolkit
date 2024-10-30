<?php

add_filter( 'iki_dynamic_css_compile', 'iki_generate_fs_panels_css', 20 );
add_filter( 'iki_dynamic_css_get_mixin_files', 'iki_toolkit_dynamic_css_get_mixins' );
add_filter( 'iki_dynamic_css_get_variable_files', 'iki_toolkit_get_sass_variables' );

/**
 * Generate css for full screen panels
 *
 * @param $css
 *
 * @return bool|string
 * @throws Exception
 */
function iki_generate_fs_panels_css( $css ) {

	if ( ! defined( 'FW' ) ) {
		return false;
	}
	$panel_css      = '';
	$enabled_panels = array();

	/**
	 * All panel data is generated via loop.
	 * And all sass variables have are the same , so we compile each panel individually.
	 */

	for ( $i = 1; $i <= 3; $i ++ ) {

		$index = ( 3 == $i ) ? 'search' : $i;

		$panel_key_prefix = 'fs_panel_' . $index;
		$panel_enabled    = iki_toolkit()->get_customizer_option( $panel_key_prefix . '_enabled', 'disabled' );

		$custom_colors = iki_toolkit()->get_customizer_option( 'fs_panel_' . $index . '_colors_enabled', 'disabled' );
		$custom_colors = Iki_Toolkit_Utils::string_to_boolean( $custom_colors );

		if ( 'enabled' == $panel_enabled && $custom_colors ) {

			$extracted_vars = Iki_Toolkit_Utils::array_extract_part_by_key( Iki_Dynamic_SASS::get_theme_sass_variables_as_array(), '$fs_panel_' . $index );
			$extracted_vars = Iki_Toolkit_Utils::array_modify_keys( $extracted_vars, '_' . $index . '_', '_' );

			$panel_data = array(
				'index'  => $index,
				'vars'   => $extracted_vars,
				'string' => Iki_Toolkit_Utils::array_to_sass_string( $extracted_vars )
			);

			$panel_data['string'] .= PHP_EOL . '$panelId:' . ( $index ) . ';';
			$panel_data['string'] .= '$panel_custom_colors:' . json_encode( $custom_colors ) . ';' . PHP_EOL;

			if ( 'search' == $index ) {
				$panel_data['string'] .= '$panel-el-search:true;' . PHP_EOL;
			}

			$url = iki_toolkit()->get_customizer_option( 'fs_panel_' . $index . '_url_bg', false );

			if ( $url ) {

				$url = Iki_Toolkit_Utils::construct_css_background_url( array( '$fs_panel_url_bg' => $url['attachment_id'] ) );

				$panel_data['string'] .= $url . PHP_EOL;
			}

			$enabled_panels[] = $panel_data;
		}
	}

	//if we have some panels enabled
	if ( ! empty( $enabled_panels ) ) {

		//panel sass file
		$fs_panel_sass_file = Iki_Sass_Compiler::get_sass_file_full_path( IKI_TOOLKIT_ROOT . 'sass/dynamic/_fs-panels.scss' );

		if ( $fs_panel_sass_file ) {


			foreach ( $enabled_panels as $index => $value ) {

				$panel_sass_string = $enabled_panels[ $index ]['string'];

				$fs_panels_sass = Iki_Dynamic_SASS::get_mixins( 'fs_panels' ) . PHP_EOL .
				                  Iki_Dynamic_SASS::get_variables( 'fs_panels' ) . PHP_EOL .
				                  $panel_sass_string . PHP_EOL .
				                  $fs_panel_sass_file . PHP_EOL;

				$compiled_panel_sass = Iki_Sass_Compiler::compile( $fs_panels_sass );
				$compiled_panel_sass .= apply_filters( 'iki_toolkit_fs_panel_compile_css', '', $value['index'] );

				if ( Iki_Dynamic_SASS::validate_css( $compiled_panel_sass ) ) {
					$panel_css .= $compiled_panel_sass . PHP_EOL;

				}
			}
		}
		$css .= $panel_css;
	}

	return $css;
}


/**
 * Get sass files that contain mixins
 *
 * @param $mixins
 *
 * @return string
 */
function iki_toolkit_dynamic_css_get_mixins( $mixins ) {

	if ( current_theme_supports( 'iki-toolkit-fs-panels' ) ) {

		$r = Iki_Sass_Compiler::get_sass_file_full_path( IKI_TOOLKIT_ROOT . 'sass/dynamic/_mixins.scss' );
		if ( $r ) {
			$mixins['fs_panels'] = ( isset( $mixins['fs_panels'] ) ) ? $mixins['fs_panels'] : '';
			$mixins['fs_panels'] .= $r . PHP_EOL;
		}
	}

	return $mixins;
}


function iki_toolkit_get_sass_variables( $var_files ) {

	if ( current_theme_supports( 'iki-toolkit-fs-panels' ) ) {

		$r = Iki_Sass_Compiler::get_sass_file_full_path( IKI_TOOLKIT_ROOT . 'sass/dynamic/_dynamic-vars.scss' );

		if ( ! empty( $r ) ) {

			$var_files['fs_panels'] = ( isset( $var_files['fs_panels'] ) ) ? $var_files['fs_panels'] : '';
			$var_files['fs_panels'] .= $r . PHP_EOL;
		}

	}

	return $var_files;
}

