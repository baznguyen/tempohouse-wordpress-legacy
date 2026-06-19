import { Rect, Circle } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number; shape: 'square' | 'round' | 'rect' }

export function CoffeeTable({ item, pw, ph, shape }: Props) {
  const fill   = '#E0D8CC'
  const stroke = 'rgba(26,24,22,0.15)'
  if (shape === 'round') {
    return <Circle x={pw/2} y={ph/2} radius={pw/2} fill={fill} stroke={stroke} strokeWidth={1} />
  }
  return <Rect x={0} y={0} width={pw} height={ph} fill={fill} stroke={stroke} strokeWidth={1} cornerRadius={2} />
}
