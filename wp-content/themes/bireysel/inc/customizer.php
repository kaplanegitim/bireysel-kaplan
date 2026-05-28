<?php
/**
 * Customizer ayarları — logo (WP Site Kimliği) dışındaki tema seçenekleri.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function bireysel_customize_register( $wp_customize ) {

	$wp_customize->add_panel( 'bireysel_panel', array(
		'title'    => __( 'bireysel Tema Ayarları', 'bireysel' ),
		'priority' => 30,
	) );

	/* ---------- İletişim Bilgileri ---------- */
	$wp_customize->add_section( 'bireysel_contact', array(
		'title' => __( 'İletişim Bilgileri', 'bireysel' ),
		'panel' => 'bireysel_panel',
	) );

	$contact_fields = array(
		'bireysel_phone'        => array( __( 'Telefon', 'bireysel' ), '+90 530 967 23 66', 'text' ),
		'bireysel_email'        => array( __( 'E-posta', 'bireysel' ), 'bilgi@kaplanegitim.com', 'email' ),
		'bireysel_address'      => array( __( 'Adres', 'bireysel' ), "Örnek Mah. Eğitim Cad. No:1\nKadıköy / İstanbul", 'textarea' ),
		'bireysel_hours'        => array( __( 'Çalışma Saatleri', 'bireysel' ), 'Hafta içi 09:00 – 18:00', 'text' ),
		'bireysel_map'          => array( __( 'Harita embed URL', 'bireysel' ), '', 'url' ),
		'bireysel_form_email'   => array( __( 'Form bildirim e-postası', 'bireysel' ), get_option( 'admin_email' ), 'email' ),
		'bireysel_footer_about' => array( __( 'Footer açıklaması', 'bireysel' ), 'Gelişim kitapları, planlama setleri ve online eğitimlerle bireysel gelişimini destekleyen Kaplan Eğitim platformu.', 'textarea' ),
	);
	foreach ( $contact_fields as $id => $f ) {
		list( $label, $default, $type ) = $f;
		$sanitize = 'text' === $type ? 'sanitize_text_field' : ( 'email' === $type ? 'sanitize_email' : ( 'url' === $type ? 'esc_url_raw' : 'sanitize_textarea_field' ) );
		$wp_customize->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => $sanitize, 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'bireysel_contact',
			'type'    => ( 'textarea' === $type ? 'textarea' : ( 'email' === $type ? 'email' : ( 'url' === $type ? 'url' : 'text' ) ) ),
		) );
	}

	/* ---------- SEO & Analytics ---------- */
	$wp_customize->add_section( 'bireysel_seo', array(
		'title' => __( 'SEO & Analytics', 'bireysel' ),
		'panel' => 'bireysel_panel',
	) );
	$wp_customize->add_setting( 'bireysel_ga4_id', array( 'default' => '', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'bireysel_ga4_id', array(
		'label'       => __( 'GA4 Ölçüm Kimliği (G-XXXXXXX)', 'bireysel' ),
		'description' => __( 'Boş bırakılırsa analitik kodu eklenmez.', 'bireysel' ),
		'section'     => 'bireysel_seo',
		'type'        => 'text',
	) );
	$wp_customize->add_setting( 'bireysel_meta_home', array( 'default' => get_bloginfo( 'description' ), 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'bireysel_meta_home', array(
		'label'   => __( 'Anasayfa meta açıklaması', 'bireysel' ),
		'section' => 'bireysel_seo',
		'type'    => 'textarea',
	) );
	$wp_customize->add_setting( 'bireysel_og_default', array( 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'bireysel_og_default', array(
		'label'   => __( 'Varsayılan paylaşım görseli (OG)', 'bireysel' ),
		'section' => 'bireysel_seo',
	) ) );

	/* ---------- Sosyal Medya ---------- */
	$wp_customize->add_section( 'bireysel_social', array(
		'title' => __( 'Sosyal Medya', 'bireysel' ),
		'panel' => 'bireysel_panel',
		'description' => __( 'Profil URL’leri. Dolu olanlar footer’da ikon + schema sameAs olarak çıkar.', 'bireysel' ),
	) );
	foreach ( array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'twitter'   => 'X (Twitter)',
		'linkedin'  => 'LinkedIn',
		'youtube'   => 'YouTube',
	) as $net => $lbl ) {
		$wp_customize->add_setting( 'bireysel_social_' . $net, array( 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
		$wp_customize->add_control( 'bireysel_social_' . $net, array(
			'label'       => $lbl,
			'section'     => 'bireysel_social',
			'type'        => 'url',
			'input_attrs' => array( 'placeholder' => 'https://' ),
		) );
	}

	/* ---------- En Yakın Eğitim (Geri Sayım) ---------- */
	$wp_customize->add_section( 'bireysel_event', array(
		'title' => __( 'En Yakın Eğitim (Geri Sayım)', 'bireysel' ),
		'panel' => 'bireysel_panel',
	) );
	$wp_customize->add_setting( 'bireysel_event_title', array( 'default' => 'Verilerle Karar Verme — Canlı Oturum', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'bireysel_event_title', array( 'label' => __( 'Eğitim başlığı', 'bireysel' ), 'section' => 'bireysel_event', 'type' => 'text' ) );
	$wp_customize->add_setting( 'bireysel_event_date', array( 'default' => '2026-06-12T10:00:00', 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh' ) );
	$wp_customize->add_control( 'bireysel_event_date', array(
		'label'       => __( 'Eğitim tarihi/saati', 'bireysel' ),
		'description' => __( 'Biçim: 2026-06-12T10:00:00', 'bireysel' ),
		'section'     => 'bireysel_event',
		'type'        => 'text',
	) );

	// Yardımcı: metin/textarea/url kontrolü ekle
	$bir_text = function ( $id, $label, $section, $default = '', $type = 'text' ) use ( $wp_customize ) {
		$san = 'textarea' === $type ? 'sanitize_textarea_field' : ( 'url' === $type ? 'esc_url_raw' : 'sanitize_text_field' );
		$wp_customize->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => $san, 'transport' => 'refresh' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => $section, 'type' => ( 'url' === $type ? 'url' : $type ) ) );
	};

	/* ---------- En Yakın Eğitim — ek alanlar ---------- */
	$bir_text( 'bireysel_event_sub', __( 'Alt metin', 'bireysel' ), 'bireysel_event', 'Sınırlı kontenjanlı canlı eğitime kayıt için son günler.', 'text' );
	$bir_text( 'bireysel_event_btn', __( 'Buton yazısı', 'bireysel' ), 'bireysel_event', 'Hemen Kayıt Ol', 'text' );
	$bir_text( 'bireysel_event_btn_url', __( 'Buton bağlantısı (boş = Kurslar)', 'bireysel' ), 'bireysel_event', '', 'url' );

	/* ---------- Hakkımızda ---------- */
	$wp_customize->add_section( 'bireysel_about', array( 'title' => __( 'Hakkımızda', 'bireysel' ), 'panel' => 'bireysel_panel' ) );
	$bir_text( 'bireysel_stat_students', __( 'Mutlu Öğrenci sayısı (örn. 5.000+)', 'bireysel' ), 'bireysel_about', '5.000+', 'text' );

	/* ---------- Anasayfa: Bölüm Başlıkları ---------- */
	$wp_customize->add_section( 'bireysel_home_headings', array( 'title' => __( 'Anasayfa: Bölüm Başlıkları', 'bireysel' ), 'panel' => 'bireysel_panel' ) );
	$bir_secs = array(
		'products' => array( __( 'Eğitim Setlerimiz', 'bireysel' ), 'Ürünler & Setler', 'Eğitim Setlerimiz', 'Kişisel, iş, satış, liderlik ve Excel gelişim setleri; planlama defterleri ve kitaplar.' ),
		'courses'  => array( __( 'Bireysel Kurslarımız', 'bireysel' ), 'Online Eğitim', 'Bireysel Kurslarımız', 'Uzman eğitmenlerden, kendi hızında izleyebileceğin eğitimler.' ),
		'teachers' => array( __( 'Eğitmenler', 'bireysel' ), 'Ekip', 'Eğitmenlerimiz', 'Alanında deneyimli, uygulamacı eğitmen kadrosu.' ),
		'news'     => array( __( 'Bülten / CTA', 'bireysel' ), 'Bülten', 'Yeni eğitim ve setlerden ilk sen haberdar ol', 'Kampanya ve yeni içerikleri kaçırmamak için e-bültene katıl.' ),
	);
	foreach ( $bir_secs as $key => $d ) {
		$bir_text( "bireysel_{$key}_eyebrow", $d[0] . ' — ' . __( 'üst etiket', 'bireysel' ), 'bireysel_home_headings', $d[1], 'text' );
		$bir_text( "bireysel_{$key}_title", $d[0] . ' — ' . __( 'başlık', 'bireysel' ), 'bireysel_home_headings', $d[2], 'text' );
		$bir_text( "bireysel_{$key}_sub", $d[0] . ' — ' . __( 'alt metin', 'bireysel' ), 'bireysel_home_headings', $d[3], 'textarea' );
	}

	/* ---------- Anasayfa: Güven Şeridi ---------- */
	$wp_customize->add_section( 'bireysel_home_trust', array( 'title' => __( 'Anasayfa: Güven Şeridi', 'bireysel' ), 'panel' => 'bireysel_panel' ) );
	$bir_trust = array(
		1 => array( 'Ücretsiz Kargo', '1.000 TL üzeri siparişlerde' ),
		2 => array( 'Güvenli Ödeme', 'iyzico altyapısı' ),
		3 => array( 'Uzman Eğitmenler', 'Alanında deneyimli' ),
		4 => array( 'Destek', 'Sipariş & eğitim desteği' ),
	);
	foreach ( $bir_trust as $n => $d ) {
		$bir_text( "bireysel_trust{$n}_title", sprintf( __( '%d. başlık', 'bireysel' ), $n ), 'bireysel_home_trust', $d[0], 'text' );
		$bir_text( "bireysel_trust{$n}_sub", sprintf( __( '%d. alt metin', 'bireysel' ), $n ), 'bireysel_home_trust', $d[1], 'text' );
	}

	/* ---------- Anasayfa: Gelişim Kitapları Bloğu ---------- */
	$wp_customize->add_section( 'bireysel_home_series', array( 'title' => __( 'Anasayfa: Gelişim Kitapları', 'bireysel' ), 'panel' => 'bireysel_panel' ) );
	$bir_text( 'bireysel_series_title', __( 'Başlık', 'bireysel' ), 'bireysel_home_series', 'Gelişim Kitapları Serisi', 'text' );
	$bir_text( 'bireysel_series_desc', __( 'Açıklama', 'bireysel' ), 'bireysel_home_series', 'Planlamadan veri okuryazarlığına, profesyonel hayatın her alanı için tasarlanmış kitap ve set serisi.', 'textarea' );
	$bir_text( 'bireysel_series_bullets', __( 'Maddeler (her satır bir madde)', 'bireysel' ), 'bireysel_home_series', "Uygulamalı, sahadan örneklerle\nPlanlama defterleri + ajanda kombinleri\nExcel & veri analizi gelişim setleri", 'textarea' );
	$bir_text( 'bireysel_series_btn', __( 'Buton yazısı', 'bireysel' ), 'bireysel_home_series', 'Seriyi İncele', 'text' );
	$bir_text( 'bireysel_series_btn_url', __( 'Buton bağlantısı (boş = Mağaza)', 'bireysel' ), 'bireysel_home_series', '', 'url' );
	foreach ( array( 1, 2, 3 ) as $n ) {
		$wp_customize->add_setting( "bireysel_series_img{$n}", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh' ) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, "bireysel_series_img{$n}", array(
			'label'   => sprintf( __( 'Görsel %d', 'bireysel' ), $n ),
			'section' => 'bireysel_home_series',
		) ) );
	}
}
add_action( 'customize_register', 'bireysel_customize_register' );
