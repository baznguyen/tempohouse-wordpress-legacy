import { Group, Circle } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

const TABLE_FILL   = '#D4C9B8'
const TABLE_STROKE = 'rgba(26,24,22,0.25)'
const CHAIR_FILL   = '#E8E0D8'
const CHAIR_R      = 5

interface Props { item: CanvasItem; pw: number }

export function RoundTable({ item, pw }: Props) {
  const r = pw / 2
  const chairCount = Math.min(item.pax || 4, 8)
  const chairR = CHAIR_R
  const dist = r + 3 + chairR

  const chairs = Array.from({ length: chairCount }, (_, i) => {
    const angle = (i / chairCount) * Math.PI * 2 - Math.PI / 2
    return (
      <Circle
        key={i}
        x={r + Math.cos(angle) * dist}
        y={r + Math.sin(angle) * dist}
        radius={chairR}
        fill={CHAIR_FILL}
        stroke={TABLE_STROKE}
        strokeWidth={0.5}
      />
    )
  })

  return (
    <Group>
      {chairs}
      <Circle x={r} y={r} radius={r} fill={TABLE_FILL} stroke={TABLE_STROKE} strokeWidth={1} />
    </Group>
  )
}
