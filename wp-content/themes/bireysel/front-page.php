<?php
/**
 * Ana sayfa.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$uploads      = home_url( '/wp-content/uploads' );
$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$courses_url  = function_exists( 'bireysel_page_link' ) ? bireysel_page_link( 'kurslar' ) : home_url( '/kurslar/' );
$teachers_url = function_exists( 'bireysel_page_link' ) ? bireysel_page_link( 'egitmenler' ) : home_url( '/egitmenler/' );
$event_date   = get_theme_mod( 'bireysel_event_date', '2026-06-12T10:00:00' );
?>

<!-- Hero Slider -->
<section class="hero-slider" id="hero">
	<div class="hero-track">
		<?php
		$slides = new WP_Query( array(
			'post_type'      => 'bir_slide',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		) );
		if ( $slides->have_posts() ) :
			$si = 0;
			while ( $slides->have_posts() ) : $slides->the_post();
				$si++;
				$variant  = get_post_meta( get_the_ID(), '_bir_variant', true ) ?: 'dark';
				$eyebrow  = get_post_meta( get_the_ID(), '_bir_eyebrow', true );
				$lead     = get_post_meta( get_the_ID(), '_bir_lead', true );
				$pricetag = get_post_meta( get_the_ID(), '_bir_pricetag', true );
				$b1t = get_post_meta( get_the_ID(), '_bir_btn1_text', true );
				$b1u = get_post_meta( get_the_ID(), '_bir_btn1_url', true );
				$b2t = get_post_meta( get_the_ID(), '_bir_btn2_text', true );
				$b2u = get_post_meta( get_the_ID(), '_bir_btn2_url', true );
				$stats_raw = get_post_meta( get_the_ID(), '_bir_stats', true );
				?>
				<div class="hero-slide hs--<?php echo esc_attr( $variant ); ?>">
					<div class="container hero__inner">
						<div class="hero__content">
							<?php if ( $eyebrow ) : ?><span class="hero__eyebrow"><svg class="ico" style="width:16px;height:16px"><use href="#i-star"/></svg> <?php echo esc_html( $eyebrow ); ?></span><?php endif; ?>
							<?php $htag = ( 1 === $si ) ? 'h1' : 'h2'; ?>
								<<?php echo $htag; ?> class="hero__title"><?php echo wp_kses( get_the_title(), array( 'br' => array() ) ); ?></<?php echo $htag; ?>>
							<?php if ( $lead ) : ?><p class="hero__lead"><?php echo esc_html( $lead ); ?></p><?php endif; ?>
							<div class="hero__cta">
								<?php if ( $b1t ) : ?><a href="<?php echo esc_url( $b1u ?: '#' ); ?>" class="btn btn--primary"><?php echo esc_html( $b1t ); ?></a><?php endif; ?>
								<?php if ( $b2t ) : ?><a href="<?php echo esc_url( $b2u ?: '#' ); ?>" class="btn btn--ghost"><?php echo esc_html( $b2t ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a><?php endif; ?>
							</div>
							<?php if ( $stats_raw ) : ?>
								<div class="hero__stats">
									<?php foreach ( preg_split( '/\r\n|\r|\n/', $stats_raw ) as $line ) {
										$parts = explode( '|', $line, 2 );
										if ( '' === trim( $parts[0] ) ) { continue; }
										printf( '<div class="hero__stat"><b>%s</b><span>%s</span></div>', esc_html( trim( $parts[0] ) ), esc_html( isset( $parts[1] ) ? trim( $parts[1] ) : '' ) );
									} ?>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="hero__product">
								<?php the_post_thumbnail( 'full', array( 'loading' => 'eager' ) ); ?>
								<?php if ( $pricetag ) : ?><span class="hero__pricetag"><?php echo esc_html( $pricetag ); ?></span><?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; wp_reset_postdata();
		else : ?>
			<div class="hero-slide hs--dark"><div class="container hero__inner"><div class="hero__content">
				<h1><?php bloginfo( 'name' ); ?></h1>
				<p class="hero__lead"><?php bloginfo( 'description' ); ?></p>
				<div class="hero__cta"><a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn--primary"><?php esc_html_e( 'Ürünleri Keşfet', 'bireysel' ); ?></a></div>
			</div></div></div>
		<?php endif; ?>
	</div>
	<button class="hero-arrow hero-arrow--prev" aria-label="Önceki"><svg class="ico" style="transform:rotate(180deg)"><use href="#i-arrow"/></svg></button>
	<button class="hero-arrow hero-arrow--next" aria-label="Sonraki"><svg class="ico"><use href="#i-arrow"/></svg></button>
	<div class="hero-dots" role="tablist"></div>
</section>

<!-- Güven şeridi -->
<section class="section section--soft" style="padding-block:2.2rem">
	<div class="container">
		<div class="trust">
			<?php
			$bir_trust_icons = array( 1 => 'i-truck', 2 => 'i-shield', 3 => 'i-cap', 4 => 'i-headset' );
			$bir_trust_def   = array(
				1 => array( 'Ücretsiz Kargo', '1.000 TL üzeri siparişlerde' ),
				2 => array( 'Güvenli Ödeme', 'iyzico altyapısı' ),
				3 => array( 'Uzman Eğitmenler', 'Alanında deneyimli' ),
				4 => array( 'Destek', 'Sipariş & eğitim desteği' ),
			);
			foreach ( $bir_trust_icons as $n => $icon ) {
				$t = bireysel_pll( get_theme_mod( "bireysel_trust{$n}_title", $bir_trust_def[ $n ][0] ) );
				$s = bireysel_pll( get_theme_mod( "bireysel_trust{$n}_sub", $bir_trust_def[ $n ][1] ) );
				if ( '' === trim( $t ) ) { continue; }
				printf(
					'<div class="trust__item"><svg class="ico"><use href="#%s"/></svg><div><b>%s</b><span>%s</span></div></div>',
					esc_attr( $icon ), esc_html( $t ), esc_html( $s )
				);
			}
			?>
		</div>
	</div>
</section>

<!-- Eğitim Setlerimiz (ürünler) -->
<section class="section" id="urunler">
	<div class="container">
		<div class="section__head">
			<span class="section__eyebrow"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_products_eyebrow', 'Ürünler & Setler' ) ) ); ?></span>
			<h2 class="section__title"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_products_title', 'Eğitim Setlerimiz' ) ) ); ?></h2>
			<p class="section__sub"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_products_sub', 'Kişisel, iş, satış, liderlik ve Excel gelişim setleri; planlama defterleri ve kitaplar.' ) ) ); ?></p>
		</div>
		<?php
		$products = new WP_Query( array(
			'post_type'           => 'product',
			'posts_per_page'      => 8,
			'post_status'         => 'publish',
			'orderby'             => 'menu_order date',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
		) );
		if ( $products->have_posts() ) : ?>
			<div class="grid-cards">
				<?php while ( $products->have_posts() ) : $products->the_post(); get_template_part( 'template-parts/product-card' ); endwhile; ?>
			</div>
			<div class="section__more"><a href="<?php echo esc_url( $shop_url ); ?>" class="btn btn--ghost"><?php esc_html_e( 'Tüm Ürünler', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a></div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>

<!-- Bireysel Kurslarımız -->
<section class="section section--soft" id="kurslar">
	<div class="container">
		<div class="section__head">
			<span class="section__eyebrow"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_courses_eyebrow', 'Online Eğitim' ) ) ); ?></span>
			<h2 class="section__title"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_courses_title', 'Bireysel Kurslarımız' ) ) ); ?></h2>
			<p class="section__sub"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_courses_sub', 'Uzman eğitmenlerden, kendi hızında izleyebileceğin eğitimler.' ) ) ); ?></p>
		</div>
		<?php
		$courses = new WP_Query( array(
			'post_type'           => 'courses',
			'posts_per_page'      => 3,
			'post_status'         => 'publish',
			'orderby'             => 'menu_order date',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
		) );
		if ( $courses->have_posts() ) : ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( $courses->have_posts() ) : $courses->the_post(); get_template_part( 'template-parts/course-card' ); endwhile; ?>
			</div>
			<div class="section__more"><a href="<?php echo esc_url( $courses_url ); ?>" class="btn btn--ghost"><?php esc_html_e( 'Tüm Eğitimler', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a></div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>

<!-- En Yakın Eğitim — Geri Sayım -->
<section class="section countdown" data-deadline="<?php echo esc_attr( $event_date ); ?>">
	<div class="container">
		<span class="countdown__eyebrow"><?php esc_html_e( 'En Yakın Eğitim', 'bireysel' ); ?></span>
		<h2><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_event_title', 'Verilerle Karar Verme — Canlı Oturum' ) ) ); ?></h2>
		<p class="countdown__sub"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_event_sub', 'Sınırlı kontenjanlı canlı eğitime kayıt için son günler.' ) ) ); ?></p>
		<div class="cd-timer">
			<div class="cd-unit"><b data-cd="d">0</b><span><?php esc_html_e( 'Gün', 'bireysel' ); ?></span></div>
			<div class="cd-unit"><b data-cd="h">00</b><span><?php esc_html_e( 'Saat', 'bireysel' ); ?></span></div>
			<div class="cd-unit"><b data-cd="m">00</b><span><?php esc_html_e( 'Dakika', 'bireysel' ); ?></span></div>
			<div class="cd-unit"><b data-cd="s">00</b><span><?php esc_html_e( 'Saniye', 'bireysel' ); ?></span></div>
		</div>
		<a href="<?php echo esc_url( get_theme_mod( 'bireysel_event_btn_url', '' ) ?: $courses_url ); ?>" class="btn btn--primary"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_event_btn', 'Hemen Kayıt Ol' ) ) ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a>
	</div>
</section>

<!-- Gelişim Kitapları Serisi -->
<section class="section" id="seri">
	<div class="container">
		<?php
		$bir_series_bullets = bireysel_pll( get_theme_mod( 'bireysel_series_bullets', "Uygulamalı, sahadan örneklerle\nPlanlama defterleri + ajanda kombinleri\nExcel & veri analizi gelişim setleri" ) );
		$bir_series_btn_url = get_theme_mod( 'bireysel_series_btn_url', '' ) ?: $shop_url;
		$bir_series_imgs    = array(
			get_theme_mod( 'bireysel_series_img1', '' ) ?: $uploads . '/2024/10/Planlamacının-Seyir-Defteri-.jpg',
			get_theme_mod( 'bireysel_series_img2', '' ) ?: $uploads . '/2020/03/KAPAK.jpeg',
			get_theme_mod( 'bireysel_series_img3', '' ) ?: $uploads . '/2018/05/kutu01.jpg',
		);
		?>
		<div class="series">
			<div>
				<h2><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_series_title', 'Gelişim Kitapları Serisi' ) ) ); ?></h2>
				<p><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_series_desc', 'Planlamadan veri okuryazarlığına, profesyonel hayatın her alanı için tasarlanmış kitap ve set serisi.' ) ) ); ?></p>
				<ul class="series__list">
					<?php foreach ( preg_split( '/\r\n|\r|\n/', $bir_series_bullets ) as $bir_b ) {
						if ( '' === trim( $bir_b ) ) { continue; }
						printf( '<li><svg class="ico"><use href="#i-check"/></svg> %s</li>', esc_html( trim( $bir_b ) ) );
					} ?>
				</ul>
				<a href="<?php echo esc_url( $bir_series_btn_url ); ?>" class="btn btn--primary"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_series_btn', 'Seriyi İncele' ) ) ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a>
			</div>
			<div class="series__shots">
				<?php foreach ( $bir_series_imgs as $bir_img ) : ?>
					<img src="<?php echo esc_url( $bir_img ); ?>" alt="" loading="lazy">
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

<!-- Eğitmenler -->
<section class="section section--soft">
	<div class="container">
		<div class="section__head">
			<span class="section__eyebrow"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_teachers_eyebrow', 'Ekip' ) ) ); ?></span>
			<h2 class="section__title"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_teachers_title', 'Eğitmenlerimiz' ) ) ); ?></h2>
			<p class="section__sub"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_teachers_sub', 'Alanında deneyimli, uygulamacı eğitmen kadrosu.' ) ) ); ?></p>
		</div>
		<?php
		$teachers = new WP_Query( array(
			'post_type'           => 'teachers',
			'posts_per_page'      => 3,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
		) );
		$tg = array( 'linear-gradient(135deg,#5AC8FB,#129BD8)', 'linear-gradient(135deg,#2BB4F2,#0F1B2D)', 'linear-gradient(135deg,#129BD8,#15263d)' );
		if ( $teachers->have_posts() ) : $i = 0; ?>
			<div class="grid-cards grid-cards--3">
				<?php while ( $teachers->have_posts() ) : $teachers->the_post();
					$nm = get_the_title();
					$ini = '';
					foreach ( preg_split( '/\s+/', trim( $nm ) ) as $w ) { $ini .= mb_substr( $w, 0, 1 ); }
					$ini = mb_strtoupper( mb_substr( $ini, 0, 2 ) );
					?>
					<div class="teacher">
						<a href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'teacher__photo' ) ); ?>
							<?php else : ?>
								<div class="teacher__photo" style="background:<?php echo esc_attr( $tg[ $i % 3 ] ); ?>;display:grid;place-items:center;color:#fff;font-family:var(--font-heading);font-weight:800;font-size:2.2rem"><?php echo esc_html( $ini ); ?></div>
							<?php endif; ?>
						</a>
						<h4><a href="<?php the_permalink(); ?>"><?php echo esc_html( $nm ); ?></a></h4>
						<span><?php echo esc_html( wp_trim_words( get_the_excerpt(), 6, '' ) ); ?></span>
					</div>
				<?php $i++; endwhile; ?>
			</div>
			<div class="section__more"><a href="<?php echo esc_url( $teachers_url ); ?>" class="btn btn--ghost"><?php esc_html_e( 'Tüm Eğitmenler', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></a></div>
		<?php endif; wp_reset_postdata(); ?>
	</div>
</section>

<!-- Bülten / CTA -->
<section class="section section--dark cta-band">
	<div class="container">
		<span class="section__eyebrow" style="background:rgba(90,200,251,.15);color:#5AC8FB"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_news_eyebrow', 'Bülten' ) ) ); ?></span>
		<h2><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_news_title', 'Yeni eğitim ve setlerden ilk sen haberdar ol' ) ) ); ?></h2>
		<p style="color:#b9c6d8"><?php echo esc_html( bireysel_pll( get_theme_mod( 'bireysel_news_sub', 'Kampanya ve yeni içerikleri kaçırmamak için e-bültene katıl.' ) ) ); ?></p>
		<form class="newsletter bireysel-form">
			<input type="hidden" name="form_type" value="newsletter">
			<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
			<input type="email" name="email" placeholder="E-posta adresin" aria-label="E-posta" required>
			<button class="btn btn--primary" type="submit">Katıl</button>
		</form>
	</div>
</section>

<?php get_footer();
