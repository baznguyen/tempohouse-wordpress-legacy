import { Group, Rect, Line } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number }

export function Projector({ item, pw, ph }: Props) {
  return (
    <Group>
      <Rect x={0} y={0} width={pw} height={ph} fill="#C8C0B4" stroke="rgba(26,24,22,0.3)" strokeWidth={1} cornerRadius={3} />
      {/* Lens */}
      <Rect x={pw*0.6} y={ph*0.2} width={pw*0.3} height={ph*0.6} fill="rgba(26,24,22,0.15)" cornerRadius={2} />
      {/* Projection beam lines */}
      <Line points={[pw*0.85, ph*0.3, pw*1.2, 0]} stroke="rgba(26,24,22,0.08)" strokeWidth={1} />
      <Line points={[pw*0.85, ph*0.7, pw*1.2, ph]} stroke="rgba(26,24,22,0.08)" strokeWidth={1} />
    </Group>
  )
}
