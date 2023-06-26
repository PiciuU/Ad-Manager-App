<template>
    <el-dialog :model-value="true" :title="`Edycja reklamy (ID: ${props.advert.id})`" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="name" label="Nazwa reklamy">
                <el-input v-model="formData.name" maxlength="255" placeholder="Wprowadź nazwę reklamy..."></el-input>
            </el-form-item>
            <el-form-item prop="adStartDate" label="Okres emisji reklamy">
                <el-date-picker
                v-model="selectedDateRange"
                type="daterange"
                range-separator="do"
                start-placeholder="Początek"
                end-placeholder="Koniec"
                @change="handleDateRangeChange"
                ></el-date-picker>
            </el-form-item>
            <el-form-item prop="status" label="Status">
                <el-select v-model="formData.status" placeholder="Wybierz status reklamy..." class="full-width">
                    <el-option label="Aktywna" value="active" />
                    <el-option label="Nieaktywna" value="inactive" />
                    <el-option label="Wygasła" value="expired" />
                    <el-option label="Nieopłacona" value="unpaid" />
                </el-select>
            </el-form-item>
            <el-form-item prop="url" label="Adres przekierowania">
                <el-input v-model="formData.url" maxlength="255" placeholder="(Opcjonalne) Wprowadź adres na który ma przekierować reklama..."></el-input>
            </el-form-item>
        </el-form>
        <template #footer>
            <span class="dialog-footer">
                <el-button @click="closeModal" :loading="isLoading">Anuluj</el-button>
                <el-button type="primary" @click="validateData" :loading="isLoading">Zapisz zmiany</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
    import { ref, reactive, onMounted } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';

    import { convertToDateFormat } from '@/common/helpers/date.helper';
    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const props = defineProps({
        advert: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        name: props.advert.name,
        adStartDate: props.advert.adStartDate,
        adEndDate: props.advert.adEndDate,
        status: props.advert.status,
        url: props.advert.url,
    })

    onMounted(() => {
        selectedDateRange.value = [formData.adStartDate, formData.adEndDate];
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
        adminStore.updateAdvert(props.advert.id, formData)
            .then((response) => {
                emit('update', response.data);
                emit('close');
                NotificationService.displayMessage('success', 'Pomyślnie zaktualizowano dane reklamy.');
            })
            .catch((e) => {
                console.log(e);
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy edycji reklamy, spróbuj ponownie później.');
            })
            .finally(() => {
                isLoading.value = false;
            })
    };

    /* Date */
    const selectedDateRange = ref([]);

    const handleDateRangeChange = () => {
        if (selectedDateRange.value == null) {
            formData.adStartDate = ''
            formData.adEndDate = ''
            return;
        }

        formData.adStartDate = convertToDateFormat(selectedDateRange.value[0], 'yyyy-MM-dd');
        formData.adEndDate = convertToDateFormat(selectedDateRange.value[1], 'yyyy-MM-dd');
    };
</script>

<style lang="scss" scoped>
    .full-width {
        width: 100%;
    }
</style>