<?php
/**
 * "Eğitmenler" sayfası — eğitmen grid'i (teachers CPT).
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header(); ?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
		<h1><?php the_title(); ?></h1>
		<p style="color:#9fb1c7;margin:0;max-width:60ch"><?php esc_html_e( 'Alanında deneyimli, uygulamacı eğitmen kadromuzla tanışın.', 'bireysel' ); ?></p>
	</div>
</section>
<main class="section">
	<div class="container">
		<?php
		$teachers = new WP_Query( array( 'post_type' => 'teachers', 'posts_per_page' => -1, 'post_status' => 'publish' ) );
		if ( $teachers->have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( $teachers->have_posts() ) : $teachers->the_post(); get_template_part( 'template-parts/teacher-card' ); endwhile; ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</main>
<?php get_footer();
