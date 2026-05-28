<?php
/**
 * Tek eğitmen — profil + verdiği kurslar.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

while ( have_posts() ) : the_post();
	$tid  = get_the_ID();
	$nm   = get_the_title();
	$role = wp_trim_words( get_the_excerpt(), 8, '' );
	$ini  = '';
	foreach ( preg_split( '/\s+/', trim( $nm ) ) as $w ) { $ini .= mb_substr( $w, 0, 1 ); }
	$ini = mb_strtoupper( mb_substr( $ini, 0, 2 ) );

	$my_courses = new WP_Query( array(
		'post_type'      => 'courses',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'meta_query'     => array( array( 'key' => '_bireysel_course_teachers', 'value' => $tid ) ),
	) );
	?>
	<section class="course-hero">
		<div class="container">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><a href="<?php echo esc_url( function_exists( 'bireysel_page_link' ) ? bireysel_page_link( 'egitmenler' ) : home_url( '/egitmenler/' ) ); ?>"><?php esc_html_e( 'Eğitmenler', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php echo esc_html( $nm ); ?></span></nav>
			<div class="tprofile">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium', array( 'class' => 'tprofile__avatar', 'style' => 'object-fit:cover' ) ); ?>
				<?php else : ?>
					<div class="tprofile__avatar" style="background:linear-gradient(135deg,#2BB4F2,#0F1B2D)"><?php echo esc_html( $ini ); ?></div>
				<?php endif; ?>
				<div>
					<h1><?php echo esc_html( $nm ); ?></h1>
					<?php if ( $role ) : ?><div class="role"><?php echo esc_html( $role ); ?></div><?php endif; ?>
					<?php
					$bio = bireysel_strip_legacy_shortcodes( get_the_content() );
					$bio = trim( wp_strip_all_tags( $bio ) );
					if ( $bio ) : ?>
						<p class="tprofile__bio"><?php echo esc_html( wp_trim_words( $bio, 45 ) ); ?></p>
					<?php endif; ?>
					<div class="tprofile__stats">
						<div><b><?php echo (int) $my_courses->found_posts; ?></b><span><?php esc_html_e( 'Eğitim', 'bireysel' ); ?></span></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $my_courses->have_posts() ) : ?>
		<section class="section">
			<div class="container">
				<div class="section__head"><span class="section__eyebrow"><?php esc_html_e( 'Eğitimler', 'bireysel' ); ?></span><h2 class="section__title"><?php printf( esc_html__( '%s Eğitimleri', 'bireysel' ), esc_html( $nm ) ); ?></h2></div>
				<div class="grid-cards grid-cards--3">
					<?php while ( $my_courses->have_posts() ) : $my_courses->the_post(); get_template_part( 'template-parts/course-card' ); endwhile; ?>
				</div>
			</div>
		</section>
	<?php endif; wp_reset_postdata(); ?>

<?php endwhile;

get_footer();
