<template>
    <el-card class="auth-container">
        <template #header>
            <h2 class="auth__subtitle">Witaj ponownie</h2>
            <h1 class="auth__title">Logowanie do panelu statystyk</h1>
        </template>

        <el-form class="auth__form" ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials">
            <el-form-item prop="login" class="auth__input-group">
                <el-input v-model="credentials.login" placeholder="Wprowadź login...">
                    <template #prefix>
                        <font-awesome-icon class="auth__input_icon" icon="user" />
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item prop="password" class="auth__input-group">
                <el-input v-model="credentials.password" placeholder="Wprowadź hasło..." show-password>
                    <template #prefix>
                        <font-awesome-icon class="auth__input_icon" icon="lock" />
                    </template>
                </el-input>
            </el-form-item>

            <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading">Zaloguj</el-button>
        </el-form>

        <div class="auth__options">
            <div class="auth__hint">Nie posiadasz konta? <router-link to="/rejestracja">Aktywuj konto</router-link></div>
            <div class="auth__hint">Nie pamiętasz hasła? <router-link to="/przypomnienie-hasla">Odzyskaj konto</router-link></div>
        </div>
    </el-card>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const validationRules = ({
		login: [
			{
				required: true,
				message: "Nazwa użytkownika jest wymagana",
				trigger: "blur"
			}
		],
		password: [
			{
				required: true,
				message: "Hasło jest wymagane",
				trigger: "blur"
			}
		]
	});

    const credentialsForm = ref();

	const credentials = reactive({
		login: '',
		password: ''
	});

	const validateCredentials = () => {
		credentialsForm.value.validate((valid) => {
			if (!valid) return;
			submitForm();
		});
	};

    const submitForm = () => {
        authStore.login(credentials)
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
    }
</style>