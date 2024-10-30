<?php
/** Customizer background options
 * @return array
 */
function iki_toolkit_breadcrumbs_options() {

	return array(
		'title'   => __( 'Breadcrumb navigation', 'iki-toolkit' ),
		'options' => array(
			'use_breadcrumbs'       => array(
				'type'         => 'switch',
				'value'        => 'enabled',
				'label'        => __( 'Show breadcrumb navigation', 'iki-toolkit' ),
				'help'         => __( 'Breadcrumb navigation will be shown everywhere, except for "blank page" template. It can also be disabled for some page templates on page by page basis.', 'iki-toolkit' ),
				'attr'         => array(
					'data-iki-switch'  => 1,
					'data-iki-for'     => 'breadcrumbs_separator,breadcrumbs_root_name',
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
			'breadcrumbs_separator' => array(
				'type'  => 'text',
				'value' => ' / ',
				'label' => __( 'Breadcrumb separator', 'iki-toolkit' ),
			),
			'breadcrumbs_root_name' => array(
				'type'  => 'text',
				'value' => _x( 'Home', 'Breadcrumb name for front page of the site.', 'iki-toolkit' ),
				'label' => __( 'Homepage indicator', 'iki-toolkit' ),
				'desc'  => __( 'Breadcrumb name for front page of the site.', 'iki-toolkit' )
			)
		)
	);
}
