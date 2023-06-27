<template>
    <el-card class="auth-container">
        <template #header>
            <h1 class="auth__title">Resetowanie hasła</h1>
        </template>

        <div v-if="!formState.isFormDone">
            <el-form ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials()">
                <el-form-item prop="password" class="auth__input-group">
                    <el-input v-model="credentials.password" maxlength="255" placeholder="Wprowadź nowe hasło..." show-password>
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="passwordConfirmation" class="auth__input-group">
                    <el-input v-model="credentials.passwordConfirmation" maxlength="255" placeholder="Wprowadź ponownie nowe hasło..." show-password>
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading">Zresetuj hasło</el-button>
            </el-form>

            <div class="auth__options">
                <div class="auth__hint">Pamiętasz swoje hasło? <router-link to="/">Zaloguj się</router-link></div>
            </div>
        </div>

        <div v-else>
            <div class="auth__success">Hasło zostało zmienione</div>

			<router-link to="/">
                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading" plain>Przejdź do logowania</el-button>
            </router-link>
        </div>
    </el-card>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useRoute } from 'vue-router';
    import { useAuthStore } from '@/stores/AuthStore';

    const route = useRoute();
    const authStore = useAuthStore();

    const formState = reactive({
		isFormDone: false
	});

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
			},
		],
		passwordConfirmation: [
			{
				validator: (rule, value, callback) => {
					if (value === '') callback(new Error('Wprowadź ponownie hasło'))
					else if (value !== credentials.password) callback(new Error("Podane hasła nie zgadzają się"))
					else callback()
				},
				trigger: "blur"
			},
		]
	};

    const credentialsForm = ref();

	const credentials = reactive({
		hash: route.params.hash,
		password: '',
		passwordConfirmation: ''
	});

	const validateCredentials = () => {
		credentialsForm.value.validate((valid) => {
			if (!valid) return;
			submitForm();
		});
	};

    const submitForm = () => {
        authStore.passwordReset(credentials)
            .then(() => {
                formState.isFormDone = true;
            })
    };
</script>
