<!-- eslint-disable vue/multi-word-component-names -->
<!-- eslint-disable vue/multi-word-component-names -->
<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="24" :md="8">
                <el-card class="card card--full-height">
                    <div class="card__title">Dane kontaktowe</div>
                    <el-descriptions :column="1">
                        <el-descriptions-item
                            label="Osoba kontaktowa: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.person) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Adres email: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.email) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Telefon: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.phone) }} -->
                        </el-descriptions-item>
                    </el-descriptions>
                    <div class="card__buttons--left card__buttons--bottom">
                        <el-button
                            class="card__button"
                            type="primary"
                            @click="enableCompanyDetails('person')"
                            plain
                            >Edytuj dane kontaktowe</el-button
                        >
                    </div>
                </el-card>
            </el-col>
            <el-col :span="24" :md="16">
                <el-card class="card">
                    <div class="card__title">Dane reklamodawcy</div>
                    <el-descriptions :column="1">
                        <el-descriptions-item
                            label="Nazwa: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.name) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Adres: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.address1) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Kod pocztowy i miasto: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.address2) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Kraj: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.country) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="NIP: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.nip) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Firmowy adres email: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.company_email) }} -->
                        </el-descriptions-item>
                        <el-descriptions-item
                            label="Firmowy telefon: "
                            label-class-name="card__data-label"
                            class-name="card__data-line"
                        >
                            <!-- {{ stringToLocale(user.company_phone) }} -->
                        </el-descriptions-item>
                    </el-descriptions>
                    <div class="card__buttons--left">
                        <el-button
                            class="card__button card__buttons--bottom"
                            type="primary"
                            @click="enableCompanyDetails('company')"
                            plain
                            >Edytuj dane reklamodawcy</el-button
                        >
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <company-details></company-details>
    </el-main>
</template>

<script>
import { reactive, computed, defineAsyncComponent } from 'vue'
import { useStore } from 'vuex'

import { stringToLocale } from '@/common/helpers/utility.helper'

import NotificationService from '@/services/notification.service'

export default {
    // eslint-disable-next-line vue/multi-word-component-names
    name: 'Company',
    components: {
        'company-details': defineAsyncComponent(() =>
            import('@/modules/components/CompanyDetails.vue')
        )
    },
    setup() {
        const modal = reactive({
            isOpen: false,
            currentValue: ''
        })

        const store = useStore()

        const user = computed(() => store.getters.currentUser)

        function enableCompanyDetails(type) {
            modal.isOpen = true
            modal.currentValue = type
        }

        function disableCompanyDetails() {
            modal.isOpen = false
            modal.currentValue = ''
        }

        return {
            stringToLocale,
            modal,
            user,
            NotificationService,
            enableCompanyDetails,
            disableCompanyDetails
        }
    }
}
</script>

<style lang="scss" scoped></style>
