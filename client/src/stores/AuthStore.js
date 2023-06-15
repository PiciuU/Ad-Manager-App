import { defineStore } from 'pinia';
import { router } from '@/router/index';

import ApiService from '@/services/api.service';
import NotificationService from '@/services/notification.service';
import { setCookie, getCookie, deleteCookie } from '@/services/storage.service';

export const useAuthStore = defineStore('authStore', {
    state: () => ({
        token: getCookie('token') || null,
        user: {},
        loading: false,
    }),
    getters: {
        isLoading: (state) => state.loading,
        isLogged: (state) => !!state.token,
        isAuthenticated: (state) => !!state.token && Object.keys(state.user).length != 0 && state.user.constructor === Object,
    },
    actions: {
        async register(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/register', credentials);
                this.setAuthorization(response.data.token, response.data.user, false);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async validateActivationKey(activationKey) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/validation/key', { activationKey });
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async validateActivationLogin(activationKey, login) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/validation/login', { activationKey, login });
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async validateActivationEmail(activationKey, email) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/validation/email', { activationKey, email });
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async login(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/login', credentials);
                this.setAuthorization(response.data.token, response.data.user);
            }
            catch (error) {
                NotificationService.displayError('Nie udało się zalogować', 'Wprowadzono błędny login lub hasło.');
            } finally {
                this.loading = false;
            }
        },
        async passwordRecover(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/recover', credentials);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async validatePasswordResetHash(hash) {
            try {
                this.loading = true;
                const response = await ApiService.get('/auth/recover', hash);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async passwordReset(credentials) {
            try {
                this.loading = true;
                const response = await ApiService.post('/auth/reset', credentials);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async fetchUser() {
            try {
                this.loading = true;
                const response = await ApiService.get('auth/user');
                this.user = response.data;
            } catch (error) {
               this.clearAuthorization();
            } finally {
                this.loading = false;
            }

        },
        setAuthorization(token, user, redirect = true) {
            this.user = user;
            this.token = token;
            setCookie('token', token);
            if (redirect) router.push({ name: 'Home' });
        },
        clearAuthorization() {
            this.user = {};
            this.token = null;
            deleteCookie('token');
            router.push({ name: 'Login' });
        },
        logout() {
            router.push({ name: 'Login' });
        }
    }
});