'use client'

import Link from 'next/link'
import { usePathname, useRouter } from 'next/navigation'
import type { User } from '@supabase/supabase-js'
import { createClient } from '@/lib/supabase/client'
import styles from './AdminSidebar.module.css'

export function AdminSidebar({ user }: { user: User }) {
  const pathname = usePathname()
  const router = useRouter()

  async function signOut() {
    const supabase = createClient()
    await supabase.auth.signOut()
    router.push('/login')
    router.refresh()
  }

  return (
    <aside className={styles.sidebar}>
      <div className={styles.brand}>
        <div className={styles.brandMark}>T</div>
        <div className={styles.brandText}>
          <span>TEMPO</span>
          <em>Events</em>
        </div>
      </div>

      <nav className={styles.nav}>
        <Link
          href="/layouts"
          className={`${styles.navItem} ${pathname.startsWith('/layouts') ? styles.active : ''}`}
        >
          <LayoutsIcon />
          Event Layouts
        </Link>
      </nav>

      <div className={styles.footer}>
        <div className={styles.userInfo}>
          <div className={styles.userAvatar}>{user.email?.[0].toUpperCase()}</div>
          <span className={styles.userEmail}>{user.email}</span>
        </div>
        <button onClick={signOut} className={styles.signOut}>Sign out</button>
      </div>
    </aside>
  )
}

function LayoutsIcon() {
  return (
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
      <rect x="1" y="1" width="6" height="6" rx="1" stroke="currentColor" strokeWidth="1.5"/>
      <rect x="9" y="1" width="6" height="6" rx="1" stroke="currentColor" strokeWidth="1.5"/>
      <rect x="1" y="9" width="6" height="6" rx="1" stroke="currentColor" strokeWidth="1.5"/>
      <rect x="9" y="9" width="6" height="6" rx="1" stroke="currentColor" strokeWidth="1.5"/>
    </svg>
  )
}
