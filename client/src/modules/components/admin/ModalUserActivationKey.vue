<template>
    <el-dialog :model-value="true" title="Generator klucza aktywacyjnego" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <div v-if="!activationKey">Generowanie klucza aktywacyjnego...</div>
        <div v-else>Wygenerowany klucz aktywacyjny: {{ activationKey }}</div>

        <template #footer>
            <span class="dialog-footer">
                <el-button type="primary" @click="handleAction" :loading="isLoading" v-if="activationKey">Przypisz klucz do użytkownika</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';
    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const props = defineProps({
        user: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    onMounted(() => {
        isLoading.value = true;
        adminStore.generateActivationKey()
            .then((response) => {
                activationKey.value = response.data;
            })
            .catch(() => {
                NotificationService.displayError('Nieoczekiwany błąd', 'Nie udało się wygenerować klucza aktywacyjnego, spróbuj ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    });

    const isLoading = ref(false);
    const activationKey = ref('');

    const closeModal = () => {
        if (!isLoading.value) emit('close');
    }

    const handleAction = () => {
        if (!activationKey.value) return;
        isLoading.value = true;
        adminStore.assignActivationKey({ id: props.user.id, activationKey: activationKey.value })
            .then((response) => {
                emit('update', response.data);
                emit('close');
            })
            .catch((e) => {
                console.log(e);
                NotificationService.displayError('Nieoczekiwany błąd', e.message);
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>
