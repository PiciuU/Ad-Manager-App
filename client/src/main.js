import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import '@/assets/styles/main.css'

const app = createApp(App)

/* Font Awesome */
import FontAwesome from '@/plugins/font-awesome'
FontAwesome(app)

/* Element Plus */
import ElementPlus from '@/plugins/element-plus'
ElementPlus(app)

/* axios */
import ApiService from '@/services/api.service'
ApiService.init()

/* Chartkick */
import 'chartkick/chart.js'
import VueChartkick from 'vue-chartkick'
app.use(VueChartkick)

app.use(createPinia())

app.use(router).mount('#app')
