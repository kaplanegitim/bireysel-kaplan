<?php
/**
 * Eski stack kalıntı temizliği.
 *
 * EducationPack / nd-learning / WPBakery (js_composer) / Revolution Slider / Timetable
 * eklentileri bu temada YOK. İçerikte (page/product/course/teacher/doc) kalan orphan
 * shortcode'lar, eklenti olmadığı için ham metin olarak görünürdü. Bu filtre onları
 * render anında söker — yalnızca BİLİNEN eski prefix'leri hedefler; WooCommerce, gallery
 * ve diğer meşru shortcode'lara DOKUNMAZ.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * İçerikten eski/orphan WPBakery & nd & revslider & timetable shortcode'larını temizler.
 *
 * @param string $content Ham içerik.
 * @return string Temizlenmiş içerik.
 */
function bireysel_strip_legacy_shortcodes( $content ) {
	if ( ! is_string( $content ) || false === strpos( $content, '[' ) ) {
		return $content;
	}
	// Açılış + kapanış tag'leri: [vc_row ...], [/vc_column], [nd_...], [rev_slider ...], [timetable ...]
	return preg_replace(
		'#\[/?(?:vc_[a-z0-9_]*|nd_[a-z0-9_]*|rev_slider[a-z0-9_]*|timetable[a-z0-9_]*)[^\]]*\]#i',
		'',
		$content
	);
}

// wpautop (10) ve do_shortcode (11)'dan ÖNCE temizle ki orphan tag'ler hiç işlenmesin.
add_filter( 'the_content', 'bireysel_strip_legacy_shortcodes', 8 );
add_filter( 'get_the_excerpt', 'bireysel_strip_legacy_shortcodes', 8 );
// WooCommerce ürün kısa açıklaması (the_content filtresinden geçmez).
add_filter( 'woocommerce_short_description', 'bireysel_strip_legacy_shortcodes', 8 );
