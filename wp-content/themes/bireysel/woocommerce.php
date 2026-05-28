<?php
/**
 * WooCommerce sarmalayıcı — tüm Woo sayfaları (mağaza, ürün, sepet, ödeme, hesabım)
 * bu şablondan geçer; ortak header/footer + page-hero verir.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$is_single = is_product();

if ( ! $is_single ) :
	// Başlık
	$title = woocommerce_page_title( false );
	?>
	<section class="page-hero">
		<div class="container">
			<?php if ( function_exists( 'woocommerce_breadcrumb' ) ) {
				woocommerce_breadcrumb( array(
					'wrap_before' => '<nav class="breadcrumb">',
					'wrap_after'  => '</nav>',
					'delimiter'   => '<span class="sep">/</span>',
					'home'        => __( 'Ana Sayfa', 'bireysel' ),
				) );
			} ?>
			<h1><?php echo wp_kses_post( $title ); ?></h1>
		</div>
	</section>
<?php else : ?>
	<section class="section" style="padding:1.4rem 0 0">
		<div class="container">
			<?php woocommerce_breadcrumb( array(
				'wrap_before' => '<nav class="breadcrumb" style="color:var(--c-muted)">',
				'wrap_after'  => '</nav>',
				'delimiter'   => '<span class="sep">/</span>',
				'home'        => __( 'Ana Sayfa', 'bireysel' ),
			) ); ?>
		</div>
	</section>
<?php endif; ?>

<main class="section woo-main" style="padding-block:2.5rem">
	<div class="container">
		<?php woocommerce_content(); ?>
	</div>
</main>

<?php
get_footer();
