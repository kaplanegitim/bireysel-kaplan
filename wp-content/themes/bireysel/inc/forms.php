<?php
/**
 * Form sistemi — Contact Form 7 yerine.
 * bir_submission CPT (gönderi kaydı) + AJAX handler + e-posta bildirimi.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Gönderi kayıt CPT'si (yalnızca yönetim).
 */
function bireysel_register_submission_cpt() {
	register_post_type( 'bir_submission', array(
		'labels' => array(
			'name'          => __( 'Form Gönderileri', 'bireysel' ),
			'singular_name' => __( 'Gönderi', 'bireysel' ),
			'menu_name'     => __( 'Form Gönderileri', 'bireysel' ),
		),
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-email-alt',
		'capability_type'     => 'post',
		'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'        => true,
		'supports'            => array( 'title' ),
		'exclude_from_search' => true,
	) );
}
add_action( 'init', 'bireysel_register_submission_cpt' );

/**
 * AJAX: form gönderimi (giriş yapan + yapmayan).
 */
function bireysel_handle_form() {
	check_ajax_referer( 'bireysel_form', 'nonce' );

	// Bal küpü (bot tuzağı) — doluysa sessizce "başarılı" dön.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Teşekkürler!', 'bireysel' ) ) );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$ftype   = isset( $_POST['form_type'] ) ? sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) : 'contact';
	$source  = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';

	if ( empty( $email ) || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Geçerli bir e-posta girin.', 'bireysel' ) ) );
	}
	if ( 'newsletter' !== $ftype && empty( $name ) ) {
		wp_send_json_error( array( 'message' => __( 'Lütfen adınızı girin.', 'bireysel' ) ) );
	}

	$labels = array( 'contact' => 'İletişim', 'register' => 'Kurs Kaydı', 'newsletter' => 'Bülten' );
	$flabel = isset( $labels[ $ftype ] ) ? $labels[ $ftype ] : $ftype;
	$title  = sprintf( '[%s] %s', $flabel, $name ? $name : $email );

	$post_id = wp_insert_post( array(
		'post_type'   => 'bir_submission',
		'post_status' => 'publish',
		'post_title'  => $title,
	) );

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		wp_send_json_error( array( 'message' => __( 'Bir hata oluştu, tekrar deneyin.', 'bireysel' ) ) );
	}

	update_post_meta( $post_id, '_bir_name', $name );
	update_post_meta( $post_id, '_bir_email', $email );
	update_post_meta( $post_id, '_bir_phone', $phone );
	update_post_meta( $post_id, '_bir_subject', $subject );
	update_post_meta( $post_id, '_bir_message', $message );
	update_post_meta( $post_id, '_bir_form', $ftype );
	update_post_meta( $post_id, '_bir_source', $source );

	// E-posta bildirimi (wp-mail-smtp üzerinden)
	$to   = get_theme_mod( 'bireysel_form_email', get_option( 'admin_email' ) );
	$subj = sprintf( '[%s] %s — %s', get_bloginfo( 'name' ), $flabel, $name ? $name : $email );
	$body = sprintf(
		"Form: %s\nAd: %s\nE-posta: %s\nTelefon: %s\nKonu: %s\nKaynak: %s\n\nMesaj:\n%s\n",
		$flabel, $name, $email, $phone, $subject, $source, $message
	);
	$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email );
	wp_mail( $to, $subj, $body, $headers );

	$ok_msg = 'newsletter' === $ftype
		? __( 'Bültene kaydoldunuz, teşekkürler!', 'bireysel' )
		: __( 'Mesajınız alındı. En kısa sürede dönüş yapacağız.', 'bireysel' );
	wp_send_json_success( array( 'message' => $ok_msg ) );
}
add_action( 'wp_ajax_bireysel_form', 'bireysel_handle_form' );
add_action( 'wp_ajax_nopriv_bireysel_form', 'bireysel_handle_form' );

/**
 * Yönetim liste sütunları.
 */
function bireysel_submission_columns( $cols ) {
	return array(
		'cb'         => $cols['cb'],
		'title'      => __( 'Gönderi', 'bireysel' ),
		'bir_email'  => __( 'E-posta', 'bireysel' ),
		'bir_phone'  => __( 'Telefon', 'bireysel' ),
		'bir_form'   => __( 'Tür', 'bireysel' ),
		'date'       => __( 'Tarih', 'bireysel' ),
	);
}
add_filter( 'manage_bir_submission_posts_columns', 'bireysel_submission_columns' );

function bireysel_submission_column( $col, $post_id ) {
	if ( 'bir_email' === $col ) { echo esc_html( get_post_meta( $post_id, '_bir_email', true ) ); }
	if ( 'bir_phone' === $col ) { echo esc_html( get_post_meta( $post_id, '_bir_phone', true ) ); }
	if ( 'bir_form' === $col )  { echo esc_html( get_post_meta( $post_id, '_bir_form', true ) ); }
}
add_action( 'manage_bir_submission_posts_custom_column', 'bireysel_submission_column', 10, 2 );

/**
 * Gönderi detayını düzenleme ekranında göster (meta box).
 */
function bireysel_submission_metabox() {
	add_meta_box( 'bir_submission_detail', __( 'Gönderi Detayı', 'bireysel' ), function ( $post ) {
		$f = function ( $k ) use ( $post ) { return esc_html( get_post_meta( $post->ID, $k, true ) ); };
		echo '<p><strong>Ad:</strong> ' . $f( '_bir_name' ) . '</p>';
		echo '<p><strong>E-posta:</strong> ' . $f( '_bir_email' ) . '</p>';
		echo '<p><strong>Telefon:</strong> ' . $f( '_bir_phone' ) . '</p>';
		echo '<p><strong>Konu:</strong> ' . $f( '_bir_subject' ) . '</p>';
		echo '<p><strong>Tür:</strong> ' . $f( '_bir_form' ) . ' &middot; <strong>Kaynak:</strong> ' . $f( '_bir_source' ) . '</p>';
		echo '<p><strong>Mesaj:</strong><br>' . nl2br( $f( '_bir_message' ) ) . '</p>';
	}, 'bir_submission', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'bireysel_submission_metabox' );
