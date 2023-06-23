<template>
    <el-dialog :model-value="true" title="Wyślij powiadomienie do użytkownika" @close="$emit('close')" :lock-scroll="true" :close-on-click-modal="true">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="title" label="Tytuł">
                <el-input v-model="formData.title" maxlength="255" placeholder="Wprowadź tytuł powiadomienia..." />
            </el-form-item>
            <el-form-item prop="description" label="Treść">
                <el-input type="textarea" rows="3" resize="none" v-model="formData.description" maxlength="255" placeholder="Wprowadź treść powiadomienia..." />
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('close')" :loading="adminStore.isLoading">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData" :loading="adminStore.isLoading">Wyślij</el-button>
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
        user: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close']);

    const form = ref()

    const formData = reactive({
        userId: props.user.id,
        title: '',
        description: ''
    })

    const validationRules = {
        title: [
        {
				required: true,
				message: "Tytuł powiadomienia jest wymagany",
				trigger: "blur"
			},
			{
				max: 255,
				message: "Tytuł powiadomienia może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			}
        ],
        description: [
        {
				required: true,
				message: "Treść powiadomienia jest wymagana",
				trigger: "blur"
			},
			{
				max: 255,
				message: "Treść powiadomienia może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			}
        ],
    }

    const validateData = () => {
        form.value.validate((valid) => {
            if (!valid) return;
            submitForm();
        });
    }

    const submitForm = () => {
        adminStore.sendNotification(formData)
            .then(() => {
                NotificationService.displaySuccess('Sukces', 'Pomyślnie wysłano powiadomienie do użytkownika.');
                emit('close');
            })
            .catch((e) => {
                console.log(e);
                NotificationService.displayError('Nieoczekiwany błąd', e.message);
            })
    };
</script>