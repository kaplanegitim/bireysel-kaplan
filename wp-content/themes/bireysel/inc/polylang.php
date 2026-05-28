<?php
/**
 * Polylang entegrasyonu.
 *
 * 1) Customizer'daki DİNAMİK metinleri (güven şeridi, bölüm başlıkları, seri bloğu,
 *    geri sayım, footer) çeviri için kaydeder → Polylang → Diller → "String çevirileri"
 *    ekranında EN karşılıkları girilir.
 * 2) bireysel_pll() yardımcısı: tema şablonlarında bu metinleri pll__() ile çevirir.
 *
 * Tema UI string'leri (__()/_e) ayrıca languages/bireysel-en_US.l10n.php ile çevrilir.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Çeviriye kaydedilecek Customizer ayarları: id => array( etiket, varsayılan, çok-satır? ).
 */
function bireysel_pll_strings() {
	return array(
		// İletişim / footer
		'bireysel_footer_about'  => array( 'Footer açıklaması', 'Gelişim kitapları, planlama setleri ve online eğitimlerle bireysel gelişimini destekleyen Kaplan Eğitim platformu.', true ),
		'bireysel_address'       => array( 'Adres', "Örnek Mah. Eğitim Cad. No:1\nKadıköy / İstanbul", true ),
		'bireysel_hours'         => array( 'Çalışma saatleri', 'Hafta içi 09:00 – 18:00', false ),
		// Güven şeridi
		'bireysel_trust1_title'  => array( 'Güven 1 başlık', 'Ücretsiz Kargo', false ),
		'bireysel_trust1_sub'    => array( 'Güven 1 alt', '1.000 TL üzeri siparişlerde', false ),
		'bireysel_trust2_title'  => array( 'Güven 2 başlık', 'Güvenli Ödeme', false ),
		'bireysel_trust2_sub'    => array( 'Güven 2 alt', 'iyzico altyapısı', false ),
		'bireysel_trust3_title'  => array( 'Güven 3 başlık', 'Uzman Eğitmenler', false ),
		'bireysel_trust3_sub'    => array( 'Güven 3 alt', 'Alanında deneyimli', false ),
		'bireysel_trust4_title'  => array( 'Güven 4 başlık', 'Destek', false ),
		'bireysel_trust4_sub'    => array( 'Güven 4 alt', 'Sipariş & eğitim desteği', false ),
		// Bölüm başlıkları
		'bireysel_products_eyebrow' => array( 'Ürünler üst', 'Ürünler & Setler', false ),
		'bireysel_products_title'   => array( 'Ürünler başlık', 'Eğitim Setlerimiz', false ),
		'bireysel_products_sub'     => array( 'Ürünler alt', 'Kişisel, iş, satış, liderlik ve Excel gelişim setleri; planlama defterleri ve kitaplar.', true ),
		'bireysel_courses_eyebrow'  => array( 'Kurslar üst', 'Online Eğitim', false ),
		'bireysel_courses_title'    => array( 'Kurslar başlık', 'Bireysel Kurslarımız', false ),
		'bireysel_courses_sub'      => array( 'Kurslar alt', 'Uzman eğitmenlerden, kendi hızında izleyebileceğin eğitimler.', true ),
		'bireysel_teachers_eyebrow' => array( 'Eğitmenler üst', 'Ekip', false ),
		'bireysel_teachers_title'   => array( 'Eğitmenler başlık', 'Eğitmenlerimiz', false ),
		'bireysel_teachers_sub'     => array( 'Eğitmenler alt', 'Alanında deneyimli, uygulamacı eğitmen kadrosu.', true ),
		'bireysel_news_eyebrow'     => array( 'Bülten üst', 'Bülten', false ),
		'bireysel_news_title'       => array( 'Bülten başlık', 'Yeni eğitim ve setlerden ilk sen haberdar ol', false ),
		'bireysel_news_sub'         => array( 'Bülten alt', 'Kampanya ve yeni içerikleri kaçırmamak için e-bültene katıl.', true ),
		// Gelişim Kitapları bloğu
		'bireysel_series_title'   => array( 'Seri başlık', 'Gelişim Kitapları Serisi', false ),
		'bireysel_series_desc'    => array( 'Seri açıklama', 'Planlamadan veri okuryazarlığına, profesyonel hayatın her alanı için tasarlanmış kitap ve set serisi.', true ),
		'bireysel_series_bullets' => array( 'Seri maddeler', "Uygulamalı, sahadan örneklerle\nPlanlama defterleri + ajanda kombinleri\nExcel & veri analizi gelişim setleri", true ),
		'bireysel_series_btn'     => array( 'Seri buton', 'Seriyi İncele', false ),
		// Geri sayım
		'bireysel_event_title'    => array( 'Geri sayım başlık', 'Verilerle Karar Verme — Canlı Oturum', false ),
		'bireysel_event_sub'      => array( 'Geri sayım alt', 'Sınırlı kontenjanlı canlı eğitime kayıt için son günler.', false ),
		'bireysel_event_btn'      => array( 'Geri sayım buton', 'Hemen Kayıt Ol', false ),
		// Hakkımızda
		'bireysel_stat_students'  => array( 'Mutlu öğrenci sayısı', '5.000+', false ),
	);
}

/**
 * Customizer string'lerini Polylang'a kaydet.
 */
function bireysel_pll_register() {
	if ( ! function_exists( 'pll_register_string' ) ) { return; }
	foreach ( bireysel_pll_strings() as $id => $d ) {
		$val = get_theme_mod( $id, $d[1] );
		if ( '' === trim( (string) $val ) ) { continue; }
		pll_register_string( $id, $val, 'bireysel', ! empty( $d[2] ) );
	}
}
add_action( 'init', 'bireysel_pll_register' );

/**
 * Hero slaytlarını (bir_slide) Polylang'da çevrilebilir yap — her dil kendi slaytlarını gösterir.
 */
add_filter( 'pll_get_post_types', function ( $types, $is_settings ) {
	$types['bir_slide'] = 'bir_slide';
	$types['teachers']  = 'teachers';
	$types['courses']   = 'courses';
	return $types;
}, 10, 2 );

/**
 * Kurs taksonomilerini (kart süre/kategori vb.) çevrilebilir yap.
 */
add_filter( 'pll_get_taxonomies', function ( $taxes, $is_settings ) {
	foreach ( array( 'duration-course', 'category-course', 'difficulty-level-course', 'typology-course' ) as $t ) {
		$taxes[ $t ] = $t;
	}
	return $taxes;
}, 10, 2 );

/**
 * TR slug'lı bir Sayfa'nın GEÇERLİ dildeki çevirisinin kalıcı bağlantısını döndürür.
 * Çeviri yoksa orijinal sayfaya, sayfa yoksa ana sayfaya düşer. Dil-duyarlı menü/buton linkleri için.
 */
function bireysel_page_link( $slug ) {
	$p = get_page_by_path( $slug );
	if ( ! $p ) { return home_url( '/' ); }
	$id = $p->ID;
	if ( function_exists( 'pll_get_post' ) ) {
		$tr = pll_get_post( $id ); // geçerli dildeki çeviri
		if ( $tr ) { $id = $tr; }
	}
	return get_permalink( $id );
}

/**
 * Dinamik metni geçerli dile çevirir. Öncelik: Polylang string çevirisi (admin'in girdiği);
 * yoksa tema çevirisine düşer (languages/bireysel-en_US.l10n.php). Şablonlarda get_theme_mod
 * sonucunu sarmalar. Böylece varsayılan metinler Polylang dizgi girişi gerekmeden EN'de çıkar.
 */
function bireysel_pll( $text ) {
	if ( ! is_string( $text ) || '' === $text ) { return $text; }
	if ( function_exists( 'pll__' ) ) {
		$t = pll__( $text );
		if ( $t !== $text ) { return $t; } // Polylang'da çeviri girilmişse onu kullan
	}
	// translate() bilinmeyen domain için güvenli; çeviri yoksa metni aynen döndürür.
	return __( $text, 'bireysel' );
}
