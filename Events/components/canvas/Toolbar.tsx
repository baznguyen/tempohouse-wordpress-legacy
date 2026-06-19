'use client'

import { useState } from 'react'
import { useCanvasStore } from '@/store/canvas-store'
import { FURNITURE } from '@/lib/canvas/furniture-config'
import type { ItemType } from '@/types/layout'
import styles from './Toolbar.module.css'

const GROUPS = [
  {
    label: 'Tables',
    items: [
      { type: 'rect-table'   as ItemType, label: 'Rect',   hasVariants: true  },
      { type: 'round-table'  as ItemType, label: 'Round',  hasVariants: false },
      { type: 'square-table' as ItemType, label: 'Square', hasVariants: false },
    ],
  },
  {
    label: 'Seating',
    items: [
      { type: 'bar-stool'     as ItemType, label: 'Stool',  hasVariants: false },
      { type: 'theatre-chair' as ItemType, label: 'Chair',  hasVariants: false },
      { type: 'lounge'        as ItemType, label: 'Lounge', hasVariants: false },
    ],
  },
  {
    label: 'Coffee',
    items: [
      { type: 'coffee-square' as ItemType, label: 'Sq',   hasVariants: false },
      { type: 'coffee-round'  as ItemType, label: 'Rd',   hasVariants: false },
      { type: 'coffee-rect'   as ItemType, label: 'Rect', hasVariants: false },
      { type: 'side-table'    as ItemType, label: 'Side', hasVariants: false },
    ],
  },
  {
    label: 'AV / Decor',
    items: [
      { type: 'centrepiece' as ItemType, label: 'Floral',   hasVariants: false },
      { type: 'backdrop'    as ItemType, label: 'Backdrop', hasVariants: true  },
      { type: 'projector'   as ItemType, label: 'Projector',hasVariants: false },
    ],
  },
]

export function Toolbar() {
  const {
    placingType, placingVariant, startPlacing, cancelPlacing,
    zoneDrawMode, setZoneDrawMode,
    notationMode, setNotationMode,
    snapEnabled, toggleSnap,
    undo, redo, past, future,
  } = useCanvasStore()

  const [openVariant, setOpenVariant] = useState<ItemType | null>(null)

  function handleItemClick(type: ItemType, hasVariants: boolean) {
    if (hasVariants) {
      setOpenVariant(openVariant === type ? null : type)
      return
    }
    if (placingType === type && !openVariant) {
      cancelPlacing()
    } else {
      startPlacing(type)
      setOpenVariant(null)
      setZoneDrawMode(false)
      setNotationMode(false)
    }
  }

  function handleVariantClick(type: ItemType, variant: string) {
    startPlacing(type, variant)
    setOpenVariant(null)
    setZoneDrawMode(false)
    setNotationMode(false)
  }

  return (
    <div className={styles.toolbar}>
      {GROUPS.map((group, gi) => (
        <div key={group.label} className={styles.group}>
          {gi > 0 && <div className={styles.groupSep} />}
          <span className={styles.groupLabel}>{group.label}</span>
          {group.items.map(({ type, label, hasVariants }) => {
            const isActive = placingType === type && openVariant !== type
            const isOpen = openVariant === type
            const variants = hasVariants ? FURNITURE.filter(f => f.type === type && f.variant) : []
            return (
              <div key={type} className={styles.itemWrap}>
                <button
                  className={`${styles.item} ${isActive || isOpen ? styles.active : ''}`}
                  onClick={() => handleItemClick(type, hasVariants)}
                  title={label}
                >
                  <span className={styles.itemIcon}><FurnitureIcon type={type} /></span>
                  <span className={styles.itemLabel}>{label}</span>
                  {hasVariants && <span className={styles.chevron}>▾</span>}
                </button>
                {isOpen && (
                  <div className={styles.variantMenu}>
                    {variants.map(v => (
                      <button
                        key={v.variant}
                        className={`${styles.variantItem} ${placingType === type && placingVariant === v.variant ? styles.active : ''}`}
                        onClick={() => handleVariantClick(type, v.variant!)}
                      >
                        {v.label}
                      </button>
                    ))}
                  </div>
                )}
              </div>
            )
          })}
        </div>
      ))}

      {/* Zone + notation tools */}
      <div className={styles.groupSep} />
      <div className={styles.group}>
        <span className={styles.groupLabel}>Zones</span>
        <button
          className={`${styles.item} ${zoneDrawMode ? styles.active : ''}`}
          onClick={() => { setZoneDrawMode(!zoneDrawMode); cancelPlacing(); setOpenVariant(null) }}
          title="Draw Zone"
        >
          <span className={styles.itemIcon}><ZoneIcon /></span>
          <span className={styles.itemLabel}>Zone</span>
        </button>
        <button
          className={`${styles.item} ${notationMode ? styles.active : ''}`}
          onClick={() => { setNotationMode(!notationMode); cancelPlacing(); setOpenVariant(null) }}
          title="Add Notation pin"
        >
          <span className={styles.itemIcon}><NoteIcon /></span>
          <span className={styles.itemLabel}>Note</span>
        </button>
        <button
          className={`${styles.item} ${placingType === 'text-label' ? styles.active : ''}`}
          onClick={() => { startPlacing('text-label'); setOpenVariant(null) }}
          title="Text label"
        >
          <span className={styles.itemIcon}><TextIcon /></span>
          <span className={styles.itemLabel}>Label</span>
        </button>
      </div>

      {/* Right-side controls */}
      <div className={styles.rightControls}>
        <button onClick={() => undo()} disabled={past.length === 0} className={styles.ctrl} title="Undo (⌘Z)">
          <UndoIcon /> Undo
        </button>
        <button onClick={() => redo()} disabled={future.length === 0} className={styles.ctrl} title="Redo">
          <RedoIcon /> Redo
        </button>
        <button
          onClick={toggleSnap}
          className={`${styles.ctrl} ${snapEnabled ? styles.active : ''}`}
          title="Snap to grid"
        >
          <SnapIcon /> {snapEnabled ? 'Snap on' : 'Snap off'}
        </button>
      </div>
    </div>
  )
}

function FurnitureIcon({ type }: { type: ItemType }) {
  const map: Record<string, React.ReactElement> = {
    'rect-table':    <><rect x="3" y="5" width="10" height="6" rx="1" fill="currentColor" opacity="0.85"/></>,
    'round-table':   <><circle cx="8" cy="8" r="4.5" fill="currentColor" opacity="0.85"/></>,
    'square-table':  <><rect x="3" y="3" width="10" height="10" rx="1.5" fill="currentColor" opacity="0.85"/></>,
    'bar-stool':     <><circle cx="8" cy="8" r="3.5" fill="currentColor" opacity="0.85"/><circle cx="8" cy="8" r="1.5" fill="var(--surface)" opacity="0.7"/></>,
    'theatre-chair': <><rect x="4" y="5" width="8" height="7" rx="2" fill="currentColor" opacity="0.85"/><rect x="4" y="4" width="8" height="3" rx="1" fill="currentColor" opacity="0.5"/></>,
    'lounge':        <><rect x="2" y="6" width="12" height="5" rx="2" fill="currentColor" opacity="0.85"/><rect x="2" y="6" width="2" height="5" rx="1" fill="currentColor" opacity="0.6"/><rect x="12" y="6" width="2" height="5" rx="1" fill="currentColor" opacity="0.6"/></>,
    'coffee-square': <><rect x="4" y="4" width="8" height="8" rx="1" fill="currentColor" opacity="0.6"/></>,
    'coffee-round':  <><circle cx="8" cy="8" r="4" fill="currentColor" opacity="0.6"/></>,
    'coffee-rect':   <><rect x="2" y="5" width="12" height="6" rx="1" fill="currentColor" opacity="0.6"/></>,
    'side-table':    <><rect x="5" y="5" width="6" height="6" rx="1" fill="currentColor" opacity="0.6"/></>,
    'centrepiece':   <><circle cx="8" cy="8" r="4" fill="none" stroke="currentColor" strokeWidth="1.5"/><circle cx="8" cy="5" r="1.5" fill="currentColor" opacity="0.6"/><circle cx="11" cy="9.5" r="1.5" fill="currentColor" opacity="0.6"/><circle cx="5" cy="9.5" r="1.5" fill="currentColor" opacity="0.6"/></>,
    'backdrop':      <><rect x="6" y="2" width="4" height="12" rx="1" fill="currentColor" opacity="0.8"/><line x1="4" y1="2" x2="4" y2="14" stroke="currentColor" strokeWidth="1" opacity="0.4"/><line x1="12" y1="2" x2="12" y2="14" stroke="currentColor" strokeWidth="1" opacity="0.4"/></>,
    'projector':     <><polygon points="4,5 12,8 4,11" fill="currentColor" opacity="0.8"/></>,
    'text-label':    <><text x="2" y="12" fontSize="10" fill="currentColor" fontFamily="serif" fontStyle="italic">Aa</text></>,
  }
  return (
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
      {map[type] ?? <rect x="3" y="3" width="10" height="10" rx="1" fill="currentColor"/>}
    </svg>
  )
}

function ZoneIcon() { return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="12" height="12" rx="1.5" stroke="currentColor" strokeWidth="1.5" strokeDasharray="3 2"/></svg> }
function NoteIcon() { return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="5.5" stroke="currentColor" strokeWidth="1.5"/><text x="5.2" y="11" fontSize="7" fill="currentColor" fontFamily="serif" fontWeight="bold">A</text></svg> }
function TextIcon() { return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><text x="3" y="13" fontSize="11" fill="currentColor" fontFamily="serif">T</text></svg> }
function UndoIcon() { return <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M3 7H10a3 3 0 010 6H7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/><path d="M3 7L6 4M3 7l3 3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg> }
function RedoIcon() { return <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><path d="M13 7H6a3 3 0 000 6h3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/><path d="M13 7l-3-3M13 7l-3 3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg> }
function SnapIcon() { return <svg width="13" height="13" viewBox="0 0 16 16" fill="none"><circle cx="4" cy="4" r="1.5" fill="currentColor"/><circle cx="12" cy="4" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="8" cy="8" r="1.5" fill="currentColor"/></svg> }
