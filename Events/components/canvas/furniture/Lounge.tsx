import { Group, Rect } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number }

export function Lounge({ item, pw, ph }: Props) {
  return (
    <Group>
      {/* Back */}
      <Rect x={0} y={0} width={pw} height={ph * 0.3} fill="#C4B9A8" stroke="rgba(26,24,22,0.2)" strokeWidth={1} cornerRadius={4} />
      {/* Seat */}
      <Rect x={0} y={ph * 0.3} width={pw} height={ph * 0.7} fill="#D4C9B8" stroke="rgba(26,24,22,0.2)" strokeWidth={1} cornerRadius={[0,0,4,4]} />
      {/* Arms */}
      <Rect x={0} y={0} width={ph*0.25} height={ph} fill="#BDB2A0" stroke="rgba(26,24,22,0.2)" strokeWidth={1} cornerRadius={4} />
      <Rect x={pw - ph*0.25} y={0} width={ph*0.25} height={ph} fill="#BDB2A0" stroke="rgba(26,24,22,0.2)" strokeWidth={1} cornerRadius={4} />
    </Group>
  )
}
