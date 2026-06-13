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
  var REAL = Math.round(allCards.length / 2); // 3

  // Mark track as infinite so the CSS nth-child hide is lifted
  track.classList.add('is-infinite');

  // Prepend head clones (clone of the first REAL real items placed before them)
  // This gives: [hA hB hC | A B C | A' B' C']
  allCards.slice(0, REAL).forEach(function (el) {
    var c = el.cloneNode(true);
    c.setAttribute('aria-hidden', 'true');
    c.classList.add('is-clone');
    track.insertBefore(c, track.firstChild);
  });

  // DOM: head(0…REAL-1) | real(REAL…2*REAL-1) | phpTail(2*REAL…3*REAL-1)
  // Start the viewport scrolled to the first real item (index REAL)

  var prevBtn = section.querySelector('.events__nav-prev');
  var nextBtn = section.querySelector('.events__nav-next');
  var dots    = Array.from(section.querySelectorAll('.events__dot'));
  var settleTimer = null;

  // ── Helpers ───────────────────────────────────────

  function getCards() { return Array.from(track.querySelectorAll('.event-card')); }

  function getCurrentIndex() {
    var vpLeft    = viewport.getBoundingClientRect().left;
    var scrollPad = parseFloat(getComputedStyle(viewport).scrollPaddingInlineStart) || 0;
    var snapLine  = vpLeft + scrollPad;
    var cards     = getCards();
    var best = REAL, bestDist = Infinity;
    cards.forEach(function (el, i) {
      var d = Math.abs(el.getBoundingClientRect().left - snapLine);
      if (d < bestDist) { bestDist = d; best = i; }
    });
    return best;
  }

  function scrollToIdx(idx, smooth) {
    var cards   = getCards();
    var card    = cards[idx];
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

  // ── Scroll listener ───────────────────────────────

  viewport.addEventListener('scroll', function () {
    updateDots();
    clearTimeout(settleTimer);
    settleTimer = setTimeout(normalize, 180);
  }, { passive: true });

  viewport.addEventListener('scrollend', function () {
    clearTimeout(settleTimer);
    normalize();
  }, { passive: true });

  // ── Init ──────────────────────────────────────────

  scrollToIdx(REAL, false);
  updateDots();

  // ── Buttons ───────────────────────────────────────

  if (prevBtn) {
    prevBtn.disabled = false;
    prevBtn.addEventListener('click', function () {
      scrollToIdx(getCurrentIndex() - 1, true);
    });
  }

  if (nextBtn) {
    nextBtn.disabled = false;
    nextBtn.addEventListener('click', function () {
      scrollToIdx(getCurrentIndex() + 1, true);
    });
  }

  dots.forEach(function (d, i) {
    d.addEventListener('click', function () {
      scrollToIdx(REAL + i, true);
    });
  });
});
