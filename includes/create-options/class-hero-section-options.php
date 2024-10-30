<?php

/**
 * Class that handles creating options for hero section functionality
 */
class Iki_Toolkit_Hero_Section_Options {

	private static $class = null;

	protected $cached_hs_gradient_options;
	protected $cached_hs_layout_options;
	protected $cached_hs_image_bg_options;
	protected $cached_hs_bg_with_video_options;
	protected $cached_hs_bg_without_video_options;
	protected $cached_hs_custom_content_options;
	protected $default_background_options;

	protected $revolution_sliders;
	protected $revolution_slider_dropdown;

	// option caches
	protected $cached_video_bg_options;
	protected $cached_default_bg_options;

	/** Get class instance
	 * @return Iki_Toolkit_Hero_Section_Options
	 */
	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}

	public function __construct() {

		$this->default_background_options = $this->default_background_options();
	}

	/** Get default backgorund options
	 *
	 * @param null $additional_data data to be replaced
	 *
	 * @return array|mixed|null
	 */
	public function default_background_options( $additional_data = null ) {
		$cache = $this->check_cache( '', $additional_data, $this->cached_default_bg_options );

		if ( $cache ) {
			return $cache;
		}
		$r = array();

		$backgroundUrl = array(
			'type'        => 'upload',
			'value'       => $GLOBALS['iki_toolkit_admin']['body']['background']['url'],
			'label'       => __( 'Image', 'iki-toolkit' ),
			'images_only' => true
		);

		$backgroundRepeat = array(
			'type'    => 'radio',
			'value'   => $GLOBALS['iki_toolkit_admin']['body']['background']['repeat'],
			'label'   => __( 'Repeat', 'iki-toolkit' ),
			'choices' => array(
				'no-repeat' => __( 'No repeat', 'iki-toolkit' ),
				'repeat'    => __( 'Repeat', 'iki-toolkit' ),
				'repeat-x'  => __( 'Repeat X', 'iki-toolkit' ),
				'repeat-y'  => __( 'Repeat Y', 'iki-toolkit' ),
			),
			// Display choices inline instead of list
			'inline'  => false,
		);

		$backgroundSize = array(
			'type'    => 'radio',
			'value'   => $GLOBALS['iki_toolkit_admin']['body']['background']['size'],
			'attr'    => array( 'class' => 'custom-class', 'data-foo' => 'bar' ),
			'label'   => __( 'Background size', 'iki-toolkit' ),
			'choices' => array(
				'cover'   => __( 'Cover', 'iki-toolkit' ),
				'contain' => __( 'Contain', 'iki-toolkit' ),
				'auto'    => __( 'Auto', 'iki-toolkit' ),
			),
			'inline'  => false,
		);


		$backgroundPosition = array(
			'type'    => 'radio',
			'value'   => $GLOBALS['iki_toolkit_admin']['body']['background']['position'],
			'label'   => __( 'Position', 'iki-toolkit' ),
			'choices' => array(
				'top left'      => __( 'Top Left', 'iki-toolkit' ),
				'top center'    => __( 'Top Center', 'iki-toolkit' ),
				'top right'     => __( 'Top Right', 'iki-toolkit' ),
				'left center'   => __( 'Left Center', 'iki-toolkit' ),
				'center center' => __( 'Center Center', 'iki-toolkit' ),
				'right center'  => __( 'Right Center', 'iki-toolkit' ),
				'bottom left'   => __( 'Bottom Left', 'iki-toolkit' ),
				'bottom center' => __( 'Bottom Center', 'iki-toolkit' ),
				'bottom right'  => __( 'Bottom Right', 'iki-toolkit' ),

			)
		);


		$backgroundAttachment = array(
			'type'    => 'select',
			'label'   => __( 'Attachment', 'iki-toolkit' ),
			'value'   => $GLOBALS['iki_toolkit_admin']['body']['background']['attachment'],
			'choices' => array(
				'scroll' => __( 'Scroll', 'iki-toolkit' ),
				'fixed'  => __( 'Fixed', 'iki-toolkit' ),
			)
		);

		$r['color'] = array(
			'type'  => 'rgba-color-picker',
			'value' => $GLOBALS['iki_toolkit_admin']['colors']['body']['color_bg'],
			'label' => __( 'Background color', 'iki-toolkit' ),
		);

		$r['url']        = $backgroundUrl;
		$r['size']       = $backgroundSize;
		$r['position']   = $backgroundPosition;
		$r['repeat']     = $backgroundRepeat;
		$r['attachment'] = $backgroundAttachment;


		$this->cached_default_bg_options = $this->maybe_cache_options( $r, '', $additional_data, $this->cached_default_bg_options );

		return $this->maybe_replace_options( $r, $additional_data );
	}

	/** Popup hero options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 * @param string $block_for
	 *
	 * @return array|mixed
	 */
	public function popup_hero_section( $key_prefix = '', $additional_data = null, $block_for = 'hero_section' ) {

		$additional_layout = null;
		if ( $additional_data ) {
			if ( isset( $additional_data['popup-options']['layout_tab']['options'] ) ) {
				$additional_layout = $additional_data['popup-options']['layout_tab']['options'];
			}
		}

		$layout = $this->get_hero_layout( $key_prefix, $additional_layout );

		$background = $this->hero_section_custom_background_options( $key_prefix, null, true );

		$custom_content    = $this->hero_section_content( '', null, $block_for );
		$overlay           = $this->overlay_options( $key_prefix );
		$separator_options = null;

		if ( isset( $additional_data['popup-options']['separator_tab'] ) ) {
			$separator_options = $additional_data['popup-options']['separator_tab'];
		}

		$separator = $this->separator_options( $key_prefix, $separator_options );

		$text_options = $this->hero_section_text_options( $key_prefix );

		$custom_social_icons = array(
			'custom_social_group' => array(
				'type'    => 'group',
				'options' => array(
					'custom_social_enabled' => $this->custom_social_enabled(),
					'social_design'         => Iki_Toolkit_Admin_Options::get_instance()->get_share_design()
				)
			)
		);

		$r = array(
			'type'          => 'popup',
			'value'         => array(),
			'label'         => __( 'Setup hero section', 'iki-toolkit' ),
			'desc'          => false,
			'popup-title'   => __( 'Hero section options', 'iki-toolkit' ),
			'button'        => __( 'Open', 'iki-toolkit' ),
			'size'          => 'medium', // small, medium, large
			'popup-options' => array(
				'layout_tab'              => array(
					'type'    => 'tab',
					'options' => array(),
					'title'   => __( 'Layout', 'iki-toolkit' ),
				),
				'background_tab'          => array(
					'type'    => 'tab',
					'options' => array(),
					'title'   => __( 'Background', 'iki-toolkit' ),
				),
				'text_color_tab'          => array(
					'type'    => 'tab',
					'options' => array(),
					'title'   => __( 'Text colors', 'iki-toolkit' ),
				),
				'overlay_tab'             => array(
					'type'    => 'tab',
					'options' => array(),
					'title'   => __( 'Overlay', 'iki-toolkit' ),
				),
				'custom_content_tab'      => array(
					'type'    => 'tab',
					'options' => array(),
					'title'   => __( 'Custom Content', 'iki-toolkit' ),
				),
				'separator_tab'           => array(
					'type'    => 'tab',
					'options' => $separator,
					'title'   => __( 'Separator', 'iki-toolkit' )
				),
				'custom_social_icons_tab' => array(
					'type'    => 'tab',
					'title'   => __( 'Social icons design', 'iki-toolkit' ),
					'options' => $custom_social_icons,

				)
			)
		);

		// layout tab
		$r['popup-options']['layout_tab']['options'] = array_merge( $r['popup-options']['layout_tab']['options'], $layout );

		$r['popup-options']['background_tab']['options'] = array_merge( $r['popup-options']['background_tab']['options'], $background );

		$r['popup-options']['custom_content_tab']['options'] = array_merge( $r['popup-options']['custom_content_tab']['options'], $custom_content );

		$r['popup-options']['overlay_tab']['options'] = array_merge( $r['popup-options']['overlay_tab']['options'], $overlay );

		$r['popup-options']['text_color_tab']['options'] = array_merge( $r['popup-options']['text_color_tab']['options'], $text_options );

		$r = $this->maybe_replace_options( $r, $additional_data );

		if ( isset( $r['popup-options']['custom_social_icons_tab'] ) ) {

			$r['popup-options']['custom_social_icons_tab']['options'] = array_reverse( $r['popup-options']['custom_social_icons_tab']['options'] );
		}

		return $r;
	}


	/** Get hero layout options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 *
	 * @return array|mixed|null
	 */
	public function get_hero_layout( $key_prefix = '', $additional_data = null ) {
		$cache = $this->check_cache( $key_prefix, $additional_data, $this->cached_hs_layout_options );

		if ( $cache ) {
			return $cache;
		}

		$r = array(
			"{$key_prefix}custom_layout"             => array(
				'label'        => __( 'Custom layout', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => "{$key_prefix}height,
						{$key_prefix}width_fixed,
						{$key_prefix}scroll_indicator,
						{$key_prefix}scroll_indicator_position,
						{$key_prefix}horizontal_aligment,
						{$key_prefix}vertical_aligment,
						{$key_prefix}title_inside",
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				),
			),
			"{$key_prefix}width_fixed"               => array(
				'type'    => 'select',
				'value'   => 'disabled',
				'label'   => __( 'Width', 'iki-toolkit' ),
				'desc'    => __( 'Width of the hero section', 'iki-toolkit' ),
				'choices' => array(
					'enabled'  => __( 'Theme fixed width', 'iki-toolkit' ),
					'disabled' => __( 'Browser width', 'iki-toolkit' ),
				)
			),
			"{$key_prefix}height"                    => array(
				'type'    => 'select',
				'value'   => 'default',
				'label'   => __( 'Height', 'iki-toolkit' ),
				'desc'    => __( 'Height of hero section', 'iki-toolkit' ),
				'help'    => __( 'Full height value is the height of the browser window.', 'iki-toolkit' ),
				'choices' => array(
					'default' => __( 'Default', 'iki-toolkit' ),
					'medium'  => __( 'Medium', 'iki-toolkit' ),
					'large'   => __( 'Large', 'iki-toolkit' ),
					'xl'      => __( 'Extra large (XL)', 'iki-toolkit' ),
					'xxl'     => __( 'Extra extra large (XXL)', 'iki-toolkit' ),
					'full'    => __( 'Full browser height', 'iki-toolkit' ),
				)
			),
			"{$key_prefix}scroll_indicator"          => array(
				'label'        => __( 'Show scroll indicator', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'help'         => __( 'Scroll indicator is useful if hero section has full height, so it can indicate to user that there is more content.
			If hero section is not full height, scroll indicator will be automatically hidden.', 'iki-toolkit' ),
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => "{$key_prefix}scroll_indicator_position",
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'No', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Yes', 'iki-toolkit' ),
				),
			),
			"{$key_prefix}scroll_indicator_position" => array(
				'type'    => 'select',
				'value'   => 'right',
				'label'   => __( 'Scroll indicator position', 'iki-toolkit' ),
				'help'    => __( 'Scroll indicator position inside the hero section.', 'iki-toolkit' ),
				'choices' => array(
					'default'     => __( 'Default', 'iki-toolkit' ),
					'left'        => __( 'Left', 'iki-toolkit' ),
					'fixed-left'  => __( 'Fixed Left', 'iki-toolkit' ),
					'center'      => __( 'Center', 'iki-toolkit' ),
					'right'       => __( 'Right', 'iki-toolkit' ),
					'fixed-right' => __( 'Fixed Right', 'iki-toolkit' ),
				)
			),
			"{$key_prefix}title_inside"              => array(
				'type'         => 'switch',
				'value'        => 'enabled',
				'label'        => __( 'Page title inside hero section', 'iki-toolkit' ),
				'desc'         => __( 'If enabled title will be inside the hero section, otherwise title will appear below hero section', 'iki-toolkit' ),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'No', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Yes', 'iki-toolkit' ),
				),
			),
			"{$key_prefix}horizontal_aligment"       => array(
				'type'    => 'select',
				'value'   => 'center',
				'label'   => __( 'Content horizontal alignment', 'iki-toolkit' ),
				'desc'    => __( 'Horizontal aligment of the content inside the hero section', 'iki-toolkit' ),
				'choices' => array(
					'center'     => __( 'Center', 'iki-toolkit' ),
					'left'       => __( 'Left', 'iki-toolkit' ),
					'fixed-left' => __( 'Fixed Left', 'iki-toolkit' )
				),
			),
			"{$key_prefix}vertical_aligment"         => array(
				'type'    => 'select',
				'value'   => 'center',
				'label'   => __( 'Content vertical aligment', 'iki-toolkit' ),
				'desc'    => __( 'Vertical aligment of the content inside the hero section', 'iki-toolkit' ),
				'choices' => array(
					'top'    => __( 'Top', 'iki-toolkit' ),
					'center' => __( 'Center', 'iki-toolkit' ),
					'bottom' => __( 'Bottom', 'iki-toolkit' ),
				)
			),
		);

		if ( empty( $key_prefix ) && is_null( $additional_data ) ) {
			if ( is_null( $this->cached_hs_layout_options ) ) {
				$this->cached_hs_layout_options = $r;
			}
		}
		$this->cached_hs_layout_options = $this->maybe_cache_options( $r, $key_prefix, $additional_data, $this->cached_hs_layout_options );

		return $this->maybe_replace_options( $r, $additional_data );
	}

	/** Check options cache
	 *
	 * @param string $key_prefix option prefix
	 * @param array $additional_data additional data
	 * @param string $cache_container options container
	 *
	 * @return null
	 */
	protected function check_cache( $key_prefix, $additional_data, $cache_container ) {
		if ( empty( $key_prefix ) && is_null( $additional_data ) && ! is_null( $cache_container ) ) {
			return $cache_container;
		} else {
			return null;
		}

	}


	/** Recursively replace options
	 *
	 * @param $source array original options
	 * @param $new  array new options options
	 *
	 * @return array|mixed
	 */
	protected function maybe_replace_options( $source, $new ) {

		if ( $new ) {

			$source = array_replace_recursive( $source, $new );

			return $this->remove_null_options( $source );
		}

		return $source;
	}

	/** Maybe cache options
	 *
	 * @param array $options options to cache
	 * @param string $key_prefix options prefix
	 * @param array $additional_data additional data
	 * @param string $cache_container options container name
	 *
	 * @return null
	 */
	protected function maybe_cache_options( $options, $key_prefix, $additional_data, $cache_container ) {
		if ( empty( $key_prefix ) && is_null( $additional_data ) && is_null( $cache_container ) ) {
			return $options;
		} else {
			return null;
		}


	}

	/** Custom background options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 * @param bool $video_background
	 *
	 * @return array|mixed|null
	 */
	public function hero_section_custom_background_options( $key_prefix = '', $additional_data = null, $video_background = false ) {

		$background = $this->hero_section_custom_image_background_options( $key_prefix );

		$video_options = array();

		if ( $video_background ) {
			$video_options = $this->video_background_options( $key_prefix );
			$cache         = $this->check_cache( $key_prefix, $additional_data, $this->cached_hs_bg_with_video_options );
			if ( $cache ) {
				return $cache;
			}
		} else {
			$cache = $this->check_cache( $key_prefix, $additional_data, $this->cached_hs_bg_without_video_options );
			if ( $cache ) {
				return $cache;
			}
		}


		$r = array(
			"{$key_prefix}background_enabled" => array(
				'label'        => __( 'Custom background', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => 'image_bg_group,video_bg_group',
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'Disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				)
			)
		);

		$r = array_merge( $r, $background, $video_options );

		if ( $video_background ) {
			$this->cached_hs_bg_with_video_options = $this->maybe_cache_options( $r, $key_prefix, $additional_data, $this->cached_hs_bg_with_video_options );
		} else {
			$this->cached_hs_bg_without_video_options = $this->maybe_cache_options( $r, $key_prefix, $additional_data, $this->cached_hs_bg_without_video_options );
		}

		return $this->maybe_replace_options( $r, $additional_data );
	}

	/** Remove options with null values
	 *
	 * @param $haystack haystack of options
	 *
	 * @return array with or  without null options
	 */
	protected function remove_null_options( $haystack ) {

		if ( isset( $haystack['options'] ) ) {
			$options = &$haystack['options'];
		} elseif ( isset( $haystack['popup-options'] ) ) {
			$options = &$haystack['popup-options'];
		} else {
			$options = &$haystack;
		}

		foreach ( $options as $key => &$value ) {

			if ( is_null( $value ) ) {
				unset( $options[ $key ] );
			} elseif ( isset( $value['options'] ) ) {
				$value = $this->remove_null_options( $value );
			}
		}

		return $haystack;
	}

	/**
	 * @param string $key_prefix
	 * @param null $additional_data
	 * @param string $block_for
	 *
	 * @return array|mixed
	 */
	public function hero_section_content( $key_prefix = '', $additional_data = null, $block_for = 'hero_section' ) {
		$key_prefix = ( ! empty( $key_prefix ) ) ? $key_prefix . '_' : '';

		$r = $this->hero_section_content_options( $key_prefix, null, $block_for );

		return $this->maybe_replace_options( $r, $additional_data );
	}

	/** Overlay options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 *
	 * @return array|mixed|null
	 */
	public function overlay_options( $key_prefix = '', $additional_data = null ) {

		$cache = $this->check_cache( $key_prefix, $additional_data, $this->cached_hs_gradient_options );
		if ( $cache ) {
			return $cache;
		}

		$r = array(

			"{$key_prefix}overlay_fake"         => array(
				'type'  => 'html',
				'value' => '',
				'label' => false,
				'html'  => __( 'Gradient overlay goes over the hero background image, and can provide a nice contrast for a more readable text.', 'iki-toolkit' ) . '</br>' . __( 'It consists of two colors, you can change them independently.', 'iki-toolkit' ),
			),
			"{$key_prefix}overlay_enabled"      => array(
				'label'        => __( 'Custom overlay', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => "{$key_prefix}gradient_color_1,
					{$key_prefix}gradient_color_2,
					{$key_prefix}gradient_orientation,
					{$key_prefix}gradient_c_1_start,
					{$key_prefix}gradient_c_2_start
					",
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'Disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				),

			),
			"{$key_prefix}gradient_color_1"     => array(
				'type'  => 'rgba-color-picker',
				'value' => $GLOBALS['iki_toolkit_admin']['colors']['hero']['overlay']['gradient_1'],
				'label' => __( 'Color 1', 'iki-toolkit' ),
				'desc'  => __( 'First gradient color', 'iki-toolkit' ),
			),
			"{$key_prefix}gradient_color_2"     => array(
				'type'  => 'rgba-color-picker',
				'value' => $GLOBALS['iki_toolkit_admin']['colors']['hero']['overlay']['gradient_2'],
				'label' => __( 'Color 2', 'iki-toolkit' ),
				'desc'  => __( 'Second gradient color', 'iki-toolkit' ),
			),
			"{$key_prefix}gradient_orientation" => array(
				'type'    => 'select',
				'value'   => 'top',
				'label'   => __( 'Gradient orientation', 'iki-toolkit' ),
				'choices' => array(
					'bottom'       => __( 'Vertical from top to bottom', 'iki-toolkit' ),
					'right top'    => __( 'Diagonal from bottom to top', 'iki-toolkit' ),
					'right bottom' => __( 'Diagonal from top to bottom', 'iki-toolkit' ),
				),
			),
			"{$key_prefix}gradient_c_1_start"   => array(
				'type'       => 'slider',
				'value'      => 0,
				'properties' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1
				),
				'label'      => __( 'Color 1 start', 'iki-toolkit' ),
				'desc'       => __( 'Best results are between 0-50', 'iki-toolkit' ),
			),
			"{$key_prefix}gradient_c_2_start"   => array(
				'type'       => 'slider',
				'value'      => 100,
				'properties' => array(
					'min'  => 0,
					'max'  => 100,
					'step' => 1
				),
				'label'      => __( 'Color 2 start', 'iki-toolkit' ),
				'desc'       => __( 'Best results are between 50-100', 'iki-toolkit' )
			)
		);

		$this->cached_hs_gradient_options = $this->maybe_cache_options( $r, $key_prefix, $additional_data, $this->cached_hs_gradient_options );

		return $this->maybe_replace_options( $r, $additional_data );

	}

	/** Separator options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 *
	 * @return array|mixed
	 */
	public function separator_options( $key_prefix = '', $additional_data = null ) {


		$r = array(
			"{$key_prefix}separator_info"    => array(
				'type'  => 'html',
				'label' => false,
				'value' => '',
				'html'  => __( 'Separator is a visual element that gives an illusion of irregular shape for hero section.', 'iki-toolkit' )
			),
			"{$key_prefix}separator_enabled" => array(
				'label'        => __( 'Separator', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => "separator_options_group",
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'Disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				),

			),
			"separator_options_group"        => array(
				'type'    => 'group',
				'options' => array(

					"{$key_prefix}separator_design"   => array(
						'type'    => 'select',
						'value'   => 'tilt-left-s',
						'label'   => __( 'Design', 'iki-toolkit' ),
						'choices' => $this->get_row_separator_designs(),
					),
					"{$key_prefix}separator_position" => array(
						'type'    => 'select',
						'value'   => 'relative',
						'label'   => __( 'Position', 'iki-toolkit' ),
						'choices' => array(
							'relative' => __( 'Relative', 'iki-toolkit' ),
							'absolute' => __( 'Absolute', 'iki-toolkit' ),
						),
					),
					"{$key_prefix}separator_width"    => array(

						'type'    => 'select',
						'value'   => 'full',
						'label'   => __( 'Width', 'iki-toolkit' ),
						'choices' => array(
							'fixed' => __( 'Theme fixed width', 'iki-toolkit' ),
							'full'  => __( 'Hero section width', 'iki-toolkit' ),
						)
					)
				)
			)
		);

		return $this->maybe_replace_options( $r, $additional_data );

	}


	/** Text options
	 *
	 * @param $key_prefix
	 *
	 * @return array
	 */
	public function hero_section_text_options( $key_prefix ) {


		$r = array(
			"custom_colors_enabled"     => array(
				'label'        => __( 'Enable custom colors', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => "text_colors_options_group",
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'Disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				)
			),
			"text_colors_options_group" => array(
				'type'    => 'group',
				'options' => array(
					"text_color"         => array(
						'type'  => 'color-picker',
						'value' => $GLOBALS['iki_toolkit_admin']['colors']['hero']['text_color'],
						'label' => __( 'Text color', 'iki-toolkit' )
					),
					"link_color"         => array(
						'type'  => 'color-picker',
						'value' => $GLOBALS['iki_toolkit_admin']['colors']['hero']['link_color'],
						'label' => __( 'Link color', 'iki-toolkit' )
					),
					"link_color_hover"   => array(
						'type'  => 'color-picker',
						'value' => $GLOBALS['iki_toolkit_admin']['colors']['hero']['link_color_hover'],
						'label' => __( 'Link color hover', 'iki-toolkit' ),
					),
					'remove_text_shadow' => array(
						'type'  => 'checkbox',
						'value' => false,
						'label' => __( 'Remove text shadows', 'iki-toolkit' ),
						'help'  => __( 'Some text elements (title, subtitle etc..) have very subtle text shadows applied. You can choose to remove them.', 'iki-toolkit' )
					)
				)
			)
		);

		return $r;

	}

	/** Disable / enable social media buttons
	 *
	 * @param string $for
	 *
	 * @return array
	 */
	public function custom_social_enabled( $for = 'social_design' ) {
		return array(
			'label'        => __( 'Use custom design', 'iki-toolkit' ),
			'type'         => 'switch',
			'value'        => 'disabled',
			'attr'         => array(
				'data-iki-switch'  => 1,
				'data-iki-for'     => "{$for}",
				'data-iki-test'    => 'enabled',
				'data-iki-refresh' => 'alwaysRefresh',
			),
			'left-choice'  => array(
				'value' => 'disabled',
				'label' => __( 'No', 'iki-toolkit' ),
			),
			'right-choice' => array(
				'value' => 'enabled',
				'label' => __( 'Yes', 'iki-toolkit' ),
			)
		);
	}

	/** Custom image background options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 *
	 * @return array|mixed|null
	 */
	public function hero_section_custom_image_background_options( $key_prefix = '', $additional_data = null ) {

		$background                   = $this->default_background_options();
		$background['color']['value'] = $GLOBALS['iki_toolkit_admin']['colors']['hero']['background']['color_bg'];

		$cache = $this->check_cache( $key_prefix, $additional_data, $this->cached_hs_image_bg_options );
		if ( $cache ) {
			return $cache;
		}

		$r = array(
			'image_bg_group' => array(
				'type'    => 'group',
				'options' => array(
					"{$key_prefix}background_color"      => $background['color'],
					"{$key_prefix}background_url"        => $background['url'],
					"{$key_prefix}background_size"       => $background['size'],
					"{$key_prefix}background_position"   => $background['position'],
					"{$key_prefix}background_repeat"     => $background['repeat'],
					"{$key_prefix}background_attachment" => $background['attachment'],
					"{$key_prefix}generate_blur"         => array(
						'type'         => 'switch',
						'label'        => __( 'Blur background', 'iki-toolkit' ),
						'desc'         => __( 'Blur the background while the real (big) image is loading', 'iki-toolkit' ),
						'value'        => 'disabled',
						'attr'         => array(
							'data-iki-switch'  => 1,
							'data-iki-for'     => "{$key_prefix}blur_strength,
							{$key_prefix}permanent_blur",
							'data-iki-test'    => 'enabled',
							'data-iki-refresh' => 'alwaysRefresh',
						),
						'left-choice'  => array(
							'value' => 'disabled',
							'label' => __( 'No', 'iki-toolkit' ),
						),
						'right-choice' => array(
							'value' => 'enabled',
							'label' => __( 'Yes', 'iki-toolkit' ),
						)
					),

					"{$key_prefix}blur_strength"  => array(
						'type'    => 'select',
						'label'   => __( 'Blur strength', 'iki-toolkit' ),
						'value'   => 10,
						'choices' => array(
							'5'  => __( 'Weak', 'iki-toolkit' ),
							'10' => __( 'Normal', 'iki-toolkit' ),
							'15' => __( 'Strong', 'iki-toolkit' )
						)
					),
					"{$key_prefix}permanent_blur" => array(
						'type'         => 'switch',
						'label'        => __( 'Permanent blur', 'iki-toolkit' ),
						'desc'         => __( 'Leave the blurred background (don\'t load big image)', 'iki-toolkit' ),
						'help'         => __( 'If used, hero section will have blurred background and big background picture will never be loaded. This can be a very interesting design effect, and improve page load performance.', 'iki-toolkit' ),
						'value'        => 'disabled',
						'left-choice'  => array(
							'value' => 'disabled',
							'label' => __( 'No', 'iki-toolkit' ),
						),
						'right-choice' => array(
							'value' => 'enabled',
							'label' => __( 'Yes', 'iki-toolkit' ),
						)
					),
				)
			)
		);

		$this->cached_hs_image_bg_options = $this->maybe_cache_options( $r, $key_prefix, $additional_data, $this->cached_hs_image_bg_options );

		return $this->maybe_replace_options( $r, $additional_data );
	}

	/** Hero section content options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 * @param string $block_for
	 *
	 * @return array|mixed|null
	 */
	public function hero_section_content_options( $key_prefix = '', $additional_data = null, $block_for = 'hero_section' ) {

		$key_prefix = ( ! empty( $key_prefix ) ) ? $key_prefix . '_' : '';

		$r = array(
			"{$key_prefix}custom_content_enabled" => array(
				'label'        => __( 'Custom content', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'help'         => __( 'Please note that if "featured posts" option is present and enabled for this hero section it will override custom content.', 'iki-toolkit' ),
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => "{$key_prefix}custom_content,
					{$key_prefix}content_background,
					{$key_prefix}content_width,
					{$key_prefix}content_remove_spacing,
					{$key_prefix}content_custom_width",
					'data-iki-test'    => 'enabled',
					'data-iki-refresh' => 'alwaysRefresh',
				),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				),
			),
			"{$key_prefix}custom_content"         => $this->hero_section_custom_content( $block_for ),
			"{$key_prefix}content_background"     => array(
				'type'         => 'switch',
				'value'        => 'disabled',
				'label'        => __( 'Background behind content', 'iki-toolkit' ),
				'help'         => __( 'For some custom content elements ( gallery , wp editor ) this option will show background behind those
				elements, so it is  easier to read and see the actual content.', 'iki-toolkit' ),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'No', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Yes', 'iki-toolkit' ),
				),
			),
			"{$key_prefix}content_width"          => array(
				'type'    => 'select',
				'value'   => '1',
				'label'   => __( 'Content width', 'iki-toolkit' ),
				'help'    => __( 'Depending on how slider revolution is constructed it might not respect this setting.', 'iki-toolkit' ),
				'choices' => array(
					'default' => __( 'Theme width', 'iki-toolkit' ) . ' ~ 1200px',
					'full'    => __( 'Browser width', 'iki-toolkit' ),
					'1'       => __( 'Narow', 'iki-toolkit' ) . ' ~ 850px',
					'2'       => __( 'More narrow', 'iki-toolkit' ) . ' ~ 650px',
					'3'       => __( 'Even more narrow', 'iki-toolkit' ) . ' ~ 500px',
					'custom'  => __( 'Custom width', 'iki-toolkit' )
				)
			),
			"{$key_prefix}content_custom_width"   => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Custom content width', 'iki-toolkit' ),
				'desc'  => __( 'Specify custom content width (with units). Make sure that "custom width" option is selected above. ', 'iki-toolkit' ),
				'help'  => __( 'Use only numbers, no units.The value will always be in pixels.', 'iki-toolkit' )
			),
			"{$key_prefix}content_remove_spacing" => array(
				'type'         => 'switch',
				'value'        => 'disabled',
				'label'        => __( 'Remove vertical spacing for custom content', 'iki-toolkit' ),
				'help'         => __( 'This option is particularly useful when you have "slider revolution" for custom content. So the slider appears over the whole hero section.', 'iki-toolkit' ),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'No', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Yes', 'iki-toolkit' ),
				),
			)
		);

		return $this->maybe_replace_options( $r, $additional_data );

	}

	/** Available custom content options
	 *
	 * @param string $block_for
	 *
	 * @return array
	 */
	public function hero_section_custom_content( $block_for = 'hero_section' ) {

		$r = array(
			'type'    => 'multi-picker',
			'label'   => false,
			'desc'    => false,
			'value'   => array(
				'chosen_content' => 'none'
			),
			'picker'  => array(
				'chosen_content' => array(
					'type'    => 'select',
					'label'   => 'Content',
					'choices' => array(
						'none'            => __( 'None', 'iki-toolkit' ),
						'image'           => __( 'Image', 'iki-toolkit' ),
						'featured_image'  => __( 'Featured image', 'iki-toolkit' ),
						'multiple_images' => __( 'Image gallery', 'iki-toolkit' ),
						'oembed'          => __( 'Oembed', 'iki-toolkit' ),
						'content_block'   => __( 'Content Block', 'iki-toolkit' ),
						'rev_slider'      => __( 'slider revolution', 'iki-toolkit' ),
						'wp_editor'       => __( 'WordPress editor', 'iki-toolkit' ),
						'html'            => __( 'Raw HTML', 'iki-toolkit' ),

					),
				)
			),
			'choices' => array(
				'image'           => array(
					'id'   => array(
						'type'        => 'upload',
						'label'       => __( 'Image', 'iki-toolkit' ),
						'images_only' => true
					),
					'size' => array(
						'type'    => 'select',
						'value'   => 'grid_2_landscape_stripe',
						'label'   => __( 'Image size', 'iki-toolkit' ),
						'choices' => array(
							'thumbnail'               => __( 'Thumbnail', 'iki-toolkit' ),
							'medium'                  => __( 'Medium', 'iki-toolkit' ),
							'large'                   => __( 'Large', 'iki-toolkit' ),
							'full'                    => __( 'Original', 'iki-toolkit' ),
							'grid_2_landscape'        => __( 'Landscape', 'iki-toolkit' ),
							'grid_2_landscape_stripe' => __( 'Landscape slim', 'iki-toolkit' ),
							'grid_2_square'           => __( 'Square', 'iki-toolkit' ),
							'grid_2_portrait'         => __( 'Portrait', 'iki-toolkit' ),
						)
					)
				),
				'featured_image'  => array(
					'featured_image_html_info' => array(
						'type'  => 'html',
						'value' => '',
						'label' => false,
						'desc'  => false,
						'html'  => __( 'Please note: This option is only availabe for posts, pages, and custom post types (where you can actually set the featured image) for other locations use "image" option instead.', 'iki-toolkit' )
					),
					'size'                     => array(
						'type'    => 'select',
						'value'   => 'grid_2_landscape_stripe',
						'label'   => __( 'Image size', 'iki-toolkit' ),
						'choices' => array(
							'thumbnail'               => __( 'Thumbnail', 'iki-toolkit' ),
							'medium'                  => __( 'Medium', 'iki-toolkit' ),
							'large'                   => __( 'Large', 'iki-toolkit' ),
							'full'                    => __( 'Original', 'iki-toolkit' ),
							'grid_2_landscape'        => __( 'Landscape', 'iki-toolkit' ),
							'grid_2_landscape_stripe' => __( 'Landscape slim', 'iki-toolkit' ),
							'grid_2_square'           => __( 'Square', 'iki-toolkit' ),
							'grid_2_portrait'         => __( 'Portrait', 'iki-toolkit' ),
						)
					)
				),
				'multiple_images' => array(
					'images'    => array(
						'type'        => 'multi-upload',
						'value'       => array(),
						'label'       => __( 'Images', 'iki-toolkit' ),
						'images_only' => true,
					),
					'columns'   => array(
						'type'    => 'select',
						'value'   => '4',
						'label'   => __( 'Columns', 'iki-toolkit' ),
						'choices' => array(
							'1'  => __( '1', 'iki-toolkit' ),
							'2'  => __( '2', 'iki-toolkit' ),
							'3'  => __( '3', 'iki-toolkit' ),
							'4'  => __( '4', 'iki-toolkit' ),
							'5'  => __( '5', 'iki-toolkit' ),
							'6'  => __( '6', 'iki-toolkit' ),
							'7'  => __( '7', 'iki-toolkit' ),
							'8'  => __( '8', 'iki-toolkit' ),
							'9'  => __( '9', 'iki-toolkit' ),
							'10' => __( '10', 'iki-toolkit' ),
						)
					),
					'animation' => $this->get_animation_in_option( null, false )
				),
				'oembed'          => array(
					'payload'            => array(
						'type'    => 'oembed',
						'value'   => '',
						'label'   => __( 'Oembed links', 'iki-toolkit' ),
						'desc'    => __( 'Youtube, Vimeo, Twitter, SoundCloud and other wordpress supported oemb providers.', 'iki-toolkit' ),
						'help'    => __( 'If wordpress doesn\'t properly embed the content from your chosen provider. Try the "custom html" option instead', 'iki-toolkit' ),
						'preview' => array(
							'keep_ratio' => true
						)
					),
					'oembed_orientation' => array(
						'type'    => 'select',
						'value'   => 'landscape',
						'label'   => __( 'Force oembed element orientation', 'iki-toolkit' ),
						'help'    => __( 'You can force oembed element layout. For instance, youtube videos are in landscape mode, instagram videos are square. Defining this value will prevent content resizing (jumping) when the content is initially loaded.
							This is completely optional.', 'iki-toolkit' ),
						'choices' => array(
							'default'   => __( 'No', 'iki-toolkit' ),
							'landscape' => __( 'Landscape', 'iki-toolkit' ),
							'square'    => __( 'Square', 'iki-toolkit' ),
							'portrait'  => __( 'Portrait', 'iki-toolkit' )
						)
					)
				),
				'rev_slider'      => array(
					'alias' => $this->get_revolution_slider_option( array(
						'label' => __( 'Slider', 'iki-toolkit' ),
						'desc'  => ''
					) )
				),
				'html'            => array(
					'payload' => array(

						'type'  => 'textarea',
						'value' => '',
						'label' => __( 'Custom html', 'iki-toolkit' ),
						'desc'  => __( 'Please note: Be careful when using raw html and javascript, be sure to trust the code you are using, or your site could be
				exploited via malicious code.', 'iki-toolkit' )
					)
				),
				'content_block'   => array(
					'id' => Iki_Toolkit_Admin_Options::get_instance()->get_content_block_top_option(
						array(
							'label' => __( 'Content block', 'iki-toolkit' )
						),
						false,
						$block_for
					)
				),
				'wp_editor'       => array(
					'payload' => array(
						'type'  => 'wp-editor',
						'value' => '',
						'label' => __( 'Custom Text', 'iki-toolkit' ),
						'size'  => 'large'
					)
				)
			)
		);

		return $r;
	}

	/**
	 * Hero section featured posts options
	 *
	 * @param bool $only_same_tax_option only same taxonomy
	 * @param bool $choose_source maybe show "source" dropdown option
	 *
	 * @return array
	 */
	public function hero_section_featured_posts_opts( $only_same_tax_option = false, $choose_source = false ) {
		$r = array(
			'type'    => 'tab',
			'title'   => __( 'Featured Posts', 'iki-toolkit' ),
			'options' => array(
				'featured_posts_group' => array(
					'type'    => 'group',
					'options' => array(
						'featured_exp'            => array(
							'type'  => 'html',
							'value' => '',
							'label' => false,
							'desc'  => false,
							'html'  => __( 'You can choose to feature particular posts in hero section.', 'iki-toolkit' )
						),
						'featured_posts_enabled'  => array(
							'label'        => __( 'Enabled', 'iki-toolkit' ),
							'type'         => 'switch',
							'value'        => 'diabled',
							'attr'         => array(
								'data-iki-switch'  => 1,
								'data-iki-for'     => 'feat_posts_group_toggle',
								'data-iki-test'    => 'enabled',
								'data-iki-refresh' => 'alwaysRefresh',
							),
							'left-choice'  => array(
								'value' => 'disabled',
								'label' => __( 'No', 'iki-toolkit' ),
							),
							'right-choice' => array(
								'value' => 'enabled',
								'label' => __( 'Yes', 'iki-toolkit' ),
							)
						),
						'feat_posts_group_toggle' => array(
							'type'    => 'group',
							'options' => array(
								'toolkit_featured_posts' => array(
									'label'        => __( 'Use posts setup via Iki Toolkit plugin setttings', 'iki-toolkit' ),
									'help'         => __( 'You can go to "Settings->Iki Toolkit->Featured posts" and setup featured posts for the post type you want to use.', 'iki-toolkit' ),
									'type'         => 'switch',
									'value'        => 'enabled',
									'attr'         => array(
										'data-iki-switch'  => 1,
										'data-iki-for'     => 'specific_featured_posts',
										'data-iki-against' => 'featured_posts_source',
										'data-iki-test'    => 'disabled',
										'data-iki-refresh' => 'alwaysRefresh',
									),
									'left-choice'  => array(
										'value' => 'disabled',
										'label' => __( 'No', 'iki-toolkit' ),
									),
									'right-choice' => array(
										'value' => 'enabled',
										'label' => __( 'Yes', 'iki-toolkit' ),
									)
								)
							)
						)
					)
				)
			)
		);

		$r['options']['featured_posts_group']['options']['feat_posts_group_toggle']['options']['specific_featured_posts'] = array(
			'type'  => 'text',
			'value' => '',
			'label' => __( 'Specific posts', 'iki-toolkit' ),
			'desc'  => __( 'Use specific posts, write post ID\'s comma separated', 'iki-toolkit' ),
		);

		if ( $choose_source ) {
			$r['options']['featured_posts_group']['options']['feat_posts_group_toggle']['options']['featured_posts_source'] = array(
				'type'    => 'select',
				'value'   => 'blog',
				'label'   => __( 'Post source', 'iki-toolkit' ),
				'choices' => array(
					'blog' => __( 'Post', 'iki-toolkit' )//this is wp post type
				)
			);

			if ( get_theme_support( 'iki-toolkit-portfolio-cpt' ) ) {
				$r['options']['featured_posts_group']['options']['feat_posts_group_toggle']['options']['featured_posts_source']['choices']['iki_portfolio'] = __( 'Portfolio', 'iki-toolkit' );
			}

			if ( get_theme_support( 'iki-toolkit-team-member-cpt' ) ) {
				$r['options']['featured_posts_group']['options']['feat_posts_group_toggle']['options']['featured_posts_source']['choices']['iki_team_member'] = __( 'Team Member', 'iki-toolkit' );
			}

			if ( class_exists( 'WooCommerce' ) ) {
				$r['options']['featured_posts_group']['options']['feat_posts_group_toggle']['options']['featured_posts_source']['choices']['product'] = __( 'Product', 'iki-toolkit' );
			}

		}

		if ( $only_same_tax_option ) {

			$r['options']['featured_posts_group']['options']['feat_posts_group_toggle']['options']['only_same_taxonomy'] = array(
				'label'        => __( 'Show only posts from this category', 'iki-toolkit' ),
				'help'         => __( 'If post doesn\'t belong to this category it won\'t be shown', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'enabled',
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'No', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Yes', 'iki-toolkit' ),
				)
			);

		}


		return $r;
	}

	/** Enable or disable hero section options
	 *
	 * @param null $additional_data
	 * @param null $connected_elements
	 *
	 * @return array
	 */
	public function get_enable_hero_section( $additional_data = null, $connected_elements = null ) {

		$r = array(
			'type'         => 'switch',
			'value'        => 'disabled',
			'label'        => __( 'Enable hero section', 'iki-toolkit' ),
			'left-choice'  => array(
				'value' => 'disabled',
				'label' => __( 'No', 'iki-toolkit' ),
			),
			'right-choice' => array(
				'value' => 'enabled',
				'label' => __( 'Yes', 'iki-toolkit' ),
			)
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		if ( ! is_null( $connected_elements ) ) {

			$r['attr'] = array(
				'data-iki-switch'  => 1,
				'data-iki-for'     => join( ',', $connected_elements ),
				'data-iki-test'    => 'enabled',
				'data-iki-refresh' => 'alwaysRefresh',
			);
		}

		return $r;
	}

	/** Video background options
	 *
	 * @param string $key_prefix
	 * @param null $additional_data
	 *
	 * @return array|mixed|null
	 */
	protected function video_background_options( $key_prefix = '', $additional_data = null ) {
		if ( ! $additional_data && empty( $key_prefix ) && $this->cached_video_bg_options ) {
			return $this->cached_video_bg_options;
		}

		$cache = $this->check_cache( $key_prefix, $additional_data, $this->cached_video_bg_options );

		if ( $cache ) {
			return $cache;
		}

		$r = array(
			'video_bg_group' => array(
				'type'    => 'group',
				'options' => array(
					"{$key_prefix}video_background_enabled" => array(
						'label'        => __( 'Video background', 'iki-toolkit' ),
						'type'         => 'switch',
						'value'        => 'disabled',
						'desc'         => __( 'Please note: Video background will not be shown on mobile devices', 'iki-toolkit' ),
						'help'         => __( 'Video background severly affects bandwidth so it is disabled on mobile devices. It will
						 also be removed on smaller screen sizes via media queries. On mobile
				devices background image will be shown instead, so make sure you also setup background image.', 'iki-toolkit' ),
						'attr'         => array(
							'data-iki-switch'  => 1,
							'data-iki-for'     => "{$key_prefix}video_background_url,
							{$key_prefix}video_background_quality,
							{$key_prefix}video_background_pattern",
							'data-iki-test'    => 'enabled',
							'data-iki-refresh' => 'alwaysRefresh',
						),
						'left-choice'  => array(
							'value' => 'disabled',
							'label' => __( 'Disabled', 'iki-toolkit' ),
						),
						'right-choice' => array(
							'value' => 'enabled',
							'label' => __( 'Enabled', 'iki-toolkit' ),
						)
					),
					"{$key_prefix}video_background_url"     => array(
						'type'  => 'oembed',
						'value' => '',
						'label' => __( 'Video background', 'iki-toolkit' ),
						'desc'  => __( 'Youtube videos only.', 'iki-toolkit' ),
						'help'  => __( 'This option only supports youtube videos, all other links and video providers will be ignored.', 'iki-toolkit' ),
					),
					"{$key_prefix}video_background_quality" => array(
						'type'    => 'select',
						'value'   => 'small',
						'label'   => __( 'Video quality', 'iki-toolkit' ),
						'desc'    => __( 'Please note: High quality severly impacts load times.', 'iki-toolkit' ),
						'help'    => __( 'Best to try with the "small" option and see if that looks good. If not try "medium" etc..', 'iki-toolkit' ),
						'choices' => array(
							'small'  => __( 'Low', 'iki-toolkit' ),
							'medium' => __( 'Medium', 'iki-toolkit' ),
							'large'  => __( 'High', 'iki-toolkit' ),
						)
					),
					"{$key_prefix}video_background_pattern" => array(
						'type'    => 'select',
						'value'   => 'none',
						'label'   => __( 'Pattern overlay', 'iki-toolkit' ),
						'help'    => __( 'Pattern overlay can hide artifacts in low quality videos. And it looks stylish.', 'iki-toolkit' ),
						'choices' => array(
							'none'                   => __( 'none', 'iki-toolkit' ),
							'criss-cross.png'        => __( 'criss cross', 'iki-toolkit' ),
							'diagonal-lines.png'     => __( 'diagonal lines', 'iki-toolkit' ),
							'horizontal-lines.png'   => __( 'horizontal lines', 'iki-toolkit' ),
							'horizontal-stripes.png' => __( 'horizontal stripes', 'iki-toolkit' ),
							'vertical-lines.png'     => __( 'vertical lines', 'iki-toolkit' ),
							'vertical-stripes.png'   => __( 'vertical stripes', 'iki-toolkit' ),
							'squares.png'            => __( 'squares', 'iki-toolkit' ),
							'dots.png'               => __( 'dots', 'iki-toolkit' ),
							'small-checks.png'       => __( 'small checks', 'iki-toolkit' ),
							'medium-checks.png'      => __( 'medium checks', 'iki-toolkit' ),
						)
					)
				),
			)
		);

		$this->cached_video_bg_options = $this->maybe_cache_options( $r, $key_prefix, $additional_data, $this->cached_video_bg_options );

		return $this->maybe_replace_options( $r, $additional_data );


	}

	/** Animation IN options
	 *
	 * @param null $additional_data
	 * @param bool $enable_no_animation
	 * @param null $additional_animations
	 *
	 * @return array
	 */
	public function get_animation_in_option( $additional_data = null, $enable_no_animation = true, $additional_animations = null ) {

		$animations = self::get_velocity_animations_in( $enable_no_animation );

		if ( ! $enable_no_animation ) {
			unset( $animations['none'] );
		}

		if ( $additional_animations ) {
			$animations = array_merge( $animations, $additional_animations );
		}

		$r = array(
			'label'   => __( 'Animation IN', 'iki-toolkit' ),
			'type'    => 'select',
			'value'   => 'transition.slideUpIn',
			'choices' => $animations,
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;
	}

	/** Available velocity.js animations IN
	 *
	 * @param bool $no_animation_option option not to use the animation
	 *
	 * @return array animations
	 */
	public function get_velocity_animations_in( $no_animation_option = true ) {

		$r = Iki_Toolkit_Velocity_Options::get_instance()->get_velocity_animations_in( $no_animation_option );

		return $r;
	}


	/** Available velocity.js animations OUT
	 *
	 * @param bool $no_animation_option option not to use the animation
	 *
	 * @return array animation out
	 */
	public function get_velocity_animations_out( $no_animation_option = true ) {

		$r = Iki_Toolkit_Velocity_Options::get_instance()->get_velocity_animations_out( $no_animation_option );

		return $r;
	}

	/** Revolution slider options
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_revolution_slider_option( $additional_data = null ) {

		$sliders = self::get_instance()->get_revolution_sliders();
		$r       = array(
			'type'    => 'select',
			'value'   => '  ',
			'label'   => __( 'slider revolution', 'iki-toolkit' ),
			'desc'    => __( 'Choose slider revolution', 'iki-toolkit' ),
			'choices' => $sliders
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}

	/** Get available revolution sliders
	 * @return array
	 */
	protected function get_revolution_sliders() {

		if ( is_null( $this->revolution_sliders ) ) {

			$this->revolution_slider_dropdown = array();
			if ( shortcode_exists( "rev_slider" ) ) {
				/**@RevSlider $slider */
				$slider                   = new RevSlider();
				$this->revolution_sliders = $slider->getArrSliders();

				if ( $this->revolution_sliders ) {
					foreach ( $this->revolution_sliders as $revolution_slider ) {

						$alias = $revolution_slider->getID();
						$title = $revolution_slider->getTitle();

						$title = ( ! empty( $title ) ) ? $title : $alias;

						$this->revolution_slider_dropdown[ $alias ] = $title;
					}
				} else {
					$this->revolution_slider_dropdown[''] = __( 'No sliders available', 'iki-toolkit' );
				}
			} else {
				$this->revolution_sliders             = array();
				$this->revolution_slider_dropdown[''] = __( 'No sliders available', 'iki-toolkit' );

			}
		}

		return $this->revolution_slider_dropdown;
	}

	/** Get default row separator designs.
	 * @return array
	 */
	public function get_row_separator_designs() {
		return array(
			'none'           => __( 'Don\'t use row separator', 'iki-toolkit' ),
			'tilt-left-s'    => __( 'Tilt to left - small', 'iki-toolkit' ),
			'tilt-left-m'    => __( 'Tilt to left - medium', 'iki-toolkit' ),
			'tilt-left-l'    => __( 'Tilt to left - large', 'iki-toolkit' ),
			'tilt-right-s'   => __( 'Tilt to right - small', 'iki-toolkit' ),
			'tilt-right-m'   => __( 'Tilt to right - medium', 'iki-toolkit' ),
			'tilt-right-l'   => __( 'Tilt to right - large', 'iki-toolkit' ),
			'arrow-up'       => __( 'Arrow up center', 'iki-toolkit' ),
			'arrow-up-left'  => __( 'Arrow up left', 'iki-toolkit' ),
			'arrow-up-right' => __( 'Arrow up right', 'iki-toolkit' ),
			'circle-up'      => __( 'Circle', 'iki-toolkit' ),
			'wave'           => __( 'Wave', 'iki-toolkit' )
		);
	}

	/** Get portfolio project custom content
	 * @return array
	 */
	public function portfolio_project_custom_content() {
		$iki_custom_content = Iki_Toolkit_Hero_Section_Options::get_instance()->hero_section_custom_content();

		//unset options that are not available for project assets
		unset( $iki_custom_content['choices']['wp_editor'] );
		unset( $iki_custom_content['choices']['multiple_images'] );
		unset( $iki_custom_content['choices']['featured_image']['featured_image_html_info'] );

		unset( $iki_custom_content['picker']['chosen_content']['choices']['wp_editor'] );
		unset( $iki_custom_content['picker']['chosen_content']['choices']['multiple_images'] );

		//add asset grid
		$iki_custom_content['picker']['chosen_content']['choices']['asset_grid'] = __( 'Image Grid', 'iki-toolkit' );
		$iki_custom_content['choices']['asset_grid']                             = array(
			'images'           => array(
				'type'        => 'multi-upload',
				'value'       => array(),
				'label'       => __( 'Images', 'iki-toolkit' ),
				'images_only' => true,
			),
			'id'               => array(
				'type'    => 'select',
				'value'   => 'portrait_4',
				'label'   => __( 'Grid', 'iki-toolkit' ),
				'desc'    => __( 'Choose grid layout', 'iki-toolkit' ),
				'choices' => Iki_Toolkit_Admin_Grid_Options::get_instance()->get_wonder_grid_posts()
			),
			'animation'        => $this->get_animation_in_option(),
			'lightbox_design'  => array(
				'type'    => 'select',
				'label'   => __( 'Hover design', 'iki-toolkit' ),
				'value'   => 'symbol',
				'choices' => array(
					'symbol'  => __( 'Lightbox symbol', 'iki-toolkit' ),
					'caption' => __( 'Image caption', 'iki-toolkit' )
				)
			),
			'ajax_pagination'  => array(
				'label'        => __( 'Ajax pagination', 'iki-toolkit' ),
				'type'         => 'switch',
				'value'        => 'disabled',
				'desc'         => __( 'If enabled some of the images will be loaded via ajax.', 'iki-toolkit' ),
				'help'         => __( 'You can choose how many images you want loaded when the page loads. The rest of the images can be loaded on user request via ajax. ', 'iki-toolkit' ),
				'left-choice'  => array(
					'value' => 'disabled',
					'label' => __( 'Disabled', 'iki-toolkit' ),
				),
				'right-choice' => array(
					'value' => 'enabled',
					'label' => __( 'Enabled', 'iki-toolkit' ),
				)
			),
			'batch_image_load' => array(
				'type'    => 'select',
				'value'   => '4',
				'label'   => __( 'Number of images to load initially', 'iki-toolkit' ),
				'desc'    => __( 'Please note that ajax pagination needs to be enabled', 'iki-toolkit' ),
				'choices' => array(
					'all' => __( 'All at once', 'iki-toolkit' ),
					'1'   => 1,
					'2'   => 2,
					'3'   => 3,
					'4'   => 4,
					'5'   => 5,
					'6'   => 6,
					'7'   => 7,
					'8'   => 8,
					'9'   => 9,
					'10'  => 10,
					'11'  => 11,
					'12'  => 12,
					'13'  => 13,
					'14'  => 14,
					'15'  => 15,
					'16'  => 16,
					'17'  => 17,
					'18'  => 18,
					'19'  => 19,
					'20'  => 20,
				),
			),
			'design'           => array(
				'type'    => 'select',
				'value'   => 'default',
				'label'   => __( 'Grid design', 'iki-toolkit' ),
				'choices' => apply_filters( 'iki_admin_grid_portfolio_project_design_options', Iki_Toolkit_Admin_Grid_Options::get_instance()->get_portfolio_project_design() )
			)
		);


		$iki_custom_content['choices']['content_block'] = array(
			'id' => Iki_Toolkit_Admin_Options::get_instance()->get_content_block_top_option( array(
				'label' => __( 'Content block', 'iki-toolkit' )
			),
				false,
				'portfolio_project' )
		);

		return $iki_custom_content;
	}

	/** Portfolio project layout options
	 * @return array
	 */
	public function get_portfolio_project_layout() {
		return array(
			'type'    => 'multi-picker',
			'label'   => false,
			'desc'    => false,
			'value'   => array(),
			'picker'  => array(
				'chosen' => array(
					'type'    => 'select', // or 'short-select'
					'label'   => 'Project layout',
					'value'   => 'horizontal',
					'help'    => __( 'Project assets can be positioned horizontally or vertically on the page', 'iki-toolkit' ),
					'title'   => __( 'Project asset position', 'iki-toolkit' ),
					'choices' => array(
						'horizontal' => __( 'Horizontal', 'iki-toolkit' ),
						'vertical'   => __( 'Vertical', 'iki-toolkit' ),
					)
				)
			),
			'choices' => array(
				'horizontal' => array(
					'asset_position' => array(
						'type'    => 'select',
						'label'   => __( 'Asset position', 'iki-toolkit' ),
						'value'   => 'top',
						'choices' => array(
							'top'    => __( 'Top', 'iki-toolkit' ),
							'bottom' => __( 'Bottom', 'iki-toolkit' )
						)
					)
				),
				'vertical'   => array(
					'asset_width'    => array(
						'type'       => 'slider',
						'value'      => 60,
						'properties' => array(
							'min'  => 40,
							'max'  => 70,
							'step' => 1
						),
						'label'      => __( 'Asset panel width', 'iki-toolkit' ),
						'desc'       => __( 'Width of the asset panel in realation to project info content.', 'iki-toolkit' ),
						'help'       => __( 'Project asset panel can have custom width. Project info will take the rest of the width. For example if the asset panel width is 60% project info will have 40% width.', 'iki-toolkit' ),
					),
					'asset_position' => array(
						'type'    => 'select',
						'label'   => __( 'Asset position', 'iki-toolkit' ),
						'value'   => 'left',
						'choices' => array(
							'left'  => __( 'Left', 'iki-toolkit' ),
							'right' => __( 'Right', 'iki-toolkit' )
						)
					)
				)
			)
		);
	}
}//end class


