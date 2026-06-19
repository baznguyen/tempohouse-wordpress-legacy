'use client'

import { Group, Rect, Text } from 'react-konva'
import type { Zone } from '@/types/layout'
import { PX_PER_UNIT } from '@/lib/canvas/furniture-config'

interface Props { zone: Zone }

function hexToRgba(hex: string, alpha: number) {
  const r = parseInt(hex.slice(1, 3), 16)
  const g = parseInt(hex.slice(3, 5), 16)
  const b = parseInt(hex.slice(5, 7), 16)
  return `rgba(${r},${g},${b},${alpha})`
}

export function ZoneRect({ zone }: Props) {
  const step = PX_PER_UNIT
  return (
    <Group x={zone.x * step} y={zone.y * step}>
      <Rect
        width={zone.width * step}
        height={zone.height * step}
        fill={hexToRgba(zone.hexColor, 0.12)}
        stroke={zone.hexColor}
        strokeWidth={1.5}
        dash={[8, 4]}
        cornerRadius={4}
        listening={false}
      />
      {zone.name && (
        <Text
          x={6} y={5}
          text={zone.name.toUpperCase()}
          fontSize={10}
          fill={zone.hexColor}
          fontFamily="'Space Grotesk', sans-serif"
          fontStyle="bold"
          letterSpacing={0.8}
          listening={false}
        />
      )}
    </Group>
  )
}
