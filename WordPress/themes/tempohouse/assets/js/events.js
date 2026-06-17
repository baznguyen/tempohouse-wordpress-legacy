document.addEventListener('DOMContentLoaded', function () {
  var section  = document.querySelector('.events');
  if (!section) return;

  var viewport = section.querySelector('.events__viewport');

  // ── Video hover handlers (desktop) ───────────────
  section.querySelectorAll('.event-card').forEach(function (card) {
    card.addEventListener('mouseenter', function () {
      var v = card.querySelector('video');
      if (v) v.play().catch(function () {});
    });
    card.addEventListener('mouseleave', function () {
      var v = card.querySelector('video');
      if (v) { v.pause(); v.currentTime = 0; }
    });
  });

  if (!viewport) return;

  // ── Mobile infinite carousel ──────────────────────
  if (!window.matchMedia('(max-width: 900px)').matches) return;

  if (typeof window.tempoDragScroll === 'function') {
    window.tempoDragScroll(viewport);
  }

  var track = section.querySelector('.events__track');
  if (!track) return;

  // PHP renders 2× items [A B C A' B' C']; REAL = half that count
  var allCards = Array.from(track.querySelectorAll('.event-card'));
  var REAL = Math.round(allCards.length / 2);

  // Mark track as infinite so the CSS nth-child hide is lifted
  track.classList.add('is-infinite');

  // Prepend head clones: [hA hB hC | A B C | A' B' C']
  allCards.slice(0, REAL).forEach(function (el) {
    var c = el.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    c.classList.add('is-clone');
    track.insertBefore(c, track.firstChild);
  });

  // Cache full card list after DOM is built — doesn't change after init
  var cards = Array.from(track.querySelectorAll('.event-card'));

  // Cache scroll-pad — CSS value, only changes on resize
  var scrollPad = parseFloat(getComputedStyle(viewport).scrollPaddingInlineStart) || 0;

  var prevBtn    = section.querySelector('.events__nav-prev');
  var nextBtn    = section.querySelector('.events__nav-next');
  var dots       = Array.from(section.querySelectorAll('.events__dot'));
  var settleTimer = null;
  var dotsRafId   = 0;

  function getCurrentIndex() {
    var snapLine = viewport.getBoundingClientRect().left + scrollPad;
    var best = REAL, bestDist = Infinity;
    for (var i = 0; i < cards.length; i++) {
      var d = Math.abs(cards[i].getBoundingClientRect().left - snapLine);
      if (d < bestDist) { bestDist = d; best = i; }
    }
    return best;
  }

  function scrollToIdx(idx, smooth) {
    var card = cards[idx];
    if (!card) return;
    var vpRect   = viewport.getBoundingClientRect();
    var cardRect = card.getBoundingClientRect();
    var target   = viewport.scrollLeft + cardRect.left - vpRect.left;
    if (smooth) {
      viewport.scrollTo({ left: target, behavior: 'smooth' });
    } else {
      viewport.scrollLeft = target;
    }
  }

  function normalize() {
    var idx = getCurrentIndex();
    if (idx < REAL) {
      scrollToIdx(idx + REAL, false);
    } else if (idx >= REAL * 2) {
      scrollToIdx(idx - REAL, false);
    }
    updateDots();
  }

  function updateDots() {
    var idx    = getCurrentIndex();
    var dotIdx = ((idx - REAL) % REAL + REAL) % REAL;
    dots.forEach(function (d, i) {
      d.classList.toggle('events__dot--active', i === dotIdx);
    });
    if (prevBtn) prevBtn.disabled = false;
    if (nextBtn) nextBtn.disabled = false;
  }

  // ── Scroll listeners ──────────────────────────────

  viewport.addEventListener('scroll', function () {
    // Throttle dot updates to one rAF per frame
    if (!dotsRafId) dotsRafId = requestAnimationFrame(function () {
      updateDots();
      dotsRafId = 0;
    });
    clearTimeout(settleTimer);
    settleTimer = setTimeout(normalize, 180);
  }, { passive: true });

  viewport.addEventListener('scrollend', function () {
    clearTimeout(settleTimer);
    normalize();
  }, { passive: true });

  // ── Resize: re-snap + recache scroll-pad ─────────

  var resizeTimer = null;
  window.addEventListener('resize', function () {
    scrollPad = parseFloat(getComputedStyle(viewport).scrollPaddingInlineStart) || 0;
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      scrollToIdx(getCurrentIndex(), false);
    }, 150);
  }, { passive: true });

  // ── Init ──────────────────────────────────────────

  scrollToIdx(REAL, false);
  updateDots();

  // ── Buttons ───────────────────────────────────────

  if (prevBtn) {
    prevBtn.disabled = false;
    prevBtn.addEventListener('click', function () { scrollToIdx(getCurrentIndex() - 1, true); });
  }
  if (nextBtn) {
    nextBtn.disabled = false;
    nextBtn.addEventListener('click', function () { scrollToIdx(getCurrentIndex() + 1, true); });
  }

  dots.forEach(function (d, i) {
    d.addEventListener('click', function () { scrollToIdx(REAL + i, true); });
  });
});
