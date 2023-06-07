import store from '@/store'
import axios from 'axios'

export function checkAuth(to, next) {
    if (to.matched.some((record) => record.meta.requiresAuth)) {
        if (store.getters.isLogged) next()
        else next('/')
    } else {
        if (store.getters.isLogged) next('/panel')
        else next()
    }
}

export function checkResetPasswordHash(to, from, next) {
    axios
        .post('/auth/validation/hash', { hash: to.params.hash })
        .then(() => {
            next()
        })
        .catch(() => {
            next('/')
        })
}

export default { checkAuth, checkResetPasswordHash }
