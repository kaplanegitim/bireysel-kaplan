# bireysel-kaplan — Kurulum & Yeniden Yapılandırma

Bu dosya, repo'dan başlayarak çalışan bir `bireysel.kaplanegitim.com` kurulumu (lokal veya prod) elde etmek için gereken adımları listeler. Repo **sadece**:

- `wp-content/themes/bireysel/` (custom tema)
- `.htaccess`, `.user.ini`, `llms.txt`
- Doküman dosyaları (`README.md`, `SETUP.md`, `.gitignore`)

içerir. WordPress core, default tema'lar, 3rd-party plugin'ler, `wp-content/uploads/`, `wp-config.php`, tüm `*.sql` dump'ları ve `_*_BACKUP_*/` dizinleri repo'da **tutulmaz**.

> Bu dosya bir **dokümandır, backup değildir.** Plugin verisi / ayarları / uploads / DB için All-in-One WP Migration export'u veya hosting backup'ı kullanın.

---

## 1. Gereksinimler

| Bileşen | Versiyon |
|---|---|
| WordPress | **7.0** (`wp-includes/version.php`'deki `$wp_version`) |
| PHP | **8.1** (prod target) — `.user.ini` ile yükseltilmiş limit'ler (`memory_limit=768M`, `upload_max_filesize=1024M` — AIOWPM restore için) |
| MySQL / MariaDB | **5.5.5+** |
| WooCommerce'in çalışması için | PHP 8.1+ ve WP 6.6+ önerilir |

---

## 2. Kurulum adımları (sıfırdan)

### 2.1 Repo'yu klonla

```bash
git clone git@github.com:kaplanegitim/bireysel-kaplan.git bireysel
cd bireysel
```

### 2.2 WordPress core'unu indir

```bash
wp core download --version=7.0 --skip-content
```

`--skip-content` flag'i `wp-content/` klasörünü atlar.

### 2.3 `wp-config.php` oluştur

```bash
cp wp-config-sample.php wp-config.php
```

Sonra şu alanları doldur (lokal örnek altta — §6):

- `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `DB_HOST`
- Salts: https://api.wordpress.org/secret-key/1.1/salt/ (prod için **mutlaka** taze üret)
- `WP_CACHE`, memory limit'leri vb.

### 2.4 Veritabanını içe aktar

DB dump repo'da **yok**. Hosting backup'ından veya All-in-One WP Migration export'undan alın.

```bash
mysql -u root -p bireysel_local < bireysel-backup.sql

# Lokal ↔ prod URL migration:
wp search-replace 'https://bireysel.kaplanegitim.com' 'http://localhost/bireysel' --all-tables
```

### 2.5 Uploads'ları getir

`wp-content/uploads/` (~228 MB) repo'da yok. Prod'dan kopyala:

```bash
rsync -avz user@prod:/path/to/wp-content/uploads/ wp-content/uploads/
```

### 2.6 Plugin'leri kur

Aşağıdaki §3'teki tabloya bakarak hepsini kur. Toplu komut:

```bash
wp plugin install \
  akismet \
  all-in-one-wp-migration \
  google-analytics-for-wordpress \
  litespeed-cache \
  polylang \
  wonderm00ns-simple-facebook-open-graph-tags \
  woo-order-export-lite \
  woocommerce \
  wordpress-seo \
  wp-mail-smtp \
  --activate

# iyzico-payment-module — WordPress.org'da değil:
# https://www.iyzico.com adresinden veya https://www.kahvedigital.com fork'undan indirin
# wp-content/plugins/iyzico-payment-module/ altına çıkarın

# kargo-takip-turkiye — WordPress.org'da yok, GitHub'da arayın (slug ile)
# wp-content/plugins/kargo-takip-turkiye/ altına çıkarın
```

### 2.7 Tema'yı aktive et

```bash
wp theme activate bireysel
```

### 2.8 Permalink'leri flush et

```bash
wp rewrite flush
```

---

## 3. Plugin listesi (mevcut prod state)

| Slug | Plugin | Versiyon | Kaynak |
|---|---|---|---|
| `akismet` | Akismet Anti-spam: Spam Protection | 5.7 | https://wordpress.org/plugins/akismet/ |
| `all-in-one-wp-migration` | All-in-One WP Migration (ServMask) | 6.65 | https://wordpress.org/plugins/all-in-one-wp-migration/ |
| `google-analytics-for-wordpress` | Google Analytics for WordPress (MonsterInsights) | 9.5.2 | https://wordpress.org/plugins/google-analytics-for-wordpress/ |
| `iyzico-payment-module` | WooCommerce iyzico checkout form Payment Gateway | 1.0.6 | https://www.iyzico.com / https://www.kahvedigital.com — wordpress.org'da değil |
| `kargo-takip-turkiye` | Kargo Takip Türkiye | 0.2.4 | GitHub / üretici sitesi |
| `litespeed-cache` | LiteSpeed Cache | 7.8.1 | https://wordpress.org/plugins/litespeed-cache/ |
| `polylang` | Polylang (multilang) | 3.8.4 | https://wordpress.org/plugins/polylang/ |
| `wonderm00ns-simple-facebook-open-graph-tags` | Open Graph and Twitter Card Tags | 3.3.5 | https://wordpress.org/plugins/wonderm00ns-simple-facebook-open-graph-tags/ |
| `woo-order-export-lite` | Advanced Order Export For WooCommerce | 4.0.9 | https://wordpress.org/plugins/woo-order-export-lite/ |
| `woocommerce` | WooCommerce | 10.8.0 | https://wordpress.org/plugins/woocommerce/ |
| `wordpress-seo` | Yoast SEO | 25.1 | https://wordpress.org/plugins/wordpress-seo/ |
| `wp-mail-smtp` | WP Mail SMTP | 4.8.0 | https://wordpress.org/plugins/wp-mail-smtp/ |
| `hello.php` | Hello Dolly | WP core ile gelir (gerekirse: `wp plugin delete hello`) |

> **Listeyi güncel tutmak için:** plugin ekleyip/çıkardığınızda bu tabloyu güncelleyin. Otomatize etmek isterseniz `wp plugin list --format=csv > plugins.lock.csv`.

---

## 4. Tema'lar

| Slug | Tip | Versiyon | Repo'da? |
|---|---|---|---|
| `bireysel` | Custom | — (style.css'e bakın) | ✅ Evet |
| `twentytwentyfive` | WP default | 1.5 | ❌ Hayır — `wp theme install twentytwentyfive` |

---

## 5. mu-plugins

Boş — `wp-content/mu-plugins/` klasörü gitignore'da, manuel kurulum gereken bir şey yok.

---

## 6. `wp-config.php` örnek template (lokal)

```php
<?php
define( 'WP_CACHE', true );

define( 'DB_NAME',     'bireysel_local' );
define( 'DB_USER',     'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST',     'localhost' );
define( 'DB_CHARSET',  'utf8mb4' );
define( 'DB_COLLATE',  '' );

// Salts — prod için MUTLAKA https://api.wordpress.org/secret-key/1.1/salt/ ile üretin
define( 'AUTH_KEY',         '...' );
define( 'SECURE_AUTH_KEY',  '...' );
define( 'LOGGED_IN_KEY',    '...' );
define( 'NONCE_KEY',        '...' );
define( 'AUTH_SALT',        '...' );
define( 'SECURE_AUTH_SALT', '...' );
define( 'LOGGED_IN_SALT',   '...' );
define( 'NONCE_SALT',       '...' );

$table_prefix = 'wp_';

define( 'WP_DEBUG',           false );
define( 'WP_MEMORY_LIMIT',    '512M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}
require_once ABSPATH . 'wp-settings.php';
```

---

## 7. Backup stratejisi (öneri)

- **Kod:** bu repo (push & pull).
- **Veritabanı:** günlük `mysqldump` veya hosting otomatik backup. WooCommerce sipariş verisi kritik — saatlik snapshot düşünülmeli.
- **Uploads:** `rsync` veya hosting backup; her major content değişikliğinden sonra.
- **Bütünsel snapshot:** All-in-One WP Migration export (kurulu plugin). `wp-content/ai1wm-backups/`'a yazar; oradan indirip dış depolama (S3 / Drive). Repo bunları ignore'lar.

---

## 8. Yararlı komutlar

```bash
wp plugin list --status=active --format=table
wp theme list
wp core version

# Site URL'lerini topluca değiştir (DB taşıma sonrası)
wp search-replace 'https://eski.com' 'https://yeni.com' --all-tables --dry-run

# WooCommerce sipariş export (lite plugin ile)
wp wc order list --format=csv > orders.csv  # WC CLI gerekir
```

---

_Son güncelleme: bu dosya manuel tutulur. Plugin/tema değiştiğinde §3-4'ü güncelleyin._
