<?php

/**
 * Setup admin  options for wordpress authors
 */
class Iki_Admin_Author_Options {
	/**
	 * Iki_User_Profile constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization method.
	 */
	public function init() {

		add_filter( 'user_contactmethods', array( $this, 'add_contact_methods' ), 10, 1 );

		add_action( 'show_user_profile', array( $this, 'list_author_blocks' ) );
		add_action( 'edit_user_profile', array( $this, 'list_author_blocks' ) );

		add_action( 'personal_options_update', array( $this, 'save_user_custom_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_custom_fields' ) );
	}

	/** Print option dropdowns with available content blocks
	 *
	 * @param $user WP_User
	 */
	public function list_author_blocks( $user ) {
		if ( current_user_can( 'edit_user', $user->ID ) ) {
			?>
			<table class="form-table">
				<tbody>
				<tr class="">
					<th scope="row"><?php esc_html_e( 'Remove global block top', 'iki-toolkit' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php esc_html_e( 'Remove global block top', 'iki-toolkit' ); ?></span></legend>
							<label for="author_rm_global_content_block_top">
								<input name="author_rm_global_content_block_top" id="author_rm_global_content_block_top"
									   type="checkbox" value="yes"
									<?php checked( get_the_author_meta( 'author_rm_global_content_block_top', $user->ID ), 'yes' ) ?>>
								<?php esc_html_e( 'Yes', 'iki-toolkit' ); ?></label><br>
						</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
			<?php
			$this->print_author_block( $user, esc_html( __( 'top', 'iki-toolkit' ) ) );
			?>
			<table class="form-table">
				<tbody>
				<tr class="">
					<th scope="row"><?php esc_html_e( 'Remove global block bottom', 'iki-toolkit' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text">
								<span><?php esc_html_e( 'Remove global block bottom', 'iki-toolkit' ); ?></span>
							</legend>
							<label for="author_rm_global_content_block_bottom">
								<input name="author_rm_global_content_block_bottom"
									   id="author_rm_global_content_block_bottom" type="checkbox" value="yes"
									<?php checked( get_the_author_meta( 'author_rm_global_content_block_bottom', $user->ID ), 'yes' ) ?>>
								<?php esc_html_e( 'Yes', 'iki-toolkit' ); ?></label><br>
						</fieldset>
					</td>
				</tr>
				</tbody>
			</table>
			<?php
			$this->print_author_block( $user, esc_html( __( 'bottom', 'iki-toolkit' ) ) );
		}
	}

	/**
	 * Print user content block dropdown
	 *
	 * @param $user WP_User
	 * @param $position string option for two available locations
	 */
	protected function print_author_block( $user, $position ) {
		{
			//if post type exists
			$position = esc_html( $position );
			if ( post_type_exists( 'iki_content_block' ) && current_user_can( 'edit_user', $user->ID ) ) {

				if ( is_admin() ) {
					?>
					<table class="form-table">
						<tbody>
						<tr>
							<th><?php esc_html_e( 'Author block', 'iki-toolkit' );
								echo ' ' . $position ?><span class="description"></span>
							</th>
							<td>
								<select class="description-wide" id="edit-menu-content-block"
										name="iki_content_block_<?php echo $position ?>">
									<?php
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
												'terms'    => array(
													'author',
													'global'
												)
											)
										)
									);

									$default_select = get_the_author_meta( "iki_content_block_{$position}", $user->ID );
									$blockPosts     = get_posts( $args );
									?>
									<?php
									if ( ! empty( $blockPosts ) ) { ?>

										<option
											value=""><?php esc_html_e( 'Disabled', 'iki-toolkit' ); ?></option>
										<?php foreach ( $blockPosts as $block ) { ?>
											<option value="<?php echo esc_attr( $block->ID ) ?>"
												<?php if ( $default_select == $block->ID ) {
													echo 'selected="selected"';
												} ?>>
												<?php echo esc_html( $block->post_title ); ?>
											</option>
											<?php
										}

									} else { ?>
										<option
											value=""><?php esc_html_e( 'No content blocks found', 'iki-toolkit' ); ?></option>
									<?php } ?>
								</select>
							</td>
						</tr>
						</tbody>
					</table>
					<?php
				}

			}

		}

	}

	/**
	 * Save user data
	 *
	 * @param $user_id int User id
	 *
	 * @return bool
	 */
	public function save_user_custom_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$rm_global_cb_top = isset( $_POST['author_rm_global_content_block_top'] ) ? 'yes' : '';
		update_user_meta( $user_id, 'author_rm_global_content_block_top', esc_attr( $rm_global_cb_top ) );


		$rm_global_cb_bottom = isset( $_POST['author_rm_global_content_block_bottom'] ) ? 'yes' : '';
		update_user_meta( $user_id, 'author_rm_global_content_block_bottom', esc_attr( $rm_global_cb_bottom ) );

		if ( isset( $_POST['iki_content_block_top'] ) ) {
			update_user_meta( $user_id, 'iki_content_block_top', esc_attr( $_POST['iki_content_block_top'] ) );
		}


		if ( isset( $_POST['iki_content_block_bottom'] ) ) {
			update_user_meta( $user_id, 'iki_content_block_bottom', esc_attr( $_POST['iki_content_block_bottom'] ) );
		}

	}

	/**
	 * Contact methods to be displayed on the user profile page in backend
	 *
	 * @param $contact_methods array contact methods
	 *
	 * @return  array
	 */
	function add_contact_methods( $contact_methods ) {

		unset( $contact_methods['yim'] );
		unset( $contact_methods['aim'] );
		unset( $contact_methods['jabber'] );

		foreach ( $GLOBALS['iki_toolkit_admin']['available_social_profiles'] as $key => $value ) {

			$contact_methods[ 'iki_contact_' . $key ] = $value;
		}

		return $contact_methods;
	}
}

new Iki_Admin_Author_Options();
