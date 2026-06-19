import { Group, Rect, Circle } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

const TABLE_FILL   = '#D4C9B8'
const TABLE_STROKE = 'rgba(26,24,22,0.25)'
const CHAIR_FILL   = '#E8E0D8'
const CHAIR_R      = 5
const CHAIR_MARGIN = 3

interface Props { item: CanvasItem; pw: number; ph: number }

export function RectTable({ item, pw, ph }: Props) {
  // Chairs: long sides top/bottom, short sides left/right
  const chairsPerLong  = Math.round(ph / 30)
  const chairsPerShort = Math.round(pw / 30)

  const chairs: React.ReactElement[] = []

  // Top row
  for (let i = 0; i < chairsPerLong; i++) {
    const cx = (pw / (chairsPerLong + 1)) * (i + 1)
    chairs.push(<Circle key={`top-${i}`} x={cx} y={-CHAIR_MARGIN - CHAIR_R} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>)
  }
  // Bottom row
  for (let i = 0; i < chairsPerLong; i++) {
    const cx = (pw / (chairsPerLong + 1)) * (i + 1)
    chairs.push(<Circle key={`bot-${i}`} x={cx} y={ph + CHAIR_MARGIN + CHAIR_R} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>)
  }
  // Left column
  for (let i = 0; i < chairsPerShort; i++) {
    const cy = (ph / (chairsPerShort + 1)) * (i + 1)
    chairs.push(<Circle key={`left-${i}`} x={-CHAIR_MARGIN - CHAIR_R} y={cy} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>)
  }
  // Right column
  for (let i = 0; i < chairsPerShort; i++) {
    const cy = (ph / (chairsPerShort + 1)) * (i + 1)
    chairs.push(<Circle key={`right-${i}`} x={pw + CHAIR_MARGIN + CHAIR_R} y={cy} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>)
  }

  return (
    <Group>
      {chairs}
      <Rect
        x={0} y={0}
        width={pw} height={ph}
        fill={TABLE_FILL}
        stroke={TABLE_STROKE}
        strokeWidth={1}
        cornerRadius={2}
      />
    </Group>
  )
}
