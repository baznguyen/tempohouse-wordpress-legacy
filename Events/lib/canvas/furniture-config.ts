import type { FurnitureConfig, ItemType } from '@/types/layout'

// Scale: 1 canvas unit = 10mm at 1:50. Default render: 4px per unit.
export const MM_PER_UNIT = 10
export const PX_PER_UNIT = 4   // at default zoom (1:50)

export function mmToUnits(mm: number) { return mm / MM_PER_UNIT }
export function unitsToMm(u: number)  { return u * MM_PER_UNIT }
export function mmToPx(mm: number)    { return (mm / MM_PER_UNIT) * PX_PER_UNIT }

export function snapToGrid(value: number, gridUnits = 1): number {
  return Math.round(value / gridUnits) * gridUnits
}

// ── All furniture items ────────────────────────────────────────────────────

export const FURNITURE: FurnitureConfig[] = [
  // Rectangle tables
  { type: 'rect-table', variant: '800x1200', label: '800×1200', widthMm: 800,  heightMm: 1200, defaultPax: 6, maxPax: 8, hasSeating: true,  category: 'table'   },
  { type: 'rect-table', variant: '800x1500', label: '800×1500', widthMm: 800,  heightMm: 1500, defaultPax: 6, maxPax: 8, hasSeating: true,  category: 'table'   },
  { type: 'rect-table', variant: '600x1200', label: '600×1200', widthMm: 600,  heightMm: 1200, defaultPax: 4, maxPax: 6, hasSeating: true,  category: 'table'   },
  { type: 'rect-table', variant: '500x1200', label: '500×1200', widthMm: 500,  heightMm: 1200, defaultPax: 4, maxPax: 6, hasSeating: true,  category: 'table'   },
  // Round table
  { type: 'round-table',  label: 'Round Ø700', widthMm: 700, heightMm: 700,  defaultPax: 4, maxPax: 4, hasSeating: true,  category: 'table'   },
  // Square table
  { type: 'square-table', label: 'Square 700', widthMm: 700, heightMm: 700,  defaultPax: 4, maxPax: 4, hasSeating: true,  category: 'table'   },
  // Seating — no pax panel
  { type: 'bar-stool',      label: 'Bar Stool',      widthMm: 400, heightMm: 400, defaultPax: 1, maxPax: 1, hasSeating: false, category: 'seating' },
  { type: 'theatre-chair',  label: 'Chair',           widthMm: 450, heightMm: 500, defaultPax: 1, maxPax: 1, hasSeating: false, category: 'seating' },
  // Lounge
  { type: 'lounge',         label: 'Lounge',          widthMm: 1800, heightMm: 800, defaultPax: 3, maxPax: 4, hasSeating: true,  category: 'lounge'  },
  // Coffee tables
  { type: 'coffee-square',  label: 'Coffee Sq',       widthMm: 600, heightMm: 600, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'lounge'  },
  { type: 'coffee-round',   label: 'Coffee Rd',       widthMm: 600, heightMm: 600, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'lounge'  },
  { type: 'coffee-rect',    label: 'Coffee Rect',     widthMm: 1200, heightMm: 600, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'lounge'  },
  { type: 'side-table',     label: 'Side Table',      widthMm: 450, heightMm: 450, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'lounge'  },
  // Decor
  { type: 'centrepiece',    label: 'Centrepiece',     widthMm: 300, heightMm: 300, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'decor'   },
  // AV / staging
  { type: 'backdrop', variant: '1200', label: 'Backdrop 1.2m', widthMm: 1200, heightMm: 2000, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'av' },
  { type: 'backdrop', variant: '1500', label: 'Backdrop 1.5m', widthMm: 1500, heightMm: 2000, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'av' },
  { type: 'backdrop', variant: '2000', label: 'Backdrop 2m',   widthMm: 2000, heightMm: 2000, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'av' },
  { type: 'projector',      label: 'Projector',       widthMm: 400, heightMm: 300, defaultPax: 0, maxPax: 0, hasSeating: false, category: 'av'      },
  // Label
  { type: 'text-label',     label: 'Text Label',      widthMm: 0,   heightMm: 0,  defaultPax: 0, maxPax: 0, hasSeating: false, category: 'label'   },
]

export function getFurnitureConfig(type: ItemType, variant?: string): FurnitureConfig | undefined {
  return FURNITURE.find(f => f.type === type && (!variant || !f.variant || f.variant === variant))
    ?? FURNITURE.find(f => f.type === type)
}

// Chair offset from table edge in mm
export const CHAIR_OFFSET_MM = 120
export const CHAIR_SIZE_MM   = 400

// Toolbar groups for display
export const TOOLBAR_GROUPS = [
  {
    label: 'Tables',
    items: [
      { type: 'rect-table' as ItemType,   label: 'Rect',   hasVariants: true },
      { type: 'round-table' as ItemType,  label: 'Round',  hasVariants: false },
      { type: 'square-table' as ItemType, label: 'Square', hasVariants: false },
    ],
  },
  {
    label: 'Seating',
    items: [
      { type: 'bar-stool' as ItemType,     label: 'Stool',  hasVariants: false },
      { type: 'theatre-chair' as ItemType, label: 'Chair',  hasVariants: false },
      { type: 'lounge' as ItemType,        label: 'Lounge', hasVariants: false },
    ],
  },
  {
    label: 'Coffee',
    items: [
      { type: 'coffee-square' as ItemType, label: 'Sq',    hasVariants: false },
      { type: 'coffee-round' as ItemType,  label: 'Rd',    hasVariants: false },
      { type: 'coffee-rect' as ItemType,   label: 'Rect',  hasVariants: false },
      { type: 'side-table' as ItemType,    label: 'Side',  hasVariants: false },
    ],
  },
  {
    label: 'AV / Decor',
    items: [
      { type: 'centrepiece' as ItemType, label: 'Centrepiece', hasVariants: false },
      { type: 'backdrop' as ItemType,    label: 'Backdrop',    hasVariants: true  },
      { type: 'projector' as ItemType,   label: 'Projector',   hasVariants: false },
    ],
  },
]
