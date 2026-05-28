<?php
/**
 * Hakkımızda sayfası.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
while ( have_posts() ) : the_post(); endwhile;
$img = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'large' ) : home_url( '/wp-content/uploads/2016/08/parallax8.jpg' );
?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
		<h1><?php the_title(); ?></h1>
	</div>
</section>

<section class="section">
	<div class="container split">
		<div>
			<span class="split__eyebrow"><?php esc_html_e( 'Hikayemiz', 'bireysel' ); ?></span>
			<h2><?php esc_html_e( 'Gelişimi herkes için erişilebilir kılıyoruz', 'bireysel' ); ?></h2>
			<p><?php esc_html_e( 'Kaplan Eğitim olarak, profesyonel hayatın gerektirdiği becerileri sade, uygulanabilir ve sürdürülebilir biçimde sunmak için yola çıktık. Bireysel markamız altında; gelişim kitapları, planlama setleri ve online eğitimlerle hem öğrencilere hem profesyonellere ulaşıyoruz.', 'bireysel' ); ?></p>
			<ul class="series__list" style="margin:1.2rem 0 1.6rem">
				<li style="color:var(--c-ink-soft)"><svg class="ico" style="color:var(--c-success)"><use href="#i-check"/></svg> <?php esc_html_e( 'Uygulamalı, sahadan örneklerle', 'bireysel' ); ?></li>
				<li style="color:var(--c-ink-soft)"><svg class="ico" style="color:var(--c-success)"><use href="#i-check"/></svg> <?php esc_html_e( 'Kitap, set ve online eğitim bir arada', 'bireysel' ); ?></li>
				<li style="color:var(--c-ink-soft)"><svg class="ico" style="color:var(--c-success)"><use href="#i-check"/></svg> <?php esc_html_e( 'Uzman, uygulamacı eğitmen kadrosu', 'bireysel' ); ?></li>
			</ul>
			<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'iletisim' ) ) ); ?>" class="btn btn--primary"><?php esc_html_e( 'Bize Ulaşın', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a>
		</div>
		<img src="<?php echo esc_url( $img ); ?>" alt="<?php bloginfo( 'name' ); ?>" style="aspect-ratio:4/3;object-fit:cover">
	</div>
</section>

<section class="section section--soft">
	<div class="container">
		<div class="section__head"><span class="section__eyebrow"><?php esc_html_e( 'Değerlerimiz', 'bireysel' ); ?></span><h2 class="section__title"><?php esc_html_e( 'Neye İnanıyoruz?', 'bireysel' ); ?></h2></div>
		<div class="grid-cards grid-cards--3">
			<div class="value-card"><svg class="ico"><use href="#i-target"/></svg><h4><?php esc_html_e( 'Uygulanabilirlik', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Teoriden çok, ertesi gün işine yarayacak pratik araçlar ve örnekler.', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-bulb"/></svg><h4><?php esc_html_e( 'Sadelik', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Karmaşık konuları herkesin anlayabileceği bir dile çeviriyoruz.', 'bireysel' ); ?></p></div>
			<div class="value-card"><svg class="ico"><use href="#i-heart"/></svg><h4><?php esc_html_e( 'Süreklilik', 'bireysel' ); ?></h4><p><?php esc_html_e( 'Tek seferlik değil; gelişimi alışkanlığa dönüştüren bir yaklaşım.', 'bireysel' ); ?></p></div>
		</div>
	</div>
</section>

<section class="section section--dark">
	<div class="container statband">
		<div><b><?php echo esc_html( get_theme_mod( 'bireysel_stat_students', '5.000+' ) ); ?></b><span><?php esc_html_e( 'Mutlu Öğrenci', 'bireysel' ); ?></span></div>
		<div><b><?php echo (int) wp_count_posts( 'product' )->publish; ?></b><span><?php esc_html_e( 'Ürün & Set', 'bireysel' ); ?></span></div>
		<div><b><?php echo (int) wp_count_posts( 'courses' )->publish; ?></b><span><?php esc_html_e( 'Online Eğitim', 'bireysel' ); ?></span></div>
		<div><b><?php echo (int) wp_count_posts( 'teachers' )->publish; ?></b><span><?php esc_html_e( 'Uzman Eğitmen', 'bireysel' ); ?></span></div>
	</div>
</section>

<section class="section">
	<div class="container">
		<div class="section__head"><span class="section__eyebrow"><?php esc_html_e( 'Ekip', 'bireysel' ); ?></span><h2 class="section__title"><?php esc_html_e( 'Eğitmenlerimiz', 'bireysel' ); ?></h2></div>
		<?php $t = new WP_Query( array( 'post_type' => 'teachers', 'posts_per_page' => 3, 'post_status' => 'publish' ) );
		if ( $t->have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( $t->have_posts() ) : $t->the_post(); get_template_part( 'template-parts/teacher-card' ); endwhile; ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>

<?php get_footer();
