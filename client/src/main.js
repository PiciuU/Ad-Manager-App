import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

/* Styles */
import '@/assets/styles/main.scss'

/* Font Awesome */
import { fontAwesome } from '@/plugins/font-awesome'

/* Element Plus */
import { elementPlus } from '@/plugins/element-plus'

/* Axios */
import ApiService from '@/services/api.service'
ApiService.init()

/* Chartkick */
import VueChartkick from 'vue-chartkick'

const app = createApp(App)

app.use(createPinia()).use(router).use(fontAwesome).use(elementPlus).use(VueChartkick).mount('#app')
