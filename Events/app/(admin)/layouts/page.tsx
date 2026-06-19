import Link from 'next/link'
import { createClient } from '@/lib/supabase/server'
import type { EventLayout } from '@/types/layout'
import styles from './layouts.module.css'

export default async function LayoutsPage() {
  const supabase = await createClient()
  const { data: layouts } = await supabase
    .from('event_layouts')
    .select('*')
    .order('event_date', { ascending: true, nullsFirst: false })
    .order('created_at', { ascending: false })

  return (
    <div className={styles.page}>
      <header className={styles.header}>
        <div>
          <h1 className={styles.title}>Event Layouts</h1>
          <p className={styles.subtitle}>Design and manage floor plans for private events</p>
        </div>
        <Link href="/layouts/new" className={styles.newBtn}>
          <span>+</span> New Layout
        </Link>
      </header>

      {(!layouts || layouts.length === 0) ? (
        <div className={styles.empty}>
          <div className={styles.emptyIcon}>⬜</div>
          <p>No layouts yet. Create one to get started.</p>
          <Link href="/layouts/new" className={styles.newBtn}>
            + New Layout
          </Link>
        </div>
      ) : (
        <div className={styles.grid}>
          {(layouts as EventLayout[]).map(layout => (
            <Link key={layout.id} href={`/layouts/${layout.id}`} className={styles.card}>
              <div className={styles.cardThumb}>
                <svg viewBox="0 0 80 60" xmlns="http://www.w3.org/2000/svg">
                  <rect width="80" height="60" fill="#E8E0D8"/>
                  <rect x="10" y="10" width="24" height="16" rx="2" fill="#C76E4B" opacity="0.4"/>
                  <rect x="46" y="10" width="24" height="16" rx="2" fill="#C76E4B" opacity="0.4"/>
                  <rect x="10" y="34" width="60" height="16" rx="2" fill="#DDAA62" opacity="0.3"/>
                </svg>
              </div>
              <div className={styles.cardBody}>
                <span className={styles.cardName}>{layout.name}</span>
                <div className={styles.cardMeta}>
                  {layout.eventType && (
                    <span className={styles.tag}>{EVENT_TYPE_LABELS[layout.eventType] ?? layout.eventType}</span>
                  )}
                  {layout.eventDate && (
                    <span className={styles.date}>{formatDate(layout.eventDate)}</span>
                  )}
                  {layout.capacity > 0 && (
                    <span className={styles.capacity}>{layout.capacity} pax</span>
                  )}
                </div>
                {layout.shareEnabled && (
                  <span className={styles.shared}>🔗 Shared</span>
                )}
              </div>
            </Link>
          ))}
        </div>
      )}
    </div>
  )
}

const EVENT_TYPE_LABELS: Record<string, string> = {
  cocktail:     'Cocktail',
  seated_dinner:'Seated Dinner',
  theatre:      'Theatre',
  gallery:      'Gallery',
  boardroom:    'Boardroom',
  custom:       'Custom',
}

function formatDate(d: string) {
  return new Date(d).toLocaleDateString('en-AU', { day: 'numeric', month: 'short', year: 'numeric' })
}
