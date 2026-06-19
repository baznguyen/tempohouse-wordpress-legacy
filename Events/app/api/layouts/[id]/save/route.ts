import { NextResponse } from 'next/server'
import { createClient } from '@/lib/supabase/server'
import type { CanvasItem, Zone, Notation } from '@/types/layout'

export async function POST(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params
  const supabase = await createClient()

  const { data: { user } } = await supabase.auth.getUser()
  if (!user) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 })

  const body = await request.json() as {
    items: CanvasItem[]
    zones: Zone[]
    notations: Notation[]
  }

  // Calculate total capacity
  const capacity = body.items.reduce((sum, item) => sum + (item.pax ?? 0), 0)

  // Upsert in a transaction — delete all children then re-insert
  const { error: layoutErr } = await supabase
    .from('event_layouts')
    .update({ capacity, updated_at: new Date().toISOString() })
    .eq('id', id)

  if (layoutErr) return NextResponse.json({ error: layoutErr.message }, { status: 500 })

  // Delete existing children
  await Promise.all([
    supabase.from('event_layout_items').delete().eq('layout_id', id),
    supabase.from('event_zones').delete().eq('layout_id', id),
    supabase.from('event_notations').delete().eq('layout_id', id),
  ])

  // Re-insert items
  if (body.items.length > 0) {
    const { error } = await supabase.from('event_layout_items').insert(
      body.items.map(item => ({
        id:          item.id,
        layout_id:   id,
        item_type:   item.type,
        variant:     item.variant ?? null,
        x_pos:       item.x,
        y_pos:       item.y,
        rotation_deg: item.rotation,
        pax:         item.pax,
        label:       item.label ?? null,
        zone_id:     item.zoneId ?? null,
        join_group:  item.joinGroup ?? null,
        notation_ref: item.notationRef ?? null,
      }))
    )
    if (error) return NextResponse.json({ error: error.message }, { status: 500 })
  }

  // Re-insert zones
  if (body.zones.length > 0) {
    const { error } = await supabase.from('event_zones').insert(
      body.zones.map(z => ({
        id:        z.id,
        layout_id: id,
        name:      z.name,
        hex_color: z.hexColor,
        x_pos:     z.x,
        y_pos:     z.y,
        width:     z.width,
        height:    z.height,
      }))
    )
    if (error) return NextResponse.json({ error: error.message }, { status: 500 })
  }

  // Re-insert notations
  if (body.notations.length > 0) {
    const { error } = await supabase.from('event_notations').insert(
      body.notations.map(n => ({
        id:         n.id,
        layout_id:  id,
        ref:        n.ref,
        x_pos:      n.x,
        y_pos:      n.y,
        title:      n.title,
        body:       n.body,
        staff_only: n.staffOnly,
      }))
    )
    if (error) return NextResponse.json({ error: error.message }, { status: 500 })
  }

  return NextResponse.json({ ok: true, capacity })
}
