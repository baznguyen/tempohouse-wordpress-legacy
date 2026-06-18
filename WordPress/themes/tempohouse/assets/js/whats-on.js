(function () {
  'use strict';

  function initSection(section) {
    var vp  = section.querySelector('.events__viewport');
    var nav = section.querySelector('.page-whats-on__carousel-nav');
    if (!vp || !nav) return;

    var track = vp.querySelector('.events__track');
    if (!track) return;

    var cards   = Array.from(track.querySelectorAll('.event-card'));
    if (cards.length < 2) return;

    var prevBtn = nav.querySelector('.events__nav-prev');
    var nextBtn = nav.querySelector('.events__nav-next');
    var dots    = Array.from(nav.querySelectorAll('.events__dot'));

    var settleTimer = null;
    var rafId       = 0;

    function getScrollPad() {
      return parseFloat(getComputedStyle(vp).scrollPaddingInlineStart) || 0;
    }

    function getCurrentIndex() {
      var snapLine = vp.getBoundingClientRect().left + getScrollPad();
      var best = 0, bestDist = Infinity;
      for (var i = 0; i < cards.length; i++) {
        var d = Math.abs(cards[i].getBoundingClientRect().left - snapLine);
        if (d < bestDist) { bestDist = d; best = i; }
      }
      return best;
    }

    function scrollToIdx(idx, smooth) {
      var i    = Math.max(0, Math.min(idx, cards.length - 1));
      var card = cards[i];
      if (!card) return;
      var target = vp.scrollLeft
                 + card.getBoundingClientRect().left
                 - vp.getBoundingClientRect().left
                 - getScrollPad();
      if (smooth) {
        vp.scrollTo({ left: target, behavior: 'smooth' });
      } else {
        vp.scrollLeft = target;
      }
    }

    function updateNav() {
      var idx = getCurrentIndex();
      dots.forEach(function (d, i) {
        d.classList.toggle('events__dot--active', i === idx);
      });
      if (prevBtn) prevBtn.disabled = (idx <= 0);
      if (nextBtn) nextBtn.disabled = (idx >= cards.length - 1);
    }

    vp.addEventListener('scroll', function () {
      if (!rafId) rafId = requestAnimationFrame(function () { updateNav(); rafId = 0; });
      clearTimeout(settleTimer);
      settleTimer = setTimeout(updateNav, 160);
    }, { passive: true });

    vp.addEventListener('scrollend', function () {
      clearTimeout(settleTimer);
      updateNav();
    }, { passive: true });

    window.addEventListener('resize', function () {
      clearTimeout(settleTimer);
      settleTimer = setTimeout(function () { scrollToIdx(getCurrentIndex(), false); }, 150);
    }, { passive: true });

    if (prevBtn) {
      prevBtn.addEventListener('click', function () { scrollToIdx(getCurrentIndex() - 1, true); });
    }
    if (nextBtn) {
      nextBtn.addEventListener('click', function () { scrollToIdx(getCurrentIndex() + 1, true); });
    }

    dots.forEach(function (d, i) {
      d.addEventListener('click', function () { scrollToIdx(i, true); });
    });

    scrollToIdx(0, false);
    updateNav();
  }

  function init() {
    document.querySelectorAll('.page-whats-on__section').forEach(initSection);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
