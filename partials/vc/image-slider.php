<?php

/**
 * Image slider shortcode partial
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

$iki_link_classes = array( 'iki-cell-wrap', 'iki-cell-wrap-img-vc' );
$image_html       = Iki_Toolkit_Utils::image_as_css_bg( $image, $atts['image_size'], array( 'iki-vc-p-img-bg' ) );

?>
<div class="iki-img-slide-vc">
	<div class="iki-img-bg-wrap-vc">
		<div class="<?php echo 'embed-responsive-' . $atts['image_orientation']; ?> ">
			<?php
			if ( isset( $image ) ) {
				echo $image;
			}
			if ( $link ) {
				echo $link;
			}
			?>
		</div>
	</div>
</div>
