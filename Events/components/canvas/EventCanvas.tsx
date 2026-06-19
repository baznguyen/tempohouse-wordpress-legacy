'use client'

import { useRef, useCallback, useEffect, useState, type ReactElement } from 'react'
import { Stage, Layer, Rect, Line, Transformer } from 'react-konva'
import type Konva from 'konva'
import { useCanvasStore } from '@/store/canvas-store'
import { getFurnitureConfig, PX_PER_UNIT } from '@/lib/canvas/furniture-config'
import { FurnitureItem } from './furniture/FurnitureItem'
import { ZoneRect } from './ZoneRect'
import { NotationPin } from './NotationPin'
import styles from './EventCanvas.module.css'

const STEP = PX_PER_UNIT              // 4px per canvas unit at zoom=1
const GRID_COLOR = 'rgba(26,24,22,0.055)'
const ROOM_W_UNITS = 3000             // 30,000mm = 30m
const ROOM_H_UNITS = 2000             // 20,000mm = 20m

// getRelativePointerPosition() doesn't exist on Konva.Stage (only on Node).
// This manually inverts the Stage's x/y/scale to get stage-local coordinates.
function stagePointerPos(stage: Konva.Stage): { x: number; y: number } | null {
  const ptr = stage.getPointerPosition()
  if (!ptr) return null
  const sc = stage.scaleX()
  return { x: (ptr.x - stage.x()) / sc, y: (ptr.y - stage.y()) / sc }
}

interface Props { layoutId: string }

export function EventCanvas({ layoutId }: Props) {
  const containerRef = useRef<HTMLDivElement>(null)
  const stageRef     = useRef<Konva.Stage | null>(null)
  const trRef        = useRef<Konva.Transformer | null>(null)
  const nodeMap      = useRef<Map<string, Konva.Group>>(new Map())
  const spaceDown    = useRef(false)
  const panState     = useRef<{ startX: number; startY: number; ox: number; oy: number } | null>(null)
  const rubberStart  = useRef<{ x: number; y: number; isZone: boolean } | null>(null)

  const [dims, setDims]   = useState({ w: 0, h: 0 })
  const [rubber, setRubber] = useState<{ x: number; y: number; w: number; h: number } | null>(null)

  const {
    items, zones, notations,
    selectedId, selectedIds,
    placingType, placeItem,
    select, multiSelect, clearSelection,
    rotateItem,
    zoneDrawMode, notationMode, addNotation, addZone,
  } = useCanvasStore()

  // ── Register Konva Group refs from child items ─────────────────────────
  const registerNode = useCallback((id: string, node: Konva.Group | null) => {
    if (node) nodeMap.current.set(id, node)
    else nodeMap.current.delete(id)
  }, [])

  // ── Sync Transformer to selection ─────────────────────────────────────
  useEffect(() => {
    const tr = trRef.current
    if (!tr) return
    const nodes = selectedIds
      .map(id => nodeMap.current.get(id))
      .filter((n): n is Konva.Group => !!n)
    tr.nodes(nodes)
    tr.getLayer()?.batchDraw()
  }, [selectedIds, items])

  // ── Resize observer ────────────────────────────────────────────────────
  useEffect(() => {
    const el = containerRef.current
    if (!el) return
    const ro = new ResizeObserver(entries => {
      const { width, height } = entries[0].contentRect
      setDims({ w: width, h: height })
    })
    ro.observe(el)
    return () => ro.disconnect()
  }, [])

  // ── Keyboard shortcuts (ported from WP builder) ───────────────────────
  useEffect(() => {
    const onDown = (e: KeyboardEvent) => {
      if (e.target instanceof HTMLInputElement || e.target instanceof HTMLTextAreaElement) return
      if (e.code === 'Space') { e.preventDefault(); spaceDown.current = true }
      const s = useCanvasStore.getState()
      if ((e.metaKey || e.ctrlKey) && !e.shiftKey && e.key === 'z') { s.undo(); e.preventDefault() }
      if ((e.metaKey || e.ctrlKey) && (e.key === 'y' || (e.shiftKey && e.key === 'z'))) { s.redo(); e.preventDefault() }
      if (e.key === 'Escape') { s.cancelPlacing(); s.clearSelection(); s.setZoneDrawMode(false); s.setNotationMode(false) }
      if ((e.key === 'Delete' || e.key === 'Backspace') && s.selectedId) { s.deleteItem(s.selectedId); e.preventDefault() }
      if ((e.metaKey || e.ctrlKey) && e.key === 'd' && s.selectedId) { s.duplicateItem(s.selectedId); e.preventDefault() }
    }
    const onUp = (e: KeyboardEvent) => { if (e.code === 'Space') spaceDown.current = false }
    window.addEventListener('keydown', onDown)
    window.addEventListener('keyup', onUp)
    return () => { window.removeEventListener('keydown', onDown); window.removeEventListener('keyup', onUp) }
  }, [])

  // ── Wheel zoom with pointer pivot ─────────────────────────────────────
  const handleWheel = useCallback((e: Konva.KonvaEventObject<WheelEvent>) => {
    e.evt.preventDefault()
    const stage = stageRef.current
    if (!stage) return
    const old = stage.scaleX()
    const ptr = stage.getPointerPosition()
    if (!ptr) return
    const factor = e.evt.deltaY < 0 ? 1.12 : 0.9
    const next = Math.min(Math.max(old * factor, 0.1), 6)
    const mx = (ptr.x - stage.x()) / old
    const my = (ptr.y - stage.y()) / old
    stage.scale({ x: next, y: next })
    stage.position({ x: ptr.x - mx * next, y: ptr.y - my * next })
    stage.batchDraw()
  }, [])

  // ── Stage MouseDown ────────────────────────────────────────────────────
  const handleMouseDown = useCallback((e: Konva.KonvaEventObject<MouseEvent>) => {
    const stage = stageRef.current
    if (!stage) return

    // Middle mouse OR Space+left = pan (takes priority over everything)
    if (e.evt.button === 1 || (spaceDown.current && e.evt.button === 0)) {
      e.evt.preventDefault()
      panState.current = { startX: e.evt.clientX, startY: e.evt.clientY, ox: stage.x(), oy: stage.y() }
      return
    }

    if (e.evt.button !== 0) return

    const pos = stagePointerPos(stage)
    if (!pos) return
    const cx = pos.x / STEP
    const cy = pos.y / STEP

    // Placement + notation work on ANY click (including over existing items)
    if (placingType) { e.cancelBubble = true; placeItem(cx, cy); return }
    if (notationMode) { e.cancelBubble = true; addNotation(cx, cy, 'Note', '', false); return }

    // Zone draw + rubber-band only on empty background
    const isBackground = e.target === (e.target as Konva.Node).getStage()
    if (!isBackground) return

    if (zoneDrawMode || e.evt.shiftKey) {
      rubberStart.current = { x: cx, y: cy, isZone: zoneDrawMode }
      return
    }

    clearSelection()
  }, [placingType, notationMode, zoneDrawMode, placeItem, addNotation, clearSelection])

  // ── Stage MouseMove ────────────────────────────────────────────────────
  const handleMouseMove = useCallback((e: Konva.KonvaEventObject<MouseEvent>) => {
    const stage = stageRef.current
    if (!stage) return

    if (panState.current) {
      stage.position({
        x: panState.current.ox + (e.evt.clientX - panState.current.startX),
        y: panState.current.oy + (e.evt.clientY - panState.current.startY),
      })
      stage.batchDraw()
      return
    }

    if (rubberStart.current) {
      const pos = stagePointerPos(stage)
      if (!pos) return
      const cx = pos.x / STEP
      const cy = pos.y / STEP
      setRubber({
        x: Math.min(rubberStart.current.x, cx),
        y: Math.min(rubberStart.current.y, cy),
        w: Math.abs(cx - rubberStart.current.x),
        h: Math.abs(cy - rubberStart.current.y),
      })
    }
  }, [])

  // ── Stage MouseUp ──────────────────────────────────────────────────────
  const handleMouseUp = useCallback((_e: Konva.KonvaEventObject<MouseEvent>) => {
    panState.current = null

    if (rubberStart.current && rubber) {
      const { x, y, w, h, isZone } = { ...rubberStart.current, ...rubber }

      if (isZone && w > 2 && h > 2) {
        addZone({ name: 'Zone', hexColor: '#C76E4B', x, y, width: w, height: h })
      } else if (!isZone && (w > 1 || h > 1)) {
        // Rubber-band select
        const toSelect = items.filter(item => {
          const cfg = getFurnitureConfig(item.type, item.variant)
          if (!cfg) return false
          const hw = cfg.widthMm / 10 / 2
          const hh = cfg.heightMm / 10 / 2
          return item.x + hw > x && item.x - hw < x + w &&
                 item.y + hh > y && item.y - hh < y + h
        })
        if (toSelect.length > 0) {
          useCanvasStore.setState({
            selectedIds: toSelect.map(i => i.id),
            selectedId: toSelect[0].id,
          })
        }
      }
    }

    rubberStart.current = null
    setRubber(null)
  }, [rubber, items, addZone])

  // ── Transformer rotation end ────────────────────────────────────────────
  const handleTransformEnd = useCallback(() => {
    const tr = trRef.current
    if (!tr) return
    tr.nodes().forEach(node => {
      const id = node.id()  // set via <Group id={item.id}>
      if (id) rotateItem(id, node.rotation())
      node.scaleX(1)
      node.scaleY(1)
    })
  }, [rotateItem])

  // ── Cursor style ───────────────────────────────────────────────────────
  const cursor = placingType || zoneDrawMode ? 'crosshair'
    : notationMode ? 'cell'
    : panState.current ? 'grabbing'
    : spaceDown.current ? 'grab'
    : 'default'

  return (
    <div ref={containerRef} className={styles.canvas} style={{ cursor }}>
      {dims.w > 0 && (
        <Stage
          ref={stageRef}
          width={dims.w}
          height={dims.h}
          x={40} y={40}
          onWheel={handleWheel}
          onMouseDown={handleMouseDown}
          onMouseMove={handleMouseMove}
          onMouseUp={handleMouseUp}
        >
          <Layer>
            {/* Room floor — listening:false so clicks pass through to Stage */}
            <Rect
              x={0} y={0}
              width={ROOM_W_UNITS * STEP}
              height={ROOM_H_UNITS * STEP}
              fill="#EFEBE5"
              stroke="rgba(26,24,22,0.18)"
              strokeWidth={1}
              listening={false}
            />

            {/* 1m grid lines (every 100 units = 1000mm) */}
            <GridLines w={ROOM_W_UNITS} h={ROOM_H_UNITS} step={STEP} />

            {/* Zones (behind furniture) */}
            {zones.map(zone => <ZoneRect key={zone.id} zone={zone} />)}

            {/* Furniture */}
            {items.map(item => (
              <FurnitureItem
                key={item.id}
                item={item}
                gridStep={STEP}
                registerNode={registerNode}
              />
            ))}

            {/* Rubber-band selection / zone preview */}
            {rubber && rubber.w > 0.5 && (
              <Rect
                x={rubber.x * STEP} y={rubber.y * STEP}
                width={rubber.w * STEP} height={rubber.h * STEP}
                stroke={zoneDrawMode ? '#C76E4B' : '#3B82F6'}
                strokeWidth={1.5}
                fill={zoneDrawMode ? 'rgba(199,110,75,0.07)' : 'rgba(59,130,246,0.05)'}
                dash={[5, 3]}
                listening={false}
              />
            )}

            {/* Notation pins */}
            {notations.map(n => (
              <NotationPin key={n.id} notation={n} gridStep={STEP} />
            ))}

            {/* Konva Transformer — matches WP builder style */}
            <Transformer
              ref={trRef}
              rotateEnabled={true}
              resizeEnabled={false}
              borderStroke="#C76E4B"
              borderStrokeWidth={1.5}
              anchorSize={8}
              anchorCornerRadius={4}
              anchorFill="#FFFFFF"
              anchorStroke="#C76E4B"
              anchorStrokeWidth={1.5}
              padding={5}
              onTransformEnd={handleTransformEnd}
            />
          </Layer>
        </Stage>
      )}
    </div>
  )
}

// Grid drawn every 100 units (= 1000mm = 1m) with minor lines every 10 units (100mm)
function GridLines({ w, h, step }: { w: number; h: number; step: number }) {
  const lines: ReactElement[] = []
  // Minor lines every 10 units (100mm)
  for (let x = 0; x <= w; x += 10) {
    const isMajor = x % 100 === 0
    lines.push(<Line key={`v${x}`} points={[x*step,0,x*step,h*step]}
      stroke={isMajor ? 'rgba(26,24,22,0.12)' : GRID_COLOR} strokeWidth={isMajor ? 1 : 0.5} />)
  }
  for (let y = 0; y <= h; y += 10) {
    const isMajor = y % 100 === 0
    lines.push(<Line key={`h${y}`} points={[0,y*step,w*step,y*step]}
      stroke={isMajor ? 'rgba(26,24,22,0.12)' : GRID_COLOR} strokeWidth={isMajor ? 1 : 0.5} />)
  }
  return <>{lines}</>
}
