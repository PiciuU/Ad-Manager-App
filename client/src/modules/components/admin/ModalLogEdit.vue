<template>
    <el-dialog :model-value="true" :title="`Edycja wpisu z dziennika zdarzeń (ID: ${log.id})`" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form label-position="top" :hide-required-asterisk="true" :model="formData" @submit.prevent="validateData">
            <el-alert v-if="log.adId" class="alert" type="info" show-icon :closable="false">
                <p>
                    Wpis ten tyczy się reklamy o identyfikatorze #{{ log.adId }}.
                </p>
                <router-link @click="closeModal" :to="{ name: 'AdminAds', params: { id: log.adId }}">
                    Kliknij tutaj aby ją wyświetlić
                </router-link>
            </el-alert>
            <el-form-item label="Notatka">
                <el-input class="modal__input" v-model="formData.notes" :rows="3" resize="none" type="textarea" placeholder="Wprowadź notatkę..."></el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Zapisz zmiany</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';
    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const props = defineProps({
        log: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const formData = reactive({
        notes: props.log.notes
    })

    const closeModal = () => {
        if (!isLoading.value) emit('close');
    }

    const validateData = () => {
        submitForm();
    }

    const submitForm = () => {
        isLoading.value = true;
        adminStore.updateLogs(props.log.id, formData)
            .then((response) => {
                emit('update', response.data);
                emit('close');
                NotificationService.displayMessage('success', 'Pomyślnie edytowano wpis w dzieniku zdarzeń.');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy edycji wpisu, spróbuj ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>

<style lang="scss" scoped>
    .alert {
        margin: 30px 0px 10px 0px;

        &:first-of-type {
            margin: 0px 0px 10px 0px;
        }

        a {
            color: $--color-primary;
            text-decoration: none;
        }
    }
</style>