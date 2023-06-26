<template>
    <el-dialog :model-value="true" title="Kreator reklam" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="name" label="Nazwa reklamy">
                <el-input v-model="formData.name" maxlength="255" placeholder="Wprowadź nazwę swojej reklamy..."></el-input>
            </el-form-item>
            <el-form-item prop="adStartDate" label="Termin publikacji reklamy (Maksymalny okres emisji wynosi 30 dni)">
                <el-date-picker
                v-model="selectedDateRange"
                type="daterange"
                range-separator="do"
                start-placeholder="Początek emisji reklamy"
                end-placeholder="Koniec emisji reklamy"
                :disabled-date="disabledDates"
                @change="handleDateRangeChange"
                ></el-date-picker>
            </el-form-item>
            <el-form-item prop="url" label="Adres przekierowania">
                <el-input v-model="formData.url" maxlength="255" placeholder="(Opcjonalne) Wprowadź adres na który ma przekierować reklama..."></el-input>
            </el-form-item>
        </el-form>
        <div v-if="estimatedPrice">
            <el-alert type="info" show-icon :closable="false">
                <p>Koszt publikacji reklamy wyniesie: {{ estimatedPrice }} zł.</p>
                <p>Kwota ta została obliczona na podstawie okresu emisji, gdzie koszt jednego dnia wynosi {{ adStore.getPricePerDay }} zł.</p>
            </el-alert>
        </div>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Utwórz reklamę</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive, computed } from 'vue'
    import { useAdStore } from '@/stores/AdStore';

    import { isToday, convertToDateFormat } from '@/common/helpers/date.helper';
    import NotificationService from '@/services/notification.service'

    const adStore = useAdStore();

    const emit = defineEmits(['close', 'add']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        name: '',
        adStartDate: '',
        adEndDate: '',
        url: '',
    })

    const validationRules = {
        name: [
			{
				required: true,
				message: 'Nazwa reklamy jest wymagana',
				trigger: 'blur'
			},
			{
				max: 255,
				message: "Nazwa reklamy może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			},
		],
        adStartDate: [
			{
				required: true,
				message: 'Termin publikacji reklamy jest wymagany',
				trigger: 'blur'
			},
			{
				type: 'date',
				message: 'Termin publikacji reklamy musi być datą',
				trigger: 'blur'
			},
		],
        url: [
			{
				max: 255,
				message: "Adres przekierowania może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			},
		],
    }

    const closeModal = () => {
        if (!isLoading.value) emit('close');
    }

    const validateData = () => {
        form.value.validate((valid) => {
            if (!valid) return;
            submitForm();
        });
    }

    const submitForm = () => {
        isLoading.value = true;
        adStore.createAdvert(formData)
            .then((response) => {
                emit('add', response.data);
                emit('close');
                NotificationService.displayMessage('success', 'Pomyślnie stworzono reklamę.');
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy tworzeniu reklamy, prosimy spróbować ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };

    /* DATA */

    const selectedDateRange = ref([]);

    const estimatedPrice = computed(() => {
        if (!formData.adStartDate || !formData.adEndDate) {
            return 0;
        }

        const msInDay = 24 * 60 * 60 * 1000;
        const diffInDays = Math.round(Math.abs((new Date(formData.adEndDate) - new Date(formData.adStartDate)) / msInDay));

        return diffInDays * adStore.getPricePerDay;
    });

    const handleDateRangeChange = () => {
        if (selectedDateRange.value == null) {
            formData.adStartDate = ''
            formData.adEndDate = ''
            return;
        }
        const [startDate, endDate] = selectedDateRange.value;

        const msInDay = 24 * 60 * 60 * 1000;
        const maxRange = 30;

        const diffInDays = Math.round(Math.abs((endDate - startDate) / msInDay));
        if (diffInDays > maxRange) {
            const newEndDate = new Date(startDate.getTime() + maxRange * msInDay);
            selectedDateRange.value = [startDate, newEndDate];
        }
        formData.adStartDate = convertToDateFormat(selectedDateRange.value[0], 'yyyy-MM-dd');
        formData.adEndDate = convertToDateFormat(selectedDateRange.value[1], 'yyyy-MM-dd');
    };

    const disabledDates = (date) => {
        const currentDate = new Date();
        return date < currentDate && !isToday(date);
    };
</script>