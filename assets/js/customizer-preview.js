/**
 * VivaHost — Customizer Live Preview
 * Updates the preview iframe instantly without a full reload.
 */
(function () {
  'use strict';

  if (!window.wp || !wp.customize) return;

  var c = wp.customize;

  /* ── CSS variable helpers ─────────────────────────────────────────── */
  function setCssVar(name, val) {
    document.documentElement.style.setProperty(name, val);
  }

  /* ── Hex to RGB Helper ────────────────────────────────────────────── */
  function hexToRgb(hex) {
    hex = (hex || '').replace(/^#/, '');
    if (hex.length === 3) {
      hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    var num = parseInt(hex, 16);
    if (isNaN(num)) return '255, 56, 92';
    var r = (num >> 16) & 255;
    var g = (num >> 8) & 255;
    var b = num & 255;
    return r + ', ' + g + ', ' + b;
  }

  /* ── Colors ─────────────────────────────────────────────────────────── */
  c('vivahost_color_primary', function (v) {
    v.bind(function (val) {
      setCssVar('--vh-primary', val);
      setCssVar('--vh-primary-rgb', hexToRgb(val));
    });
  });
  c('vivahost_color_footer', function (v) {
    v.bind(function (val) { setCssVar('--vh-footer-bg', val); });
  });

  /* ── Font ────────────────────────────────────────────────────────────── */
  c('vivahost_font_family', function (v) {
    v.bind(function (val) {
      setCssVar('--f', "'" + val + "', -apple-system, BlinkMacSystemFont, sans-serif");
      // reload Google Font link
      var existing = document.getElementById('vh-font-preview');
      if (existing) existing.remove();
      var link = document.createElement('link');
      link.id   = 'vh-font-preview';
      link.rel  = 'stylesheet';
      link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(val) + ':ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap';
      document.head.appendChild(link);
    });
  });

  /* ── Helper: update text nodes by data-vh ────────────────────────────── */
  function bindText(settingKey, selector) {
    c('vivahost_' + settingKey, function (v) {
      v.bind(function (val) {
        var attr = '[data-vh="' + settingKey + '"]';
        document.querySelectorAll(selector || attr).forEach(function (el) {
          el.textContent = val;
        });
      });
    });
  }

  /* ── Hero ────────────────────────────────────────────────────────────── */
  bindText('hero_eyebrow');
  bindText('hero_title');
  bindText('hero_subtitle');
  bindText('hero_btn1_text');
  bindText('hero_btn2_text');

  c('vivahost_hero_btn1_link', function (v) {
    v.bind(function (val) {
      var el = document.querySelector('.hero-actions a:first-child');
      if (el) el.href = val;
    });
  });
  c('vivahost_hero_btn2_link', function (v) {
    v.bind(function (val) {
      var el = document.querySelector('.hero-actions a:last-child');
      if (el) el.href = val;
    });
  });

  /* ── Stats ─────────────────────────────────────────────────────────────── */
  for (var i = 1; i <= 4; i++) {
    (function (idx) {
      bindText('stat_' + idx + '_num');
      bindText('stat_' + idx + '_label');
    })(i);
  }

  /* ── Comparison ─────────────────────────────────────────────────────── */
  bindText('compare_title');
  bindText('compare_left_title');
  bindText('compare_right_title');
  for (var cp = 1; cp <= 3; cp++) {
    (function (idx) {
      bindText('compare_left_' + idx);
      bindText('compare_right_' + idx);
    })(cp);
  }

  /* ── Process ─────────────────────────────────────────────────────────── */
  bindText('process_title');
  bindText('process_intro');
  for (var s = 1; s <= 4; s++) {
    (function (idx) {
      bindText('step_' + idx + '_title');
      bindText('step_' + idx + '_text');
    })(s);
  }

  /* ── Services ─────────────────────────────────────────────────────────── */
  bindText('services_title');
  bindText('services_intro');
  bindText('commission_rate');
  for (var sv = 1; sv <= 6; sv++) {
    (function (idx) {
      bindText('service_' + idx + '_title');
      bindText('service_' + idx + '_text');
    })(sv);
  }

  /* ── Properties ──────────────────────────────────────────────────────── */
  bindText('imoveis_title');
  bindText('imoveis_desc');
  for (var p = 1; p <= 3; p++) {
    (function (idx) {
      bindText('prop_' + idx + '_name');
      bindText('property_' + idx + '_name');
      bindText('prop_' + idx + '_loc');
      bindText('property_' + idx + '_loc');
      bindText('prop_' + idx + '_rating');
      bindText('property_' + idx + '_rating');
      bindText('prop_' + idx + '_reviews');
      bindText('property_' + idx + '_reviews');
    })(p);
  }

  /* ── Testimonial ──────────────────────────────────────────────────────── */
  bindText('testimonial_text');
  bindText('testimonial_author');
  bindText('testimonial_city');

  /* ── Host ─────────────────────────────────────────────────────────────── */
  bindText('host_name');
  bindText('host_subtitle');
  bindText('host_bio');
  bindText('host_properties_count');
  bindText('host_properties_label');
  for (var hs = 1; hs <= 3; hs++) {
    (function (idx) {
      bindText('host_stat_' + idx + '_num');
      bindText('host_' + idx + '_num');
      bindText('host_stat_' + idx + '_label');
      bindText('host_' + idx + '_label');
    })(hs);
  }

  /* ── Reviews ──────────────────────────────────────────────────── */
  bindText('reviews_score');
  bindText('reviews_count');
  for (var rv = 1; rv <= 3; rv++) {
    (function (idx) {
      bindText('review_' + idx + '_text');
      bindText('review_' + idx + '_name');
      bindText('review_' + idx + '_author');
      bindText('review_' + idx + '_loc');
      bindText('review_' + idx + '_city');
    })(rv);
  }

  /* ── CTA ─────────────────────────────────────────────────────────────── */
  bindText('cta_title');
  bindText('cta_lead');
  bindText('trust_1');
  bindText('trust_2');
  bindText('trust_3');
  bindText('form_wa_cta');

  /* ── Footer ──────────────────────────────────────────────────────────── */
  bindText('footer_tagline');
  bindText('footer_copyright');
  bindText('footer_city');
  bindText('footer_hours');
  bindText('footer_razao');
  bindText('footer_cnpj');

  /* ── Testimonial Banner & Blog ────────────────────────────────────────── */
  bindText('testimonial_banner_quote');
  bindText('testimonial_banner_sub');
  bindText('blog_section_eyebrow');
  bindText('blog_section_title');
  bindText('blog_section_intro');

  /* ── Header CTA ──────────────────────────────────────────────────────── */
  bindText('header_cta_text');

  c('vivahost_header_cta_link', function (v) {
    v.bind(function (val) {
      var el = document.querySelector('.header-cta');
      if (el) el.href = val;
      var mobileCta = document.querySelector('.mobile-nav-cta');
      if (mobileCta) mobileCta.href = val;
    });
  });

})();
