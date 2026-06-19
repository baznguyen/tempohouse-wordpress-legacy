'use client'

import { create } from 'zustand'
import { produce } from 'immer'
import { nanoid } from 'nanoid'
import type { CanvasItem, Zone, Notation, CanvasSnapshot, ItemType } from '@/types/layout'
import { getFurnitureConfig, snapToGrid } from '@/lib/canvas/furniture-config'

const HISTORY_LIMIT = 40

interface CanvasState {
  // Data
  items:      CanvasItem[]
  zones:      Zone[]
  notations:  Notation[]
  layoutId:   string | null

  // Selection
  selectedId:    string | null
  selectedIds:   string[]        // multi-select

  // Placement mode
  placingType:    ItemType | null
  placingVariant: string | null

  // Tool modes
  snapEnabled:  boolean
  zoneDrawMode: boolean
  notationMode: boolean

  // Dirty flag
  dirty: boolean

  // Undo/redo
  past:   CanvasSnapshot[]
  future: CanvasSnapshot[]

  // Actions
  setLayoutId:    (id: string) => void
  loadSnapshot:   (snap: CanvasSnapshot) => void

  // Placement
  startPlacing:   (type: ItemType, variant?: string) => void
  cancelPlacing:  () => void
  placeItem:      (x: number, y: number) => void

  // Item manipulation
  moveItem:       (id: string, x: number, y: number) => void
  rotateItem:     (id: string, deg: number) => void
  setPax:         (id: string, pax: number) => void
  setLabel:       (id: string, label: string) => void
  deleteItem:     (id: string) => void
  duplicateItem:  (id: string) => void

  // Selection
  select:         (id: string | null) => void
  multiSelect:    (id: string) => void
  clearSelection: () => void

  // Zones
  addZone:        (zone: Omit<Zone, 'id' | 'layoutId'>) => void
  updateZone:     (id: string, patch: Partial<Zone>) => void
  deleteZone:     (id: string) => void

  // Notations
  addNotation:    (x: number, y: number, title: string, body: string, staffOnly: boolean) => void
  updateNotation: (id: string, patch: Partial<Notation>) => void
  deleteNotation: (id: string) => void

  // Table joining
  joinItems:      (idA: string, idB: string) => void
  unjoinItem:     (id: string) => void

  // Undo/redo
  undo: () => void
  redo: () => void

  // Modes
  toggleSnap:       () => void
  setZoneDrawMode:  (v: boolean) => void
  setNotationMode:  (v: boolean) => void
}

function snapshot(state: Pick<CanvasState, 'items' | 'zones' | 'notations'>): CanvasSnapshot {
  return {
    items:     state.items.map(i => ({ ...i })),
    zones:     state.zones.map(z => ({ ...z })),
    notations: state.notations.map(n => ({ ...n })),
  }
}

function pushHistory(state: CanvasState) {
  state.past.push(snapshot(state))
  if (state.past.length > HISTORY_LIMIT) state.past.shift()
  state.future = []
}

function nextNotationRef(notations: Notation[]): string {
  const used = new Set(notations.map(n => n.ref))
  for (let i = 0; i < 26; i++) {
    const ref = String.fromCharCode(65 + i) // A–Z
    if (!used.has(ref)) return ref
  }
  return String(notations.length + 1)
}

export const useCanvasStore = create<CanvasState>((set, get) => ({
  items:      [],
  zones:      [],
  notations:  [],
  layoutId:   null,
  selectedId:    null,
  selectedIds:   [],
  placingType:    null,
  placingVariant: null,
  snapEnabled:  true,
  zoneDrawMode: false,
  notationMode: false,
  dirty: false,
  past:   [],
  future: [],

  setLayoutId: (id) => set({ layoutId: id }),

  loadSnapshot: (snap) => set({
    items:     snap.items,
    zones:     snap.zones,
    notations: snap.notations,
    past:      [],
    future:    [],
    dirty:     false,
    selectedId: null,
    selectedIds: [],
  }),

  // ── Placement ────────────────────────────────────────────────────────────

  startPlacing: (type, variant) => set({
    placingType: type,
    placingVariant: variant ?? null,
    selectedId: null,
    selectedIds: [],
    zoneDrawMode: false,
    notationMode: false,
  }),

  cancelPlacing: () => set({ placingType: null, placingVariant: null }),

  placeItem: (x, y) => set(produce((s: CanvasState) => {
    const { placingType, placingVariant, snapEnabled } = s
    if (!placingType) return
    const cfg = getFurnitureConfig(placingType, placingVariant ?? undefined)
    if (!cfg) return
    pushHistory(s)
    const snappedX = snapEnabled ? snapToGrid(x) : x
    const snappedY = snapEnabled ? snapToGrid(y) : y
    const item: CanvasItem = {
      id:       nanoid(),
      type:     placingType,
      variant:  placingVariant ?? undefined,
      x:        snappedX,
      y:        snappedY,
      rotation: 0,
      pax:      cfg.defaultPax,
      label:    '',
    }
    s.items.push(item)
    s.selectedId = item.id
    s.dirty = true
    // Stay in placing mode for repeated placement
  })),

  // ── Item manipulation ─────────────────────────────────────────────────────

  moveItem: (id, x, y) => set(produce((s: CanvasState) => {
    const item = s.items.find(i => i.id === id)
    if (!item) return
    const snappedX = s.snapEnabled ? snapToGrid(x) : x
    const snappedY = s.snapEnabled ? snapToGrid(y) : y
    if (item.x === snappedX && item.y === snappedY) return
    pushHistory(s)
    item.x = snappedX
    item.y = snappedY
    s.dirty = true
  })),

  rotateItem: (id, deg) => set(produce((s: CanvasState) => {
    const item = s.items.find(i => i.id === id)
    if (!item) return
    pushHistory(s)
    item.rotation = ((deg % 360) + 360) % 360
    s.dirty = true
  })),

  setPax: (id, pax) => set(produce((s: CanvasState) => {
    const item = s.items.find(i => i.id === id)
    if (!item) return
    const cfg = getFurnitureConfig(item.type, item.variant)
    if (!cfg) return
    pushHistory(s)
    item.pax = Math.max(0, Math.min(pax, cfg.maxPax))
    s.dirty = true
  })),

  setLabel: (id, label) => set(produce((s: CanvasState) => {
    const item = s.items.find(i => i.id === id)
    if (!item) return
    item.label = label
    s.dirty = true
  })),

  deleteItem: (id) => set(produce((s: CanvasState) => {
    pushHistory(s)
    s.items = s.items.filter(i => i.id !== id)
    if (s.selectedId === id) s.selectedId = null
    s.selectedIds = s.selectedIds.filter(sid => sid !== id)
    s.dirty = true
  })),

  duplicateItem: (id) => set(produce((s: CanvasState) => {
    const item = s.items.find(i => i.id === id)
    if (!item) return
    pushHistory(s)
    const dupe: CanvasItem = { ...item, id: nanoid(), x: item.x + 3, y: item.y + 3, joinGroup: undefined }
    s.items.push(dupe)
    s.selectedId = dupe.id
    s.dirty = true
  })),

  // ── Selection ─────────────────────────────────────────────────────────────

  select: (id) => set({ selectedId: id, selectedIds: id ? [id] : [] }),

  multiSelect: (id) => set(produce((s: CanvasState) => {
    if (s.selectedIds.includes(id)) {
      s.selectedIds = s.selectedIds.filter(sid => sid !== id)
      s.selectedId = s.selectedIds[0] ?? null
    } else {
      s.selectedIds.push(id)
      s.selectedId = id
    }
  })),

  clearSelection: () => set({ selectedId: null, selectedIds: [] }),

  // ── Zones ─────────────────────────────────────────────────────────────────

  addZone: (zone) => set(produce((s: CanvasState) => {
    pushHistory(s)
    s.zones.push({ ...zone, id: nanoid(), layoutId: s.layoutId ?? '' })
    s.dirty = true
  })),

  updateZone: (id, patch) => set(produce((s: CanvasState) => {
    const z = s.zones.find(z => z.id === id)
    if (!z) return
    Object.assign(z, patch)
    s.dirty = true
  })),

  deleteZone: (id) => set(produce((s: CanvasState) => {
    pushHistory(s)
    s.zones = s.zones.filter(z => z.id !== id)
    s.dirty = true
  })),

  // ── Notations ─────────────────────────────────────────────────────────────

  addNotation: (x, y, title, body, staffOnly) => set(produce((s: CanvasState) => {
    pushHistory(s)
    s.notations.push({
      id: nanoid(),
      layoutId: s.layoutId ?? '',
      ref: nextNotationRef(s.notations),
      x, y, title, body, staffOnly,
    })
    s.dirty = true
  })),

  updateNotation: (id, patch) => set(produce((s: CanvasState) => {
    const n = s.notations.find(n => n.id === id)
    if (!n) return
    Object.assign(n, patch)
    s.dirty = true
  })),

  deleteNotation: (id) => set(produce((s: CanvasState) => {
    pushHistory(s)
    s.notations = s.notations.filter(n => n.id !== id)
    s.dirty = true
  })),

  // ── Table joining ──────────────────────────────────────────────────────────

  joinItems: (idA, idB) => set(produce((s: CanvasState) => {
    const a = s.items.find(i => i.id === idA)
    const b = s.items.find(i => i.id === idB)
    if (!a || !b) return
    pushHistory(s)
    const groupId = a.joinGroup ?? b.joinGroup ?? nanoid()
    a.joinGroup = groupId
    b.joinGroup = groupId
    s.dirty = true
  })),

  unjoinItem: (id) => set(produce((s: CanvasState) => {
    const item = s.items.find(i => i.id === id)
    if (!item?.joinGroup) return
    const groupId = item.joinGroup
    pushHistory(s)
    // Remove from group (if only 2 members, clear both)
    const members = s.items.filter(i => i.joinGroup === groupId)
    if (members.length <= 2) {
      members.forEach(m => { m.joinGroup = undefined })
    } else {
      item.joinGroup = undefined
    }
    s.dirty = true
  })),

  // ── Undo/redo ─────────────────────────────────────────────────────────────

  undo: () => set(produce((s: CanvasState) => {
    if (s.past.length === 0) return
    s.future.unshift(snapshot(s))
    const prev = s.past.pop()!
    s.items     = prev.items
    s.zones     = prev.zones
    s.notations = prev.notations
    s.dirty = true
  })),

  redo: () => set(produce((s: CanvasState) => {
    if (s.future.length === 0) return
    s.past.push(snapshot(s))
    const next = s.future.shift()!
    s.items     = next.items
    s.zones     = next.zones
    s.notations = next.notations
    s.dirty = true
  })),

  // ── Modes ─────────────────────────────────────────────────────────────────

  toggleSnap:      () => set(s => ({ snapEnabled: !s.snapEnabled })),
  setZoneDrawMode: (v) => set({ zoneDrawMode: v, placingType: null, notationMode: false }),
  setNotationMode: (v) => set({ notationMode: v, placingType: null, zoneDrawMode: false }),
}))

