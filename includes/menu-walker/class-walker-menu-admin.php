<?php
/**
 *Class for implementing custom walker menu in the backend.
 *It prints custom fields/options in the backend menu
 * */

class Iki_Walker_Menu_Admin extends Walker_Nav_Menu {


	protected $iki_content_block_cpt_present = false;
	protected $block_posts;

	/**
	 * Iki_Walker_Menu_Admin constructor.
	 */
	public function __construct() {
		if ( class_exists( 'Iki_Content_Block_CPT' ) ) {
			$args = array(
				'post_type'        => 'iki_content_block',
				'suppress_filters' => 0,
				'numberposts'      => - 1,
				'order'            => 'ASC',
				'post_status'      => 'publish',
				'include_children' => false,
				'tax_query'        => array(
					array(
						'taxonomy' => 'iki_content_block_cat',
						'field'    => 'slug',
						'terms'    => 'menu',
					),
				),
			);

			$this->iki_content_block_cpt_present = true;
			$this->block_posts                   = get_posts( $args );
		}
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker_Nav_Menu::start_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker_Nav_Menu::end_lvl()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param array $args Not used.
	 * @param int $id Not used.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_wp_nav_menu_max_depth;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

		ob_start();
		$item_id      = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);

		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) ) {
				$original_title = false;
			}
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title  = get_the_title( $original_object->ID );
		}

		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
		);

		$title = $item->title;

		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __( '%s (Pending)' ), $item->title );
		}

		$title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

		$submenu_text = '';
		if ( 0 == $depth ) {
			$submenu_text = 'style="display: none;"';
		}

		?>
	<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode( ' ', $classes ); ?>">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span
						class="is-submenu" <?php echo sanitize_text_field( $submenu_text ); ?>><?php esc_html_e( 'sub item', 'iki-toolkit' ); ?></span></span>
				<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-up-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-up"><abbr
									title="<?php esc_attr_e( 'Move up', 'iki-toolkit' ); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
							echo wp_nonce_url(
								add_query_arg(
									array(
										'action'    => 'move-down-menu-item',
										'menu-item' => $item_id,
									),
									remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) )
								),
								'move-menu_item'
							);
							?>" class="item-move-down"><abbr
									title="<?php esc_attr_e( 'Move down', 'iki-toolkit' ); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>"
						   title="<?php esc_attr_e( 'Edit Menu Item', 'iki-toolkit' ); ?>" href="<?php
						echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php esc_html_e( 'Edit Menu Item', 'iki-toolkit' ); ?></a>
					</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
			<?php if ( 'custom' == $item->type ) : ?>
				<p class="field-url description description-wide">
					<label for="edit-menu-item-url-<?php echo $item_id; ?>">
						<?php esc_html_e( 'URL', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>"
							   class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->url ); ?>"/>
					</label>
				</p>
			<?php endif; ?>
			<p class="description description-thin">
				<label for="edit-menu-item-title-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Navigation Label', 'iki-toolkit' ); ?><br/>
					<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>"
						   class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]"
						   value="<?php echo esc_attr( $item->title ); ?>"/>
				</label>
			</p>

			<p class="description description-thin">
				<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Title Attribute', 'iki-toolkit' ); ?><br/>
					<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>"
						   class="widefat edit-menu-item-attr-title"
						   name="menu-item-attr-title[<?php echo $item_id; ?>]"
						   value="<?php echo esc_attr( $item->post_excerpt ); ?>"/>
				</label>
			</p>

			<p class="field-link-target description">
				<label for="edit-menu-item-target-<?php echo $item_id; ?>">
					<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank"
						   name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
					<?php esc_html_e( 'Open link in a new window/tab', 'iki-toolkit' ); ?>
				</label>
			</p>

			<p class="field-css-classes description description-wide">
				<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
					<?php esc_html_e( 'CSS Classes (optional)', 'iki-toolkit' ); ?><br/>
					<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>"
						   class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]"
						   value="<?php echo esc_attr( implode( ' ', $item->classes ) ); ?>"/>
				</label>
			</p>

			<p class="field-xfn description description-thin">
				<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Link Relationship (XFN)', 'iki-toolkit' ); ?><br/>
					<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>"
						   class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]"
						   value="<?php echo esc_attr( $item->xfn ); ?>"/>
				</label>
			</p>

			<p class="field-description description description-wide">
				<label for="edit-menu-item-description-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Description', 'iki-toolkit' ); ?><br/>
					<textarea id="edit-menu-item-description-<?php echo $item_id; ?>"
							  class="widefat edit-menu-item-description" rows="3" cols="20"
							  name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped
						?></textarea>
					<span
						class="description"><?php esc_html_e( 'The description will be displayed in the menu if the current theme supports it.', 'iki-toolkit' ); ?></span>
				</label>
			</p>

			<p class="field-move hide-if-no-js description description-wide">
				<label>
					<span><?php esc_html_e( 'Move', 'iki-toolkit' ); ?></span>
					<a href="#" class="menus-move menus-move-up"
					   data-dir="up"><?php esc_html_e( 'Up one', 'iki-toolkit' ); ?></a>
					<a href="#" class="menus-move menus-move-down"
					   data-dir="down"><?php esc_html_e( 'Down one', 'iki-toolkit' ); ?></a>
					<a href="#" class="menus-move menus-move-left" data-dir="left"></a>
					<a href="#" class="menus-move menus-move-right" data-dir="right"></a>
					<a href="#" class="menus-move menus-move-top"
					   data-dir="top"><?php esc_html_e( 'To the top', 'iki-toolkit' ); ?></a>
				</label>
			</p>

			<!-- CUSTOM INSERTION START HERE -->
			<p class="field-custom description description-wide">
				<label for="edit-menu-item-iconclass-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Menu font awesome css icon (fa-info, fa-home etc..)', 'iki-toolkit' ); ?><br/>
					<input type="text" id="edit-menu-item-iconclass-<?php echo $item_id; ?>"
						   class="widefat code edit-menu-item-custom"
						   name="menu-item-iki_icon_class[<?php echo $item_id; ?>]"
						   value="<?php echo esc_attr( $item->iki_icon_class ); ?>"/>
				</label>
			</p>
			<p class="field-custom description description-wide iki-tl-options ">
				<label for="edit-menu-item-type-menu-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Menu Icon size', 'iki-toolkit' ); ?><br/>
					<select id="edit-menu-item-type-menu-<?php echo $item_id; ?>"
							name="menu-item-iki_menu_icon_size[<?php echo $item_id; ?>]">
						<option
							value="s" <?php if ( esc_attr( $item->iki_menu_icon_size ) === "s" || esc_attr( $item->iki_menu_icon_size ) == '' ) {
							echo 'selected="selected"';
						} ?>><?php esc_html_e( 'Small', 'iki-toolkit' ) ?>
						</option>
						<option value="m" <?php if ( esc_attr( $item->iki_menu_icon_size ) === "m" ) {
							echo 'selected="selected"';
						} ?>><?php esc_html_e( 'Medium', 'iki-toolkit' ); ?></option>
						<option value="l" <?php if ( esc_attr( $item->iki_menu_icon_size ) === "l" ) {
							echo 'selected="selected"';
						} ?>><?php esc_html_e( 'Large', 'iki-toolkit' ); ?></option>
						<option value="xl" <?php if ( esc_attr( $item->iki_menu_icon_size ) === "xl" ) {
							echo 'selected="selected"';
						} ?>><?php esc_html_e( 'Extra large', 'iki-toolkit' ); ?></option>
					</select>
				</label>
			</p>
			<p class="field-custom description description-wide iki-tl-options ">
				<label for="edit-menu-item-dropdown-arrow-<?php echo $item_id; ?>">
					<input type="checkbox"
						   id="edit-menu-item-dropdown-arrow-<?php echo $item_id; ?>"
						   name="menu-item-iki_dropdown_arrow[<?php echo $item_id; ?>]"
						<?php checked( $item->iki_dropdown_arrow, 'iki_dropdown' ); ?>
						   value="iki_dropdown"/>
					<?php esc_html_e( 'Show dropdown arrow', 'iki-toolkit' ); ?>
				</label>
			</p>
			<p class="field-custom description iki-tl-options">
				<label for="edit-menu-item-type-menu-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Force float menu item', 'iki-toolkit' ); ?><br/>
					<select id="edit-menu-item-type-menu-<?php echo $item_id; ?>"
							name="menu-item-iki_menu_float[<?php echo $item_id; ?>]">
						<option
							value="left" <?php if ( esc_attr( $item->iki_menu_float ) === "left" || esc_attr( $item->iki_menu_float ) == '' ) {
							echo 'selected="selected"';
						} ?>><?php esc_html_e( 'Left', 'iki-toolkit' ) ?>
						</option>
						<option
							value="right" <?php if ( esc_attr( $item->iki_menu_float ) === "right" ) {
							echo 'selected="selected"';
						} ?>><?php esc_html_e( 'Right', 'iki-toolkit' ); ?>
						</option>
					</select>
				</label>
			</p>
			<div class="iki-tl-options">
				<p class="field-custom description description-wide">
					<?php esc_html_e( 'Assign Z-index value (number - integer) to the button that has a stamp , so if the stamp is outside of the button , it won\'t be overlapped by some other top level button menu.', 'iki-toolkit' ); ?>
				</p>
				<p class="field-custom description description-wide1">
					<label for="edit-menu-item-stamp-z-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Z Index (optional) :', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-stamp-z-<?php echo $item_id; ?>"
							   class=" code edit-menu-item-custom"
							   name="menu-item-iki_menu_z_index[<?php echo $item_id; ?>]"
							   value="<?php echo ( ! empty( $item->iki_menu_z_index ) ) ? esc_attr( $item->iki_menu_z_index ) : 0 ?>"/>
					</label>
				</p>
			</div>


			<p class="field-custom iki-stamp-chb-parent description description-wide ">
				<label for="edit-menu-item-has-new-stamp-<?php echo $item_id; ?>">
					<input class="iki-stamp-menu-chb" type="checkbox"
						   id="edit-menu-item-has-new-stamp-<?php echo $item_id; ?>"
						   name="menu-item-iki_has_new_stamp[<?php echo $item_id; ?>]"
						<?php checked( $item->iki_has_new_stamp, 'iki_has_new_stamp' ); ?>
						   value="iki_has_new_stamp"/>
					<?php esc_html_e( 'Insert stamp on the menu button', 'iki-toolkit' ); ?>
				</label>
			</p>

			<div id="iki-stamp-options-<?php echo $item_id; ?>" class="iki-stamp-options">
				<p class="field-custom description description-wide">
					<label for="edit-menu-item-stamp-text-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Stamp Text', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-stamp-text-<?php echo $item_id; ?>"
							   class="widefat code edit-menu-item-custom "
							   name="menu-item-iki_stamp_text[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->iki_stamp_text ); ?>"/>
					</label>
				</p>

				<p class="field-custom iki-stamp-chb-parent description ">
					<label for="edit-menu-item-animate-stamp-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Stamp Animation', 'iki-toolkit' ); ?> <br>
						<select id="edit-menu-item-type-menu-<?php echo $item_id; ?>"
								name="menu-item-iki_menu_stamp_animation[<?php echo $item_id; ?>]">
							<option
								value="none" <?php if ( $item->iki_menu_stamp_animation === "none" ||
								                        $item->iki_menu_stamp_animation == ''
							) {
								echo 'selected="selected"';
							} ?>><?php esc_html_e( 'No Animation', 'iki-toolkit' ); ?>
							</option>
							<option value="pulse" <?php if ( $item->iki_menu_stamp_animation === "pulse" ) {
								echo 'selected="selected"';
							} ?>><?php esc_html_e( 'Pulse', 'iki-toolkit' ) ?>
							</option>
							<option
								value="pulse-2" <?php if ( $item->iki_menu_stamp_animation === "pulse-2" ) {
								echo 'selected="selected"';
							} ?>><?php esc_html_e( 'Pulse Alternative', 'iki-toolkit' ) ?>
							</option>
							<option value="swing" <?php if ( $item->iki_menu_stamp_animation === "swing" ) {
								echo 'selected="selected"';
							} ?>><?php esc_html_e( 'Swing', 'iki-toolkit' ); ?>
							</option>
							<option
								value="swing-2" <?php if ( $item->iki_menu_stamp_animation === "swing-2" ) {
								echo 'selected="selected"';
							} ?>><?php esc_html_e( 'Swing Alternative', 'iki-toolkit' ); ?>
							</option>
						</select>
					</label>
				</p>

				<p class="field-custom description description-wide">
					<?php esc_html_e( 'Stamp position :', 'iki-toolkit' ) ?></p>

				<p class="field-custom description description-wide">
					<?php esc_html_e( 'If left empty, stamp will appear on the right side of the button text.', 'iki-toolkit' ) ?></p>

				<p class="field-custom description description-wide">
					<?php esc_html_e( 'Please provide a value together with the unit (px,%)', 'iki-toolkit' ) ?></p>

				<p class="field-custom description description-wide">
					<label for="edit-menu-item-stamp-pos-top-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Top (optional) :', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-stamp-pos-top-<?php echo $item_id; ?>"
							   class=" code edit-menu-item-custom"
							   name="menu-item-iki_stamp_pos_top[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->iki_stamp_pos_top ); ?>"/>
					</label>
				</p>
				<p class="field-custom description description-wide">
					<label for="edit-menu-item-stamp-pos-bottom-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Bottom (optional):', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-stamp-pos-bottom-<?php echo $item_id; ?>"
							   class="code edit-menu-item-custom"
							   name="menu-item-iki_stamp_pos_bottom[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->iki_stamp_pos_bottom ); ?>"/>
					</label>
				</p>

				<p class="field-custom description description-wide">
					<label for="edit-menu-item-stamp-pos-left-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Left (optional) :', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-stamp-pos-left-<?php echo $item_id; ?>"
							   class="code edit-menu-item-custom"
							   name="menu-item-iki_stamp_pos_left[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->iki_stamp_pos_left ); ?>"/>
					</label>
				</p>

				<p class="field-custom description description-wide">
					<label for="edit-menu-item-stamp-rotation-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Rotation of the stamp (optional) 0-360 :', 'iki-toolkit' ); ?><br/>
						<input type="text" id="edit-menu-item-stamp-rotation-<?php echo $item_id; ?>"
							   class="code edit-menu-item-custom"
							   name="menu-item-iki_stamp_rotation[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->iki_stamp_rotation ); ?>"/>
					</label>
				</p>
				<p class="field-custom description description-wide">
					<label for="edit-menu-item-stamp-rotation-<?php echo $item_id; ?>">
						<?php esc_html_e( 'Minimum width: Please provide unit together with the value. Can be left empty.', 'iki-toolkit' ); ?>
						<br/>
						<input type="text" id="edit-menu-item-stamp-width-<?php echo $item_id; ?>"
							   class="code edit-menu-item-custom"
							   name="menu-item-iki_stamp_width[<?php echo $item_id; ?>]"
							   value="<?php echo esc_attr( $item->iki_stamp_width ); ?>"/>
					</label>
				</p>
			</div>
			<div class="iki-level-hide">
				<p class="field-custom description ">
					<label>
						<?php esc_html_e( 'Mega Horizontal Columns', 'iki-toolkit' ); ?> <br>
						<select name="menu-item-iki_horizontal_columns[<?php echo $item_id; ?>]">
							<option value="disabled"
								<?php if ( $item->iki_horizontal_columns == ''
								           || $item->iki_horizontal_columns == 'disabled' ) {
									echo 'selected="selected"';
								} ?>>
								<?php esc_html_e( 'Disabled', 'iki-toolkit' ); ?>
							</option>
							<option
								value="enabled"
								<?php if ( $item->iki_horizontal_columns == "enabled" ) {
									echo 'selected="selected"';
								} ?>>
								<?php esc_html_e( 'Enabled', 'iki-toolkit' ) ?>
							</option>
						</select>
					</label>
				</p>
				<p class="field-custom description description-wide">
					<?php esc_html_e( 'Please note that "Mega Menu Block" and "Mega Horizontal Columns" are mutually exclusive options.', 'iki-toolkit' ) ?></p>
			</div>
			<?php if ( $this->iki_content_block_cpt_present && get_theme_support( 'iki-toolkit-menu' ) ) { ?>
				<br>
				<div id="iki-cb-options-<?php echo $item_id; ?>" class="iki-cb-options iki-level-hide iki-tl-options">

					<p class="field-custom description description-wide description-thin-custom">
						<label for="edit-menu-content-block-<?php echo $item_id; ?>">
							<?php esc_html_e( 'Mega Menu Block', 'iki-toolkit' ); ?><br/>
							<select class="description-wide" id="edit-menu-content-block-<?php echo $item_id; ?>"
									name="menu-item-iki_content_block[<?php echo $item_id; ?>]">
								<?php
								$defaultSelect = $item->iki_content_block;
								?>
								<?php
								if ( ! empty( $this->block_posts ) ) { ?>
									<option
										value="false"><?php esc_html_e( 'Disabled', 'iki-toolkit' ); ?></option>
									<?php foreach ( $this->block_posts as $block ) { ?>
										<option value="<?php echo esc_attr( $block->ID ) ?>"
											<?php if ( esc_attr( $defaultSelect ) == $block->ID ) {
												echo 'selected="selected"';
											} ?>><?php echo $block->post_title ?>
										</option>
										<?php
									}

								} else { ?>
									<option
										value="false"><?php esc_html_e( 'No content blocks found', 'iki-toolkit' ); ?></option>
								<?php } ?>
							</select>
						</label>
					</p>

					<p class="field-custom description description-wide ">
						<label>
							<input type="radio" name="menu-item-iki_menu_cb_width[<?php echo $item_id; ?>]"
								<?php checked( $item->iki_menu_cb_width, 'full' ); ?>
								   value="full"/>
							<?php esc_html_e( 'Mega menu width equals menu width', 'iki-toolkit' ); ?>
						</label><br>
						<label>
							<input type="radio" name="menu-item-iki_menu_cb_width[<?php echo $item_id; ?>]"
								<?php checked( $item->iki_menu_cb_width, 'fixed' );
								checked( $item->iki_menu_cb_width, '' ); ?>
								   value="fixed"/>
							<?php esc_html_e( 'Mega menu width equals theme fixed width', 'iki-toolkit' ); ?>
						</label><br>
					</p>

					<p class="field-custom description ">
						<label>
							<?php esc_html_e( 'Mega menu position', 'iki-toolkit' ); ?> <br>
							<select name="menu-item-iki_menu_cb_aligment[<?php echo $item_id; ?>]">
								<option value="center"
									<?php if ( $item->iki_menu_cb_aligment == '' || $item->iki_menu_cb_aligment == 'center' ) {
										echo 'selected="selected"';
									} ?>>
									<?php esc_html_e( 'Center', 'iki-toolkit' ); ?>
								</option>
								<option
									value="left"
									<?php if ( $item->iki_menu_cb_aligment === "left" ) {
										echo 'selected="selected"';
									} ?>>
									<?php esc_html_e( 'Left', 'iki-toolkit' ) ?>
								</option>
								<option value="right"
									<?php if ( $item->iki_menu_cb_aligment === "right" ) {
										echo 'selected="selected"';
									} ?>>
									<?php esc_html_e( 'Right', 'iki-toolkit' ) ?>
								</option>
							</select>
						</label>
					</p>
				</div>
			<?php } ?>

			<!--CUSTOM INSERTION STOPS HERE-->

			<div class="menu-item-actions description-wide submitbox">
				<?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
					<p class="link-to-original">
						<?php printf( esc_html( __( 'Original: %s', 'iki-toolkit' ) ), '<a href="' . esc_url( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
					</p>
				<?php endif; ?>
				<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
				echo wp_nonce_url(
					add_query_arg(
						array(
							'action'    => 'delete-menu-item',
							'menu-item' => $item_id,
						),
						admin_url( 'nav-menus.php' )
					),
					'delete-menu_item_' . $item_id
				); ?>"><?php esc_html_e( 'Remove', 'iki-toolkit' ); ?></a> <span
					class="meta-sep hide-if-no-js"> | </span>
				<a
					class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>"
					href="<?php echo esc_url( add_query_arg( array(
						'edit-menu-item' => $item_id,
						'cancel'         => time()
					), admin_url( 'nav-menus.php' ) ) );
					?>#menu-item-settings-<?php echo $item_id; ?>"><?php esc_html_e( 'Cancel', 'iki-toolkit' ); ?></a>
			</div>

			<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]"
				   value="<?php echo $item_id; ?>"/>
			<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]"
				   value="<?php echo esc_attr( $item->object_id ); ?>"/>
			<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]"
				   value="<?php echo esc_attr( $item->object ); ?>"/>
			<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]"
				   value="<?php echo esc_attr( $item->menu_item_parent ); ?>"/>
			<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]"
				   value="<?php echo esc_attr( $item->menu_order ); ?>"/>
			<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]"
				   value="<?php echo esc_attr( $item->type ); ?>"/>

		</div>
		<!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	}

}