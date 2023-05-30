import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'app',
      component: HomeView
    },
    {
      path: '/app',
      name: 'app',
      component: () => import('../App.vue')
    },
    {
      path: '/theposts',
      name: 'theposts',
      component: () => import('../common/components/theposts.vue')
    }
  ]
})

export default router
