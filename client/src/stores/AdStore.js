import { defineStore } from 'pinia';

export const useAdStore = defineStore('adStore', {
    state: () => ({
        loading: false
    }),
    getters: {
        isLoading: (state) => state.loading,
    },
    actions: {

    }
});