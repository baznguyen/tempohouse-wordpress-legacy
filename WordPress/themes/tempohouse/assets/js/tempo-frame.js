/**
 * Tempo Frame — lightbox and clickthrough handler.
 *
 * Behaviour:
 *  - <a class="tempo-frame" data-lightbox href="image.jpg">  → lightbox
 *  - <a class="tempo-frame" href="/page">                    → normal navigation
 *  - Escape key / click-outside closes lightbox
 */
(function () {
  'use strict';

  var lightbox     = null;
  var lightboxImg  = null;
  var lightboxCap  = null;
  var scrollY      = 0;

  function buildLightbox() {
    if (lightbox) return;

    lightbox = document.createElement('div');
    lightbox.className = 'tempo-lightbox';
    lightbox.setAttribute('role', 'dialog');
    lightbox.setAttribute('aria-modal', 'true');
    lightbox.setAttribute('aria-label', 'Image viewer');

    var closeBtn = document.createElement('button');
    closeBtn.className = 'tempo-lightbox__close';
    closeBtn.setAttribute('aria-label', 'Close image viewer');
    closeBtn.innerHTML = '&times;';
    closeBtn.addEventListener('click', closeLightbox);

    var inner = document.createElement('div');
    inner.className = 'tempo-lightbox__inner';

    lightboxImg = document.createElement('img');
    lightboxImg.className = 'tempo-lightbox__img';
    lightboxImg.alt = '';

    lightboxCap = document.createElement('p');
    lightboxCap.className = 'tempo-lightbox__caption';

    inner.appendChild(lightboxImg);
    inner.appendChild(lightboxCap);
    lightbox.appendChild(closeBtn);
    lightbox.appendChild(inner);
    document.body.appendChild(lightbox);

    // Click outside image closes
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox) closeLightbox();
    });
  }

  function openLightbox(src, caption) {
    buildLightbox();

    scrollY = window.scrollY;
    lightboxImg.src = src;
    lightboxImg.alt = caption || '';
    lightboxCap.textContent = caption || '';
    lightboxCap.hidden = !caption;

    lightbox.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    document.addEventListener('keydown', onKeyDown);

    // Focus the close button
    var closeBtn = lightbox.querySelector('.tempo-lightbox__close');
    if (closeBtn) closeBtn.focus();
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove('is-open');
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeyDown);
    window.scrollTo(0, scrollY);
  }

  function onKeyDown(e) {
    if (e.key === 'Escape') closeLightbox();
  }

  function resolveImageSrc(frame) {
    // Prefer explicit data attribute
    var src = frame.dataset.lightboxSrc;
    if (src) return src;

    // Use href (assumed to be an image URL)
    if (frame.href && /\.(jpg|jpeg|png|gif|webp|avif|svg)(\?.*)?$/i.test(frame.href)) {
      return frame.href;
    }

    // Fall back to <img> inside the frame
    var img = frame.querySelector('img');
    if (img && img.src) return img.src;

    return null;
  }

  function resolveCaption(frame) {
    // Prefer data attribute
    if (frame.dataset.lightboxCaption) return frame.dataset.lightboxCaption;
    // Fall back to aria-label
    if (frame.getAttribute('aria-label')) return frame.getAttribute('aria-label');
    // Fall back to .tempo-frame__caption text
    var cap = frame.querySelector('.tempo-frame__caption, .tempo-frame__label');
    if (cap) return cap.textContent.trim();
    return '';
  }

  function init() {
    document.querySelectorAll('a.tempo-frame[data-lightbox], a[data-lightbox].tempo-frame').forEach(function (frame) {
      frame.addEventListener('click', function (e) {
        var src = resolveImageSrc(frame);
        if (!src) return; // no image src — let the link navigate normally

        e.preventDefault();
        openLightbox(src, resolveCaption(frame));
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
