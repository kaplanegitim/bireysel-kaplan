<?php
/**
 * Footer.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bireysel_shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$bireysel_acc  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
// İç sayfa bağlantıları (slug'a göre; geçerli dilin çevirisine yönlendirir, yoksa ana sayfa)
$bir_link = function ( $slug ) {
	$p = get_page_by_path( $slug );
	if ( ! $p ) { return home_url( '/' ); }
	$id = $p->ID;
	if ( function_exists( 'pll_get_post' ) ) {
		$tr = pll_get_post( $id ); // geçerli dildeki çeviri
		if ( $tr ) { $id = $tr; }
	}
	return get_permalink( $id );
};
$bireysel_kurslar    = $bir_link( 'kurslar' );
$bireysel_egitmenler = $bir_link( 'egitmenler' );
$bireysel_hakkimizda = $bir_link( 'hakkimizda' );
$bireysel_iletisim   = $bir_link( 'iletisim' );
$bireysel_mesafeli   = get_page_by_path( 'mesafeli-satis-sozlesmesi' );
?>
<footer class="footer">
	<div class="container">
		<div class="footer__grid">
			<div class="footer__brand">
				<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" style="margin-bottom:1rem">
					<img class="brand__logo" src="<?php echo esc_url( BIREYSEL_URI . '/assets/img/logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
				</a>
				<p><?php echo esc_html( get_theme_mod( 'bireysel_footer_about', 'Gelişim kitapları, planlama setleri ve online eğitimlerle bireysel gelişimini destekleyen Kaplan Eğitim platformu.' ) ); ?></p>
				<?php
				$bir_socials = array(
					'facebook'  => array( 'i-fb', 'Facebook' ),
					'instagram' => array( 'i-instagram', 'Instagram' ),
					'twitter'   => array( 'i-x', 'X' ),
					'linkedin'  => array( 'i-in', 'LinkedIn' ),
					'youtube'   => array( 'i-youtube', 'YouTube' ),
				);
				$bir_social_out = '';
				foreach ( $bir_socials as $net => $meta ) {
					$url = trim( (string) get_theme_mod( 'bireysel_social_' . $net, '' ) );
					if ( ! $url ) { continue; }
					$bir_social_out .= sprintf(
						'<a href="%s" class="footer__social-link" target="_blank" rel="noopener" aria-label="%s"><svg class="ico"><use href="#%s"/></svg></a>',
						esc_url( $url ), esc_attr( $meta[1] ), esc_attr( $meta[0] )
					);
				}
				if ( $bir_social_out ) { echo '<div class="footer__social">' . $bir_social_out . '</div>'; }
				?>
			</div>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<div class="footer__menus" style="display:contents">
					<?php wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'items_wrap' => '<div><h4>' . esc_html__( 'Menü', 'bireysel' ) . '</h4>%3$s</div>', 'depth' => 1 ) ); ?>
				</div>
			<?php else : ?>
				<div>
					<h4><?php esc_html_e( 'Mağaza', 'bireysel' ); ?></h4>
					<a href="<?php echo esc_url( $bireysel_shop ); ?>"><?php esc_html_e( 'Tüm Ürünler', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_shop ); ?>"><?php esc_html_e( 'Gelişim Setleri', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_shop ); ?>"><?php esc_html_e( 'Planlama & Ajanda', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_shop ); ?>"><?php esc_html_e( 'Excel Setleri', 'bireysel' ); ?></a>
				</div>
				<div>
					<h4><?php esc_html_e( 'Eğitim', 'bireysel' ); ?></h4>
					<a href="<?php echo esc_url( $bireysel_kurslar ); ?>"><?php esc_html_e( 'Online Kurslar', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_egitmenler ); ?>"><?php esc_html_e( 'Eğitmenler', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_iletisim ); ?>"><?php esc_html_e( 'Kurumsal Eğitim', 'bireysel' ); ?></a>
				</div>
				<div>
					<h4><?php esc_html_e( 'Kurumsal', 'bireysel' ); ?></h4>
					<a href="<?php echo esc_url( $bireysel_hakkimizda ); ?>"><?php esc_html_e( 'Hakkımızda', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_iletisim ); ?>"><?php esc_html_e( 'İletişim', 'bireysel' ); ?></a>
					<a href="<?php echo esc_url( $bireysel_acc ); ?>"><?php esc_html_e( 'Hesabım', 'bireysel' ); ?></a>
					<?php if ( $bireysel_mesafeli ) : ?>
						<a href="<?php echo esc_url( get_permalink( $bireysel_mesafeli ) ); ?>"><?php esc_html_e( 'Mesafeli Satış Sözleşmesi', 'bireysel' ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="footer__bottom">
			<span>&copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'Tüm hakları saklıdır.', 'bireysel' ); ?></span>
			<span><?php esc_html_e( 'Güvenli ödeme: iyzico · Havale/EFT · Kapıda Ödeme', 'bireysel' ); ?></span>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
