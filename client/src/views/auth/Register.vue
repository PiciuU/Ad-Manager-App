<template>
    <el-card class="auth-container">
        <template #header>
            <h1 class="auth__title">Aktywacja konta</h1>

            <div class="auth__steps" v-if="!formState.isFormDone">
                <span v-if="formState.step === 1">Krok 1: wprowadź otrzymany klucz aktywacyjny.</span>
				<span v-else-if="formState.step === 2">Krok 2: wypełnij podstawowe dane konta.</span>
				<span v-else-if="formState.step === 3">Krok 3: ustaw oraz zatwierdź hasło dla konta.</span>
            </div>
        </template>

        <div v-if="!formState.isFormDone">
            <!-- Check integrity of access key -->
            <el-form v-if="formState.step === 1" ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials()">
                <el-form-item prop="activationKey" class="auth__input-group">
                    <el-input v-model="credentials.activationKey" maxlength="32" placeholder="Wprowadź klucz aktywacyjny...">
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="key" />
                        </template>
                    </el-input>
                </el-form-item>

                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading">Kontynuuj</el-button>
            </el-form>

            <!-- Check login and email -->
            <el-form v-else-if="formState.step === 2" ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials()">
                <el-form-item prop="login" class="auth__input-group">
                    <el-input v-model="credentials.login" maxlength="32" placeholder="Wprowadź login...">
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="user" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="email" class="auth__input-group">
                    <el-input v-model="credentials.email" maxlength="255" placeholder="Wprowadź adres email...">
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="envelope" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading">Kontynuuj</el-button>
            </el-form>

            <!-- Check passwords -->
            <el-form v-else-if="formState.step === 3" ref="credentialsForm" :model="credentials" :rules="validationRules" @submit.prevent="validateCredentials()">
                <el-form-item prop="password" class="auth__input-group">
                    <el-input v-model="credentials.password" maxlength="255" placeholder="Wprowadź hasło..." show-password>
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-form-item prop="passwordConfirmation" class="auth__input-group">
                    <el-input v-model="credentials.passwordConfirmation" maxlength="255" placeholder="Wprowadź ponownie hasło..." show-password>
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>
                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading">Aktywuj</el-button>
            </el-form>

            <div class="auth__options">
                <div class="auth__hint">Posiadasz konto? <router-link to="/">Zaloguj się</router-link></div>
            </div>
        </div>

        <div v-else>
            <div class="auth__success">Konto zostało pomyślnie aktywowane</div>

			<router-link to="/panel">
                <el-button class="auth__button" type="primary" native-type="submit" :loading="authStore.isLoading" plain>Przejdź do panelu</el-button>
            </router-link>
        </div>
    </el-card>
</template>

<script setup>
    import { ref, reactive } from 'vue';
    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const formState = reactive({
		step: 1,
		isFormValidating: false,
		isFormDone: false
	});

    const validationRules = {
		activationKey: [
			{
				required: true,
				message: "Klucz aktywacyjny jest wymagany aby przejść dalej.",
				trigger: "blur"
			},
			{
				len: 32,
				message: "Klucz aktywacyjny musi mieć długość dokładnie 32 znaków.",
				trigger: "blur"
			},
			{
				asyncValidator: (rule, value) => {
					if (!formState.isFormValidating) return;
					return new Promise((resolve, reject) => {
                        authStore.validateActivationKey(value)
							.then((response) => {
								Object.assign(credentials, response.data);
								resolve();
							})
							.catch(() => {
								reject('Wprowadzony klucz aktywacyjny nie istnieje.');
							});
					});
				},
				trigger: "blur"
			}
		],
		login: [
			{
				required: true,
				message: "Nazwa użytkownika jest wymagana",
				trigger: "blur"
			},
			{
				min: 2,
				message: "Nazwa użytkownika musi posiadać przynajmniej dwa znaki",
				trigger: "blur"
			},
			{
				max: 32,
				message: "Nazwa użytkownika może posiadać maksymalnie 32 znaki",
				trigger: "blur"
			},
			{
				validator: (rule, value, callback) => {
					if (/^[a-z0-9_]*$/i.test(value)) callback();
					else callback(new Error("Nazwa użytkownika może zawierać litery bez znaków diaktrycznych, cyfry oraz znak \"_\""));
				},
				trigger: "blur"
			},
			{
				asyncValidator: (rule, value) => {
					if (!formState.isFormValidating) return;
                    return new Promise((resolve, reject) => {
                        authStore.validateActivationLogin(credentials.activationKey, value)
							.then(() => {
								resolve();
							})
							.catch(() => {
								reject('Podany login jest już zajęty.');
							});
					});
				},
				trigger: "blur"
			}
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
				max: 255,
				message: "Adres email może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			},
			{
				asyncValidator: (rule, value) => {
					if (!formState.isFormValidating) return;
                    return new Promise((resolve, reject) => {
                        authStore.validateActivationEmail(credentials.activationKey, value)
							.then(() => {
								resolve();
							})
							.catch(() => {
								reject('Podany adres email jest już zajęty.');
							});
					});
				},
				trigger: "blur"
			}
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
		activationKey: '',
		login: '',
		email: '',
		password: '',
		passwordConfirmation: ''
	});

	const validateCredentials = () => {
        formState.isFormValidating = true;
		credentialsForm.value.validate((valid) => {
			formState.isFormValidating = false;
			if (!valid) return;

			if (formState.step == 3) submitForm();
			else formState.step += 1;
		});
	};

    const submitForm = () => {
        authStore.register(credentials)
            .then(() => {
                formState.isFormDone = true;
				formState.step = 4;
            })
    };
</script>