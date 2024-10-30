<?php

/**
 * Class with miscellaneous helper methods for the plugin
 * @since 1.0.0
 */
class Iki_Toolkit_Utils {


	protected static $blogAuthors = null;
	public $ignoredProps;
	public static $image_sizes = array(
		'thumbnail',
		'medium',
		'medium_large',
		'large',
		'full',
		'thumb'
	);

	/** Sanitize nested array of value against html class sanitization method.
	 *  This is basically a wrapper for "sanitize_html_class"
	 *
	 * @param $classes array Array of classes to sanitize
	 *
	 * @return string sanitized string
	 */
	public static function sanitize_html_class_array( $classes ) {
		$classes = join( ' ', $classes );

		return esc_attr( $classes );
	}


	/** Convert array data to html attributes
	 *  And sanitize it
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public static function array_to_html_attr( $data ) {
		$s = '';
		foreach ( $data as $key => $value ) {

			if ( ! is_array( $value ) ) {
				$value = esc_attr( $value );
			}

			$s .= esc_attr( $key ) . '=' . json_encode( $value ) . ' ';

		}

		return $s;
	}

	/**
	 * Get current url
	 * @return string Current url
	 */
	public static function get_current_url() {
		static $url = null;

		if ( $url === null ) {
			$url = 'http://';

			$server_wildcard_or_regex = preg_match( '/(^~\^|^\*\.|\.\*$)/', $_SERVER['SERVER_NAME'] );

			if ( $_SERVER['SERVER_NAME'] === '_' || 1 == $server_wildcard_or_regex ) { // https://github.com/ThemeFuse/Unyson/issues/126
				$url .= $_SERVER['HTTP_HOST'];
			} else {
				$url .= $_SERVER['SERVER_NAME'];
			}

			if ( ! in_array( intval( $_SERVER['SERVER_PORT'] ), array( 80, 443 ) ) ) {
				$url .= ':' . $_SERVER['SERVER_PORT'];
			}

			$url .= $_SERVER['REQUEST_URI'];

			$url = set_url_scheme( $url ); // https fix

			if ( is_multisite() ) {
				if ( defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL ) {
					$site_url = parse_url( $url );

					if ( isset( $site_url['query'] ) ) {
						$url = home_url( $site_url['path'] . '?' . $site_url['query'] );
					} else {
						$url = home_url( $site_url['path'] );
					}
				}
			}
		}

		return $url;
	}

	/**
	 * Get grid options from admin. Helper method.
	 *  Returns false if there are no options to return.
	 *
	 * @param $id
	 * @param string $grid_location
	 *
	 * @return array|bool
	 */
	public static function get_grid_options( $id, $grid_location = '' ) {

		$grid_data = Iki_Grids::get_instance()->find( $id );

		if ( ! $grid_data ) {
			return false;
		}

		$grid_data['location'] = $grid_location;
		if ( ! is_array( $grid_data['classes'] ) ) {
			$grid_data['classes'] = array( $grid_data['classes'] );
		}
		$grid_data['fill_grid'] = apply_filters( 'iki_grid_fill', $grid_data['fill_grid'], $grid_location );
		$grid_data['condensed'] = apply_filters( 'iki_grid_condensed', $grid_data['condensed'], $grid_location );

		return $grid_data;

	}

	/** Generate style tag for image background
	 *
	 * @param $img_id
	 * @param $image_size
	 * @param array $classes
	 *
	 * @return string
	 */
	public static function image_as_css_bg( $img_id, $image_size, $classes = array() ) {

		$r         = '';
		$classes[] = 'iki-css-img-holder';
		if ( ! empty( $img_id ) ) {

			$img_src = wp_get_attachment_image_src( $img_id, $image_size );

			if ( ! empty( $img_src ) ) {

				$img_src = sprintf( 'style="background-image: url(\'%1$s\');"', esc_url( $img_src[0] ) );

			} else {
				$classes[] = 'iki-no-css-img';

			}
			$classes = self::sanitize_html_class_array( $classes );
			$r       = sprintf( '<div class="%2$s" %1$s ></div>',
				$img_src,
				$classes );
		}

		return $r;
	}

	/**
	 * Check string against another string and return true if they match.
	 *
	 * @param $check
	 * @param string $true_string default string to match against
	 *
	 * @return bool
	 */
	public static function string_to_boolean( $check, $true_string = 'enabled' ) {
		if ( is_bool( $check ) ) {
			return $check;
		}

		return ( $check === $true_string ) ? true : false;
	}

	/**
	 * Bulk update transients by transient key
	 *
	 * @param $transient_key
	 * @param string $prefix
	 */
	public static function update_transient_list( $transient_key, $prefix = 'iki_tk' ) {
		// Get the current list of transients.
		$key            = "{$prefix}_transients";
		$transient_keys = get_option( $key );

		// Append our new one.
		if ( $transient_keys ) {
			$transient_keys[] = $transient_key;
			// Save it to the DB.
			update_option( $key, $transient_keys );
		} else {
			$transient_keys = array(
				$transient_key
			);
			update_option( $key, $transient_keys );
		}
	}

	/**
	 * Check if image size is supported
	 *
	 * @param $image_size
	 *
	 * @return bool
	 */
	public static function has_image_size( $image_size ) {

		return in_array( $image_size, self::$image_sizes ) || has_image_size( $image_size );
	}


	/**
	 * Get taxonomy name and link as array
	 *
	 * @param $post_id
	 * @param $taxonomy
	 * @param int $how_many
	 *
	 * @return array
	 */
	public static function get_term_name_and_link( $post_id, $taxonomy, $how_many = 1 ) {
		$r = array();

		/**@var WP_Term $terms */
		$terms = wp_get_post_terms( $post_id, $taxonomy );

		if ( ! empty( $terms ) ) {

			for ( $i = 0; $i < $how_many; $i ++ ) {
				if ( isset( $terms[ $i ] ) ) {
					array_push( $r, array(
						'name' => $terms[ $i ]->name,
						'link' => esc_url( get_term_link( $terms[ $i ]->slug, $taxonomy ) ),
						'id'   => $terms[ $i ]->term_id
					) );
				}
			}
		}

		return $r;
	}

	/**
	 * Wrap content in link
	 *
	 * @param $link
	 * @param $content
	 *
	 * @return string
	 */
	public static function wrap_in_link( $link, $content ) {

		return sprintf( '<a href="%1$s">%2$s</a>', $link, $content );

	}

	/**
	 * Create style tag with custom colors for each "term"
	 *
	 * @param $term_id int term id
	 * @param $taxonomy string taxonomy name  (category etc..)
	 *
	 * @return string
	 */
	public static function get_taxonomy_colors( $term_id, $taxonomy ) {

		$r = '';

		if ( isset( $GLOBALS['iki_toolkit_admin'] ) ) {
			$default_theme_color    = $GLOBALS['iki_toolkit_admin']['colors']['buttons']['color'];
			$default_theme_bg_color = $GLOBALS['iki_toolkit_admin']['colors']['buttons']['color_bg'];

			$tax_color = sprintf( 'color:%1$s;', iki_toolkit()->get_term_option( $term_id,
				$taxonomy,
				'tax_color',
				$default_theme_color ) );

			$tax_color_bg = sprintf( 'background-color:%1$s;', iki_toolkit()->get_term_option( $term_id,
				$taxonomy,
				'tax_color_bg',
				$default_theme_bg_color ) );

			if ( ! empty( $tax_color ) || ! empty( $tax_color_bg ) ) {
				$r .= sprintf( 'style="%1$s %2$s"', $tax_color, $tax_color_bg );
			}
		}

		return $r;
	}


	/**
	 * Truncate string to character limit
	 *
	 * @param $string
	 * @param $limit
	 * @param string $break
	 * @param string $pad
	 *
	 * @return string
	 */
	public static function truncate_string( $string, $limit, $break = ".", $pad = "..." ) {
		// return with no change if string is shorter than $limit
		if ( strlen( $string ) <= $limit ) {
			return $string;
		}

		// is $break present between $limit and the end of the string?
		if ( false !== ( $breakpoint = strpos( $string, $break, $limit ) ) ) {
			if ( $breakpoint < strlen( $string ) - 1 ) {
				$string = substr( $string, 0, $breakpoint ) . $pad;
			}
		}

		return $string;
	}

	/**
	 * Social profiles chosen by the user
	 * @return array social profiles
	 */
	public static function get_chosen_social_profiles() {

		$r                    = array();
		$share_services_popup = null;

		$share_services_popup = get_option( 'iki_toolkit_social_profiles' );

		if ( $share_services_popup ) {
			if ( isset( $share_services_popup['default_share'] ) ) {
				unset( $share_services_popup['default_share'] );
			}
			if ( isset( $share_services_popup['fake_separator'] ) ) {
				unset( $share_services_popup['fake_separator'] );
			}
			foreach ( $share_services_popup as $key => $value ) {

				$value = trim( $value );
				if ( ! empty( $value ) ) {
					$r[ $key ] = $value;
				}
			}

		}

		return $r;
	}

	/**
	 * Parse post sharing data
	 *
	 * @param null|array $design design data
	 *
	 * @return null | array parsed design
	 */
	public static function parse_post_sharing_design( $design = null ) {

		if ( ! $design ) {

			$design = array(
				'fg'            => '',
				'bg'            => '',
				'rounded'       => '0',
				'spread'        => '0',
				'design'        => 'dark',
				'chosen_design' => 'pre_made'
			);

		} elseif ( isset( $design['chosen_design'] ) && isset( $design[ $design['chosen_design'] ] ) ) {

			$chosen = $design['chosen_design'];
			$design = $design[ $chosen ];

			$design['chosen_design'] = $chosen;

		}

		$design['fg']      = ( isset( $design['fg'] ) ) ? $design['fg'] : '';
		$design['bg']      = ( isset( $design['bg'] ) ) ? $design['bg'] : '';
		$design['spread']  = ( isset( $design['spread'] ) ) ? $design['spread'] : '';
		$design['rounded'] = ( isset( $design['rounded'] ) ) ? $design['rounded'] : '';


		$chosen          = isset( $design['chosen_design'] ) ? $design['chosen_design'] : 'custom';
		$design['class'] = isset( $design['class'] ) ? $design['class'] : '';
		if ( $chosen == 'custom_symbol' ) {


			$design['class'] = 'sc-custom-symbol';

		} elseif ( $chosen == 'pre_made' ) {
			$design['design'] = str_replace( 'classic-', '', $design['design'] );
			$design['class']  = 'sc-' . $design['design'];
		} elseif ( $chosen == 'custom_background' ) {

			$design['class'] = 'sc-custom-background';

		}

		return $design;
	}


	/**
	 * Search array for part of the key and extract if present
	 *
	 * @param array $haystack haystack
	 * @param string $needle needle to search for
	 *
	 * @return array
	 */
	public static function array_extract_part_by_key( $haystack, $needle ) {

		$r = array();

		foreach ( $haystack as $key => $value ) {
			if ( strpos( $key, $needle ) === 0 ) {
				$r[ $key ] = $value;
			}
		}

		return $r;
	}

	/**
	 * Search and replace keys in array
	 *
	 * @param $haystack
	 * @param $needle
	 * @param string $replace
	 *
	 * @return array
	 */
	public static function array_modify_keys( $haystack, $needle, $replace = '' ) {

		$r = array();

		foreach ( $haystack as $key => $value ) {

			$key       = str_replace( $needle, $replace, $key );
			$r[ $key ] = $value;
		}

		return $r;
	}

	/**
	 * Create sass variables as one string from array
	 *
	 * @param $haystack
	 * @param string $needle
	 *
	 * @return string
	 */
	public static function array_to_sass_string( $haystack, $needle = '$' ) {
		{

			$r = '';

			foreach ( $haystack as $key => $value ) {

				$prefix = ( strpos( $key, $needle ) !== 0 ) ? $needle : '';
				$r      .= $prefix . $key . ':' . $value . ';' . PHP_EOL;
			}

			return $r;
		}

	}

	/**
	 * Create css string of value for background image, from array data
	 *
	 * @param $haystack
	 * @param string $imageSize
	 *
	 * @return string
	 */
	public static function construct_css_background_url( $haystack, $imageSize = 'large' ) {

		$r = '';

		foreach ( $haystack as $key => $value ) {
			$img = null;

			if ( 'null' != $value ) {
				$img = wp_get_attachment_image_src( $value, $imageSize );
			}

			if ( $img ) {
				$value = 'url(' . $img[0] . ');';
				$r     .= $key . ':' . $value . PHP_EOL;
			} else {
				$r .= $key . ':null;' . PHP_EOL;
			}
		}

		return $r;
	}


	/**
	 * Prepare all option values that start with sass_ for use in generating sass data
	 *
	 * @param $options
	 *
	 * @return array
	 */
	public static function prepare_sass_vars( $options ) {
		$r = array(
			'string' => '',
			'data'   => array()
		);

		if ( is_array( $options ) ) {
			foreach ( $options as $key => $value ) {
				if ( strpos( $key, 'sass_' ) === 0 ) {
					$key   = '$' . substr( $key, 5 );
					$value = empty( $value ) ? 'null' : $value;
					if ( is_array( $value ) ) {
						$value = $value['attachment_id'];
					} else {
						$r['string'] .= sprintf( '%s: %s;' . PHP_EOL, $key, $value );
					}
					$r['data'][ $key ] = $value;
				}
			}
		}

		return $r;

	}


	/**
	 * Create css gradient string from array of data.
	 *
	 * @param $classes
	 * @param $data
	 * @param bool $fallback
	 *
	 * @return string
	 */
	public static function construct_css_gradient( $classes, $data, $fallback = true ) {
		// setup gradient
		$css = '';
		$css .= " {$classes} {";
		if ( $fallback ) {
			$css .= "background:{$data['color_1']};";
		}

		$css .= "background:linear-gradient(
					to {$data['orientation']},
					{$data['color_1']} {$data['color_1_start']}% ,
					{$data['color_2']} {$data['color_2_start']}%
				);}";

		return $css;
	}

	/**
	 * Get oembed data to be used on pages that are not post types
	 *
	 * @param $identification
	 * @param $url
	 * @param array $attr
	 *
	 * @return mixed
	 */
	public static function get_oembed_for_non_posts( $identification, $url, $attr = array() ) {
		global $wp_embed;

		if ( is_numeric( $identification ) ) {

			return $wp_embed->shortcode( $attr, $url );
		}

		if ( ! defined( 'FW' ) ) {
			return wp_oembed_get( $url, $attr );
		}

		$current_url_hash = md5( trim( $url ) );
		$cachekey         = '_oembed_cache'; //hidden fields in unyson
		$cachekey_time    = '_oembed_time_';// hidden fields in unyson
		$cachekey_url     = '_oembed_url';

		/**
		 * Filter the oEmbed TTL value (time to live).
		 *
		 * @since 4.0.0
		 *
		 * @param int $time Time to live (in seconds).
		 * @param string $url The attempted embed URL.
		 * @param array $attr An array of shortcode attributes.
		 * @param int $post_ID Post ID.
		 */
		$ttl = apply_filters( 'iki_oembed_ttl', DAY_IN_SECONDS, $url, $attr, $identification );

		$cache_time = false;// fw_get_db_customizer_option( $identification . $cachekey_time, false );
		$cache_url  = false;// fw_get_db_customizer_option( $identification . $cachekey_url, false );
		$cache      = false;// fw_get_db_customizer_option( $identification . $cachekey, false );

		$get_from_cache = false;

		if ( ! empty( $cache_url ) ) {
			// compare urls
			if ( $cache_url === $current_url_hash ) {
				// different urls, get new data
				$get_from_cache = true;
			}
		}

		if ( ! $cache_time ) {
			$cache_time = 0;
		}

		$cached_recently = ( time() - $cache_time ) < $ttl;


		if ( $cached_recently && $get_from_cache ) {
			// Failures are cached. Serve one if we're using the cache.
			if ( '{{unknown}}' === $cache ) {
				return self::make_link( $url );
			}

			if ( ! empty( $cache ) ) {
				/**
				 * Filter the cached oEmbed HTML.
				 *
				 * @since 2.9.0
				 *
				 * @see WP_Embed::shortcode()
				 *
				 * @param mixed $cache The cached HTML result, stored in post meta.
				 * @param string $url The attempted embed URL.
				 * @param array $attr An array of shortcode attributes.
				 * @param int $post_ID Post ID.
				 */
				return apply_filters( 'iki_embed_oembed_html', $cache, $url, $attr, $identification );
			}
		}

		/**
		 * Filter whether to inspect the given URL for discoverable link tags.
		 *
		 * @since 2.9.0
		 * @since 4.4.0 The default value changed to true.
		 *
		 * @see WP_oEmbed::discover()
		 *
		 * @param bool $enable Whether to enable `<link>` tag discovery. Default true.
		 */
		$attr['discover'] = ( apply_filters( 'embed_oembed_discover', true ) );

		$html = wp_oembed_get( $url, $attr );

		// Maybe cache the result
		if ( $html ) {

			fw_set_db_customizer_option( $identification . $cachekey, $html );
			fw_set_db_customizer_option( $identification . $cachekey_time, time() );
			fw_set_db_customizer_option( $identification . $cachekey_url, $current_url_hash );

		} elseif ( ! $cache ) {
			fw_set_db_customizer_option( $identification . $cachekey, '{{unknown}}' );
		}

		// If there was a result, return it
		if ( $html ) {
			/** This filter is documented in wp-includes/class-wp-embed.php */
			return apply_filters( 'iki_embed_oembed_html', $html, $url, $attr, $identification );
		}

		// Still unknown
		return self::make_link( $url );
	}

	/**
	 * Get all blog authors
	 * @return array|null
	 */
	public static function get_blog_authors() {

		if ( is_null( self::$blogAuthors ) ) {

			$allUsers          = get_users( 'orderby=post_count&order=DESC' );
			self::$blogAuthors = array();

			// Remove subscribers from the list as they won't write any articles
			foreach ( $allUsers as $currentUser ) {
				if ( ! in_array( 'subscriber', $currentUser->roles ) ) {
					self::$blogAuthors[] = $currentUser;
				}
			}
		}

		return self::$blogAuthors;
	}

	/**
	 * Wrap url in to link
	 *
	 * @param string $url
	 *
	 * @return string html link
	 */
	public static function make_link( $url ) {

		$output = ( true ) ? '<a href="' . esc_url( $url ) . '">' . esc_html( $url ) . '</a>' : $url;

		/**
		 * Filter the returned, maybe-linked embed URL.
		 *
		 * @since 2.9.0
		 *
		 * @param string $output The linked or original URL.
		 * @param string $url The original URL.
		 */
		return apply_filters( 'embed_maybe_make_link', $output, $url );
	}

	/**
	 * Get contacts that the user enabled
	 * @return array
	 */
	public static function get_available_social_profiles() {
		return $GLOBALS['iki_toolkit_admin']['available_social_profiles'];
	}

	/**
	 * Get author contac options
	 *
	 * @param null $userId user id
	 *
	 * @return array user contacts
	 */
	public static function get_author_contacts( $userId = null ) {


		$user_meta        = get_user_meta( $userId );
		$user_contacts    = array();
		$enabled_contacts = Iki_Post_Utils::get_available_social_profiles();
		$prefix           = 'iki_contact_';

		foreach ( $enabled_contacts as $key => $value ) {

			if ( isset( $user_meta[ $prefix . $key ] ) && ! empty( $user_meta[ $prefix . $key ][0] ) ) {
				$user_contacts[ $key ] = $user_meta[ $prefix . $key ][0];
			}

		}

		return $user_contacts;
	}
}
