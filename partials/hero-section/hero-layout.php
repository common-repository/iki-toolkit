<?php
/**
 * Default blog hero section partial
 */
?>
<section id="iki-hero-section"<?php iki_toolkit_hero_section_data();
iki_toolkit_hero_section_class( 'iki-hero-section' ); ?>>
	<?php do_action( 'iki_hero_section_start' ); ?>
	<div id="iki-hero-content" <?php iki_toolkit_hero_content_class( 'iki-hero-content' ); ?>>
		<div class="iki-hs-content-wrap">
			<?php
			do_action( 'iki_hero_section_content_start' );
			do_action( 'iki_hero_section_content_end' );
			?>
		</div><!-- .iki-hs-content-wrap-->
	</div><!--.iki-hero-content-->
	<?php do_action( 'iki_hero_section_end' ); ?>
</section>
