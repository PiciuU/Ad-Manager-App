<template>
    <router-view v-slot="{ Component }" v-if="authStore.isAdmin">
        <keep-alive>
            <component :is="Component" />
        </keep-alive>
    </router-view>
</template>

<script setup>
    import { onBeforeMount } from 'vue';
    import { useRouter } from 'vue-router';
    import { useAuthStore } from '@/stores/AuthStore'

    const authStore = useAuthStore();
    const router = useRouter();

    onBeforeMount(() => {
        if (!authStore.isAdmin) router.push({ name: 'Login' });
    });
</script>