import { defineStore } from 'pinia';

import ApiService from '@/services/api.service';

export const useAdminStore = defineStore('adminStore', {
    state: () => ({
        loading: false
    }),
    getters: {
        isLoading: (state) => state.loading,
    },
    actions: {
        async getUsers() {
            try {
                this.loading = true;
                const response = await ApiService.get('/admin/users');
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async generateActivationKey() {
            try {
                this.loading = true;
                const response = await ApiService.get('/admin/user/activationKey');
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async assignActivationKey(payload) {
            try {
                this.loading = true;
                const response = await ApiService.put('/admin/user/activationKey', payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async banUser(payload) {
            try {
                this.loading = true;
                const response = await ApiService.put('/admin/user/ban', payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async changePassword(payload) {
            try {
                this.loading = true;
                const response = await ApiService.put('/admin/user/password', payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async changeUserData(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/admin/users/${id}`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async createUser(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/admin/users', payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async getLogs(page = 1) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/logs?page=${page}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async getUserLogs(id, page = 1) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/logs/${id}?page=${page}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateLogs(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/admin/logs/${id}`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
    }
});