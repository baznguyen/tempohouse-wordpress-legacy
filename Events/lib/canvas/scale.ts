// Canvas scale utilities
// Design scale: 1:50 (default)
// 1 canvas unit = 10mm
// At 1:50, 1mm real = 0.2mm on canvas → we render 1 unit as PX_PER_UNIT px

export const MM_PER_UNIT   = 10
export const DEFAULT_SCALE = 1   // 1.0 = PX_PER_UNIT px per unit

// Convert from mm spec to canvas units (for positioning / sizing)
export function mm(value: number): number {
  return value / MM_PER_UNIT
}

// Snap to nearest 10mm grid (1 unit)
export function snapToGrid(value: number, gridUnits = 1): number {
  return Math.round(value / gridUnits) * gridUnits
}

// Scale label for PDF (e.g. '1:50')
export function scaleLabel(pxPerUnit: number): string {
  const ratio = Math.round(MM_PER_UNIT * 10 / pxPerUnit * 10) / 10
  return `1:${ratio}`
}
