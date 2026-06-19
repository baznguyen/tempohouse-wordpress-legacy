'use client'

import { useState } from 'react'
import { useCanvasStore } from '@/store/canvas-store'
import { ConfirmModal } from '@/components/ui/ConfirmModal'
import styles from './LayoutHeader.module.css'

export function StandaloneHeader() {
  const { undo, redo, past, future, snapEnabled, toggleSnap, dirty, items } = useCanvasStore()
  const totalPax = items.reduce((sum, i) => sum + (i.pax ?? 0), 0)
  const [confirmNew, setConfirmNew] = useState(false)

  function handleSave() {
    const { items, zones, notations } = useCanvasStore.getState()
    try {
      localStorage.setItem('tempo-events-canvas', JSON.stringify({ items, zones, notations }))
      useCanvasStore.setState({ dirty: false })
    } catch {}
  }

  function handleNew() {
    setConfirmNew(true)
  }

  function doNew() {
    useCanvasStore.getState().loadSnapshot({ items: [], zones: [], notations: [] })
    localStorage.removeItem('tempo-events-canvas')
    setConfirmNew(false)
  }

  function handleExportJSON() {
    const { items, zones, notations } = useCanvasStore.getState()
    const blob = new Blob([JSON.stringify({ items, zones, notations }, null, 2)], { type: 'application/json' })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `tempo-layout-${new Date().toISOString().slice(0, 10)}.json`
    a.click()
    URL.revokeObjectURL(url)
  }

  return (
    <>
      <header className={styles.header}>
        <div className={styles.left}>
          <div className={styles.info}>
            <h1 className={styles.name}>TEMPO Events</h1>
            <span className={styles.modePill}>Event Layout</span>
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
          <div className={styles.divider} />
          <button onClick={handleNew} className={styles.iconBtn}>
            <span className={styles.btnLabel}>New</span>
          </button>
          <button onClick={handleExportJSON} className={styles.iconBtn}>
            <span className={styles.btnLabel}>Export</span>
          </button>
        </div>

        <div className={styles.right}>
          {dirty && <span className={styles.saveStatus} style={{ color: 'var(--terracotta)' }}>Unsaved</span>}
          {!dirty && <span className={styles.saveStatus}>Saved locally</span>}
          <button onClick={handleSave} className={styles.saveBtn}>Save</button>
          <div className={styles.capacity}>
            <span className={styles.capacityNum}>{totalPax}</span>
            <span className={styles.capacityLabel}>pax</span>
          </div>
        </div>
      </header>

      <ConfirmModal
        open={confirmNew}
        title="Start fresh?"
        message="This will clear the entire canvas. Any unsaved changes will be lost."
        confirmLabel="Clear canvas"
        cancelLabel="Keep editing"
        danger
        onConfirm={doNew}
        onCancel={() => setConfirmNew(false)}
      />
    </>
  )
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
