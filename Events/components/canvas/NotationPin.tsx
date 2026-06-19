'use client'

import { Group, Circle, Text } from 'react-konva'
import type { Notation } from '@/types/layout'

interface Props { notation: Notation; gridStep: number }

export function NotationPin({ notation, gridStep }: Props) {
  const x = notation.x * gridStep
  const y = notation.y * gridStep
  const r = 10

  return (
    <Group x={x} y={y}>
      <Circle
        radius={r}
        fill="#1A1816"
        stroke="#C76E4B"
        strokeWidth={1.5}
      />
      <Text
        x={0} y={0}
        text={notation.ref}
        fontSize={9}
        fill="#F7F3EE"
        fontFamily="'Space Grotesk', sans-serif"
        fontStyle="bold"
        align="center"
        offsetX={r * 0.5}
        offsetY={r * 0.45}
        listening={false}
      />
    </Group>
  )
}
