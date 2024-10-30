<?php
/**
 *
 * @link              hhttps://wordpress.org/plugins/iki-toolkit
 * @since             1.0.0
 * @package           iki_toolkit
 *
 * @wordpress-plugin
 * Plugin Name:       Iki Toolkit
 * Plugin URI:        https://wordpress.org/plugins/iki-toolkit
 * Description:       The Iki Toolkit extends functionality to Iki Themes, providing custom post types and more.
 *
 * Version:           1.2.11
 * Author:            Ivan Vlatkovic
 * Author URI:        https://profiles.wordpress.org/iki_xx
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       iki-toolkit
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

register_activation_hook( __FILE__, 'activate_iki_toolkit' );
register_deactivation_hook( __FILE__, 'deactivate_iki_toolkit' );

add_action( 'init', 'iki_toolkit_on_wp_init', 30 );
add_action( 'after_setup_theme', 'iki_toolkit_after_setup_theme', 1000 );



define( 'IKI_TOOLKIT', true );
define( 'IKI_TOOLKIT_ROOT', plugin_dir_path( __FILE__ ) );
define( 'IKI_TOOLKIT_ROOT_URL', plugin_dir_url( __FILE__ ) );


require( 'includes/menu-walker/class-menu-admin-save.php' );

require( 'includes/functions/core-functions.php' );
require( 'includes/functions/grid-functions.php' );
//needed in front end for wp backery grid shortcodes
require( 'includes/create-options/class-grid-admin-options.php' );
require( 'includes/create-options/class-admin-options.php' );

require( 'includes/misc/class-svg-row-separator.php' );
require( 'includes/admin-settings/ajax-contact.php' );

if ( ! class_exists( 'Iki_Options_Helper' ) ) {
	require( 'includes/options-helper/i-options-helper.php' );
	require( 'includes/options-helper/class-options-helper.php' );
	require( 'includes/options-helper/class-unyson-options-helper.php' );
}

require( 'includes/functions/contact-functions.php' );
require( 'includes/functions/fs-panel-functions.php' );
require( 'includes/functions/sass-hooks.php' );
require( 'includes/functions/team-functions.php' );
require( 'includes/utils/class-utils.php' );
require( 'includes/utils/class-custom-tax-filter.php' );

//api keys section
require( 'includes/admin-settings/class-external-service-front-end.php' );
require( 'includes/admin-settings/api-keys/class-external-service-callbacks.php' );
require( 'includes/admin-settings/api-keys/api/class-abstract-api.php' );
require( 'includes/admin-settings/api-keys/api/class-flickr-api.php' );
require( 'includes/admin-settings/api-keys/api/class-pinterest-api.php' );
require( 'includes/admin-settings/api-keys/api-helpers/class-service-helper.php' );
require( 'includes/admin-settings/api-keys/api-helpers/class-flickr-helper.php' );
require( 'includes/admin-settings/api-keys/api-helpers/class-pinterest-helper.php' );


// required classes for "blocks" functionality
require( 'includes/blocks/class-abstract-block-cpt.php' );
require( 'includes/blocks/block-utils.php' );
require( 'includes/blocks/content-blocks/class-content-block-cpt.php' );
require( 'includes/blocks/content-blocks/class-cb-factory.php' );
require( 'includes/blocks/content-blocks/class-content-block-widget.php' );
require( 'includes/functions/portfolio-functions.php' );
require( 'includes/functions/hero-section.php' );
require( 'includes/functions/hero-section-featured.php' );

require( 'includes/menu-walker/class-stamp-creator.php' );

//sass preprocessor.
require( 'includes/sass-compiler/class-scss-compiler.php' );
require( 'includes/sass-compiler/class-dynamic-sass.php' );
require( 'includes/sass-compiler/class-dynamic-css.php' );

//shortcodes
require( 'includes/vc/vc-utils.php' );
require( 'includes/vc/class-wonder-grid-vc.php' );
require( 'includes/vc/class-slider-options.php' );
require( 'includes/vc/class-post-options.php' );
require( 'includes/vc/class-post-slider.php' );
require( 'includes/vc/class-image-grid-vc.php' );
require( 'includes/vc/class-image-slider-vc.php' );
require( 'includes/vc/class-share-icons.php' );
require( 'includes/vc/class-pricing-item.php' );
require( 'includes/vc/class-title-vc.php' );
require( 'includes/vc/class-img-bg-vc.php' );
require( 'includes/vc/class-social-profiles.php' );
require( 'includes/vc/class-theme-social-profiles.php' );
require( 'includes/vc/class-post-listing.php' );
require( 'includes/class-iki-toolkit.php' );

//breadcrumbs
require( 'includes/breadcrumbs/class-iki-breadcrumbs.php' );

//wonder grid
require( 'includes/wonder-grid/load.php' );

//custom post types
require( 'includes/team/class-team-member-cpt.php' );
require 'includes/portfolio/class-portfolio-cpt.php';


// widgets
require( 'includes/widgets/class-recent-posts-with-thumbs.php' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_iki_toolkit() {
	require( 'includes/iki-toolkit-activator.php' );
	Iki_Toolkit_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_iki_toolkit() {
	require( 'includes/iki-toolkit-deactivator.php' );
	Iki_Toolkit_Deactivator::deactivate();
}


/**
 * Flush rewrite rules after cpt registraction,and only on activation of the plugin.
 */
function iki_toolkit_on_wp_init() {

	load_plugin_textdomain( 'iki-toolkit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	if ( get_option( 'iki_toolkit_flush_rewrite_rules_flag' ) ) {
		flush_rewrite_rules();
		delete_option( 'iki_toolkit_flush_rewrite_rules_flag' );
	}

}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_iki_toolkit() {

	Iki_Toolkit::get_instance()->init();
}

/**
 * Check for appropriate theme support
 * @since    1.0.0
 */
function iki_toolkit_after_setup_theme() {

	if ( is_admin() || is_customize_preview() ) {

		/*Include classes that handle the creation of plugin settings*/
		require( 'includes/create-options/class-admin-author-options.php' );
		require( 'includes/create-options/option-hooks.php' );
		require( 'includes/create-options/class-admin-options-fs-panels.php' );
		require( 'includes/create-options/fs-panel-options.php' );
		require( 'includes/create-options/velocity-options.php' );
		require( 'includes/create-options/social-sharing.php' );
		require( 'includes/create-options/breadcrumbs.php' );
		require( 'includes/admin-settings/class-abstract-options-section.php' );
		require( 'includes/admin-settings/classs-admin-settings.php' );
		require( 'includes/admin-settings/class-social-profiles-section.php' );
		require( 'includes/admin-settings/featured-posts/class-admin-featured-posts.php' );
		require( 'includes/admin-settings/featured-posts/class-search-posts-ajax.php' );
		require( 'includes/admin-settings/api-keys/api/class-external-api-data-check.php' );
		require( 'includes/admin-settings/api-keys/class-api-keys-section.php' );
		require( 'includes/create-options/class-hero-section-options.php' );
		require( 'includes/menu-walker/class-walker-menu-admin.php' );

	}

	iki_toolkit()->init_options_helper();
	iki_toolkit()->set_default_options();

	if ( is_admin() ) {
		//instatiate classes for creation of option settings
		new Iki_Admin_Settings();
		new Iki_Social_Profiles_Admin_Section();

		if ( get_theme_support( 'iki-toolkit-external-services' ) ) {
			new Iki_API_Keys_Admin_Section();
		}

		if ( current_theme_supports( 'iki-toolkit-featured-posts' ) ) {

			new Iki_Admin_Featured_Posts( 10, array(
				'title'       => __( 'Featured Blog Posts', 'iki-toolkit' ),
				'post_type'   => 'post',
				'option_name' => 'iki_tk_feat_blog',
				'settings_id' => 'feat_blog_posts_section'
			) );

			if ( post_type_exists( 'iki_portfolio' ) ) {
				new Iki_Admin_Featured_Posts( 10, array(
					'title'       => __( 'Featured Portfolio Posts', 'iki-toolkit' ),
					'post_type'   => 'iki_portfolio',
					'option_name' => 'iki_tk_feat_iki_portfolio',
					'settings_id' => 'feat_portfolio_posts_section'
				) );
			}

			if ( post_type_exists( 'iki_team_member' ) ) {

				new Iki_Admin_Featured_Posts( 10, array(
					'title'       => __( 'Featured Team Member Posts', 'iki-toolkit' ),
					'post_type'   => 'iki_team_member',
					'option_name' => 'iki_tk_feat_iki_team_member',
					'settings_id' => 'feat_team_member_posts_section'
				) );
			}
			if ( class_exists( 'WooCommerce' ) ) {

				new Iki_Admin_Featured_Posts( 10, array(
					'title'       => __( 'Featured Products', 'iki-toolkit' ),
					'post_type'   => 'product',
					'option_name' => 'iki_tk_feat_product',
					'settings_id' => 'feat_product_posts_section'
				) );
			}
		}
	}


	new Iki_Wonder_Grid_VC(
		'iki_wonder_grid_vc',
		__( 'Wonder Grid', 'iki-toolkit' )
	);

}


// run the plugin.
run_iki_toolkit();

