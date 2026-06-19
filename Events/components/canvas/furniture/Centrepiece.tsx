import { Group, Circle, Line } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number }

export function Centrepiece({ item, pw, ph }: Props) {
  const cx = pw / 2
  const cy = ph / 2
  const r  = pw / 2
  // Stylised flower/centrepiece
  return (
    <Group>
      <Circle x={cx} y={cy} radius={r} fill="rgba(221,170,98,0.15)" stroke="rgba(221,170,98,0.6)" strokeWidth={1} />
      <Circle x={cx} y={cy} radius={r * 0.4} fill="rgba(221,170,98,0.4)" />
      {[0, 60, 120, 180, 240, 300].map(deg => {
        const rad = (deg * Math.PI) / 180
        const px = cx + Math.cos(rad) * r * 0.65
        const py = cy + Math.sin(rad) * r * 0.65
        return <Circle key={deg} x={px} y={py} radius={r * 0.22} fill="rgba(221,170,98,0.25)" />
      })}
    </Group>
  )
}
