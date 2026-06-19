import { Group, Rect, Circle, Arc } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number; isStool?: boolean }

export function Chair({ item, pw, ph, isStool }: Props) {
  if (isStool) {
    return (
      <Group>
        <Circle
          x={pw/2} y={ph/2}
          radius={pw/2}
          fill="#C4B9A8"
          stroke="rgba(26,24,22,0.2)"
          strokeWidth={1}
        />
        <Circle x={pw/2} y={ph/2} radius={pw/4} fill="rgba(26,24,22,0.08)" />
      </Group>
    )
  }

  return (
    <Group>
      {/* Seat */}
      <Rect
        x={0} y={ph * 0.35}
        width={pw} height={ph * 0.65}
        fill="#D4C9B8"
        stroke="rgba(26,24,22,0.2)"
        strokeWidth={1}
        cornerRadius={3}
      />
      {/* Back */}
      <Rect
        x={pw * 0.1} y={0}
        width={pw * 0.8} height={ph * 0.3}
        fill="#C4B9A8"
        stroke="rgba(26,24,22,0.2)"
        strokeWidth={1}
        cornerRadius={3}
      />
    </Group>
  )
}
