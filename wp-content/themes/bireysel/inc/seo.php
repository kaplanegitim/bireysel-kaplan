<?php
/**
 * SEO & Analytics — Yoast/MonsterInsights/OG plugin'lerinin yerine.
 * Meta description, Open Graph, Twitter Card, GA4 (gtag).
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Tekil içeriğin elle girilmiş SEO alanını oku (_bireysel_seo_title / _bireysel_seo_desc).
 */
function bireysel_seo_meta( $key ) {
	if ( ! is_singular() ) { return ''; }
	$id = get_queried_object_id();
	return $id ? trim( (string) get_post_meta( $id, '_bireysel_seo_' . $key, true ) ) : '';
}

/**
 * Geçerli sayfa için kısa açıklama üret. Öncelik: elle girilen SEO açıklaması.
 */
function bireysel_meta_description() {
	$manual = bireysel_seo_meta( 'desc' );
	if ( $manual ) { return mb_substr( $manual, 0, 160 ); }

	$desc = '';
	if ( is_front_page() ) {
		$desc = get_theme_mod( 'bireysel_meta_home', get_bloginfo( 'description' ) );
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		if ( $post && ! empty( $post->post_excerpt ) ) {
			$desc = $post->post_excerpt;
		} elseif ( $post ) {
			$desc = $post->post_content;
		}
	} elseif ( is_tax() || is_category() || is_tag() ) {
		$desc = term_description();
	}
	if ( ! $desc ) { $desc = get_bloginfo( 'description' ); }
	// Kayıtlı olmayan [vc_*]/[nd_*] tag'lerini ve HTML'i temizle.
	$desc = preg_replace( '/\[\/?[a-z0-9_]+[^\]]*\]/i', '', $desc );
	$desc = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $desc ) ) );
	return mb_substr( $desc, 0, 160 );
}

/**
 * <head> meta + Open Graph + Twitter + GA4.
 */
function bireysel_head_meta() {
	$desc  = bireysel_meta_description();
	$title = wp_get_document_title();
	$url   = ( is_front_page() ? home_url( '/' ) : ( is_singular() ? get_permalink() : home_url( add_query_arg( array() ) ) ) );
	$type  = ( is_singular( 'product' ) ) ? 'product' : ( is_singular() ? 'article' : 'website' );
	$img   = '';
	if ( is_singular() && has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	}
	if ( ! $img ) {
		$img = get_theme_mod( 'bireysel_og_default', BIREYSEL_URI . '/assets/img/logo.png' );
	}

	echo "\n<!-- bireysel SEO -->\n";
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	// Open Graph
	echo '<meta property="og:locale" content="' . esc_attr( get_bloginfo( 'language' ) ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) { echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n"; }
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	if ( $img ) { echo '<meta property="og:image" content="' . esc_url( $img ) . '">' . "\n"; }
	// Ürün fiyatı (og:product)
	if ( 'product' === $type && function_exists( 'wc_get_product' ) ) {
		$product = wc_get_product( get_the_ID() );
		if ( $product && '' !== $product->get_price() ) {
			echo '<meta property="product:price:amount" content="' . esc_attr( $product->get_price() ) . '">' . "\n";
			echo '<meta property="product:price:currency" content="' . esc_attr( get_woocommerce_currency() ) . '">' . "\n";
			echo '<meta property="product:availability" content="' . ( $product->is_in_stock() ? 'in stock' : 'out of stock' ) . '">' . "\n";
		}
	}
	// Twitter
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) { echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n"; }
	if ( $img ) { echo '<meta name="twitter:image" content="' . esc_url( $img ) . '">' . "\n"; }
}
add_action( 'wp_head', 'bireysel_head_meta', 5 );

/**
 * GA4 — gtag.js. Customizer'dan ölçüm kimliği (G-XXXX).
 */
function bireysel_ga4() {
	if ( is_admin() || is_customize_preview() ) { return; }
	$id = trim( get_theme_mod( 'bireysel_ga4_id', '' ) );
	if ( ! $id ) { return; }
	?>
<!-- GA4 -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $id ); ?>"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '<?php echo esc_js( $id ); ?>');
</script>
	<?php
}
add_action( 'wp_head', 'bireysel_ga4', 20 );

/**
 * Kurumsal kimlik JSON-LD (EducationalOrganization + LocalBusiness sinyalleri).
 * Identity Schema + Local Business Schema + adres/telefon SEO/GEO kalemlerini karşılar.
 */
function bireysel_org_schema() {
	$logo  = get_theme_mod( 'bireysel_og_default', BIREYSEL_URI . '/assets/img/logo.png' );
	$phone = trim( (string) get_theme_mod( 'bireysel_phone', '' ) );
	$email = trim( (string) get_theme_mod( 'bireysel_email', '' ) );
	$addr  = trim( (string) get_theme_mod( 'bireysel_address', '' ) );

	$data = array(
		'@context' => 'https://schema.org',
		'@type'    => array( 'EducationalOrganization', 'LocalBusiness' ),
		'name'     => get_bloginfo( 'name' ),
		'url'      => home_url( '/' ),
		'logo'     => esc_url_raw( $logo ),
	);
	if ( $phone ) { $data['telephone'] = $phone; }
	if ( $email ) { $data['email'] = $email; }
	if ( $addr ) {
		$data['address'] = array(
			'@type'          => 'PostalAddress',
			'streetAddress'  => trim( preg_replace( '/\s*\n\s*/', ', ', $addr ) ),
			'addressCountry' => 'TR',
		);
	}
	// Sosyal profiller (Customizer'da varsa): bireysel_social_* → sameAs
	$sameas = array();
	foreach ( array( 'facebook', 'instagram', 'twitter', 'linkedin', 'youtube' ) as $net ) {
		$u = trim( (string) get_theme_mod( 'bireysel_social_' . $net, '' ) );
		if ( $u ) { $sameas[] = esc_url_raw( $u ); }
	}
	if ( $sameas ) { $data['sameAs'] = $sameas; }

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}
add_action( 'wp_head', 'bireysel_org_schema', 6 );

/**
 * Elle girilen SEO başlığı varsa belge başlığını onunla değiştir.
 */
add_filter( 'pre_get_document_title', function ( $title ) {
	$manual = bireysel_seo_meta( 'title' );
	return $manual ? $manual : $title;
} );

/**
 * Arama sonuç sayfasını indeksletme (ince/yinelenen içerik).
 */
add_filter( 'wp_robots', function ( $robots ) {
	if ( is_search() ) {
		$robots['noindex']  = true;
		$robots['nofollow'] = true;
	}
	return $robots;
} );

/* -------------------------------------------------------------------------
 * Tekil içerik için basit SEO meta box (Yoast'sız manuel başlık/açıklama).
 * ---------------------------------------------------------------------- */

/**
 * Meta box'ın görüneceği genel (public) tekil tipler.
 */
function bireysel_seo_post_types() {
	return array( 'post', 'page', 'product', 'courses', 'teachers' );
}

/**
 * Meta box ekle.
 */
function bireysel_seo_metabox() {
	add_meta_box(
		'bireysel_seo',
		__( 'SEO (Kaplan)', 'bireysel' ),
		function ( $post ) {
			wp_nonce_field( 'bireysel_seo_save', 'bireysel_seo_nonce' );
			$t = get_post_meta( $post->ID, '_bireysel_seo_title', true );
			$d = get_post_meta( $post->ID, '_bireysel_seo_desc', true );
			echo '<style>.bir-seo label{display:block;font-weight:600;margin:10px 0 3px}.bir-seo input,.bir-seo textarea{width:100%}.bir-seo .c{color:#646970;font-size:12px}</style>';
			echo '<div class="bir-seo">';
			echo '<label>' . esc_html__( 'SEO Başlığı', 'bireysel' ) . '</label>';
			echo '<input type="text" name="bireysel_seo_title" maxlength="70" value="' . esc_attr( $t ) . '" placeholder="' . esc_attr__( 'Boş bırakılırsa otomatik başlık kullanılır', 'bireysel' ) . '">';
			echo '<p class="c">' . esc_html__( 'Önerilen: ~60 karakter.', 'bireysel' ) . '</p>';
			echo '<label>' . esc_html__( 'Meta Açıklama', 'bireysel' ) . '</label>';
			echo '<textarea name="bireysel_seo_desc" rows="3" maxlength="160" placeholder="' . esc_attr__( 'Boş bırakılırsa özet/içerikten üretilir', 'bireysel' ) . '">' . esc_textarea( $d ) . '</textarea>';
			echo '<p class="c">' . esc_html__( 'Önerilen: ~155 karakter.', 'bireysel' ) . '</p>';
			echo '</div>';
		},
		bireysel_seo_post_types(),
		'normal',
		'low'
	);
}
add_action( 'add_meta_boxes', 'bireysel_seo_metabox' );

/**
 * Meta box kaydet.
 */
function bireysel_seo_save( $post_id ) {
	if ( ! isset( $_POST['bireysel_seo_nonce'] ) || ! wp_verify_nonce( $_POST['bireysel_seo_nonce'], 'bireysel_seo_save' ) ) { return; }
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	$title = isset( $_POST['bireysel_seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['bireysel_seo_title'] ) ) : '';
	$desc  = isset( $_POST['bireysel_seo_desc'] ) ? sanitize_text_field( wp_unslash( $_POST['bireysel_seo_desc'] ) ) : '';
	if ( '' !== $title ) { update_post_meta( $post_id, '_bireysel_seo_title', $title ); } else { delete_post_meta( $post_id, '_bireysel_seo_title' ); }
	if ( '' !== $desc ) { update_post_meta( $post_id, '_bireysel_seo_desc', $desc ); } else { delete_post_meta( $post_id, '_bireysel_seo_desc' ); }
}
add_action( 'save_post', 'bireysel_seo_save' );
