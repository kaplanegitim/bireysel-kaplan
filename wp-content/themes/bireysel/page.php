<?php
/**
 * Genel sayfa şablonu.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

while ( have_posts() ) : the_post(); ?>
	<section class="page-hero">
		<div class="container">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>

	<?php
	// Sepet/ödeme/hesabım gibi WC sayfaları geniş container ister; düz metin sayfaları okunabilirlik için 860px.
	$bireysel_wide = ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) );
	?>
	<main class="section">
		<div class="container"<?php echo $bireysel_wide ? '' : ' style="max-width:860px"'; ?>>
			<?php // Eski [vc_*]/[nd_*] kalıntıları inc/legacy-cleanup.php'deki the_content filtresiyle temizlenir; meşru shortcode'lar normal çalışır. ?>
			<?php if ( $bireysel_wide ) : the_content(); else : ?>
				<div class="entry-content"><?php the_content(); ?></div>
			<?php endif; ?>
		</div>
	</main>
<?php endwhile;

get_footer();
