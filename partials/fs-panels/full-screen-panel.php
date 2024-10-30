<?php
/**
 * Partial for full screen panel element
 * */

$iki_fs_panel_name = sanitize_html_class( $name );

?>
<div id="<?php echo $iki_fs_panel_name; ?>" class="iki-fs-panel iki-offscreen <?php echo $iki_fs_panel_name; ?>">
	<div class="panel-overlay"></div>
	<?php do_action( 'iki_fs_panel_start', $iki_fs_panel_name ); ?>

	<div class="close-btn-wrap tooltip-js" aria-label="<?php esc_html_e( __( 'Close Panel', 'iki-toolkit' ) ); ?>"
		 title="<?php esc_html_e( __( 'Close Panel', 'iki-toolkit' ) ); ?>">
		<div class=" iki-close-btn iki-icon-cancel"><?php esc_html_e( __( 'Close Panel', 'iki-toolkit' ) ); ?></div>
	</div>
	<?php

	$iki_panel_class   = array( 'iki-fs-wrapper' );
	$iki_panel_class[] = ( $width === 'fixed' ) ? 'iki-fixed-width' : '';
	$iki_panel_class[] = ( $align === 'top' ) ? 'iki-align-top' : '';
	$iki_panel_class   = apply_filters( 'iki_fs_panel_class', $iki_panel_class, $iki_fs_panel_name );

	do_action( 'iki_panel_content_before', $iki_fs_panel_name );
	?>
	<div class="<?php echo Iki_Toolkit_Utils::sanitize_html_class_array( $iki_panel_class ); ?>">
		<?php

		iki_toolkit_print_content_block( $block_top, array( 'iki-fs-cb-top' ), $iki_fs_panel_name . '-top' );

		if ( isset( $custom_element ) ) {
			$element_name = ( isset( $custom_element['name'] ) ) ? $custom_element['name'] : 'none';
			do_action( 'iki_print_panel_custom_element', $element_name, $custom_element );
		}

		iki_toolkit_print_content_block( $block_bottom, array( 'iki-fs-cb-bottom' ), $iki_fs_panel_name . '-bottom' );
		?>
	</div><!--.fs-wrapper-->
	<?php do_action( 'iki_panel_content_after', $iki_fs_panel_name );
	do_action( 'iki_fs_panel_end', $iki_fs_panel_name ); ?>
</div><!--.iki-fs-panel-->

