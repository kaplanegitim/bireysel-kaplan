<?php
/**
 * Sepet toplamları — bireysel .summary-card (Sipariş Özeti).
 * WC core cart/cart-totals.php temel alındı; tablo yerine .summary-row div'leri.
 *
 * @package bireysel
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="cart_totals summary-card <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h3><?php esc_html_e( 'Sipariş Özeti', 'bireysel' ); ?></h3>

	<div class="summary-row cart-subtotal">
		<span><?php esc_html_e( 'Ara Toplam', 'bireysel' ); ?></span>
		<span><?php wc_cart_totals_subtotal_html(); ?></span>
	</div>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<div class="summary-row coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
			<span><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php
	// Kargo — div tabanlı (summary-card içinde tablo/tr olmadan). Default cart-shipping.php tr ürettiği için inline.
	if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) :
		do_action( 'woocommerce_cart_totals_before_shipping' );
		$packages       = WC()->shipping()->get_packages();
		$chosen_methods = WC()->session->get( 'chosen_shipping_methods', array() );
		foreach ( $packages as $i => $package ) :
			$available = $package['rates'];
			$chosen    = isset( $chosen_methods[ $i ] ) ? $chosen_methods[ $i ] : '';
			?>
			<div class="summary-row shipping">
				<span><?php esc_html_e( 'Gönderim', 'bireysel' ); ?></span>
				<span>
					<?php if ( $available ) : ?>
						<ul class="woocommerce-shipping-methods">
							<?php foreach ( $available as $method ) : ?>
								<li>
									<?php
									if ( 1 < count( $available ) ) {
										printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $i, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen, false ) ); // phpcs:ignore
									} else {
										printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" />', $i, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ) ); // phpcs:ignore
									}
									printf( '<label for="shipping_method_%1$d_%2$s">%3$s</label>', $i, esc_attr( sanitize_title( $method->id ) ), wc_cart_totals_shipping_method_label( $method ) ); // phpcs:ignore
									?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php else : ?>
						<span class="muted"><?php esc_html_e( 'Adres girince güncellenir.', 'bireysel' ); ?></span>
					<?php endif; ?>
				</span>
			</div>
			<?php
		endforeach;
		do_action( 'woocommerce_cart_totals_after_shipping' );
	endif;
	?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<div class="summary-row fee">
			<span><?php echo esc_html( $fee->name ); ?></span>
			<span><?php wc_cart_totals_fee_html( $fee ); ?></span>
		</div>
	<?php endforeach; ?>

	<?php
	if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
		$taxable_address = WC()->customer->get_taxable_address();
		$estimated_text  = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping()
			? sprintf( ' <small>' . esc_html__( '(%s için tahmini)', 'bireysel' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] )
			: '';
		if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
			foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : ?>
				<div class="summary-row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<span><?php echo esc_html( $tax->label ) . wp_kses_post( $estimated_text ); ?></span>
					<span><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
				</div>
			<?php endforeach;
		} else { ?>
			<div class="summary-row tax-total">
				<span><?php echo esc_html( WC()->countries->tax_or_vat() ) . wp_kses_post( $estimated_text ); ?></span>
				<span><?php wc_cart_totals_taxes_total_html(); ?></span>
			</div>
		<?php }
	}
	?>

	<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

	<div class="summary-row total order-total">
		<span><?php esc_html_e( 'Toplam', 'bireysel' ); ?></span>
		<span><?php wc_cart_totals_order_total_html(); ?></span>
	</div>

	<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<p class="summary-secure"><svg class="ico"><use href="#i-shield"/></svg> <?php esc_html_e( 'iyzico ile güvenli ödeme', 'bireysel' ); ?></p>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
