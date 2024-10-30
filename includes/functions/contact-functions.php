<?php
if ( ! function_exists( 'iki_toolkit_print_contact_form' ) ) {

	/**
	 * Print custom contact form (for contact.php template)
	 *
	 * @param int $id post id
	 */
	function iki_toolkit_print_contact_form( $id ) {

		$classes = array(
			'clearfix',
			'iki-contact-form-section'
		);
		if ( 'disabled' == iki_toolkit()->get_post_option( $id, 'show_phone_field', 'disabled' ) ) {
			$classes[] = 'iki-contact-rpf';
		};
		$iki_question = iki_toolkit()->get_post_option( $id, 'custom_question', '' );

		$use_custom_question = iki_toolkit()->get_post_option( $id, 'use_custom_qestion', 'disabled' );
		$iki_answer          = iki_toolkit()->get_post_option( $id, 'custom_answer', false );
		$wrong_answer        = __( 'Wrong answer', 'iki-toolkit' );
		$empty_notification  = __( 'Must provide value', 'iki-toolkit' );

		$iki_name_placeholder    = __( 'Your Name', 'iki-toolkit' );
		$iki_email_placeholder   = __( 'Your Email', 'iki-toolkit' );
		$iki_answer_placeholder  = __( 'Your Answer', 'iki-toolkit' );
		$iki_phone_placeholder   = __( 'Phone', 'iki-toolkit' );
		$iki_message_placeholder = __( 'Message', 'iki-toolkit' );
		$iki_subject_placeholder = __( 'Subject', 'iki-toolkit' );
		$iki_contact_form_title  = __( 'Send us a message', 'iki-toolkit' );
		$iki_contact_form_title  = apply_filters( 'iki_contact_page_contact_form_title', $iki_contact_form_title );
		?>

		<?php do_action( 'iki_toolkit_contact_form_before' ); ?>
		<h3 class="contact-form-title"><?php echo esc_html( $iki_contact_form_title ) ?></h3>
		<div class="<?php echo Iki_Toolkit_Utils::sanitize_html_class_array( $classes ) ?>">
			<form id='iki-contact-form' data-iki-wrong-a="<?php echo esc_html( $wrong_answer ); ?>"
				  data-iki-empty="<?php echo esc_html( $empty_notification ); ?>" class="iki-form clearfix"
				  role="form" method="post" action="">
				<input type="hidden" name="post_id" value="<?php echo esc_html( $id ); ?>">
				<div class="iki-form-section section-name">
					<label class="iki-form-label" for="name"><?php echo esc_html( $iki_name_placeholder ); ?><span
							title="<?php esc_html_e( 'Required Field', 'iki-toolkit' ); ?>"
							class="required tooltip-js">*</span></label>
					<input required type="text" class="iki-input iki-form-element"
						   id="name" name="name">
				</div>
				<div class="iki-form-section section-email">
					<label class="iki-form-label" for="email"><?php echo esc_html( $iki_email_placeholder ); ?> <span
							title="<?php esc_html_e( 'Required Field', 'iki-toolkit' ); ?>"
							class="required tooltip-js">*</span></label>
					<input required type="email" class="iki-input iki-form-element"
						   id="email" name="email">
				</div>
				<?php if ( 'enabled' == iki_toolkit()->get_post_option( $id, 'show_phone_field', 'disabled' ) ) { ?>
					<div class="iki-form-section section-phone">
						<label class="iki-form-label" for="phone">
							<?php echo esc_html( $iki_phone_placeholder ); ?></label>
						<input type="tel" class="iki-input iki-form-element"
							   id="phone" name="phone">
					</div>
				<?php } ?>
				<div class="iki-form-section section-subject">
					<label class="iki-form-label" for="subject"><?php echo esc_html( $iki_subject_placeholder ); ?>
						<span
							title="<?php esc_html_e( 'Required Field', 'iki-toolkit' ); ?>"
							class="required tooltip-js">*</span>
					</label>
					<input required type="text"
						   class="iki-input iki-form-element"
						   data-placement="<?php echo esc_html( $iki_subject_placeholder ); ?>"
						   id="subject" name="subject">
				</div>
				<div class="iki-form-section section-message"
					 data-iki-error=<?php esc_html_e( 'Invalid Email Address', 'iki-toolkit' ); ?>>
					<label class="iki-form-label" for="message"><?php echo esc_html( $iki_message_placeholder ); ?>
						<span
							title="<?php esc_html_e( 'Required Field', 'iki-toolkit' ); ?>"
							class="required tooltip-js">*</span></label>
					<textarea required class="iki-input iki-form-element" rows="4"
							  id="message" name="message"></textarea>
				</div>
				<?php if ( 'enabled' == $use_custom_question && ! empty( $iki_question ) && ! empty( $iki_answer ) ) { ?>
					<div data-iki-answer="<?php echo esc_attr( $iki_answer ) ?>"
						 class="iki-form-section section-iki-contact-answer">
						<p class="iki-form-question"><?php esc_html_e( 'Question: ', 'iki-toolkit' );
							echo esc_html( $iki_question ); ?></p>

						<label class="iki-form-label" for="answer"><?php echo esc_html( $iki_answer_placeholder ); ?>
							<span
								title="<?php esc_html_e( 'Required Field', 'iki-toolkit' ); ?>"
								class="required tooltip-js">*</span></label>
						<input required type="text" class="iki-input iki-form-element"
							   id="iki-contact-answer" name="iki-contact-answer">
					</div>
				<?php } ?>
				<?php if ( apply_filters( 'iki_toolkit_show_contact_form_gdpr_check_box', true ) ) { ?>
					<div class="">
						<input id="iki-gdpr-checkbox" name="iki-gdpr-checkbox" type="checkbox"
							   class="iki-input">
						<label for="iki-gdpr-checkbox"
							   class="iki-gdpr-label iki-form-label"><span><?php esc_html_e( 'I understand this email form collects my personal data in order to be contacted.', 'iki-toolkit' ); ?></span></label>
					</div>
				<?php } ?>
				<?php printf( '<button title="%2$s" type="submit" data-iki-default="%1$s" data-iki-progress="%4$s" data-iki-success="%3$s" class="tooltip-js iki-btn iki-form-btn"><span class="iki-notif-text">%1$s</span><span
				class="iki-submit-state iki-icon-mail"></span><span class="iki-working-state iki-icon-cog"></span></button>',
					esc_html( __( 'Send', 'iki-toolkit' ) ),
					esc_html( __( 'Send us your message', 'iki-toolkit' ) ),
					esc_html( __( 'Mail sent, thank You!', 'iki-toolkit' ) ),
					esc_html( __( 'Working', 'iki-toolkit' ) ) ); ?>
			</form>
			<div class="iki-notification-panel"></div>
		</div><!-- .iki-contact-form-section-->
		<?php
		do_action( 'iki_toolkit_contact_form_after' );
	}
}

if ( ! function_exists( 'iki_toolkit_print_custom_contact_fields' ) ) {
	/**
	 * Print contact fields
	 *
	 * @param int $id post id
	 */
	function iki_toolkit_print_custom_contact_fields( $id ) {
		do_action( 'iki_toolkit_custom_contact_fields_before' );
		$business_hours = iki_toolkit()->get_post_option( $id, 'business_hours' );
		$business_hours = trim( $business_hours );
		if ( ! empty( $business_hours ) ) {
			iki_toolkit_icon_info_html(
				'briefcase',
				apply_filters( 'iki_toolkit_contact_page_business_hours_info_title_text', __( 'Business hours', 'iki-toolkit' ) ),
				apply_filters( 'the_content', $business_hours ) );
		}

		$telephone = iki_toolkit()->get_post_option( $id, 'telephone' );
		$telephone = trim( $telephone );
		if ( ! empty( $telephone ) ) {
			iki_toolkit_icon_info_html(
				'phone',
				apply_filters( 'iki_toolkit_contact_page_telephone_info_title_text', __( 'Telephone', 'iki-toolkit' ) ),
				apply_filters( 'the_content', $telephone ) );
		}
		$location = iki_toolkit()->get_post_option( $id, 'location' );
		$location = trim( $location );
		if ( ! empty( $location ) ) {
			iki_toolkit_icon_info_html(
				'location',
				apply_filters( 'iki_toolkit_contact_page_location_info_title_text', __( 'Location', 'iki-toolkit' ) ),
				apply_filters( 'the_content', $location ) );
		}

		$misc = iki_toolkit()->get_post_option( $id, 'misc' );
		$misc = trim( $misc );
		if ( ! empty( $misc ) ) {
			echo '<div class="iki-vc-sec-wrap">';
			echo apply_filters( 'the_content', $misc );
			echo '</div>';
		}

		do_action( 'iki_toolkit_custom_contact_fields_after' );
	}
}


if ( ! function_exists( 'iki_toolkit_icon_info_html' ) ) {
	/** Print some content with icons
	 *
	 * @param $icon
	 * @param $text
	 * @param $content
	 * @param bool $echo
	 *
	 * @return string
	 */
	function iki_toolkit_icon_info_html( $icon, $text, $content, $echo = true ) {

		$r = sprintf( '<div class="iki-vc-sec-wrap"><div class="iki-vc-icon-wrap iki-vc-native">
                <span class="iki-vc-icon iki-icon-%1$s"></span>
                <h4 class="iki-vc-icon-txt">%2$s</h4>
                <span class="iki-vc-icon-sep"></span>
            </div>
        <div class="iki-vc-content-wrap">%3$s</div>
        </div>',
			sanitize_html_class( $icon ),
			sanitize_text_field( $text ),
			wp_kses_post( $content ) );

		if ( $echo ) {
			echo $r;
		} else {
			return $r;
		}

	}
}

