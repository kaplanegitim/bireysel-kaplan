<?php
/**
 * Anti-spam / bot sipariş koruması — checkout sertleştirme.
 *
 * Üç katman:
 *   1) Honeypot       — bot'un dolduracağı gizli alan; doluysa reddet.
 *   2) Time-trap      — form açılışı ile gönderim arası < N sn ise reddet (imzalı zaman damgası).
 *   3) IP rate-limit  — aynı IP'den belirli pencerede N sipariş denemesinden fazlasını engelle.
 *   (+) Cloudflare Turnstile — wp-config'de anahtarlar tanımlıysa görünmez CAPTCHA doğrulaması.
 *
 * Classic checkout (woocommerce_checkout_process) + Store API (REST) kapsanır.
 * Anahtarları wp-config.php içine ekleyerek Turnstile'ı açabilirsiniz:
 *   define( 'BIREYSEL_TURNSTILE_SITEKEY', '...' );
 *   define( 'BIREYSEL_TURNSTILE_SECRET',  '...' );
 *
 * @package bireysel
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Ayarlar (filtrelerle özelleştirilebilir)
 * ---------------------------------------------------------------------- */

/** Form gönderimi için minimum süre (saniye). Bundan hızlı = bot. */
function bireysel_antispam_min_seconds() {
	return (int) apply_filters( 'bireysel_antispam_min_seconds', 4 );
}

/** Rate-limit: pencere (saniye) ve bu pencerede izin verilen azami deneme. */
function bireysel_antispam_rate() {
	return apply_filters( 'bireysel_antispam_rate', array(
		'window' => 10 * MINUTE_IN_SECONDS,
		'max'    => 4,
	) );
}

/** Honeypot alan adı (masum görünmeli ki bot doldursun). */
function bireysel_antispam_hp_field() {
	return 'bireysel_url_confirm';
}

/* -------------------------------------------------------------------------
 * Gerçek istemci IP'si (Cloudflare / proxy farkında)
 * ---------------------------------------------------------------------- */
function bireysel_antispam_ip() {
	$candidates = array();

	if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		$candidates[] = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}
	if ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
		$candidates[] = trim( $parts[0] );
	}
	if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
		$candidates[] = $_SERVER['REMOTE_ADDR'];
	}

	foreach ( $candidates as $ip ) {
		$ip = filter_var( trim( $ip ), FILTER_VALIDATE_IP );
		if ( $ip ) {
			return $ip;
		}
	}
	return '0.0.0.0';
}

/* -------------------------------------------------------------------------
 * 1) Time-trap — imzalı zaman damgası
 *    (bot eski bir zamanı uyduramaz; HMAC anahtarı WP salt'tan)
 * ---------------------------------------------------------------------- */
function bireysel_antispam_token() {
	$t   = time();
	$sig = hash_hmac( 'sha256', (string) $t, wp_salt( 'auth' ) );
	return $t . '.' . $sig;
}

function bireysel_antispam_token_age( $token ) {
	if ( ! is_string( $token ) || strpos( $token, '.' ) === false ) {
		return false;
	}
	list( $t, $sig ) = explode( '.', $token, 2 );
	$expected = hash_hmac( 'sha256', (string) (int) $t, wp_salt( 'auth' ) );
	if ( ! hash_equals( $expected, $sig ) ) {
		return false; // İmza tutmuyor → kurcalanmış / sahte.
	}
	return time() - (int) $t;
}

/* -------------------------------------------------------------------------
 * Checkout'a gizli alanları bas (honeypot + time-trap)
 * ---------------------------------------------------------------------- */
function bireysel_antispam_render_fields() {
	$hp = bireysel_antispam_hp_field();
	?>
	<div aria-hidden="true" style="position:absolute!important;left:-9999px!important;top:-9999px!important;height:0;width:0;overflow:hidden;">
		<label><?php esc_html_e( 'Bu alanı boş bırakın', 'bireysel' ); ?>
			<input type="text" name="<?php echo esc_attr( $hp ); ?>" tabindex="-1" autocomplete="off" value="">
		</label>
	</div>
	<input type="hidden" name="bireysel_ts" value="<?php echo esc_attr( bireysel_antispam_token() ); ?>">
	<?php
}
add_action( 'woocommerce_after_order_notes', 'bireysel_antispam_render_fields' );
// Order notes alanı kapalıysa yedek konum:
add_action( 'woocommerce_review_order_before_submit', 'bireysel_antispam_render_fields' );

/* -------------------------------------------------------------------------
 * Rate-limit sayacı (transient)
 * ---------------------------------------------------------------------- */
function bireysel_antispam_rate_exceeded() {
	$rate = bireysel_antispam_rate();
	$key  = 'bireysel_oc_' . md5( bireysel_antispam_ip() );
	$hits = (int) get_transient( $key );

	if ( $hits >= $rate['max'] ) {
		return true;
	}
	set_transient( $key, $hits + 1, $rate['window'] );
	return false;
}

/* -------------------------------------------------------------------------
 * Cloudflare Turnstile (opsiyonel — anahtarlar tanımlıysa)
 * ---------------------------------------------------------------------- */
function bireysel_antispam_turnstile_on() {
	return defined( 'BIREYSEL_TURNSTILE_SITEKEY' ) && defined( 'BIREYSEL_TURNSTILE_SECRET' )
		&& BIREYSEL_TURNSTILE_SITEKEY && BIREYSEL_TURNSTILE_SECRET;
}

function bireysel_antispam_turnstile_assets() {
	if ( ! bireysel_antispam_turnstile_on() || ! function_exists( 'is_checkout' ) || ! is_checkout() ) {
		return;
	}
	wp_enqueue_script(
		'cf-turnstile',
		'https://challenges.cloudflare.com/turnstile/v0/api.js',
		array(),
		null,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'bireysel_antispam_turnstile_assets' );

function bireysel_antispam_turnstile_widget() {
	if ( ! bireysel_antispam_turnstile_on() ) {
		return;
	}
	printf(
		'<div class="cf-turnstile" data-sitekey="%s" data-theme="auto" style="margin:12px 0;"></div>',
		esc_attr( BIREYSEL_TURNSTILE_SITEKEY )
	);
}
add_action( 'woocommerce_review_order_before_submit', 'bireysel_antispam_turnstile_widget' );

function bireysel_antispam_turnstile_verify( $token ) {
	if ( ! $token ) {
		return false;
	}
	$resp = wp_remote_post( 'https://challenges.cloudflare.com/turnstile/v0/siteverify', array(
		'timeout' => 5,
		'body'    => array(
			'secret'   => BIREYSEL_TURNSTILE_SECRET,
			'response' => $token,
			'remoteip' => bireysel_antispam_ip(),
		),
	) );

	if ( is_wp_error( $resp ) ) {
		// Cloudflare'a ulaşılamadıysa müşteriyi mağdur etme — geçir.
		return true;
	}
	$data = json_decode( wp_remote_retrieve_body( $resp ), true );
	return ! empty( $data['success'] );
}

/* -------------------------------------------------------------------------
 * Classic checkout doğrulaması
 * ---------------------------------------------------------------------- */
function bireysel_antispam_validate_classic() {
	// 1) Honeypot
	$hp = bireysel_antispam_hp_field();
	if ( ! empty( $_POST[ $hp ] ) ) {
		bireysel_antispam_block( 'honeypot' );
		return;
	}

	// 2) Time-trap
	$token = isset( $_POST['bireysel_ts'] ) ? wp_unslash( $_POST['bireysel_ts'] ) : '';
	$age   = bireysel_antispam_token_age( $token );
	if ( $age === false || $age < bireysel_antispam_min_seconds() ) {
		bireysel_antispam_block( 'timetrap' );
		return;
	}

	// 3) Rate-limit
	if ( bireysel_antispam_rate_exceeded() ) {
		bireysel_antispam_block( 'ratelimit' );
		return;
	}

	// 4) Turnstile
	if ( bireysel_antispam_turnstile_on() ) {
		$tok = isset( $_POST['cf-turnstile-response'] ) ? wp_unslash( $_POST['cf-turnstile-response'] ) : '';
		if ( ! bireysel_antispam_turnstile_verify( $tok ) ) {
			bireysel_antispam_block( 'turnstile' );
			return;
		}
	}
}
add_action( 'woocommerce_checkout_process', 'bireysel_antispam_validate_classic' );

/** Müşteriye nötr hata göster, logla, siparişi durdur. */
function bireysel_antispam_block( $reason ) {
	if ( function_exists( 'wc_get_logger' ) ) {
		wc_get_logger()->warning(
			sprintf( 'Anti-spam blok [%s] IP=%s UA=%s', $reason, bireysel_antispam_ip(), isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 120 ) : '' ),
			array( 'source' => 'bireysel-antispam' )
		);
	}
	wc_add_notice(
		__( 'Siparişiniz işlenemedi. Lütfen sayfayı yenileyip tekrar deneyin.', 'bireysel' ),
		'error'
	);
}

/* -------------------------------------------------------------------------
 * Store API (blok checkout / REST) için rate-limit + Turnstile
 *    Bot çoğu zaman /wc/store/v1/checkout endpoint'ini vurur.
 * ---------------------------------------------------------------------- */
function bireysel_antispam_validate_store_api() {
	$exception = '\Automattic\WooCommerce\StoreApi\Exceptions\RouteException';
	if ( ! class_exists( $exception ) ) {
		return;
	}

	if ( bireysel_antispam_rate_exceeded() ) {
		bireysel_antispam_block_store_api( $exception, 'ratelimit' );
	}

	if ( bireysel_antispam_turnstile_on() ) {
		$tok = isset( $_POST['cf-turnstile-response'] ) ? wp_unslash( $_POST['cf-turnstile-response'] ) : '';
		if ( ! bireysel_antispam_turnstile_verify( $tok ) ) {
			bireysel_antispam_block_store_api( $exception, 'turnstile' );
		}
	}
}
add_action( 'woocommerce_store_api_checkout_update_order_from_request', 'bireysel_antispam_validate_store_api', 5 );

function bireysel_antispam_block_store_api( $exception, $reason ) {
	if ( function_exists( 'wc_get_logger' ) ) {
		wc_get_logger()->warning(
			sprintf( 'Anti-spam blok (Store API) [%s] IP=%s', $reason, bireysel_antispam_ip() ),
			array( 'source' => 'bireysel-antispam' )
		);
	}
	throw new $exception(
		'bireysel_antispam_blocked',
		__( 'Siparişiniz işlenemedi. Lütfen tekrar deneyin.', 'bireysel' ),
		429
	);
}
