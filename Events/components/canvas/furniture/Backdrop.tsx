import { Group, Rect, Text, Line } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number }

export function Backdrop({ item, pw, ph }: Props) {
  return (
    <Group>
      <Rect x={0} y={0} width={pw} height={ph} fill="#D8D0C4" stroke="rgba(26,24,22,0.3)" strokeWidth={1} />
      {/* Diagonal stripes to indicate media surface */}
      <Line points={[0, ph*0.33, pw, ph*0.33]} stroke="rgba(26,24,22,0.1)" strokeWidth={1} />
      <Line points={[0, ph*0.66, pw, ph*0.66]} stroke="rgba(26,24,22,0.1)" strokeWidth={1} />
      <Text
        x={pw/2} y={ph/2}
        text="BACKDROP"
        fontSize={Math.max(6, pw * 0.12)}
        fill="rgba(26,24,22,0.35)"
        fontFamily="'Space Grotesk', sans-serif"
        fontStyle="bold"
        align="center"
        offsetX={pw * 0.35}
        offsetY={6}
        rotation={90}
        listening={false}
      />
    </Group>
  )
}
