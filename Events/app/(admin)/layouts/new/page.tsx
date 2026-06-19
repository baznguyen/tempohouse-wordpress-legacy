import { redirect } from 'next/navigation'
import { createClient } from '@/lib/supabase/server'

export default async function NewLayoutPage() {
  const supabase = await createClient()
  const { data: { user } } = await supabase.auth.getUser()

  const { data, error } = await supabase
    .from('event_layouts')
    .insert({
      name: 'Untitled Layout',
      share_enabled: false,
      capacity: 0,
      created_by: user?.id,
    })
    .select('id')
    .single()

  if (error || !data) {
    redirect('/layouts')
  }

  redirect(`/layouts/${data.id}`)
}
