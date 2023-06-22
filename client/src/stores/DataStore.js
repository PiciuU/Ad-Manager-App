import { defineStore } from 'pinia'

export const useDataStore = defineStore('dataStore', {
    state: () => ({
        sidebar: {
            hide: false,
            collapse: false,
            lockScroll: false
        }
    }),
    getters: {
        isSidebarHidden: (state) => state.sidebar.hide,
        isSidebarCollapsed: (state) => state.sidebar.collapse,
        isScrollDisabled: (state) => state.sidebar.lockScroll
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
        }
    }
})
