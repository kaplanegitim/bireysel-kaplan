<?php
/**
 * Ödeme formu — bireysel demo tasarımı (.checkout-layout: sol müşteri detayları, sağ sipariş özeti).
 * WC core checkout/form-checkout.php temel alındı; tüm hook'lar korundu, sadece sarmalayıcı yapı değişti.
 *
 * @package bireysel
 */
defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

// Üye girişi gerekiyor ama kapalıysa
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'Ödeme yapabilmek için giriş yapmalısınız.', 'bireysel' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout checkout-layout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php esc_attr_e( 'Ödeme', 'bireysel' ); ?>">

	<div class="checkout-main">
		<?php if ( $checkout->get_checkout_fields() ) : ?>
			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
			<div id="customer_details">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		<?php endif; ?>
	</div>

	<aside class="checkout-side summary-card">
		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
		<h3 id="order_review_heading"><?php esc_html_e( 'Siparişiniz', 'bireysel' ); ?></h3>
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</aside>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
