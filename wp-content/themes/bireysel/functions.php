<?php
/**
 * bireysel teması — bootstrap
 *
 * @package bireysel
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'BIREYSEL_VERSION', '1.0.0' );
define( 'BIREYSEL_DIR', get_template_directory() );
define( 'BIREYSEL_URI', get_template_directory_uri() );

/**
 * Tema desteği.
 */
function bireysel_setup() {
	load_theme_textdomain( 'bireysel', BIREYSEL_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'custom-logo', array( 'height' => 60, 'width' => 220, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/tokens.css', 'assets/css/components.css' ) );

	// WooCommerce
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	register_nav_menus( array(
		'primary' => __( 'Ana Menü', 'bireysel' ),
		'footer'  => __( 'Footer Menü', 'bireysel' ),
	) );
}
add_action( 'after_setup_theme', 'bireysel_setup' );

/**
 * Frontend varlıkları.
 */
function bireysel_assets() {
	// Google Fonts — Inter + Manrope
	wp_enqueue_style(
		'bireysel-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap',
		array(),
		null
	);

	// Asset sürümü = dosya değişiklik zamanı (cache-bust: her düzenlemede otomatik tazelenir)
	$ver = function ( $rel ) {
		$path = BIREYSEL_DIR . $rel;
		return file_exists( $path ) ? (string) filemtime( $path ) : BIREYSEL_VERSION;
	};

	// Design tokens (single source of truth) — önce yüklenir
	wp_enqueue_style( 'bireysel-tokens', BIREYSEL_URI . '/assets/css/tokens.css', array(), $ver( '/assets/css/tokens.css' ) );

	// Bileşenler — token'lara bağımlı
	wp_enqueue_style( 'bireysel-components', BIREYSEL_URI . '/assets/css/components.css', array( 'bireysel-tokens' ), $ver( '/assets/css/components.css' ) );

	// Tema kök style.css (yalnızca header; zararsız)
	wp_enqueue_style( 'bireysel-style', get_stylesheet_uri(), array( 'bireysel-components' ), $ver( '/style.css' ) );

	// JS — etkileşimler (slider, sepet, qty, sekme, akordeon, countdown, form)
	wp_enqueue_script( 'bireysel-main', BIREYSEL_URI . '/assets/js/main.js', array(), $ver( '/assets/js/main.js' ), true );
	wp_localize_script( 'bireysel-main', 'bireyselForm', array(
		'ajax'  => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'bireysel_form' ),
	) );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'bireysel_assets' );

/**
 * nd-learning kendi şablonlarını template_include ile dayatıyor;
 * kurs arşiv/tek/taksonomi için TEMA şablonlarımızı zorla (yüksek öncelik).
 */
function bireysel_courses_template( $template ) {
	if ( is_singular( 'courses' ) ) {
		$t = locate_template( 'single-courses.php' );
		if ( $t ) { return $t; }
	}
	if ( is_post_type_archive( 'courses' ) || is_tax( array( 'category-course', 'duration-course', 'difficulty-level-course', 'typology-course', 'location-course' ) ) ) {
		$t = locate_template( 'archive-courses.php' );
		if ( $t ) { return $t; }
	}
	return $template;
}
add_filter( 'template_include', 'bireysel_courses_template', 999 );

// Mağaza/arşiv başlığını WC tekrar basmasın — başlık zaten woocommerce.php page-hero'da gösteriliyor.
add_filter( 'woocommerce_show_page_title', '__return_false' );

/**
 * Ücretsiz kargo uygunsa (sepet, admin'de tanımlı eşiği aştıysa) diğer kargo oranlarını gizle.
 * Böylece müşteriye seçim sordurmadan otomatik: eşik üstü → ücretsiz, eşik altı → sabit fiyat.
 */
function bireysel_hide_other_shipping_when_free( $rates, $package ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}
add_filter( 'woocommerce_package_rates', 'bireysel_hide_other_shipping_when_free', 100, 2 );

/**
 * Ücretsiz kargo eşiğini (free_shipping min_amount) WC kargo bölgelerinden okur.
 * Topbar/trust mesajlarının daima doğru olması için. 0 = ücretsiz kargo eşiği tanımlı değil.
 */
function bireysel_free_shipping_threshold() {
	static $cached = null;
	if ( null !== $cached ) { return $cached; }
	$cached = 0;
	if ( ! class_exists( 'WC_Shipping_Zones' ) ) { return $cached; }
	$zones   = WC_Shipping_Zones::get_zones();
	$zones[] = array( 'zone_id' => 0 ); // "Diğer bölgeler"
	foreach ( $zones as $z ) {
		$zone = new WC_Shipping_Zone( isset( $z['zone_id'] ) ? $z['zone_id'] : 0 );
		foreach ( $zone->get_shipping_methods( true ) as $method ) {
			if ( 'free_shipping' === $method->id ) {
				$amt = (float) $method->get_option( 'min_amount' );
				if ( $amt > 0 ) { $cached = $amt; return $cached; }
			}
		}
	}
	return $cached;
}

/**
 * Ana menü atanmadığında basit fallback.
 */
function bireysel_nav_fallback() {
	$items = array( home_url( '/' ) => __( 'Ana Sayfa', 'bireysel' ) );
	if ( function_exists( 'wc_get_page_permalink' ) ) {
		$items[ wc_get_page_permalink( 'shop' ) ] = __( 'Ürünlerimiz', 'bireysel' );
	}
	echo '<ul>';
	foreach ( $items as $url => $label ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * İçerik genişliği.
 */
function bireysel_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'bireysel_content_width', 0 );

/**
 * Modüler include'lar (fazlar ilerledikçe doldurulur).
 */
$bireysel_includes = array(
	'/inc/cpt.php',
	'/inc/course-teachers.php',
	'/inc/slides.php',
	'/inc/seo.php',
	'/inc/forms.php',
	'/inc/customizer.php',
	'/inc/legacy-cleanup.php',
	'/inc/polylang.php',
);
foreach ( $bireysel_includes as $file ) {
	$path = BIREYSEL_DIR . $file;
	if ( file_exists( $path ) ) { require_once $path; }
}
