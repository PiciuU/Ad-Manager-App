<template>
    <el-card class="auth-container">
        <template #header>
            <h1 class="auth__title">Resetowanie hasła</h1>
        </template>

        <div v-if="!formState.isFormDone">
            <el-form ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials()">
                <el-form-item prop="password" class="auth__input-group">
                    <el-input v-model="credentials.password" maxlength="128" placeholder="Wprowadź nowe hasło..." show-password>
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="passwordConfirmation" class="auth__input-group">
                    <el-input v-model="credentials.passwordConfirmation" maxlength="128" placeholder="Wprowadź ponownie nowe hasło..." show-password>
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
				max: 128,
				message: "Hasło może posiadać maksymalnie 128 znaków",
				trigger: "blur"
			},
		],
		password_confirmation: [
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
		password_confirmation: ''
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

<style lang="scss" scoped>
    .auth-container {
        max-width: 500px;
        width: 100%;
        height: fit-content;
        padding: 20px 10px;
    }

    .auth {

        &__subtitle {
            color: var(--color-text-muted-3);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.4rem;
            text-align: center;
        }

        &__title {
            text-align: center;
            font-weight: bold;
            font-size: 2.2rem;
        }

        &__steps {
            margin-top: 20px;
        }

        &__input-group {
            margin-bottom: 30px;
        }

        &__button {
            width: 100%;
            text-transform: uppercase;
        }

        &__options {
            margin-top: 20px;
        }

        &__hint {
            color: var(--color-text-muted-3);
            margin-bottom: 5px;
            font-size: 1.4rem;

            &:last-of-type {
                margin-bottom: 0px;
            }

            a {
                color: var(--color-text);
                text-decoration: none;
            }
        }

        &__success {
            color: var(--color-success);
            margin-bottom: 40px;
            font-size: 2rem;
            text-align: center;
        }
    }
</style>