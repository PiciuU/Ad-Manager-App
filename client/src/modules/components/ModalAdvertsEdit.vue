<template>
    <el-dialog :model-value="true" title="Edycja reklamy" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="name" label="Nazwa reklamy">
                <el-input v-model="formData.name" maxlength="255" placeholder="Wprowadź nazwę reklamy..."></el-input>
            </el-form-item>
            <el-form-item prop="url" label="Adres przekierowania">
                <el-input v-model="formData.url" maxlength="255" placeholder="(Opcjonalne) Wprowadź adres na który ma przekierować reklama..."></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Zapisz zmiany</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive } from 'vue'
    import { useAdStore } from '@/stores/AdStore';

    import NotificationService from '@/services/notification.service'

    const adStore = useAdStore();

    const props = defineProps({
        advert: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        name: props.advert.name,
        url: props.advert.url,
    })

    const validationRules = {
        name: [
			{
				required: true,
				message: 'Nazwa reklamy jest wymagana',
				trigger: 'blur'
			},
			{
				max: 255,
				message: "Nazwa reklamy może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			},
		],
        url: [
			{
				max: 255,
				message: "Adres przekierowania może posiadać maksymalnie 255 znaków",
				trigger: "blur"
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
        adStore.updateAdvert(props.advert.id, formData)
            .then((response) => {
                emit('update', response.data);
                emit('close');
                NotificationService.displayMessage('success', 'Pomyślnie zaktualizowano dane reklamy.');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy edycji reklamy, prosimy spróbować ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>