/**
 * Events FAQ — Flip-card toggle
 * Click or keyboard (Enter/Space) flips a .events-faq-card.
 * Second interaction flips it back.
 */
(function () {
  'use strict';

  function init() {
    var cards = Array.from(document.querySelectorAll('.events-faq-card'));
    if (!cards.length) return;

    cards.forEach(function (card) {
      card.addEventListener('click', function () {
        card.classList.toggle('is-flipped');
        var flipped = card.classList.contains('is-flipped');
        card.setAttribute('aria-expanded', flipped ? 'true' : 'false');
        var back = card.querySelector('.events-faq-face--back');
        if (back) back.setAttribute('aria-hidden', flipped ? 'false' : 'true');
      });

      card.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          card.click();
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
