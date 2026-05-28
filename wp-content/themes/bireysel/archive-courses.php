<?php
/**
 * Kurs arşivi (nd-learning "courses").
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$cats = get_terms( array( 'taxonomy' => 'category-course', 'hide_empty' => true ) );
?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php esc_html_e( 'Kurslar', 'bireysel' ); ?></span></nav>
		<h1><?php esc_html_e( 'Online Eğitimler', 'bireysel' ); ?></h1>
		<p style="color:#9fb1c7;margin:0;max-width:60ch"><?php esc_html_e( 'Veri okuryazarlığından kişisel liderliğe; uzman eğitmenlerle kendi hızında öğren.', 'bireysel' ); ?></p>
	</div>
</section>

<main class="section" style="padding-block:2.5rem">
	<div class="container">

		<?php if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) : ?>
			<div class="shop-toolbar">
				<div class="catstrip" style="justify-content:flex-start">
					<a class="catpill" href="<?php echo esc_url( get_post_type_archive_link( 'courses' ) ); ?>"><svg class="ico"><use href="#i-cap"/></svg> <?php esc_html_e( 'Tümü', 'bireysel' ); ?></a>
					<?php foreach ( array_slice( $cats, 0, 4 ) as $c ) : ?>
						<a class="catpill" href="<?php echo esc_url( get_term_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/course-card' ); endwhile; ?>
			</div>
			<nav class="pagination">
				<?php echo paginate_links( array(
					'prev_text' => '<svg class="ico" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg>',
					'next_text' => '<svg class="ico"><use href="#i-arrow"/></svg>',
					'type'      => 'list',
				) ); ?>
			</nav>
		<?php else : ?>
			<p><?php esc_html_e( 'Henüz eğitim eklenmedi.', 'bireysel' ); ?></p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
