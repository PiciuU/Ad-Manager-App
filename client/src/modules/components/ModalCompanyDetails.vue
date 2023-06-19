<template>
    <el-dialog
        :model-value="true"
        :title="`Edycja danych ${mode == 'person' ? 'kontaktowych' : 'reklamodawcy'}`"
        @close="$emit('close')"
        :lock-scroll="true"
        :close-on-click-modal="false"
    >
        <el-form
            v-if="mode == 'person'"
            ref="personDataForm"
            label-position="top"
            :hide-required-asterisk="true"
            :model="personData"
            :rules="validationPersonRules"
            @submit.prevent="validateData"
        >
            <el-form-item prop="representative" label="Osoba kontaktowa">
                <el-input v-model="personData.representative" maxlength="100" placeholder="Wprowadź dane osoby kontakowej..."></el-input>
            </el-form-item>
            <el-form-item prop="representativePhone" label="Telefon">
                <el-input v-model="personData.representativePhone" maxlength="32" placeholder="Wprowadź numer telefonu..."></el-input>
            </el-form-item>
        </el-form>

        <el-form
            v-else
            ref="companyDataForm"
            label-position="top"
            :hide-required-asterisk="true"
            :model="companyData"
            :rules="validationCompanyRules"
            @submit.prevent="validateData"
        >
            <el-form-item prop="name" label="Nazwa">
                <el-input v-model="companyData.name" maxlength="255" placeholder="Wprowadź nazwę firmy..."></el-input>
            </el-form-item>
            <el-form-item prop="address" label="Adres">
                <el-input v-model="companyData.address" maxlength="255" placeholder="Wprowadź adres firmy..."></el-input>
            </el-form-item>
            <el-form-item prop="postalCode" label="Kod pocztowy">
                <el-input v-model="companyData.postalCode" maxlength="255" placeholder="Wprowadź kod pocztowy..."></el-input>
            </el-form-item>
            <el-form-item prop="nip" label="NIP">
                <el-input v-model="companyData.nip" maxlength="10" placeholder="Wprowadź kod NIP..."></el-input>
            </el-form-item>
            <el-form-item prop="companyEmail" label="Firmowy adres email">
                <el-input v-model="companyData.companyEmail" maxlength="255" placeholder="Wprowadź firmowy adres email..."></el-input>
            </el-form-item>
            <el-form-item prop="companyPhone" label="Firmowy telefon">
                <el-input v-model="companyData.companyPhone" maxlength="32" placeholder="Wprowadź firmowy numer telefonu..."></el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('close')" :loading="authStore.isLoading">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData" :loading="authStore.isLoading">Zaktualizuj dane</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive } from 'vue'

    import { useAuthStore } from '@/stores/AuthStore';
    import NotificationService from '@/services/notification.service'

    import { isStringEmpty } from '@/common/helpers/utility.helper'

    const authStore = useAuthStore();

    const props = defineProps({
        mode: { type: String, required: true, default: 'person' }
    });

    const emit = defineEmits(['close']);

    const validationPersonRules = {
        representative: [
            {
                required: true,
                message: 'Dane osoby kontaktowej nie mogą być puste',
                trigger: 'blur'
            },
            {
                max: 255,
                message: 'Dane osoby kontaktowej mogą mieć maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        representativePhone: [
            {
                required: true,
                message: 'Numer telefonu nie może być pusty',
                trigger: 'blur'
            },
            {
                max: 32,
                message: 'Numer telefonu może mieć maksymalnie 32 znaki',
                trigger: 'blur'
            },
            {
                validator: (rule, value, callback) => {
                    if (!isStringEmpty(value) && /[a-z]/i.test(value))
                        callback(new Error('Numer telefonu nie może zawierać liter'))
                    else callback()
                },
                trigger: 'blur'
            }
        ]
    }

    const personDataForm = ref()

    const personData = reactive({
        representative: authStore.getUser.representative,
        representativePhone: authStore.getUser.representativePhone
    })

    const validationCompanyRules = {
        name: [
            {
                required: true,
                message: 'Nazwa firmy nie może być pusta',
                trigger: 'blur'
            },
            {
                max: 255,
                message: 'Nazwa firmy może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        address: [
            {
                required: true,
                message: 'Adres nie może być pusty',
                trigger: 'blur'
            },
            {
                max: 255,
                message: 'Adres może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        postalCode: [
            {
                required: true,
                message: 'Kod pocztowy nie może być pusty',
                trigger: 'blur'
            },
            {
                max: 255,
                message: 'Kod pocztowy może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        nip: [
            {
                required: true,
                message: 'Kod NIP nie może być pusty',
                trigger: 'blur'
            },
            {
                max: 10,
                message: 'Kod NIP może posiadać maksymalnie 10 znaków',
                trigger: 'blur'
            },
            {
                validator: (rule, value, callback) => {
                    if (!isStringEmpty(value) && /[a-z]/i.test(value))
                        callback(new Error('Kod NIP nie może zawierać liter'))
                    else callback()
                },
                trigger: 'blur'
            }
        ],
        companyEmail: [
            {
                type: 'email',
                message: 'Wprowadź poprawny adres email',
                trigger: 'blur'
            },
            {
                min: 3,
                message: 'Adres email musi posiadać przynajmniej trzy znaki',
                trigger: 'blur'
            },
            {
                max: 255,
                message: 'Adres email może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        companyPhone: [
            {
                max: 32,
                message: 'Numer telefonu może mieć maksymalnie 32 znaki',
                trigger: 'blur'
            },
            {
                validator: (rule, value, callback) => {
                    if (!isStringEmpty(value) && /[a-z]/i.test(value))
                        callback(new Error('Numer telefonu nie może zawierać liter'))
                    else callback()
                },
                trigger: 'blur'
            }
        ]
    }

    const companyDataForm = ref()

    const companyData = reactive({
        name: authStore.getUser.name,
        address: authStore.getUser.address,
        postalCode: authStore.getUser.postalCode,
        nip: authStore.getUser.nip,
        companyEmail: authStore.getUser.companyEmail,
        companyPhone: authStore.getUser.companyPhone
    })

    const validateData = () => {
        if (props.mode == 'person') {
            personDataForm.value.validate((valid) => {
                if (!valid) return;
                submitForm();
            });
        } else {
            companyDataForm.value.validate((valid) => {
                if (!valid) return;
                submitForm();
            });
        }
    }

    const submitForm = () => {
        const formData = props.mode == 'person' ? personData : companyData
        authStore.updateData(formData)
            .then(() => {
                NotificationService.displaySuccess(
                    'Sukces!',
                    'Pomyślnie zaktualizowano dane konta'
                )
            })
            .catch((e) => {
                console.log(e);
                NotificationService.displayError()
            })
            .finally(() => {
                emit('close')
            });
    };
</script>
