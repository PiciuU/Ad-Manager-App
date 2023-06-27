<template>
    <el-dialog :model-value="true" title="Zmiana hasła użytkownika" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="password" label="Hasło">
                <el-input v-model="formData.password" maxlength="255" placeholder="Wprowadź nowe hasło dla użytkownika...">
                    <template #append>
                        <el-button @click="generateSecurePassword(10)">
                            Generuj hasło
                        </el-button>
                    </template>
                </el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Zmień hasło</el-button>
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

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        banReason: ''
    })

    const validationRules = {
        password: [
        {
				required: true,
				message: "Hasło jest wymagane",
				trigger: "blur"
			},
			{
				min: 6,
				message: "Hasło musi posiadać przynajmniej 6 znaków",
				trigger: "blur"
			},
			{
				max: 255,
				message: "Hasło może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			}
        ],
    }

    const generateSecurePassword = (length) => {
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+-={}:;"<>,.?/';
        let password = '';

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }

        formData.password = password;
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
        adminStore.changePassword({ id: props.user.id, password: formData.password })
            .then(() => {
                NotificationService.displayMessage('success', 'Pomyślnie ustawiono nowe hasło dla użytkownika.');
                emit('close');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy ustawianiu nowego hasła dla użytkownika, spróbuj ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>