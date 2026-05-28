<?php
/**
 * İletişim formu (AJAX). bireysel form sistemi.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<form class="bireysel-form form-card">
	<h3><?php esc_html_e( 'Bize Yazın', 'bireysel' ); ?></h3>
	<input type="hidden" name="form_type" value="contact">
	<input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
	<div class="field-row">
		<div class="field"><label><?php esc_html_e( 'Ad Soyad', 'bireysel' ); ?></label><input type="text" name="name" required></div>
		<div class="field"><label><?php esc_html_e( 'E-posta', 'bireysel' ); ?></label><input type="email" name="email" required></div>
	</div>
	<div class="field-row">
		<div class="field"><label><?php esc_html_e( 'Telefon', 'bireysel' ); ?></label><input type="tel" name="phone"></div>
		<div class="field"><label><?php esc_html_e( 'Konu', 'bireysel' ); ?></label>
			<select name="subject">
				<option><?php esc_html_e( 'Genel', 'bireysel' ); ?></option>
				<option><?php esc_html_e( 'Sipariş', 'bireysel' ); ?></option>
				<option><?php esc_html_e( 'Online Eğitim', 'bireysel' ); ?></option>
				<option><?php esc_html_e( 'Kurumsal', 'bireysel' ); ?></option>
			</select>
		</div>
	</div>
	<div class="field"><label><?php esc_html_e( 'Mesajınız', 'bireysel' ); ?></label><textarea name="message" rows="5"></textarea></div>
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Gönder', 'bireysel' ); ?> <svg class="ico"><use href="#i-arrow"/></svg></button>
</form>
