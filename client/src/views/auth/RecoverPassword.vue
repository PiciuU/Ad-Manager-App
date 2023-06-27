<template>
    <el-card class="auth-container">
        <template #header>
            <h1 class="auth__title">Przypomnienie hasła</h1>
        </template>

        <div v-if="!formState.isFormDone">
            <el-form class="auth__form" ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials">
                <el-form-item prop="email" class="auth__input-group">
                    <el-input v-model="credentials.email" maxlength="254" placeholder="Wprowadź adres email...">
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="envelope" />
                        </template>
                    </el-input>
                </el-form-item>

                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading">Przypomnij hasło</el-button>
            </el-form>

            <div class="auth__options">
                <div class="auth__hint">Pamiętasz swoje hasło? <router-link to="/">Zaloguj się</router-link></div>
            </div>
        </div>

        <div v-else>
            <div class="auth__success">Mail ze zmianą hasła został wysłany</div>

			<router-link to="/">
                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading" plain>Wróć do logowania</el-button>
            </router-link>
        </div>
    </el-card>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const formState = reactive({
		isFormDone: false
	});

    const validationRules = {
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
			}
		],
	};

    const credentialsForm = ref();

	const credentials = reactive({
		email: ''
	});

	const validateCredentials = () => {
		credentialsForm.value.validate((valid) => {
			if (!valid) return;
			submitForm();
		});
	};

    const submitForm = () => {
        authStore.passwordRecover(credentials)
            .then(() => {
                formState.isFormDone = true;
            })
    };
</script>