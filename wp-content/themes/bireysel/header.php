<?php
/**
 * Header.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$bireysel_cart_count = 0;
$bireysel_cart_url   = '#';
$bireysel_account    = '#';
if ( function_exists( 'wc_get_cart_url' ) ) {
	$bireysel_cart_url = wc_get_cart_url();
	if ( WC()->cart ) { $bireysel_cart_count = WC()->cart->get_cart_contents_count(); }
}
if ( function_exists( 'wc_get_page_permalink' ) ) {
	$bireysel_account = wc_get_page_permalink( 'myaccount' );
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) : // Site Icon ayarlı değilse tema fallback favicon ?>
		<link rel="icon" href="<?php echo esc_url( BIREYSEL_URI . '/assets/img/favicon.png' ); ?>" sizes="any">
		<link rel="apple-touch-icon" href="<?php echo esc_url( BIREYSEL_URI . '/assets/img/favicon.png' ); ?>">
	<?php endif; ?>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'template-parts/icons' ); ?>

<!-- Topbar -->
<div class="topbar">
	<div class="container">
		<div class="topbar__left">
			<?php
			$bireysel_phone = trim( get_theme_mod( 'bireysel_phone', '' ) );
			$bireysel_email = trim( get_theme_mod( 'bireysel_email', 'bilgi@kaplanegitim.com' ) );
			if ( $bireysel_phone ) :
				$bireysel_tel = preg_replace( '/[^0-9+]/', '', $bireysel_phone );
				?>
				<a class="topbar__contact" href="tel:<?php echo esc_attr( $bireysel_tel ); ?>"><svg class="ico"><use href="#i-phone"/></svg> <?php echo esc_html( $bireysel_phone ); ?></a>
			<?php endif; ?>
			<?php if ( $bireysel_email ) : ?>
				<a class="topbar__contact" href="mailto:<?php echo esc_attr( $bireysel_email ); ?>"><svg class="ico"><use href="#i-mail"/></svg> <?php echo esc_html( $bireysel_email ); ?></a>
			<?php endif; ?>
			<?php $bireysel_fs = function_exists( 'bireysel_free_shipping_threshold' ) ? bireysel_free_shipping_threshold() : 0; ?>
			<?php if ( $bireysel_fs > 0 ) : ?>
				<span><svg class="ico"><use href="#i-truck"/></svg> <?php printf( esc_html__( '%s TL üzeri kargo ücretsiz', 'bireysel' ), number_format( $bireysel_fs, 0, ',', '.' ) ); ?></span>
			<?php endif; ?>
		</div>
		<div class="topbar__right">
			<?php
			if ( function_exists( 'pll_the_languages' ) ) {
				$bireysel_langs = pll_the_languages( array( 'raw' => 1, 'hide_if_empty' => 0 ) );
				if ( ! empty( $bireysel_langs ) ) {
					$bireysel_flags = array( 'tr' => 'tr.svg', 'en' => 'us.svg' );
					echo '<span class="topbar__lang">';
					foreach ( $bireysel_langs as $bl ) {
						$slug = $bl['slug'];
						$name = ! empty( $bl['name'] ) ? $bl['name'] : strtoupper( $slug );
						$cls  = ! empty( $bl['current_lang'] ) ? 'lang is-active' : 'lang';
						if ( isset( $bireysel_flags[ $slug ] ) ) {
							printf( '<a href="%s" class="%s" title="%s" aria-label="%s"><img class="lang__flag" src="%s" alt="%s" width="22" height="15"></a>', esc_url( $bl['url'] ), esc_attr( $cls ), esc_attr( $name ), esc_attr( $name ), esc_url( BIREYSEL_URI . '/assets/img/flags/' . $bireysel_flags[ $slug ] ), esc_attr( $name ) );
						} else {
							printf( '<a href="%s" class="%s">%s</a>', esc_url( $bl['url'] ), esc_attr( $cls ), esc_html( strtoupper( $slug ) ) );
						}
					}
					echo '</span>';
				}
			}
			?>
			<a href="<?php echo esc_url( $bireysel_account ); ?>"><?php esc_html_e( 'Hesabım', 'bireysel' ); ?></a>
		</div>
	</div>
</div>

<!-- Header -->
<header class="header">
	<div class="container">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<img class="brand__logo" src="<?php echo esc_url( BIREYSEL_URI . '/assets/img/logo.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
			<?php endif; ?>
		</a>

		<nav class="nav" aria-label="<?php esc_attr_e( 'Ana menü', 'bireysel' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'items_wrap'     => '<ul>%3$s</ul>',
				'depth'          => 2,
				'fallback_cb'    => 'bireysel_nav_fallback',
			) );
			?>
		</nav>

		<div class="header__actions">
			<button class="iconbtn" type="button" aria-label="<?php esc_attr_e( 'Ara', 'bireysel' ); ?>" data-search-toggle><svg class="ico"><use href="#i-search"/></svg></button>
			<a class="iconbtn" href="<?php echo esc_url( $bireysel_account ); ?>" aria-label="<?php esc_attr_e( 'Hesabım', 'bireysel' ); ?>"><svg class="ico"><use href="#i-user"/></svg></a>
			<a class="iconbtn" href="<?php echo esc_url( $bireysel_cart_url ); ?>" aria-label="<?php esc_attr_e( 'Sepet', 'bireysel' ); ?>"><svg class="ico"><use href="#i-cart"/></svg><span class="iconbtn__count"><?php echo esc_html( $bireysel_cart_count ); ?></span></a>
			<button class="iconbtn nav-toggle" type="button" aria-label="<?php esc_attr_e( 'Menü', 'bireysel' ); ?>"><svg class="ico"><use href="#i-menu"/></svg></button>
		</div>
	</div>
</header>

<!-- Arama paneli (header arama ikonuyla açılır) -->
<div class="search-overlay" data-search-panel role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site içi arama', 'bireysel' ); ?>">
	<div class="container">
		<button class="search-overlay__close" type="button" data-search-close aria-label="<?php esc_attr_e( 'Kapat', 'bireysel' ); ?>">&times;</button>
		<?php get_search_form(); ?>
	</div>
</div>
