<?php

/**
 * Class for creating breadcrumb navigation.
 * Modified from unyson plugin -> extension -> breadcrumbs
 * */
class Iki_Breadcrumbs {

	protected $settings = array();
	protected $blog_posts_permalink;

	public function __construct( $settings = array() ) {
		$this->settings['separator'] = ' / ';

		$this->settings['labels'] = array(
			'homepage-title' => __( 'Homepage', 'iki-toolkit' ),
			'blogpage-title' => __( 'Blog', 'iki-toolkit' ),
			'404-title'      => __( '404 Not found', 'iki-toolkit' ),
		);

		if ( isset( $settings['labels'] ) ) {
			$this->settings['labels'] = array_merge( $this->settings['labels'], $settings['labels'] );
		}

		if ( isset( $settings['separator'] ) && ! empty( $settings['separator'] ) ) {
			$this->settings['separator'] = $settings['separator'];
		}
	}

	/**
	 * Determine if the page has parents and in case it has, adds all page parents hierarchy
	 *
	 * @param $id , page id
	 *
	 * @return array
	 */
	private function get_page_hierarchy( $id ) {
		$page = get_post( $id );

		if ( empty( $page ) || is_wp_error( $page ) ) {
			return array();
		}

		$return   = array();
		$page_obj = array();

		$page_obj['type']      = 'post';
		$page_obj['post_type'] = $page->post_type;
		$page_obj['id']        = $id;
		$page_obj['name']      = apply_filters( 'the_title', $page->post_title, $id );
		$page_obj['url']       = get_permalink( $id );

		$return[] = $page_obj;
		if ( $page->post_parent > 0 ) {
			$return = array_merge( $return, $this->get_page_hierarchy( $page->post_parent ) );
		}

		return $return;
	}

	/**
	 * Determine if the term has parents and in case it has, adds all term parents hierarchy
	 *
	 * @param $id , term id
	 * @param $taxonomy , term taxonomy name
	 *
	 * @return array
	 */
	private function get_term_hierarchy( $id, $taxonomy ) {
		$term = get_term( $id, $taxonomy );

		if ( empty( $term ) || is_wp_error( $term ) ) {
			return array();
		}

		$return   = array();
		$term_obj = array();

		$term_obj['type']     = 'taxonomy';
		$term_obj['name']     = $term->name;
		$term_obj['id']       = $id;
		$term_obj['url']      = get_term_link( $id, $taxonomy );
		$term_obj['taxonomy'] = $taxonomy;

		$return[] = $term_obj;

		if ( $term->parent > 0 ) {
			$return = array_merge( $return, $this->get_term_hierarchy( $term->parent, $taxonomy ) );
		}

		return $return;
	}

	/**
	 * Get the data for blog
	 */
	protected function get_blog_node() {
		if ( ! $this->blog_posts_permalink ) {
			if ( 'page' === get_option( 'show_on_front' ) ) {
				$this->blog_posts_permalink = get_permalink( get_option( 'page_for_posts' ) );
			} else {
				$this->blog_posts_permalink = '';
			}
		}

		return array(
			'name' => $this->settings['labels']['blogpage-title'],
			'url'  => $this->blog_posts_permalink
		);

	}

	protected function maybe_add_blog_node( &$r ) {
		$blog = $this->get_blog_node();
		if ( ! empty( $blog['url'] ) ) {
			$r[] = $blog;
		}
	}

	/**
	 * Determine the current frontend page location, in creates the breadcrumbs array
	 * @return array
	 */
	private function build_breadcrumbs() {

		if ( is_admin() ) {
			return array();
		}

		if ( did_action( 'wp' ) == 0 ) {
			return array();
		}


		if ( is_front_page() ) {
			return;
		}

		$is_home = is_home();
		$r       = array(
			array(
				'name' => sanitize_text_field( $this->settings['labels']['homepage-title'] ),
				'url'  => esc_url( home_url( '/' ) ),
				'type' => 'front_page'
			)
		);

		$custom_page = apply_filters( 'iki_toolkit_breadcrumbs_current_page', array() );

		if ( is_array( $custom_page ) && ! empty( $custom_page ) ) {
			$r[] = $custom_page;
			$r   = apply_filters( 'iki_toolkit_breadcrumbs_build', $r );

			return $r;
		}

		if ( is_404() ) {
			$page = array();

			$page['type'] = '404';
			$page['name'] = sanitize_text_field( $this->settings['labels']['404-title'] );
			$page['url']  = Iki_Toolkit_Utils::get_current_url();

			$r[] = $page;
		} elseif ( is_search() ) {
			$search = array();

			$search['type'] = 'search';
			$search['name'] = __( 'Searching for:', 'iki-toolkit' ) . ' ' . esc_html( get_search_query() );
			$s              = '?s=' . apply_filters( 'iki_toolkit_breadcrumbs_search_query', esc_html( get_search_query() ) );
			$search['url']  = home_url( '/' ) . $s;

			$r[] = $search;
		} elseif ( $is_home ) {

			$r[] = $this->get_blog_node();

		} elseif ( is_page() ) {
			global $post;
			$r = array_merge( $r, array_reverse( $this->get_page_hierarchy( $post->ID ) ) );
		} elseif ( is_single() ) {
			global $post;

			if ( 'post' != $post->post_type || 'posts' !== get_option( 'show_on_front' ) ) {

				$postType          = get_post_type_object( get_post_type( $post ) );
				$post_name         = $postType->labels->singular_name;
				$post_archive_link = get_post_type_archive_link( $post->post_type );

				array_push( $r, array(
					'name' => $post_name,
					'url'  => $post_archive_link,
					'type' => 'archive'
				) );


			}

			$taxonomies = get_object_taxonomies( $post->post_type, 'objects' );
			$slugs      = array();
			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $key => $tax ) {
					if ( $tax->show_ui === true && $tax->public === true && $tax->hierarchical !== false ) {
						array_push( $slugs, $tax->name );
					}
				}

				$terms = wp_get_post_terms( $post->ID, $slugs );

				if ( ! empty( $terms ) ) {
					$lowest_term = $this->get_lowest_taxonomy_terms( $terms );
					$term        = $lowest_term[0];
					$r           = array_merge( $r,
						array_reverse( $this->get_term_hierarchy( $term->term_id, $term->taxonomy ) )
					);
				}
			}

			$r = array_merge( $r, array_reverse( $this->get_page_hierarchy( $post->ID ) ) );
		} elseif ( is_category() ) {
			$term_id = get_query_var( 'cat' );

			$this->maybe_add_blog_node( $r );
			$r = array_merge( $r, array_reverse( $this->get_term_hierarchy( $term_id, 'category' ) ) );

		} elseif ( is_tag() ) {
			$term_id = get_query_var( 'tag' );
			$term    = get_term_by( 'slug', $term_id, 'post_tag' );

			if ( empty( $term ) || is_wp_error( $term ) ) {
				return array();
			}

			$this->maybe_add_blog_node( $r );
			$tag = array();

			$tag['type']     = 'taxonomy';
			$tag['name']     = $term->name;
			$tag['url']      = get_term_link( $term_id, 'post_tag' );
			$tag['taxonomy'] = 'post_tag';
			$r[]             = $tag;
		} elseif ( is_tax() ) {
			$post_type = get_post_type();

			if ( $post_type && 'post' !== $post_type ) {

				$post_type_data = get_post_type_object( $post_type );

				$r[] = array(
					'name' => $post_type_data->labels->menu_name,
					'url'  => get_post_type_archive_link( $post_type )
				);
			}

			$term_id  = get_queried_object()->term_id;
			$taxonomy = get_query_var( 'taxonomy' );
			$r        = array_merge( $r, array_reverse( $this->get_term_hierarchy( $term_id, $taxonomy ) ) );

		} elseif ( is_author() ) {
			$author = array();

			$author['name'] = get_queried_object()->data->display_name;
			$author['id']   = get_queried_object()->data->ID;
			$author['url']  = get_author_posts_url( $author['id'], get_queried_object()->data->user_nicename );
			$author['type'] = 'author';

			$r[] = $author;
		} elseif ( is_date() ) {
			$date = array();

			if ( get_option( 'permalink_structure' ) ) {
				$day   = get_query_var( 'day' );
				$month = get_query_var( 'monthnum' );
				$year  = get_query_var( 'year' );
			} else {
				$m     = get_query_var( 'm' );
				$year  = substr( $m, 0, 4 );
				$month = substr( $m, 4, 2 );
				$day   = substr( $m, 6, 2 );
			}

			if ( is_day() ) {
				$date['name']      = mysql2date( apply_filters( 'iki_toolkit_breadcrumbs_date_day_format', 'd F Y' ),
					$day . '-' . $month . '-' . $year );
				$date['url']       = get_day_link( $year, $month, $day );
				$date['date_type'] = 'daily';
				$date['day']       = $day;
				$date['month']     = $month;
				$date['year']      = $year;
			} elseif ( is_month() ) {
				$date['name']      = mysql2date( apply_filters( 'iki_toolkit_breadcrumbs_date_month_format', 'F Y' ),
					'01.' . $month . '.' . $year );
				$date['url']       = get_month_link( $year, $month );
				$date['date_type'] = 'monthly';
				$date['month']     = $month;
				$date['year']      = $year;
			} else {
				$date['name']      = mysql2date( apply_filters( 'iki_toolkit_breadcrumbs_date_year_format', 'Y' ),
					'01.01.' . $year );
				$date['url']       = get_year_link( $year );
				$date['date_type'] = 'yearly';
				$date['year']      = $year;
			}

			$r[] = $date;
		} elseif ( is_archive() ) {
			$post_type = get_query_var( 'post_type' );
			if ( $post_type ) {
				$post_type_obj   = get_post_type_object( $post_type );
				$archive         = array();
				$archive['name'] = $post_type_obj->labels->menu_name;
				$archive['url']  = get_post_type_archive_link( $post_type );
				$r[]             = $archive;
			}
		}

		foreach ( $r as $key => $item ) {
			if ( empty( $item['name'] ) ) {
				$r[ $key ]['name'] = __( 'No title', 'iki-toolkit' );
			}
		}

		$r = apply_filters( 'iki_toolkit_breadcrumbs_build', $r );

		return $r;
	}


	/**
	 * Returns the lowest hierarchical term
	 *
	 * @param $terms
	 *
	 * @return bool
	 */
	private function get_lowest_taxonomy_terms( $terms ) {
		// if terms is not array or its empty don't proceed
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return false;
		}

		return $this->filter_terms( $terms );
	}

	private function filter_terms( $terms ) {
		$return_terms = array();
		$term_ids     = array();

		foreach ( $terms as $t ) {
			$term_ids[] = $t->term_id;
		}

		foreach ( $terms as $t ) {
			if ( $t->parent == false || ! in_array( $t->parent, $term_ids ) ) {
				// remove this term
			} else {
				$return_terms[] = $t;
			}
		}

		if ( count( $return_terms ) ) {
			return $this->filter_terms( $return_terms );
		} else {
			return $terms;
		}
	}

	/**
	 * Returns the breadcrumbs array
	 * @return string
	 */
	public function get_breadcrumbs() {
		return $this->build_breadcrumbs();
	}

	protected function render_breadcrumbs( $items, $separator ) {
		$separator = esc_html( $separator );
		if ( ! empty( $items ) ) : ?>
			<div class="iki-breadcrumbs iki-df-hide">
				<?php for ( $i = 0; $i < count( $items ); $i ++ ) : ?>
					<?php if ( $i == ( count( $items ) - 1 ) ) :
						$items[ $i ]['name'] = wp_kses( $items[ $i ]['name'], array() )
						?>
						<span class="last-item"><?php echo $items[ $i ]['name'] ?></span>
					<?php elseif ( $i == 0 ) : ?>
						<span class="first-item">
						<?php if ( isset( $items[ $i ]['url'] ) ) : ?>
							<a href="<?php echo esc_attr( $items[ $i ]['url'] ) ?>"><?php echo $items[ $i ]['name'] ?></a></span>
						<?php else : echo $items[ $i ]['name']; endif ?>
						<span class="separator"><?php echo $separator ?></span>
					<?php
					else : ?>
					<span class="<?php echo( $i - 1 ) ?>-item">
						<?php if ( isset( $items[ $i ]['url'] ) ) : ?>
							<a href="<?php echo esc_attr( $items[ $i ]['url'] ) ?>"><?php echo $items[ $i ]['name'] ?></a></span>
						<?php else : echo $items[ $i ]['name']; endif ?>
						<span class="separator"><?php echo $separator ?></span>
					<?php endif ?>
				<?php endfor ?>
			</div>
		<?php endif;
	}

	/**
	 * Print breadcrumbs
	 */
	public function print_breadcrumbs() {

		$data['items'] = $this->build_breadcrumbs();
		if ( ! empty( $data['items'] ) ) {

			foreach ( $data['items'] as $item ) {
				$item['name'] = sanitize_text_field( $item['name'] );
			}

		}
		$this->render_breadcrumbs( $data['items'], $this->settings['separator'] );
	}
}