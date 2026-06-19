import { Rect } from 'react-konva'
import type { CanvasItem } from '@/types/layout'

interface Props { item: CanvasItem; pw: number; ph: number }

export function SideTable({ item, pw, ph }: Props) {
  return <Rect x={0} y={0} width={pw} height={ph} fill="#E0D8CC" stroke="rgba(26,24,22,0.15)" strokeWidth={1} cornerRadius={2} />
}
