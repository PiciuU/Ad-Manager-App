<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="24" :md="8">
                <el-card class="card card--full-height">
                    <div class="card__title">Dane kontaktowe</div>
                    <el-descriptions :column="1">
                        <el-descriptions-item label="Osoba kontaktowa: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.representative) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Adres email: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.email) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Telefon: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.representativePhone) }}
                        </el-descriptions-item>
                    </el-descriptions>
                    <div class="card__buttons--left card__buttons--bottom">
                        <el-button class="card__button" type="primary" @click="toggleCompanyDetails('person')" plain>Edytuj dane kontaktowe</el-button>
                    </div>
                </el-card>
            </el-col>
            <el-col :span="24" :md="16">
                <el-card class="card">
                    <div class="card__title">Dane firmowe</div>
                    <el-descriptions :column="1">
                        <el-descriptions-item label="Nazwa: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.name) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Adres: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.address) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kod pocztowy: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.postalCode) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Kraj: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.country) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="NIP: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.nip) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Firmowy adres email: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.companyEmail) }}
                        </el-descriptions-item>
                        <el-descriptions-item label="Firmowy telefon: " label-class-name="card__data-label" class-name="card__data-line">
                            {{ stringToLocale(user.companyPhone) }}
                        </el-descriptions-item>
                    </el-descriptions>
                    <div class="card__buttons--left">
                        <el-button class="card__button card__buttons--bottom" type="primary" @click="toggleCompanyDetails('company')" plain>Edytuj dane firmowe</el-button>
                    </div>
                </el-card>
            </el-col>
        </el-row>

        <ModalCompanyDetails v-if="modal.isVisible" :mode="modal.mode" @close="toggleCompanyDetails"/>
    </el-main>
</template>

<script setup>
    import { reactive, computed } from 'vue'

    import { stringToLocale } from '@/common/helpers/utility.helper'
    import ModalCompanyDetails from '@/modules/components/ModalCompanyDetails.vue'

    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const user = computed(() => authStore.user);

    const modal = reactive({
        isVisible: false,
        mode: ''
    })

    const toggleCompanyDetails = (type = '') => {
        modal.isVisible = !modal.isVisible
        modal.mode = type
    };
</script>