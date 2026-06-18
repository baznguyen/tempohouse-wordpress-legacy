/* ═══════════════════════════════════════════════════════════════
   Tempo Time-of-Day Switcher — nav popup
   Default: "day". User manual selection persisted in localStorage.
   ═══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var HERO_ACT = { day: 'morning', afternoon: 'afternoon', night: 'evening' };

  function syncHero(period) {
    var hero = document.querySelector('.hero');
    if (hero) hero.setAttribute('data-tempo-act', HERO_ACT[period] || 'morning');
  }

  function applyPeriod(period) {
    document.documentElement.setAttribute('data-tempo-time', period);
    syncHero(period);
    try { localStorage.setItem('tempo-time', period); } catch (e) {}

    document.querySelectorAll('.theme-popup__opt').forEach(function (btn) {
      btn.classList.toggle('is-active', btn.dataset.period === period);
    });
  }

  function openPopup(btn, popup) {
    popup.hidden = false;
    btn.setAttribute('aria-expanded', 'true');
  }

  function closePopup(btn, popup) {
    popup.hidden = true;
    btn.setAttribute('aria-expanded', 'false');
  }

  function initNavSwitcher() {
    var btn   = document.getElementById('theme-switch-btn');
    var popup = document.getElementById('theme-switch-popup');
    if (!btn || !popup) return;

    var current = document.documentElement.getAttribute('data-tempo-time') || 'day';
    syncHero(current);

    // Mark the active option on load
    document.querySelectorAll('.theme-popup__opt').forEach(function (opt) {
      opt.classList.toggle('is-active', opt.dataset.period === current);
    });

    // Toggle popup on icon click
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (popup.hidden) {
        openPopup(btn, popup);
      } else {
        closePopup(btn, popup);
      }
    });

    // Option selection
    popup.querySelectorAll('.theme-popup__opt').forEach(function (opt) {
      opt.addEventListener('click', function () {
        applyPeriod(opt.dataset.period);
        closePopup(btn, popup);
      });
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
      if (!popup.hidden && !btn.contains(e.target) && !popup.contains(e.target)) {
        closePopup(btn, popup);
      }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !popup.hidden) {
        closePopup(btn, popup);
        btn.focus();
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavSwitcher);
  } else {
    initNavSwitcher();
  }
})();
