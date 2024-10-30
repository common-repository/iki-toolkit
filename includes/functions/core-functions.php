<?php


add_filter( 'template_include', '_filter_iki_toolkit_template_include', 1000000 );
add_filter( 'dynamic_sidebar_params', '_filter_iki_toolkit_full_width_cb_widget' );
add_filter( 'wp_calculate_image_srcset', '_filter_iki_toolkit_disable_src_set' );
add_filter( 'script_loader_tag', '_filter_iki_toolkit_defer_script_tags', 10, 2 );


/**
 * Disable automatically setting src-set attribute on images
 */
function _filter_iki_toolkit_disable_src_set( $sources ) {

	if ( get_theme_support( 'iki-toolkit-disable-src-set' ) ) {
		return false;
	}

	return $sources;
}

/**
 * Deffer the loading of some scripts
 *
 * @param string $tag script tag
 * @param string $handle script handle
 *
 * @return mixed
 */
function _filter_iki_toolkit_defer_script_tags( $tag, $handle ) {

	if ( get_theme_support( 'iki-toolkit-defer-script-tags' ) ) {

		$scripts_to_defer = array(
			'iki-vendor',
			'iki-main-js',
			'iki-photoswipe',
			'iki-photoswipe-ui',
			'iki-yt-background',
			'iki-demo'
		);

		//wordpress customizer is very sensitive to deferred script tags, avoid the customizer
		if ( ! is_customize_preview() && in_array( $handle, $scripts_to_defer ) ) {
			return str_replace( ' src', 'defer src', $tag );
		}
	}

	return $tag;
}


/** Iki_Toolkit wrapper function
 * @return Iki_Toolkit - plugin instance
 */
function iki_toolkit() {
	return Iki_Toolkit::get_instance();
}


/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @access public
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string
 */
function iki_toolkit_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = iki_toolkit()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = iki_toolkit()->plugin_path() . '/partials/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'iki_toolkit_locate_template', $template, $template_name, $template_path );
}


/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 *
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 */
function iki_toolkit_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	$located = iki_toolkit_locate_template( $template_name, $template_path, $default_path );

	if ( ! empty( $located ) ) {
		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'iki_toolkit_get_template', $located, $template_name, $args, $template_path, $default_path );

		do_action( 'iki_toolkit_before_template_part', $template_name, $template_path, $located, $args );

		include $located;

		do_action( 'iki_toolkit_after_template_part', $template_name, $template_path, $located, $args );
	}

}


/** Create an array that holds all the location data that is used throughout the theme.
 *  there is:
 *  "location" (blog,archive,post etc..)
 *  "type" - to be used in combination with "location" e.g location=archive and type=category
 *  or location=post and type=iki_portfolio
 * @return array
 */
function iki_toolkit_get_active_location() {
	$location = '';
	$type     = false;
	$id       = false;

	//wpbakery frontend editing fix
	if ( isset( $_REQUEST['action'] ) && 'vc_load_shortcode' == $_REQUEST['action'] ) {
		$template = 'index.php';
	} else {
		$template = $GLOBALS['iki_toolkit']['data']['template'];
	}


	if ( is_home() ) {
		$location = 'blog';
		$type     = 'blog';
	} elseif ( is_singular() ) {
		$location = 'post';
		$type     = get_post_type();
	} elseif ( is_archive() ) {
		$location = 'archive';
		if ( is_category() ) {
			$type = 'category';
		} elseif ( is_tag() ) {
			$type = 'tag';
		} elseif ( is_author() ) {
			$type = 'author';
		} elseif ( is_post_type_archive( 'iki_team_member' ) ) {
			$type = 'iki_team_member';
		} elseif ( is_post_type_archive( 'iki_portfolio' ) ) {
			$type = 'iki_portfolio';
		} elseif ( is_post_type_archive( 'post' ) ) {
			$type = 'post';
		} elseif ( is_date() ) {
			$type = 'date';
		} elseif ( is_tax( 'iki_portfolio_cat' ) ) {
			$type = 'iki_portfolio_cat';
		} elseif ( is_tax( 'iki_portfolio_tag' ) ) {
			$type = 'iki_portfolio_tag';
		} elseif ( is_tax( 'iki_team_member_cat' ) ) {
			$type = 'iki_team_member_cat';
		} elseif ( is_tax( 'iki_team_member_tag' ) ) {
			$type = 'iki_team_member_tag';
		}

		//setup woocommerce location
		if ( ! $type && class_exists( 'WooCommerce', false ) ) {
			if ( is_tax( 'product_cat' ) ) {
				$type = 'product_cat'; // woocommerce product category
			} elseif ( is_tax( 'product_tag' ) ) {
				$type = 'product_tag';
			} elseif ( is_shop() ) {
				$type = 'shop';
			}
		}


	} elseif ( is_404() ) {
		$location = 'not_found';
		$type     = '404';
	} elseif ( is_search() ) {
		$location = 'search';
		$type     = 'search';
	}


	if ( isset( $GLOBALS['iki_theme'] ) && $GLOBALS['iki_theme']['flags']['ajax_pagination'] ) {

		$ajaxData = $GLOBALS['iki_theme']['data']['ajaxData'];
		$id       = $ajaxData['pagination_data']['optionId'];

	} else {
		$id = get_queried_object_id();
		$id = ( $id > 0 ) ? $id : false;

		if ( ! $id && class_exists( 'WooCommerce', false ) && is_woocommerce() ) {
			if ( is_shop() ) {
				$id       = wc_get_page_id( 'shop' );
				$location = 'post';
			}
		}
	}


	return array(
		'location' => $location,
		'type'     => $type,
		'template' => $template,
		'id'       => $id
	);
}

/** Remember the chosen template by wordpress to render the page
 *
 * @param $template
 *
 * @return mixed
 */
function _filter_iki_toolkit_template_include( $template ) {

	$GLOBALS['iki_toolkit']['data']['template'] = wp_basename( $template );

	return $template;
}


/** Setup full width widget
 *
 * @param $params
 *
 * @return mixed
 */
function _filter_iki_toolkit_full_width_cb_widget( $params ) {
	global $wp_registered_widgets;

	/**@var WP_Widget $settings_getter */
	$settings_getter = $wp_registered_widgets[ $params[0]['widget_id'] ]['callback'][0];

	if ( $settings_getter instanceof Iki_Content_Block_Widget ) {

		$widget_opts = $settings_getter->get_settings();
		$widget_opts = $widget_opts[ $params[1]['number'] ];
		if ( isset( $widget_opts['full_width'] ) && 'on' == $widget_opts['full_width'] ) {
			$params[0]['before_widget'] = preg_replace( '/(\sclass=")/', '${1}iki-full-width ', $params[0]['before_widget'] );
		}

	}

	return $params;
}


if ( ! function_exists( 'iki_toolkit_print_term_info' ) ) {

	/**
	 * Print information about currently active term.
	 */
	function iki_toolkit_print_term_info() {

		$location_info = iki_toolkit()->get_location_info();
		$type          = $location_info['type'];

		if ( $location_info['id'] ) {
			//we have an id , so we are looking at category , tag , or some custom post term archive
			$remove_title = iki_toolkit()->get_term_option( $location_info['id'], $type, 'remove_title', 'disabled' );
			if ( 'disabled' == $remove_title ) {
				if ( 'enabled' === iki_toolkit()->get_term_option( $location_info['id'], $type, 'show_custom_title', 'disabled' ) ) {
					$title = iki_toolkit()->get_term_option( $location_info['id'], $type, 'custom_title', 'disabled' );
				} else {
					$title = single_term_title( '', false );
				}
				$description = term_description();
			}
		} else {
			// regular archive , or custom post type archive listing.
			$remove_title = iki_toolkit()->get_customizer_option( "archive_{$type}_remove_title", 'disabled' );
			if ( 'disabled' == $remove_title ) {
				if ( is_post_type_archive() ) {

					//get custom title
					$title = iki_toolkit()->get_customizer_option( "archive_{$type}_title", '' );
					$title = trim( $title );
					if ( empty( $title ) ) {
						$title = post_type_archive_title( '', false );
					}
					$description = iki_toolkit_archive_description();
				} else {
					$title = get_the_archive_title();
				}
			}
		}

		if ( ! empty( $title ) ) {
			do_action( 'iki_archive_title_before' );
			printf( '<h1 class="entry-title page-title">%1$s</h1>', esc_html( $title ) );
			do_action( 'iki_archive_title_after' );
		}

		if ( ! empty( $description ) ) {
			do_action( 'iki_archive_description_before' );
			printf( '<div class="taxonomy-description">%1$s</div>', wp_kses_post( $description ) );
			do_action( 'iki_archive_description_after' );
		}
	}
}

if ( ! function_exists( 'iki_toolkit_archive_description' ) ) {
	/**
	 * Print currently active archive description
	 * @return string
	 */
	function iki_toolkit_archive_description() {


		$archive_description = apply_filters( 'iki_archive_description', '' );

		return wp_kses_post( $archive_description );
	}
}

if ( ! function_exists( 'iki_toolkit_blog_title' ) ) {
	/**
	 * Print blog title
	 *
	 * @param string $class
	 * @param string $before
	 * @param string $after
	 */
	function iki_toolkit_blog_title( $class = '', $before = '', $after = '' ) {

		$show_title = Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_customizer_option( 'blog_title', 'enabled' ) );
		$show_title = apply_filters( 'iki_show_blog_title', $show_title );
		if ( $show_title ) {
			do_action( 'iki_blog_title_before' );
			$title = get_option( 'blogname' );

			if ( $title ) {

				$c   = array();
				$c[] = $class;
				$c[] = 'entry-title';
				$c[] = 'iki-blog-title';


				$c  = apply_filters( 'iki_blog_title_class', $c );
				$c  = Iki_Toolkit_Utils::sanitize_html_class_array( $c );
				$st = sprintf( '<h1 class="%1$s"> %2$s </h1>', $c, esc_html( $title ) );

				echo $before . $st . $after;
				do_action( 'iki_blog_title_after' );

			}
		}

	}
}

if ( ! function_exists( 'iki_toolkit_blog_tagline' ) ) {
	/**
	 * Print blog description
	 *
	 * @param string $class
	 * @param string $before
	 * @param string $after
	 */
	function iki_toolkit_blog_tagline( $class = '', $before = '', $after = '' ) {

		$show_blog_info = Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_customizer_option( 'blog_show_description', 'enabled' ) );

		$show_blog_info = apply_filters( 'iki_show_blog_description', $show_blog_info );
		if ( $show_blog_info ) {

			$subtitle = get_bloginfo( 'description', 'display' );

			if ( $subtitle ) {
				do_action( 'iki_blog_subtitle_before' );
				$c   = array();
				$c[] = $class;
				$c[] = 'entry-subtitle';
				$c[] = 'iki-blog-tagline';

				$c  = apply_filters( 'iki_blog_subtitle_class', $c );
				$c  = Iki_Toolkit_Utils::sanitize_html_class_array( $c );
				$st = sprintf( '<h2 class=" %1$s "> %2$s </h2>', $c, esc_html( $subtitle ) );

				echo $before . $st . $after;
				do_action( 'iki_blog_subtitle_after' );

			}
		}
	}
}

if ( ! function_exists( 'iki_toolkit_the_title' ) ) {
	/**
	 * Print title
	 *
	 * @param string $before before subtitle
	 * @param string $after after subtitle
	 * @param bool $echo echo result
	 * @param int $id id of the post
	 *
	 * @return string title if $echo is false
	 */
	function iki_toolkit_the_title( $before = '', $after = '', $echo = true, $id = null ) {

		if ( is_null( $id ) ) {
			global $post;
			if ( ! $post ) {
				return;
			}
			$id = $post->ID;
		}

		$title = get_the_title( $id );

		if ( strlen( $title ) == 0 ) {
			return;
		}

		$title = $before . $title . $after;

		if ( $echo ) {

			echo $title;
		} else {
			return $title;
		}
	}
}
if ( ! function_exists( 'iki_toolkit_post_subtitle' ) ) {
	/**
	 * Print post subtitle
	 *
	 * @param string $before before subtitle
	 * @param string $after after subtitle
	 * @param bool $echo echo result
	 * @param int $id id of the post
	 *
	 * @return string title if $echo is false
	 */
	function iki_toolkit_post_subtitle( $before = '', $after = '', $echo = true, $id = null ) {

		if ( is_null( $id ) ) {
			global $post;
			if ( ! $post ) {
				return;
			}
			$id = $post->ID;
		}

		$title = iki_toolkit()->get_post_option( $id, 'subtitle', '' );
		$title = apply_filters( 'iki_post_subtitle', $title );
		$title = wp_kses( $title, array( 'br' => array() ) );
		if ( ! empty( $title ) ) {
			$title = $before . $title . $after;
			if ( $echo ) {
				echo $title;
			} else {
				return $title;
			}

		}
	}
}


if ( ! function_exists( 'iki_toolkit_posted_on' ) ) {

	/** Print author and date data for the post
	 *
	 * @param bool $show_author show author data
	 * @param bool $show_when_updated show when the post was updated.
	 */
	function iki_toolkit_posted_on( $show_author = true, $show_when_updated = true ) {
		iki_toolkit_print_post_date( $show_when_updated );
		if ( $show_author ) {
			echo '<span class="iki-icon-standard"></span>';
			echo iki_post_author();
		}

	}
}

if ( ! function_exists( 'iki_toolkit_post_author' ) ) {
	/**
	 * get the post author html as string
	 *
	 * @param int $post_id post id
	 *
	 * @return string
	 */
	function iki_toolkit_post_author( $post_id = null ) {

		$author_id = get_post( $post_id )->post_author;

		$byline = sprintf( '<span class="author vcard"><a class="url fn n " href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ),
			esc_html( get_the_author_meta( 'display_name', $author_id ) )
		);

		return '<span class=" iki-content-meta byline "> ' . $byline . '</span>';
	}
}
if ( ! function_exists( 'iki_toolkit_print_comments_number' ) ) {
	/**
	 * Print comments number
	 *
	 * @param string $before before the comments
	 * @param string $after after the comments
	 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default is the global `$post`.
	 */
	function iki_toolkit_print_comments_number( $before = '', $after = '', $post_id = 0 ) {
		$c = get_comments_number( $post_id );
		echo wp_kses_post( $before ) . $c . wp_kses_post( $after );
	}
}

if ( ! function_exists( 'iki_toolkit_print_author_name' ) ) {
	/**
	 * Print author name and twitter handle.
	 *
	 * @param int $post_id post id
	 */
	function iki_toolkit_print_author_name( $post_id = null ) {

		$author_id = get_post( $post_id )->post_author;

		echo iki_post_author( $post_id );
		$title       = get_the_author_meta( 'display_name', $author_id ) . ' ' . __( 'profile on Twitter ', 'iki-toolkit' );
		$twitter_url = esc_attr( get_the_author_meta( 'iki_contact_twitter', $author_id ) );

		if ( ! empty( $twitter_url ) ) {

			preg_match( '/\.com\/(.+)/', $twitter_url, $regex_match );

			if ( isset( $regex_match[1] ) ) {

				$twitter_handle = $regex_match[1];
				$twitter_handle = '<span class="iki-content-meta iki-auth-twitter-handle" ><a href="https://twitter.com/'
				                  . $twitter_handle
				                  . '" target="_blank" title="' . esc_html( $title ) . '" class="author-twitter-link tooltip-js">(@' . $twitter_handle . ')</a></span>';
				echo $twitter_handle;
			}
		}

	}
}

if ( ! function_exists( 'iki_toolkit_author_avatar' ) ) {
	/**
	 * Get author avatar
	 *
	 * @param int $size image size
	 * @param int $post_id post id
	 */
	function iki_toolkit_author_avatar( $size = 50, $post_id = null ) {

		$author_id = get_post( $post_id )->post_author;

		$before = sprintf( '<span class=" iki-content-meta author-img-wrapper"><a href="%1$s">',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ) );

		$after = '</a></span>';


		$t = get_avatar( $author_id, $size );

		echo $before . $t . $after;

	}
}

if ( ! function_exists( 'iki_toolkit_print_post_date' ) ) {
	/**
	 * Print post date, and maybe print post update time.
	 *
	 * @param bool $show_update_time
	 * @param int $post_id post id
	 */
	function iki_toolkit_print_post_date( $show_update_time = true, $post_id = null ) {

		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
		if ( $show_update_time && get_the_time( 'U', $post_id ) !== get_the_modified_time( 'U', $post_id ) ) {

			$time_string .= __( ' |  updated : ', 'iki-toolkit' );
			$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c', $post_id ) ),
			esc_html( get_the_date( '', $post_id ) ),
			esc_attr( get_the_modified_date( 'c', $post_id ) ),
			esc_html( get_the_modified_date( '', $post_id ) )
		);

		$posted_on = sprintf(
			_x( ' %s', 'post date', 'iki-toolkit' ),
			'<span class="iki-icon-clock" >' . $time_string . '</span>'
		);

		echo '<span class=" iki-content-meta posted-on ">' . $posted_on . '</span>';

	}
}


if ( ! function_exists( 'iki_toolkit_edit_post_link' ) ) {
	/**
	 * Print edit post link
	 */
	function iki_toolkit_edit_post_link() {
		edit_post_link( esc_html( __( 'Edit', 'iki-toolkit' ) ),
			'<div class="iki-content-meta"><span class="iki-icon-standard edit-link ">',
			'</span></div>' );
	}
}

if ( ! function_exists( 'iki_toolkit_categories' ) ) {
	/**
	 * Print categories
	 *
	 * @param string $before before content
	 * @param string $after after content
	 * @param string $separator category separator
	 * @param int $post_id Optional. Post ID to retrieve categories
	 * @param bool $echo should echo
	 *
	 * @return string
	 */
	function iki_toolkit_categories( $before = '', $after = '', $separator = ' , ', $post_id = false, $echo = true ) {

		$category_list = get_the_category_list( $separator, '', $post_id );

		if ( $echo && ! empty( $category_list ) ) {
			echo $before . $category_list . $after;

		} else {
			return $category_list;
		}

	}
}

if ( ! function_exists( 'iki_toolkit_get_user_contacts' ) ) {
	/**
	 * Get user contact methods from admin
	 *
	 * @param null $user_id
	 * @param null $user_name
	 * @param null $design
	 *
	 * @return string
	 */
	function iki_toolkit_get_user_contacts( $user_id = null, $user_name = null, $design = null ) {

		if ( ! $user_id ) {
			// try to get global author id
			global $authordata;
			if ( $authordata ) {
				$user_id = $authordata->ID;
			} else {
				$user_id = get_queried_object_id();
			}
		}

		$user_contacts = Iki_Toolkit_Utils::get_author_contacts( $user_id );

		if ( ! $user_name ) {
			$user_meta = get_userdata( $user_id );
			$user_name = $user_meta->display_name;
		}

		$link_title = esc_html( _x( 'on ', 'Title for the link to author social profile on some service', 'iki-toolkit' ) );

		if ( ! empty( $user_contacts ) ) {
			return iki_toolkit_print_social_profiles( $user_contacts, $user_name . ' ' . $link_title, $design, false );
		}
	}
}

/** Helper function for default plugin admin data
 * @return array
 */
function iki_toolkit_admin_data() {

	$theme_color_1          = '#4885ed';
	$theme_color_1_darken_5 = '#3176eb';

	$r = array(
		'available_social_profiles' => array(
			'twitter'   => 'Twitter',
			'facebook'  => 'Facebook',
			'github'    => 'Github',
			'pinterest' => 'Pinterest',
			'bitbucket' => 'BitBucket',
			'linkedin'  => 'LinkedIn',
			'vk'        => 'VK',
			'weibo'     => 'Weibo',
			'reddit'    => 'Reddit',
			'tumblr'    => 'Tumblr',
			'myspace'   => 'My space',
			'instagram' => 'Instagram',
			'dribbble'  => 'Dribbble',
			'flickr'    => 'Flickr',
			'500px'     => '500px',
		),
		'available_share_services'  => array(
			'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=',
			'twitter'   => 'https://twitter.com/intent/tweet?url=',
			'linkedin'  => 'https://www.linkedin.com/shareArticle?mini=true&url=',
			'vk'        => 'https://vk.com/share.php?url=',
			'weibo'     => 'https://service.weibo.com/staticjs/weiboshare.html?url=',
			'pinterest' => 'https://pinterest.com/pin/create/button?url=',
			'reddit'    => 'https://www.reddit.com/submit?url=',
//        'tumblr' => 'https://www.tumblr.com/share/link?url=',
			'tumblr'    => 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=',
			'buffer'    => 'https://bufferapp.com/add?url=',
			'digg'      => 'https://digg.com/submit?phase=2&url='
		),
		'default_share_services'    => array(
			'facebook' => 1,
			'twitter'  => 1,
			'linkedin' => 1,
		),
		'colors'                    => array(
			'theme_color'        => $theme_color_1,
			'theme_color_darken' => $theme_color_1_darken_5,
			'body'               => array(
				'color_bg' => '#ffffff'
			),
			'social_icons'       => array(
				'custom_background' => array(
					'fg' => '#000000',
					'bg' => 'rgba(0,0,0,1)'
				),
				'custom_symbol'     => array(
					'fg' => '#0000'
				)
			),
			'buttons'            => array(
				'color'              => '#ffffff',
				'color_hover'        => '#ffffff',
				'color_bg'           => $theme_color_1,
				'color_bg_hover'     => $theme_color_1_darken_5,
				'border_color'       => $theme_color_1,
				'border_color_hover' => $theme_color_1_darken_5
			),
			'hero'               => array(
				'background'       => array(
					'color_bg' => $theme_color_1,
				),
				'overlay'          => array(
					'gradient_1' => 'rgba(0,0,0,0.5)',
					'gradient_2' => 'rgba(0,0,0,0.5)',
				),
				'text_color'       => '#ffffff',
				'link_color'       => '#ff0000',
				'link_color_hover' => '#000000'
			),
			'fs_panel'           => array(
				'close_btn_color'    => '#ffffff',
				'close_btn_bg_color' => '#000000',
				'color'              => '#000000',
				'title_color'        => '#000000',
				'bg_color'           => '#ffffff',
				'overlay_bg_color'   => 'rgba(0,0,0,0,0)',
			),
			'custom_fs_elements' => array(
				'search' => array(
					'fs_panel_search_color'               => '#000000',
					'fs_panel_search_ph_color'            => '#666666',// placeholder color
					'fs_panel_search_bg_color'            => 'rgba(255,255,255,0)',
					'fs_panel_search_border_bottom_color' => 'rgba(0,0,0,0)'
				)
			)
		)
	);

	return $r;

}

/**
 * Check if we are on woocommerce page.
 * @return bool true if we are on woocommerce page
 */
function iki_toolkit_is_woocommerce_page() {
	$r = false;
	if ( class_exists( 'WooCommerce' ) ) {

		return ( is_woocommerce() || is_cart() || is_checkout() );

	}

	return $r;
}

if ( ! function_exists( 'iki_toolkit_woo_the_page_title' ) ) {
	/** Print woo shop page title
	 *
	 * @param $id
	 */
	function iki_toolkit_woo_the_page_title( $id ) {

		echo sprintf( '<h1 class="entry-title">%1$s</h1>', esc_html( get_the_title( $id ) ) );
	}
}

if ( ! function_exists( 'iki_toolkit_woo_the_page_subtitle' ) ) {
	/**
	 * Print woo page subtitle
	 *
	 * @param $id
	 */
	function iki_toolkit_woo_the_page_subtitle( $id ) {
		iki_post_subtitle( '<h3 class="entry-subtitle">', '</h3>', true, $id );

	}
}
