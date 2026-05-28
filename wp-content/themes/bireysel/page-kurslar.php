<?php
/**
 * "Kurslar" sayfası — kurs grid'i (courses CPT).
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
while ( have_posts() ) : the_post(); endwhile;
?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
		<h1><?php esc_html_e( 'Online Eğitimler', 'bireysel' ); ?></h1>
		<p style="color:#9fb1c7;margin:0;max-width:60ch"><?php esc_html_e( 'Veri okuryazarlığından kişisel liderliğe; uzman eğitmenlerle kendi hızında öğren.', 'bireysel' ); ?></p>
	</div>
</section>
<main class="section">
	<div class="container">
		<?php
		$q = new WP_Query( array( 'post_type' => 'courses', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'menu_order date', 'order' => 'ASC' ) );
		if ( $q->have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( $q->have_posts() ) : $q->the_post(); get_template_part( 'template-parts/course-card' ); endwhile; ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</main>
<?php get_footer();
