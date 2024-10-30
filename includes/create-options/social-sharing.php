<?php
/**
 * Add social sharing options to customizer
 * @return array return options for social sharing
 */


function iki_toolkit_social_sharing_options() {

	return array(
		'title'   => __( 'Social sharing', 'iki-toolkit' ),
		'options' => array(
			'share_services_popup' => array(
				'type'          => 'popup',
				'value'         => array(
					'default_share' => $GLOBALS['iki_toolkit_admin']['default_share_services'],
					'facebook'      => '',
					'twitter'       => '',
					'linkedin'      => '',
					'vk'            => '',
					'weibo'         => '',
					'pinterest'     => '',
					'reddit'        => '',
					'tumblr'        => '',
					'lastFM'        => '',
					'myspace'       => '',
					'instagram'     => '',
					'dribbble'      => '',
					'flickr'        => '',
					'500px'         => '',
					'github'        => '',
					'bitbucket'     => '',
					'test_text'     => ''
				),
				'label'         => __( 'Share services', 'iki-toolkit' ),
				'desc'          => __( 'Choose share services that you would like to enable throughout the site.', 'iki-toolkit' ),
				'popup-title'   => __( 'Share services', 'iki-toolkit' ),
				'button'        => __( 'Open', 'iki-toolkit' ),
				'size'          => 'medium', // small, medium, large
				'popup-options' => array(
					'share_service_info_html' => array(
						'type'  => 'html',
						'label' => false,
						'value' => '',
						'html'  => __( 'Choose what services you wish to be shown on posts and pages.', 'iki-toolkit' ) . '</br>' .
						           __( 'Users will be able to click on the share buttons and automatically share your site to chosen networks.', 'iki-toolkit' ) . '</br>' .
						           __( 'Share buttons will generally be shown after posts and pages.', 'iki-toolkit' )
					),
					'default_share'           => array(
						'type'    => 'checkboxes',
						'value'   => $GLOBALS['iki_toolkit_admin']['default_share_services'],
						'label'   => __( 'Share services', 'iki-toolkit' ),
						'choices' => array( // Note: Avoid bool or int keys http://bit.ly/1cQgVzk
							'facebook'  => __( 'Facebook', 'iki-toolkit' ),
							'twitter'   => __( 'Twitter', 'iki-toolkit' ),
							'linkedin'  => __( 'LinkedIn', 'iki-toolkit' ),
							'vk'        => __( 'VK', 'iki-toolkit' ),
							'weibo'     => __( 'Weibo', 'iki-toolkit' ),
							'pinterest' => __( 'Pinterest', 'iki-toolkit' ),
							'reddit'    => __( 'Reddit', 'iki-toolkit' ),
							'tumblr'    => __( 'Tumblr', 'iki-toolkit' ),
							'buffer'    => __( 'Buffer', 'iki-toolkit' ),
							'digg'      => __( 'Digg', 'iki-toolkit' ),
						),
						'inline'  => false
					)
				)
			)
		)
	);
}
