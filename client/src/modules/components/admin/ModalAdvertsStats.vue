<template>
    <el-dialog :model-value="true" :title="`Generator statystyk (ID: ${props.advert.id})`" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="date" label="Data" class="full-width">
                <el-date-picker
                    v-model="formData.date"
                    type="date"
                    value-format="YYYY-MM-DD"
                    placeholder="Wybierz datę dla której mają być wygenerowane statystyki..."
                />
            </el-form-item>
            <el-form-item prop="views" label="Wyświetlenia">
                <el-input v-model.number="formData.views" placeholder="Wprowadź liczbę wyświetleń..."></el-input>
            </el-form-item>
            <el-form-item prop="clicks" label="Kliknięcia">
                <el-input v-model.number="formData.clicks" placeholder="Wprowadź liczbę kliknięć..."></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Wygeneruj statystyki</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';

    import { convertToDateFormat } from '@/common/helpers/date.helper';
    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const props = defineProps({
        advert: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        adId: props.advert.id,
        date: null,
        views: 0,
        clicks: 0,
    })

    const validationRules = {
        date: [
			{
				required: true,
				message: 'Data jest wymagana',
				trigger: 'blur'
			},
			{
				type: 'date',
				message: 'Data musi być datą',
				trigger: 'blur'
			},
		],
        views: [
            {
				required: true,
				message: 'Liczba wyświetleń jest wymagana',
				trigger: 'blur'
			},
		],
        clicks: [
            {
				required: true,
				message: 'Liczba kliknięć jest wymagana',
				trigger: 'blur'
			},
		],
    }

    const closeModal = () => {
        if (!isLoading.value) emit('close');
    }

    const validateData = () => {
        form.value.validate((valid) => {
            if (!valid) return;
            submitForm();
        });
    }

    const submitForm = () => {
        isLoading.value = true;
        adminStore.updateAdvertStats(props.advert.id, formData)
            .then(() => {
                NotificationService.displayMessage('success', 'Pomyślnie wygenerowano statystyki dla reklamy.');
                Object.assign(formData, {
                    adId: props.advert.id,
                    date: null,
                    views: 0,
                    clicks: 0,
                });
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy generowaniu statystyk, spróbuj ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>

<style lang="scss" scoped>
    .full-width, ::v-deep(.el-date-editor), ::v-deep(.el-date-editor > .el-input__wrapper) {
        width: 100%;
    }
</style>