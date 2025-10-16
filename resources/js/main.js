import { createApp } from 'vue'
import './bootstrap'
import App from './App.vue'
import '../css/app.css'
import { router } from './router'

createApp(App).use(router).mount('#app')