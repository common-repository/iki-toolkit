<?php

/**
 * Class for creating various options in backend
 */
class Iki_Toolkit_Admin_Options {


	private static $class = null;
	protected $content_blocks = null;
	protected $content_block_dropdown = array();
	protected $content_block_dropdown_with_disabled = array();
	protected $block_posts = array();
	protected $cached_share_design_options;

	protected $content_block_query = array();
	protected $cached_default_bg_options;

	/** Get class instance
	 * @return Iki_Toolkit_Admin_Options
	 */
	public static function get_instance() {
		if ( null === self::$class ) {
			self::$class = new self;
		}

		return self::$class;
	}

	/**
	 * Iki_Toolkit_Admin_Options constructor.
	 */
	public function __construct() {

		$cb_query                             = array(
			'post_type'        => 'iki_content_block',
			'suppress_filters' => 0,
			'numberposts'      => - 1,
			'orderby'          => array(
				'date' => 'DESC'
			),
			'post_status'      => 'publish',
		);
		$this->content_block_query['default'] = array(
			'tax_query' => array(
				'relation' => 'OR',
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'term_id',
					'terms'    => get_terms( array(
							'taxonomy' => 'iki_content_block_cat',
							'fields'   => 'ids'
						)
					),
					'operator' => 'NOT IN'
				),
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'global'
					),
					'operator' => 'IN'
				)
			)
		);

		$this->content_block_query['portfolio'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'portfolio',
						'global'
					)
				)
			)
		);

		$this->content_block_query['portfolio_archive'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'portfolio_archive',
						'global'
					)
				)
			)
		);

		$this->content_block_query['portfolio_project'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'portfolio_project'
					)
				)
			)
		);

		//woocommerce product
		$this->content_block_query['product'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'product',
						'global'
					)
				)
			)
		);

		$this->content_block_query['product_archive']     = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'product_archive',
						'global'
					)
				)
			)
		);
		$this->content_block_query['team_member']         = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'team_member',
						'global'
					)
				)
			)
		);
		$this->content_block_query['team_member_archive'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'team_member_archive',
						'global'
					)
				)
			)
		);

		$this->content_block_query['blog_archive'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'blog_archive',
						'global'
					)
				)
			)
		);

		$this->content_block_query['header'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'header',
						'global'
					)
				)
			)
		);

		$this->content_block_query['hero_section']           = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'hero_section',
						'global'
					)
				)
			)
		);
		$this->content_block_query['hero_section_portfolio'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'hero_section',
						'global',
						'hero_section_portfolio'
					)
				)
			)
		);

		$this->content_block_query['hero_section_product'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'hero_section',
						'global',
						'hero_section_product'
					)
				)
			)
		);

		$this->content_block_query['hero_section_team'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'hero_section',
						'global',
						'hero_section_team'
					)
				)
			)
		);
		$this->content_block_query['hero_section_post'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'hero_section',
						'global',
						'hero_section_post'
					)
				)
			)
		);
		$this->content_block_query['hero_section_page'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'hero_section',
						'global',
						'hero_section_page'
					)
				)
			)
		);
		$this->content_block_query['fs_panel']          = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'fs_panel',
						'global'
					)
				)
			)
		);
		$this->content_block_query['page']              = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'page',
						'global'
					)
				)
			)
		);
		$this->content_block_query['post']              = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'post',
						'global'
					)
				)
			)
		);

		$this->content_block_query['author'] = array(
			'tax_query' => array(
				array(
					'taxonomy' => 'iki_content_block_cat',
					'field'    => 'slug',
					'terms'    => array(
						'author',
						'global'
					)
				)
			)
		);

		$this->content_block_query['default'] = array_merge_recursive( $cb_query,
			$this->content_block_query['default'] );

		$this->content_block_query['portfolio'] = array_merge_recursive( $cb_query,
			$this->content_block_query['portfolio'] );

		$this->content_block_query['portfolio_archive'] = array_merge_recursive( $cb_query,
			$this->content_block_query['portfolio_archive'] );

		$this->content_block_query['product'] = array_merge_recursive( $cb_query,
			$this->content_block_query['product'] );

		$this->content_block_query['product_archive'] = array_merge_recursive( $cb_query,
			$this->content_block_query['product_archive'] );

		$this->content_block_query['team_member'] = array_merge_recursive( $cb_query,
			$this->content_block_query['team_member'] );

		$this->content_block_query['blog_archive'] = array_merge_recursive( $cb_query,
			$this->content_block_query['blog_archive'] );

		$this->content_block_query['team_member_archive'] = array_merge_recursive( $cb_query,
			$this->content_block_query['team_member_archive'] );

		$this->content_block_query['portfolio_project'] = array_merge_recursive( $cb_query,
			$this->content_block_query['portfolio_project'] );

		$this->content_block_query['header'] = array_merge_recursive( $cb_query,
			$this->content_block_query['header'] );

		$this->content_block_query['hero_section'] = array_merge_recursive( $cb_query,
			$this->content_block_query['hero_section'] );

		$this->content_block_query['page'] = array_merge_recursive( $cb_query,
			$this->content_block_query['page'] );

		$this->content_block_query['post'] = array_merge_recursive( $cb_query,
			$this->content_block_query['post'] );

		$this->content_block_query['hero_section_portfolio'] = array_merge_recursive( $cb_query,
			$this->content_block_query['hero_section_portfolio'] );

		$this->content_block_query['hero_section_product'] = array_merge_recursive( $cb_query,
			$this->content_block_query['hero_section_product'] );
		$this->content_block_query['hero_section_team']    = array_merge_recursive( $cb_query,
			$this->content_block_query['hero_section_team'] );


		$this->content_block_query['hero_section_post'] = array_merge_recursive( $cb_query,
			$this->content_block_query['hero_section_post'] );

		$this->content_block_query['hero_section_page'] = array_merge_recursive( $cb_query,
			$this->content_block_query['hero_section_page'] );

		$this->content_block_query['fs_panel'] = array_merge_recursive( $cb_query,
			$this->content_block_query['fs_panel'] );

		$this->content_block_query['author'] = array_merge_recursive( $cb_query,
			$this->content_block_query['author'] );
	}

	/**
	 * @param null $additional_data
	 * @param bool $add_dont_use_option
	 * @param string $for
	 *
	 * @return array
	 */
	public function get_content_block_top_option( $additional_data = null, $add_dont_use_option = true, $for = 'default' ) {

		$r = array(
			'type'    => 'select',
			'value'   => '',
			'label'   => __( 'Content block top', 'iki-toolkit' ),
			'choices' => $this->get_content_blocks( $add_dont_use_option, $for )
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}

	/**
	 * @param null $additional_data
	 * @param bool $add_dont_use_option
	 * @param string $for
	 *
	 * @return array
	 */
	public function get_content_block_bottom_option( $additional_data = null, $add_dont_use_option = true, $for = 'default' ) {

		$r = array(
			'type'    => 'select',
			'value'   => 'disabled',
			'label'   => __( 'Content block bottom', 'iki-toolkit' ),
			'choices' => $this->get_content_blocks( $add_dont_use_option, $for )
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}

	public function get_content_blocks( $addDontUseOption = true, $for = 'default' ) {

		if ( ! isset( $this->block_posts[ $for ] ) ) {

			$this->content_block_dropdown[ $for ]               = array();
			$this->content_block_dropdown_with_disabled[ $for ] = array();
			$block_query                                        = $this->content_block_query[ $for ];

			$this->block_posts[ $for ] = get_posts( $block_query );

			$this->content_block_dropdown_with_disabled[ $for ]['disabled'] = '--- ' . __( 'Disabled', 'iki-toolkit' );

			if ( $this->block_posts[ $for ] ) {

				foreach ( $this->block_posts[ $for ] as $block ) {
					$this->content_block_dropdown[ $for ][ $block->ID ]               = $block->post_title;
					$this->content_block_dropdown_with_disabled[ $for ][ $block->ID ] = $block->post_title;
				}
			} else {

				$no_block_available                                             = __( 'No content blocks available', 'iki-toolkit' );
				$this->content_block_dropdown[ $for ]['disabled']               = $no_block_available;
				$this->content_block_dropdown_with_disabled[ $for ]['disabled'] = $no_block_available;
			}

		}

		if ( $addDontUseOption ) {
			return $this->content_block_dropdown_with_disabled[ $for ];
		} else {
			return $this->content_block_dropdown[ $for ];
		}
	}

	/** Check options cache
	 *
	 * @param string $key_prefix options prefix
	 * @param array $additional_data additional data
	 * @param string $cache_container option container name
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

	/** Get options for social sharing design
	 *
	 * @param null $additional_data
	 *
	 * @return array|mixed|null
	 */
	public function get_share_design( $additional_data = null ) {

		$cache = $this->check_cache( '', $additional_data, $this->cached_share_design_options );
		if ( $cache ) {
			return $cache;
		}

		$file_location = plugin_dir_url( __FILE__ );

		$r = array(
			'type'         => 'multi-picker',
			'label'        => false,
			'value'        => array(
				'chosen_design' => 'pre_made',

				'pre_made'          => array(
					'design' => 'classic-dark',
				),
				'custom_symbol'     => array(
					'fg' => '#000000',
				),
				'custom_background' => array(
					'fg' => 'rgba(255,255,255,1)',
					'bg' => 'rgba(0,0,0,1)',

				)
			),
			'picker'       => array(
				'chosen_design' => array(
					'label'   => __( 'Design for social media buttons', 'iki-toolkit' ),
					'desc'    => __( 'Please note that you need to setup your social profiles via "iki toolkit" plugin.', 'iki-toolkit' ) . '</br>' . _x( 'Settings ->iki toolkit -> social profiles', 'Location of the options', 'iki-toolkit' ),
					'type'    => 'select',
					'choices' => array(
						'pre_made'          => __( 'Pre-made design', 'iki-toolkit' ),
						'custom_symbol'     => __( 'Symbol only color', 'iki-toolkit' ),
						'custom_background' => __( 'Symbol and background', 'iki-toolkit' )
					),
				)
			),
			'choices'      => array(
				'custom_symbol'     => array(
					'fg'      => array(
						'type'  => 'color-picker',
						'value' => $GLOBALS['iki_toolkit_admin']['colors']['social_icons']['custom_symbol']['fg'],
						'label' => __( 'Symbol Color', 'iki-toolkit' ),
						'help'  => __( 'This option let\'s you change only the symbol color.', 'iki-toolkit' ),
					),
					'rounded' => array(
						'type'  => 'checkbox',
						'value' => 0,
						'label' => __( 'Make icons round', 'iki-toolkit' ),
						'text'  => __( 'Yes', 'iki-toolkit' ),
					),
				),
				'custom_background' => array(
					'fg'      => array(
						'type'  => 'color-picker',
						'value' => $GLOBALS['iki_toolkit_admin']['colors']['social_icons']['custom_background']['fg'],
						'label' => __( 'Symbol color', 'iki-toolkit' ),
					),
					'bg'      => array(
						'type'  => 'rgba-color-picker',
						'value' => $GLOBALS['iki_toolkit_admin']['colors']['social_icons']['custom_background']['bg'],
						'label' => __( 'Background color', 'iki-toolkit' ),
					),
					'rounded' => array(
						'type'  => 'checkbox',
						'value' => 0,
						'label' => __( 'Make icons round', 'iki-toolkit' ),
						'help'  => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive. "Stretch" option will take precedence', 'iki-toolkit' ),
						'text'  => __( 'Yes', 'iki-toolkit' ),
					),
					'spread'  => array(
						'type'  => 'checkbox',
						'value' => 0,
						'label' => __( 'Strech icon width', 'iki-toolkit' ),
						'desc'  => __( 'Make icons wide as their container', 'iki-toolkit' ),
						'help'  => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive. "Stretch" option will take precedence', 'iki-toolkit' ),
						'text'  => __( 'Yes', 'iki-toolkit' ),
					)
				),
				'pre_made'          => array(
					'design'  => array(
						'type'    => 'image-picker',
						'value'   => 'classic-dark',
						'label'   => __( 'Pre-made designs', 'iki-toolkit' ),
						'choices' => array(
							'classic-dark'  => array(
								'small' => $file_location . '../../images/admin/social-design/dark-small.png',
								'large' => $file_location . '../../images/admin/social-design/dark-large.png',
							),
							'classic-light' => array(
								'small' => $file_location . '../../images/admin/social-design/light-small.png',
								'large' => $file_location . '../../images/admin/social-design/light-large.png',
							),
							'service'       => array(
								'small' => $file_location . '../../images/admin/social-design/service-small.png',
								'large' => $file_location . '../../images/admin/social-design/service-large.png',
							)
						),
						'blank'   => false, // (optional) if true, images can be deselected
					),
					'rounded' => array(
						'type'  => 'checkbox',
						'value' => 0,
						'label' => __( 'Make icons round', 'iki-toolkit' ),
						'help'  => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive', 'iki-toolkit' ),
						'text'  => __( 'Yes', 'iki-toolkit' ),
					),
					'spread'  => array(
						'type'  => 'checkbox',
						'value' => 0,
						'label' => __( 'Strech icon width', 'iki-toolkit' ),
						'desc'  => __( 'Make icons wide as their container', 'iki-toolkit' ),
						'help'  => __( 'Please note that "rounded" icon and "stretch" icon options are mutually exclusive', 'iki-toolkit' ),
						'text'  => __( 'Yes', 'iki-toolkit' ),
					)
				),
			),
			'show_borders' => true,
		);

		$this->cached_share_design_options = $this->maybe_cache_options( $r, '', $additional_data, $this->cached_share_design_options );

		return $this->maybe_replace_options( $r, $additional_data );

	}

	/** Maybe cache options
	 *
	 * @param array $options options to cache
	 * @param string $key_prefix options prefix
	 * @param array $additional_data additional options data
	 * @param string $cache_container container for options
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

	/** Remove options with null values
	 *
	 * @param array $haystack haystack of options
	 *
	 * @return array with or  withouth null options
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
			'value'       => isset( $GLOBALS['iki_toolkit_admin'] ) ? $GLOBALS['iki_toolkit_admin']['body']['background']['url'] : '',
			'label'       => __( 'Image', 'iki-toolkit' ),
			'images_only' => true
		);

		$backgroundRepeat = array(
			'type'    => 'radio',
			'value'   => isset( $GLOBALS['iki_toolkit_admin'] ) ? $GLOBALS['iki_toolkit_admin']['body']['background']['repeat'] : 'no-repeat',
			'label'   => __( 'Repeat', 'iki-toolkit' ),
			'choices' => array(
				'no-repeat' => __( 'No repeat', 'iki-toolkit' ),
				'repeat'    => __( 'Repeat', 'iki-toolkit' ),
				'repeat-x'  => __( 'Repeat X', 'iki-toolkit' ),
				'repeat-y'  => __( 'Repeat Y', 'iki-toolkit' ),
			),
			'inline'  => false,
		);

		$backgroundSize = array(
			'type'    => 'radio',
			'value'   => isset( $GLOBALS['iki_toolkit_admin'] ) ? $GLOBALS['iki_toolkit_admin']['body']['background']['size'] : 'cover',
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
			'value'   => isset( $GLOBALS['iki_toolkit_admin'] ) ? $GLOBALS['iki_toolkit_admin']['body']['background']['position'] : 'top left',
			'label'   => __( 'Position', 'iki-toolkit' ),
			'choices' => array(
				'top left'      => __( 'Top Left', 'iki-toolkit' ),
				'top center'    => __( 'Top Center', 'iki-toolkit' ),
				'top right'     => __( 'Top Right', 'iki-toolkit' ),
				'left center'   => __( 'Left Center', 'iki-toolkit' ),
				'center center' => __( 'Center Center', 'iki-toolkit' ),
				'right bottom'  => __( 'Right Bottom', 'iki-toolkit' ),
				'bottom left'   => __( 'Bottom Left', 'iki-toolkit' ),
				'bottom center' => __( 'Bottom Center', 'iki-toolkit' ),
				'bottom right'  => __( 'Bottom Right', 'iki-toolkit' ),

			)
		);


		$backgroundAttachment = array(
			'type'    => 'select',
			'label'   => __( 'Attachment', 'iki-toolkit' ),
			'value'   => isset( $GLOBALS['iki_toolkit_admin'] ) ? $GLOBALS['iki_toolkit_admin']['body']['background']['attachment'] : 'scroll',
			'choices' => array(
				'scroll' => __( 'Scroll', 'iki-toolkit' ),
				'fixed'  => __( 'Fixed', 'iki-toolkit' ),
			)
		);

		$r['color'] = array(
			'type'  => 'color-picker',
			'value' => isset( $GLOBALS['iki_toolkit_admin'] ) ? $GLOBALS['iki_toolkit_admin']['colors']['body']['color_bg'] : '#ffffff',
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


	/** Get blog author options
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_blog_authors( $additional_data = null ) {

		$r = array(
			'type'  => 'select',
			'value' => '',
			'label' => __( 'Blog author', 'iki-toolkit' ),
			'desc'  => __( 'Chose blog author to be associated with current team member.', 'iki-toolkit' ),
			'help'  => __( 'If this team member is associated with blog author, blog posts from chosen author will be displayed as if they are from team member itself.', 'iki-toolkit' ),
		);

		if ( is_admin() ) {

			$authors = Iki_Post_Utils::get_blog_authors();
			$choices = array(
				'' => '--- ' . __( 'Don\'t associate this team member with blog author', 'iki-toolkit' )
			);

			if ( ! $authors ) {
				$choices[''] = __( 'No blog authors found', 'iki-toolkit' );
			}

			foreach ( $authors as $author ) {
				$choices[ $author->data->ID ] = $author->data->display_name . ' ( ' . $author->data->user_login . ' ) ';
			}

			$r['choices'] = $choices;
			if ( $additional_data ) {
				$r = array_replace_recursive( $r, $additional_data );
			}
		}

		return $r;
	}

	/** Get contacts options
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_contacts( $additional_data = null ) {

		$r = array(
			'fake_separator' => array(
				'type'  => 'html',
				'value' => '',
				'label' => '',
				'desc'  => '',
				'html'  => sprintf( '<h3>%1$s</h3><p>%2$s</p>',
					__( 'Setup links to social profiles for this team member.', 'iki-toolkit' ),
					__( 'These contact links will be shown if "Use author contact data" is set to "No"', 'iki-toolkit' ) )
			),
			'facebook'       => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Facebook', 'iki-toolkit' ),
				'desc'  => __( 'Link to Facebook profile', 'iki-toolkit' ),
			),
			'twitter'        => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Twitter', 'iki-toolkit' ),
				'desc'  => __( 'Link to Twitter profile', 'iki-toolkit' ),
			),
			'linkedin'       => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'LinkedIn', 'iki-toolkit' ),
				'desc'  => __( 'Link to LinkedIn profile', 'iki-toolkit' ),
			),
			'vk'             => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'VK', 'iki-toolkit' ),
				'desc'  => __( 'Link to VK profile', 'iki-toolkit' ),
			),
			'weibo'          => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Weibo', 'iki-toolkit' ),
				'desc'  => __( 'Link to Weibo profile', 'iki-toolkit' ),
			),
			'pinterest'      => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Pinterest', 'iki-toolkit' ),
				'desc'  => __( 'Link to Pinterest profile', 'iki-toolkit' ),
			),
			'reddit'         => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Reddit', 'iki-toolkit' ),
				'desc'  => __( 'Link to Reddit profile', 'iki-toolkit' ),
			),
			'tumblr'         => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Tumblr', 'iki-toolkit' ),
				'desc'  => __( 'Link to Tumblr profile', 'iki-toolkit' ),
			),
			'lastFM'         => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'LastFM', 'iki-toolkit' ),
				'desc'  => __( 'Link to LastFM profile', 'iki-toolkit' ),
			),
			'myspace'        => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'MySpace', 'iki-toolkit' ),
				'desc'  => __( 'Link to MySpace profile', 'iki-toolkit' ),
			),
			'instagram'      => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Instagram', 'iki-toolkit' ),
				'desc'  => __( 'Link to Instagram profile', 'iki-toolkit' ),
			),
			'dribbble'       => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Dribbble', 'iki-toolkit' ),
				'desc'  => __( 'Link to Dribbble profile', 'iki-toolkit' ),
			),
			'flickr'         => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'Flickr', 'iki-toolkit' ),
				'desc'  => __( 'Link to Flickr profile', 'iki-toolkit' ),
			),
			'500px'          => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( '500px', 'iki-toolkit' ),
				'desc'  => __( 'Link to 500px profile', 'iki-toolkit' ),
			),
			'github'         => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'GitHub', 'iki-toolkit' ),
				'desc'  => __( 'Link to GitHub profile', 'iki-toolkit' ),
			),
			'bitbucket'      => array(
				'type'  => 'text',
				'value' => '',
				'label' => __( 'BitBucket', 'iki-toolkit' ),
				'desc'  => __( 'Link to BitBucket profile', 'iki-toolkit' ),
			)
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;
	}

	/** Get similar posts options depending on the type of the post
	 *
	 * @param string $post_type post type
	 *
	 * @return array
	 */
	public function get_similar_posts( $post_type = 'post' ) {
		if ( 'post' == $post_type ) {

			$similar_grid_design = $this->get_blog_grid_template_option();
		} elseif ( 'iki_portfolio' == $post_type ) {

			$similar_grid_design = $this->get_portfolio_grid_templates_option();
		} elseif ( 'iki_team_member' == $post_type ) {

			$similar_grid_design = $this->get_team_grid_template_option();
		}

		return array(
			'type'    => 'box',
			'title'   => __( 'Similar post options', 'iki-toolkit' ),
			'options' => array(
				'similar_grid_id'        => Iki_Toolkit_Admin_Grid_Options::get_instance()->get_wonder_grid_post_option( array(
					'label' => __( 'Grid', 'iki-toolkit' )
				) ),
				'similar_grid_template'  => $similar_grid_design,
				'similar_grid_animation' => Iki_Toolkit_Velocity_Options::get_instance()->get_animation_in_option( array(
					'label' => __( 'Animation', 'iki-toolkit' ),
					'desc'  => __( 'Animation for grid thumbs', 'iki-toolkit' )
				), false ),
				'similar_posts_per_page' => array(
					'type'    => 'select',
					'value'   => '4',
					'label'   => __( 'Number of posts', 'iki-toolkit' ),
					'desc'    => __( 'Initial number of posts to show. Every ajax query will also load the same number of posts', 'iki-toolkit' ),
					'choices' => array(
						'1'  => 1,
						'2'  => 2,
						'3'  => 3,
						'4'  => 4,
						'5'  => 5,
						'6'  => 6,
						'7'  => 7,
						'8'  => 8,
						'9'  => 9,
						'10' => 10
					),
				)
			)
		);
	}

	/** Get grid template options
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_blog_grid_template_option( $additional_data = null ) {

		$r = array(
			'type'    => 'select',
			'value'   => 'default',
			'label'   => __( 'Design', 'iki-toolkit' ),
			'desc'    => __( 'Choose the design for the grid', 'iki-toolkit' ),
			'choices' => apply_filters( 'iki_admin_grid_blog_design_options', Iki_Toolkit_Admin_Grid_Options::get_instance()->get_blog_design(), false )
			//false == not for visual composer
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}

	/** Get team grid template options
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_team_grid_template_option( $additional_data = null ) {

		$r = array(
			'type'    => 'select',
			'value'   => 'default',
			'label'   => __( 'Team Grid Templates', 'iki-toolkit' ),
			'desc'    => __( 'Choose template for grid', 'iki-toolkit' ),
			'choices' => apply_filters( 'iki_admin_grid_team_design_options', Iki_Toolkit_Admin_Grid_Options::get_instance()->get_team_design(), false )
		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}

	/** Get portfolio grid template options
	 *
	 * @param null $additional_data
	 *
	 * @return array
	 */
	public function get_portfolio_grid_templates_option( $additional_data = null ) {
		$r = array(
			'type'    => 'select',
			'value'   => 'default',
			'label'   => __( 'Portfolio Grid Templates', 'iki-toolkit' ),
			'desc'    => __( 'Choose template for grid', 'iki-toolkit' ),
			'choices' => apply_filters( 'iki_admin_grid_portfolio_design_options', Iki_Toolkit_Admin_Grid_Options::get_instance()->get_portfolio_design(), false )
			//false == not for visual composer

		);

		if ( $additional_data ) {
			$r = array_replace_recursive( $r, $additional_data );
		}

		return $r;

	}
}
