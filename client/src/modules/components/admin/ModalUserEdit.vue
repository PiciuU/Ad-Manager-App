<template>
    <el-dialog :model-value="true" title="Edycja danych użytkownika" @close="$emit('close')" :lock-scroll="true" :close-on-click-modal="true">

        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="login" label="Login">
                <el-input v-model="formData.login" maxlength="32" placeholder="Wprowadź login..."></el-input>
            </el-form-item>
            <el-form-item prop="email" label="Adres email">
                <el-input v-model="formData.email" maxlength="255" placeholder="Wprowadź adres email..."></el-input>
            </el-form-item>
            <el-form-item prop="name" label="Nazwa firmy">
                <el-input v-model="formData.name" maxlength="255" placeholder="Wprowadź nazwę firmy..."></el-input>
            </el-form-item>
            <el-form-item prop="representative" label="Osoba kontaktowa">
                <el-input v-model="formData.representative" maxlength="255" placeholder="Wprowadź osobę kontaktową..."></el-input>
            </el-form-item>
            <el-form-item prop="representativePhone" label="Telefon kontaktowy">
                <el-input v-model="formData.representativePhone" maxlength="255" placeholder="Wprowadź telefon kontaktowy..."></el-input>
            </el-form-item>
            <el-form-item prop="address" label="Adres firmowy">
                <el-input v-model="formData.address" maxlength="255" placeholder="Wprowadź adres firmy..."></el-input>
            </el-form-item>
            <el-form-item prop="postalCode" label="Kod pocztowy">
                <el-input v-model="formData.postalCode" maxlength="255" placeholder="Wprowadź kod pocztowy..."></el-input>
            </el-form-item>
            <el-form-item prop="country" label="Kraj">
                <el-input v-model="formData.country" maxlength="255" placeholder="Wprowadź kraj..."></el-input>
            </el-form-item>
            <el-form-item prop="nip" label="NIP">
                <el-input v-model="formData.nip" maxlength="10" placeholder="Wprowadź kod NIP..."></el-input>
            </el-form-item>
            <el-form-item prop="companyEmail" label="Firmowy adres email">
                <el-input v-model="formData.companyEmail" maxlength="255" placeholder="Wprowadź firmowy adres email..."></el-input>
            </el-form-item>
            <el-form-item prop="companyPhone" label="Firmowy telefon">
                <el-input v-model="formData.companyPhone" maxlength="32" placeholder="Wprowadź firmowy numer telefonu..."></el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="$emit('close')" :loading="isLoading">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Zaktualizuj dane</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';

    import { isStringEmpty } from '@/common/helpers/utility.helper'

    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const props = defineProps({
        user: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        login: props.user.login,
        email: props.user.email,
        name: props.user.name,
        representative: props.user.representative,
        representativePhone: props.user.representativePhone,
        address: props.user.address,
        postalCode: props.user.postalCode,
        country: props.user.country,
        nip: props.user.nip,
        companyEmail: props.user.companyEmail,
        companyPhone: props.user.companyPhone,
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
        representative: [
            {
                max: 255,
                message: 'Dane osoby kontaktowej mogą mieć maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        representativePhone: [
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
        ],
        address: [
            {
                max: 255,
                message: 'Adres może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        postalCode: [
            {
                max: 255,
                message: 'Kod pocztowy może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        country: [
            {
                max: 255,
                message: 'Kraj może posiadać maksymalnie 255 znaków',
                trigger: 'blur'
            }
        ],
        nip: [
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
                validator: (rule, value, callback) => {
                    if (!isStringEmpty(value) && /[a-z]/i.test(value))
                        callback(new Error('Numer telefonu nie może zawierać liter'))
                    else callback()
                },
                trigger: 'blur'
            }
        ]
    }

    const validateData = () => {
        form.value.validate((valid) => {
            if (!valid) return;
            submitForm();
        });
    }

    const submitForm = () => {
        isLoading.value = true;
        adminStore.changeUserData(props.user.id, formData)
            .then((response) => {
                emit('update', response.data);
                emit('close');
            })
            .catch((e) => {
                console.log(e);
                NotificationService.displayError('Nieoczekiwany błąd', e.message);
            })
            .finally(() => {
                isLoading.value = false;
            })
    };
</script>