<?php
/**
 * Minimal contact form handler using only WordPress core (wp_mail), no
 * plugin dependency. Nonce-verified, sanitized, escaped on the way out.
 */

defined( 'ABSPATH' ) || exit;

function elvina_handle_contact_form() {
	$nonce_ok = isset( $_POST['elvina_contact_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['elvina_contact_nonce'] ) ), 'elvina_contact' );

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( ! $nonce_ok || ! $name || ! is_email( $email ) || ! $message ) {
		wp_safe_redirect( add_query_arg( 'contact', 'error', wp_get_referer() ) );
		exit;
	}

	$to      = get_option( 'admin_email' );
	$subject = sprintf(
		/* translators: %s: site name. */
		__( '[%s] Nouveau message de contact', 'elvina' ),
		get_bloginfo( 'name' )
	);
	$body    = "Nom : {$name}\nE-mail : {$email}\n\nMessage :\n{$message}";
	$headers = array( 'Reply-To: ' . $email );

	wp_mail( $to, $subject, $body, $headers );

	wp_safe_redirect( add_query_arg( 'contact', 'sent', wp_get_referer() ) );
	exit;
}
add_action( 'admin_post_elvina_contact', 'elvina_handle_contact_form' );
add_action( 'admin_post_nopriv_elvina_contact', 'elvina_handle_contact_form' );
