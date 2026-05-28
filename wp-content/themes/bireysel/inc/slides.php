<?php
/**
 * Hero Slider — admin'den yönetilen bir_slide CPT.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * CPT.
 */
function bireysel_register_slide_cpt() {
	register_post_type( 'bir_slide', array(
		'labels' => array(
			'name'          => __( 'Slaytlar', 'bireysel' ),
			'singular_name' => __( 'Slayt', 'bireysel' ),
			'add_new_item'  => __( 'Yeni Slayt', 'bireysel' ),
			'edit_item'     => __( 'Slaytı Düzenle', 'bireysel' ),
			'menu_name'     => __( 'Hero Slaytları', 'bireysel' ),
		),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-images-alt2',
		'supports'     => array( 'title', 'thumbnail', 'page-attributes' ),
		'has_archive'  => false,
	) );
}
add_action( 'init', 'bireysel_register_slide_cpt' );

/**
 * Slayt alanları.
 */
function bireysel_slide_fields() {
	return array(
		'_bir_eyebrow'   => array( __( 'Üst etiket (eyebrow)', 'bireysel' ), 'text' ),
		'_bir_lead'      => array( __( 'Açıklama', 'bireysel' ), 'textarea' ),
		'_bir_variant'   => array( __( 'Stil (dark / light)', 'bireysel' ), 'select', array( 'dark', 'light' ) ),
		'_bir_pricetag'  => array( __( 'Fiyat etiketi (ops.)', 'bireysel' ), 'text' ),
		'_bir_btn1_text' => array( __( '1. Buton metni', 'bireysel' ), 'text' ),
		'_bir_btn1_url'  => array( __( '1. Buton bağlantısı', 'bireysel' ), 'text' ),
		'_bir_btn2_text' => array( __( '2. Buton metni', 'bireysel' ), 'text' ),
		'_bir_btn2_url'  => array( __( '2. Buton bağlantısı', 'bireysel' ), 'text' ),
		'_bir_stats'     => array( __( 'İstatistikler (her satır: sayı|etiket)', 'bireysel' ), 'textarea' ),
	);
}

/**
 * Meta box.
 */
function bireysel_slide_metabox() {
	add_meta_box( 'bir_slide_fields', __( 'Slayt İçeriği', 'bireysel' ), function ( $post ) {
		wp_nonce_field( 'bir_slide_save', 'bir_slide_nonce' );
		echo '<style>.bir-f{margin:0 0 12px}.bir-f label{display:block;font-weight:600;margin-bottom:3px}.bir-f input,.bir-f textarea,.bir-f select{width:100%}</style>';
		foreach ( bireysel_slide_fields() as $key => $f ) {
			$val = get_post_meta( $post->ID, $key, true );
			echo '<p class="bir-f"><label>' . esc_html( $f[0] ) . '</label>';
			if ( 'textarea' === $f[1] ) {
				echo '<textarea name="' . esc_attr( $key ) . '" rows="3">' . esc_textarea( $val ) . '</textarea>';
			} elseif ( 'select' === $f[1] ) {
				echo '<select name="' . esc_attr( $key ) . '">';
				foreach ( $f[2] as $opt ) {
					echo '<option value="' . esc_attr( $opt ) . '"' . selected( $val, $opt, false ) . '>' . esc_html( $opt ) . '</option>';
				}
				echo '</select>';
			} else {
				echo '<input type="text" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
			}
			echo '</p>';
		}
		echo '<p style="color:#666">' . esc_html__( 'Görsel için sağdaki “Öne çıkan görsel”i kullanın.', 'bireysel' ) . '</p>';
	}, 'bir_slide', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'bireysel_slide_metabox' );

/**
 * Kaydet.
 */
function bireysel_slide_save( $post_id ) {
	if ( ! isset( $_POST['bir_slide_nonce'] ) || ! wp_verify_nonce( $_POST['bir_slide_nonce'], 'bir_slide_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	foreach ( bireysel_slide_fields() as $key => $f ) {
		if ( ! isset( $_POST[ $key ] ) ) { continue; }
		$raw = wp_unslash( $_POST[ $key ] );
		$val = ( 'textarea' === $f[1] ) ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
		update_post_meta( $post_id, $key, $val );
	}
}
add_action( 'save_post_bir_slide', 'bireysel_slide_save' );
