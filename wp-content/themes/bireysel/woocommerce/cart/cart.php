<?php
/**
 * Sepet — bireysel demo tasarımı (.cart-layout / .cart-box / .summary-card).
 * WC core templates/cart/cart.php temel alındı; markup demo'ya uyarlandı,
 * tüm form mekaniği (qty, kaldır, kupon, güncelle, nonce, hook'lar) korundu.
 *
 * @package bireysel
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<div class="cart-layout">
	<form class="woocommerce-cart-form cart-box" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<?php do_action( 'woocommerce_before_cart_table' ); ?>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail', array( 'class' => 'cart-row__img' ) ), $cart_item, $cart_item_key );
				$item_data         = wc_get_formatted_cart_item_data( $cart_item );
				?>
				<div class="cart-row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
					<?php
					if ( $product_permalink ) {
						printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore
					} else {
						echo $thumbnail; // phpcs:ignore
					}
					?>
					<div class="cart-row__info">
						<div class="cart-row__title">
							<?php
							if ( $product_permalink ) {
								echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
							} else {
								echo wp_kses_post( $_product->get_name() );
							}
							do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );
							?>
						</div>
						<?php if ( $item_data ) : ?>
							<div class="cart-row__opt"><?php echo wp_kses_post( $item_data ); ?></div>
						<?php endif; ?>
						<?php
						if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Stokta yok, ön sipariş olarak eklenecek.', 'bireysel' ) . '</p>', $product_id ) );
						}
						?>
					</div>
					<span class="cart-row__price"><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore ?></span>
					<?php
					if ( $_product->is_sold_individually() ) {
						printf( '<span class="cart-row__qty1">1<input type="hidden" name="cart[%s][qty]" value="1" /></span>', esc_attr( $cart_item_key ) );
					} else {
						$min = $_product->get_min_purchase_quantity();
						$max = $_product->get_max_purchase_quantity();
						?>
						<div class="qty">
							<button type="button" data-step="-" aria-label="<?php esc_attr_e( 'Azalt', 'bireysel' ); ?>">&minus;</button>
							<input type="number" class="input-text qty text" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" min="<?php echo esc_attr( $min ); ?>" <?php echo $max > 0 ? 'max="' . esc_attr( $max ) . '"' : ''; ?> step="1" inputmode="numeric" aria-label="<?php esc_attr_e( 'Adet', 'bireysel' ); ?>">
							<button type="button" data-step="+" aria-label="<?php esc_attr_e( 'Artır', 'bireysel' ); ?>">+</button>
						</div>
						<?php
					}
					?>
					<span class="cart-row__total"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore ?></span>
					<?php
					echo apply_filters( // phpcs:ignore
						'woocommerce_cart_item_remove_link',
						sprintf(
							'<a href="%s" class="cart-row__remove remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg class="ico"><use href="#i-trash"/></svg></a>',
							esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
							esc_html__( 'Bu ürünü kaldır', 'bireysel' ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() )
						),
						$cart_item_key
					);
					?>
				</div>
				<?php
			}
		}
		do_action( 'woocommerce_cart_contents' );
		?>

		<div class="cart-box__foot">
			<?php if ( wc_coupons_enabled() ) : ?>
				<div class="coupon">
					<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'İndirim kodu', 'bireysel' ); ?>" />
					<button type="submit" class="btn btn--ghost" name="apply_coupon" value="<?php esc_attr_e( 'Uygula', 'bireysel' ); ?>"><?php esc_html_e( 'Uygula', 'bireysel' ); ?></button>
					<?php do_action( 'woocommerce_cart_coupon' ); ?>
				</div>
			<?php endif; ?>
			<button type="submit" class="btn btn--ghost" name="update_cart" value="<?php esc_attr_e( 'Sepeti Güncelle', 'bireysel' ); ?>"><?php esc_html_e( 'Sepeti Güncelle', 'bireysel' ); ?></button>
			<?php do_action( 'woocommerce_cart_actions' ); ?>
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
		</div>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		<?php do_action( 'woocommerce_after_cart_table' ); ?>
	</form>

	<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>
	<div class="cart-collaterals">
		<?php woocommerce_cart_totals(); ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
