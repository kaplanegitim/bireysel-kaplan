<?php
/**
 * Arama formu — get_search_form() çağrılarında kullanılır.
 *
 * @package bireysel
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<svg class="ico" aria-hidden="true"><use href="#i-search"/></svg>
	<input type="search" class="search-form__input" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Ürün, kurs veya sayfa ara…', 'bireysel' ); ?>" aria-label="<?php esc_attr_e( 'Ara', 'bireysel' ); ?>" autocomplete="off">
	<button type="submit" class="btn btn--primary"><?php esc_html_e( 'Ara', 'bireysel' ); ?></button>
</form>
