<?php
/**
 * Hero section partial for search results
 */
if ( have_posts() ) { ?>
	<?php global $wp_query; ?>
	<h1 class="entry-title"><span class="search-count"><?php echo esc_attr( $wp_query->found_posts ); ?> </span>
		<?php echo esc_html( _n( 'Search result for : ', 'Search Results for :', $wp_query->found_posts, 'iki-toolkit' ) ); ?>
		<span class="iki-search-query"><?php echo '"' . esc_html( get_search_query() ) . '"'; ?></span>
	</h1>
	<?php
} else {
	?>
	<h1 class="entry-title"><?php echo esc_html( __( 'Nothing Found for: ', 'iki-toolkit' ) ); ?>
		<span class="iki-search-query"><?php echo '"' . esc_html( get_search_query() ) . '"'; ?></span>
	</h1>
<?php } ?>

