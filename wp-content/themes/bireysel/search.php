<?php
/**
 * Arama sonuçları.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
$bir_q     = get_search_query();
$bir_found = (int) $GLOBALS['wp_query']->found_posts;
?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php esc_html_e( 'Arama', 'bireysel' ); ?></span></nav>
		<h1><?php printf( esc_html__( '“%s” için sonuçlar', 'bireysel' ), esc_html( $bir_q ) ); ?></h1>
		<p style="color:#9fb1c7;margin:0">
			<?php
			if ( $bir_found ) {
				printf( esc_html( _n( '%d sonuç bulundu.', '%d sonuç bulundu.', $bir_found, 'bireysel' ) ), $bir_found );
			} else {
				esc_html_e( 'Sonuç bulunamadı.', 'bireysel' );
			}
			?>
		</p>
	</div>
</section>

<main class="section">
	<div class="container" style="max-width:820px">
		<?php if ( have_posts() ) : ?>
			<div class="search-results">
				<?php
				while ( have_posts() ) : the_post();
					$pt    = get_post_type_object( get_post_type() );
					$label = $pt && isset( $pt->labels->singular_name ) ? $pt->labels->singular_name : '';
					$raw   = get_the_excerpt() ? get_the_excerpt() : get_the_content();
					if ( function_exists( 'bireysel_strip_legacy_shortcodes' ) ) { $raw = bireysel_strip_legacy_shortcodes( $raw ); }
					$excerpt = wp_trim_words( wp_strip_all_tags( strip_shortcodes( $raw ) ), 28 );
					?>
					<article class="search-result">
						<?php if ( $label ) : ?><span class="search-result__type"><?php echo esc_html( $label ); ?></span><?php endif; ?>
						<h3 class="search-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<?php if ( $excerpt ) : ?><p class="search-result__excerpt"><?php echo esc_html( $excerpt ); ?></p><?php endif; ?>
						<a class="search-result__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Görüntüle', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a>
					</article>
				<?php endwhile; ?>
			</div>
			<?php
			the_posts_pagination( array(
				'mid_size'  => 1,
				'prev_text' => esc_html__( 'Önceki', 'bireysel' ),
				'next_text' => esc_html__( 'Sonraki', 'bireysel' ),
				'class'     => 'pagination',
			) );
			?>
		<?php else : ?>
			<div class="search-empty">
				<p><?php esc_html_e( 'Aramanızla eşleşen bir içerik bulunamadı. Farklı bir kelimeyle tekrar deneyin.', 'bireysel' ); ?></p>
				<?php get_search_form(); ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php get_footer();
