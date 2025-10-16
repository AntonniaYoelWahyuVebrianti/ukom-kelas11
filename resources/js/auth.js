import { createApp } from 'vue'
import './bootstrap'
import AuthPortal from './views/AuthPortal.vue'
import '../css/app.css'

const el = document.getElementById('auth-app')

if (el) {
  const initialMode = el.dataset.initialMode === 'register' ? 'register' : 'login'
  createApp(AuthPortal, { initialMode }).mount(el)
}
