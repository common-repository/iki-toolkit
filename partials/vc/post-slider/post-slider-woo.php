<?php

/**
 * Blog post (woocommerce product) slider shortcode partial
 *
 * available variables:
 * $layout - post layout options (from shortcode)
 * $atts - shortcode attributes
 * $title - html title
 * $subtitle - html subtitle
 * $date - html date
 * $comments - html comments
 * $link - link to post
 * $excerpt - post excerpt
 */

global $post;
$iki_terms = '';
if ( $layout['categories'] ) {
	$iki_terms = Iki_Toolkit_Utils::get_term_name_and_link( $post->ID, $layout['tax_name'] );
}
?>
	<div class="iki-vc-post woocommerce">
		<?php if ( 'above' == $layout['title_location'] ) {
			iki_toolkit_vc_the_post_listing_term( $iki_terms, $atts, $layout, $prefix );
			iki_toolkit_vc_post_title( $title, array( 'iki-vc-post-title-above' ) );
			iki_toolkit_vc_post_subtitle( $subtitle, array( 'iki-vc-post-subtitle-above' ) );
			iki_toolkit_vc_woo_price_rating_wrap_loop();
			?>
		<?php } ?>
		<?php if ( ! empty( $image ) ) { ?>
			<div class="iki-vc-post-img">
				<?php iki_toolkit_vc_woo_sale_flash(); ?>
				<a href="<?php echo $link ?>">
					<div class="<?php echo 'embed-responsive-' . $layout['image_orientation'] ?>">
						<?php echo $image; ?>
					</div>
				</a>
			</div>
		<?php } ?>
		<div class="iki-vc-text">
			<?php if ( 'above' != $layout['title_location'] ) { ?>
				<?php
				iki_toolkit_vc_the_post_listing_term( $iki_terms, $atts, $layout, $prefix );
				iki_toolkit_vc_post_title( $title );
				iki_toolkit_vc_post_subtitle( $subtitle );
				iki_toolkit_vc_woo_price_rating_wrap_loop();
			}
			iki_toolkit_vc_post_excerpt( $excerpt );
			iki_toolkit_vc_date_and_comments( $date, $comments );
			iki_toolkit_vc_woo_slide_btn( $post->ID );
			?>
		</div>
	</div>
<?php
