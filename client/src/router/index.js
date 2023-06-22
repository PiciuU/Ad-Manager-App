import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/AuthStore';

export const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/panel',
            meta: { requiresAuth: true },
            component: () => import(/* webpackChunkName: "group-authorized" */ '@/layouts/Authorized.vue'),
            children: [
                {
                    name: 'Home',
                    path: '',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Dashboard.vue')
                },
                {
                    name: 'AdDetails',
                    path: 'szczegoly',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Adverts.vue')
                },
                {
                    name: 'Company',
                    path: 'dane',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Company.vue')
                },
                {
                    name: 'Settings',
                    path: 'ustawienia',
                    component: () => import(/* webpackChunkName: "group-authorized" */ '@/views/Settings.vue')
                },
                {
                    path: 'admin',
                    component: () => import(/* webpackChunkName: "group-authorized-admin" */ '@/layouts/Admin.vue'),
                    children: [
                        {
                            name: 'AdminUsers',
                            path: 'uzytkownicy',
                            component: () => import(/* webpackChunkName: "group-authorized-admin" */ '@/views/admin/Users.vue')
                        },
                        {
                            name: 'AdminLogs',
                            path: 'dziennik',
                            component: () => import(/* webpackChunkName: "group-authorized-admin" */ '@/views/admin/Logs.vue')
                        }
                    ]
                }
            ],
        },
        {
            path: '/',
            meta: { requiresAuth: false },
            component: () => import(/* webpackChunkName: "group-default" */ '@/layouts/Default.vue'),
            children: [
                {
                    name: 'Login',
                    path: '',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/Login.vue')
                },
                {
                    name: 'Register',
                    path: 'rejestracja',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/Register.vue')
                },
                {
                    name: 'RecoverPassword',
                    path: 'przypomnienie-hasla',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/RecoverPassword.vue')
                },
                {
                    name: 'ResetPassword',
                    path: 'przypomnienie-hasla/:hash',
                    component: () => import(/* webpackChunkName: "group-default" */ '@/views/auth/ResetPassword.vue'),
                    beforeEnter: (to, from, next) => {
                        const authStore = useAuthStore();
                        authStore.validatePasswordResetHash(to.params.hash)
                            .then(() => next())
                            .catch(() => next('/'))
                    },
                },
            ]
        },
    ]
})

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth && !authStore.isLogged) next('/'); // User not logged in, redirect to the login page
    else if (!to.meta.requiresAuth && authStore.isLogged) next('/panel'); // User logged in, redirect to the Home view
    else next(); // Redirect to the intended view
});

export default router
