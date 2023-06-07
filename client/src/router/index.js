import { createRouter, createWebHistory } from 'vue-router'

const routes = [
    {
        path: '/panel',
        component: () => import('@/layouts/AuthorizedComponent.vue'),
        children: [
            {
                name: 'home',
                path: '',
                component: () => import('@/views/Dashboard.vue')
            },
            {
                name: 'ad-details',
                path: 'szczegoly',
                component: () => import('@/views/Adverts.vue')
            },
            {
                name: 'company-details',
                path: 'dane',
                component: () => import('@/views/Company.vue')
            },
            {
                name: 'settings',
                path: 'ustawienia',
                component: () => import('@/views/Settings.vue')
            }
        ]
    },

    {
        path: '/default',
        name: 'default',
        component: () => import('@/layouts/Default.vue'),
        children: [
            {
                name: 'login',
                path: 'logowanie',
                component: () => import('@/views/Login.vue')
            },
            {
                name: 'register',
                path: 'rejestracja',
                component: () => import('@/views/Register.vue')
            },
            {
                name: 'recover-password',
                path: 'przypomnienie-hasła',
                component: () => import('@/views/PasswordReset.vue')
                // beforeEnter: checkResetPasswordHash
            }
        ]
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// router.beforeEach((to, from, next) => {
// 	checkAuth(to, next);
// });

export function routerPush(name, params = null) {
    if (params === null) return router.push({ name })
    else return router.push({ name, params })
}

export default router
