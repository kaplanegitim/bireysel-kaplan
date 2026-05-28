<?php
/**
 * İletişim sayfası.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
$phone = trim( get_theme_mod( 'bireysel_phone', '' ) );
$email = trim( get_theme_mod( 'bireysel_email', 'bilgi@kaplanegitim.com' ) );
$tel   = preg_replace( '/[^0-9+]/', '', $phone );
$addr  = get_theme_mod( 'bireysel_address', "Örnek Mah. Eğitim Cad. No:1\nKadıköy / İstanbul" );
$hours = get_theme_mod( 'bireysel_hours', 'Hafta içi 09:00 – 18:00' );
$map   = get_theme_mod( 'bireysel_map', 'https://www.openstreetmap.org/export/embed.html?bbox=29.02%2C40.98%2C29.05%2C41.00&amp;layer=mapnik&amp;marker=40.99%2C29.035' );
while ( have_posts() ) : the_post(); endwhile;
?>
<section class="page-hero">
	<div class="container">
		<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php esc_html_e( 'İletişim', 'bireysel' ); ?></span></nav>
		<h1><?php esc_html_e( 'İletişim', 'bireysel' ); ?></h1>
		<p style="color:#9fb1c7;margin:0;max-width:60ch"><?php esc_html_e( 'Sorularını, sipariş ve eğitim taleplerini bize iletebilirsin.', 'bireysel' ); ?></p>
	</div>
</section>

<main class="section" style="padding-block:2.5rem">
	<div class="container contact-grid">
		<div>
			<div class="info-list">
				<div class="info-card"><svg class="ico"><use href="#i-pin"/></svg><div><b><?php esc_html_e( 'Adres', 'bireysel' ); ?></b><span><?php echo nl2br( esc_html( $addr ) ); ?></span></div></div>
				<?php if ( $phone ) : ?><div class="info-card"><svg class="ico"><use href="#i-phone"/></svg><div><b><?php esc_html_e( 'Telefon', 'bireysel' ); ?></b><span><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $phone ); ?></a></span></div></div><?php endif; ?>
				<?php if ( $email ) : ?><div class="info-card"><svg class="ico"><use href="#i-mail"/></svg><div><b><?php esc_html_e( 'E-posta', 'bireysel' ); ?></b><span><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></span></div></div><?php endif; ?>
				<div class="info-card"><svg class="ico"><use href="#i-clock"/></svg><div><b><?php esc_html_e( 'Çalışma Saatleri', 'bireysel' ); ?></b><span><?php echo esc_html( $hours ); ?></span></div></div>
			</div>
			<?php if ( $map ) : ?>
				<div class="map"><iframe src="<?php echo esc_url( $map ); ?>" title="<?php esc_attr_e( 'Harita', 'bireysel' ); ?>" loading="lazy"></iframe></div>
			<?php endif; ?>
		</div>
		<?php get_template_part( 'template-parts/form-contact' ); ?>
	</div>
</main>

<?php get_footer();
