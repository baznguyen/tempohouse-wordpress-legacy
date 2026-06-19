import { Text } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem }

export function TextLabel({ item }: Props) {
  return (
    <Text
      x={0} y={0}
      text={item.label || 'Label'}
      fontSize={14}
      fill="rgba(26,24,22,0.7)"
      fontFamily="'Cormorant Garamond', serif"
      fontStyle="italic"
    />
  )
}
