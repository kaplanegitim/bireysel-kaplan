<?php
/**
 * Ürün kartı (WooCommerce). Loop içinde, global $product ile çağrılır.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

global $product;
if ( ! $product instanceof WC_Product ) {
	$product = wc_get_product( get_the_ID() );
}
if ( ! $product ) { return; }

$cats = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
$cat  = ! empty( $cats ) ? $cats[0] : '';
$ajax = ( $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() );
?>
<article <?php wc_product_class( 'card', $product ); ?>>
	<div class="card__media">
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="badge badge--sale"><?php esc_html_e( 'İndirim', 'bireysel' ); ?></span>
		<?php elseif ( $product->is_featured() ) : ?>
			<span class="badge badge--best"><?php esc_html_e( 'Öne Çıkan', 'bireysel' ); ?></span>
		<?php endif; ?>
		<a href="<?php the_permalink(); ?>"><?php echo $product->get_image( 'woocommerce_thumbnail' ); ?></a>
	</div>
	<div class="card__body">
		<?php if ( $cat ) : ?><span class="card__cat"><?php echo esc_html( $cat ); ?></span><?php endif; ?>
		<h3 class="card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<div class="price"><?php echo $product->get_price_html(); ?></div>
		<div class="card__foot">
			<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
			   data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
			   data-quantity="1" rel="nofollow"
			   class="btn btn--primary <?php echo $ajax ? 'add_to_cart_button ajax_add_to_cart' : ''; ?>">
				<svg class="ico"><use href="#<?php echo $ajax ? 'i-cart' : 'i-arrow'; ?>"/></svg> <?php echo esc_html( $product->add_to_cart_text() ); ?>
			</a>
		</div>
	</div>
</article>
