<?php
/**
 * Tek kurs (nd-learning "courses").
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

while ( have_posts() ) : the_post();
	$cid        = get_the_ID();
	$price      = get_post_meta( $cid, 'nd_learning_meta_box_price', true );
	$teacher_ids   = function_exists( 'bireysel_course_teacher_ids' ) ? bireysel_course_teacher_ids( $cid ) : array_filter( array( (int) get_post_meta( $cid, 'nd_learning_meta_box_teacher', true ) ) );
	$teacher_id    = $teacher_ids ? $teacher_ids[0] : 0; // birincil
	$teacher       = $teacher_id ? get_the_title( $teacher_id ) : '';
	$teacher_names = array_map( 'get_the_title', $teacher_ids );
	$avail      = get_post_meta( $cid, 'nd_learning_meta_box_max_availability', true );
	$docs_raw   = get_post_meta( $cid, 'nd_learning_meta_box_docs_courses', true );
	$doc_slugs  = $docs_raw ? array_filter( array_map( 'trim', explode( ',', $docs_raw ) ) ) : array();
	$lessons    = count( $doc_slugs );
	$dur_terms  = wp_get_post_terms( $cid, 'duration-course', array( 'fields' => 'names' ) );
	$duration   = ! empty( $dur_terms ) ? $dur_terms[0] : '';
	$courses_url = function_exists( 'bireysel_page_link' ) ? bireysel_page_link( 'kurslar' ) : home_url( '/kurslar/' );

	// Müfredat: doküman slug'larından başlık çöz
	$lesson_titles = array();
	if ( $doc_slugs ) {
		$docs = get_posts( array(
			'post_type'      => 'nd_learning_docs',
			'post_name__in'  => $doc_slugs,
			'posts_per_page' => -1,
			'orderby'        => 'post_name__in',
		) );
		foreach ( $docs as $d ) { $lesson_titles[] = get_the_title( $d ); }
	}
	if ( empty( $lesson_titles ) ) {
		foreach ( $doc_slugs as $s ) { $lesson_titles[] = ucwords( str_replace( array( '-', '_' ), ' ', $s ) ); }
	}

	$tini = '';
	if ( $teacher ) { foreach ( preg_split( '/\s+/', trim( $teacher ) ) as $w ) { $tini .= mb_substr( $w, 0, 1 ); } $tini = mb_strtoupper( mb_substr( $tini, 0, 2 ) ); }
	?>

	<section class="course-hero">
		<div class="container">
			<nav class="breadcrumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Ana Sayfa', 'bireysel' ); ?></a><span class="sep">/</span><a href="<?php echo esc_url( $courses_url ); ?>"><?php esc_html_e( 'Kurslar', 'bireysel' ); ?></a><span class="sep">/</span><span aria-current="page"><?php the_title(); ?></span></nav>
			<h1><?php the_title(); ?></h1>
			<div class="course-hero__meta">
				<?php if ( $lessons ) : ?><span><svg class="ico"><use href="#i-play"/></svg> <?php printf( esc_html__( '%d ders', 'bireysel' ), $lessons ); ?></span><?php endif; ?>
				<?php if ( $duration ) : ?><span><svg class="ico"><use href="#i-clock"/></svg> <?php echo esc_html( $duration ); ?></span><?php endif; ?>
				<?php if ( $avail ) : ?><span><svg class="ico"><use href="#i-users"/></svg> <?php printf( esc_html__( '%s kontenjan', 'bireysel' ), esc_html( $avail ) ); ?></span><?php endif; ?>
				<?php if ( $teacher_names ) : ?><span><svg class="ico"><use href="#i-cap"/></svg> <?php printf( esc_html( _n( 'Eğitmen: %s', 'Eğitmenler: %s', count( $teacher_names ), 'bireysel' ) ), esc_html( implode( ', ', $teacher_names ) ) ); ?></span><?php endif; ?>
			</div>
		</div>
	</section>

	<section class="section" style="padding-block:2.5rem">
		<div class="container course-layout">

			<div class="course-main">
				<?php
				// İçerik WPBakery ile yazılmış; kayıtlı olmayan [vc_*]/[nd_*] tag'lerini görüntüden temizle.
				$raw_content = get_the_content();
				$clean = trim( bireysel_strip_legacy_shortcodes( $raw_content ) );
				if ( $clean ) : ?>
					<div class="course-block">
						<h2><?php esc_html_e( 'Eğitim Hakkında', 'bireysel' ); ?></h2>
						<div class="entry-content"><?php echo wpautop( wp_kses_post( $clean ) ); ?></div>
					</div>
				<?php endif; ?>

				<?php if ( $lesson_titles ) : ?>
					<div class="course-block">
						<h2><?php esc_html_e( 'Program', 'bireysel' ); ?></h2>
						<div class="curriculum">
							<div class="curr-item is-open">
								<button class="curr-head" type="button"><svg class="ico" style="color:var(--c-primary-700)"><use href="#i-play"/></svg> <?php esc_html_e( 'Ders İçeriği', 'bireysel' ); ?> <span class="meta"><?php printf( esc_html__( '%d başlık', 'bireysel' ), $lessons ); ?></span><svg class="ico chev"><use href="#i-chev"/></svg></button>
								<div class="curr-body"><ul>
									<?php foreach ( $lesson_titles as $lt ) : ?>
										<li><svg class="ico"><use href="#i-play"/></svg> <?php echo esc_html( $lt ); ?></li>
									<?php endforeach; ?>
								</ul></div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( $teacher_ids ) : ?>
					<div class="course-block">
						<h2><?php echo esc_html( _n( 'Eğitmen', 'Eğitmenler', count( $teacher_ids ), 'bireysel' ) ); ?></h2>
						<?php foreach ( $teacher_ids as $ti ) :
							$tn = get_the_title( $ti );
							$tin = '';
							foreach ( preg_split( '/\s+/', trim( $tn ) ) as $w ) { $tin .= mb_substr( $w, 0, 1 ); }
							$tin = mb_strtoupper( mb_substr( $tin, 0, 2 ) );
							?>
							<div class="instructor-box" style="margin-bottom:12px">
								<?php if ( has_post_thumbnail( $ti ) ) : ?>
									<?php echo get_the_post_thumbnail( $ti, 'thumbnail', array( 'class' => 'instructor-box__avatar', 'style' => 'object-fit:cover' ) ); ?>
								<?php else : ?>
									<div class="instructor-box__avatar"><?php echo esc_html( $tin ); ?></div>
								<?php endif; ?>
								<div>
									<h4><a href="<?php echo esc_url( get_permalink( $ti ) ); ?>"><?php echo esc_html( $tn ); ?></a></h4>
									<?php $tbio = trim( wp_strip_all_tags( get_the_excerpt( $ti ) ) ); ?>
									<?php if ( $tbio ) : ?><p style="margin:0"><?php echo esc_html( wp_trim_words( $tbio, 40 ) ); ?></p><?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="course-block" id="kayit">
					<div class="course-register">
						<h2><?php esc_html_e( 'Kayıt / Bilgi Talebi', 'bireysel' ); ?></h2>
						<form class="bireysel-form">
							<input type="hidden" name="form_type" value="register">
							<input type="hidden" name="source" value="<?php echo esc_attr( get_the_title() ); ?>">
							<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
							<div class="field-row">
								<div class="field"><label><?php esc_html_e( 'Ad Soyad', 'bireysel' ); ?></label><input type="text" name="name" required></div>
								<div class="field"><label><?php esc_html_e( 'E-posta', 'bireysel' ); ?></label><input type="email" name="email" required></div>
							</div>
							<div class="field"><label><?php esc_html_e( 'Telefon', 'bireysel' ); ?></label><input type="tel" name="phone"></div>
							<div class="field"><label><?php esc_html_e( 'Notunuz', 'bireysel' ); ?></label><textarea name="message" rows="3"></textarea></div>
							<button type="submit" class="btn btn--primary"><svg class="ico"><use href="#i-cart"/></svg> <?php esc_html_e( 'Kayıt Talebi Gönder', 'bireysel' ); ?></button>
						</form>
					</div>
				</div>
			</div>

			<aside>
				<div class="enroll-card">
					<div class="enroll-card__media">
						<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large' ); else : ?>
							<svg class="ico"><use href="#i-play"/></svg>
						<?php endif; ?>
					</div>
					<div class="enroll-card__body">
						<?php if ( $price ) : ?><div class="enroll-card__price"><span class="now"><?php echo esc_html( $price ); ?></span></div><?php endif; ?>
						<a href="#kayit" class="btn btn--primary btn--block"><svg class="ico"><use href="#i-cart"/></svg> <?php esc_html_e( 'Kursa Katıl', 'bireysel' ); ?></a>
						<ul class="enroll-card__list">
							<?php if ( $lessons ) : ?><li><svg class="ico"><use href="#i-play"/></svg> <?php printf( esc_html__( '%d ders', 'bireysel' ), $lessons ); ?></li><?php endif; ?>
							<?php if ( $duration ) : ?><li><svg class="ico"><use href="#i-clock"/></svg> <?php echo esc_html( $duration ); ?></li><?php endif; ?>
							<?php if ( $avail ) : ?><li><svg class="ico"><use href="#i-users"/></svg> <?php printf( esc_html__( '%s kişilik kontenjan', 'bireysel' ), esc_html( $avail ) ); ?></li><?php endif; ?>
							<li><svg class="ico"><use href="#i-cert"/></svg> <?php esc_html_e( 'Katılım sertifikası', 'bireysel' ); ?></li>
						</ul>
					</div>
				</div>
			</aside>
		</div>
	</section>

<?php endwhile;

get_footer();
