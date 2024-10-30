<?php

/**
 * Create stamps in the menu
 */
class Iki_Stamp_Creator {


	/**
	 * Get created stamp
	 *
	 * @param array $item menu item
	 * @param int $dept menu depth
	 *
	 * @return string stamp html
	 */
	public function get_stamp( &$item, $depth ) {

		$stamp_tag = '';

		$item->iki_stamp_text = sanitize_text_field( $item->iki_stamp_text );

		if ( ! empty( $item->iki_has_new_stamp ) && ! empty( $item->iki_stamp_text ) ) {

			$stamp_layout = array(
				'top'      => $item->iki_stamp_pos_top,
				'right'    => $item->iki_stamp_pos_right,
				'bottom'   => $item->iki_stamp_pos_bottom,
				'left'     => $item->iki_stamp_pos_left,
				'rotation' => $item->iki_stamp_rotation,
				'width'    => $item->iki_stamp_width

			);


			$stamp_tag = $this->create_stamp(
				$item->iki_stamp_text,
				$stamp_layout,
				$item->iki_menu_stamp_animation,
				array(
					'iki-stamp-depth-' . $depth
				) );

			$item->iki_link_classes [] = ' iki-has-stamp ';

		}

		return $stamp_tag;
	}

	/**
	 * Create stamp
	 *
	 * @param string $text
	 * @param array $layout
	 * @param $animation
	 * @param array $classes
	 *
	 * @return string
	 */
	protected function create_stamp( $text = '', Array $layout, $animation, Array $classes = array() ) {

		$stampStyle = ' style="';
		$classes[]  = 'iki-stamp-tag';

		if ( $animation != 'none' && ! empty( $animation ) ) {
			$classes[] = 'iki-anim-' . sanitize_html_class( $animation );
		}

		//rotation
		$stampStyle .= $this->set_rotation( esc_attr( $layout['rotation'] ) );

		//position
		$stampStyle .= $this->set_positioning( $layout );

		$stampStyle .= '"';

		$classes = ' class="' . Iki_Toolkit_Utils::sanitize_html_class_array( $classes ) . '"';

		$stampTag = '<span ' . $stampStyle . $classes . '  ><span class="iki-stamp-text">' . sanitize_text_field( $text ) . '</span></span>';

		return $stampTag;
	}

	/** Set default value with some cleanup
	 *
	 * @param $val
	 *
	 * @return string
	 */
	protected function set_value( $val ) {

		if ( empty( $val ) ) {
			$val = 'auto';
		} else {
			if ( ctype_digit( $val ) ) {
				$val = $val . 'px';
			}
		}

		return $val;
	}


	/**
	 * Set rotation
	 *
	 * @param string $rotation
	 *
	 * @return string
	 */
	protected function set_rotation( $rotation = '' ) {

		$stamp_rotation = '';
		$rotation       = trim( $rotation );
		if ( ! empty( $rotation ) ) {
			$rotation       = esc_attr( $rotation );
			$stamp_rotation = "transform:rotate({$rotation}deg);";
		}

		return $stamp_rotation;
	}

	/**
	 * Set positioning
	 *
	 * @param $positions
	 *
	 * @return string
	 */
	protected function set_positioning( $positions ) {

		$is_absolute         = false;
		$stamp_style         = '';
		$custom_left         = false;
		$positions['bottom'] = trim( $positions['bottom'] );
		if ( ! empty( $positions['bottom'] ) ) {
			$is_absolute = true;
			$stamp_style .= "bottom:" . $this->set_value_2( $positions['bottom'], '0' ) . ';';
		}

		$positions['right'] = trim( $positions['right'] );
		if ( ! empty( $positions['right'] ) ) {
			$is_absolute = true;
			$stamp_style .= "right:" . $this->set_value_2( $positions['right'], '0' ) . ';';
		}

		$positions['left'] = trim( $positions['left'] );
		if ( ! empty( $positions['left'] ) ) {
			$is_absolute = true;
			$custom_left = true;
			$stamp_style .= "left:" . $this->set_value_2( $positions['left'], '0' ) . ';';
		}

		$positions['top'] = trim( $positions['top'] );
		if ( ! empty( $positions['top'] ) ) {
			$is_absolute = true;
			$stamp_style .= "top:" . $this->set_value_2( $positions['top'], '0' ) . ';';
		}

		//width
		$positions['width'] = trim( $positions['width'] );
		if ( ! empty( $positions['width'] ) ) {
			$stamp_style .= "min-width: " . $this->set_value( $positions['width'] ) . ';';
		}

		//end style
		if ( $is_absolute ) {
			if ( ! $custom_left ) {
				$stamp_style .= 'left:0px;';
			}
			$stamp_style .= 'position:absolute;';
		}

		return $stamp_style;
	}

	/**
	 * Set value 2
	 *
	 * @param string $val
	 * @param $default
	 *
	 * @return string
	 */
	protected function set_value_2( $val = '', $default ) {
		$val = trim( $val );
		if ( ! empty( $val ) ) {
			return esc_attr( $val );
		} else {
			return $default;
		}
	}

	/**
	 * Setup z index on element
	 *
	 * @param $item
	 * @param $depth
	 */
	public static function setup_z_index( &$item, $depth ) {
		if ( 0 == $depth ) {
			if ( ! empty( $item->iki_menu_z_index ) ) {
				$item->iki_styles[] = 'z-index:' . esc_attr( intval( $item->iki_menu_z_index ) );
			}

		}

	}
}