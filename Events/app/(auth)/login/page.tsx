import { LoginForm } from './LoginForm'
import styles from './login.module.css'

export default function LoginPage() {
  return (
    <div className={styles.page}>
      <div className={styles.card}>
        <div className={styles.brand}>
          <span className={styles.brandMark}>T</span>
          <span className={styles.brandName}>TEMPO<br /><em>Events</em></span>
        </div>
        <h1 className={styles.heading}>Staff sign in</h1>
        <LoginForm />
      </div>
    </div>
  )
}
