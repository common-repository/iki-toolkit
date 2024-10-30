<?php

/**
 * Generic class for featured posts options section
 */
class Iki_Admin_Featured_Posts extends Iki_Admin_Options_Section {

	//what post type is goint to be used?
	protected $post_type;

	/**
	 * Iki_Admin_Featured_Posts constructor.
	 *
	 * @param int $index section position in options
	 * @param array $options Options for creating the class
	 */
	public function __construct( $index = 10, $options ) {

		parent::__construct( $index );

		$this->wrap_class           = 'iki-search-posts-form';
		$this->name                 = 'featured_blog_posts';
		$this->name                 = $options['settings_id'];
		$this->title                = $options['title'];
		$this->post_type            = $options['post_type'];
		$this->option_name          = $options['option_name'];//'iki_toolkit_feat_blog_posts';
		$this->settings_sections_id = $options['settings_id'];//'iki_toolkit_feat_blog_posts_section';

		add_filter( 'iki_toolkit_exports', array( $this, 'export_translations' ) );
	}

	/** Export optional translations for javascript
	 *
	 * @param $exports
	 *
	 * @return mixed
	 */
	public function export_translations( $exports ) {
		return $exports;
	}

	/** Add needed scripts
	 *
	 * @param $hook
	 */
	public function add_section_scripts( $hook ) {

		parent::add_section_scripts( $hook );

		if ( 'settings_page_iki_toolkit_options' == $hook ) {

			wp_enqueue_script( 'iki-admin-search-posts',
				IKI_TOOLKIT_ROOT_URL . 'js/admin/admin-featured-posts.min.js',
				array( 'underscore' )
			);
		}
	}

	/**
	 * Print section description
	 */
	public function section_description() {
		echo '<p class="iki-explain">' . __( 'Depending on the theme, featured posts can be shown in various places (hero section, sidebar, footer)', 'iki-toolkit' ) . '</p>';
	}


	/**
	 * Setup section option fields
	 */
	protected function setup_option_fields() {

		add_settings_field(
			'posts',
			'',
			array( $this, 'print_search_form' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'post_type' => $this->post_type,
			)
		);


		add_settings_field(
			'remove_from_query',
			'',
			array( $this, 'print_query' ),
			$this->option_name,
			$this->settings_sections_id,
			array(
				'post_type' => $this->post_type,
			)
		);
	}

	/**
	 * Print "remove from query" UI
	 */
	public function print_query() {

		$options = get_option( $this->option_name );

		$options['remove_from_query'] = isset( $options['remove_from_query'] ) ? $options['remove_from_query'] : '';

		printf( '<label class="iki-rm-query-chb">%3$s<input type="checkbox" name="%2$s[remove_from_query]" %1$s ></label>',
			checked( $options['remove_from_query'], 'on', false ),
			$this->option_name,
			__( 'Remove featured post from main query', 'iki-toolkit' ) );
	}

	/** Print section options
	 *
	 * @param $data array section data
	 */
	public function print_search_form( $data ) {

		$options = get_option( $this->option_name );

		printf( '<div class="iki-search-posts-wrap" id="iki-search-posts-wrap" data-iki-type="%1$s" data-iki-option-name="%2$s">',
			$data['post_type'],
			$this->option_name
		);

		echo '<div class="iki-search-posts-ui-wrap">';
		printf( '<label>%1$s<input type="text" id="iki-search-post" value="" /></label><span id="iki-spinner" class="spinner"></span>', __( 'Search', 'iki-toolkit' ) );
		printf( '<p id="iki-posts-not-found" class="iki-posts-not-found hidden">%1$s</p>', __( 'No posts found', 'iki-toolkit' ) );
		printf( '<p id="iki-posts-error" class="iki-posts-error iki-ajax-notif error notice hidden">%1$s</p><span class="iki-server-error"></span>', __( 'Server error', 'iki-toolkit' ) );
		printf( '<div id="iki-found-posts" class="iki-found-posts"></div>' );
		printf( '<button id="iki-add-post"class="iki-add-post button">%1$s</button>', __( 'Add selected posts', 'iki-toolkit' ) );
		echo '</div>';

		$selected_posts = '';

		if ( ! empty( $options['posts'] ) ) {

			foreach ( $options['posts'] as $id ) {
				$selected_posts .= sprintf( '<li class="iki-title-wrap" data-iki-id="%2$s"><span class="content button iki-remove-post">X</span><p class="iki-post-title"><a href="%5$s" target="_blank">%1$s</a></p><input type="hidden" name="%4$s[posts][%2$s]" value="%2$s" /></li>',
					get_the_title( $id ),
					$id,
					__( 'Remove post', 'iki-toolkit' ),
					$this->option_name,
					get_edit_post_link( $id )
				);
			}
		}

		printf( '<div id="iki-selected-posts-ui-wrap" class="iki-selected-posts-ui-wrap"><span class="iki-featured-posts">%2$s</span><ul id="iki-selected-posts">%1$s</ul></div>',
			$selected_posts,
			__( 'Featured Posts', 'iki-toolkit' )
		);

		echo '</div>';
	}

	/** Sanitize api key options
	 *
	 * @param array $input data to sanitize
	 *
	 * @return mixed sanitized data
	 */
	public function sanitize_options( $input ) {

		// Define the array for the updated options
		$output = array();
		// Loop through each of the options sanitizing the data
		if ( isset( $input['posts'] ) ) {
			$output['posts'] = array();
			foreach ( $input['posts'] as $key => $val ) {

				$output['posts'][] = sanitize_text_field( $val );
			} // end foreach
		}

		if ( isset( $input['remove_from_query'] ) ) {
			$output['remove_from_query'] = sanitize_text_field( $input['remove_from_query'] );
		}

		// Return the new collection
		return apply_filters( 'iki_toolkit_sanitize_featured_posts', $output, $input, $this->post_type );

	} // end sandbox_theme_sanitize_social_options

	/** Set default options
	 * @return mixed|void
	 */
	public function default_options() {
		// noop
	}
}

/*Class for creating options for featured posts on single post pages*/

class Iki_Toolkit_Featured_Posts_Single_Options {


	/** Get all the options
	 * @return array
	 */
	public static function get_single_featured_options() {

		return array(
			'type'    => 'box',
			'title'   => __( 'Featured post', 'iki-toolkit' ),
			'options' => array(
				'featured_exp'                     => array(
					'type'  => 'html',
					'value' => '',
					'label' => false,
					'desc'  => false,
					'html'  => __( 'Modify hero section (if available) of this post when it get\'s featured in some other hero section.', 'iki-toolkit' )
				),
				'featured_rm_video_bg'             => array(
					'type'  => 'checkbox',
					'value' => true, // checked/unchecked
					'label' => __( 'Remove video background', 'iki-toolkit' ),
					'desc'  => __( 'Remove video background when this post get\'s featured in some hero section', 'iki-toolkit' ),
					'help'  => __( 'If this post has video background in hero section, when it get\'s featured in some hero section, only image background will be shown. Video background will be removed', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
				),
				'featured_text_above_title'        => array(
					'type'  => 'text',
					'value' => '',
					'label' => __( 'Featured text above post title', 'iki-toolkit' ),
				),
				'featured_rm_title'                => array(
					'type'  => 'checkbox',
					'value' => false, // checked/unchecked
					'label' => __( 'Remove post title', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
				),
				'featured_rm_subtitle'             => array(
					'type'  => 'checkbox',
					'value' => false, // checked/unchecked
					'label' => __( 'Remove post subtitle', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
				),
				'featured_rm_image_bg'             => array(
					'type'  => 'checkbox',
					'value' => false,
					'label' => __( 'Remove image and color background', 'iki-toolkit' ),
					'help'  => __( 'If this post has image and color background in hero section, when it get\'s featured in some hero section image and color background, as well as "overlay" will be removed.', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
				),
				"featured_rm_custom_content"       => array(
					'label'        => __( 'Remove custom content', 'iki-toolkit' ),
					'desc'         => __( 'Remove custom content from hero section.', 'iki-toolkit' ),
					'help'         => __( 'If this post has custom content in hero section, when it get\'s featured in some hero section, custom content will be removed.', 'iki-toolkit' ),
					'type'         => 'switch',
					'value'        => 'disabled',
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "featured_use_post_excerp",
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
				'featured_use_post_excerp'         => array(
					'type'  => 'checkbox',
					'value' => false,
					'label' => __( 'Show post excerp', 'iki-toolkit' ),
					'desc'  => __( 'Show post excerp instead of custom content', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
				),
				"featured_row_separator"           => array(
					'type'    => 'select',
					'value'   => 'from_parent',
					'label'   => __( 'Row separator', 'iki-toolkit' ),
					'desc'    => __( 'Remove row separator from hero section.', 'iki-toolkit' ),
					'help'    => __( 'If this post has row separator in hero section, when it get\'s featured in some hero section, row separator will be removed. You can also choose to use the row separtor from the hero section where the post is going to be featured by choosing "Use parent row separator"', 'iki-toolkit' ),
					'choices' => array(
						'remove'      => __( 'Remove', 'iki-toolkit' ),
						'keep'        => __( 'Keep', 'iki-toolkit' ),
						'from_parent' => __( 'Use parent row separator', 'iki-toolkit' ),
					)
				),
				'featured_override_content_layout' => array(
					'label'        => __( 'Change content layout', 'iki-toolkit' ),
					'type'         => 'switch',
					'value'        => 'diabled',
					'desc'         => __( 'Change this post hero section content layout when it get\'s featured.', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "featured_content_group",
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
				'featured_content_group'           => array(
					'type'    => 'group',
					'options' => array(
						"featured_horizontal_alignment" => array(
							'type'    => 'select',
							'value'   => 'from_parent',
							'label'   => __( 'Content horizontal alignment', 'iki-toolkit' ),
							'desc'    => __( 'Horizontal alignment of the content inside the hero section', 'iki-toolkit' ),
							'help'    => __( 'You can also choose to use alignment from the hero section where the post is going to be featured by choosing "Use alignment setup by parent"', 'iki-toolkit' ),
							'choices' => array(
								'from_parent' => __( 'Use alignment setup by parent', 'iki-toolkit' ),
								'center'      => __( 'Center', 'iki-toolkit' ),
								'left'        => __( 'Left', 'iki-toolkit' ),
								'fixed-left'  => __( 'Fixed Left', 'iki-toolkit' )
							),
						),
						"featured_vertical_alignment"   => array(
							'type'    => 'select',
							'value'   => 'from_parent',
							'label'   => __( 'Content vertical alignment', 'iki-toolkit' ),
							'desc'    => __( 'Vertical alignment of the content inside the hero section', 'iki-toolkit' ),
							'help'    => __( 'You can also choose to use alignment from the hero section where the post is going to be featured by choosing "Use alignment setup by parent"', 'iki-toolkit' ),
							'choices' => array(
								'from_parent' => __( 'Use alignment setup by parent', 'iki-toolkit' ),
								'top'         => __( 'Top', 'iki-toolkit' ),
								'center'      => __( 'Center', 'iki-toolkit' ),
								'bottom'      => __( 'Bottom', 'iki-toolkit' ),
							)
						),

					)
				),
				"featured_override_hs_layout"      => array(
					'label'        => __( 'Change hero section layout', 'iki-toolkit' ),
					'type'         => 'switch',
					'value'        => 'disabled',
					'desc'         => __( 'Change this post hero section layout when it get\'s featured in some other hero section', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "featured_hs_layout_group",
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
				'featured_hs_layout_group'         => array(
					'type'    => 'group',
					'options' => array(
						"featured_hs_width"  => array(
							'type'    => 'select',
							'value'   => 'from_parent',
							'label'   => __( 'Width', 'iki-toolkit' ),
							'desc'    => __( 'Width of the hero section', 'iki-toolkit' ),
							'help'    => __( 'You can also choose to use the width from the hero section where the post is going to be featured by choosing "Use width setup by parent"', 'iki-toolkit' ),
							'choices' => array(
								'from_parent' => __( 'Use width setup by parent', 'iki-toolkit' ),
								'fixed'       => __( 'Theme fixed width', 'iki-toolkit' ),
								'full'        => __( 'Browser width', 'iki-toolkit' ),
							)
						),
						"featured_hs_height" => array(
							'type'    => 'select',
							'value'   => 'from_parent',
							'label'   => __( 'Height', 'iki-toolkit' ),
							'desc'    => __( 'Height of hero section', 'iki-toolkit' ),
							'help'    => __( 'You can also choose to use the height from the hero section where the post is going to be featured by choosing "Use height setup by parent"', 'iki-toolkit' ),
							'choices' => array(
								'from_parent' => __( 'Use height setup by parent', 'iki-toolkit' ),
								'default'     => __( 'Default', 'iki-toolkit' ),
								'medium'      => __( 'Medium', 'iki-toolkit' ),
								'large'       => __( 'Large', 'iki-toolkit' ),
								'xl'          => __( 'Extra large (XL)', 'iki-toolkit' ),
								'xxl'         => __( 'Extra extra large (XXL)', 'iki-toolkit' ),
								'full'        => __( 'Full browser height', 'iki-toolkit' ),
							)
						),
					)
				),
				'featured_rm_custom_colors'        => array(
					'type'  => 'checkbox',
					'value' => false,
					'label' => __( 'Remove custom colors', 'iki-toolkit' ),
					'desc'  => __( 'Remove hero section custom colors', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
					'help'  => __( 'You can remove the custom colors from hero section when this post get\'s featured in some hero section.', 'iki-toolkit' ),
				),
				'featured_sign_text'               => array(
					'type'  => 'text',
					'value' => '',
					'label' => __( 'Featured sign text', 'iki-toolkit' ),
					'help'  => __( 'Leave empty for default text', 'iki-toolkit' )
				),
				'featured_sign_colors'             => array(
					'label'        => __( 'Custom featured sign colors', 'iki-toolkit' ),
					'type'         => 'switch',
					'value'        => 'disabled',
					'desc'         => __( 'Featured sign can have custom colors', 'iki-toolkit' ),
					'attr'         => array(
						'data-iki-switch'  => 1,
						'data-iki-for'     => "featured_sign_colors_group",
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
				'featured_sign_colors_group'       => array(
					'type'    => 'group',
					'options' => array(
						"feat_sign_bg_color"   => array(
							'type'  => 'rgba-color-picker',
							'value' => 'rgba(0,0,0,0.3)',
							'label' => __( 'Background', 'iki-toolkit' )
						),
						"feat_sign_text_color" => array(
							'type'  => 'color-picker',
							'value' => '#ffffff',
							'label' => __( 'Text color', 'iki-toolkit' )
						),
					)
				),
				'featured_add_link'                => array(
					'type'  => 'checkbox',
					'value' => false,
					'label' => __( 'Add read more link', 'iki-toolkit' ),
					'desc'  => __( 'Link to the post page', 'iki-toolkit' ),
					'text'  => __( 'Yes', 'iki-toolkit' ),
				),

				"featured_link_design" => array(
					'type'    => 'select',
					'value'   => 'hollow',
					'label'   => __( 'Read more link design', 'iki-toolkit' ),
					'choices' => array(
						'default'   => __( 'Simple', 'iki-toolkit' ),
						'hollow'    => __( 'Hollow', 'iki-toolkit' ),
						'h-round-1' => __( 'Hollow round', 'iki-toolkit' ),
						'h-round-2' => __( 'Hollow round 2', 'iki-toolkit' ),
						'h-round-3' => __( 'Hollow round 3', 'iki-toolkit' ),
					)
				),
			)
		);
	}
}


/** add options to single post options
 *
 * @param $options
 * @param $post_type
 *
 * @return mixed
 */
function _filter_iki_toolkit_post_single_page_options( $options, $post_type ) {

	if ( current_theme_supports( 'iki-toolkit-featured-posts' ) ) {
		$options['6_1_featured'] = Iki_Toolkit_Featured_Posts_Single_Options::get_single_featured_options();
	}
	if ( 'product' == $post_type ) {
		//change "read more link admin options to "shop now";
		$options['6_1_featured']['options']['featured_add_link']['label'] = __( 'Add "shop now" button"', 'iki-toolkit' );
		$options['6_1_featured']['options']['featured_add_link']['desc']  = __( 'Link to the product page', 'iki-toolkit' );
	}

	return $options;
}

add_filter( 'iki_admin_single_options', '_filter_iki_toolkit_post_single_page_options', 10, 2 );
