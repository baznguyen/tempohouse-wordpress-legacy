/* ═══════════════════════════════════════════════════════════════
   Tempo Time-of-Day Switcher
   - Sets html[data-tempo-time] immediately (avoids FOUC)
   - Builds a floating pill widget for manual preview
   - Persists selection to localStorage
   Auto periods: Day 05–13, Afternoon 13–18, Night 18–05
   ═══════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  var PERIODS = ['day', 'afternoon', 'night'];
  var LABELS  = { day: 'Day', afternoon: 'Afternoon', night: 'Night' };
  var STORE_KEY = 'tempo-time';

  function getAutoPeriod() {
    var h = new Date().getHours();
    if (h >= 5  && h < 13) return 'day';
    if (h >= 13 && h < 18) return 'afternoon';
    return 'night';
  }

  function loadSaved() {
    try {
      var s = localStorage.getItem(STORE_KEY);
      return (s && PERIODS.indexOf(s) !== -1) ? s : null;
    } catch (e) { return null; }
  }

  function savePeriod(p) {
    try { localStorage.setItem(STORE_KEY, p); } catch (e) {}
  }

  // Map theme period → hero data-tempo-act value that hero.css recognises
  var HERO_ACT = { day: 'morning', afternoon: 'afternoon', night: 'evening' };

  function syncHero(period) {
    var hero = document.querySelector('.hero');
    if (!hero) return;
    hero.setAttribute('data-tempo-act', HERO_ACT[period] || 'morning');
  }

  function applyPeriod(period, buttons) {
    document.documentElement.setAttribute('data-tempo-time', period);
    savePeriod(period);
    syncHero(period);
    if (!buttons) return;
    buttons.forEach(function (btn) {
      var active = btn.dataset.period === period;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
  }

  function buildWidget(initialPeriod) {
    // Sync hero palette now that DOM is ready (hero.js has already run)
    syncHero(initialPeriod);

    var widget = document.createElement('div');
    widget.className = 'tempo-time-switcher';
    widget.setAttribute('role', 'group');
    widget.setAttribute('aria-label', 'Time of day theme');

    var buttons = PERIODS.map(function (p) {
      var btn = document.createElement('button');
      btn.className = 'tempo-time-switcher__btn';
      if (p === initialPeriod) btn.classList.add('is-active');
      btn.dataset.period = p;
      btn.textContent = LABELS[p];
      btn.setAttribute('type', 'button');
      btn.setAttribute('aria-pressed', p === initialPeriod ? 'true' : 'false');
      widget.appendChild(btn);
      return btn;
    });

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        applyPeriod(btn.dataset.period, buttons);
      });
    });

    document.body.appendChild(widget);
  }

  // ── Apply theme immediately (this script runs in <footer> but after
  //    the wp_head inline snippet for true zero-FOUC) ──────────────

  var initial = loadSaved() || getAutoPeriod();
  document.documentElement.setAttribute('data-tempo-time', initial);

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { buildWidget(initial); });
  } else {
    buildWidget(initial);
  }
})();
