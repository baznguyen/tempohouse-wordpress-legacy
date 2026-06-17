/* ══════════════════════════════════════════════════════════════════════
   TEMPO House — Floor Plan Editor v6.0
   Konva.js canvas + horizontal toolbar + floating properties card
   ══════════════════════════════════════════════════════════════════════ */
(function () {
  'use strict';

  const cfg   = window.thrFP || {};
  const API   = cfg.apiUrl  || '';
  const NONCE = cfg.nonce   || '';
  const TYPES = cfg.types   || {};
  const TODAY = cfg.today   || new Date().toISOString().slice(0, 10);

  /* ── Status palette ─────────────────────────────────────────────── */
  const STATUS = {
    free:     { fill: '#22C55E', stroke: '#16A34A', chair: '#86EFAC', text: '#FFFFFF', label: 'Free' },
    booked:   { fill: '#3B82F6', stroke: '#2563EB', chair: '#93C5FD', text: '#FFFFFF', label: 'Booked' },
    occupied: { fill: '#F97316', stroke: '#EA580C', chair: '#FED7AA', text: '#FFFFFF', label: 'Occupied' },
    blocked:  { fill: '#9CA3AF', stroke: '#6B7280', chair: '#D1D5DB', text: '#FFFFFF', label: 'Blocked' },
  };
  const TABLE_FILL   = '#7C8290';
  const TABLE_STROKE = '#5E636E';
  const CHAIR_FILL   = '#B5BAC4';

  /* ── Toolbar definition (replaces PALETTE) ──────────────────────── */
  const TOOLBAR = [
    { key: 'table-round',  lbl: 'Round',     w: 80,  h: 80,  cap: 4 },
    { key: 'table-square', lbl: 'Square',    w: 72,  h: 72,  cap: 4 },
    { key: 'table-rect',   lbl: 'Rect',      w: 110, h: 60,  cap: 6 },
    { key: 'lounge',       lbl: 'Lounge',    w: 130, h: 64,  cap: 4 },
    { key: 'bar-seat',     lbl: 'Bar Seat',  w: 36,  h: 36,  cap: 1 },
    { key: 'bar-table',    lbl: 'Bar Table', w: 52,  h: 90,  cap: 4 },
    null, // separator
    { key: 'text_label',   lbl: 'Label',     w: 80,  h: 30,  cap: 0 },
    { key: '__zone',       lbl: 'Zone',      w: 0,   h: 0,   cap: 0, action: 'zone' },
  ];

  /* ── App state ──────────────────────────────────────────────────── */
  const S = {
    mode:        'live',
    floors:      [],
    floorId:     null,
    tables:      {},      // id → item
    liveStatus:  {},      // id → 'free'|'booked'|'occupied'|'blocked'
    floorStats:  {},      // floorId → { tables, seats, booked }
    selected:    null,
    selectedIds: new Set(), // for multi-select
    rubber:      null,    // rubber band state
    dirty:       false,
    date:        TODAY,
    time:        '',
    placing:     null,
    liveTimer:   null,
    undoStack:   [],
    redoStack:   [],
    bgConfig:    { url: null, scale: 1, opacity: 0.5, offsetX: 0, offsetY: 0 },
    snapEnabled: true,
  };

  /* ── Konva refs ─────────────────────────────────────────────────── */
  let stage, bgLayer, zoneLayer, tableLayer, transformer;

  /* ══════════════════════════════════════════════════════════════════
     BOOT
  ══════════════════════════════════════════════════════════════════ */
  document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('fp-app')) return;
    initKonva();
    bindHeader();
    bindFloorNav();
    bindSubbar();
    buildToolbar();
    bindCanvasTools();
    bindFloatPanel();
    bindToolbar();
    bindModal();
    bindKeyboard();
    bindBgControls();
    setDateLabel(S.date);
    setTimeLabel(S.time);
    loadFloors();
  });

  /* ══════════════════════════════════════════════════════════════════
     KONVA INIT
  ══════════════════════════════════════════════════════════════════ */
  function initKonva() {
    var wrap = document.getElementById('fp-canvas-wrap');
    var W = wrap ? wrap.clientWidth  : 800;
    var H = wrap ? wrap.clientHeight : 500;

    stage = new Konva.Stage({ container: 'fp-konva', width: W, height: H });

    // Background layer (non-interactive)
    bgLayer = new Konva.Layer({ listening: false });
    stage.add(bgLayer);

    // Zone layer (between bg and tables so zones render behind tables)
    zoneLayer = new Konva.Layer();
    stage.add(zoneLayer);

    tableLayer = new Konva.Layer();

    transformer = new Konva.Transformer({
      rotateEnabled: true,
      resizeEnabled: false,
      borderStroke: '#3B82F6',
      borderStrokeWidth: 1.5,
      anchorSize: 7,
      anchorCornerRadius: 3,
      anchorFill: '#FFFFFF',
      anchorStroke: '#3B82F6',
      padding: 4,
    });
    tableLayer.add(transformer);
    stage.add(tableLayer);

    var ro = new ResizeObserver(function () {
      if (!wrap) return;
      stage.width(wrap.clientWidth);
      stage.height(wrap.clientHeight);
      tableLayer.batchDraw();
    });
    ro.observe(wrap);

    // Mouse events for rubber band + placing
    stage.on('mousedown touchstart', function (e) {
      if (e.target !== stage) return;
      if (S.placing) return; // placing handled on click
      if (S.mode === 'builder') {
        var pos = stage.getPointerPosition();
        S.rubber = { x: pos.x, y: pos.y, active: false };
      }
    });

    stage.on('mousemove touchmove', function () {
      if (!S.rubber) return;
      var pos = stage.getPointerPosition();
      var x  = Math.min(S.rubber.x, pos.x);
      var y  = Math.min(S.rubber.y, pos.y);
      var w  = Math.abs(pos.x - S.rubber.x);
      var h  = Math.abs(pos.y - S.rubber.y);
      if (w > 4 || h > 4) {
        S.rubber.active = true;
        showRubberBand(x, y, w, h);
      }
    });

    stage.on('mouseup touchend', function (e) {
      if (S.rubber && S.rubber.active) {
        selectItemsInRect();
        hideRubberBand();
      }
      S.rubber = null;
    });

    stage.on('click tap', function (e) {
      if (e.target !== stage) return;
      if (S.placing) { placeTable(stage.getPointerPosition()); return; }
      if (!S.rubber || !S.rubber.active) deselect();
    });

    stage.on('wheel', function (e) {
      e.evt.preventDefault();
      zoomBy(e.evt.deltaY < 0 ? 1.1 : 1 / 1.1);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     RUBBER BAND SELECTION
  ══════════════════════════════════════════════════════════════════ */
  function showRubberBand(x, y, w, h) {
    var wrap = document.getElementById('fp-canvas-wrap');
    if (!wrap) return;
    var rb = document.getElementById('fp-rubber-band');
    if (!rb) {
      rb = document.createElement('div');
      rb.id = 'fp-rubber-band';
      rb.style.cssText = 'position:absolute;pointer-events:none;border:1.5px dashed #3B82F6;background:rgba(59,130,246,0.08);border-radius:2px;z-index:10;';
      wrap.appendChild(rb);
    }
    rb.hidden = false;
    rb.style.left   = x + 'px';
    rb.style.top    = y + 'px';
    rb.style.width  = w + 'px';
    rb.style.height = h + 'px';
  }

  function updateRubberBand(x, y, w, h) {
    var rb = document.getElementById('fp-rubber-band');
    if (!rb) return;
    rb.style.left   = x + 'px';
    rb.style.top    = y + 'px';
    rb.style.width  = w + 'px';
    rb.style.height = h + 'px';
  }

  function hideRubberBand() {
    var rb = document.getElementById('fp-rubber-band');
    if (rb) rb.hidden = true;
  }

  function selectItemsInRect() {
    if (!S.rubber) return;
    var pos = stage.getPointerPosition();
    var rx1 = Math.min(S.rubber.x, pos.x);
    var ry1 = Math.min(S.rubber.y, pos.y);
    var rx2 = Math.max(S.rubber.x, pos.x);
    var ry2 = Math.max(S.rubber.y, pos.y);

    // Convert canvas px to stage coords
    var scale = stage.scaleX();
    var stPos = stage.position();
    var sx1 = (rx1 - stPos.x) / scale;
    var sy1 = (ry1 - stPos.y) / scale;
    var sx2 = (rx2 - stPos.x) / scale;
    var sy2 = (ry2 - stPos.y) / scale;

    var found = [];
    Object.values(S.tables).forEach(function (item) {
      if (item.type === 'zone') return;
      var cx = toNum(item.pos_x, 0);
      var cy = toNum(item.pos_y, 0);
      if (cx >= sx1 && cx <= sx2 && cy >= sy1 && cy <= sy2) {
        found.push(String(item.id));
      }
    });

    if (found.length === 0) {
      deselect();
    } else if (found.length === 1) {
      selectTable(found[0]);
    } else {
      S.selected = null;
      S.selectedIds = new Set(found);
      var nodes = found.map(function (id) { return stage.findOne('#tbl-' + id); }).filter(Boolean);
      if (S.mode === 'builder') { transformer.nodes(nodes); tableLayer.batchDraw(); }
      updateZoneButton();
      showMultiSelectFloat(found.length);
    }
  }

  /* ══════════════════════════════════════════════════════════════════
     OVERLAP DETECTION
  ══════════════════════════════════════════════════════════════════ */
  function tableAABB(item) {
    var w = toNum(item.width, 80);
    var h = toNum(item.height, 80);
    return { x: toNum(item.pos_x, 0) - w / 2, y: toNum(item.pos_y, 0) - h / 2, w: w, h: h };
  }

  function aabbOverlap(a, b, pad) {
    pad = pad || 0;
    return !(
      a.x + a.w + pad <= b.x ||
      b.x + b.w + pad <= a.x ||
      a.y + a.h + pad <= b.y ||
      b.y + b.h + pad <= a.y
    );
  }

  function isOverlappingAny(x, y, w, h, excludeId) {
    var test = { x: x - w / 2, y: y - h / 2, w: w, h: h };
    return Object.values(S.tables).some(function (other) {
      if (excludeId !== undefined && String(other.id) === String(excludeId)) return false;
      if (other.type === 'text_label' || other.type === 'zone') return false;
      return aabbOverlap(test, tableAABB(other), 4);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     CUSTOM MODAL (replaces window.prompt / confirm / alert)
  ══════════════════════════════════════════════════════════════════ */
  var _modalResolve = null;

  function fpModal(opts) {
    return new Promise(function (resolve) {
      _modalResolve = resolve;
      var overlay  = document.getElementById('fp-modal');
      var titleEl  = document.getElementById('fp-modal-title');
      var bodyEl   = document.getElementById('fp-modal-body');
      var okBtn    = document.getElementById('fp-modal-ok');
      var cancelBtn= document.getElementById('fp-modal-cancel');
      if (!overlay) { resolve(opts.type === 'confirm' ? false : null); return; }

      titleEl.textContent = opts.title || '';
      bodyEl.innerHTML    = opts.body  || '';
      okBtn.textContent   = opts.ok    || 'OK';
      cancelBtn.textContent = opts.cancel || 'Cancel';
      cancelBtn.hidden    = (opts.type === 'alert');
      okBtn.className     = 'fp-btn ' + (opts.danger ? 'fp-btn-modal-danger' : 'fp-btn-primary');

      overlay.hidden = false;

      if (opts.type === 'prompt') {
        var inp = bodyEl.querySelector('.fp-modal-input');
        if (inp) setTimeout(function () { inp.focus(); inp.select(); }, 60);
      } else {
        setTimeout(function () { okBtn.focus(); }, 60);
      }
    });
  }

  function _closeModal(result) {
    var overlay = document.getElementById('fp-modal');
    if (overlay) overlay.hidden = true;
    if (_modalResolve) { _modalResolve(result); _modalResolve = null; }
  }

  function bindModal() {
    var okBtn    = document.getElementById('fp-modal-ok');
    var cancelBtn= document.getElementById('fp-modal-cancel');
    var overlay  = document.getElementById('fp-modal');
    if (!overlay) return;

    okBtn.addEventListener('click', function () {
      var inp = document.querySelector('#fp-modal-body .fp-modal-input');
      _closeModal(inp ? (inp.value.trim() || null) : true);
    });
    cancelBtn.addEventListener('click', function () { _closeModal(null); });

    // Close on backdrop click
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) _closeModal(null);
    });

    // Enter/Esc keyboard shortcuts
    overlay.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        e.preventDefault();
        okBtn.click();
      }
      if (e.key === 'Escape') { e.preventDefault(); _closeModal(null); }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     TOAST NOTIFICATIONS
  ══════════════════════════════════════════════════════════════════ */
  function showToast(msg, type) {
    var toast = document.getElementById('fp-toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.className = 'fp-toast fp-toast--' + (type || 'info') + ' fp-toast--visible';
    clearTimeout(toast._t);
    toast._t = setTimeout(function () {
      toast.classList.remove('fp-toast--visible');
    }, 2500);
  }

  /* ══════════════════════════════════════════════════════════════════
     FLOOR LOADING
  ══════════════════════════════════════════════════════════════════ */
  function loadFloors() {
    apiFetch('GET', 'floor-plans').then(function (data) {
      S.floors = Array.isArray(data) ? data : (data.data || []);
      renderFloorTabs();
      if (S.floors.length) {
        selectFloor(S.floors[0].id);
        S.floors.forEach(function (f) { loadFloorStats(f.id); });
      } else {
        showEmptyState(true);
      }
    }).catch(function () {
      S.floors = [];
      renderFloorTabs();
      showEmptyState(true);
    });
  }

  function selectFloor(id) {
    S.floorId = id;
    document.querySelectorAll('.fp-floor-tab').forEach(function (el) {
      el.classList.toggle('fp-floor-tab--active', String(el.dataset.floorId) === String(id));
    });
    var floor = S.floors.find(function (f) { return String(f.id) === String(id); });
    loadBackground(floor || null);
    loadFurniture(id);
    deselect();
  }

  /* ── Render floor tabs ──────────────────────────────────────────── */
  function renderFloorTabs() {
    var container = document.getElementById('fp-floor-tabs');
    if (!container) return;
    container.innerHTML = '';

    S.floors.forEach(function (floor, i) {
      var iconLabel = i === 0 ? 'G' : String(i);
      var tab = document.createElement('div');
      tab.className = 'fp-floor-tab';
      tab.dataset.floorId = floor.id;
      tab.innerHTML =
        '<div class="fp-floor-tab-inner">' +
          '<span class="fp-floor-icon">' + escHtml(iconLabel) + '</span>' +
          '<span class="fp-floor-tab-name">' + escHtml(floor.name) + '</span>' +
        '</div>' +
        '<div class="fp-floor-popup">' +
          '<div class="fp-fpopup-title">' + escHtml(floor.name) + '</div>' +
          '<div class="fp-fpopup-stats">' +
            '<div class="fp-fpopup-row"><span class="fp-fpopup-label">Tables</span><span class="fp-fpopup-num" data-stat="tables">—</span></div>' +
            '<div class="fp-fpopup-row"><span class="fp-fpopup-label">Seat capacity</span><span class="fp-fpopup-num" data-stat="seats">—</span></div>' +
          '</div>' +
          '<div class="fp-fpopup-divider"></div>' +
          '<div class="fp-fpopup-booked">' +
            '<span class="fp-fpopup-label">Booked today</span>' +
            '<span class="fp-fpopup-num" data-stat="booked">—</span>' +
          '</div>' +
        '</div>';

      tab.querySelector('.fp-floor-tab-inner').addEventListener('click', function () {
        selectFloor(floor.id);
      });
      container.appendChild(tab);
    });
  }

  /* ── Load popup stats ───────────────────────────────────────────── */
  function loadFloorStats(floorId) {
    apiFetch('GET', 'floor-plans/' + floorId + '/furniture').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      var tables = items.filter(function (i) { return !isZone(i.type) && i.type !== 'text_label'; }).length;
      var seats  = items.reduce(function (s, i) { return s + (parseInt(i.capacity_max) || 0); }, 0);
      if (!S.floorStats[floorId]) S.floorStats[floorId] = {};
      S.floorStats[floorId].tables = tables;
      S.floorStats[floorId].seats  = seats;
      updateFloorPopup(floorId);
    });

    apiFetch('GET', 'reservations?date=' + S.date + '&per_page=100').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      var booked = items.filter(function (r) {
        return r.floor_plan_id === floorId || r.floor_plan_id === null;
      }).length;
      if (!S.floorStats[floorId]) S.floorStats[floorId] = {};
      S.floorStats[floorId].booked = booked;
      updateFloorPopup(floorId);
    }).catch(function () {});
  }

  function updateFloorPopup(floorId) {
    var stats = S.floorStats[floorId];
    if (!stats) return;
    var tab = document.querySelector('.fp-floor-tab[data-floor-id="' + floorId + '"]');
    if (!tab) return;
    var popup = tab.querySelector('.fp-floor-popup');
    if (!popup) return;
    ['tables', 'seats', 'booked'].forEach(function (k) {
      var el = popup.querySelector('[data-stat="' + k + '"]');
      if (el && stats[k] !== undefined) el.textContent = String(stats[k]);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     BACKGROUND IMAGE LAYER
  ══════════════════════════════════════════════════════════════════ */
  function loadBackground(floor) {
    bgLayer.destroyChildren();
    bgLayer.batchDraw();

    if (!floor || !floor.background_url) {
      S.bgConfig = { url: null, scale: 1, opacity: 0.5, offsetX: 0, offsetY: 0 };
      updateBgControls();
      return;
    }

    S.bgConfig = {
      url:     floor.background_url,
      scale:   toNum(floor.bg_scale,    1),
      opacity: toNum(floor.bg_opacity,  0.5),
      offsetX: toNum(floor.bg_offset_x, 0),
      offsetY: toNum(floor.bg_offset_y, 0),
    };

    var img = new window.Image();
    img.crossOrigin = 'anonymous';
    img.onload = function () {
      var konvaImg = new Konva.Image({
        image:   img,
        x:       S.bgConfig.offsetX,
        y:       S.bgConfig.offsetY,
        scaleX:  S.bgConfig.scale,
        scaleY:  S.bgConfig.scale,
        opacity: S.bgConfig.opacity,
        listening: false,
        draggable: false,
        name: 'bg-image',
      });
      S.bgKonvaImg = konvaImg;
      bgLayer.add(konvaImg);
      bgLayer.batchDraw();
    };
    img.onerror = function () { showToast('Background image failed to load', 'warn'); };
    img.src = floor.background_url;

    updateBgControls();
  }

  function updateBgControls() {
    var hasUrl    = !!S.bgConfig.url;
    var sliders   = document.getElementById('fp-bg-sliders');
    var noThumb   = document.getElementById('fp-bg-no-thumb');
    var thumb     = document.getElementById('fp-bg-thumb');
    var opSlider  = document.getElementById('fp-bg-opacity');
    var scSlider  = document.getElementById('fp-bg-scale');
    var dot       = document.getElementById('fp-bg-nav-dot');
    var navBtn    = document.getElementById('fp-bg-nav-btn');

    if (sliders)  sliders.hidden  = !hasUrl;
    if (noThumb)  noThumb.hidden  = hasUrl;
    if (thumb) {
      thumb.hidden = !hasUrl;
      if (hasUrl) thumb.src = S.bgConfig.url;
    }
    if (opSlider) opSlider.value = String(S.bgConfig.opacity);
    if (scSlider) scSlider.value = String(S.bgConfig.scale);
    if (dot) dot.hidden = !hasUrl;
    if (navBtn) navBtn.classList.toggle('fp-bg-nav-btn--has-bg', hasUrl);
  }

  function applyBgUpdate(patch) {
    Object.assign(S.bgConfig, patch);
    if (S.bgKonvaImg) {
      if (patch.opacity  !== undefined) S.bgKonvaImg.opacity(patch.opacity);
      if (patch.scale    !== undefined) { S.bgKonvaImg.scaleX(patch.scale); S.bgKonvaImg.scaleY(patch.scale); }
      if (patch.offsetX  !== undefined) S.bgKonvaImg.x(patch.offsetX);
      if (patch.offsetY  !== undefined) S.bgKonvaImg.y(patch.offsetY);
      bgLayer.batchDraw();
    }
    clearTimeout(applyBgUpdate._t);
    applyBgUpdate._t = setTimeout(function () {
      if (!S.floorId || !S.bgConfig.url) return;
      apiFetch('PATCH', 'floor-plans/' + S.floorId, {
        bg_scale:    S.bgConfig.scale,
        bg_opacity:  S.bgConfig.opacity,
        bg_offset_x: S.bgConfig.offsetX,
        bg_offset_y: S.bgConfig.offsetY,
      }).catch(function () {});
    }, 800);
  }

  function removeBg() {
    if (!S.floorId) return;
    apiFetch('PATCH', 'floor-plans/' + S.floorId, {
      bg_scale: 1, bg_opacity: 0.5, bg_offset_x: 0, bg_offset_y: 0,
    }).catch(function () {});
    bgLayer.destroyChildren();
    bgLayer.batchDraw();
    S.bgConfig = { url: null, scale: 1, opacity: 0.5, offsetX: 0, offsetY: 0 };
    S.bgKonvaImg = null;
    var floor = S.floors.find(function (f) { return String(f.id) === String(S.floorId); });
    if (floor) floor.background_url = null;
    updateBgControls();
    showToast('Background removed', 'info');
  }

  function bindBgControls() {
    var fileInput = document.getElementById('fp-bg-file');
    var uploadBtn = document.getElementById('fp-bg-upload');
    var opSlider  = document.getElementById('fp-bg-opacity');
    var scSlider  = document.getElementById('fp-bg-scale');
    var removeBtn = document.getElementById('fp-bg-remove');
    var navBtn    = document.getElementById('fp-bg-nav-btn');
    var popover   = document.getElementById('fp-bg-popover');

    if (navBtn && popover) {
      navBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        var isOpen = !popover.hidden;
        popover.hidden = isOpen;
        navBtn.classList.toggle('fp-bg-nav-btn--open', !isOpen);
      });
      document.addEventListener('click', function (e) {
        if (!popover.hidden && !popover.contains(e.target) && e.target !== navBtn) {
          popover.hidden = true;
          navBtn.classList.remove('fp-bg-nav-btn--open');
        }
      });
    }

    if (uploadBtn && fileInput) {
      var uploadLbl = uploadBtn.querySelector('span');
      uploadBtn.addEventListener('click', function () { fileInput.click(); });
      fileInput.addEventListener('change', function () {
        var file = fileInput.files[0];
        if (!file || !S.floorId) return;
        var allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
          showToast('Please upload a JPEG, PNG, or WebP image', 'warn');
          fileInput.value = '';
          return;
        }
        if (file.size > 20 * 1024 * 1024) {
          showToast('File must be under 20 MB', 'warn');
          fileInput.value = '';
          return;
        }
        if (uploadLbl) uploadLbl.textContent = 'Uploading…';
        uploadBtn.disabled = true;
        var fd = new FormData();
        fd.append('background', file);
        fetch(API + 'floor-plans/' + S.floorId + '/background', {
          method: 'POST',
          headers: { 'X-WP-Nonce': NONCE },
          body: fd,
        }).then(function (r) {
          if (!r.ok) return r.json().then(function (e) { throw e; });
          return r.json();
        }).then(function (floor) {
          var idx = S.floors.findIndex(function (f) { return String(f.id) === String(S.floorId); });
          if (idx >= 0) S.floors[idx] = floor;
          loadBackground(floor);
          showToast('Background uploaded', 'info');
        }).catch(function (e) {
          showToast((e && e.message) ? e.message : 'Upload failed', 'warn');
        }).finally(function () {
          if (uploadLbl) uploadLbl.textContent = 'Upload image';
          uploadBtn.disabled = false;
          fileInput.value = '';
        });
      });
    }

    if (opSlider) {
      opSlider.addEventListener('input', function () {
        applyBgUpdate({ opacity: parseFloat(this.value) });
      });
    }

    if (scSlider) {
      scSlider.addEventListener('input', function () {
        applyBgUpdate({ scale: parseFloat(this.value) });
      });
    }

    if (removeBtn) {
      removeBtn.addEventListener('click', function () {
        removeBg();
        if (popover) { popover.hidden = true; }
        if (navBtn) navBtn.classList.remove('fp-bg-nav-btn--open');
      });
    }
  }

  /* ══════════════════════════════════════════════════════════════════
     FURNITURE LOADING + RENDERING
  ══════════════════════════════════════════════════════════════════ */
  function loadFurniture(floorId) {
    S.tables = {};
    clearCanvas();
    showEmptyState(false);

    apiFetch('GET', 'floor-plans/' + floorId + '/furniture').then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      items.forEach(function (item) { S.tables[item.id] = item; });
      renderAllTables();
      renderZoneLayer();
      updateStatusBar();
      if (S.mode === 'live') startLiveUpdates();
      if (!items.length) showEmptyState(true);
    }).catch(function () {
      showEmptyState(true);
    });
  }

  function renderAllTables() {
    clearCanvas();
    Object.values(S.tables).forEach(function (item) { addTableNode(item); });
    tableLayer.batchDraw();
  }

  function clearCanvas() {
    tableLayer.getChildren(function (n) { return n !== transformer; }).forEach(function (n) { n.destroy(); });
    tableLayer.batchDraw();
  }

  /* ── Build and add a table / label to the layer ─────────────────── */
  function addTableNode(item, autoSelect) {
    // Zones are rendered in zoneLayer, not tableLayer
    if (item.type === 'zone') return null;

    var group = new Konva.Group({
      id:        'tbl-' + item.id,
      x:         toNum(item.pos_x, 200),
      y:         toNum(item.pos_y, 200),
      rotation:  toNum(item.rotation_deg, 0),
      draggable: S.mode === 'builder',
      name:      'table-group',
    });

    if (item.type === 'text_label')   { drawTextLabel(group, item); }
    else if (isBarSeat(item))         { drawBarSeat(group, item); }
    else if (isRound(item))           { drawRoundTable(group, item); }
    else if (isLounge(item))          { drawLounge(group, item); }
    else if (isBooth(item.type))      { drawBoothTable(group, item); }
    else                              { drawRectTable(group, item); }

    group.on('click tap', function (e) {
      e.cancelBubble = true;
      if (S.placing) return;
      if (e.evt && e.evt.shiftKey && S.mode === 'builder') {
        toggleMultiSelect(String(item.id));
      } else {
        selectTable(item.id);
      }
    });

    group.on('dblclick dbltap', function (e) {
      e.cancelBubble = true;
      if (S.mode !== 'builder') return;
      startInlineLabelEdit(item.id, group);
    });

    group.on('dragstart', function () {
      pushUndo(item.id);
    });

    group.on('dragend', function () {
      var d = S.tables[item.id];
      if (!d) return;
      var nx = snapToGrid(this.x());
      var ny = snapToGrid(this.y());

      if (d.type !== 'text_label' && d.type !== 'zone' && isOverlappingAny(nx, ny, toNum(d.width, 80), toNum(d.height, 80), item.id)) {
        this.position({ x: d.pos_x, y: d.pos_y });
        if (transformer.nodes().indexOf(this) !== -1) {
          transformer.nodes([this]);
        }
        tableLayer.draw();
        showToast('Tables cannot overlap', 'warn');
        S.undoStack.pop();
        syncUndoBtns();
        return;
      }

      d.pos_x = nx;
      d.pos_y = ny;
      this.position({ x: d.pos_x, y: d.pos_y });
      markDirty();
      tableLayer.batchDraw();
      renderZoneLayer(); // update zone overlays on drag
    });

    group.on('transformend', function () {
      var d = S.tables[item.id];
      if (!d) return;
      d.pos_x        = Math.round(this.x());
      d.pos_y        = Math.round(this.y());
      d.rotation_deg = Math.round(this.rotation());
      markDirty();
      renderZoneLayer();
    });

    tableLayer.add(group);
    tableLayer.moveToTop(transformer);

    if (autoSelect) selectTable(item.id);
    return group;
  }

  /* ── Inline label editing ────────────────────────────────────────── */
  function startInlineLabelEdit(id, group) {
    var item = S.tables[id];
    if (!item) return;

    var absPos   = group.getAbsolutePosition();
    var stageBox = stage.container().getBoundingClientRect();
    var scale    = stage.scaleX();

    var input = document.createElement('input');
    input.value = item.label || '';
    input.style.cssText = [
      'position:fixed',
      'top:'    + (stageBox.top  + absPos.y * scale - 12) + 'px',
      'left:'   + (stageBox.left + absPos.x * scale - 30) + 'px',
      'width:80px',
      'font-size:12px',
      'font-family:system-ui,sans-serif',
      'font-weight:700',
      'text-align:center',
      'padding:2px 6px',
      'border:2px solid #3B82F6',
      'border-radius:4px',
      'background:#fff',
      'color:#0F1523',
      'z-index:99999',
      'outline:none',
    ].join(';');
    document.body.appendChild(input);
    input.focus();
    input.select();

    function commit() {
      var val = input.value.trim() || item.label;
      input.remove();
      if (val === item.label) return;
      item.label = val;
      var node = stage.findOne('#tbl-' + id);
      if (node) { node.destroy(); }
      addTableNode(item, false);
      if (S.selected === id) selectTable(id);
      tableLayer.batchDraw();
      markDirty();
      var title = document.getElementById('fp-fp-title');
      if (title) title.textContent = val;
    }
    input.addEventListener('blur',    commit);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter')  { e.preventDefault(); commit(); }
      if (e.key === 'Escape') { input.removeEventListener('blur', commit); input.remove(); }
    });
  }

  /* ── Round table ────────────────────────────────────────────────── */
  function addChair(group, cx, cy, cW, cH, angleDeg, fill) {
    var rBack = cW / 2;
    group.add(new Konva.Rect({
      x: cx, y: cy,
      width: cW, height: cH,
      offsetX: cW / 2, offsetY: cH / 2,
      cornerRadius: [rBack, rBack, 3, 3],
      fill: fill,
      rotation: angleDeg,
      listening: false,
    }));
  }

  function drawRoundTable(group, item) {
    var st    = liveStyle(item);
    var r     = Math.max(24, Math.min(52, toNum(item.width, 70) / 2));
    var cap   = Math.max(1, parseInt(item.capacity_max) || 4);
    var cW    = Math.max(10, r * 0.34);
    var cH    = Math.max(13, r * 0.48);
    var gap   = 4;
    var cDist = r + gap + cH / 2;

    for (var i = 0; i < cap; i++) {
      var a    = (i / cap) * Math.PI * 2 - Math.PI / 2;
      var cx   = Math.cos(a) * cDist;
      var cy   = Math.sin(a) * cDist;
      var deg  = a * 180 / Math.PI + 90;
      addChair(group, cx, cy, cW, cH, deg, st.chair);
    }

    group.add(new Konva.Circle({
      radius: r,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.15)', shadowBlur: 6, shadowOffsetY: 2,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: Math.max(11, r * 0.42),
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: r * 2, height: r * 2,
      offsetX: r, offsetY: r,
      listening: false,
    }));
  }

  /* ── Rect table ─────────────────────────────────────────────────── */
  function drawRectTable(group, item) {
    var st   = liveStyle(item);
    var W    = toNum(item.width,  90);
    var H    = toNum(item.height, 56);
    var cap  = Math.max(1, parseInt(item.capacity_max) || 4);
    var top  = Math.ceil(cap / 2);
    var bot  = Math.floor(cap / 2);
    var cW   = 12, cH = 14, gap = 4;

    for (var i = 0; i < top; i++) {
      var sp = W / (top + 1);
      var cx = -W / 2 + sp * (i + 1);
      var cy = -H / 2 - gap - cH / 2;
      addChair(group, cx, cy, cW, cH, 0, st.chair);
    }
    for (var j = 0; j < bot; j++) {
      var sp2 = W / (bot + 1);
      var cx2 = -W / 2 + sp2 * (j + 1);
      var cy2 = H / 2 + gap + cH / 2;
      addChair(group, cx2, cy2, cW, cH, 180, st.chair);
    }

    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2,
      width: W, height: H,
      cornerRadius: 6,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.12)', shadowBlur: 5, shadowOffsetY: 2,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 13,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: W, height: H,
      offsetX: W / 2, offsetY: H / 2,
      listening: false,
    }));
  }

  /* ── Booth ──────────────────────────────────────────────────────── */
  function drawBoothTable(group, item) {
    var st  = liveStyle(item);
    var W   = toNum(item.width, 100);
    var H   = toNum(item.height, 60);
    var bH  = 14;

    group.add(new Konva.Rect({ x: -W / 2, y: -H / 2 - bH, width: W, height: bH, cornerRadius: [6, 6, 0, 0], fill: st.chair, listening: false }));
    group.add(new Konva.Rect({ x: -W / 2, y: H / 2, width: W, height: bH, cornerRadius: [0, 0, 6, 6], fill: st.chair, listening: false }));

    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2,
      width: W, height: H,
      cornerRadius: 4,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      listening: false,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 13,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: W, height: H,
      offsetX: W / 2, offsetY: H / 2,
      listening: false,
    }));
  }

  /* ── Lounge / sofa ──────────────────────────────────────────────── */
  function drawLounge(group, item) {
    var st = liveStyle(item);
    var W  = toNum(item.width, 130);
    var H  = toNum(item.height, 64);

    // Backrest at top
    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2 - 14,
      width: W, height: 14,
      cornerRadius: [6, 6, 0, 0],
      fill: st.chair,
      listening: false,
    }));

    // Main body
    group.add(new Konva.Rect({
      x: -W / 2, y: -H / 2,
      width: W, height: H,
      cornerRadius: 6,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.12)', shadowBlur: 5, shadowOffsetY: 2,
      listening: false,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 13,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: W, height: H,
      offsetX: W / 2, offsetY: H / 2,
      listening: false,
    }));
  }

  /* ── Bar seat / stool ───────────────────────────────────────────── */
  function drawBarSeat(group, item) {
    var st = liveStyle(item);
    var r  = 15;

    // Stem below circle
    group.add(new Konva.Rect({
      x: -2, y: r,
      width: 4, height: 10,
      cornerRadius: 2,
      fill: st.chair,
      listening: false,
    }));

    // Seat circle
    group.add(new Konva.Circle({
      radius: r,
      fill: st.fill, stroke: st.stroke, strokeWidth: 1.5,
      shadowColor: 'rgba(0,0,0,0.15)', shadowBlur: 4, shadowOffsetY: 1,
    }));

    group.add(new Konva.Text({
      text: item.label || '?',
      fontSize: 10,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: 'bold',
      fill: '#FFFFFF',
      align: 'center',
      verticalAlign: 'middle',
      width: r * 2, height: r * 2,
      offsetX: r, offsetY: r,
      listening: false,
    }));
  }

  /* ── Text label ─────────────────────────────────────────────────── */
  function drawTextLabel(group, item) {
    var meta     = item.meta || {};
    var fontSize = toNum(meta.fontSize, 15);
    var color    = meta.color || '#1a1a1a';
    var text     = item.label || 'Label';
    var bgColor  = meta.bgColor || '';

    if (bgColor) {
      group.add(new Konva.Rect({
        x: -4, y: -fontSize / 2 - 4,
        width: text.length * fontSize * 0.62 + 8,
        height: fontSize + 8,
        cornerRadius: 4,
        fill: bgColor,
        opacity: 0.85,
        listening: false,
      }));
    }

    group.add(new Konva.Text({
      text: text,
      fontSize: fontSize,
      fontFamily: 'system-ui,-apple-system,sans-serif',
      fontStyle: meta.bold ? 'bold' : 'normal',
      fill: color,
      listening: false,
      shadowColor: 'rgba(0,0,0,0.3)',
      shadowBlur: 3,
      shadowOffsetY: 1,
    }));
  }

  /* ── Resolve live vs builder style ─────────────────────────────── */
  function liveStyle(item) {
    if (S.mode !== 'live') return { fill: TABLE_FILL, stroke: TABLE_STROKE, chair: CHAIR_FILL };
    if (item.is_available === '0' || item.is_available === 0) return STATUS.blocked;
    var key = S.liveStatus[item.id] || 'free';
    return STATUS[key] || STATUS.free;
  }

  /* ══════════════════════════════════════════════════════════════════
     ZONE LAYER
  ══════════════════════════════════════════════════════════════════ */
  function renderZoneLayer() {
    zoneLayer.destroyChildren();

    Object.values(S.tables).forEach(function (item) {
      if (item.type !== 'zone') return;
      var meta    = item.meta || {};
      var members = meta.members || [];
      var color   = meta.color || '#EAF5EE';

      if (!members.length) return;

      // Compute AABB from member positions
      var minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
      members.forEach(function (mid) {
        var m = S.tables[mid];
        if (!m) return;
        var mW = toNum(m.width, 80) / 2;
        var mH = toNum(m.height, 80) / 2;
        var mx = toNum(m.pos_x, 0);
        var my = toNum(m.pos_y, 0);
        minX = Math.min(minX, mx - mW);
        minY = Math.min(minY, my - mH);
        maxX = Math.max(maxX, mx + mW);
        maxY = Math.max(maxY, my + mH);
      });

      if (!isFinite(minX)) return;

      var pad = 20;
      var zx  = minX - pad;
      var zy  = minY - pad;
      var zw  = (maxX - minX) + pad * 2;
      var zh  = (maxY - minY) + pad * 2;

      var rect = new Konva.Rect({
        x: zx, y: zy,
        width: zw, height: zh,
        cornerRadius: 8,
        fill: color,
        opacity: 0.25,
        stroke: color,
        strokeWidth: 1.5,
        dash: [8, 4],
        name: 'zone-rect',
      });

      var label = new Konva.Text({
        x: zx + 8, y: zy + 6,
        text: item.label || 'Zone',
        fontSize: 11,
        fontFamily: 'system-ui,-apple-system,sans-serif',
        fontStyle: 'bold',
        fill: '#1a4a2e',
        opacity: 0.8,
        listening: false,
      });

      // Clickable zone group
      var zGroup = new Konva.Group({ id: 'zone-' + item.id, name: 'zone-group' });
      zGroup.add(rect);
      zGroup.add(label);

      zGroup.on('click tap', function (e) {
        e.cancelBubble = true;
        if (S.mode === 'builder') selectZone(item.id);
      });

      zoneLayer.add(zGroup);
    });

    zoneLayer.batchDraw();
  }

  function createZone() {
    if (S.selectedIds.size < 2 || S.mode !== 'builder') return;
    fpModal({
      type:  'prompt',
      title: 'Create Zone',
      body:  '<p>Name this zone to group the selected tables into a labelled area.</p>' +
             '<input class="fp-modal-input" type="text" placeholder=\'e.g. VIP, Outdoor, Bar Area\' maxlength="40">',
      ok:    'Create Zone',
    }).then(function (name) {
      if (!name) return;
      var payload = {
        type:         'zone',
        label:        name,
        pos_x:        0,
        pos_y:        0,
        width:        0,
        height:       0,
        rotation_deg: 0,
        capacity_min: 0,
        capacity_max: 0,
        meta:         { color: '#EAF5EE', members: Array.from(S.selectedIds) },
      };
      apiFetch('POST', 'floor-plans/' + S.floorId + '/furniture', payload).then(function (item) {
        S.tables[item.id] = item;
        renderZoneLayer();
        deselect();
        showToast('Zone "' + item.label + '" created', 'info');
      }).catch(function (err) {
        console.warn('Create zone failed', err);
      });
    });
  }

  function deleteZone(id) {
    apiFetch('DELETE', 'furniture/' + id).then(function () {
      delete S.tables[id];
      renderZoneLayer();
      deselect();
    }).catch(function (err) { console.warn('Delete zone failed', err); });
  }

  function selectZone(id) {
    S.selected = id;
    S.selectedIds = new Set();
    showFloatPanel(id);
    // No transformer for zones
    transformer.nodes([]);
    tableLayer.batchDraw();
  }

  /* ══════════════════════════════════════════════════════════════════
     SELECTION
  ══════════════════════════════════════════════════════════════════ */
  function selectTable(id) {
    S.selected = id;
    S.selectedIds = new Set();
    var node = stage.findOne('#tbl-' + id);
    if (node && S.mode === 'builder') {
      transformer.nodes([node]);
      tableLayer.batchDraw();
    }
    showFloatPanel(id);
    var delBtn = document.getElementById('fp-delete-sel');
    if (delBtn) delBtn.disabled = false;
    // Position float panel after DOM update
    if (node) {
      setTimeout(function () { positionFloatPanel(node); }, 10);
    }
    updateZoneButton();
  }

  function deselect() {
    S.selected    = null;
    S.selectedIds = new Set();
    transformer.nodes([]);
    tableLayer.batchDraw();
    hideFloatPanel();
    var delBtn = document.getElementById('fp-delete-sel');
    if (delBtn) delBtn.disabled = true;
    updateZoneButton();
  }

  /* ── Multi-select ────────────────────────────────────────────────── */
  function toggleMultiSelect(id) {
    S.selected = null;
    if (S.selectedIds.has(id)) {
      S.selectedIds.delete(id);
    } else {
      S.selectedIds.add(id);
    }

    if (S.selectedIds.size === 0) {
      deselect();
      return;
    }

    var nodes = Array.from(S.selectedIds).map(function (sid) {
      return stage.findOne('#tbl-' + sid);
    }).filter(Boolean);

    if (S.mode === 'builder') { transformer.nodes(nodes); tableLayer.batchDraw(); }
    updateZoneButton();

    if (S.selectedIds.size === 1) {
      selectTable(Array.from(S.selectedIds)[0]);
    } else {
      showMultiSelectFloat(S.selectedIds.size);
    }
  }

  function showMultiSelectFloat(count) {
    var panel = document.getElementById('fp-float-panel');
    if (!panel) return;
    panel.hidden = false;
    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = count + ' items selected';
    var body = document.getElementById('fp-fp-body');
    if (body) body.innerHTML = '<p style="font-size:13px;color:#6B7280;margin:8px 0;">Use Shift+click to add/remove. Press Ctrl+D to duplicate all selected.</p>';
    var delBtn = document.getElementById('fp-fp-delete');
    if (delBtn) delBtn.disabled = true;
    var dupBtn = document.getElementById('fp-tb-dup');
    if (dupBtn) dupBtn.disabled = false;
  }

  function updateZoneButton() {
    var zoneBtn = document.getElementById('fp-tb-zone-btn');
    if (zoneBtn) zoneBtn.disabled = S.selectedIds.size < 2;
  }

  /* ══════════════════════════════════════════════════════════════════
     TOOLBAR
  ══════════════════════════════════════════════════════════════════ */
  function buildToolbar() {
    var bar = document.getElementById('fp-toolbar');
    if (!bar) return;
    bar.innerHTML = '';

    TOOLBAR.forEach(function (def) {
      if (def === null) {
        var sep = document.createElement('div');
        sep.className = 'fp-tb-sep';
        bar.appendChild(sep);
        return;
      }

      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'fp-tb-item';
      btn.dataset.typeKey = def.key;
      if (def.key === '__zone') btn.id = 'fp-tb-zone-btn';
      btn.innerHTML =
        '<span class="fp-tb-icon">' + toolbarIcon(def.key) + '</span>' +
        '<span class="fp-tb-lbl">' + escHtml(def.lbl) + '</span>';

      if (def.action === 'zone') {
        btn.disabled = true;
        btn.addEventListener('click', function () { createZone(); });
      } else {
        btn.addEventListener('click', function () { startPlacing(def.key, btn); });
      }

      bar.appendChild(btn);
    });

    // Right side controls
    var right = document.createElement('div');
    right.className = 'fp-tb-right';
    right.innerHTML =
      '<button type="button" id="fp-tb-snap" class="fp-tb-ctrl fp-tb-ctrl--active" title="Snap to grid">' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="10" y="1" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="1" y="10" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/><rect x="10" y="10" width="5" height="5" rx="1" stroke="currentColor" stroke-width="1.3"/></svg>' +
        '<span>Snap</span>' +
      '</button>' +
      '<button type="button" id="fp-tb-dup" class="fp-tb-ctrl" title="Duplicate (Ctrl+D)" disabled>' +
        '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="1" y="5" width="10" height="10" rx="1.5" stroke="currentColor" stroke-width="1.3"/><path d="M5 5V3a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v7a1 1 0 0 0-1 1h-2" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>' +
        '<span>Dupe</span>' +
      '</button>' +
      '<span id="fp-tb-status" class="fp-tb-status"></span>';
    bar.appendChild(right);

    updateStatusBar();
  }

  function toolbarIcon(key) {
    switch (key) {
      case 'table-round':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><circle cx="14" cy="14" r="6" fill="currentColor" opacity=".85"/><circle cx="14" cy="4" r="2.5" fill="currentColor" opacity=".4"/><circle cx="14" cy="24" r="2.5" fill="currentColor" opacity=".4"/><circle cx="4" cy="14" r="2.5" fill="currentColor" opacity=".4"/><circle cx="24" cy="14" r="2.5" fill="currentColor" opacity=".4"/></svg>';
      case 'table-square':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="8" y="8" width="12" height="12" rx="1.5" fill="currentColor" opacity=".85"/><rect x="4" y="3.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="18" y="3.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="4" y="20.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="18" y="20.5" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/></svg>';
      case 'table-rect':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="4" y="9" width="20" height="10" rx="1.5" fill="currentColor" opacity=".85"/><rect x="5" y="4" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="12" y="4" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="17" y="4" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="5" y="20" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="12" y="20" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="17" y="20" width="6" height="4" rx="1.5" fill="currentColor" opacity=".4"/></svg>';
      case 'lounge':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="3" y="11" width="22" height="10" rx="3" fill="currentColor" opacity=".85"/><rect x="2" y="8" width="24" height="6" rx="3" fill="currentColor" opacity=".45"/></svg>';
      case 'bar-seat':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><circle cx="14" cy="17" r="6" fill="currentColor" opacity=".85"/><rect x="13" y="4" width="2" height="9" rx="1" fill="currentColor" opacity=".5"/></svg>';
      case 'bar-table':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="9" y="3" width="10" height="22" rx="2" fill="currentColor" opacity=".85"/><rect x="3" y="5" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="21" y="5" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="3" y="12" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/><rect x="21" y="12" width="4" height="4" rx="1.5" fill="currentColor" opacity=".4"/></svg>';
      case 'text_label':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><text x="4" y="20" font-size="16" font-weight="700" font-family="system-ui" fill="currentColor" opacity=".85">Aa</text></svg>';
      case '__zone':
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="3" y="3" width="22" height="22" rx="3" stroke="currentColor" stroke-width="1.8" stroke-dasharray="4 2.5" fill="none" opacity=".7"/><path d="M9 10h10M9 14h7M9 18h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity=".5"/></svg>';
      default:
        return '<svg width="28" height="28" viewBox="0 0 28 28"><rect x="6" y="6" width="16" height="16" rx="2" fill="currentColor" opacity=".7"/></svg>';
    }
  }

  /* ══════════════════════════════════════════════════════════════════
     PLACING
  ══════════════════════════════════════════════════════════════════ */
  function startPlacing(typeKey, el) {
    if (S.mode !== 'builder') return;
    S.placing = typeKey;
    document.querySelectorAll('.fp-tb-item').forEach(function (d) {
      d.classList.toggle('fp-tb-item--active', d === el);
    });
    var hint = document.getElementById('fp-place-hint');
    if (hint) hint.hidden = false;
    var cnv = stage.container();
    if (cnv) cnv.style.cursor = 'crosshair';
  }

  function cancelPlacing() {
    S.placing = null;
    document.querySelectorAll('.fp-tb-item').forEach(function (d) {
      d.classList.remove('fp-tb-item--active');
    });
    var hint = document.getElementById('fp-place-hint');
    if (hint) hint.hidden = true;
    var cnv = stage.container();
    if (cnv) cnv.style.cursor = '';
  }

  function placeTable(pos) {
    if (!S.placing || !S.floorId) return;
    var typeKey = S.placing;
    var tbDef   = TOOLBAR.find(function (d) { return d && d.key === typeKey; });
    var W = tbDef ? tbDef.w : 90;
    var H = tbDef ? tbDef.h : 56;
    var cap = tbDef ? tbDef.cap : 4;

    var scale = stage.scaleX();
    var stPos  = stage.position();
    var sx = (pos.x - stPos.x) / scale;
    var sy = (pos.y - stPos.y) / scale;
    var px = snapToGrid(sx);
    var py = snapToGrid(sy);

    if (typeKey !== 'text_label' && isOverlappingAny(px, py, W, H)) {
      showToast('Cannot place here — overlaps another table', 'warn');
      return;
    }

    cancelPlacing();

    var defaultLabel = typeKey === 'text_label'
      ? 'Label'
      : 'T' + (Object.values(S.tables).filter(function (t) { return t.type !== 'text_label' && t.type !== 'zone'; }).length + 1);

    var typeDef = TYPES[typeKey] || {};
    var payload = {
      type:         typeKey,
      label:        defaultLabel,
      pos_x:        px,
      pos_y:        py,
      width:        W,
      height:       H,
      rotation_deg: 0,
      capacity_min: typeDef.capacity ? typeDef.capacity[0] : 0,
      capacity_max: cap,
      shape:        typeDef.shape || (isRoundKey(typeKey) ? 'circle' : 'rect'),
    };

    apiFetch('POST', 'floor-plans/' + S.floorId + '/furniture', payload).then(function (item) {
      S.tables[item.id] = item;
      addTableNode(item, false);
      tableLayer.batchDraw();
      updateStatusBar();
      markDirty();
      showEmptyState(false);
      if (S.floorStats[S.floorId]) {
        S.floorStats[S.floorId].tables = Object.values(S.tables).filter(function (t) { return !isZone(t.type) && t.type !== 'text_label'; }).length;
        S.floorStats[S.floorId].seats  = Object.values(S.tables).reduce(function (s, t) { return s + (parseInt(t.capacity_max) || 0); }, 0);
        updateFloorPopup(S.floorId);
      }
      // Open float panel immediately after placing
      selectTable(item.id);
      showFloatPanel(item.id);

      if (typeKey === 'text_label') {
        var node = stage.findOne('#tbl-' + item.id);
        if (node) setTimeout(function () { startInlineLabelEdit(item.id, node); }, 100);
      }
    }).catch(function (err) {
      console.warn('Place failed', err);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     FLOAT PANEL
  ══════════════════════════════════════════════════════════════════ */
  function showFloatPanel(id) {
    var item = S.tables[id];
    if (!item) return;
    var panel = document.getElementById('fp-float-panel');
    if (!panel) return;
    panel.hidden = false;

    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = item.label || (item.type === 'text_label' ? 'Label' : item.type === 'zone' ? 'Zone' : 'Table');

    if (item.type === 'zone') {
      showZoneFloatProps(id, item);
    } else if (item.type === 'text_label') {
      showLabelFloatProps(id, item);
    } else {
      showTableFloatProps(id, item);
    }

    // Enable delete button
    var delBtn = document.getElementById('fp-fp-delete');
    if (delBtn) delBtn.disabled = false;

    // Enable dup button (non-zone)
    var dupBtn = document.getElementById('fp-tb-dup');
    if (dupBtn) dupBtn.disabled = (item.type === 'zone');

    // Position on desktop
    if (item.type !== 'zone') {
      var node = stage.findOne('#tbl-' + id);
      if (node) positionFloatPanel(node);
    } else {
      // For zones, try to position near the zone group
      var zNode = zoneLayer.findOne('#zone-' + id);
      if (zNode) {
        var zRect = zNode.getClientRect();
        positionFloatPanelByRect(zRect);
      }
    }
  }

  function hideFloatPanel() {
    var panel = document.getElementById('fp-float-panel');
    if (panel) panel.hidden = true;
    var delBtn = document.getElementById('fp-fp-delete');
    if (delBtn) delBtn.disabled = true;
    var dupBtn = document.getElementById('fp-tb-dup');
    if (dupBtn) dupBtn.disabled = true;
  }

  function positionFloatPanel(konvaNode) {
    if (!konvaNode) return;
    var rect = konvaNode.getClientRect();
    positionFloatPanelByRect(rect);
  }

  function positionFloatPanelByRect(rect) {
    if (window.innerWidth <= 768) return; // mobile: bottom sheet handled by CSS

    var panel = document.getElementById('fp-float-panel');
    var wrap  = document.getElementById('fp-canvas-wrap');
    if (!panel || !wrap) return;

    var wrapRect  = wrap.getBoundingClientRect();
    var panelW    = panel.offsetWidth  || 240;
    var panelH    = panel.offsetHeight || 300;

    var canvasLeft  = rect.x - wrapRect.left;
    var canvasTop   = rect.y - wrapRect.top;

    var left = canvasLeft + rect.width + 12;
    var top  = canvasTop;

    // Flip left if right overflow
    if (left + panelW > wrapRect.width) {
      left = canvasLeft - panelW - 12;
    }

    // Clamp horizontally
    left = Math.max(4, Math.min(left, wrapRect.width - panelW - 4));

    // Clamp vertically
    top = Math.max(4, Math.min(top, wrapRect.height - panelH - 4));

    panel.style.position = 'absolute';
    panel.style.left     = left + 'px';
    panel.style.top      = top  + 'px';
  }

  function showTableFloatProps(id, item) {
    var body = document.getElementById('fp-fp-body');
    if (!body) return;
    var statusKey = S.liveStatus[id] || (item.is_available === '0' || item.is_available === 0 ? 'blocked' : 'free');
    var readonly  = S.mode === 'live' ? 'readonly' : '';
    var typeDef   = TYPES[item.type] || {};
    var isBarSeatItem = isBarSeat(item);

    body.innerHTML =
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Label</label>' +
        '<input class="fp-prop-input" id="fp-fp-label" value="' + escAttr(item.label || '') + '" ' + readonly + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Capacity</label>' +
        '<input class="fp-prop-input" id="fp-fp-cap" type="number" min="1" max="20" value="' + (parseInt(item.capacity_max) || 4) + '"' +
          (isBarSeatItem ? ' readonly' : ' ' + readonly) + '>' +
      '</div>' +
      (S.mode === 'live' ?
        '<div class="fp-prop-row">' +
          '<label class="fp-prop-label">Status</label>' +
          '<span class="fp-status-chip fp-status-chip--' + statusKey + '">' + (STATUS[statusKey] ? STATUS[statusKey].label : statusKey) + '</span>' +
        '</div>' : '') +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label" style="font-size:11px;color:#9CA3AF;">' + escHtml(typeDef.label || item.type) + '</label>' +
      '</div>';

    if (S.mode === 'builder') {
      var capEl = document.getElementById('fp-fp-cap');
      if (capEl && !isBarSeatItem) {
        capEl.addEventListener('input', function () { applyPropChange(id); });
      }
      var labelEl = document.getElementById('fp-fp-label');
      if (labelEl) {
        labelEl.addEventListener('change', function () { applyPropChange(id); });
      }
    }
  }

  function showLabelFloatProps(id, item) {
    var body = document.getElementById('fp-fp-body');
    if (!body) return;
    var meta     = item.meta || {};
    var readonly = S.mode === 'live' ? 'readonly' : '';

    body.innerHTML =
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Text</label>' +
        '<input class="fp-prop-input" id="fp-fp-label" value="' + escAttr(item.label || '') + '" ' + readonly + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Font size</label>' +
        '<input class="fp-prop-input" id="fp-fp-font-size" type="number" min="8" max="72" value="' + toNum(meta.fontSize, 15) + '" ' + readonly + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Colour</label>' +
        '<input class="fp-prop-input fp-prop-color" id="fp-fp-color" type="color" value="' + escAttr(meta.color || '#1a1a1a') + '" ' + (S.mode === 'live' ? 'disabled' : '') + '>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Background</label>' +
        '<input class="fp-prop-input fp-prop-color" id="fp-fp-bg-color" type="color" value="' + escAttr(meta.bgColor || '#ffffff') + '" ' + (S.mode === 'live' ? 'disabled' : '') + '>' +
        '<label style="margin-top:4px;display:flex;align-items:center;gap:6px;font-size:12px;">' +
          '<input type="checkbox" id="fp-fp-bg-none" ' + (!meta.bgColor ? 'checked' : '') + ' ' + readonly + '> None' +
        '</label>' +
      '</div>';

    if (S.mode === 'builder') {
      ['fp-fp-label', 'fp-fp-font-size', 'fp-fp-color', 'fp-fp-bg-color', 'fp-fp-bg-none'].forEach(function (inputId) {
        var el = document.getElementById(inputId);
        if (el) el.addEventListener('change', function () { applyLabelPropChange(id); });
      });
    }
  }

  function showZoneFloatProps(id, item) {
    var body = document.getElementById('fp-fp-body');
    if (!body) return;
    var meta    = item.meta || {};
    var members = meta.members || [];

    body.innerHTML =
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Name</label>' +
        '<input class="fp-prop-input" id="fp-fp-label" value="' + escAttr(item.label || '') + '">' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<label class="fp-prop-label">Colour</label>' +
        '<input class="fp-prop-input fp-prop-color" id="fp-fp-zone-color" type="color" value="' + escAttr(meta.color || '#EAF5EE') + '">' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<span style="font-size:12px;color:#6B7280;">' + members.length + ' table' + (members.length !== 1 ? 's' : '') + ' grouped</span>' +
      '</div>' +
      '<div class="fp-prop-row">' +
        '<button type="button" id="fp-fp-ungroup" class="fp-btn fp-btn-secondary" style="width:100%;margin-top:4px;">Ungroup</button>' +
      '</div>';

    var labelEl = document.getElementById('fp-fp-label');
    if (labelEl) {
      labelEl.addEventListener('change', function () {
        item.label = labelEl.value.trim() || item.label;
        markDirty();
        renderZoneLayer();
        var title = document.getElementById('fp-fp-title');
        if (title) title.textContent = item.label;
      });
    }

    var colorEl = document.getElementById('fp-fp-zone-color');
    if (colorEl) {
      colorEl.addEventListener('input', function () {
        if (!item.meta) item.meta = {};
        item.meta.color = colorEl.value;
        markDirty();
        renderZoneLayer();
      });
    }

    var ungroupBtn = document.getElementById('fp-fp-ungroup');
    if (ungroupBtn) {
      ungroupBtn.addEventListener('click', function () {
        deleteZone(id);
      });
    }
  }

  /* ── Prop change handlers ────────────────────────────────────────── */
  function applyPropChange(id) {
    var item = S.tables[id];
    if (!item) return;
    var labelEl = document.getElementById('fp-fp-label');
    var capEl   = document.getElementById('fp-fp-cap');
    if (labelEl)  item.label        = labelEl.value.trim();
    if (capEl)    item.capacity_max = parseInt(capEl.value) || 1;

    var node = stage.findOne('#tbl-' + id);
    if (node) { node.destroy(); }
    addTableNode(item);
    if (S.selected === id) {
      var newNode = stage.findOne('#tbl-' + id);
      if (newNode && S.mode === 'builder') transformer.nodes([newNode]);
    }
    tableLayer.batchDraw();
    markDirty();
    updateStatusBar();

    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = item.label || 'Table';
  }

  function applyLabelPropChange(id) {
    var item = S.tables[id];
    if (!item) return;

    var labelEl   = document.getElementById('fp-fp-label');
    var sizeEl    = document.getElementById('fp-fp-font-size');
    var colorEl   = document.getElementById('fp-fp-color');
    var bgColorEl = document.getElementById('fp-fp-bg-color');
    var bgNoneEl  = document.getElementById('fp-fp-bg-none');

    if (labelEl)  item.label = labelEl.value.trim() || 'Label';
    var meta = item.meta || {};
    if (sizeEl)    meta.fontSize = parseInt(sizeEl.value) || 15;
    if (colorEl)   meta.color    = colorEl.value;
    if (bgNoneEl && bgNoneEl.checked) {
      meta.bgColor = '';
    } else if (bgColorEl) {
      meta.bgColor = bgColorEl.value;
    }
    item.meta = meta;

    var node = stage.findOne('#tbl-' + id);
    if (node) { node.destroy(); }
    addTableNode(item);
    if (S.selected === id) {
      var newNode = stage.findOne('#tbl-' + id);
      if (newNode && S.mode === 'builder') transformer.nodes([newNode]);
    }
    tableLayer.batchDraw();
    markDirty();

    var title = document.getElementById('fp-fp-title');
    if (title) title.textContent = item.label;
  }

  /* ══════════════════════════════════════════════════════════════════
     DUPLICATE
  ══════════════════════════════════════════════════════════════════ */
  function duplicateSelected() {
    if (!S.selected || !S.floorId) return;
    var item = S.tables[S.selected];
    if (!item || item.type === 'zone') return;

    var payload = {
      type:         item.type,
      label:        (item.label || 'T') + ' (copy)',
      pos_x:        toNum(item.pos_x, 0) + 24,
      pos_y:        toNum(item.pos_y, 0) + 24,
      width:        item.width,
      height:       item.height,
      rotation_deg: item.rotation_deg || 0,
      capacity_min: item.capacity_min || 0,
      capacity_max: item.capacity_max || 4,
      shape:        item.shape || 'rect',
    };
    if (item.meta) payload.meta = JSON.parse(JSON.stringify(item.meta));

    apiFetch('POST', 'floor-plans/' + S.floorId + '/furniture', payload).then(function (newItem) {
      S.tables[newItem.id] = newItem;
      addTableNode(newItem, false);
      tableLayer.batchDraw();
      updateStatusBar();
      markDirty();
      selectTable(newItem.id);
    }).catch(function (err) {
      console.warn('Duplicate failed', err);
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     DELETE / UNDO / REDO
  ══════════════════════════════════════════════════════════════════ */
  function deleteSelectedWithConfirm(sourceBtn) {
    var btn = sourceBtn || document.getElementById('fp-fp-delete');
    if (!btn || !S.selected) { deleteSelected(); return; }
    if (btn.dataset.confirm === '1') {
      clearTimeout(parseInt(btn.dataset.timer, 10));
      delete btn.dataset.confirm;
      delete btn.dataset.timer;
      var item = S.tables[S.selected];
      btn.textContent = item && item.type === 'text_label' ? 'Remove label' : item && item.type === 'zone' ? 'Remove zone' : 'Remove table';
      btn.classList.remove('fp-btn-danger--confirming');
      deleteSelected();
      return;
    }
    btn.textContent = 'Confirm remove?';
    btn.dataset.confirm = '1';
    btn.classList.add('fp-btn-danger--confirming');
    var t = setTimeout(function () {
      var it = S.tables[S.selected];
      btn.textContent = it && it.type === 'text_label' ? 'Remove label' : it && it.type === 'zone' ? 'Remove zone' : 'Remove table';
      delete btn.dataset.confirm;
      delete btn.dataset.timer;
      btn.classList.remove('fp-btn-danger--confirming');
    }, 3000);
    btn.dataset.timer = String(t);
  }

  function deleteSelected() {
    var id = S.selected;
    if (!id) return;

    var item = S.tables[id];
    if (item && item.type === 'zone') {
      deleteZone(id);
      return;
    }

    apiFetch('DELETE', 'furniture/' + id).then(function () {
      var node = stage.findOne('#tbl-' + id);
      if (node) node.destroy();
      delete S.tables[id];
      deselect();
      tableLayer.batchDraw();
      updateStatusBar();
      markDirty();
      if (!Object.keys(S.tables).length) showEmptyState(true);
    }).catch(function (err) { console.warn('Delete failed', err); });
  }

  function pushUndo(id) {
    var item = S.tables[id];
    if (!item) return;
    S.undoStack.push({ id: id, pos_x: item.pos_x, pos_y: item.pos_y, rotation_deg: item.rotation_deg });
    if (S.undoStack.length > 60) S.undoStack.shift();
    S.redoStack = [];
    syncUndoBtns();
  }

  function undo() {
    var snap = S.undoStack.pop();
    if (!snap) return;
    var item = S.tables[snap.id];
    if (item) {
      S.redoStack.push({ id: snap.id, pos_x: item.pos_x, pos_y: item.pos_y, rotation_deg: item.rotation_deg });
      item.pos_x = snap.pos_x; item.pos_y = snap.pos_y; item.rotation_deg = snap.rotation_deg;
      var node = stage.findOne('#tbl-' + snap.id);
      if (node) { node.position({ x: item.pos_x, y: item.pos_y }); node.rotation(item.rotation_deg); }
      tableLayer.batchDraw();
      renderZoneLayer();
      markDirty();
    }
    syncUndoBtns();
  }

  function redo() {
    var snap = S.redoStack.pop();
    if (!snap) return;
    var item = S.tables[snap.id];
    if (item) {
      S.undoStack.push({ id: snap.id, pos_x: item.pos_x, pos_y: item.pos_y, rotation_deg: item.rotation_deg });
      item.pos_x = snap.pos_x; item.pos_y = snap.pos_y; item.rotation_deg = snap.rotation_deg;
      var node = stage.findOne('#tbl-' + snap.id);
      if (node) { node.position({ x: item.pos_x, y: item.pos_y }); node.rotation(item.rotation_deg); }
      tableLayer.batchDraw();
      renderZoneLayer();
      markDirty();
    }
    syncUndoBtns();
  }

  function syncUndoBtns() {
    var u = document.getElementById('fp-btn-undo');
    var r = document.getElementById('fp-btn-redo');
    if (u) u.disabled = S.undoStack.length === 0;
    if (r) r.disabled = S.redoStack.length === 0;
  }

  /* ══════════════════════════════════════════════════════════════════
     PUBLISH / SAVE
  ══════════════════════════════════════════════════════════════════ */
  function markDirty() {
    S.dirty = true;
    var btn = document.getElementById('fp-btn-publish');
    if (btn) btn.disabled = false;
  }

  function saveLayout() {
    if (!S.floorId) return;
    var promises = Object.values(S.tables).map(function (item) {
      var patch = {
        label:        item.label,
        pos_x:        item.pos_x,
        pos_y:        item.pos_y,
        rotation_deg: item.rotation_deg || 0,
        capacity_min: item.capacity_min,
        capacity_max: item.capacity_max,
      };
      if (item.meta) patch.meta = item.meta;
      return apiFetch('PATCH', 'furniture/' + item.id, patch);
    });
    var btn = document.getElementById('fp-btn-publish');
    if (btn) btn.textContent = 'Saving…';
    Promise.all(promises).then(function () {
      S.dirty = false;
      if (btn) {
        btn.textContent = 'Saved ✓';
        btn.disabled = true;
        setTimeout(function () { btn.textContent = 'Publish updates'; }, 2500);
      }
    }).catch(function () {
      if (btn) { btn.textContent = 'Error — retry'; btn.disabled = false; }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     LIVE STATUS
  ══════════════════════════════════════════════════════════════════ */
  function startLiveUpdates() {
    fetchLiveStatus();
    clearInterval(S.liveTimer);
    S.liveTimer = setInterval(fetchLiveStatus, 30000);
  }

  function stopLiveUpdates() {
    clearInterval(S.liveTimer);
    S.liveTimer = null;
  }

  function fetchLiveStatus() {
    if (!S.floorId || S.mode !== 'live') return;
    var url = 'reservations?date=' + S.date + '&per_page=100';
    apiFetch('GET', url).then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      S.liveStatus = {};
      items.forEach(function (res) {
        if (!res.furniture_ids || !res.furniture_ids.length) return;
        var statusKey = 'booked';
        if (res.status === 'seated')   statusKey = 'occupied';
        if (res.status === 'canceled' || res.status === 'no_show') return;
        res.furniture_ids.forEach(function (fid) {
          S.liveStatus[fid] = statusKey;
        });
      });
      renderAllTables();
    }).catch(function () {});
  }

  /* ══════════════════════════════════════════════════════════════════
     MODE SWITCHING
  ══════════════════════════════════════════════════════════════════ */
  function enterBuilderMode() {
    S.mode = 'builder';
    document.getElementById('fp-app').dataset.mode = 'builder';
    stopLiveUpdates();
    tableLayer.getChildren(function (n) { return n.name() === 'table-group'; })
      .forEach(function (g) { g.draggable(true); });
    renderAllTables();
    renderZoneLayer();
    tableLayer.batchDraw();
    setTimeout(zoomFit, 150);
  }

  function exitBuilderMode() {
    if (!S.dirty) { _doExitBuilder(); return; }
    fpModal({
      type:   'confirm',
      title:  'Exit without publishing?',
      body:   '<p>You have unsaved layout changes. These will be lost if you exit now.</p>',
      ok:     'Exit',
      cancel: 'Stay',
      danger: true,
    }).then(function (ok) {
      if (ok) _doExitBuilder();
    });
  }

  function _doExitBuilder() {
    S.mode = 'live';
    document.getElementById('fp-app').dataset.mode = 'live';
    deselect();
    cancelPlacing();
    S.dirty = false;
    tableLayer.getChildren(function (n) { return n.name() === 'table-group'; })
      .forEach(function (g) { g.draggable(false); });
    transformer.nodes([]);
    renderAllTables();
    renderZoneLayer();
    tableLayer.batchDraw();
    startLiveUpdates();
    var btn = document.getElementById('fp-btn-publish');
    if (btn) btn.disabled = true;
  }

  /* ══════════════════════════════════════════════════════════════════
     ADD FLOOR
  ══════════════════════════════════════════════════════════════════ */
  function addFloor() {
    fpModal({
      type:  'prompt',
      title: 'Add Floor',
      body:  '<input class="fp-modal-input" type="text" placeholder=\'e.g. Level 1, Rooftop, Garden\' maxlength="40">',
      ok:    'Add Floor',
    }).then(function (name) {
      if (!name) return;
      apiFetch('POST', 'floor-plans', {
        name:         name,
        floor_number: S.floors.length + 1,
        is_active:    1,
        width_px:     1200,
        height_px:    800,
      }).then(function (floor) {
        S.floors.push(floor);
        renderFloorTabs();
        selectFloor(floor.id);
        loadFloorStats(floor.id);
      }).catch(function (err) {
        console.warn('Add floor failed', err);
        fpModal({ type: 'alert', title: 'Error', body: '<p>Could not add floor. You may not have permission.</p>', ok: 'OK' });
      });
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     ZOOM
  ══════════════════════════════════════════════════════════════════ */
  function zoomBy(factor) {
    var oldScale = stage.scaleX();
    var newScale = Math.max(0.2, Math.min(4, oldScale * factor));
    var cx = stage.width() / 2, cy = stage.height() / 2;
    var pos = stage.position();
    stage.scale({ x: newScale, y: newScale });
    stage.position({
      x: cx - (cx - pos.x) * (newScale / oldScale),
      y: cy - (cy - pos.y) * (newScale / oldScale),
    });
    stage.batchDraw();
    var badge = document.getElementById('fp-zoom-pct');
    if (badge) badge.textContent = Math.round(newScale * 100) + '%';
  }

  function zoomFit() {
    stage.scale({ x: 1, y: 1 });
    stage.position({ x: 0, y: 0 });
    stage.batchDraw();
    var badge = document.getElementById('fp-zoom-pct');
    if (badge) badge.textContent = '100%';
  }

  /* ══════════════════════════════════════════════════════════════════
     EVENT BINDINGS
  ══════════════════════════════════════════════════════════════════ */
  function bindHeader() {
    bindEl('fp-btn-edit',          'click', enterBuilderMode);
    bindEl('fp-btn-exit-builder',  'click', exitBuilderMode);
    bindEl('fp-btn-publish',       'click', saveLayout);
    bindEl('fp-btn-undo',          'click', undo);
    bindEl('fp-btn-redo',          'click', redo);

    var newResBtn = document.getElementById('fp-btn-new-res');
    if (newResBtn && cfg.newResUrl) newResBtn.href = cfg.newResUrl;
  }

  function bindFloorNav() {
    bindEl('fp-add-floor', 'click', addFloor);
  }

  function bindSubbar() {
    var chipDate  = document.getElementById('fp-chip-date');
    var chipTime  = document.getElementById('fp-chip-time');
    var datePanel = document.getElementById('fp-date-panel');
    var timePanel = document.getElementById('fp-time-panel');

    if (chipDate) chipDate.addEventListener('click', function (e) {
      e.stopPropagation();
      if (datePanel) { datePanel.hidden = !datePanel.hidden; if (timePanel) timePanel.hidden = true; }
    });

    if (chipTime) chipTime.addEventListener('click', function (e) {
      e.stopPropagation();
      if (timePanel) { timePanel.hidden = !timePanel.hidden; if (datePanel) datePanel.hidden = true; }
    });

    var dateInput = document.getElementById('fp-date-input');
    if (dateInput) {
      dateInput.value = S.date;
      dateInput.addEventListener('change', function () {
        S.date = this.value || TODAY;
        setDateLabel(S.date);
        if (datePanel) datePanel.hidden = true;
        S.floors.forEach(function (f) { loadFloorStats(f.id); });
        if (S.mode === 'live') fetchLiveStatus();
      });
    }

    document.querySelectorAll('.fp-time-opt').forEach(function (opt) {
      opt.addEventListener('click', function () {
        S.time = this.dataset.time || '';
        setTimeLabel(S.time);
        document.querySelectorAll('.fp-time-opt').forEach(function (o) {
          o.classList.toggle('fp-time-opt--active', o === opt);
        });
        if (timePanel) timePanel.hidden = true;
        if (S.mode === 'live') fetchLiveStatus();
      });
    });

    document.querySelectorAll('.fp-view-tab').forEach(function (btn) {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.fp-view-tab').forEach(function (b) { b.classList.remove('fp-view-tab--active'); });
        this.classList.add('fp-view-tab--active');
      });
    });

    document.addEventListener('click', function () {
      if (datePanel) datePanel.hidden = true;
      if (timePanel) timePanel.hidden = true;
    });
  }

  function bindCanvasTools() {
    bindEl('fp-zoom-in',    'click', function () { zoomBy(1.2); });
    bindEl('fp-zoom-out',   'click', function () { zoomBy(1 / 1.2); });
    bindEl('fp-zoom-fit',   'click', zoomFit);
    bindEl('fp-delete-sel', 'click', function () { deleteSelectedWithConfirm(null); });
  }

  function bindFloatPanel() {
    bindEl('fp-fp-close',  'click', function () { hideFloatPanel(); deselect(); });
    bindEl('fp-fp-delete', 'click', function (e) { deleteSelectedWithConfirm(e.currentTarget); });
    bindEl('fp-fp-back',   'click', deselect);
  }

  function bindToolbar() {
    bindEl('fp-tb-snap', 'click', function (e) {
      S.snapEnabled = !S.snapEnabled;
      e.currentTarget.classList.toggle('fp-tb-ctrl--active', S.snapEnabled);
    });
    bindEl('fp-tb-dup', 'click', duplicateSelected);
  }

  function bindKeyboard() {
    document.addEventListener('keydown', function (e) {
      var tag = document.activeElement ? document.activeElement.tagName : '';
      var inField = tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT';

      if (e.key === 'Escape') {
        if (S.placing) { cancelPlacing(); return; }
        if (S.selected || S.selectedIds.size) { deselect(); return; }
      }
      if (!inField && (e.key === 'Delete' || e.key === 'Backspace') && S.selected && S.mode === 'builder') {
        deleteSelected(); return;
      }
      if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) { e.preventDefault(); undo(); return; }
      if ((e.ctrlKey || e.metaKey) && (e.key === 'y' || (e.key === 'z' && e.shiftKey))) { e.preventDefault(); redo(); return; }
      if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); if (S.mode === 'builder' && S.dirty) saveLayout(); return; }
      if ((e.ctrlKey || e.metaKey) && e.key === 'd') { e.preventDefault(); if (S.mode === 'builder') duplicateSelected(); return; }
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     UI HELPERS
  ══════════════════════════════════════════════════════════════════ */
  function updateStatusBar() {
    var items   = Object.values(S.tables).filter(function (t) { return t.type !== 'text_label' && t.type !== 'zone'; });
    var tCount  = items.length;
    var sCount  = items.reduce(function (s, t) { return s + (parseInt(t.capacity_max) || 0); }, 0);

    var statusEl = document.getElementById('fp-tb-status');
    if (statusEl) {
      statusEl.textContent = tCount
        ? tCount + ' table' + (tCount !== 1 ? 's' : '') + ' · ' + sCount + ' seat' + (sCount !== 1 ? 's' : '')
        : 'No tables';
    }

    // Backwards compat
    var cntEl = document.getElementById('fp-table-count');
    if (cntEl) cntEl.textContent = String(tCount);
  }

  // Kept for backwards compat (called in some places)
  function updateTableCount() { updateStatusBar(); }

  function showEmptyState(show) {
    var wrap = document.getElementById('fp-canvas-wrap');
    if (!wrap) return;
    var existing = wrap.querySelector('.fp-canvas-empty');
    if (show && !existing) {
      var div = document.createElement('div');
      div.className = 'fp-canvas-empty';
      div.innerHTML =
        '<div class="fp-canvas-empty-icon">' +
          '<svg width="24" height="24" fill="none" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M6 12h12M12 8v8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>' +
        '</div>' +
        '<span class="fp-canvas-empty-text">No tables yet — click <strong>Edit Floorplan</strong> to start adding</span>';
      wrap.appendChild(div);
    } else if (!show && existing) {
      existing.remove();
    }
  }

  function setDateLabel(date) {
    var el = document.getElementById('fp-date-lbl');
    if (!el) return;
    if (!date || date === TODAY) { el.textContent = 'Today'; return; }
    try {
      var d = new Date(date + 'T00:00:00');
      el.textContent = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } catch (e) { el.textContent = date; }
  }

  function setTimeLabel(time) {
    var el = document.getElementById('fp-time-lbl');
    if (!el) return;
    if (!time) { el.textContent = 'All day'; return; }
    var parts = time.split(':');
    var h = parseInt(parts[0], 10), m = parts[1] || '00';
    el.textContent = (h % 12 || 12) + ':' + m + (h >= 12 ? ' PM' : ' AM');
  }

  function bindEl(id, ev, fn) {
    var el = document.getElementById(id);
    if (el) el.addEventListener(ev, fn);
  }

  /* ══════════════════════════════════════════════════════════════════
     TYPE HELPERS
  ══════════════════════════════════════════════════════════════════ */
  function isRound(item) {
    var t = item.type || '';
    if (t === 'table-round' || t === 'bar-seat') return true;
    var typeDef = TYPES[t] || {};
    return (typeDef.shape || item.shape || 'rect') === 'circle';
  }

  // Checks by type key string (for placeTable shape resolution)
  function isRoundKey(typeKey) {
    return typeKey === 'table-round' || typeKey === 'bar-seat' || typeKey === 'table-square' ||
      (TYPES[typeKey] && (TYPES[typeKey].shape === 'circle'));
  }

  function isLounge(item) {
    return item.type === 'lounge';
  }

  function isBarSeat(item) {
    var t = item.type || '';
    return t === 'bar-seat' || t === 'bar-stool';
  }

  function isBooth(typeKey) {
    return typeof typeKey === 'string' && typeKey.indexOf('booth') === 0;
  }

  function isZone(typeKey) {
    return typeKey === 'zone' ||
      typeKey === 'stage' || typeKey === 'dj-booth' || typeKey === 'area-vip' || typeKey === 'bar-counter';
  }

  function snapToGrid(v) { return S.snapEnabled ? Math.round(v / 24) * 24 : Math.round(v); }

  // Kept for backwards compat
  function snapTo24(v) { return Math.round(v / 24) * 24; }

  function toNum(v, def) { return isNaN(parseFloat(v)) ? def : parseFloat(v); }

  /* ══════════════════════════════════════════════════════════════════
     API
  ══════════════════════════════════════════════════════════════════ */
  function apiFetch(method, path, body) {
    var opts = {
      method: method,
      headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': NONCE },
    };
    if (body !== undefined) opts.body = JSON.stringify(body);
    return fetch(API + path, opts).then(function (r) {
      if (!r.ok) return r.json().then(function (e) { throw e; }, function () { throw new Error(r.status); });
      return r.json();
    });
  }

  /* ══════════════════════════════════════════════════════════════════
     SECURITY
  ══════════════════════════════════════════════════════════════════ */
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;');
  }

  function escAttr(str) { return escHtml(str); }
})();
