import { defineStore } from 'pinia'

import { setLocalStorage, getLocalStorage } from '@/services/storage.service';

export const useDataStore = defineStore('dataStore', {
    state: () => ({
        sidebar: {
            hide: true,
            collapse: getLocalStorage('sidebarCollapse') == 'true',
            lockScroll: false
        }
    }),
    getters: {
        isSidebarHidden: (state) => state.sidebar.hide,
        isSidebarCollapsed: (state) => state.sidebar.collapse,
        isScrollDisabled: (state) => state.sidebar.lockScroll,
        isSidebarCollapsedPreference: () => getLocalStorage('sidebarCollapse')  == 'true'
    },
    actions: {
        collapseSidebar() {
            this.sidebar.hide = true
            this.sidebar.lockScroll = false
            this.sidebar.collapse = !this.sidebar.collapse
        },
        hideSidebar() {
            this.sidebar.collapse = false
            this.sidebar.lockScroll = this.sidebar.hide
            this.sidebar.hide = !this.sidebar.hide
        },
        sidebarVisibilityPreference(value) {
            setLocalStorage('sidebarCollapse', value);
            this.collapseSidebar();
        }
    }
})
