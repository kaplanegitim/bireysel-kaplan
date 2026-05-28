<?php
/**
 * Eğitmen kartı. Loop içinde global $post ile çağrılır.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

$nm  = get_the_title();
$ini = '';
foreach ( preg_split( '/\s+/', trim( $nm ) ) as $w ) { $ini .= mb_substr( $w, 0, 1 ); }
$ini = mb_strtoupper( mb_substr( $ini, 0, 2 ) );
$grads = array( 'linear-gradient(135deg,#5AC8FB,#129BD8)', 'linear-gradient(135deg,#2BB4F2,#0F1B2D)', 'linear-gradient(135deg,#129BD8,#15263d)' );
$grad  = $grads[ get_the_ID() % 3 ];
$role  = wp_trim_words( get_the_excerpt(), 6, '' );
?>
<div class="teacher">
	<a href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'thumbnail', array( 'class' => 'teacher__photo', 'style' => 'object-fit:cover' ) ); ?>
		<?php else : ?>
			<div class="teacher__photo" style="background:<?php echo esc_attr( $grad ); ?>;display:grid;place-items:center;color:#fff;font-family:var(--font-heading);font-weight:800;font-size:2.2rem"><?php echo esc_html( $ini ); ?></div>
		<?php endif; ?>
	</a>
	<h4><a href="<?php the_permalink(); ?>"><?php echo esc_html( $nm ); ?></a></h4>
	<?php if ( $role ) : ?><span><?php echo esc_html( $role ); ?></span><?php endif; ?>
</div>
