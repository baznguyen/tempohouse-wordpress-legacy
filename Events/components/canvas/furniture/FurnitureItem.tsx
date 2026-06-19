'use client'

import { useEffect, useRef, type ReactElement } from 'react'
import { Group, Rect, Text } from 'react-konva'
import type Konva from 'konva'
import type { CanvasItem } from '@/types/layout'
import { useCanvasStore } from '@/store/canvas-store'
import { getFurnitureConfig, PX_PER_UNIT } from '@/lib/canvas/furniture-config'
import { RectTable } from './RectTable'
import { RoundTable } from './RoundTable'
import { SquareTable } from './SquareTable'
import { Chair } from './Chair'
import { Lounge } from './Lounge'
import { CoffeeTable } from './CoffeeTable'
import { Backdrop } from './Backdrop'
import { Projector } from './Projector'
import { Centrepiece } from './Centrepiece'
import { SideTable } from './SideTable'
import { TextLabel } from './TextLabel'

const STEP = PX_PER_UNIT

interface Props {
  item: CanvasItem
  gridStep: number
  registerNode?: (id: string, node: Konva.Group | null) => void
}

export function FurnitureItem({ item, gridStep, registerNode }: Props) {
  const groupRef = useRef<Konva.Group | null>(null)
  const { selectedIds, select, moveItem } = useCanvasStore()
  const isSelected = selectedIds.includes(item.id)
  const cfg = getFurnitureConfig(item.type, item.variant)

  // Register this node's ref so the parent Transformer can attach to it
  useEffect(() => {
    if (!registerNode) return
    registerNode(item.id, groupRef.current)
    return () => registerNode(item.id, null)
  }, [item.id, registerNode])

  if (!cfg) return null

  const px = item.x * gridStep
  const py = item.y * gridStep
  const pw = (cfg.widthMm / 10) * STEP
  const ph = (cfg.heightMm / 10) * STEP
  const isJoined = !!item.joinGroup

  function handleClick(e: Konva.KonvaEventObject<MouseEvent | TouchEvent>) {
    e.cancelBubble = true
    if ((e.evt as MouseEvent).metaKey || (e.evt as MouseEvent).ctrlKey) {
      useCanvasStore.getState().multiSelect(item.id)
    } else {
      select(item.id)
    }
  }

  function handleDragEnd(e: Konva.KonvaEventObject<DragEvent>) {
    const nx = e.target.x() / gridStep
    const ny = e.target.y() / gridStep
    moveItem(item.id, nx, ny)
  }

  const renderers: Record<string, ReactElement> = {
    'rect-table':    <RectTable item={item} pw={pw} ph={ph} />,
    'round-table':   <RoundTable item={item} pw={pw} />,
    'square-table':  <SquareTable item={item} pw={pw} ph={ph} />,
    'bar-stool':     <Chair item={item} pw={pw} ph={ph} isStool />,
    'theatre-chair': <Chair item={item} pw={pw} ph={ph} />,
    'lounge':        <Lounge item={item} pw={pw} ph={ph} />,
    'coffee-square': <CoffeeTable item={item} pw={pw} ph={ph} shape="square" />,
    'coffee-round':  <CoffeeTable item={item} pw={pw} ph={ph} shape="round" />,
    'coffee-rect':   <CoffeeTable item={item} pw={pw} ph={ph} shape="rect" />,
    'side-table':    <SideTable item={item} pw={pw} ph={ph} />,
    'centrepiece':   <Centrepiece item={item} pw={pw} ph={ph} />,
    'backdrop':      <Backdrop item={item} pw={pw} ph={ph} />,
    'projector':     <Projector item={item} pw={pw} ph={ph} />,
    'text-label':    <TextLabel item={item} />,
  }

  return (
    <Group
      ref={groupRef}
      id={item.id}
      x={px}
      y={py}
      rotation={item.rotation}
      offsetX={pw / 2}
      offsetY={ph / 2}
      draggable
      onClick={handleClick}
      onTap={(e) => handleClick(e as Konva.KonvaEventObject<MouseEvent | TouchEvent>)}
      onDragEnd={handleDragEnd}
    >
      {renderers[item.type] ?? null}

      {/* Selection ring — 2px fixed in stage-local space (scales with zoom, which is fine) */}
      {isSelected && (
        <Rect
          x={-2}
          y={-2}
          width={pw + 4}
          height={ph + 4}
          stroke="#C76E4B"
          strokeWidth={1.5}
          fill="transparent"
          listening={false}
        />
      )}

      {/* Join group dashed outline */}
      {isJoined && !isSelected && (
        <Rect
          x={-4}
          y={-4}
          width={pw + 8}
          height={ph + 8}
          stroke="rgba(120,130,150,0.6)"
          strokeWidth={1}
          dash={[6, 4]}
          fill="transparent"
          listening={false}
        />
      )}

      {/* Pax label when selected */}
      {cfg.hasSeating && item.pax > 0 && isSelected && (
        <Text
          x={pw / 2}
          y={-18}
          text={`${item.pax} pax`}
          fontSize={10}
          fill="#C76E4B"
          fontFamily="'Space Grotesk', sans-serif"
          align="center"
          offsetX={20}
          listening={false}
        />
      )}

      {/* Custom label */}
      {item.label && (
        <Text
          x={pw / 2}
          y={ph / 2}
          text={item.label}
          fontSize={9}
          fill="rgba(26,24,22,0.6)"
          fontFamily="'Space Grotesk', sans-serif"
          align="center"
          offsetX={40}
          offsetY={5}
          listening={false}
        />
      )}
    </Group>
  )
}
