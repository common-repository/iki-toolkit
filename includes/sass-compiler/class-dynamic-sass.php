<?php

/**
 * Class that handles gathering sass files for compilation
 */

class Iki_Dynamic_SASS {

	protected static $theme_sass = null;
	protected static $mixins;
	protected static $variables;
	protected static $regex_injection_flag = 'iki-xx-customizer-override';
	protected static $css_success_check_flag = '$sass-success-check';
	protected $theme_options;
	protected $customizer_options;

	/**
	 * Get the injection flag ( where to start injecting new css )
	 * @return string
	 */
	public static function get_injection_flag() {
		return self::$regex_injection_flag;
	}

	/**
	 * Start injecting css data
	 *
	 * @param $override
	 * @param $sass_file
	 *
	 * @return mixed
	 */
	public static function replace_vars( $override, $sass_file ) {

		return preg_replace( '/^.*?' . self::$regex_injection_flag . '.*$/im', $override, $sass_file );
	}


	/**
	 * Compile theme sass
	 * @return mixed
	 */
	public function compile_css() {

		self::$theme_sass           = $this->prepare_theme_sass();
		self::$theme_sass['data']   = apply_filters( 'iki_prepare_sass_vars_as_data', self::$theme_sass['data'] );
		self::$theme_sass['string'] = apply_filters( 'iki_prepare_sass_vars_as_string', self::$theme_sass['string'] );

		// get mixins
		self::$mixins = apply_filters( 'iki_dynamic_css_get_mixin_files', array( 'global' => '' ) );

		// get variables/
		self::$variables = apply_filters( 'iki_dynamic_css_get_variable_files', array( 'global' => '' ) );

		// compile the css
		$compiled_css = apply_filters( 'iki_dynamic_css_compile', '' );

		return $compiled_css;
	}


	/**
	 * Return scoped mixin
	 *
	 * @param string $scope
	 *
	 * @return string
	 */
	public static function get_mixins( $scope = 'global' ) {

		if ( isset( self::$mixins[ $scope ] ) ) {
			return self::$mixins[ $scope ];
		} else {
			return '';
		}
	}


	/**
	 * Return scoped variables
	 *
	 * @param string $scope
	 *
	 * @return string
	 */
	public static function get_variables( $scope = 'global' ) {
		if ( isset( self::$variables[ $scope ] ) ) {
			return self::$variables[ $scope ];
		} else {
			return '';
		}
	}

	/**
	 * Return sass variables as strings
	 * @return mixed
	 */
	public static function get_theme_sass_variables_as_string() {
		return self::$theme_sass['string'];
	}

	/**
	 * Get all sass variables as array
	 * @return mixed
	 */
	public static function get_theme_sass_variables_as_array() {
		return self::$theme_sass['data'];
	}

	/**
	 * Get a string to search for after the compilation to check if the file is cimpiled successifully
	 * If the string is not there, compilation is success
	 * @return string
	 */
	public static function get_success_check_flag() {
		return self::$css_success_check_flag;
	}

	/**
	 * Validate sass compilation result
	 *
	 * @param $css
	 *
	 * @return bool
	 */
	public static function validate_css( $css ) {
		return ( ! empty( $css ) && strpos( $css, self::$css_success_check_flag ) === false );

	}

	/**
	 * Prepare sass files (vars) for compilation
	 * @return array
	 */
	private function prepare_theme_sass() {


		$r = array(
			'data'   => array(),
			'string' => ''
		);

		$theme_options      = iki_toolkit()->get_theme_option( null, array() );
		$customizer_options = iki_toolkit()->get_customizer_option( null, array() );

		if ( ! empty( $theme_options ) ) {
			$r = Iki_Toolkit_Utils::prepare_sass_vars( $theme_options );

		}
		if ( ! empty( $customizer_options ) ) {

			$custSass = Iki_Toolkit_Utils::prepare_sass_vars( $customizer_options );

			if ( ! empty( $theme_options ) ) {
				$r['string'] .= PHP_EOL . $custSass['string'] . PHP_EOL;
				$r['data']   = $r['data'] + $custSass['data'];
			} else {
				$r = $custSass;
			}
		}


		return $r;
	}

}

