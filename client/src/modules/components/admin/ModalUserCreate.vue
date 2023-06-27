<template>
    <el-dialog :model-value="true" title="Tworzenie nowego użytkownika" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">

        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="login" label="Login">
                <el-input v-model="formData.login" maxlength="32" placeholder="Wprowadź login..."></el-input>
            </el-form-item>
            <el-form-item prop="email" label="Adres email">
                <el-input v-model="formData.email" maxlength="255" placeholder="Wprowadź adres email..."></el-input>
            </el-form-item>
            <el-form-item prop="password" label="Hasło">
                <el-input v-model="formData.password" maxlength="255" placeholder="Wprowadź nowe hasło dla użytkownika...">
                    <template #append>
                        <el-button @click="generateSecurePassword(10)">
                            Generuj hasło
                        </el-button>
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item prop="name" label="Nazwa firmy">
                <el-input v-model="formData.name" maxlength="255" placeholder="Wprowadź nazwę firmy..."></el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Utwórz konto</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';

    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        login: '',
        email: '',
        password: '',
        name: '',
    })

    const validationRules = {
        login: [
			{
				required: true,
				message: 'Login jest wymagany',
				trigger: 'blur'
			},
			{
				max: 32,
				message: "Login może posiadać maksymalnie 32 znaki",
				trigger: "blur"
			},
            {
				validator: (rule, value, callback) => {
					if (/^[a-z0-9_]*$/i.test(value)) callback();
					else callback(new Error("Login może zawierać litery bez znaków diaktrycznych, cyfry oraz znak \"_\""));
				},
				trigger: "blur"
			},
		],
        email: [
			{
				required: true,
				message: 'Adres email jest wymagany',
				trigger: 'blur'
			},
			{
				type: 'email',
				message: 'Wprowadź poprawny adres email',
				trigger: 'blur'
			},
			{
				min: 3,
				message: "Adres email musi posiadać przynajmniej trzy znaki",
				trigger: "blur"
			},
			{
				max: 254,
				message: "Adres email może posiadać maksymalnie 254 znaki",
				trigger: "blur"
			},
		],
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
        name: [
			{
				required: true,
				message: 'Nazwa firmy jest wymagana',
				trigger: 'blur'
			},
			{
				max: 255,
				message: "Nazwa firmy może posiadać maksymalnie 255 znaki",
				trigger: "blur"
			},
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
        adminStore.createUser(formData)
            .then((response) => {
                emit('add', response.data);
                emit('close');
                NotificationService.displayMessage('success', 'Pomyślnie utworzono konto użytkownika.');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy tworzeniu użytkownika, spróbuj ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>