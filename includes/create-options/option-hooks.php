<?php

add_filter( 'iki_admin_page_single_options', 'iki_toolkit_external_service_options' );
add_filter( 'iki_admin_page_single_options', 'iki_toolkit_contact_data_options' );
add_filter( 'iki_admin_single_options', '_filter_iki_toolkit_portfolio_project_options', 10, 2 );
add_filter( 'iki_admin_single_options', '_filter_iki_toolkit_team_member_options', 10, 2 );

/** Create options for external service functionality. Do it only on page options
 *
 * @param $options
 *
 * @return mixed
 */
function iki_toolkit_external_service_options( $options ) {

	global $post;
	$iki_shop_id = '1324123412341234';

	if ( class_exists( 'Woocommerce', false ) ) {
		$iki_shop_id = wc_get_page_id( 'shop' );
	}

	if ( $post && $post->ID != $iki_shop_id && current_theme_supports( 'iki-toolkit-external-services' ) ) {

		$options['external_service'] = array(
			'type'    => 'box',
			'title'   => __( 'External service options', 'iki-toolkit' ),
			'options' => array(
				'ext'   => array(
					'type'    => 'tab',
					'title'   => __( 'Setup external service', 'iki-toolkit' ),
					'options' => array(
						"external_service"           => array(
							'label'   => false,
							'type'    => 'multi-picker',
							'value'   => array(
								'service' => '',
							),
							'picker'  => array(
								'service' => array(
									'label'   => __( 'Service', 'iki-toolkit' ),
									'desc'    => '',
									'type'    => 'select',
									'value'   => 'classic',
									'choices' => array(
										'pinterest' => __( 'Pinterest', 'iki-toolkit' ),
										'flickr'    => __( 'Flickr', 'iki-toolkit' ),
									),
								)
							),
							'choices' => array(
								'flickr'    => array(
									'notice-1'        => array(
										'type'  => 'html',
										'value' => '',
										'label' => '',
										'html'  => '<b>'
										           . __( 'Please note that you will need an API key in order to use the Flickr service.', 'iki-toolkit' )
										           . '</b></br><b>'
										           . _x( 'You can set it up at "Settings -> Iki Toolkit"',
												'Location for the required option',
												'iki-toolkit' )
										           . '</b>'
									),
									'username'        => array(
										'type'  => 'text',
										'value' => '',
										'label' => __( 'Flickr user ID', 'iki-toolkit' ),
										'desc'  => __( 'Please refer to help to find out how to get user id.', 'iki-toolkit' ),
										'help'  => __( 'You can get flickr user id via this site: www.idgettr.com or for a demo, you can try this one : "57803084@N07" (without the quotes)', 'iki-toolkit' )
									),
									'photoset_id'     => array(
										'type'  => 'text',
										'value' => '',
										'label' => __( 'Flickr photoset id', 'iki-toolkit' ),
										'desc'  => __( 'If left empty, user photostream will be shown instead.', 'iki-toolkit' ),
										'help'  => __( 'Photoset ID must be from the user you are using in "Flickr user ID". Please refer to theme documentation on how to get photoset id. For demo purposes you can try this photoset id "72157631981970736". Make user to use the demo flickr username ID as well.', 'iki-toolkit' )
									),
									'high_resolution' => array(
										'type'         => 'switch',
										'value'        => 'disabled',
										'label'        => __( 'Use high resolution images.', 'iki-toolkit' ),
										'desc'         => __( 'Please note that high resolution images drastically affect bandwith, and site load times.', 'iki-toolkit' ),
										'left-choice'  => array(
											'value' => 'disabled',
											'label' => __( 'No', 'iki-toolkit' ),
										),
										'right-choice' => array(
											'value' => 'enabled',
											'label' => __( 'Yes', 'iki-toolkit' ),
										),
									)
								),
								'pinterest' => array(
									'username'  => array(
										'type'  => 'text',
										'value' => '',
										'label' => __( 'Pinterest username', 'iki-toolkit' ),
										'help'  => __( 'You can try this username for a demo "natgeo"', 'iki-toolkit' )
									),
									'boardname' => array(
										'type'  => 'text',
										'value' => '',
										'label' => __( 'Pinterest board name', 'iki-toolkit' ),
										'desc'  => __( 'If left empty, user latest pins will be shown instead', 'iki-toolkit' ),
										'help'  => __( 'You can try this board for a demo "birds", please note that you must use "birds" board with the demo username.', 'iki-toolkit' )

									),
								),
							),
						),
						'external_data_test_group'   => array(
							'type'    => 'group',
							'options' => array(
								'test_external_data'    => array(
									'type'  => 'html',
									'label' => __( 'Check entered data', 'iki-toolkit' ),
									'value' => '',
									'html'  => '<button id="iki-test-ext-data" type="button" class="button">' . __( 'Check', 'iki-toolkit' ) . '</button><span id="iki-test-ext-spinner" class="spinner iki-admin-spinner"></span>'
								),
								'test_external_data_el' => array(
									'type'  => 'html',
									'label' => '',
									'value' => '',
									'html'  => '
<div id="iki-data-status-wrap">
	<div id="iki-data-success" class="updated notice hidden"></div>
	<div id="iki-data-error" data-iki-timeout="' . esc_html__( 'Server timeout', 'iki-toolkit' ) . '"class="error notice hidden"></div>
</div>'
								),
							)
						),
						'external_data_del_group'    => array(
							'type'    => 'group',
							'options' => array(
								'del_external_data'    => array(
									'type'  => 'html',
									'label' => __( 'Delete cache', 'iki-toolkit' ),
									'help'  => __( 'External content is cached for one day. Delete the cache if you changed you external content (added images to flickr or pinterest board, and you want to see new images immediately.', 'iki-toolkit' ),
									'value' => '',
									'html'  => '<button id="iki-del-ext-data" type="button" class="button">' . __( 'Delete', 'iki-toolkit' ) . '</button><span id="iki-del-ext-spinner" class="spinner iki-admin-spinner"></span>'
								),
								'del_external_data_el' => array(
									'type'  => 'html',
									'label' => '',
									'value' => '',
									'html'  => '<div id="iki-del-status-wrap">
	<div id="iki-del-success" class="updated notice hidden">' . esc_html__( 'Cache deleted', 'iki-toolkit' ) . '</div>' .
									           '<div id="iki-del-error" class="error notice hidden">' . esc_html__( 'Server timeout', 'iki-toolkit' ) . '</div>
</div>'
								),
							)
						),
						'external_animation'         => Iki_Admin_Options::get_instance()->get_animation_in_option( array(
							'label' => __( 'Image animation in', 'iki-toolkit' ),
							'desc'  => __( 'Image animation.', 'iki-toolkit' )
						),
							false,
							array(
								'random' => __( 'Random', 'iki-toolkit' ),
							) ),
						'external_stagger_animation' => array(
							'type'         => 'switch',
							'value'        => 'enabled',
							'label'        => __( 'Stagger animation', 'iki-toolkit' ),
							'desc'         => __( 'If YES, images are animated one by one. Otherwise, they are animated all at once.', 'iki-toolkit' ),
							'left-choice'  => array(
								'value' => 'disabled',
								'label' => __( 'No', 'iki-toolkit' ),
							),
							'right-choice' => array(
								'value' => 'enabled',
								'label' => __( 'Yes', 'iki-toolkit' ),
							),
						),
						'external_maximum_images'    => array(
							'type'       => 'slider',
							'value'      => 500,
							'properties' => array(
								'min'  => 20,
								'max'  => 500,
								'step' => 1,
							),
							'label'      => __( 'Total number of images.', 'iki-toolkit' ),
							'desc'       => __( 'Total number of images that are going to be displayed from the service.', 'iki-toolkit' ),
							'help'       => __( 'You can limit the number of images to be loaded.', 'iki-toolkit' ),
						),
						'external_grid_condensed'    => array(
							'type'         => 'switch',
							'value'        => 'enabled',
							'label'        => __( 'Remove space between images in the grid.', 'iki-toolkit' ),
							'left-choice'  => array(
								'value' => 'disabled',
								'label' => __( 'No', 'iki-toolkit' ),
							),
							'right-choice' => array(
								'value' => 'enabled',
								'label' => __( 'Yes', 'iki-toolkit' ),
							),
						)
					),
				),
				'ext_2' => array(
					'type'    => 'tab',
					'title'   => __( 'Profile layout.', 'iki-toolkit' ),
					'options' => array(
						'show_profile'  => array(
							'type'          => 'switch',
							'value'         => 'enabled',
							'label'         => __( 'Show profile', 'iki-toolkit' ),
							'desc'          => __( 'Show external service profile image. NOTE: Pinterest service can only have custom profile image, and "hero" section must be present.', 'iki-toolkit' ),
							'help'          => __( 'Profile image width is fixed at 100px. So if your service gives you smaller image, best thing to do is to use
custom profile image.Hero section must be present in order for profile image to be shown.', 'iki-toolkit' ),
							'attr'          => array(
								'data-iki-switch'  => 1,
								'data-iki-for'     => 'profile_group',
								'data-iki-test'    => 'enabled',
								'data-iki-refresh' => 'alwaysRefresh',
							),
							'left-choice'   => array(
								'value' => 'disabled',
								'label' => __( 'No', 'iki-toolkit' ),
							),
							'right-choice'  => array(
								'value' => 'enabled',
								'label' => __( 'Yes', 'iki-toolkit' ),
							),
							'profile_image' => array(
								'type'        => 'upload',
								'label'       => __( 'Custom image for profile.', 'iki-toolkit' ),
								'images_only' => true
							)
						),
						'profile_group' => array(
							'type'    => 'group',
							'options' => array(
								'profile_image_shape'    => array(
									'type'    => 'select',
									'value'   => 'round',
									'label'   => __( 'Profile image shape', 'iki-toolkit' ),
									'choices' => array(
										'round'  => __( 'Round', 'iki-toolkit' ),
										'square' => __( 'Square', 'iki-toolkit' )
									)
								),
								'profile_image_aligment' => array(
									'type'    => 'select',
									'value'   => 'center',
									'label'   => __( 'Profile image aligment', 'iki-toolkit' ),
									'choices' => array(
										'center' => __( 'Center', 'iki-toolkit' ),
										'left'   => __( 'Left', 'iki-toolkit' ),
									)
								),
								'custom_profile_group'   => array(
									'type'    => 'group',
									'options' => array(
										'custom_profile' => array(
											'type'         => 'switch',
											'value'        => 'disabled',
											'label'        => __( 'Custom Profile', 'iki-toolkit' ),
											'desc'         => __( 'Override profile image from external service, with custom image from media library.', 'iki-toolkit' ),
											'attr'         => array(
												'data-iki-switch'  => 1,
												'data-iki-for'     => 'profile_image',
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
										'profile_image'  => array(
											'type'        => 'upload',
											'label'       => __( 'Custom image for profile.', 'iki-toolkit' ),
											'images_only' => true
										)
									),
								)
							)
						),

					)
				)
			)
		);
	}

	return $options;
}


/** Create options for contacts section
 *
 * @param $options
 *
 * @return mixed
 */
function iki_toolkit_contact_data_options( $options ) {

	global $post;
	$iki_shop_id = '1324123412341234';

	if ( class_exists( 'Woocommerce', false ) ) {
		$iki_shop_id = wc_get_page_id( 'shop' );
	}

	if ( $post && $post->ID != $iki_shop_id && current_theme_supports( 'iki-toolkit-contact' ) ) {

		$options['contact_page'] = array(
			'type'    => 'box',
			'title'   => __( 'Contact Page', 'iki-toolkit' ),
			'options' => array(
				'message_subject'         => array(
					'type'  => 'text',
					'value' => __( 'From my site : ', 'iki-toolkit' ),
					'label' => __( 'Subject prefix', 'iki-toolkit' ),
					'desc'  => __( 'Prefix for the subject line.', 'iki-toolkit' ),
					'help'  => __( 'Prefix for the subject line that will appear in the email, if successifully sent. In default form the subject line is "From my site : [user subject line]"', 'iki-toolkit' ),
				),
				'email'                   => array(
					'type'  => 'text',
					'value' => '',
					'label' => __( 'Email address destination', 'iki-toolkit' ),
					'desc'  => __( 'Where should email message be sent.', 'iki-toolkit' ),
					'help'  => __( 'Email address where you want to receive user messages.', 'iki-toolkit' )
				),
				'show_phone_field'        => array(
					'type'         => 'switch',
					'label'        => __( 'Show phone field', 'iki-toolkit' ),
					'desc'         => __( 'Field for user to leave her phone number', 'iki-toolkit' ),
					'value'        => 'disabled',
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					)
				),
				'use_custom_qestion'      => array(
					'label'        => __( 'Use custom question for contact form.', 'iki-toolkit' ),
					'help'         => __( 'User will have to provide the correct answer for the question in order for the email to be sent.', 'iki-toolkit' ),
					'type'         => 'switch',
					'value'        => 'disabled',
					'attr'         => array(
						'data-iki-switch' => 1,
						'data-iki-for'    => 'custom_question,custom_answer',
						'data-iki-test'   => 'enabled',
					),
					'right-choice' => array(
						'value' => 'enabled',
						'label' => __( 'Yes', 'iki-toolkit' ),
					),
					'left-choice'  => array(
						'value' => 'disabled',
						'label' => __( 'No', 'iki-toolkit' ),
					)
				),
				'custom_question'         => array(
					'type'  => 'text',
					'value' => '',
					'label' => __( 'Question', 'iki-toolkit' ),
					'desc'  => __( 'Setup a question for user in contact form.', 'iki-toolkit' ),
					'help'  => __( 'User must answer the question correctly in order to send the form. For example "what is 2+2". This is used against automated submisson attack.', 'iki-toolkit' ),
				),
				'custom_answer'           => array(
					'type'  => 'text',
					'value' => '',
					'label' => __( 'Answer', 'iki-toolkit' ),
					'desc'  => __( 'Answer for the question.', 'iki-toolkit' ),
					'help'  => __( 'User must provide identical answer in order to send email.', 'iki-toolkit' ),
				),
				'contact_data_fake_field' => array(
					'type'  => 'html',
					'label' => __( 'Setup optional information', 'iki-toolkit' ),
					'value' => '',
					'html'  => __( 'Optional information to be shown next to the contact form menu', 'iki-toolkit' )
				),
				'business_hours'          => array(
					'type'  => 'wp-editor',
					'value' => '',
					'label' => __( 'Business hours', 'iki-toolkit' ),
					'desc'  => __( 'Setup business hours', 'iki-toolkit' )
				),
				'telephone'               => array(
					'type'  => 'wp-editor',
					'value' => '',
					'label' => __( 'Telephone', 'iki-toolkit' )
				),
				'location'                => array(
					'type'  => 'wp-editor',
					'value' => '',
					'label' => __( 'Location', 'iki-toolkit' )
				),
				'misc'                    => array(
					'type'  => 'wp-editor',
					'value' => '',
					'label' => __( 'Miscellaneous', 'iki-toolkit' )
				)


			)
		);
	}

	return $options;
}


/** Add options for portfolio project on portfolio single object
 *
 * @param $options
 * @param $post_type
 *
 * @return mixed
 */
function _filter_iki_toolkit_portfolio_project_options( $options, $post_type ) {
	if ( 'iki_portfolio' == $post_type ) {

		$options['3_project_section'] = array(
			'type'    => 'box',
			'title'   => __( 'Project Setup', 'iki-toolkit' ),
			'options' => array(
				'project_layout'       => Iki_Toolkit_Hero_Section_Options::get_instance()->get_portfolio_project_layout(),
				'remove_tags'          => array(
					'type'         => 'switch',
					'label'        => __( 'Remove tags', 'iki-toolkit' ),
					'desc'         => __( 'Remove tags from project info', 'iki-toolkit' ),
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
				'remove_categories'    => array(
					'type'         => 'switch',
					'label'        => __( 'Remove categories', 'iki-toolkit' ),
					'desc'         => __( 'Remove categories from project info', 'iki-toolkit' ),
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
				'custom_content_group' => array(
					'type'    => 'group',
					'options' => array(
						'project_content'      => Iki_Toolkit_Hero_Section_Options::get_instance()->portfolio_project_custom_content(),
						'content_width'        => array(
							'type'  => 'hidden',
							'value' => 'full'
						),
						'content_custom_width' => array(
							'type'  => 'hidden',
							'value' => '',
						),
						'data_fake_field'      => array(
							'type'  => 'html',
							'label' => __( 'Setup project data', 'iki-toolkit' ),
							'value' => '',
							'html'  => __( 'Setup specific project data. Empty fields won\'t be shown', 'iki-toolkit' )
						),
						'client_info'          => array(
							'type'  => 'wp-editor',
							'value' => '',
							'size'  => 'large',
							'label' => __( 'Client Info', 'iki-toolkit' )
						),
						'skills'               => array(
							'type'  => 'wp-editor',
							'value' => '',
							'size'  => 'large',
							'label' => __( 'Skills', 'iki-toolkit' ),
							'desc'  => __( 'Skills required for the project. After the skills text, portfolio tags will be printed.', 'iki-toolkit' )
						),
						'project_url'          => array(
							'type'  => 'text',
							'value' => '',
							'label' => __( 'Project URL', 'iki-toolkit' )
						),
						'misc_desc'            => array(
							'type'  => 'wp-editor',
							'value' => '',
							'size'  => 'large',
							'label' => __( 'Miscellaneous', 'iki-toolkit' )
						)
					)
				),
			)
		);
	}

	return $options;
}


/**
 * Add options for team members on team member post single page
 *
 * @param $options
 * @param $post_type
 *
 * @return mixed
 */
function _filter_iki_toolkit_team_member_options( $options, $post_type ) {

	if ( 'iki_team_member' == $post_type ) {

		$options['2_blog_author'] = array(
			'type'    => 'box',
			'title'   => __( 'Connect team member with blog author', 'iki-toolkit' ),
			'options' => array(
				'author_connection'       => Iki_Toolkit_Admin_Options::get_instance()->get_blog_authors(),
				'use_author_contact_data' => array(
					'label'        => __( 'Use author contact data', 'iki-toolkit' ),
					'help'         => __( 'If YES, team member will inherit blog author contact data ( social profiles ).This is best used if team member is also an author on the site.', 'iki-toolkit' ),
					'type'         => 'switch',
					'value'        => 'enabled',
					'attr'         => array(
						'data-iki-switch' => 1,
						'data-iki-for'    => "author_connection",
						'data-iki-test'   => 'enabled',
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
			)
		);

		$iki_similar_posts_box               = Iki_Toolkit_Admin_Options::get_instance()->get_similar_posts();
		$options['2_blog_author']['options'] = array_replace_recursive( $options['2_blog_author']['options'], $iki_similar_posts_box['options'] );

		$options['5_custom_contacts'] = array(
			'type'    => 'box',
			'title'   => __( 'Team member contact links', 'iki-toolkit' ),
			'options' => array(
				'tm_contacts'             => array(
					'type'          => 'popup',
					'value'         => array(),
					'label'         => __( 'Setup contact links', 'iki-toolkit' ),
					'desc'          => false,
					'popup-title'   => __( 'Team member contact links', 'iki-toolkit' ),
					'button'        => __( 'Open', 'iki-toolkit' ),
					'size'          => 'small', // small, medium, large
					'popup-options' => array(
						Iki_Toolkit_Admin_Options::get_instance()->get_contacts()
					)
				),
				'my_social_profiles_text' => array(
					'type'  => 'text',
					'value' => '',
					'label' => __( 'My social profiles text', 'iki-toolkit' ),
					'desc'  => __( 'Text that appears before the social profiles icons', 'iki-toolkit' ),
					'help'  => __( 'If left empty, default text will be used ("My social profiles")', 'iki-toolkit' )
				)
			)
		);

	}

	return $options;

}

