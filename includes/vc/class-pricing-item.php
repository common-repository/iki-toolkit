<?php

/**
 * Class for creating pricing item shortcode
 */

class Iki_Pricing_Item_VC {

	protected $base = 'iki_pricing_item_vc';
	private static $id = 0;

	protected $text_label_top = '';
	protected $text_above_price = '';
	protected $price_text = '';
	protected $text_below_price = '';
	protected $button_text = '';

	public function __construct() {

		add_action( 'vc_before_init', array( $this, 'register_for_lean_map' ) );
		add_shortcode( $this->base, array( $this, 'do_shortcode' ) );
		$this->price_text = _x( '$99', 'example text for shortcode', 'iki-toolkit' );
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
	 * Create shortcode options
	 * @return array wpbakery shortcode settings array
	 */
	public function vc_backend_settings() {

		$params = array(
			"name"     => __( 'Pricing Item', 'iki-toolkit' ),
			"base"     => $this->base,
			"category" => __( "Iki Themes", 'iki-toolkit' ),
			'icon'     => plugin_dir_url( __FILE__ ) . 'icons/pricing-item.png',
			"params"   => array(
				array(
					'type'        => 'textfield',
					'param_name'  => 'text_label_top',
					'admin_label' => true,
					'value'       => $this->text_label_top,
					'heading'     => esc_html__( 'Text label top', 'iki-toolkit' ),
				),
				array(
					'type'        => 'textfield',
					'param_name'  => 'text_above_price',
					'value'       => $this->text_above_price,
					'admin_label' => true,
					'heading'     => esc_html__( 'Text above the price', 'iki-toolkit' ),
				),
				array(
					'type'        => 'textfield',
					'param_name'  => 'text_above_size',
					'heading'     => esc_html__( 'Text above size', 'iki-toolkit' ),
					'description' => esc_html__( 'You must provide the units ("px" or "em") default is around 20px ', 'iki-toolkit' ),
					'dependency'  => array( 'element' => 'text_above_price', 'not_empty' => true )
				),

				array(
					'type'        => 'textfield',
					'param_name'  => 'price_text',
					'value'       => $this->price_text,
					'admin_label' => true,
					'heading'     => esc_html__( 'Price text', 'iki-toolkit' ),
				),
				array(
					'type'        => 'textfield',
					'param_name'  => 'price_size',
					'heading'     => esc_html__( 'Price size', 'iki-toolkit' ),
					'description' => esc_html__( 'You must provide the units ("px" or "em") default is around 80px ', 'iki-toolkit' ),
					'dependency'  => array( 'element' => 'price_text', 'not_empty' => true )
				),
				array(
					'type'        => 'exploded_textarea',
					'param_name'  => 'text_below_price',
					'admin_label' => true,
					'value'       => $this->text_below_price,
					'heading'     => esc_html__( 'Text below the price', 'iki-toolkit' ),
					'description' => esc_html__( 'Every line will be a separate html "<div>" tag', 'iki-toolkit' )
				),
				array(
					'type'       => 'textarea_html',
					'param_name' => 'content',
					'value'      => '',
					'heading'    => esc_html__( 'Main text', 'iki-toolkit' ),
				),
				array(
					'type'        => 'textfield',
					'param_name'  => 'button_text',
					'value'       => $this->button_text,
					'admin_label' => true,
					'heading'     => esc_html__( 'Button text', 'iki-toolkit' ),
					'group'       => esc_html__( 'Button', 'iki-toolkit' )
				),
				array(
					'type'        => 'vc_link',
					'param_name'  => 'button_link',
					'heading'     => esc_html__( 'Button Link', 'iki-toolkit' ),
					'description' => esc_html__( 'You need to have URL in order for the button to be shown', 'iki-toolkit' ),
					'dependency'  => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'       => esc_html__( 'Button', 'iki-toolkit' )
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'button_position',
					"value"      => array(
						__( 'Below main text', 'iki-toolkit' ) => 'bottom',
						__( 'Above main text', 'iki-toolkit' ) => 'top',
					),
					'heading'    => esc_html__( 'Button position', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'      => esc_html__( 'Button', 'iki-toolkit' )
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'button_size',
					"value"      => array(
						__( 'Full width', 'iki-toolkit' ) => 'full',
						__( 'Small', 'iki-toolkit' )      => 'small',
						__( 'Medium', 'iki-toolkit' )     => 'medium',
						__( 'Large', 'iki-toolkit' )      => 'large',
					),
					'heading'    => esc_html__( 'Button width', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'      => esc_html__( 'Button', 'iki-toolkit' )
				),
				array(
					"type"        => "textfield",
					"admin_label" => true,
					"heading"     => __( "Extra class name", 'iki-toolkit' ),
					"param_name"  => "html_class",
					"value"       => '',
					"description" => __( 'Style particular content element differently - add a class name and refer to it in custom CSS', 'iki-toolkit' )
				),
				//==design
				array(
					'type'       => 'colorpicker',
					'param_name' => 'top_label_color',
					'heading'    => esc_html__( 'Top label text color', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'text_label_top', 'not_empty' => true ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),

				array(
					'type'       => 'colorpicker',
					'param_name' => 'top_label_color_bg',
					'heading'    => esc_html__( 'Top label background color', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'text_label_top', 'not_empty' => true ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),

				array(
					'type'       => 'colorpicker',
					'param_name' => 'price_color',
					'heading'    => esc_html__( 'Price text color', 'iki-toolkit' ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'price_color_bg',
					'heading'    => esc_html__( 'Price background color', 'iki-toolkit' ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),

				array(
					'type'       => 'colorpicker',
					'param_name' => 'body_color',
					'heading'    => esc_html__( 'Body text color', 'iki-toolkit' ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'body_color_bg',
					'heading'    => esc_html__( 'Body background color', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'content', 'not_empty' => true ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					'type'        => 'textfield',
					'param_name'  => 'border_width',
					'value'       => '1',
					'heading'     => esc_html__( 'Border width', 'iki-toolkit' ),
					'description' => esc_html__( 'Value is in pixels, no units (default is 1px)', 'iki-toolkit' ),
					'group'       => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'border_color',
					'heading'    => esc_html__( 'Border color', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'border_width', 'not_empty' => true ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'shadow',
					"value"      => array(
						__( 'None', 'iki-toolkit' )   => 'none',
						__( 'Weak', 'iki-toolkit' )   => 'weak',
						__( 'Strong', 'iki-toolkit' ) => 'strong',
					),
					'heading'    => esc_html__( 'Shadow', 'iki-toolkit' ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					"type"       => "checkbox",
					"heading"    => __( 'Animate on hover', 'iki-toolkit' ),
					"param_name" => "animate_on_hover",
					"value"      => array(
						__( 'yes', 'iki-toolkit' ) => '1',
					),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'dropdown',
					'param_name' => 'layout',
					"value"      => array(
						__( 'Default', 'iki-toolkit' ) => 'default',
						__( 'Pull up', 'iki-toolkit' ) => 'pull-up',
						__( 'Stretch', 'iki-toolkit' ) => 'stretch',
					),
					'heading'    => esc_html__( 'Layout', 'iki-toolkit' ),
					'group'      => esc_html__( 'Design', 'iki-toolkit' )
				),

				//==BUTTON DESIGN
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_color',
					'heading'    => esc_html__( 'Text color', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'      => esc_html__( 'Button Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_color_hover',
					'heading'    => esc_html__( 'Text color on hover', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'      => esc_html__( 'Button Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_color_bg',
					'heading'    => esc_html__( 'Background color', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'      => esc_html__( 'Button Design', 'iki-toolkit' )
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_color_bg_hover',
					'heading'    => esc_html__( 'Background color on hover', 'iki-toolkit' ),
					'dependency' => array( 'element' => 'button_text', 'not_empty' => true ),
					'group'      => esc_html__( 'Button Design', 'iki-toolkit' )
				),
			)
		);

		return $params;

	}


	/**
	 * Create and print shortcode
	 *
	 * @param $atts array shortcode attributes
	 * @param $content string shortcode textarea_html content
	 *
	 * @return bool|string
	 */
	public function do_shortcode( $atts, $content ) {

		if ( ! is_array( $atts ) ) {
			$atts = array();
		}

		$defaults = array(
			'button_position' => 'bottom',
			'button_size'     => 'full',
			'price_text'      => $this->price_text,
			'layout'          => 'default'
		);

		$current_id = self::generate_id();
		$style_tag  = '';

		$atts = array_merge( $defaults, $atts );

		$classes = array( 'iki-pricing-item ', 'iki-id-' . $current_id );

		$classes[] = 'iki-price-l-' . $atts['layout'];

		if ( isset( $atts['shadow'] ) ) {
			$classes[] = 'iki-price-s-' . $atts['shadow'];
		}

		if ( isset( $atts['animate_on_hover'] ) && $atts['animate_on_hover'] ) {
			$classes[] = 'iki-price-anim-hover';
		}

		if ( ! empty( $atts['html_class'] ) ) {
			$classes[] = $atts['html_class'];
		}

		$text_label_top = '';

		if ( ! empty( $atts['text_label_top'] ) ) {

			$classes[]        = 'iki-has-label-top';
			$text_label_style = '';
			if ( ! empty( $atts['top_label_color'] ) ) {
				$text_label_style = 'color:' . esc_attr( $atts['top_label_color'] ) . ';';
			}
			if ( ! empty( $atts['top_label_color_bg'] ) ) {

				$text_label_style .= 'background-color:' . esc_attr( $atts['top_label_color_bg'] ) . ';';
			}

			$text_label_top = sprintf( '<div style="%2$s" class="iki-label-top">%1$s</div>',
				$atts['text_label_top'],
				$text_label_style
			);
		}

		$above_price = '';
		if ( ! empty( $atts['text_above_price'] ) ) {
			$above_price_size = '';
			if ( ! empty( $atts['text_above_size'] ) ) {
				$above_price_size = sprintf( 'style="font-size:%1$s;"', trim( $atts['text_above_size'] ) );
			}
			$classes[]   = 'iki-has-text-above';
			$above_price = sprintf( '<div %2$s class="iki-above-price">%1$s</div>',
				$atts['text_above_price'],
				$above_price_size
			);
		}

		$price_text = '';
		if ( ! empty( $atts['price_text'] ) ) {

			$price_text_size = '';
			if ( ! empty( $atts['price_size'] ) ) {
				$price_text_size = sprintf( 'style="font-size:%1$s;"', trim( $atts['price_size'] ) );
			}
			$classes[]  = 'iki-has-price-text';
			$price_text = sprintf( '<div %2$s class="iki-price-text">%1$s</div>',
				$atts['price_text'],
				$price_text_size
			);

		}

		$below_price_text = '';
		if ( ! empty( $atts['text_below_price'] ) ) {
			$classes[]   = 'iki-has-text-below';
			$below_price = explode( ',', $atts['text_below_price'] );

			if ( is_array( $below_price ) ) {

				foreach ( $below_price as $line ) {
					$below_price_text .= sprintf( '<div class="iki-below-price">%1$s</div>', $line );
				}
			} else {
				$below_price_text = sprintf( '<div class="iki-below-price">%1$s</div>', $atts['text_below_price'] );
			}

			$below_price_text = wp_kses_post( $below_price_text );
		}

		if ( ! empty( $content ) ) {
			$classes[] = 'iki-has-body-text';
			$content   = sprintf( '<div class="iki-price-main">%1$s</div>', apply_filters( 'the_content', $content ) );
		}

		$price_color = '';
		if ( ! empty( $atts['price_color'] ) ) {
			$price_color = 'color:' . esc_attr( $atts['price_color'] ) . ';';
		}
		if ( ! empty( $atts['price_color_bg'] ) ) {

			$price_color .= 'background-color:' . esc_attr( $atts['price_color_bg'] ) . ';';
		}

		$body_color = '';
		if ( ! empty( $atts['body_color'] ) ) {
			$body_color = 'color:' . esc_attr( $atts['body_color'] ) . ';';
		}
		if ( ! empty( $atts['body_color_bg'] ) ) {

			$body_color .= 'background-color:' . esc_attr( $atts['body_color_bg'] ) . ';';
		}

		$border_style = '';
		if ( ! empty( $atts['border_color'] ) ) {

			$border_style = 'border-color:' . esc_attr( $atts['border_color'] ) . ';';
		}

		if ( isset( $atts['border_width'] ) ) {

			if ( ! empty( $atts['border_width'] ) && 1 != $atts['border_width'] ) {
				//don't do it if its 1px (this is done via css)
				$border_style .= 'border-width:' . esc_attr( $atts['border_width'] ) . 'px;';

			} else {
				$border_style .= 'border:none';
			}
		}

		$link     = '';
		$link_top = '';
		if ( ! empty( $atts['button_link'] ) && ! empty( $atts['button_text'] ) ) {

			$button_style       = '';
			$button_style_hover = '';
			$button_has_styles  = false;

			$link   = iki_toolkit_vc_get_custom_link_attributes( $atts['button_link'] );
			$link[] = 'class="tooltip-js iki-btn iki-vc-btn-' . $atts['button_size'] . '"';
			$link   = iki_toolkit_vc_build_link_tag( $link, sanitize_text_field( $atts['button_text'] ) );
			$link   = '<div class="iki-btn-price-wrap">' . $link . '</div>';

			if ( 'top' == $atts['button_position'] ) {
				$link_top  = $link;
				$link      = '';
				$classes[] = 'iki-btn-top';
			} else {
				$classes[] = 'iki-btn-bottom';
			}

			if ( ! empty( $atts['button_color'] ) ) {
				$button_style .= 'color:' . esc_attr( $atts['button_color'] ) . ';';

			}

			if ( ! empty( $atts['button_color_bg'] ) ) {

				$button_style .= 'background-color:' . esc_attr( $atts['button_color_bg'] ) . ';';
				$button_style .= 'border-color:' . esc_attr( $atts['button_color_bg'] ) . ';';

			}

			if ( ! empty( $atts['button_color_hover'] ) ) {

				$button_style_hover .= 'color:' . esc_attr( $atts['button_color_hover'] ) . ';';
			}


			if ( ! empty( $atts['button_color_bg_hover'] ) ) {

				$button_style_hover .= 'background-color:' . esc_attr( $atts['button_color_bg_hover'] ) . ';';
				$button_style_hover .= 'border-color:' . esc_attr( $atts['button_color_bg_hover'] ) . ';';
			}

			if ( ! empty( $button_style ) ) {

				$button_has_styles = true;
				$button_style      = sprintf( '.iki-pricing-item.iki-id-%1$s .iki-btn {%2$s}',
					$current_id,
					$button_style );

			}
			if ( ! empty( $button_style_hover ) ) {

				$button_has_styles  = true;
				$button_style_hover = sprintf( '.iki-pricing-item.iki-id-%1$s .iki-btn:hover {%2$s}',
					$current_id,
					$button_style_hover );

			}

			if ( $button_has_styles ) {
				$style_tag = sprintf( '<style>%1$s %2$s</style>', $button_style, $button_style_hover );
			}
		}

		$below_price_2 = '';
		$output        = sprintf(
			'<div style="%10$s" class="%5$s"><div style="%8$s" class="iki-pricing-head"> %13$s %6$s %1$s %2$s %3$s %7$s</div><div style="%9$s" class="iki-price-body">%12$s %4$s %11$s</div></div>',
			$above_price,
			$price_text,
			$below_price_text,
			$content,
			Iki_Toolkit_Utils::sanitize_html_class_array( $classes ),
			$text_label_top,
			$below_price_2,
			$price_color,
			$body_color,
			$border_style,
			$link,
			$link_top,
			$style_tag
		);

		return $output;

	}
}

new Iki_Pricing_Item_VC();
