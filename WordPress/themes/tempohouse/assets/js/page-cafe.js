/**
 * Café page — matcha grid carousel
 * Builds dot pagination + prev/next buttons for the 6-item matcha grid on mobile (<1000px).
 */
(function () {
  'use strict';

  function initMatchaCarousel() {
    var grid     = document.querySelector('.page-cafe__matcha-grid');
    var nav      = document.querySelector('.page-cafe__matcha-nav');
    var dotsWrap = document.querySelector('.page-cafe__matcha-dots');
    if (!grid || !nav || !dotsWrap) return;

    var items   = Array.from(grid.querySelectorAll('.page-cafe__matcha-item'));
    if (!items.length) return;

    var prevBtn = nav.querySelector('.page-cafe__matcha-nav-prev');
    var nextBtn = nav.querySelector('.page-cafe__matcha-nav-next');

    var isMobile  = window.matchMedia('(max-width: 1000px)');
    var activeIdx = 0;

    // ── Build dot buttons ──────────────────────────
    items.forEach(function (item, i) {
      var dot = document.createElement('button');
      dot.className = 'page-cafe__matcha-dot' + (i === 0 ? ' is-active' : '');
      dot.setAttribute('aria-label', 'View matcha ' + (i + 1) + ' of ' + items.length);
      dot.setAttribute('type', 'button');
      dot.addEventListener('click', function () { goTo(i); });
      dotsWrap.appendChild(dot);
    });

    var dots = Array.from(dotsWrap.querySelectorAll('.page-cafe__matcha-dot'));

    var originOffset = 0;

    function computeOrigin() {
      originOffset = items[0] ? items[0].offsetLeft : 0;
    }

    function updateButtons() {
      if (prevBtn) prevBtn.disabled = activeIdx <= 0;
      if (nextBtn) nextBtn.disabled = activeIdx >= items.length - 1;
    }

    // ── Navigation ─────────────────────────────────
    function goTo(idx, instant) {
      idx = Math.max(0, Math.min(idx, items.length - 1));
      activeIdx = idx;
      grid.scrollTo({
        left: items[idx].offsetLeft - originOffset,
        behavior: instant ? 'instant' : 'smooth',
      });
      dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
      updateButtons();
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { goTo(activeIdx - 1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { goTo(activeIdx + 1); });

    // ── Track manual scroll → update dots + buttons ─
    var ticking = false;
    grid.addEventListener('scroll', function () {
      if (!isMobile.matches) return;
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        var itemWidth = items[0] ? items[0].offsetWidth : 1;
        var idx = Math.round(grid.scrollLeft / itemWidth);
        idx = Math.max(0, Math.min(idx, dots.length - 1));
        if (idx !== activeIdx) {
          activeIdx = idx;
          dots.forEach(function (d, i) { d.classList.toggle('is-active', i === idx); });
          updateButtons();
        }
        ticking = false;
      });
    }, { passive: true });

    // ── Show / hide nav on resize ──────────────────
    function updateVisibility() {
      var show = isMobile.matches;
      nav.style.display = show ? 'flex' : 'none';
      if (show) {
        computeOrigin();
        updateButtons();
      } else {
        goTo(0, true);
      }
    }

    isMobile.addEventListener('change', updateVisibility);
    updateVisibility();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMatchaCarousel);
  } else {
    initMatchaCarousel();
  }
})();
