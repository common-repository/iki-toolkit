<?php

/**
 * Class that provides dynamic css as a file or as inline style
 */

class Iki_Dynamic_CSS {

	protected static $class;
	protected $theme_hash = null;
	protected $theme_hash_match = null;
	protected $option_prefix;
	protected $access_type;
	protected $file_path;
	/**@var Iki_Dynamic_SASS $dynamic_sass */
	protected $dynamic_sass;

	public function __construct( $file_path = null ) {

		$this->file_path     = $file_path;
		$this->option_prefix = get_option( 'stylesheet' ) . '_';

	}


	/** Compile css
	 *
	 * @param bool $save_to_db
	 * @param bool $save_to_file
	 * @param bool $force_compilation
	 *
	 * @return string css string
	 */
	public function compile( $save_to_db = false, $save_to_file = false, $force_compilation = false ) {

		$hash_match = $this->theme_hash_match();

		if ( ! $hash_match || $force_compilation ) {
			//new compile
			$this->dynamic_sass = new Iki_Dynamic_SASS();
			$css                = $this->dynamic_sass->compile_css();
			// if css is not empty new db entry
			// else try to get saved option
			if ( ! empty( $css ) ) {

				if ( $save_to_db ) {

					update_option( $this->option_prefix . 'options_hash', $this->get_theme_hash() );
					update_option( $this->option_prefix . 'dynamic_css', $css );

				}
				if ( $save_to_file ) {
					$this->write_file( $css );
				}
			}

		} else {
			$css = get_option( $this->option_prefix . 'dynamic_css', '' );
		}

		return $css;
	}

	/**
	 * Return dynamic css option from the database.
	 * @return string css string or empty string if the options doesn't exists.
	 */
	public function get_css_as_string() {

		return get_option( $this->option_prefix . 'dynamic_css', '' );
	}


	/**
	 * Get a url to a dynamically created css file.
	 * @return string
	 */
	public function get_custom_css_uri() {
		$r = apply_filters( 'iki_custom_css_uri', '' );

		return $r;
	}

	/**
	 * Write css to file
	 *
	 * @param string $content css contents
	 * @param null $file_path file path
	 *
	 * @return bool
	 */
	public function write_file( $content = '', $file_path = null ) {

		if ( $this->has_direct_write_access() ) {

			if ( ! $file_path ) {
				$file_path = $this->file_path;
			}

			$file_path = apply_filters( 'iki_custom_css_file_path', $file_path );

			$path_parts = pathinfo( $file_path );
			$dirname    = $path_parts['dirname'];

			/**@var WP_Filesystem_Direct $wp_filesystem */
			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				WP_Filesystem();
			}
			if ( file_exists( $file_path ) ) {
				// write file
				if ( ! $wp_filesystem->put_contents( $file_path, $content ) ) {
					// Writing to the file failed.
					return false;
				}
			} else {
				if ( wp_mkdir_p( $dirname ) ) {
					//write file
					if ( ! $wp_filesystem->put_contents( $file_path, $content ) ) {
						//write failed
						return false;
					}
				} else {
					//directory creation failed
					return false;
				}

				//success
				return true;
			}
		} else {
			//can't write directly
			return false;
		}
	}

	/**
	 * Check if we have direct access
	 * @return bool
	 */
	public function has_direct_write_access() {
		if ( is_null( $this->access_type ) ) {
			$this->get_access_type();
		}

		return 'direct' == $this->access_type;
	}

	/**
	 * What kind of access we have
	 * @return string
	 */
	public function get_access_type() {

		if ( is_null( $this->access_type ) ) {
			$this->access_type = get_filesystem_method();
		}

		return $this->access_type;
	}

	/**
	 * Calculate theme hash
	 * @return null|string
	 */
	public function get_theme_hash() {
		if ( is_null( $this->theme_hash ) ) {
			$this->theme_hash = $this->_calculate_hash( array(
				iki_toolkit()->get_theme_option(),
				iki_toolkit()->get_customizer_option()
			), 'theme' );
		}

		return $this->theme_hash;
	}

	/**
	 * Check if new and old theme hash match
	 * @return bool|null
	 */
	public function theme_hash_match() {
		if ( is_null( $this->theme_hash_match ) ) {
			$old_hash               = get_option( $this->option_prefix . 'options_hash', false );
			$new_hash               = $this->get_theme_hash();
			$this->theme_hash_match = ( $new_hash === $old_hash );
		}

		return $this->theme_hash_match;
	}

	/**
	 * Calculate the hash
	 *
	 * @param $data
	 * @param string $for
	 *
	 * @return string
	 */
	protected function _calculate_hash( $data, $for = '' ) {
		$r = '';
		foreach ( $data as $item ) {
			$r .= md5( serialize( $item ) );
		}

		$customData                 = apply_filters( 'iki_dynamic_css_calculate_hash', array(), $for );
		$customData['dynamic_host'] = $_SERVER['HTTP_HOST'];

		$r .= md5( serialize( $customData ) );

		return $r;

	}

}
