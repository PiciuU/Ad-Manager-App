import { defineStore } from 'pinia';

import ApiService from '@/services/api.service';

export const useAdStore = defineStore('adStore', {
    state: () => ({
        adverts: null,
        loading: false,
        pricePerDay: 10
    }),
    getters: {
        getAdverts: (state) => state.adverts,
        isLoading: (state) => state.loading,
        getPricePerDay: (state) => state.pricePerDay,
    },
    actions: {
        /* Adverts */
        async fetchAdverts() {
            try {
                this.loading = true;
                const response = await ApiService.get('/ads');
                this.adverts = response.data;
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
                const response = await ApiService.get(`/ads/${id}`);
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
                const response = await ApiService.post('/ads', payload);
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
                const response = await ApiService.put(`/ads/${id}`, payload);
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
                const response = await ApiService.get(`/ads/${id}/deactivate`);
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
                const response = await ApiService.put(`/ads/${id}/renew`, payload);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        /* Invoices */
        async fetchInvoices(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/ads/${id}/invoices`);
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
                const response = await ApiService.get(`/ads/${id}/invoices/${invoiceId}/payment`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        /* Files */
        async fetchFiles(id) {
            try {
                this.loading = true;
                const response = await ApiService.get(`/ads/${id}/files`);
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
                const response = await ApiService.post(`/ads/${id}/files`, payload);
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
                const response = await ApiService.get(`/ads/${id}/files/${fileName}`);
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
                const response = await ApiService.delete(`/ads/${id}/files/${fileName}`);
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        /* Ad Stats */
        async getSummary() {
            try {
                this.loading = true;
                const response = await ApiService.get('/stats');
                return Promise.resolve(response);
            }
            catch (error) {
                return Promise.reject(error.data);
            } finally {
                this.loading = false;
            }
        },
        async getAdvertStats(id, payload) {
            try {
                this.loading = true;
                const response = await ApiService.query(`/ads/${id}/stats`, payload);
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