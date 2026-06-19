'use client'

import Link from 'next/link'
import { useState } from 'react'
import type { LayoutFull } from '@/types/layout'
import { useCanvasStore } from '@/store/canvas-store'
import styles from './LayoutHeader.module.css'

interface Props {
  layout: LayoutFull
  saving: boolean
  saveError: string | null
  onSave: () => void
}

export function LayoutHeader({ layout, saving, saveError, onSave }: Props) {
  const { undo, redo, past, future, snapEnabled, toggleSnap } = useCanvasStore()

  return (
    <header className={styles.header}>
      <div className={styles.left}>
        <Link href="/layouts" className={styles.back}>
          <BackIcon />
        </Link>
        <div className={styles.info}>
          <h1 className={styles.name}>{layout.name}</h1>
          {layout.eventDate && (
            <span className={styles.date}>
              {new Date(layout.eventDate).toLocaleDateString('en-AU', {
                day: 'numeric', month: 'short', year: 'numeric'
              })}
              {layout.eventTime && ` · ${layout.eventTime}`}
            </span>
          )}
        </div>
      </div>

      <div className={styles.center}>
        <button onClick={() => undo()} disabled={past.length === 0} className={styles.iconBtn} title="Undo (⌘Z)">
          <UndoIcon />
        </button>
        <button onClick={() => redo()} disabled={future.length === 0} className={styles.iconBtn} title="Redo (⌘⇧Z)">
          <RedoIcon />
        </button>
        <div className={styles.divider} />
        <button onClick={toggleSnap} className={`${styles.iconBtn} ${snapEnabled ? styles.active : ''}`} title="Snap to grid">
          <SnapIcon />
          <span className={styles.btnLabel}>{snapEnabled ? 'Snap on' : 'Snap off'}</span>
        </button>
      </div>

      <div className={styles.right}>
        {saving && <span className={styles.saveStatus}>Saving…</span>}
        {saveError && <span className={styles.saveError}>{saveError}</span>}
        {!saving && !saveError && <span className={styles.saveStatus} style={{ opacity: 0.4 }}>Saved</span>}
        <button onClick={onSave} className={styles.saveBtn}>Save</button>
        <CapacityBadge />
      </div>
    </header>
  )
}

function CapacityBadge() {
  const items = useCanvasStore(s => s.items)
  const total = items.reduce((sum, item) => sum + (item.pax ?? 0), 0)
  return (
    <div className={styles.capacity}>
      <span className={styles.capacityNum}>{total}</span>
      <span className={styles.capacityLabel}>pax</span>
    </div>
  )
}

function BackIcon() {
  return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10 12L6 8l4-4" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
}
function UndoIcon() {
  return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 7H10a3 3 0 010 6H7" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/><path d="M3 7L6 4M3 7l3 3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
}
function RedoIcon() {
  return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13 7H6a3 3 0 000 6h3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/><path d="M13 7l-3-3M13 7l-3 3" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
}
function SnapIcon() {
  return <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="4" cy="4" r="1.5" fill="currentColor"/><circle cx="12" cy="4" r="1.5" fill="currentColor"/><circle cx="4" cy="12" r="1.5" fill="currentColor"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/><circle cx="8" cy="8" r="1.5" fill="currentColor"/></svg>
}
