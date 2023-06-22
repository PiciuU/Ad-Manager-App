<template>
    <div class="container">
        <div class="container__item">
            <div class="container__title">Zmiana hasła</div>
            <el-form
                class="form"
                ref="credentialsForm"
                :model="credentials"
                :rules="validationRules"
                @submit.prevent="validateCredentials"
                label-width="120px"
                label-position="left"
                :hide-required-asterisk="true"
            >
                <el-form-item prop="password_current" label="Obecne hasło">
                    <el-input
                        maxlength="128"
                        placeholder="Wprowadź aktualne hasło..."
                        show-password
                        v-model="credentials.password_current"
                    >
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>

                <el-form-item prop="password" label="Nowe hasło">
                    <el-input
                        maxlength="128"
                        placeholder="Wprowadź nowe hasło..."
                        show-password
                        v-model="credentials.password"
                    >
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>

                <el-form-item prop="password_confirmation" label="Potwierdź hasło">
                    <el-input
                        maxlength="128"
                        placeholder="Wprowadź ponownie nowe hasło..."
                        show-password
                        v-model="credentials.password_confirmation"
                    >
                        <template #prefix>
                            <font-awesome-icon class="form__input_icon" icon="lock" />
                        </template>
                    </el-input>
                </el-form-item>

                <el-button class="card__buttons--left" type="primary" native-type="submit">
                    Zmień hasło
                </el-button>
            </el-form>

            <div class="container__result" v-if="formState.isFormDone">
                <el-result
                    icon="success"
                    title="Sukces"
                    sub-title="Hasło zostało pomyślnie zmienione."
                ></el-result>
            </div>
        </div>

        <div class="container__item">
            <div class="container__title">Dostęp do konta</div>
            <div class="container__description">
                Jeżeli obawiasz się, że ktoś nieautoryzowany uzyskał dostęp do twojego konta, możesz
                skorzystać z poniższej opcji. Dzięki niej wszystkie urządzenia z dostępem do konta,
                zostaną natychmiastowo wylogowane z panelu.
            </div>

            <el-button class="container__button" type="danger" @click="forceLogout">
                Wyloguj ze wszystkich urządzeń
            </el-button>
        </div>
    </div>
</template>

<script>
import { reactive } from 'vue'

export default {
    name: 'SettingsSecurity',
    setup() {
        const formState = reactive({
            isFormSubmitting: false,
            isFormValidating: false,
            isFormDone: false
        })

        const credentials = reactive({
            password_current: '',
            password: '',
            password_confirmation: ''
        })

        const validationRules = {
            password_current: [
                {
                    required: true,
                    message: 'Hasło jest wymagane',
                    trigger: 'blur'
                }
            ],
            password: [
                {
                    required: true,
                    message: 'Hasło jest wymagane',
                    trigger: 'blur'
                },
                {
                    min: 6,
                    message: 'Hasło musi posiadać przynajmniej 6 znaków',
                    trigger: 'blur'
                },
                {
                    max: 128,
                    message: 'Hasło może posiadać maksymalnie 128 znaków',
                    trigger: 'blur'
                }
            ],
            password_confirmation: [
                {
                    required: true,
                    message: 'Potwierdzenie hasła jest wymagane',
                    trigger: 'blur'
                }
            ]
        }

        return {
            formState,
            credentials,
            validationRules
        }
    }
}
</script>

<style scoped lang="scss">
.container {
    display: flex;
    gap: 50px;

    &__item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 50%;
        margin: 10px 0px;
        padding: 0px 10px;
        text-align: left;

        &:first-child:after {
            content: '';
            position: absolute;
            width: 1px;
            height: 100%;
            background: $--color-text;
            right: -25px;
        }
    }

    &__result {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    &__title {
        position: relative;
        font-size: 16px;
        color: $--color-text;
        font-weight: bold;
        margin-bottom: 20px;
    }

    &__description {
        color: $--color-text;
        font-size: 14px;
    }

    &__button {
        margin-top: 20px;
    }
}

.form {
    width: 100%;

    &__title {
        font-size: 24px;
        font-weight: bold;
        color: $--color-text;
        margin-bottom: 20px;
    }

    &__title--big {
        font-size: 32px;
        text-align: center;
    }

    &__steps {
        text-align: left;
        margin-bottom: 10px;
        font-size: 14px;
    }

    &__button {
        margin-top: 5px;
    }

    &__links {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    &__link {
        text-decoration: none;
    }

    &__link--primary {
        color: var(--color-dark-blue);
        font-weight: bold;
    }

    &__link--secondary {
        color: var(--color-gray);
    }
}

@media screen and (max-width: 1000px) {
    .container {
        flex-direction: column;
        gap: 40px;

        &__item {
            width: 100%;

            &:first-child:after {
                width: 100%;
                height: 1px;
                background: $--color-text;
                right: unset;
                bottom: -35px;
            }
        }
    }

    .form {
        width: 90%;
    }
}

@media screen and (max-width: $--breakpoint-mobile) {
    .container__item {
        padding: 0px;
    }

    .container__title {
        margin-bottom: 10px;
    }

    .container__button {
        width: 100%;
    }

    .form {
        width: 100%;
    }

    .el-form-item {
        flex-direction: column;
        margin-bottom: 15px;
    }

    .form__button {
        width: 100%;
        margin-top: 10px;
    }
}
</style>
