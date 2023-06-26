<template>
    <el-dialog :model-value="true" :title="`Edycja faktury (ID: ${props.invoice.id})`" :lock-scroll="true" :before-close="closeModal" :close-on-click-modal="!isLoading" :close-on-press-escape="!isLoading">
        <el-form ref="form" label-position="top" :hide-required-asterisk="true" :model="formData" :rules="validationRules" @submit.prevent="validateData">
            <el-form-item prop="number" label="Numer faktury">
                <el-input v-model="formData.number" maxlength="255" placeholder="Wprowadź numer faktury..."></el-input>
            </el-form-item>
            <el-form-item prop="price" label="Kwota na fakturze">
                <el-input v-model.number="formData.price" placeholder="Wprowadź kwotę na fakturze...">
                    <template v-slot:append>
                        zł
                    </template>
                </el-input>
            </el-form-item>
            <el-form-item prop="number" label="Data uiszczenia opłaty" class="full-width">
                <el-date-picker
                    v-model="formData.date"
                    type="date"
                    value-format="YYYY-MM-DD"
                    placeholder="Wybierz datę uiszczenia opłaty..."
                />
            </el-form-item>
            <el-form-item prop="status" label="Status">
                <el-select v-model="formData.status" placeholder="Wybierz status faktury..." class="full-width">
                    <el-option label="Opłacona" value="paid" />
                    <el-option label="Nieopłacona" value="unpaid" />
                </el-select>
            </el-form-item>
            <el-form-item label="Notatka">
                <el-input class="modal__input" v-model="formData.notes" :rows="3" resize="none" type="textarea" placeholder="Wprowadź notatkę..."></el-input>
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
    import { ref, reactive } from 'vue'
    import { useAdminStore } from '@/stores/AdminStore';

    import NotificationService from '@/services/notification.service'

    const adminStore = useAdminStore();

    const props = defineProps({
        advert: { type: Object, required: true, default: {} },
        invoice: { type: Object, required: true, default: {} }
    });

    const emit = defineEmits(['close', 'update']);

    const isLoading = ref(false);

    const form = ref()

    const formData = reactive({
        number: props.invoice.number,
        price: props.invoice.price,
        date: props.invoice.date,
        status: props.invoice.status,
        notes: props.invoice.notes,
    })


    const validationRules = {
        number: [
			{
				required: true,
				message: 'Numer faktury jest wymagany',
				trigger: 'blur'
			},
			{
				max: 255,
				message: "Numer faktury może posiadać maksymalnie 255 znaków",
				trigger: "blur"
			},
		],
        price: [
            {
				required: true,
				message: 'Kwota na fakturze jest wymagana',
				trigger: 'blur'
			},
        ],
        date: [
			{
				required: true,
				message: 'Data uiszczenia opłaty jest wymagana',
				trigger: 'blur'
			},
			{
				type: 'date',
				message: 'Data uiszczenia opłaty musi być datą',
				trigger: 'blur'
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
        adminStore.updateInvoice(props.advert.id, props.invoice.id, formData)
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
</script>

<style lang="scss" scoped>
    .full-width, ::v-deep(.el-date-editor), ::v-deep(.el-date-editor > .el-input__wrapper) {
        width: 100%;
    }
</style>