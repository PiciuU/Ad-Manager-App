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
        /* Manage User */
        async fetchUsers() {
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
        async fetchUser(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/users/${id}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async fetchUserAdverts(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/users/${id}/ads`);
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
        /* Manage Notifications */
        async sendNotification(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post(`/admin/notifications`, payload);
                return Promise.resolve();
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        /* Manage Advert */
        async fetchAdverts() {
            try {
                this.loading = true;
                const response = await ApiService.get('/admin/ads');
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async fetchAdvert(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async createAdvert(payload) {
            try {
                this.loading = true;
                const response = await ApiService.post('/admin/ads', payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateAdvert(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/admin/ads/${id}`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async deactivateAdvert(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}/deactivate`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async renewAdvert(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/admin/ads/${id}/renew`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
         /* Manage Invoices */
         async fetchInvoices(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}/invoices`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async payInvoice(id, invoiceId) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}/invoices/${invoiceId}/payment`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async createInvoice(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}/invoices/create`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateInvoice(id, invoiceId, payload) {
            try {
                this.loading = true;
                const response = await ApiService.put(`/admin/ads/${id}/invoices/${invoiceId}`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        /* Manage Logs */
        async fetchLogs(page = 1) {
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
        async fetchUserLogs(id, page = 1) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/logs/users/${id}?page=${page}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async fetchAdvertLogs(id, page = 1) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/logs/ads/${id}?page=${page}`);
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
        /* Manage Files */
        async fetchFiles(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}/files`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async uploadFile(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.post(`/admin/ads/${id}/files`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async highlightFile(id, fileName) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/admin/ads/${id}/files/${fileName}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async deleteFile(id, fileName) {
            try {
                this.loading = true;
                const response = await ApiService.delete(`/admin/ads/${id}/files/${fileName}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        /* Ad Stats */
        async fetchAdvertStats(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.query(`/admin/ads/${id}/stats`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async updateAdvertStats(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.post(`/admin/ads/${id}/stats`, payload);
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