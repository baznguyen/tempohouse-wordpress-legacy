'use client'

import { useEffect, useRef, useState, useCallback } from 'react'
import dynamic from 'next/dynamic'
import { useCanvasStore } from '@/store/canvas-store'
import { Toolbar } from './Toolbar'
import { PropertiesPanel } from './PropertiesPanel'
import { StandaloneHeader } from './StandaloneHeader'
import styles from './LayoutEditor.module.css'

// react-konva requires browser canvas API — prevent SSR
const EventCanvas = dynamic(() => import('./EventCanvas').then(m => m.EventCanvas), { ssr: false })

const STORAGE_KEY = 'tempo-events-canvas'

export function StandaloneEditor() {
  const { loadSnapshot, setLayoutId, dirty } = useCanvasStore()
  const [loaded, setLoaded] = useState(false)

  // Hydrate from localStorage on mount
  useEffect(() => {
    setLayoutId('local')
    try {
      const raw = localStorage.getItem(STORAGE_KEY)
      if (raw) {
        const snap = JSON.parse(raw)
        loadSnapshot(snap)
      }
    } catch { /* ignore corrupt storage */ }
    setLoaded(true)
  }, [])

  // Auto-save to localStorage whenever dirty
  useEffect(() => {
    if (!dirty || !loaded) return
    const timer = setTimeout(() => {
      const { items, zones, notations } = useCanvasStore.getState()
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({ items, zones, notations }))
        useCanvasStore.setState({ dirty: false })
      } catch { /* storage full */ }
    }, 800)
    return () => clearTimeout(timer)
  }, [dirty, loaded])

  if (!loaded) return null

  return (
    <div className={styles.editor}>
      <StandaloneHeader />
      <div className={styles.body}>
        <Toolbar />
        <div className={styles.canvasArea}>
          <EventCanvas layoutId="local" />
          <PropertiesPanel />
        </div>
      </div>
    </div>
  )
}
