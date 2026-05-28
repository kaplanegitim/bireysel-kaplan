<?php
/**
 * Gelişim Kitapları Serisi sayfası.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
while ( have_posts() ) : the_post(); endwhile;
$uploads  = home_url( '/wp-content/uploads' );
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
		<h1><?php the_title(); ?></h1>
		<p style="color:#9fb1c7;margin:0;max-width:60ch"><?php esc_html_e( 'Planlamadan veri okuryazarlığına; profesyonel hayatın her alanı için tasarlanmış kitap ve set serisi.', 'bireysel' ); ?></p>
	</div>
</section>

<section class="section">
	<div class="container split">
		<div>
			<span class="split__eyebrow"><?php esc_html_e( 'Seri Hakkında', 'bireysel' ); ?></span>
			<h2><?php esc_html_e( 'Öğren, planla, uygula', 'bireysel' ); ?></h2>
			<p><?php esc_html_e( 'Gelişim Kitapları Serisi; teoriyi pratiğe döken, sahadan örneklerle ilerleyen ve her yaştan profesyonele hitap eden kitap ve setlerden oluşur.', 'bireysel' ); ?></p>
			<a href="#kitaplar" class="btn btn--primary"><?php esc_html_e( 'Kitapları Gör', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a>
		</div>
		<img src="<?php echo esc_url( $uploads . '/2024/01/Planlamacının-Seyir-Defteri-2025-Ajanda-Yeni-Mezunun-Yaşam-Kiti.jpeg' ); ?>" alt="<?php the_title_attribute(); ?>" style="aspect-ratio:1/1;object-fit:cover">
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section__head"><span class="section__eyebrow"><?php esc_html_e( 'Neler Sunuyor?', 'bireysel' ); ?></span><h2 class="section__title"><?php esc_html_e( 'Serinin Sundukları', 'bireysel' ); ?></h2></div>
		<div class="grid-cards grid-cards--3">
			<div class="value-card"><svg class="ico"><use href="#i-book"/></svg><h4><?php esc_html_e( '2000 Kitaplık Kütüphane', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Güncel ve başucu yayınlardan oluşan kitap havuzumuzdan siz de faydalanın! Uzmanlarımız tarafından seçilmiş birbirinden değerli gelişim kitapları 6 farklı sette toplandı.', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-card"/></svg><h4><?php esc_html_e( 'Bilgi Kartları', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Sadece okumakla kalmıyoruz, bilgi kartları ile hayata ve gelişime dair pratik ipuçlarını sizlerle paylaşıyoruz: günlük yaşamı düzenleyen uygulamalar, kişisel marka, başarılı satış yöntemleri ve dahası!', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-bulb"/></svg><h4><?php esc_html_e( 'Zihin Haritaları', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Okumanın üzerinden zaman geçtikçe bilgileri unuturuz. Unutmamanın en iyi yöntemlerinden biri olarak hazırladığımız zihin haritalarını sizlerle paylaşıyoruz.', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-cap"/></svg><h4><?php esc_html_e( 'Gelişim Serileri', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Kariyer ve kişisel gelişim yolculuğunuzu setlerimizle destekliyoruz: Kişisel Gelişim, İş Gelişim, Liderlik ve Yönetim, Satış Gelişim, Pazarlama Gelişim ve Excel Gelişim.', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-cart"/></svg><h4><?php esc_html_e( 'Kutuda Gelişim', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Bir kutuya neler sığabilir? Gelişiminize katkıda bulunabilecek kaynaklar ve ipuçları bir arada.', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-cert"/></svg><h4><?php esc_html_e( 'Uzman Gözlüğü', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Eğitmen & danışman uzmanlarımızca iş ve kişisel gelişim hakkında tavsiye edilen kaynakları edinme şansı!', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-truck"/></svg><h4><?php esc_html_e( 'Hızlı Teslimat', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Siparişinizle birlikte Türkiye’nin her noktasına 7 iş günü içinde erişim.', 'bireysel' ); ?></p></div>
		</div>
	</div>
</section>

<section class="section section--soft" id="kitaplar">
	<div class="container">
		<div class="section__head"><span class="section__eyebrow"><?php esc_html_e( 'Seri', 'bireysel' ); ?></span><h2 class="section__title"><?php esc_html_e( 'Kitaplar & Setler', 'bireysel' ); ?></h2></div>
		<?php
		$q = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 8, 'post_status' => 'publish', 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( $q->have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( $q->have_posts() ) : $q->the_post(); get_template_part( 'template-parts/product-card' ); endwhile; ?>
			</div>
			<div class="section__more"><a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn--ghost"><?php esc_html_e( 'Tüm Ürünler', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a></div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>

<?php get_footer();
