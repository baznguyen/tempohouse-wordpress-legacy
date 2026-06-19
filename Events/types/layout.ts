// ── Furniture ──────────────────────────────────────────────────────────────

export type ItemType =
  | 'rect-table'
  | 'round-table'
  | 'square-table'
  | 'bar-stool'
  | 'theatre-chair'
  | 'centrepiece'
  | 'backdrop'
  | 'projector'
  | 'lounge'
  | 'coffee-square'
  | 'coffee-round'
  | 'coffee-rect'
  | 'side-table'
  | 'text-label'

export type RectVariant = '800x1200' | '800x1500' | '600x1200' | '500x1200'
export type BackdropVariant = '1200' | '1500' | '2000'

export interface CanvasItem {
  id: string
  type: ItemType
  variant?: RectVariant | BackdropVariant | string
  x: number
  y: number
  rotation: number
  pax: number
  label: string
  zoneId?: string
  joinGroup?: string       // UUID shared by joined tables
  notationRef?: string     // e.g. 'A', 'B'
}

// ── Zones ──────────────────────────────────────────────────────────────────

export interface Zone {
  id: string
  layoutId: string
  name: string
  hexColor: string
  x: number
  y: number
  width: number
  height: number
}

// ── Notations ──────────────────────────────────────────────────────────────

export interface Notation {
  id: string
  layoutId: string
  ref: string           // 'A', 'B', 'C'...
  x: number
  y: number
  title: string
  body: string
  staffOnly: boolean
}

// ── Layout ─────────────────────────────────────────────────────────────────

export type EventType =
  | 'cocktail'
  | 'seated_dinner'
  | 'theatre'
  | 'gallery'
  | 'boardroom'
  | 'custom'

export interface EventLayout {
  id: string
  name: string
  eventType?: EventType
  eventDate?: string     // YYYY-MM-DD
  eventTime?: string     // HH:MM
  notes?: string
  shareToken?: string
  shareEnabled: boolean
  capacity: number
  roomAreaSqm?: number
  createdBy?: string
  createdAt: string
  updatedAt: string
}

// Full layout with children (used in editor)
export interface LayoutFull extends EventLayout {
  items: CanvasItem[]
  zones: Zone[]
  notations: Notation[]
}

// ── Canvas ─────────────────────────────────────────────────────────────────

export interface CanvasSnapshot {
  items: CanvasItem[]
  zones: Zone[]
  notations: Notation[]
}

// ── Furniture config ────────────────────────────────────────────────────────

export interface FurnitureConfig {
  type: ItemType
  variant?: string
  label: string
  widthMm: number
  heightMm: number
  defaultPax: number
  maxPax: number
  hasSeating: boolean   // whether pax panel applies
  category: 'table' | 'seating' | 'decor' | 'av' | 'lounge' | 'label'
}
