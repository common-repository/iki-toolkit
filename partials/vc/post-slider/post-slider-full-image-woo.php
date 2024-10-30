<?php


/**
 * Blog post (woocommerce product ) slider shortcode partial
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
		<?php if ( 'above' == $layout['title_location'] ) { ?>
			<div class="iki-vc-p-ts-wrap-outside">
				<?php iki_toolkit_vc_the_post_listing_term( $iki_terms, $atts, $layout, $prefix );
				iki_toolkit_vc_post_title( $title );
				iki_toolkit_vc_post_subtitle( $subtitle );
				iki_toolkit_vc_woo_price_rating_wrap_loop();
				?>
			</div>
		<?php } ?>
		<div class="iki-vc-p-image-title-wrap <?php echo 'embed-responsive-' . $layout['image_orientation'] ?>">
			<?php if ( ! empty( $image ) ) { ?>
				<a href="<?php echo $link ?>" class="iki-vc-post-img"><?php echo $image; ?></a>
			<?php }
			?>
			<?php if ( 'below' == $layout['title_location'] ) { ?>
				<?php
				iki_toolkit_vc_woo_slide_btn( $post->ID );
			} ?>
			<?php if ( 'inside' == $layout['title_location'] ) { ?>
				<?php iki_toolkit_vc_the_post_listing_term( $iki_terms, $atts, $layout, $prefix ) ?>
				<div class="iki-vc-p-ts-wrap-inside">
					<?php
					iki_toolkit_vc_post_title( $title );
					iki_toolkit_vc_post_subtitle( $subtitle );
					iki_toolkit_vc_woo_price_rating_wrap_loop(); ?>
				</div>
			<?php }//end location inside ?>
		</div>
		<?php if ( 'below' == $layout['title_location'] ) { ?>
			<div class="iki-vc-p-ts-wrap-outside">
				<?php
				iki_toolkit_vc_post_subtitle( $subtitle );
				iki_toolkit_vc_post_title( $title );
				iki_toolkit_vc_woo_price_rating_wrap_loop(); ?>
			</div>
		<?php } ?>
		<div class="iki-vc-text">
			<?php iki_toolkit_vc_post_excerpt( $excerpt );
			if ( 'below' == $layout['title_location'] ) {
				iki_toolkit_vc_date_and_comments( $date, $comments );
			}
			?>
		</div>
	</div>
<?php
