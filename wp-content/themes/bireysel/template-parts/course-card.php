<?php
/**
 * Kurs kartı (nd-learning "courses"). Loop içinde global $post ile çağrılır.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$cid       = get_the_ID();
$price     = get_post_meta( $cid, 'nd_learning_meta_box_price', true );
$teacher_id = get_post_meta( $cid, 'nd_learning_meta_box_teacher', true );
$teacher   = $teacher_id ? get_the_title( $teacher_id ) : '';
$docs      = get_post_meta( $cid, 'nd_learning_meta_box_docs_courses', true );
$lessons   = $docs ? count( array_filter( explode( ',', $docs ) ) ) : 0;
$dur_terms = wp_get_post_terms( $cid, 'duration-course', array( 'fields' => 'names' ) );
$duration  = ! empty( $dur_terms ) ? $dur_terms[0] : '';
$cat_terms = wp_get_post_terms( $cid, 'category-course', array( 'fields' => 'names' ) );
$cat       = ! empty( $cat_terms ) ? $cat_terms[0] : __( 'Eğitim', 'bireysel' );

// Eğitmen baş harfleri (avatar)
$initials = '';
if ( $teacher ) {
	foreach ( preg_split( '/\s+/', trim( $teacher ) ) as $w ) { $initials .= mb_substr( $w, 0, 1 ); }
	$initials = mb_strtoupper( mb_substr( $initials, 0, 2 ) );
}
$grads = array( 'linear-gradient(135deg,#0F1B2D,#129BD8)', 'linear-gradient(135deg,#129BD8,#5AC8FB)', 'linear-gradient(135deg,#15263d,#2BB4F2)' );
$grad  = $grads[ $cid % count( $grads ) ];
?>
<article class="card">
	<a href="<?php the_permalink(); ?>">
		<div class="card__media card__media--contain" style="aspect-ratio:16/9;<?php echo has_post_thumbnail() ? '' : 'background:' . esc_attr( $grad ) . ';display:grid;place-items:center'; ?>">
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large' ); else : ?>
				<svg class="ico" style="width:54px;height:54px;color:#fff;opacity:.9"><use href="#i-cap"/></svg>
			<?php endif; ?>
		</div>
	</a>
	<div class="card__body">
		<span class="card__cat"><?php echo esc_html( $cat ); ?></span>
		<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="course-meta">
			<?php if ( $lessons ) : ?><span><svg class="ico"><use href="#i-play"/></svg> <?php printf( esc_html__( '%d ders', 'bireysel' ), $lessons ); ?></span><?php endif; ?>
			<?php if ( $duration ) : ?><span><svg class="ico"><use href="#i-clock"/></svg> <?php echo esc_html( $duration ); ?></span><?php endif; ?>
		</div>
		<?php if ( $teacher ) : ?>
			<div class="course-author">
				<span style="width:30px;height:30px;border-radius:50%;background:var(--c-primary-50);color:var(--c-primary-700);display:grid;place-items:center;font-weight:800;font-size:.8rem"><?php echo esc_html( $initials ); ?></span>
				<span><?php echo esc_html( $teacher ); ?></span>
			</div>
		<?php endif; ?>
		<?php if ( $price ) : ?><div class="price"><span class="price__now"><?php echo esc_html( $price ); ?></span></div><?php endif; ?>
		<div class="card__foot"><a href="<?php the_permalink(); ?>" class="btn btn--primary btn--block"><?php esc_html_e( 'Kursa Katıl', 'bireysel' ); ?></a></div>
	</div>
</article>
