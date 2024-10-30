<?php


/** Remove wp bakery page builder elements that are not supported by content blocks
 *
 * @param $arr
 * @param bool $include_global
 */
function iki_toolkit_remove_vc_elements_for_content_blocks( $arr, $include_global = false ) {

	$ignore_elements = array(
		"vc_widget_sidebar",
		'iki_content_block_vc'// NOTE - disable content blocks from showing inside other content block.
	);

	$all = ( $include_global ) ? array_merge( $ignore_elements, $arr ) : $arr;

	foreach ( $all as $vcElement ) {
		vc_remove_element( $vcElement );
	}

}


/** Print content blocks at specific places
 *
 * @param int|string $id Content block id
 * @param array $classes Additioanl classes
 * @param string $position position where is content block bein printed, used for filters
 */
function iki_toolkit_print_content_block( $id, $classes = array(), $position = '' ) {

	$id = ( 'disabled' == $id || is_null( $id ) ) ? '' : $id;
	$id = apply_filters( 'iki_toolkit_print_content_block_id', $id, $position );

	if ( empty( $id ) ) {
		return;
	}

	$classes = apply_filters( 'iki_toolkit_content_block_class', ( is_null( $classes ) ) ? array() : $classes, $id, $position );

	$cb_content = Iki_CB_Factory::get_instance()->content_block( $id, false );

	if ( ! empty( $cb_content ) ) {
		$classes[] = 'iki-block-' . $id;
		$classes   = Iki_Toolkit_Utils::sanitize_html_class_array( $classes );
		do_action( 'iki_toolkit_content_block_before', $id, $position );
		printf( '<div class="iki-content-block iki-cb-%2$s %1$s">', $classes, esc_attr( $position ) );
		do_action( 'iki_toolkit_content_block_start', $id, $position );
		echo $cb_content;
		do_action( 'iki_toolkit_content_block_end', $id, $position );
		echo '</div>';
		do_action( 'iki_toolkit_content_block_after', $id, $position );
	}
}


