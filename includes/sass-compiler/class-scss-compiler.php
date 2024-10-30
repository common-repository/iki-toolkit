<?php

/**
 * Class that handles compiling sass files
 */
class Iki_Sass_Compiler {


	public static function compile( $sass ) {
		require_once( 'class-scss.php' );
		$compiler = new iki_themes_scssc();
		$compiler->setFormatter( 'iki_themes_scss_formatter_compressed' );
		try {
			return $compiler->compile( $sass );
		} catch ( Exception $e ) {

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				throw $e;
			}

			return $sass;
		}
	}

	/**
	 * Get full path to sass file
	 *
	 * @param $path
	 *
	 * @return bool|string
	 */
	public static function get_sass_file_full_path( $path ) {
		return self::_get_sass_file( $path );
	}

	/**
	 * Get the file
	 *
	 * @param string $relative_path relative path to the file
	 *
	 * @return bool|string
	 */
	public static function get_sass_file( $relative_path ) {

		return self::_get_sass_file( trailingslashit( get_template_directory() ) . $relative_path );

	}

	/**
	 * Internal function to get the file
	 * @internal
	 *
	 * @param $path
	 *
	 * @return bool|string
	 */
	protected static function _get_sass_file( $path ) {

		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			require( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
			require( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );
		}

		if ( ! class_exists( 'WP_Filesystem_Direct' ) ) {
			return '';
		}

		$wp_filesystem = new WP_Filesystem_Direct( null );
		$sass          = $wp_filesystem->get_contents( $path );
		unset( $wp_filesystem );

		// This is slower, but okay since the results will be cached indefinitely.
		if ( empty( $sass ) ) {

			$request = wp_remote_get( $path );

			if ( ! is_wp_error( $request ) ) {
				if ( 200 == $request['response']['code'] ) {
					$sass = wp_remote_retrieve_body( $request );
				}
			}
		}

		return $sass;
	}

	/**
	 * Validate compiled css
	 *
	 * @param $css
	 *
	 * @return bool
	 */
	public static function validate( $css ) {
		return ( ! empty( $css ) && strpos( $css, '$sass-success-check' ) === false );

	}


}