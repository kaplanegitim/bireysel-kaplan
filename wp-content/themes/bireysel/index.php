<?php
/**
 * Geçici ana şablon (Faz 1).
 * Faz 2'de header.php + footer.php + gerçek loop ile değiştirilecek.
 *
 * @package bireysel
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
?>
<main class="section">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h1><?php the_title(); ?></h1>
					<div class="entry-content"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php else : ?>
			<p><?php esc_html_e( 'İçerik bulunamadı.', 'bireysel' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
