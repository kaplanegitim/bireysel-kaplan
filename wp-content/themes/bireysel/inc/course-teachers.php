<?php
/**
 * Kurs ⇄ Eğitmen (çoklu) ilişkisi.
 *
 * Bir kurs birden fazla eğitmene atanabilir. Eğitmen ID'leri `_bireysel_course_teachers`
 * çoklu postmeta satırlarında tutulur. Eski tekil `nd_learning_meta_box_teacher` "birincil"
 * eğitmen olarak korunur (geriye dönük uyum + listede ilk seçili).
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Bir kursun tüm (geçerli, yayında) eğitmen ID'leri. Çoklu meta yoksa eski tekil meta'ya düşer.
 */
function bireysel_course_teacher_ids( $course_id ) {
	$ids = array_map( 'intval', (array) get_post_meta( $course_id, '_bireysel_course_teachers', false ) );
	$ids = array_values( array_unique( array_filter( $ids ) ) );
	if ( ! $ids ) {
		$single = (int) get_post_meta( $course_id, 'nd_learning_meta_box_teacher', true );
		if ( $single ) { $ids = array( $single ); }
	}
	return array_values( array_filter( $ids, function ( $id ) {
		$p = get_post( $id );
		return $p && 'teachers' === $p->post_type && 'publish' === $p->post_status;
	} ) );
}

/**
 * Meta box: kursa çoklu eğitmen ata (kursun diliyle aynı dildeki eğitmenler listelenir).
 */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'bireysel_course_teachers', __( 'Eğitmenler', 'bireysel' ), function ( $post ) {
		wp_nonce_field( 'bireysel_ct_save', 'bireysel_ct_nonce' );
		$sel  = bireysel_course_teacher_ids( $post->ID );
		$args = array( 'post_type' => 'teachers', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' );
		if ( function_exists( 'pll_get_post_language' ) ) {
			$lang = pll_get_post_language( $post->ID );
			if ( $lang ) { $args['lang'] = $lang; }
		}
		$teachers = get_posts( $args );
		if ( ! $teachers ) { echo '<p>' . esc_html__( 'Önce eğitmen ekleyin.', 'bireysel' ) . '</p>'; return; }
		echo '<p style="color:#646970;font-size:12px;margin:0 0 8px">' . esc_html__( 'Bu kursu veren eğitmen(ler)i seçin. İlk seçili birincil eğitmen olur.', 'bireysel' ) . '</p>';
		foreach ( $teachers as $t ) {
			printf(
				'<label style="display:block;margin:5px 0"><input type="checkbox" name="bireysel_course_teachers[]" value="%d"%s> %s</label>',
				(int) $t->ID,
				in_array( (int) $t->ID, $sel, true ) ? ' checked' : '',
				esc_html( $t->post_title )
			);
		}
	}, 'courses', 'side', 'default' );
} );

/**
 * Kaydet.
 */
add_action( 'save_post_courses', function ( $post_id ) {
	if ( ! isset( $_POST['bireysel_ct_nonce'] ) || ! wp_verify_nonce( $_POST['bireysel_ct_nonce'], 'bireysel_ct_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	delete_post_meta( $post_id, '_bireysel_course_teachers' );
	$ids = isset( $_POST['bireysel_course_teachers'] ) ? array_map( 'intval', (array) $_POST['bireysel_course_teachers'] ) : array();
	$ids = array_values( array_unique( array_filter( $ids ) ) );
	foreach ( $ids as $tid ) { add_post_meta( $post_id, '_bireysel_course_teachers', $tid ); }
	// Geriye dönük: birincil eğitmen = ilk seçili.
	if ( $ids ) { update_post_meta( $post_id, 'nd_learning_meta_box_teacher', $ids[0] ); }
} );

/* -------------------------------------------------------------------------
 * Kurs Detayları (fiyat + kontenjan) — nd-learning kaldırıldığı için bu alanları
 * düzenlemek üzere tema kendi kutusunu sağlar. Şablonlar nd_learning_meta_box_* okur.
 * ---------------------------------------------------------------------- */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'bireysel_course_details', __( 'Kurs Detayları', 'bireysel' ), function ( $post ) {
		wp_nonce_field( 'bireysel_cd_save', 'bireysel_cd_nonce' );
		$price = get_post_meta( $post->ID, 'nd_learning_meta_box_price', true );
		$avail = get_post_meta( $post->ID, 'nd_learning_meta_box_max_availability', true );
		echo '<style>.bir-cd label{display:block;font-weight:600;margin:10px 0 3px}.bir-cd input{width:100%}.bir-cd .c{color:#646970;font-size:12px;margin:2px 0 0}</style>';
		echo '<div class="bir-cd">';
		echo '<label>' . esc_html__( 'Fiyat', 'bireysel' ) . '</label>';
		echo '<input type="text" name="bireysel_course_price" value="' . esc_attr( $price ) . '" placeholder="585 TL">';
		echo '<p class="c">' . esc_html__( 'Olduğu gibi gösterilir (para birimini de yaz, örn. “585 TL”).', 'bireysel' ) . '</p>';
		echo '<label>' . esc_html__( 'Kontenjan', 'bireysel' ) . '</label>';
		echo '<input type="text" name="bireysel_course_avail" value="' . esc_attr( $avail ) . '" placeholder="12">';
		echo '</div>';
	}, 'courses', 'side', 'high' );
} );

add_action( 'save_post_courses', function ( $post_id ) {
	if ( ! isset( $_POST['bireysel_cd_nonce'] ) || ! wp_verify_nonce( $_POST['bireysel_cd_nonce'], 'bireysel_cd_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	if ( isset( $_POST['bireysel_course_price'] ) ) {
		update_post_meta( $post_id, 'nd_learning_meta_box_price', sanitize_text_field( wp_unslash( $_POST['bireysel_course_price'] ) ) );
	}
	if ( isset( $_POST['bireysel_course_avail'] ) ) {
		update_post_meta( $post_id, 'nd_learning_meta_box_max_availability', sanitize_text_field( wp_unslash( $_POST['bireysel_course_avail'] ) ) );
	}
} );
