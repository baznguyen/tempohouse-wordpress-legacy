'use client'

import { useCanvasStore } from '@/store/canvas-store'
import { getFurnitureConfig } from '@/lib/canvas/furniture-config'
import styles from './PropertiesPanel.module.css'

export function PropertiesPanel() {
  const {
    selectedId, items,
    setPax, setLabel, rotateItem, deleteItem, duplicateItem, unjoinItem,
    clearSelection,
  } = useCanvasStore()

  const item = items.find(i => i.id === selectedId)
  const isOpen = !!item
  const cfg = item ? getFurnitureConfig(item.type, item.variant) : null

  return (
    <div className={`${styles.panel} ${!isOpen ? styles.hidden : ''}`}>
      {item && cfg && (
        <>
          <div className={styles.header}>
            <div>
              <div className={styles.type}>{cfg.label}</div>
              {item.variant && <div className={styles.variant}>{item.variant}</div>}
            </div>
            <button className={styles.closeBtn} onClick={clearSelection} title="Close">
              <CloseIcon />
            </button>
          </div>

          <div className={styles.body}>
            {/* Pax */}
            {cfg.hasSeating && (
              <div className={styles.field}>
                <span className={styles.label}>Seats</span>
                <div className={styles.stepper}>
                  <button
                    className={styles.stepBtn}
                    onClick={() => setPax(item.id, item.pax - 1)}
                    disabled={item.pax <= 0}
                  >−</button>
                  <span className={styles.stepVal}>{item.pax}</span>
                  <button
                    className={styles.stepBtn}
                    onClick={() => setPax(item.id, item.pax + 1)}
                    disabled={item.pax >= cfg.maxPax}
                  >+</button>
                </div>
                <span className={styles.maxHint}>max {cfg.maxPax} pax</span>
              </div>
            )}

            {/* Label */}
            <div className={styles.field}>
              <span className={styles.label}>Label</span>
              <input
                className={styles.input}
                type="text"
                value={item.label}
                onChange={e => setLabel(item.id, e.target.value)}
                placeholder="e.g. VIP, Head Table"
              />
            </div>

            {/* Rotation */}
            <div className={styles.field}>
              <span className={styles.label}>Rotation</span>
              <div className={styles.rotGroup}>
                {[0, 45, 90, 180, 270].map(deg => (
                  <button
                    key={deg}
                    className={`${styles.rotBtn} ${item.rotation === deg ? styles.active : ''}`}
                    onClick={() => rotateItem(item.id, deg)}
                  >
                    {deg}°
                  </button>
                ))}
              </div>
              <input
                type="range"
                min={0} max={360} step={5}
                value={item.rotation}
                className={styles.slider}
                onChange={e => rotateItem(item.id, Number(e.target.value))}
              />
            </div>

            {/* Join group */}
            {item.joinGroup && (
              <div className={styles.field}>
                <span className={styles.label}>Join group</span>
                <button className={styles.unjoinBtn} onClick={() => unjoinItem(item.id)}>
                  Remove from group
                </button>
              </div>
            )}

            <div className={styles.divider} />

            {/* Actions */}
            <div className={styles.actions}>
              <button className={styles.dupBtn} onClick={() => duplicateItem(item.id)}>
                Duplicate
              </button>
              <button className={styles.delBtn} onClick={() => { deleteItem(item.id) }}>
                Delete
              </button>
            </div>

            {/* Dimensions */}
            {cfg.widthMm > 0 && (
              <div className={styles.dims}>{cfg.widthMm} × {cfg.heightMm} mm</div>
            )}
          </div>
        </>
      )}
    </div>
  )
}

function CloseIcon() {
  return (
    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
      <path d="M2 2l10 10M12 2L2 12" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round"/>
    </svg>
  )
}
