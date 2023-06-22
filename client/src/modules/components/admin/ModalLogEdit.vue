<template>
    <el-dialog :model-value="true" :title="`Edycja wpisu z dziennika zdarzeń (ID: ${log.id})`" @close="$emit('close')" :lock-scroll="true" :close-on-click-modal="true">
        <el-form label-position="top" :hide-required-asterisk="true" :model="formData" @submit.prevent="validateData">
            <el-form-item label="Notatka">
                <el-input class="modal__input" v-model="formData.notes" :rows="3" resize="none" type="textarea" placeholder="Wprowadź notatkę..."></el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('close')" :loading="isLoading">Anuluj zmiany</el-button>
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

    const validateData = () => {
        submitForm();
    }

    const submitForm = () => {
        isLoading.value = true;
        adminStore.updateLogs(props.log.id, formData)
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
