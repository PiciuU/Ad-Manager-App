<template>
    <el-container class="el-container-outer is-vertical" v-if="authStore.isAuthenticated">
        <TheHeader />
        <el-container class="el-container-inner">
            <TheSidebar />
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
        </el-container>
    </el-container>
</template>

<script setup>
    import { onBeforeMount } from 'vue'
    import 'chartkick/chart.js'
    import TheHeader from '@/common/components/TheHeader.vue'
    import TheSidebar from '@/common/components/TheSidebar.vue'
    import LoaderService from '@/services/loader.service';
    import { useAuthStore } from '@/stores/AuthStore'

    const authStore = useAuthStore();

    onBeforeMount(async () => {
        if (!authStore.isAuthenticated) {
            LoaderService.create();
            await authStore.fetchUser();
            LoaderService.destroy();
        }
    });
</script>

<style lang="scss" scoped>
    :deep() { @import '@/assets/styles/cards.scss'; };
</style>
