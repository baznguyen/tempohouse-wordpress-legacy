import { Group, Rect, Circle } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

const TABLE_FILL   = '#D4C9B8'
const TABLE_STROKE = 'rgba(26,24,22,0.25)'
const CHAIR_FILL   = '#E8E0D8'
const CHAIR_R      = 5
const CHAIR_MARGIN = 3

interface Props { item: CanvasItem; pw: number; ph: number }

export function SquareTable({ item, pw, ph }: Props) {
  // 1 chair per side
  const chairs = [
    <Circle key="top"    x={pw/2}      y={-CHAIR_MARGIN-CHAIR_R} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>,
    <Circle key="bottom" x={pw/2}      y={ph+CHAIR_MARGIN+CHAIR_R} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>,
    <Circle key="left"   x={-CHAIR_MARGIN-CHAIR_R} y={ph/2} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>,
    <Circle key="right"  x={pw+CHAIR_MARGIN+CHAIR_R} y={ph/2} radius={CHAIR_R} fill={CHAIR_FILL} stroke={TABLE_STROKE} strokeWidth={0.5}/>,
  ]
  return (
    <Group>
      {chairs}
      <Rect x={0} y={0} width={pw} height={ph} fill={TABLE_FILL} stroke={TABLE_STROKE} strokeWidth={1} cornerRadius={2} />
    </Group>
  )
}
