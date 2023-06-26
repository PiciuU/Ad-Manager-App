<template>
    <el-dialog :model-value="true" title="Opłacenie faktury" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form label-position="top" @submit.prevent="submitForm">
            <el-alert class="alert" type="info" show-icon :closable="false">
                <p>Termin publikacji został przeliczony w oparciu o podany wcześniej okres emisji względem terminu płatności</p>
            </el-alert>
            <el-form-item label="Termin publikacji reklamy">
                <el-date-picker
                v-model="selectedDateRange"
                type="daterange"
                range-separator="do"
                start-placeholder="Początek"
                end-placeholder="Koniec"
                disabled
                @change="handleDateRangeChange"
                ></el-date-picker>
            </el-form-item>
            <el-alert class="alert" type="info" show-icon :closable="false">
                <p>Kwota na fakturze ostała obliczona na podstawie okresu emisji, gdzie koszt jednego dnia wynosi {{ adStore.getPricePerDay }} zł</p>
            </el-alert>
            <el-form-item label="Kwota do zapłaty">
                <el-input v-model="invoice.price" disabled>
                    <template v-slot:append>
                        zł
                    </template>
                </el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj</el-button>
                <el-button type="primary" @click="submitForm" :loading="isLoading">Opłać fakturę</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, onMounted } from 'vue'
    import { useAdStore } from '@/stores/AdStore';

    import NotificationService from '@/services/notification.service'

    const adStore = useAdStore();

    const props = defineProps({
        advert: { type: Object, required: true, default: {} }
    });

    const invoice = ref(null);

    onMounted(() => {
        handleDateRangeChange();

        for (const element of props.advert.invoices) {
            if (element.status === 'unpaid') {
                invoice.value = element;
                break;
            }
        }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const closeModal = () => {
        if (!isLoading.value) emit('close');
    }

    const submitForm = () => {
        isLoading.value = true;
        adStore.payInvoice(props.advert.id, invoice.value.id)
            .then((response) => {
                NotificationService.displayMessage('success', 'Faktura została pomyślnie opłacona, twoja reklama została aktywowana.');
                emit('update', response.data.advert, response.data.invoice);
                emit('close');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy opłacaniu faktury, prosimy spróbować ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };

    /* DATA */

    const selectedDateRange = ref([]);

    const handleDateRangeChange = () => {
        let startDate = new Date(props.advert.adStartDate);
        let endDate = new Date(props.advert.adEndDate);
        const currentDate = new Date();

        const msInDay = 24 * 60 * 60 * 1000;

        const diffInDays = Math.round(Math.abs((endDate - startDate) / msInDay));

        if (startDate < currentDate) {
            startDate = currentDate;
        }

        selectedDateRange.value = [startDate, new Date(startDate.getTime() + diffInDays * msInDay)]
    };
</script>

<style lang="scss" scoped>
    .alert {
        margin: 30px 0px 10px 0px;

        &:first-of-type {
            margin: 0px 0px 10px 0px;
        }
    }
</style>