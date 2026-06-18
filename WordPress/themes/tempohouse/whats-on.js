/**
 * What's On page — mobile carousel
 * Activates only below 1000px. Each .page-whats-on__section with an
 * .events__viewport--grid becomes a scroll-snap carousel with:
 *   - auto-scroll every 4 s (pauses on touch/drag, resumes after 6 s)
 *   - dot pagination synced to scroll position
 *   - prev/next button navigation
 */
(function () {
    'use strict';

    function init() {
        if (!window.matchMedia('(max-width: 999px)').matches) return;

        var sections = document.querySelectorAll('.page-whats-on__section');
        sections.forEach(initSection);
    }

    function initSection(section) {
        var viewport = section.querySelector('.events__viewport--grid');
        var track    = section.querySelector('.events__track');
        if (!viewport || !track) return;

        var cards   = Array.from(track.querySelectorAll('.event-card'));
        var dots    = Array.from(section.querySelectorAll('.events__dot'));
        var prevBtn = section.querySelector('.events__nav-prev');
        var nextBtn = section.querySelector('.events__nav-next');
        var TOTAL   = cards.length;

        if (TOTAL === 0) return;

        // Attach drag-scroll if available (allows mouse-drag on tablets)
        if (typeof window.tempoDragScroll === 'function') {
            window.tempoDragScroll(viewport);
        }

        var currentIdx = 0;
        var autoTimer  = null;
        var resumeTimer = null;

        // ── Helpers ─────────────────────────────────────────────────────────

        // Read scroll-padding once; CSS custom props are resolved to px by getComputedStyle.
        var scrollPad = 0;
        try {
            var raw = getComputedStyle(viewport).scrollPaddingInlineStart;
            scrollPad = (raw && raw !== 'auto') ? (parseFloat(raw) || 0) : 0;
        } catch (e) {}

        // Exact snap position for card N:
        // cards[N].offsetLeft puts the card's left at the viewport's left edge.
        // Subtracting scrollPad places it scrollPad pixels INSIDE the edge (the snap zone).
        function snapTarget(idx) {
            return Math.max(0, cards[idx].offsetLeft - scrollPad);
        }

        // ── Scroll to a card by index ───────────────────────────────────────

        function scrollToCard(idx, smooth) {
            idx = ((idx % TOTAL) + TOTAL) % TOTAL;
            currentIdx = idx;

            var target = snapTarget(idx);

            if (smooth === false) {
                viewport.scrollLeft = target;
            } else {
                viewport.scrollTo({ left: target, behavior: 'smooth' });
            }

            syncDots(idx);
        }

        // ── Dots ────────────────────────────────────────────────────────────

        function syncDots(idx) {
            dots.forEach(function (d, i) {
                d.classList.toggle('events__dot--active', i === idx);
            });
        }

        // Infer current index by comparing scrollLeft to each card's snap position.
        function getScrollIndex() {
            var best = 0, bestDist = Infinity;
            cards.forEach(function (_, i) {
                var d = Math.abs(viewport.scrollLeft - snapTarget(i));
                if (d < bestDist) { bestDist = d; best = i; }
            });
            return best;
        }

        // ── Auto-scroll ─────────────────────────────────────────────────────

        function startAuto() {
            clearInterval(autoTimer);
            autoTimer = setInterval(function () {
                var next = (currentIdx + 1) % TOTAL;
                scrollToCard(next, true);
            }, 4000);
        }

        function pauseAuto() {
            clearInterval(autoTimer);
            autoTimer = null;
        }

        function resumeAuto() {
            clearTimeout(resumeTimer);
            resumeTimer = setTimeout(startAuto, 6000);
        }

        // ── Touch / drag ────────────────────────────────────────────────────

        viewport.addEventListener('touchstart', function () {
            pauseAuto();
        }, { passive: true });

        viewport.addEventListener('touchend', function () {
            setTimeout(function () {
                currentIdx = getScrollIndex();
                syncDots(currentIdx);
            }, 350);
            resumeAuto();
        }, { passive: true });

        // ── Scroll sync (for mouse-drag via tempoDragScroll) ────────────────

        var scrollRaf = 0;
        viewport.addEventListener('scroll', function () {
            if (!scrollRaf) scrollRaf = requestAnimationFrame(function () {
                syncDots(getScrollIndex());
                scrollRaf = 0;
            });
        }, { passive: true });

        viewport.addEventListener('scrollend', function () {
            currentIdx = getScrollIndex();
            syncDots(currentIdx);
        }, { passive: true });

        // ── Buttons ─────────────────────────────────────────────────────────

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                pauseAuto();
                scrollToCard(currentIdx - 1, true);
                resumeAuto();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                pauseAuto();
                scrollToCard(currentIdx + 1, true);
                resumeAuto();
            });
        }

        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                pauseAuto();
                scrollToCard(i, true);
                resumeAuto();
            });
        });

        // ── Init ────────────────────────────────────────────────────────────

        scrollToCard(0, false);
        syncDots(0);
        startAuto();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
