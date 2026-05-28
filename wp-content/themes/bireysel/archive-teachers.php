<?php
/**
 * Eğitmen arşivi.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header(); ?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php esc_html_e( 'Eğitmenler', 'bireysel' ); ?></span></nav>
		<h1><?php esc_html_e( 'Eğitmenlerimiz', 'bireysel' ); ?></h1>
		<p style="color:#9fb1c7;margin:0;max-width:60ch"><?php esc_html_e( 'Alanında deneyimli, uygulamacı eğitmen kadromuzla tanışın.', 'bireysel' ); ?></p>
	</div>
</section>
<main class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/teacher-card' ); endwhile; ?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'Eğitmen bulunamadı.', 'bireysel' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer();
