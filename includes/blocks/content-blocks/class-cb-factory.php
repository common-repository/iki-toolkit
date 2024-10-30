<?php

/**
 * Class that handles printing of content blocks
 * */
class Iki_CB_Factory {

	public $styles = array();

	private static $class = null;
	protected $original_post;

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
	}

	/** Get the instace of the class ( singleton)
	 * @return Iki_CB_Factory|null
	 */
	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}

	/**
	 * Add custom css from content blocks.
	 */
	public function wp_footer() {
		$css = implode( '', $this->styles );
		if ( $css ) {
			echo '<style type="text/css">' . $css . '</style>';
		}
	}

	/**
	 * Add custom css from content blocks
	 *
	 * @param $css string css
	 * @param int $id id for css
	 */
	protected function inline_css( $css, $id = 0 ) {
		$this->styles[ $id ] = $css;
	}

	/**
	 * Get WPBakery page builder data for content block (to be printed later)
	 *
	 * @param $id int post id
	 */
	protected function get_custom_vc_data( $id ) {
		if ( apply_filters( 'iki_output_custom_vc_data', true ) ) {

			if ( ! isset( $this->styles[ $id ] ) ) {

				$custom_css        = get_post_meta( $id, '_wpb_post_custom_css', true );
				$custom_css        .= get_post_meta( $id, '_wpb_shortcodes_custom_css', true );
				$wp_add_custom_css = get_post_meta( $id, '_single_add_custom_css', true );//"wordpress-add-custom-css" plugin

				if ( ! empty( $wp_add_custom_css ) ) {
					$replacement       = ' .iki-block-' . $id . '$1';
					$pattern           = '/\s*(\.iki-content-block)/';
					$wp_add_custom_css = preg_replace( $pattern, $replacement, $wp_add_custom_css );
					$custom_css        .= $wp_add_custom_css;
				}
				$this->inline_css( $custom_css, $id );

			}
		}
	}


	/**
	 * @param int $id Content block id
	 * @param bool $echo if contnet should be echoed immediately
	 *
	 * @return mixed
	 */
	public function content_block( $id, $echo = true ) {
		$cb_post    = get_post( $id );
		$cb_content = '';
		if ( $cb_post && 'publish' == $cb_post->post_status ) {

			global $post;

			setup_postdata( $cb_post );

			$post       = $cb_post;//critical for WPBakery page builder
			$cb_content = do_shortcode( $cb_post->post_content );

			$this->get_custom_vc_data( $id );

			wp_reset_postdata();

			if ( $echo ) {
				echo $cb_content;

				return true;
			}
		}

		return $cb_content;
	}

}

new Iki_CB_Factory();

