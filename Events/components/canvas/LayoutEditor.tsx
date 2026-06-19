'use client'

import { useEffect, useRef, useState, useCallback } from 'react'
import dynamic from 'next/dynamic'
import type { LayoutFull } from '@/types/layout'
import { useCanvasStore } from '@/store/canvas-store'
import { Toolbar } from './Toolbar'
import { PropertiesPanel } from './PropertiesPanel'
import { LayoutHeader } from './LayoutHeader'
import styles from './LayoutEditor.module.css'

const EventCanvas = dynamic(() => import('./EventCanvas').then(m => m.EventCanvas), { ssr: false })

interface Props {
  initialLayout: LayoutFull
}

export function LayoutEditor({ initialLayout }: Props) {
  const { loadSnapshot, setLayoutId, dirty } = useCanvasStore()
  const [saving, setSaving] = useState(false)
  const [saveError, setSaveError] = useState<string | null>(null)
  const saveTimer = useRef<ReturnType<typeof setTimeout> | null>(null)

  // Hydrate store on mount
  useEffect(() => {
    setLayoutId(initialLayout.id)
    loadSnapshot({
      items:     initialLayout.items,
      zones:     initialLayout.zones,
      notations: initialLayout.notations,
    })
  }, [initialLayout.id])

  // Auto-save on dirty, debounced 2s
  const save = useCallback(async () => {
    const state = useCanvasStore.getState()
    if (!state.dirty) return
    setSaving(true)
    setSaveError(null)
    try {
      const res = await fetch(`/api/layouts/${initialLayout.id}/save`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          items:     state.items,
          zones:     state.zones,
          notations: state.notations,
        }),
      })
      if (!res.ok) throw new Error('Save failed')
      useCanvasStore.setState({ dirty: false })
    } catch (err) {
      setSaveError('Failed to save')
    } finally {
      setSaving(false)
    }
  }, [initialLayout.id])

  // Watch dirty flag and debounce auto-save
  useEffect(() => {
    if (!dirty) return
    if (saveTimer.current) clearTimeout(saveTimer.current)
    saveTimer.current = setTimeout(save, 2000)
    return () => { if (saveTimer.current) clearTimeout(saveTimer.current) }
  }, [dirty, save])

  return (
    <div className={styles.editor}>
      <LayoutHeader
        layout={initialLayout}
        saving={saving}
        saveError={saveError}
        onSave={save}
      />
      <div className={styles.body}>
        <Toolbar />
        <div className={styles.canvasArea}>
          <EventCanvas layoutId={initialLayout.id} />
          <PropertiesPanel />
        </div>
      </div>
    </div>
  )
}
