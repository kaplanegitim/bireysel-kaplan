<?php
/**
 * Özel içerik türleri — nd-learning yerine (aynı isim/slug, içerik korunur).
 * courses · teachers · nd_learning_docs + taksonomiler.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function bireysel_register_cpt() {

	/* ---------- Kurslar ---------- */
	register_post_type( 'courses', array(
		'labels' => array(
			'name'          => __( 'Kurslar', 'bireysel' ),
			'singular_name' => __( 'Kurs', 'bireysel' ),
			'add_new_item'  => __( 'Yeni Kurs', 'bireysel' ),
			'edit_item'     => __( 'Kursu Düzenle', 'bireysel' ),
			'menu_name'     => __( 'Kurslar', 'bireysel' ),
		),
		'public'       => true,
		'has_archive'  => false, // liste /kurslar/ Sayfası'ndan (page-kurslar.php) gelir; CPT arşivi gereksiz
		'rewrite'      => array( 'slug' => 'kurslar', 'with_front' => false ), // tekil kurs: /kurslar/{kurs}/ — /kurslar/ sayfa, alt segment CPT single
		'menu_icon'    => 'dashicons-welcome-learn-more',
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
	) );

	$course_tax = array(
		'category-course'         => array( 'Kurs Kategorileri', 'Kurs Kategorisi', 'categories-courses' ),
		'duration-course'         => array( 'Süreler', 'Süre', 'durations-courses' ),
		'difficulty-level-course' => array( 'Zorluk Seviyeleri', 'Zorluk', 'difficulty-levels-courses' ),
		'typology-course'         => array( 'Tipolojiler', 'Tipoloji', 'typologies-courses' ),
		'location-course'         => array( 'Lokasyonlar', 'Lokasyon', 'locations-courses' ),
	);
	foreach ( $course_tax as $tax => $d ) {
		register_taxonomy( $tax, 'courses', array(
			'labels'       => array( 'name' => $d[0], 'singular_name' => $d[1] ),
			'hierarchical' => true,
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => $d[2] ),
		) );
	}

	/* ---------- Eğitmenler ---------- */
	register_post_type( 'teachers', array(
		'labels' => array(
			'name'          => __( 'Eğitmenler', 'bireysel' ),
			'singular_name' => __( 'Eğitmen', 'bireysel' ),
			'menu_name'     => __( 'Eğitmenler', 'bireysel' ),
		),
		'public'       => true,
		'has_archive'  => false, // liste /egitmenler/ Sayfası'ndan (page-egitmenler.php) gelir
		'rewrite'      => array( 'slug' => 'egitmenler', 'with_front' => false ), // tekil eğitmen: /egitmenler/{eğitmen}/
		'menu_icon'    => 'dashicons-groups',
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
	) );
	register_taxonomy( 'category-teacher', 'teachers', array(
		'labels'       => array( 'name' => __( 'Eğitmen Kategorileri', 'bireysel' ) ),
		'hierarchical' => true,
		'public'       => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'categories-teachers' ),
	) );

	/* ---------- Kurs dokümanları (ders/ajanda kalemleri) ---------- */
	register_post_type( 'nd_learning_docs', array(
		'labels' => array(
			'name'          => __( 'Kurs Dokümanları', 'bireysel' ),
			'singular_name' => __( 'Doküman', 'bireysel' ),
			'menu_name'     => __( 'Kurs Dokümanları', 'bireysel' ),
		),
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => array( 'slug' => 'documents' ),
		'menu_icon'    => 'dashicons-media-document',
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'thumbnail' ),
	) );
}
add_action( 'init', 'bireysel_register_cpt' );
