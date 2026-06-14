/* TEMPO House — Floor Plan Builder (Konva.js, vanilla JS, no build step) */
(function () {
  'use strict';

  const cfg     = window.thrFloorPlan;   // localized via wp_localize_script
  const api     = cfg.apiUrl;
  const nonce   = cfg.nonce;
  const types   = cfg.furnitureTypes;    // from GET /furniture/types

  // ── State ─────────────────────────────────────────────────────────────────
  let stage, bgLayer, furnitureLayer, labelLayer, statusLayer, transformer;
  let floors        = [];
  let currentFloor  = null;
  let furnitureData = {};   // id → API object
  let liveInterval  = null;
  let isLiveMode    = false;
  let pendingType   = null; // furniture type to place on next canvas click
  let isDirty       = false;

  // Status colours (live view mode)
  const STATUS_COLORS = {
    available: '#2d6a4f',
    reserved:  '#ddaa62',
    seated:    '#c0392b',
    blocked:   '#555',
  };

  // ── Boot ──────────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    if (!document.getElementById('thr-fp-app')) return;
    initKonva();
    loadFloors();
    bindToolbar();
    bindPalette();
  });

  // ── Konva Stage ───────────────────────────────────────────────────────────
  function initKonva() {
    const wrap = document.getElementById('thr-konva-container');
    stage = new Konva.Stage({
      container: 'thr-konva-container',
      width:  wrap.clientWidth  || 800,
      height: wrap.clientHeight || 600,
      draggable: false,
    });

    bgLayer        = new Konva.Layer();
    furnitureLayer = new Konva.Layer();
    labelLayer     = new Konva.Layer();
    statusLayer    = new Konva.Layer();

    stage.add(bgLayer);
    stage.add(furnitureLayer);
    stage.add(labelLayer);
    stage.add(statusLayer);

    transformer = new Konva.Transformer({
      rotateEnabled: true,
      keepRatio: false,
      enabledAnchors: ['top-left','top-right','bottom-left','bottom-right','middle-left','middle-right','top-center','bottom-center'],
      borderStroke: '#ddaa62',
      borderStrokeWidth: 1.5,
      anchorFill: '#ddaa62',
      anchorStroke: '#1a1816',
      anchorSize: 8,
    });
    furnitureLayer.add(transformer);

    // Click on empty canvas — place pending furniture OR deselect
    stage.on('click tap', function (e) {
      if (e.target === stage || e.target === bgLayer.findOne('Image') || e.target === bgLayer.findOne('Rect')) {
        if (pendingType) {
          const pos = stage.getRelativePointerPosition();
          placeFurniture(pendingType, pos.x, pos.y);
          cancelPendingType();
        } else {
          transformer.nodes([]);
          furnitureLayer.batchDraw();
          showProps(null);
        }
      }
    });

    // Wheel zoom
    stage.on('wheel', function (e) {
      e.evt.preventDefault();
      const scaleBy  = 1.08;
      const oldScale = stage.scaleX();
      const pointer  = stage.getPointerPosition();
      const mousePointTo = {
        x: (pointer.x - stage.x()) / oldScale,
        y: (pointer.y - stage.y()) / oldScale,
      };
      const newScale = e.evt.deltaY < 0 ? oldScale * scaleBy : oldScale / scaleBy;
      const clamped  = Math.max(0.2, Math.min(4, newScale));
      stage.scale({ x: clamped, y: clamped });
      stage.position({
        x: pointer.x - mousePointTo.x * clamped,
        y: pointer.y - mousePointTo.y * clamped,
      });
      updateZoomLabel(clamped);
      stage.batchDraw();
    });

    // Pan with middle mouse or space+drag
    let isPanning = false, panStart = null;
    stage.on('mousedown', function (e) {
      if (e.evt.button === 1 || (e.evt.button === 0 && e.evt.altKey)) {
        isPanning = true;
        panStart  = { x: e.evt.clientX - stage.x(), y: e.evt.clientY - stage.y() };
        stage.container().style.cursor = 'grabbing';
      }
    });
    window.addEventListener('mousemove', function (e) {
      if (!isPanning) return;
      stage.position({ x: e.clientX - panStart.x, y: e.clientY - panStart.y });
      stage.batchDraw();
    });
    window.addEventListener('mouseup', function () {
      if (isPanning) { isPanning = false; stage.container().style.cursor = ''; }
    });

    // Window resize
    window.addEventListener('resize', function () {
      const w = wrap.clientWidth, h = wrap.clientHeight;
      stage.width(w); stage.height(h); stage.batchDraw();
    });

    // Delete key
    window.addEventListener('keydown', function (e) {
      if ((e.key === 'Delete' || e.key === 'Backspace') && transformer.nodes().length > 0) {
        if (document.activeElement && ['INPUT','TEXTAREA','SELECT'].includes(document.activeElement.tagName)) return;
        deleteSelectedFurniture();
      }
      if (e.key === 'Escape') { cancelPendingType(); transformer.nodes([]); furnitureLayer.batchDraw(); showProps(null); }
    });
  }

  // ── Floor management ──────────────────────────────────────────────────────
  function loadFloors() {
    apiFetch('GET', 'floor-plans').then(data => {
      floors = data.data || data || [];
      renderFloorTabs();
      if (floors.length > 0) selectFloor(floors[0].id);
    }).catch(() => {
      floors = [];
      renderFloorTabs();
    });
  }

  function renderFloorTabs() {
    const sel = document.getElementById('thr-fp-floor-select');
    sel.innerHTML = '';
    if (floors.length === 0) {
      const opt = document.createElement('option');
      opt.value = ''; opt.textContent = 'No floors — add one';
      sel.appendChild(opt);
    }
    floors.forEach(f => {
      const opt = document.createElement('option');
      opt.value = f.id; opt.textContent = f.name || `Floor ${f.floor_number}`;
      sel.appendChild(opt);
    });
  }

  function selectFloor(id) {
    currentFloor = floors.find(f => f.id == id) || null;
    if (!currentFloor) return;

    const sel = document.getElementById('thr-fp-floor-select');
    if (sel) sel.value = id;

    clearCanvas();
    if (currentFloor.background_url) loadBackground(currentFloor.background_url);
    loadFurniture(id);
  }

  // ── Background image ──────────────────────────────────────────────────────
  function loadBackground(url) {
    bgLayer.destroyChildren();
    bgLayer.add(new Konva.Rect({
      x: 0, y: 0,
      width: currentFloor.width_px || stage.width(),
      height: currentFloor.height_px || stage.height(),
      fill: '#1c1a17',
    }));
    const img = new Image();
    img.crossOrigin = 'anonymous';
    img.onload = function () {
      const ki = new Konva.Image({
        image: img, x: 0, y: 0,
        width:  currentFloor.width_px  || img.naturalWidth,
        height: currentFloor.height_px || img.naturalHeight,
        opacity: 0.35,
      });
      bgLayer.add(ki);
      bgLayer.batchDraw();
    };
    img.src = url;
  }

  // ── Furniture loading ──────────────────────────────────────────────────────
  function loadFurniture(floorId) {
    furnitureData = {};
    furnitureLayer.destroyChildren();
    furnitureLayer.add(transformer); // re-add transformer after clear
    labelLayer.destroyChildren();
    statusLayer.destroyChildren();

    apiFetch('GET', `floor-plans/${floorId}/furniture`).then(data => {
      const items = data.data || data || [];
      items.forEach(item => {
        furnitureData[item.id] = item;
        addFurnitureShape(item);
      });
      furnitureLayer.batchDraw();
      labelLayer.batchDraw();
    });
  }

  function addFurnitureShape(item, focus = false) {
    const typeInfo = types[item.type] || {};
    const isCircle = typeInfo.shape === 'circle';
    const fill     = '#2a2520';
    const stroke   = '#ddaa62';
    const w = item.width  || (isCircle ? 60 : 80);
    const h = item.height || (isCircle ? 60 : 50);
    const x = item.pos_x || 100;
    const y = item.pos_y || 100;
    const id = String(item.id);

    let shape;
    if (isCircle) {
      shape = new Konva.Circle({
        id, x, y,
        radius: Math.min(w, h) / 2,
        fill, stroke, strokeWidth: 1.5,
        draggable: true,
      });
    } else {
      shape = new Konva.Rect({
        id, x, y, width: w, height: h,
        offsetX: w / 2, offsetY: h / 2,
        rotation: item.rotation_deg || 0,
        fill, stroke, strokeWidth: 1.5,
        cornerRadius: 3,
        draggable: true,
      });
    }

    shape.on('click tap', function (e) {
      e.cancelBubble = true;
      transformer.nodes([this]);
      furnitureLayer.batchDraw();
      showProps(furnitureData[this.id()]);
    });

    shape.on('dragend', function () {
      const d = furnitureData[this.id()];
      if (!d) return;
      d.pos_x = Math.round(this.x());
      d.pos_y = Math.round(this.y());
      markDirty();
    });

    shape.on('transformend', function () {
      const d = furnitureData[this.id()];
      if (!d) return;
      d.pos_x      = Math.round(this.x());
      d.pos_y      = Math.round(this.y());
      d.rotation_deg = Math.round(this.rotation());
      if (!isCircle) {
        d.width  = Math.round(this.width()  * this.scaleX());
        d.height = Math.round(this.height() * this.scaleY());
        this.scaleX(1); this.scaleY(1);
        this.width(d.width); this.height(d.height);
        this.offsetX(d.width / 2); this.offsetY(d.height / 2);
      } else {
        d.width = d.height = Math.round(this.radius() * 2 * this.scaleX());
        this.scaleX(1); this.scaleY(1);
        this.radius(d.width / 2);
      }
      updateLabel(id, d);
      markDirty();
    });

    furnitureLayer.add(shape);

    // Label
    const lbl = new Konva.Text({
      id: 'lbl-' + id,
      x: x - (isCircle ? 0 : w / 2),
      y: y - (isCircle ? 10 : h / 2),
      width: isCircle ? 60 : w,
      align: 'center',
      text: item.label || typeInfo.label || item.type,
      fontSize: 11,
      fontFamily: 'Space Grotesk, Arial, sans-serif',
      fill: '#f7f3ee',
      listening: false,
    });
    labelLayer.add(lbl);

    if (focus) {
      transformer.nodes([shape]);
      furnitureLayer.batchDraw();
      showProps(furnitureData[id]);
    }
  }

  function updateLabel(id, data) {
    const lbl = labelLayer.findOne('#lbl-' + id);
    if (!lbl) return;
    lbl.x(data.pos_x - data.width / 2);
    lbl.y(data.pos_y - data.height / 2);
    lbl.width(data.width);
    lbl.text(data.label || (types[data.type] || {}).label || data.type);
    labelLayer.batchDraw();
  }

  // ── Place new furniture ────────────────────────────────────────────────────
  function placeFurniture(type, x, y) {
    if (!currentFloor) { alert('Please select a floor first.'); return; }
    const info     = types[type] || {};
    const isCircle = info.shape === 'circle';
    const w        = isCircle ? 60 : 80;
    const h        = isCircle ? 60 : 50;

    const payload = {
      floor_plan_id: currentFloor.id,
      type,
      label:        info.label || type,
      pos_x:        Math.round(x),
      pos_y:        Math.round(y),
      width:        w,
      height:       h,
      rotation_deg: 0,
      capacity_min: info.capacity ? info.capacity[0] : 1,
      capacity_max: info.capacity ? info.capacity[1] : 4,
      shape:        info.shape || 'rect',
      is_combinable: 0,
      is_available:  1,
    };

    apiFetch('POST', `floor-plans/${currentFloor.id}/furniture`, payload).then(item => {
      furnitureData[item.id] = item;
      addFurnitureShape(item, true);
      markDirty(false); // already persisted
    });
  }

  // ── Delete selected ────────────────────────────────────────────────────────
  function deleteSelectedFurniture() {
    const nodes = transformer.nodes();
    if (!nodes.length) return;
    if (!confirm('Delete selected furniture?')) return;

    nodes.forEach(node => {
      const id = node.id();
      apiFetch('DELETE', `furniture/${id}`).catch(() => {});
      node.destroy();
      const lbl = labelLayer.findOne('#lbl-' + id);
      if (lbl) lbl.destroy();
      delete furnitureData[id];
    });

    transformer.nodes([]);
    furnitureLayer.batchDraw();
    labelLayer.batchDraw();
    showProps(null);
  }

  // ── Save all positions ────────────────────────────────────────────────────
  async function saveAll() {
    setSaveStatus('saving');
    const items = Object.values(furnitureData);
    try {
      await Promise.all(items.map(d =>
        apiFetch('PATCH', `furniture/${d.id}`, {
          pos_x:        d.pos_x,
          pos_y:        d.pos_y,
          width:        d.width,
          height:       d.height,
          rotation_deg: d.rotation_deg || 0,
          label:        d.label,
          capacity_min: d.capacity_min,
          capacity_max: d.capacity_max,
          is_combinable: d.is_combinable,
          is_available:  d.is_available,
        })
      ));
      isDirty = false;
      setSaveStatus('saved');
      setTimeout(() => setSaveStatus(''), 2500);
    } catch (e) {
      setSaveStatus('error');
    }
  }

  // ── Properties panel ──────────────────────────────────────────────────────
  function showProps(item) {
    const panel = document.getElementById('thr-fp-props');
    if (!item) {
      panel.innerHTML = '<p class="thr-fp-props-empty">Select a piece of furniture<br>to edit its properties.</p>';
      return;
    }
    const info = types[item.type] || {};
    panel.innerHTML = `
      <div class="thr-fp-prop-section">
        <h4>${escH(info.label || item.type)}</h4>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Label</span>
          <input class="thr-fp-prop-input" id="prop-label" type="text" value="${escH(item.label || '')}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Capacity min</span>
          <input class="thr-fp-prop-input" id="prop-cap-min" type="number" min="1" value="${item.capacity_min || 1}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Capacity max</span>
          <input class="thr-fp-prop-input" id="prop-cap-max" type="number" min="1" value="${item.capacity_max || 4}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Available</span>
          <input class="thr-fp-prop-input thr-fp-prop-checkbox" id="prop-avail" type="checkbox" ${item.is_available ? 'checked' : ''}>
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Combinable</span>
          <input class="thr-fp-prop-input thr-fp-prop-checkbox" id="prop-comb" type="checkbox" ${item.is_combinable ? 'checked' : ''}>
        </div>
      </div>
      <div class="thr-fp-prop-section">
        <h4>Position</h4>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">X</span>
          <input class="thr-fp-prop-input" id="prop-x" type="number" value="${Math.round(item.pos_x || 0)}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Y</span>
          <input class="thr-fp-prop-input" id="prop-y" type="number" value="${Math.round(item.pos_y || 0)}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">W</span>
          <input class="thr-fp-prop-input" id="prop-w" type="number" value="${Math.round(item.width || 80)}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">H</span>
          <input class="thr-fp-prop-input" id="prop-h" type="number" value="${Math.round(item.height || 50)}">
        </div>
        <div class="thr-fp-prop-row">
          <span class="thr-fp-prop-label">Rotation°</span>
          <input class="thr-fp-prop-input" id="prop-rot" type="number" min="-360" max="360" value="${Math.round(item.rotation_deg || 0)}">
        </div>
      </div>
      <button class="thr-fp-btn thr-fp-btn--primary" id="prop-apply" style="width:100%;margin-top:4px;">Apply</button>
      <button class="thr-fp-btn thr-fp-btn--danger" id="prop-delete" style="width:100%;margin-top:6px;">Delete</button>
    `;

    document.getElementById('prop-apply').addEventListener('click', () => applyProps(item));
    document.getElementById('prop-delete').addEventListener('click', deleteSelectedFurniture);
  }

  function applyProps(item) {
    item.label        = document.getElementById('prop-label').value.trim();
    item.capacity_min = parseInt(document.getElementById('prop-cap-min').value, 10) || 1;
    item.capacity_max = parseInt(document.getElementById('prop-cap-max').value, 10) || 1;
    item.is_available = document.getElementById('prop-avail').checked ? 1 : 0;
    item.is_combinable= document.getElementById('prop-comb').checked  ? 1 : 0;
    item.pos_x        = parseFloat(document.getElementById('prop-x').value) || 0;
    item.pos_y        = parseFloat(document.getElementById('prop-y').value) || 0;
    item.width        = parseFloat(document.getElementById('prop-w').value) || 80;
    item.height       = parseFloat(document.getElementById('prop-h').value) || 50;
    item.rotation_deg = parseFloat(document.getElementById('prop-rot').value) || 0;

    // Update canvas shape
    const id    = String(item.id);
    const shape = furnitureLayer.findOne('#' + id);
    if (shape) {
      shape.x(item.pos_x); shape.y(item.pos_y); shape.rotation(item.rotation_deg);
      if (shape instanceof Konva.Rect) {
        shape.width(item.width); shape.height(item.height);
        shape.offsetX(item.width / 2); shape.offsetY(item.height / 2);
      } else if (shape instanceof Konva.Circle) {
        shape.radius(Math.min(item.width, item.height) / 2);
      }
      furnitureLayer.batchDraw();
    }
    updateLabel(id, item);
    markDirty();
  }

  // ── Live view mode ────────────────────────────────────────────────────────
  function enterLiveMode() {
    isLiveMode = true;
    document.getElementById('thr-fp-btn-live').classList.add('thr-fp-btn--active');
    document.getElementById('thr-fp-btn-edit').classList.remove('thr-fp-btn--active');
    furnitureLayer.draggable(false);
    transformer.nodes([]);
    furnitureLayer.find('Rect,Circle').forEach(n => n.draggable(false));
    showProps(null);
    fetchLiveStatus();
    liveInterval = setInterval(fetchLiveStatus, 30000);
  }

  function enterEditMode() {
    isLiveMode = false;
    document.getElementById('thr-fp-btn-edit').classList.add('thr-fp-btn--active');
    document.getElementById('thr-fp-btn-live').classList.remove('thr-fp-btn--active');
    if (liveInterval) { clearInterval(liveInterval); liveInterval = null; }
    statusLayer.destroyChildren(); statusLayer.batchDraw();
    furnitureLayer.find('Rect,Circle').forEach(n => n.draggable(true));
  }

  function fetchLiveStatus() {
    if (!currentFloor) return;
    const today = todayLocal();
    apiFetch('GET', `reservations?date=${today}&status=confirmed&per_page=100`).then(data => {
      const rows = (data.data || data || []);
      const bookedFurniture = {};
      rows.forEach(r => {
        (r.furniture_ids || []).forEach(fid => { bookedFurniture[fid] = r.status; });
      });
      apiFetch('GET', `reservations?date=${today}&status=seated&per_page=100`).then(data2 => {
        (data2.data || []).forEach(r => {
          (r.furniture_ids || []).forEach(fid => { bookedFurniture[fid] = 'seated'; });
        });
        renderLiveStatus(bookedFurniture);
      });
    });
  }

  function renderLiveStatus(bookedMap) {
    statusLayer.destroyChildren();
    Object.values(furnitureData).forEach(item => {
      const shape = furnitureLayer.findOne('#' + item.id);
      if (!shape) return;
      const status = bookedMap[item.id] ? (bookedMap[item.id] === 'seated' ? 'seated' : 'reserved')
                   : (item.is_available ? 'available' : 'blocked');
      const color  = STATUS_COLORS[status] || STATUS_COLORS.available;
      const info   = types[item.type] || {};
      const isCircle = info.shape === 'circle';
      let overlay;
      if (isCircle) {
        overlay = new Konva.Circle({
          x: item.pos_x, y: item.pos_y,
          radius: Math.min(item.width || 60, item.height || 60) / 2,
          fill: color, opacity: 0.55, listening: false,
        });
      } else {
        overlay = new Konva.Rect({
          x: item.pos_x, y: item.pos_y,
          width: item.width || 80, height: item.height || 50,
          offsetX: (item.width || 80) / 2, offsetY: (item.height || 50) / 2,
          rotation: item.rotation_deg || 0,
          fill: color, opacity: 0.55,
          cornerRadius: 3, listening: false,
        });
      }
      statusLayer.add(overlay);
    });
    statusLayer.batchDraw();
  }

  // ── Floor add modal ───────────────────────────────────────────────────────
  function showAddFloorModal() {
    const backdrop = document.createElement('div');
    backdrop.className = 'thr-fp-modal-backdrop';
    backdrop.innerHTML = `
      <div class="thr-fp-modal">
        <h3>Add Floor</h3>
        <label>Floor name</label>
        <input type="text" id="thr-fp-new-floor-name" placeholder="Ground Floor" value="">
        <label>Floor number</label>
        <input type="number" id="thr-fp-new-floor-num" value="${floors.length + 1}" min="1">
        <div class="thr-fp-modal-actions">
          <button class="thr-fp-btn" id="thr-fp-modal-cancel">Cancel</button>
          <button class="thr-fp-btn thr-fp-btn--primary" id="thr-fp-modal-confirm">Create</button>
        </div>
      </div>
    `;
    document.body.appendChild(backdrop);
    document.getElementById('thr-fp-modal-cancel').addEventListener('click', () => backdrop.remove());
    document.getElementById('thr-fp-modal-confirm').addEventListener('click', () => {
      const name = document.getElementById('thr-fp-new-floor-name').value.trim() || 'New Floor';
      const num  = parseInt(document.getElementById('thr-fp-new-floor-num').value, 10) || 1;
      apiFetch('POST', 'floor-plans', { name, floor_number: num, is_active: 1, width_px: 1200, height_px: 800 })
        .then(floor => {
          floors.push(floor);
          renderFloorTabs();
          selectFloor(floor.id);
          backdrop.remove();
        });
    });
  }

  // ── Background upload ─────────────────────────────────────────────────────
  function triggerBgUpload() {
    if (!currentFloor) { alert('Select a floor first.'); return; }
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp,application/pdf';
    input.onchange = function () {
      const file = this.files[0];
      if (!file) return;
      const form = new FormData();
      form.append('background', file);
      fetch(`${api}floor-plans/${currentFloor.id}/background`, {
        method: 'POST',
        headers: { 'X-WP-Nonce': nonce },
        body: form,
      })
        .then(r => r.json())
        .then(data => {
          if (data.url) {
            currentFloor.background_url = data.url;
            loadBackground(data.url);
          } else {
            alert(data.message || 'Upload failed.');
          }
        })
        .catch(() => alert('Upload failed.'));
    };
    input.click();
  }

  // ── Toolbar bindings ──────────────────────────────────────────────────────
  function bindToolbar() {
    document.getElementById('thr-fp-floor-select').addEventListener('change', function () {
      if (confirmDirty()) selectFloor(parseInt(this.value, 10));
    });

    document.getElementById('thr-fp-btn-add-floor').addEventListener('click', showAddFloorModal);
    document.getElementById('thr-fp-btn-upload-bg').addEventListener('click', triggerBgUpload);
    document.getElementById('thr-fp-btn-save').addEventListener('click', saveAll);
    document.getElementById('thr-fp-btn-delete').addEventListener('click', deleteSelectedFurniture);
    document.getElementById('thr-fp-btn-zoom-in').addEventListener('click', () => adjustZoom(1.2));
    document.getElementById('thr-fp-btn-zoom-out').addEventListener('click', () => adjustZoom(1 / 1.2));
    document.getElementById('thr-fp-btn-zoom-fit').addEventListener('click', fitToScreen);
    document.getElementById('thr-fp-btn-edit').addEventListener('click', enterEditMode);
    document.getElementById('thr-fp-btn-live').addEventListener('click', enterLiveMode);
  }

  // ── Palette bindings ──────────────────────────────────────────────────────
  function bindPalette() {
    document.querySelectorAll('[data-place-type]').forEach(el => {
      el.addEventListener('click', function () {
        const type = this.dataset.placeType;
        setPendingType(type);
      });
    });
  }

  function setPendingType(type) {
    pendingType = type;
    const wrap = document.getElementById('thr-fp-canvas-wrap');
    wrap.classList.add('thr-fp--crosshair');
    setSaveStatus('Click canvas to place furniture (Esc to cancel)');
  }

  function cancelPendingType() {
    pendingType = null;
    document.getElementById('thr-fp-canvas-wrap').classList.remove('thr-fp--crosshair');
    setSaveStatus('');
  }

  // ── Helpers ───────────────────────────────────────────────────────────────
  function clearCanvas() {
    furnitureLayer.destroyChildren();
    furnitureLayer.add(transformer);
    labelLayer.destroyChildren();
    statusLayer.destroyChildren();
    bgLayer.destroyChildren();
    bgLayer.add(new Konva.Rect({ x: 0, y: 0, width: stage.width(), height: stage.height(), fill: '#1c1a17' }));
    furnitureLayer.batchDraw();
    labelLayer.batchDraw();
    bgLayer.batchDraw();
  }

  function adjustZoom(factor) {
    const newScale = Math.max(0.2, Math.min(4, stage.scaleX() * factor));
    stage.scale({ x: newScale, y: newScale });
    updateZoomLabel(newScale);
    stage.batchDraw();
  }

  function fitToScreen() {
    stage.scale({ x: 1, y: 1 });
    stage.position({ x: 0, y: 0 });
    updateZoomLabel(1);
    stage.batchDraw();
  }

  function updateZoomLabel(scale) {
    const lbl = document.getElementById('thr-fp-zoom-label');
    if (lbl) lbl.textContent = Math.round(scale * 100) + '%';
  }

  function markDirty(flag = true) {
    isDirty = flag;
    if (flag) setSaveStatus('Unsaved changes');
  }

  function setSaveStatus(msg, cls = '') {
    const el = document.getElementById('thr-fp-save-status');
    if (!el) return;
    el.textContent = msg;
    el.className   = cls || (
      msg === 'saving'           ? 'saving' :
      msg === 'Unsaved changes'  ? ''       :
      ''
    );
    if (msg === 'saving') el.className = 'saving';
    if (msg === 'saved')  el.className = 'saved';
    if (msg === 'error')  el.className = 'error';
  }

  function confirmDirty() {
    if (!isDirty) return true;
    return confirm('You have unsaved changes. Continue without saving?');
  }

  function todayLocal() {
    return new Date(Date.now() + 7 * 3600 * 1000).toISOString().slice(0, 10);
  }

  function escH(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  function apiFetch(method, path, body) {
    const opts = {
      method,
      headers: { 'X-WP-Nonce': nonce },
    };
    if (body && method !== 'GET') {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    }
    return fetch(`${api}${path}`, opts)
      .then(r => r.json())
      .then(data => {
        if (data && data.code && data.message) throw new Error(data.message);
        return data;
      });
  }

})();
