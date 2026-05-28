# bireysel — Canlı Deploy Runbook (TAM SİTE MİGRASYONU)

> ## ✅ DEPLOY TAMAMLANDI — 2026-05-27
>
> Site **canlıda yayında**: `https://bireysel.kaplanegitim.com/` · PHP 8.1 · tüm TR+EN sayfalar 200 · çoklu-eğitmen + favicon (Kaplan-2 Site Icon) + JSON-LD schema + tek-H1 doğrulandı · Permalinks 2× kaydedildi · 320 sipariş korundu (gap=0).
> Migrasyon: AIOWPM `.wpress` (64MB sunucu limiti **Unlimited Extension fork** ile aşıldı).
>
> Bu dosya artık **arşiv runbook**. Lansman sonrası senkron kuralı: **kod → File Manager dosya yükle; içerik → canlıdan AIOWPM import.**
>
> **Prod'da kalan manuel iş:**
> - [ ] iyzico **TEST siparişi** (tarayıcı + test kart — kritik, ödeme akışı doğrulanmadı)
> - [ ] iyzico premium plugin güncelle (1.0.6 → son sürüm, iyzico panelinden indir)
> - [ ] Cloudflare **Development Mode KAPAT** (tüm doğrulama bitince)
> - [x] GA4 aktif ve veri akıyor (Search Console'da bireysel için de doğrulandı — 2026-05-28)
> - [ ] Customizer → İletişim Bilgileri → "Form bildirim e-postası" = `bilgi@kaplanegitim.com` (şu an admin_email fallback'i, `cankaplan@gmail.com`'a düşüyor)
> - [ ] Opsiyonel: `/en/` → `/en/home-en/` 301 (Polylang front-page quirk; çalışan home'a iniyor)

Hedef: `bireysel.kaplanegitim.com` (Natro cPanel · LiteSpeed + Cloudflare).

**Strateji (2026-05-27 kesinleşti): TAM SİTE MİGRASYONU.** Local DB = prod klonu + tüm bu oturumun işi (EN içerik, çoklu-eğitmen, içerik temizliği, ürün scrub, SEO/sosyal/GA4/site-icon ayarları, menü/slider, WC 10.8 + eklenti güncellemeleri). **GAP = 0** (kanıt: prod ve local'de sipariş birebir aynı — 320: 268 hazırlanıyor / 33 ödeme bekliyor / 19 iptal; en son sipariş #2679 22 May). Yani local'i prod'a yazmak **sıfır veri kaybı** demek.

> Eski "sadece-dosya, DB taşıma" planı GEÇERSİZ — o kadar DB işi yapıldı ki canlıda elle yeniden kurmak pratik değil. Artık local kaynak-doğruluğu (source of truth).

Mekanizma: **All-in-One WP Migration (.wpress)** ile DB+dosyalar tek pakette taşınır; domain/path otomatik değişir.

---

## 0) LOCAL — EXPORT (hazır)
- Export hijyeni yapıldı: `woocommerce_sessions` truncate + transient temizliği (eski klon import'unda fatal veren serialized-session sorununu önler).
- Local admin → **All-in-One WP Migration → Export → Export to File** → `.wpress` indir.
- Tema zip yedeği ayrıca: `_DEPLOY_2026-05-27/bireysel-theme.zip` (acil/alternatif için).

## 1) PROD — YEDEK (rollback güvencesi) ⚠️ ATLAMA
1. cPanel → **Full Backup** indir VEYA prod local admin → AIOWPM **Export** → `.wpress` indir (mevcut canlı hali).
2. Ayrıca cPanel → phpMyAdmin → mevcut prod DB **Export** (SQL).
> Bu yedek olmadan migrasyona başlama. Sorun olursa tek geri dönüş bu.

## 2) PROD — CLOUDFLARE
- Cloudflare → **Development Mode AÇ** (deploy boyunca cache kapalı).

## 3) PROD — IMPORT
1. Prod local admin → **All-in-One WP Migration → Import → Import From File** → local `.wpress`'i yükle.
2. AIOWPM domain/path'i otomatik değiştirir (`localhost/bireysel-eski` → `bireysel.kaplanegitim.com`, local path → prod path). Serialized-veri güvenli (AIOWPM hallediyor).
3. Import bitince **"Permalinks'i 2× kaydet"** uyarısını uygula.
- **Import limiti:** AIOWPM ücretsiz sürüm sınırı (~300MB) `.wpress`'ten küçükse: ya unlimited extension yükle (`_REMOVED_BACKUP_2026-05-27/plugins/All-In-One-WP-Migration-With-Import-master`), ya da cPanel'den `upload_max_filesize`/`post_max_size` artır.
- **Alternatif (limit sorun olursa):** phpMyAdmin'den local DB SQL'ini import et + `bireysel` teması & güncel eklenti klasörlerini & `Kaplan-2.png`'yi File Manager ile yükle; sonra serialized-güvenli URL/path replace (WP-CLI `search-replace` veya Search-Replace-DB).

## 4) PROD — PERMALINKS FLUSH (kritik)
- **Ayarlar → Kalıcı Bağlantılar → Değişiklikleri Kaydet** (hiçbir şey değiştirmeden).
- Bu: CPT rewrite (kurslar/egitmenler) + **`/en/` front-page** kuralını (page_id) düzeltir. Local'de bu adım atlanınca `/en/` 301 veriyordu — prod'da MUTLAKA yap.

## 5) PROD — PHP 8'E GEÇİŞ
- cPanel → **Select PHP Version → 8.x** (8.0+). Artık güvenli: `create_function` blocker'ı (eski timetable/educationpack) tamamen kaldırıldı; güncel tema + eklentiler PHP 8.0.26'da local'de tertemiz çalıştı (kanıtlı).
- Hesap geneli ayar ise ana site (kaplan teması) PHP 8 uyumunu da önce doğrula.

## 6) PROD — DOĞRULAMA (PHP 8 altında)
- [ ] Ana sayfa + slider (3 slayt, hepsi koyu, tek H1)
- [ ] **Favicon** sekmede görünüyor (Site Icon = Kaplan-2)
- [ ] Mağaza → ürün → sepet → ödeme → **iyzico TEST siparişi** (KRİTİK)
- [ ] Kurslar + tek kurs (çoklu eğitmen görünüyor) + eğitmen sayfası kursları listeliyor
- [ ] **EN:** `/en/` (200, redirect değil) · `/en/courses/` · dil değiştirici · footer EN linkleri
- [ ] Kurs kayıt + iletişim formu e-postası geliyor mu (wp-mail-smtp)
- [ ] Hesabım / giriş · Mesafeli Satış Sözleşmesi · llms.txt (kök) · `<head>` JSON-LD schema
- [ ] Mobil görünüm + footer sosyal ikonları (URL girilenler)

## 7) PROD — GÜVENLİK & SON AYARLAR
1. **Admin şifresini DEĞİŞTİR:** migrasyon local şifresini taşır (`cankaplan_pu7w752v` / `lokal2026!.`) → güçlü bir şifre ata.
2. **iyzico'yu güncelle** (premium, 1.0.6 taşındı → iyzico panelinden son sürüm) + tekrar test siparişi.
3. `wp-config.php` (prod): `WP_DEBUG=false`, `define('DISALLOW_FILE_EDIT', true)`.
4. SMTP gönderimini test et (wp-mail-smtp ayarları taşındı).

## 8) PROD — ÖNBELLEK
- **LiteSpeed → Tümünü Boşalt.**
- **Cloudflare → Purge Everything** + Development Mode **KAPAT**.

---

## TAŞINAN (DB ile otomatik gelir, elle kurma GEREKMEZ)
Tema (bireysel) · 8 aktif eklenti (güncel) · tüm sayfalar/kurslar/eğitmenler/ürünler/siparişler/müşteriler · **EN içerik** (sayfa/kurs/eğitmen/slide/menü/taksonomi + Polylang string) · çoklu-eğitmen atamaları · slider (3 koyu slayt + buton URL'leri) · 2 menü (TR/EN primary) · **tüm Customizer ayarları** (GA4 ID, sosyal URL, site-icon, iletişim, başlıklar, güven şeridi) · WC sayfaları/ayarları · içerik temizliği (ürün scrub, çöp trash).

## TAŞINMAYAN / PROD'DA MANUEL
GA4'a veri akışı (tag taşındı ama doğrula) · iyzico güncel sürüm · admin şifresi · gerçek sosyal profil URL'leri (girdiysen taşınır) · DNS/Cloudflare (değişmez).

## ROLLBACK
Sorun → cPanel Full Backup'ı geri yükle VEYA prod'un import-öncesi `.wpress`'ini AIOWPM ile geri yükle + phpMyAdmin DB'yi geri al. PHP'yi eski sürüme çek.

---
**Aktif eklenti (8):** woocommerce 10.8 · iyzico-payment-module · kargo-takip-turkiye · polylang 3.8.4 · wp-mail-smtp · litespeed-cache · akismet · woo-order-export-lite. (+ pasif: wordpress-seo/GA/OG-tags — custom seo.php yerine geçti; all-in-one-wp-migration deploy aracı.)
