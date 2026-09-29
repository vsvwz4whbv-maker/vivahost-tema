/**
 * VivaHost Child Theme — vivahost.js v3
 * Header scroll · Mobile nav · Smooth scroll · Scroll animations · AJAX form
 */
(function () {
  'use strict';

  // ── Header scroll ────────────────────────────────────────────────────────
  function initHeaderScroll() {
    var header = document.getElementById('vivahost-header');
    if (!header) return;
    function onScroll() { header.classList.toggle('scrolled', window.scrollY > 20); }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Mobile nav ───────────────────────────────────────────────────────────
  function initMobileNav() {
    var toggle = document.getElementById('vh-nav-toggle');
    var nav    = document.getElementById('vh-mobile-nav');
    var iMenu  = document.getElementById('vh-icon-menu');
    var iClose = document.getElementById('vh-icon-close');
    if (!toggle || !nav) return;

    toggle.setAttribute('aria-controls', 'vh-mobile-nav');

    function open() {
      nav.classList.add('open');
      nav.setAttribute('aria-hidden', 'false');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Fechar menu');
      if (iMenu)  iMenu.style.display  = 'none';
      if (iClose) iClose.style.display = '';
      document.body.style.overflow = 'hidden';
    }
    function close() {
      nav.classList.remove('open');
      nav.setAttribute('aria-hidden', 'true');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Abrir menu');
      if (iMenu)  iMenu.style.display  = '';
      if (iClose) iClose.style.display = 'none';
      document.body.style.overflow = '';
    }

    toggle.addEventListener('click', function () {
      nav.classList.contains('open') ? close() : open();
    });
    nav.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', close); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    window.addEventListener('resize', function () { if (window.innerWidth > 768) close(); });
  }

  // ── Smooth scroll ────────────────────────────────────────────────────────
  function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var href = a.getAttribute('href');
        if (!href || href === '#') return;
        var target = document.querySelector(href);
        if (!target) return;
        e.preventDefault();
        var headerEl = document.getElementById('vivahost-header');
        var offset   = headerEl ? headerEl.offsetHeight + 8 : 76;
        var top      = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({ top: top, behavior: 'smooth' });
      });
    });
  }

  // ── Scroll animations ────────────────────────────────────────────────────
  function initAnimations() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      document.querySelectorAll('.anim-fade').forEach(function (el) {
        el.classList.add('anim-visible');
      });
      return;
    }

    if (!('IntersectionObserver' in window)) {
      document.querySelectorAll('.anim-fade').forEach(function (el) {
        el.classList.add('anim-visible');
      });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('anim-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    // Stagger siblings
    ['stat', 'process-step', 'service-card', 'imovel-card', 'review-card'].forEach(function (cls) {
      var groups = new Map();
      document.querySelectorAll('.' + cls + '.anim-fade').forEach(function (el) {
        var parent = el.parentElement;
        if (!groups.has(parent)) groups.set(parent, []);
        groups.get(parent).push(el);
      });
      groups.forEach(function (siblings) {
        siblings.forEach(function (el, i) {
          el.style.transitionDelay = (i * 0.09) + 's';
        });
      });
    });

    document.querySelectorAll('.anim-fade').forEach(function (el) { io.observe(el); });
  }

  // ── AJAX contact form ────────────────────────────────────────────────────
  function initForm() {
    var form    = document.getElementById('estimate-form');
    var success = document.getElementById('form-success');
    var errBox  = document.getElementById('form-error');
    var submitBtn = document.getElementById('form-submit-btn');
    if (!form) return;

    var cfg = window.VH || {};
    var jsTokenInput = document.getElementById('vh_js_token');
    var challenge = form.getAttribute('data-vh-challenge') || '';

    function armHumanToken() {
      if (!jsTokenInput || !challenge || jsTokenInput.value) return;
      try {
        var rev = challenge.split('').reverse().join('');
        jsTokenInput.value = window.btoa(rev + ':vh-human');
      } catch (err) {}
    }

    ['focusin', 'keydown', 'pointerdown', 'touchstart', 'input', 'change'].forEach(function (ev) {
      form.addEventListener(ev, armHumanToken, { once: true, passive: true });
    });

    form.addEventListener('submit', function (e) {
      e.preventDefault();
      armHumanToken();

      // Client-side validation
      var nomeInput     = form.querySelector('[name="nome"]');
      var cidadeInput   = form.querySelector('[name="cidade"]');
      var whatsappInput = form.querySelector('[name="whatsapp"]');

      var nomeVal   = nomeInput ? nomeInput.value.trim() : '';
      var cidadeVal = cidadeInput ? cidadeInput.value.trim() : '';
      var waVal     = whatsappInput ? whatsappInput.value.replace(/\D/g, '') : '';

      if (!nomeVal || nomeVal.length < 2) {
        showError('Por favor, informe seu nome completo.');
        if (nomeInput) nomeInput.focus();
        return;
      }
      if (!cidadeVal || cidadeVal.length < 2) {
        showError('Por favor, informe a cidade ou bairro do imóvel.');
        if (cidadeInput) cidadeInput.focus();
        return;
      }
      if (/(https?:\/\/|www\.|<script)/i.test(nomeVal + ' ' + cidadeVal)) {
        showError('Por favor, não insira links ou caracteres especiais nos campos.');
        return;
      }
      if (!waVal || waVal.length < 10 || /^(\d)\1+$/.test(waVal)) {
        showError('Por favor, informe um número de WhatsApp válido com DDD (mínimo 10 dígitos).');
        if (whatsappInput) whatsappInput.focus();
        return;
      }

      // Collect data
      var data = new FormData(form);
      data.append('action', 'vh_form');

      // UI: loading state
      var btnText    = submitBtn.querySelector('.btn-text');
      var btnLoading = submitBtn.querySelector('.btn-loading');
      submitBtn.disabled = true;
      if (btnText)    btnText.style.display    = 'none';
      if (btnLoading) btnLoading.style.display = '';
      if (errBox)     errBox.style.display     = 'none';

      fetch(cfg.ajaxUrl || '/wp-admin/admin-ajax.php', {
        method:  'POST',
        body:    data,
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          submitBtn.disabled = false;
          if (btnText)    btnText.style.display    = '';
          if (btnLoading) btnLoading.style.display = 'none';

          if (!res.success) {
            showError(res.data && res.data.message ? res.data.message : 'Ocorreu um erro. Tente novamente.');
            return;
          }

          var d = res.data || {};

          // Show success state
          form.style.display = 'none';
          if (success) {
            success.style.display = 'block';
            success.setAttribute('aria-hidden', 'false');
            var waBtn = document.getElementById('form-success-wa-btn');
            if (waBtn && d.wa_url) {
              waBtn.href = d.wa_url;
            }
            if (typeof success.focus === 'function') {
              success.focus();
            }
          }

          // Open WhatsApp if mode includes it
          if (d.wa_url) {
            setTimeout(function () {
              window.open(d.wa_url, '_blank', 'noopener,noreferrer');
            }, 600);
          }
        })
        .catch(function () {
          submitBtn.disabled = false;
          if (btnText)    btnText.style.display    = '';
          if (btnLoading) btnLoading.style.display = 'none';
          showError('Erro de conexão. Verifique sua internet e tente novamente.');
        });
    });

    function showError(msg) {
      if (!errBox) return;
      errBox.textContent = msg;
      errBox.style.display = 'block';
      errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
  }

  // ── Sync all wa.me links to the canonical number from float button / VH ────
  function syncWaLinks() {
    var cfg = window.VH || {};
    var num = cfg.waNumber || '';
    if (!num) {
      var floatBtn = document.querySelector('.vivahost-wa-float');
      if (floatBtn && floatBtn.href && floatBtn.href.includes('wa.me')) {
        var match = floatBtn.href.match(/wa\.me\/(\d+)/);
        if (match) num = match[1];
      }
    }
    if (!num) return;

    document.querySelectorAll('a[href*="wa.me"]').forEach(function (a) {
      var existing = a.href.match(/wa\.me\/(\d+)/);
      if (existing && existing[1] !== num) {
        a.href = a.href.replace(/wa\.me\/\d+/, 'wa.me/' + num);
      }
    });
  }

  // ── Back to Top ─────────────────────────────────────────────────────────
  function initBackToTop() {
    var btn = document.getElementById('vh-back-to-top');
    if (!btn) return;
    function onScroll() { btn.classList.toggle('visible', window.scrollY > 500); }
    window.addEventListener('scroll', onScroll, { passive: true });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    onScroll();
  }

  // ── Parallax Hero ──────────────────────────────────────────────────────
  function initParallax() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var hero = document.querySelector('.vh-hero');
    if (!hero) return;
    var speed = 0.35;
    window.addEventListener('scroll', function () {
      var offset = window.pageYOffset * speed;
      hero.style.backgroundPositionY = 'calc(50% - ' + offset + 'px)';
    }, { passive: true });
  }

  // ── Stats Counter Animation ───────────────────────────────────────────
  function initStatsCounter() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var nums = document.querySelectorAll('.stat-num, .host-stat-num');
    if (!nums.length || !('IntersectionObserver' in window)) return;

    function parsePtBrNum(str) {
      var s = str.replace(/[^0-9.,]/g, '');
      if (/\.\d{3}/.test(s)) {
        s = s.replace(/\./g, '');
      }
      s = s.replace(',', '.');
      return parseFloat(s);
    }

    function animateNum(el) {
      // Preserve inner star-yellow span if present
      var starSpan = el.querySelector('.star-yellow');
      var starHtml = starSpan ? ' ' + starSpan.outerHTML : '';

      var clone = el.cloneNode(true);
      var cloneStar = clone.querySelector('.star-yellow');
      if (cloneStar) cloneStar.remove();
      var text = clone.textContent.replace(/\s+/g, ' ').trim();

      var match = text.match(/^([^0-9]*?)([0-9][0-9.,]*)(.*)$/);
      if (!match) return;
      var prefix = match[1] || '';
      var numStr = match[2] || '';
      var suffix = match[3] || '';

      var num = parsePtBrNum(numStr);
      if (isNaN(num)) return;
      var hasDecimals = numStr.indexOf(',') !== -1;

      var duration = 1500;
      var start = performance.now();

      function step(now) {
        var progress = Math.min((now - start) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
        var current = num * eased;
        var display;
        if (hasDecimals) {
          display = current.toFixed(2).replace('.', ',');
        } else {
          display = Math.round(current).toLocaleString('pt-BR');
        }
        if (starHtml) {
          el.innerHTML = prefix + display + suffix + starHtml;
        } else {
          el.textContent = prefix + display + suffix;
        }
        if (progress < 1) requestAnimationFrame(step);
      }
      requestAnimationFrame(step);
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          var origText = entry.target.getAttribute('data-orig');
          if (!origText) {
            entry.target.setAttribute('data-orig', entry.target.textContent);
          }
          animateNum(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.3 });

    nums.forEach(function (el) { io.observe(el); });
  }

  // ── Cookie Consent ──────────────────────────────────────────────────────
  function initCookieConsent() {
    var bar = document.getElementById('vh-cookie-bar');
    if (!bar) return;
    try {
      if (localStorage.getItem('vh_cookie_accepted')) return;
    } catch (e) {
      // localStorage disabled or private browsing
    }
    bar.style.display = 'flex';
    var acceptBtn = document.getElementById('vh-cookie-accept');
    if (acceptBtn) {
      acceptBtn.addEventListener('click', function () {
        try {
          localStorage.setItem('vh_cookie_accepted', '1');
        } catch (e) {}
        bar.style.display = 'none';
      });
    }
  }

  // ── Property Filters ──────────────────────────────────────────────────────
  function initPropertyFilters() {
    var buttons = document.querySelectorAll('.vh-prop-filter');
    var cards   = document.querySelectorAll('.imovel-card');
    if (!buttons.length || !cards.length) return;

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var filter = btn.getAttribute('data-filter');
        buttons.forEach(function (b) {
          b.classList.remove('btn-primary', 'text-white', 'active');
          b.classList.add('btn-outline', 'btn-neutral');
        });
        btn.classList.add('btn-primary', 'text-white', 'active');
        btn.classList.remove('btn-outline', 'btn-neutral');

        cards.forEach(function (card) {
          var cardFilter = card.getAttribute('data-filter');
          if (filter === 'all' || cardFilter === filter) {
            card.style.display = '';
            card.classList.remove('hidden');
          } else {
            card.style.display = 'none';
            card.classList.add('hidden');
          }
        });
      });
    });
  }

  // ── Init ─────────────────────────────────────────────────────────────────
  function init() {
    initHeaderScroll();
    initMobileNav();
    initSmoothScroll();
    initAnimations();
    initForm();
    syncWaLinks();
    initBackToTop();
    initParallax();
    initStatsCounter();
    initCookieConsent();
    initPropertyFilters();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
