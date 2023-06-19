<template>
    <el-dialog
        v-model="modalVisible"
        :title="`Edycja danych `"
        @close="closeModal"
        :lock-scroll="true"
        :close-on-click-modal="false"
    >
        <el-form
            v-if="modalType == 'person'"
            ref="personDataForm"
            label-position="top"
            :hide-required-asterisk="true"
            :model="personData"
            :rules="validationPersonRules"
            @submit.prevent="validateData"
        >
            <el-form-item prop="person" label="Osoba kontaktowa">
                <el-input
                    maxlength="100"
                    placeholder="Wprowadź dane osoby kontakowej..."
                ></el-input>
            </el-form-item>
            <el-form-item prop="phone" label="Telefon">
                <el-input maxlength="32" placeholder="Wprowadź numer telefonu..."></el-input>
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
                <el-input maxlength="100" placeholder="Wprowadź nazwę firmy..."></el-input>
            </el-form-item>
            <el-form-item prop="address1" label="Adres">
                <el-input maxlength="150" placeholder="Wprowadź adres firmy..."></el-input>
            </el-form-item>
            <el-form-item prop="address2" label="Kod pocztowy i miasto">
                <el-input
                    maxlength="150"
                    placeholder="Wprowadź kod pocztowy i miasto..."
                ></el-input>
            </el-form-item>
            <el-form-item prop="nip" label="NIP">
                <el-input maxlength="20" placeholder="Wprowadź kod NIP..."></el-input>
            </el-form-item>
            <el-form-item prop="company_email" label="Firmowy adres email">
                <el-input maxlength="254" placeholder="Wprowadź firmowy adres email..."></el-input>
            </el-form-item>
            <el-form-item prop="company_phone" label="Firmowy telefon">
                <el-input
                    maxlength="32"
                    placeholder="Wprowadź firmowy numer telefonu..."
                ></el-input>
            </el-form-item>
        </el-form>

        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal">Anuluj zmiany</el-button>
                <el-button type="primary" @click="validateData">Zaktualizuj dane</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script>
import { inject, ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'
import ApiService from '@/services/api.service'
import NotificationService from '@/services/notification.service'

import { isStringEmpty } from '@/common/helpers/utility.helper'

import { AUTH_CHECK } from '@/store/actions.type'

export default {
    name: 'CompanyDetails',
    props: {
        modalType: {
            type: String,
            default: 'person'
        }
    },
    emits: ['close-modal'],
    setup(props, { emit }) {
        const store = useStore()
        const logger = inject('logger')

        const componentState = reactive({
            isLoading: false,
            isRendered: false
        })

        const modalVisible = true

        const user = computed(() => store.getters.currentUser)

        const validationPersonRules = {
            person: [
                {
                    required: true,
                    message: 'Dane osoby kontaktowej nie mogą być puste',
                    trigger: 'blur'
                },
                {
                    max: 100,
                    message: 'Dane osoby kontaktowej mogą mieć maksymalnie 100 znaków',
                    trigger: 'blur'
                }
            ],
            phone: [
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
            person: user.value.person,
            phone: user.value.phone
        })

        const validationCompanyRules = {
            name: [
                {
                    required: true,
                    message: 'Nazwa firmy nie może być pusta',
                    trigger: 'blur'
                },
                {
                    max: 100,
                    message: 'Nazwa firmy może posiadać maksymalnie 100 znaków',
                    trigger: 'blur'
                }
            ],
            address1: [
                {
                    required: true,
                    message: 'Adres nie może być pusty',
                    trigger: 'blur'
                },
                {
                    max: 150,
                    message: 'Adres może posiadać maksymalnie 150 znaków',
                    trigger: 'blur'
                }
            ],
            address2: [
                {
                    required: true,
                    message: 'Kod pocztowy i miasto nie mogą być puste',
                    trigger: 'blur'
                },
                {
                    max: 150,
                    message: 'Kod pocztowy i miasto mogą posiadać maksymalnie 150 znaków',
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
                    max: 20,
                    message: 'Kod NIP może posiadać maksymalnie 20 znaków',
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
            company_email: [
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
                    max: 254,
                    message: 'Adres email może posiadać maksymalnie 254 znaki',
                    trigger: 'blur'
                }
            ],
            company_phone: [
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
            name: user.value.name,
            address1: user.value.address1,
            address2: user.value.address2,
            nip: user.value.nip,
            company_email: user.value.company_email,
            company_phone: user.value.company_phone
        })

        function validateData() {
            if (props.modalType == 'person') {
                personDataForm.value.validate((valid) => {
                    if (!valid) return

                    saveChanges()
                })
            } else {
                companyDataForm.value.validate((valid) => {
                    if (!valid) return

                    saveChanges()
                })
            }
        }

        function saveChanges() {
            componentState.isLoading = true
            const postData = props.modalType == 'person' ? personData : companyData

            ApiService.post('auth/updateData', { ...postData })
                .then(() => {
                    store
                        .dispatch(AUTH_CHECK)
                        .then(() =>
                            NotificationService.displaySuccess(
                                'Sukces!',
                                'Pomyślnie zaktualizowano dane konta'
                            )
                        )
                        .catch((error) => logger(error, 'error'))
                        .finally(() => {
                            closeModal()
                            componentState.isLoading = false
                        })
                })
                .catch((error) => {
                    logger(error, 'warn')
                    componentState.isLoading = false
                })
        }

        function closeModal() {
            emit('close-modal')
        }

        return {
            isStringEmpty,
            componentState,
            user,
            modalVisible,
            closeModal,
            validationPersonRules,
            personDataForm,
            personData,
            validateData,
            validationCompanyRules,
            companyDataForm,
            companyData
        }
    }
}
</script>
