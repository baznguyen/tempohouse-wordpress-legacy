import { notFound } from 'next/navigation'
import { createClient } from '@/lib/supabase/server'
import type { LayoutFull } from '@/types/layout'
import { LayoutEditor } from '@/components/canvas/LayoutEditor'

export default async function LayoutEditorPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params
  const supabase = await createClient()

  const [layoutRes, itemsRes, zonesRes, notationsRes] = await Promise.all([
    supabase.from('event_layouts').select('*').eq('id', id).single(),
    supabase.from('event_layout_items').select('*').eq('layout_id', id),
    supabase.from('event_zones').select('*').eq('layout_id', id),
    supabase.from('event_notations').select('*').eq('layout_id', id).order('ref'),
  ])

  if (layoutRes.error || !layoutRes.data) notFound()

  const layout: LayoutFull = {
    ...(layoutRes.data as any),
    items:     (itemsRes.data ?? []).map(dbItemToCanvasItem),
    zones:     (zonesRes.data ?? []).map(dbZoneToZone),
    notations: (notationsRes.data ?? []).map(dbNotationToNotation),
  }

  return <LayoutEditor initialLayout={layout} />
}

function dbItemToCanvasItem(row: any) {
  return {
    id:          row.id,
    type:        row.item_type,
    variant:     row.variant ?? undefined,
    x:           row.x_pos,
    y:           row.y_pos,
    rotation:    row.rotation_deg ?? 0,
    pax:         row.pax ?? 0,
    label:       row.label ?? '',
    zoneId:      row.zone_id ?? undefined,
    joinGroup:   row.join_group ?? undefined,
    notationRef: row.notation_ref ?? undefined,
  }
}

function dbZoneToZone(row: any) {
  return {
    id:       row.id,
    layoutId: row.layout_id,
    name:     row.name ?? '',
    hexColor: row.hex_color,
    x:        row.x_pos,
    y:        row.y_pos,
    width:    row.width,
    height:   row.height,
  }
}

function dbNotationToNotation(row: any) {
  return {
    id:        row.id,
    layoutId:  row.layout_id,
    ref:       row.ref,
    x:         row.x_pos,
    y:         row.y_pos,
    title:     row.title ?? '',
    body:      row.body ?? '',
    staffOnly: row.staff_only ?? false,
  }
}
