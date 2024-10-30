<?php

if ( ! function_exists( '_action_iki_toolkit_ajax_contact_form' ) ) {
	/**
	 * Handle ajax contact form validation
	 */
	function _action_iki_toolkit_ajax_contact_form() {
		{

			if ( ! check_ajax_referer( 'iki_nonce', false, false ) ) {
				die( 'Security check' );
			}

			if ( isset ( $_POST['formData'] ) ) {
				$response = array();
				$errors   = array();
				$formData = array();
				parse_str( $_POST['formData'], $formData );
				$email = '';
				if ( ! empty( $formData['email'] ) ) {
					$email = filter_var( $formData['email'], FILTER_VALIDATE_EMAIL );
					if ( ! $email ) {
						$errors['email'] = __( 'Email invalid', 'iki-toolkit' );
					}
				}

				$postId = '';
				if ( $formData['post_id'] ) {
					$postId            = $formData['post_id'];
					$useCustomQuestion = Iki_Toolkit_Utils::string_to_boolean( iki_toolkit()->get_post_option( $postId, 'use_custom_qestion', 'disabled' ) );
					$customQuestion    = iki_toolkit()->get_post_option( $postId, 'custom_question', '' );
					$customAnswer      = strtolower( trim( iki_toolkit()->get_post_option( $postId, 'custom_answer', '' ) ) );

					// customAnswer enabled
					if ( $useCustomQuestion && $customQuestion && $customAnswer ) {
						//get the answer from meta
						$answer = '';
						if ( isset( $formData['iki-contact-answer'] ) ) {
							$answer = strtolower( trim( $formData['iki-contact-answer'] ) );// user form answer
						}

						if ( $customAnswer != $answer ) {
							$errors['answer'] = __( 'Wrong answer', 'iki-toolkit' );
						}
					}
				}


				if ( ! empty( $errors ) ) {
					$response['errors']       = $errors;
					$response['notification'] = __( 'There were some errors while trying to submit the form', 'iki-toolkit' );
				} else {
					//send email
					$to = iki_toolkit()->get_post_option( $postId, 'email', '' );

					$prependSubject = iki_toolkit()->get_post_option( $postId, 'message_subject', '' );

					$subject = $prependSubject . ' ' . $formData['subject'];

					$phone = __( 'Phone number not provided', 'iki-toolkit' );

					if ( ! empty( $formData['phone'] ) ) {
						$phone = $formData['phone'];
					}

					$name        = $formData['name'];
					$userMessage = $formData['message'];

					$message = sprintf( '<strong>%1$s:</strong> %2$s<br/>', esc_html( __( 'Name', 'iki-toolkit' ) ),
						esc_html( $name ) );
					$message .= sprintf( '<strong>%1$s:</strong> %2$s<br/>', esc_html( __( 'Email', 'iki-toolkit' ) ),
						esc_html( $email ) );
					$message .= sprintf( '<strong>%1$s:</strong> %2$s<br/>',
						esc_html( __( 'Phone', 'iki-toolkit' ) ), esc_html( $phone ) );
					$message .= sprintf( '<strong>%1$s:</strong> %2$s<br/>',
						esc_html( __( 'Message', 'iki-toolkit' ) ),
						esc_html( $userMessage ) );

					$headers   = array();
					$headers[] = sprintf( 'From: %1$s < %2$s >', $name, $email );
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					$mailsent  = wp_mail( $to, $subject, $message, $headers );

					if ( $mailsent ) {
						$response['success'] = true;
					} else {
						$response['success']      = false;
						$response['notification'] = __( 'Error sending mail, please try again later', 'iki-toolkit' );

					}
				}

				wp_send_json( $response );

			} else {

				die( 0 );

			}
		}

	}
}

add_action( 'wp_ajax_iki_toolkit_validate_contact_form', '_action_iki_toolkit_ajax_contact_form' );
add_action( 'wp_ajax_nopriv_iki_toolkit_validate_contact_form', '_action_iki_toolkit_ajax_contact_form' );