/* bireysel demo — hafif etkileşimler */
(function () {
  'use strict';

  // Sticky header gölgesi
  var header = document.querySelector('.header');
  if (header) {
    var onScroll = function () {
      header.classList.toggle('is-stuck', window.scrollY > 8);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Mobil menü
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('is-open');
    });
    nav.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') nav.classList.remove('is-open');
    });
  }

  // Hero slider
  var slider = document.querySelector('.hero-slider');
  if (slider) {
    var track = slider.querySelector('.hero-track');
    var slides = slider.querySelectorAll('.hero-slide');
    var dotsWrap = slider.querySelector('.hero-dots');
    var n = slides.length, i = 0, timer = null;

    for (var d = 0; d < n; d++) {
      var dot = document.createElement('button');
      dot.className = 'hero-dot' + (d === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', (d + 1) + '. slayt');
      dot.dataset.idx = d;
      dotsWrap.appendChild(dot);
    }
    var dots = dotsWrap.querySelectorAll('.hero-dot');

    var go = function (idx) {
      i = (idx + n) % n;
      track.style.transform = 'translateX(' + (-i * 100) + '%)';
      dots.forEach(function (el, k) { el.classList.toggle('is-active', k === i); });
    };
    var next = function () { go(i + 1); };
    var prev = function () { go(i - 1); };
    var play = function () { stop(); timer = setInterval(next, 6000); };
    var stop = function () { if (timer) clearInterval(timer); };

    var nextBtn = slider.querySelector('.hero-arrow--next');
    var prevBtn = slider.querySelector('.hero-arrow--prev');
    if (nextBtn) nextBtn.addEventListener('click', function () { next(); play(); });
    if (prevBtn) prevBtn.addEventListener('click', function () { prev(); play(); });
    dotsWrap.addEventListener('click', function (e) {
      if (e.target.dataset.idx !== undefined) { go(parseInt(e.target.dataset.idx, 10)); play(); }
    });
    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', play);
    play();
  }

  // "Sepete Ekle" demo — sayaç artır
  var countEl = document.querySelector('.iconbtn__count');
  var cart = countEl ? parseInt(countEl.textContent, 10) || 0 : 0;
  document.querySelectorAll('[data-add-cart]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      cart += 1;
      if (countEl) countEl.textContent = cart;
      var orig = btn.textContent;
      btn.textContent = 'Eklendi ✓';
      btn.disabled = true;
      setTimeout(function () { btn.textContent = orig; btn.disabled = false; }, 1200);
    });
  });

  // Ürün galerisi — thumbnail geçişi
  var gallery = document.querySelector('.pgallery');
  if (gallery) {
    var mainImg = gallery.querySelector('.pgallery__main img');
    gallery.querySelectorAll('.pgallery__thumbs button').forEach(function (t) {
      t.addEventListener('click', function () {
        var img = t.querySelector('img');
        if (mainImg && img) mainImg.src = img.src;
        gallery.querySelectorAll('.pgallery__thumbs button').forEach(function (x) { x.classList.remove('is-active'); });
        t.classList.add('is-active');
      });
    });
  }

  // Arama paneli (header arama ikonu)
  var searchToggle = document.querySelector('[data-search-toggle]');
  var searchPanel = document.querySelector('[data-search-panel]');
  if (searchToggle && searchPanel) {
    var openSearch = function () {
      searchPanel.classList.add('is-open');
      var inp = searchPanel.querySelector('input[type=search]');
      if (inp) { setTimeout(function () { inp.focus(); }, 50); }
    };
    var closeSearch = function () { searchPanel.classList.remove('is-open'); };
    searchToggle.addEventListener('click', function () {
      searchPanel.classList.contains('is-open') ? closeSearch() : openSearch();
    });
    searchPanel.addEventListener('click', function (e) {
      if (e.target === searchPanel || (e.target.closest && e.target.closest('[data-search-close]'))) { closeSearch(); }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && searchPanel.classList.contains('is-open')) { closeSearch(); }
    });
  }

  // Adet sayacı (qty stepper)
  document.querySelectorAll('.qty').forEach(function (q) {
    var input = q.querySelector('input');
    q.querySelectorAll('button').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var v = parseInt(input.value, 10) || 1;
        v += btn.dataset.step === '-' ? -1 : 1;
        var min = parseInt(input.min, 10) || 1;
        input.value = Math.max(min, v);
        // WC sepet güncelleme butonunu aktive et / değişikliği bildir
        input.dispatchEvent(new Event('change', { bubbles: true }));
      });
    });
  });

  // Sepet sayfası: adet değişince otomatik güncelle (debounce'lu) — +/- sonrası "Sepeti Güncelle"ye basmaya gerek kalmasın
  var cartForm = document.querySelector('form.woocommerce-cart-form');
  if (cartForm) {
    var updateBtn = cartForm.querySelector('[name="update_cart"]');
    var cartTimer;
    cartForm.addEventListener('change', function (e) {
      var t = e.target;
      if (!t || !(t.classList.contains('qty') || (t.closest && t.closest('.qty')))) return;
      if (updateBtn) { updateBtn.disabled = false; updateBtn.removeAttribute('disabled'); }
      clearTimeout(cartTimer);
      cartTimer = setTimeout(function () {
        if (updateBtn) { updateBtn.click(); } // update_cart adıyla submit → WC günceller, sayfa yenilenir
      }, 650);
    });
  }

  // Varyasyon seçimi
  document.querySelectorAll('.variation__opts').forEach(function (grp) {
    grp.addEventListener('click', function (e) {
      if (e.target.tagName !== 'BUTTON') return;
      grp.querySelectorAll('button').forEach(function (b) { b.classList.remove('is-active'); });
      e.target.classList.add('is-active');
    });
  });

  // Ödeme yöntemi seçimi
  document.querySelectorAll('.pay-methods').forEach(function (grp) {
    grp.querySelectorAll('.pay-method').forEach(function (m) {
      m.addEventListener('click', function () {
        grp.querySelectorAll('.pay-method').forEach(function (x) { x.classList.remove('is-active'); });
        m.classList.add('is-active');
        var radio = m.querySelector('input[type=radio]');
        if (radio) radio.checked = true;
      });
    });
  });

  // Müfredat akordeon
  document.querySelectorAll('.curr-head').forEach(function (head) {
    head.addEventListener('click', function () {
      head.closest('.curr-item').classList.toggle('is-open');
    });
  });

  // Sekmeler (tabs)
  document.querySelectorAll('.tabs').forEach(function (tabs) {
    var btns = tabs.querySelectorAll('.tab-nav button');
    var panels = tabs.querySelectorAll('.tab-panel');
    btns.forEach(function (b, idx) {
      b.addEventListener('click', function () {
        btns.forEach(function (x) { x.classList.remove('is-active'); });
        panels.forEach(function (x) { x.classList.remove('is-active'); });
        b.classList.add('is-active');
        if (panels[idx]) panels[idx].classList.add('is-active');
      });
    });
  });

  // En Yakın Eğitim geri sayım
  var cd = document.querySelector('[data-deadline]');
  if (cd) {
    var target = new Date(cd.dataset.deadline).getTime();
    var els = {
      d: cd.querySelector('[data-cd="d"]'), h: cd.querySelector('[data-cd="h"]'),
      m: cd.querySelector('[data-cd="m"]'), s: cd.querySelector('[data-cd="s"]')
    };
    var pad = function (n) { return (n < 10 ? '0' : '') + n; };
    var tick = function () {
      var diff = Math.max(0, target - Date.now());
      var sec = Math.floor(diff / 1000);
      if (els.d) els.d.textContent = Math.floor(sec / 86400);
      if (els.h) els.h.textContent = pad(Math.floor(sec / 3600) % 24);
      if (els.m) els.m.textContent = pad(Math.floor(sec / 60) % 60);
      if (els.s) els.s.textContent = pad(sec % 60);
    };
    tick();
    setInterval(tick, 1000);
  }

  // AJAX form gönderimi (.bireysel-form)
  if (typeof bireyselForm !== 'undefined') {
    document.querySelectorAll('form.bireysel-form').forEach(function (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('[type="submit"]');
        var msg = form.querySelector('.form-msg');
        if (!msg) { msg = document.createElement('p'); msg.className = 'form-msg'; form.appendChild(msg); }
        var data = new FormData(form);
        data.append('action', 'bireysel_form');
        data.append('nonce', bireyselForm.nonce);
        var orig = btn ? btn.textContent : '';
        if (btn) { btn.disabled = true; btn.textContent = 'Gönderiliyor…'; }
        msg.className = 'form-msg';
        fetch(bireyselForm.ajax, { method: 'POST', body: data, credentials: 'same-origin' })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            msg.textContent = (res.data && res.data.message) ? res.data.message : (res.success ? 'Gönderildi.' : 'Hata.');
            msg.classList.add(res.success ? 'is-ok' : 'is-err');
            if (res.success) { form.reset(); }
          })
          .catch(function () { msg.textContent = 'Bağlantı hatası, tekrar deneyin.'; msg.classList.add('is-err'); })
          .finally(function () { if (btn) { btn.disabled = false; btn.textContent = orig; } });
      });
    });
  }

  // Hero istatistik sayaçları
  var counters = document.querySelectorAll('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        var el = en.target, target = parseInt(el.dataset.count, 10), cur = 0;
        var step = Math.max(1, Math.round(target / 40));
        var t = setInterval(function () {
          cur += step;
          if (cur >= target) { cur = target; clearInterval(t); }
          el.textContent = cur.toLocaleString('tr-TR');
        }, 28);
        io.unobserve(el);
      });
    }, { threshold: .5 });
    counters.forEach(function (c) { io.observe(c); });
  }
})();
