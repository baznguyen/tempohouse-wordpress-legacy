/* TEMPO House Reservations — Shared Modal Utility
   Exposes window.thrModal(opts) → Promise
   Self-bootstrapping: creates its own DOM on first call. */
(function () {
  'use strict';

  var _resolve   = null;
  var _container = null;

  function ensureDOM() {
    if (_container) return;
    var el = document.createElement('div');
    el.id = 'thr-modal';
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');
    el.setAttribute('aria-labelledby', 'thr-modal-title');
    el.setAttribute('hidden', '');
    el.innerHTML =
      '<div class="thr-modal-card">' +
        '<div class="thr-modal-hd">' +
          '<span class="thr-modal-title" id="thr-modal-title"></span>' +
        '</div>' +
        '<div class="thr-modal-body" id="thr-modal-body"></div>' +
        '<div class="thr-modal-foot">' +
          '<button class="thr-modal-btn thr-modal-btn-ghost" id="thr-modal-cancel" type="button">Cancel</button>' +
          '<button class="thr-modal-btn thr-modal-btn-ghost" id="thr-modal-extra" type="button" hidden>Extra</button>' +
          '<button class="thr-modal-btn thr-modal-btn-primary" id="thr-modal-ok" type="button">OK</button>' +
        '</div>' +
      '</div>';
    document.body.appendChild(el);
    _container = el;

    document.getElementById('thr-modal-ok').addEventListener('click', function () {
      var inp = _container.querySelector('#thr-modal-body .thr-modal-input');
      _close(inp ? (inp.value.trim() || null) : true);
    });
    document.getElementById('thr-modal-extra').addEventListener('click', function () {
      _close('extra');
    });
    document.getElementById('thr-modal-cancel').addEventListener('click', function () {
      _close(null);
    });
    _container.addEventListener('click', function (e) {
      if (e.target === _container) _close(null);
    });
    _container.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        document.getElementById('thr-modal-ok').click();
      }
      if (e.key === 'Escape') { e.preventDefault(); _close(null); }
    });
  }

  function _close(result) {
    if (_container) _container.hidden = true;
    if (_resolve) { _resolve(result); _resolve = null; }
  }

  window.thrModal = function (opts) {
    return new Promise(function (resolve) {
      ensureDOM();
      _resolve = resolve;

      document.getElementById('thr-modal-title').textContent = opts.title || '';
      document.getElementById('thr-modal-body').innerHTML    = opts.body  || '';

      var ok    = document.getElementById('thr-modal-ok');
      var extra = document.getElementById('thr-modal-extra');
      var cancel = document.getElementById('thr-modal-cancel');

      ok.textContent     = opts.ok     || 'OK';
      cancel.textContent = opts.cancel || 'Cancel';
      cancel.hidden      = (opts.type === 'alert');
      ok.className       = 'thr-modal-btn ' + (opts.danger ? 'thr-modal-btn-danger' : 'thr-modal-btn-primary');
      extra.textContent  = opts.extra  || '';
      extra.hidden       = !opts.extra;
      extra.className    = 'thr-modal-btn ' + (opts.extraDanger ? 'thr-modal-btn-danger' : 'thr-modal-btn-ghost');

      _container.hidden = false;

      if (opts.type === 'prompt') {
        var inp = _container.querySelector('#thr-modal-body .thr-modal-input');
        if (inp) setTimeout(function () { inp.focus(); inp.select(); }, 60);
      } else {
        setTimeout(function () { ok.focus(); }, 60);
      }
    });
  };
})();
