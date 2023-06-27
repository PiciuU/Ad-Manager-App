<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="24" :md="16">
                <el-card class="card">
                    <div class="card__title">Lista reklam</div>
                    <el-select
                        class="card__select"
                        filterable
                        @change="handleAdvertChange"
                        v-model="filter.advertId"
                        :loading="!adStore.getAdverts"
                        :disabled="isAdvertFetching"
                        loading-text="Wyszukiwanie reklam..."
                        placeholder="Wybierz reklamę..."
                        no-data-text="Nie znaleziono żadnych reklam."
                        clearable
                    >
                        <el-option v-for="advert in adStore.adverts" :key="advert.id" :label="advert.name" :value="advert.id"></el-option>
                    </el-select>
                </el-card>
            </el-col>
            <el-col :span="24" :md="8">
                <el-card class="card">
                    <div class="card__title">Operacje</div>
                    <el-button @click="toggleModal('create')" class="card__button card__button--fill" type="primary" plain>Utwórz nową reklamę</el-button>
                </el-card>
            </el-col>
        </el-row>

        <div v-if="filter.advertId && filter.advertId != null">
            <!-- Szczegóły -->
            <el-row class="cards__container" :gutter="32">
                <el-col :span="24" :md="12">
                    <el-card class="card">
                        <div class="card__title">Szczegóły reklamy</div>
                        <div v-if="!isAdvertFetching">
                            <el-descriptions :column="1">
                                <el-descriptions-item label="Nazwa reklamy: " label-class-name="card__data-label">
                                    {{ stringToLocale(data.advert.name) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Status reklamy: " label-class-name="card__data-label" :class-name="{'color-warning': data.advert.status == 'unpaid', 'color-success': data.advert.status == 'active' }">
                                    {{ advertStatusToLocaleString(data.advert.status) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Wyświetlana od: " label-class-name="card__data-label">
                                    {{ stringToLocale(data.advert.adStartDate) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Wyświetlana do: " label-class-name="card__data-label">
                                    {{ stringToLocale(data.advert.adEndDate) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Adres przekierowania: " label-class-name="card__data-label">
                                    <a v-if="data.advert.url" :href="data.advert.url" class="card__url" target="_blank">{{ data.advert.url }}</a>
                                    <span v-else> {{ stringToLocale(data.advert.url) }}</span>
                                </el-descriptions-item>
                            </el-descriptions>
                        </div>
                        <el-skeleton :rows="5" animated v-else />
                    </el-card>
                </el-col>

                <el-col :span="24" :md="12">
                    <el-card class="card">
                        <div class="card__title">Pliki reklamy</div>
                        <div v-if="!isAdvertFetching">
                            <AdvertsUploader @update="updateAdvertFile" :advert="data.advert"/>
                        </div>
                        <el-skeleton :rows="5" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <!-- Operacje -->
            <el-row class="cards__container">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Operacje na reklamie</div>
                        <div v-if="!isAdvertFetching">
                            <div class="button__group" v-if="data.advert.status == 'unpaid'">
                                <el-button @click="toggleModal('pay')" class="card__button" type="primary" plain>Opłać fakturę</el-button>
                            </div>
                            <div class="button__group" v-else>
                                <el-button @click="toggleModal('edit')" class="card__button" type="primary" plain>Edytuj reklamę</el-button>
                                <el-button v-if="data.advert.status == 'active'" @click="deactivateAdvert" class="card__button" type="primary" plain>Zdezaktywuj reklamę</el-button>
                                <el-button v-if="data.advert.status == 'inactive' || data.advert.status == 'expired'" @click="toggleModal('renew')" class="card__button" type="primary" plain>Odnów reklamę</el-button>
                            </div>
                        </div>
                        <el-skeleton :rows="1" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <!-- Faktury -->
            <el-row class="cards__container">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Twoje faktury</div>
                        <div v-if="!isAdvertFetching">
                            <el-table :data="invoicesPagination.invoices" stripe style="width: 100%">
                                <el-table-column prop="number" label="Numer faktury" width="150"/>
                                <el-table-column label="Status" width="120">
                                    <template #default="scope">
                                        <span :class="{'color-warning': scope.row.status == 'unpaid'}">
                                            {{ scope.row.status == 'unpaid' ? 'Nieopłacona' : 'Opłacona' }}
                                        </span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="date" label="Data" width="100"/>
                                <el-table-column label="Kwota" width="75">
                                    <template #default="scope">
                                        <span>{{ scope.row.price }} zł</span>
                                    </template>
                                </el-table-column>
                                <el-table-column label="Szczegóły" min-width="300" width="auto">
                                    <template #default="scope">
                                        <span>{{ stringToLocale(scope.row.notes) }}</span>
                                    </template>
                                </el-table-column>
                            </el-table>

                            <el-pagination
                                v-if="data.advert.invoices"
                                class="card__pagination"
                                :current-page="invoicesPagination.current_page"
                                :page-size="invoicesPagination.per_page"
                                :total="invoicesPagination.total"
                                :small="true"
                                :background="true"
                                layout="prev, pager, next, jumper"
                                @current-change="handlePageChange"
                            />
                        </div>
                        <el-skeleton :rows="2" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <!-- Statystyki -->
            <el-row class="cards__container">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Statystyki reklam</div>
                        <div v-if="!isAdvertFetching">
                            <el-tabs class="card__tabs" v-model="filter.type">
                                <el-tab-pane label="Tydzień" name="week">
                                    <div class="date-picker">
                                        <el-date-picker
                                            v-model="filter.week"
                                            type="week"
                                            placeholder="Wybierz tydzień"
                                            format="[Tydzień] ww"
                                            value-format="YYYY-MM-DD"
                                            :clearable="false"
                                            @change="handleAdvertStatsChange"
                                        >
                                        </el-date-picker>
                                        <el-button class="btn-spacing" @click="handleAdvertStatsChange" :disabled="isAdvertStatsFetching">
                                            <font-awesome-icon icon="search"/>
                                        </el-button>
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="Miesiąc" name="month">
                                    <div class="date-picker">
                                        <el-date-picker
                                            v-model="filter.month"
                                            type="month"
                                            placeholder="Wybierz miesiąc"
                                            format="YYYY/MM"
                                            value-format="YYYY-MM"
                                            :clearable="false"
                                            @change="handleAdvertStatsChange"
                                        >
                                        </el-date-picker>
                                        <el-button class="btn-spacing" @click="handleAdvertStatsChange" :disabled="isAdvertStatsFetching">
                                            <font-awesome-icon icon="search"/>
                                        </el-button>
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="Rok" name="year">
                                    <div class="date-picker">
                                        <el-date-picker
                                            v-model="filter.year"
                                            type="year"
                                            placeholder="Wybierz rok"
                                            format="YYYY"
                                            value-format="YYYY"
                                            :clearable="false"
                                            @change="handleAdvertStatsChange"
                                        >
                                        </el-date-picker>
                                        <el-button class="btn-spacing" @click="handleAdvertStatsChange" :disabled="isAdvertStatsFetching">
                                            <font-awesome-icon icon="search"/>
                                        </el-button>
                                    </div>
                                </el-tab-pane>
                                <el-tab-pane label="Zakres dat" name="monthrange" class="tab-date-picker">
                                    <div class="date-picker">
                                        <el-date-picker
                                            v-model="filter.monthrange"
                                            type="monthrange"
                                            unlink-panels
                                            range-separator="-"
                                            start-placeholder="Data początkowa"
                                            end-placeholder="Data końcowa"
                                            format="YYYY/MM"
                                            value-format="YYYY-MM"
                                            :shortcuts="filter.shortcuts"
                                            :clearable="false"
                                            @change="handleAdvertStatsChange"
                                        >
                                        </el-date-picker>
                                        <el-button class="btn-spacing" @click="handleAdvertStatsChange" :disabled="isAdvertStatsFetching">
                                            <font-awesome-icon icon="search"/>
                                        </el-button>
                                    </div>
                                </el-tab-pane>
                            </el-tabs>
                        </div>
                        <el-skeleton :rows="2" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <el-row class="cards__container" v-if="data.advert?.views">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Wyświetlenia</div>
                        <el-tabs v-if="!isAdvertStatsFetching" v-model="filter.graphs.currentViewsTab">
                            <el-tab-pane label="Wykres liniowy" name="line">
                                <line-chart download="Wyswietlenia_liniowy" :data="data.advert.views" :discrete="true" empty="Brak danych"></line-chart>
                            </el-tab-pane>
                            <el-tab-pane label="Wykres kolumnowy" name="column">
                                <column-chart download="Wyswietlenia_kolumnowy" :data="data.advert.views" :discrete="true" empty="Brak danych"></column-chart>
                            </el-tab-pane>
                            <el-tab-pane label="Wykres słupkowy" name="bar">
                                <bar-chart download="Wyswietlenia_slupkowy" :data="data.advert.views" :discrete="true" empty="Brak danych"></bar-chart>
                            </el-tab-pane>
                            <el-tab-pane label="Dane ogólne" name="details">
                                <el-descriptions :column="1">
                                    <el-descriptions-item label="Wszystkie wyświetlenia: " label-class-name="card__data-label" class-name="card__data-line">{{ data.advert.summary.all_views }}</el-descriptions-item>
                                    <el-descriptions-item label="Najwięcej wyświetleń w: " label-class-name="card__data-label" class-name="card__data-line">{{ data.advert.summary.most_views.date }} ({{ data.advert.summary.most_views.views }})</el-descriptions-item>
                                    <el-descriptions-item label="Najmniej wyświetleń w: " label-class-name="card__data-label" class-name="card__data-line">{{ data.advert.summary.least_views.date }} ({{ data.advert.summary.least_views.views }})</el-descriptions-item>
                                </el-descriptions>
                            </el-tab-pane>
                        </el-tabs>
                        <el-skeleton :rows="5" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <el-row class="cards__container" v-if="data.advert?.views">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Kliknięcia</div>
                        <el-tabs v-if="!isAdvertStatsFetching" v-model="filter.graphs.currentClicksTab">
                            <el-tab-pane label="Wykres liniowy" name="line">
                                <line-chart download="Wyswietlenia_liniowy" :data="data.advert.clicks" :discrete="true" empty="Brak danych"></line-chart>
                            </el-tab-pane>
                            <el-tab-pane label="Wykres kolumnowy" name="column">
                                <column-chart download="Wyswietlenia_kolumnowy" :data="data.advert.clicks" :discrete="true" empty="Brak danych"></column-chart>
                            </el-tab-pane>
                            <el-tab-pane label="Wykres słupkowy" name="bar">
                                <bar-chart download="Wyswietlenia_slupkowy" :data="data.advert.clicks" :discrete="true" empty="Brak danych"></bar-chart>
                            </el-tab-pane>
                            <el-tab-pane label="Dane ogólne" name="details">
                                <el-descriptions :column="1">
                                    <el-descriptions-item label="Wszystkie wyświetlenia: " label-class-name="card__data-label" class-name="card__data-line">{{ data.advert.summary.all_clicks }}</el-descriptions-item>
                                    <el-descriptions-item label="Najwięcej wyświetleń w: " label-class-name="card__data-label" class-name="card__data-line">{{ data.advert.summary.most_clicks.date }} ({{ data.advert.summary.most_clicks.clicks }})</el-descriptions-item>
                                    <el-descriptions-item label="Najmniej wyświetleń w: " label-class-name="card__data-label" class-name="card__data-line">{{ data.advert.summary.least_clicks.date }} ({{ data.advert.summary.least_clicks.clicks }})</el-descriptions-item>
                                </el-descriptions>
                            </el-tab-pane>
                        </el-tabs>
                        <el-skeleton :rows="5" animated v-else />
                    </el-card>
                </el-col>
            </el-row>


        </div>

        <!-- Modale -->
        <ModalAdvertsCreate v-if="modals.currentModal === 'create'" @add="addAdvert" @close="toggleModal"/>
        <ModalAdvertsPay v-if="modals.currentModal === 'pay'" :advert="data.advert" @update="activateAdvert" @close="toggleModal"/>
        <ModalAdvertsEdit v-if="modals.currentModal === 'edit'" :advert="data.advert" @update="updateAdvert" @close="toggleModal"/>
        <ModalAdvertsRenew v-if="modals.currentModal === 'renew'" :advert="data.advert" @update="renewAdvert" @close="toggleModal"/>
    </el-main>
</template>

<script setup>
    import { ref, reactive, computed, onMounted, onUpdated } from 'vue';
    import { useRoute, useRouter } from 'vue-router';

    import { useAdStore } from '@/stores/AdStore';
    import DateHelper from '@/common/helpers/date.helper';
    import NotificationService from '@/services/notification.service';
    import { advertStatusToLocaleString, stringToLocale } from '@/common/helpers/utility.helper';

    const route = useRoute();
    const router = useRouter();
    const adStore = useAdStore();

    onMounted(() => {
        adStore.fetchAdverts()
                .then(() => {
                    if (route.params.id) {
                        filter.advertId = parseInt(route.params.id);
                        handleAdvertChange();
                    }
                })
                .catch(() => {
                    NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy pobieraniu listy reklam, prosimy spróbować ponownie później.');
                })
    });

    onUpdated(() => {
        if (route.params.id && filter.advertId !== parseInt(route.params.id)) {
            filter.advertId = parseInt(route.params.id);
            handleAdvertChange();
        }
    })

	const data = reactive({
		ads: {},
		advert: {}
	});

	const filter = reactive({
		advertId: null,
		type: 'month',
		week: DateHelper.getCurrentWeek(),
		month: DateHelper.getCurrentMonth(),
		year: DateHelper.getCurrentYear(),
		monthrange: DateHelper.getCurrentMonthRange(),
		graphs:{
			currentViewsTab: 'line',
			currentClicksTab: 'line',
		},
		shortcuts: [
			{
				text: 'Ostatnie 3 miesiące',
				value: () => DateHelper.getCurrentMonthRange(2),
			},
			{
				text: 'Ostatnie 6 miesięcy',
				value: () => DateHelper.getCurrentMonthRange(5),
			},
			{
				text: 'Ostatni rok',
				value: () => DateHelper.getCurrentMonthRange(11),
			},
		]
	});

    /* Modals */
    const modals = reactive({
        currentModal: ''
    });

    const toggleModal = (modal = '') => {
        if (modal == 'pay' && data.advert.invoices.length == 0) {
            NotificationService.displayMessage('error', 'Wystąpił krytyczny błąd przy odczytywaniu faktury, prosimy o kontakt z właścicielem strony.');
            return;
        }
        modals.currentModal = modal;
    };

    /*  Advert */
    const isAdvertFetching = ref(false);
    const isAdvertStatsFetching = ref(false);

	const handleAdvertChange = async () => {
        router.push({ name: 'Ads', params: { id: filter.advertId }});

        data.advert = {};

		if (!filter.advertId || filter.advertId === null) return;

        isAdvertFetching.value = true;

        let isErrorOccurred = false;
        let advertResponseHandler = null;
        let invoicesResponseHandler = null;

        await Promise.all([
            adStore.fetchAdvert(filter.advertId)
            .then((response) => {
                advertResponseHandler = response.data;
            })
            .catch(() => {
                isErrorOccurred = true;
            }),

            adStore.fetchInvoices(filter.advertId)
            .then((response) => {
                invoicesResponseHandler = response.data;
            })
            .catch(() => {
                isErrorOccurred = true;
            })
        ])

        isAdvertFetching.value = false;

        if (isErrorOccurred) {
            NotificationService.displayMessage('error', 'Wystąpił błąd przy pobieraniu wybranej reklamy, prosimy spróbować ponownie później.');
            filter.advertId = null;
            return;
        }

        data.advert = advertResponseHandler;
        data.advert.invoices = invoicesResponseHandler;
        invoicesPagination.invoices = data.advert.invoices.slice(0, invoicesPagination.per_page);
	};

    /* Create Advert */
    import ModalAdvertsCreate from '@/modules/components/ModalAdvertsCreate.vue';

    const addAdvert = (advert) => {
        adStore.adverts.unshift(advert);
    }

    /* Pay Invoice */
    import ModalAdvertsPay from '@/modules/components/ModalAdvertsPay.vue';

    const activateAdvert = (advert, invoice) => {
        Object.assign(data.advert, advert);
        data.advert.invoices.forEach((inv) => {
            if (inv.id == invoice.id) {
                Object.assign(inv, invoice);
            }
        });
    };

    /* Edit Advert */
    import ModalAdvertsEdit from '@/modules/components/ModalAdvertsEdit.vue';

    const updateAdvert = (advert) => {
        if (data.advert.name != advert.name) {
            const ad = adStore.adverts.find((ad) => ad.name == data.advert.name);
            ad.name = advert.name;
        }
        Object.assign(data.advert, advert);
    }

    /* Renew Advert */
    import ModalAdvertsRenew from '@/modules/components/ModalAdvertsRenew.vue';

    const renewAdvert = (advert, invoice) => {
        Object.assign(data.advert, advert);
        data.advert.invoices.unshift(invoice);
        handlePageChange();
    }

    /* Deactivate Advert */

    const deactivateAdvert = async () => {
        const isConfirmed = await NotificationService.displayConfirmation('Czy na pewno chcesz zdezaktywać reklamę przed zakończeniem jej emisji? Ponowna aktywacja wymagać będzie uiszczenia opłaty za nową fakturę.');
        if (!isConfirmed) return;

        const notify = NotificationService.displayMessage('info', 'Trwa dezaktywowanie reklamy...', 0);
        adStore.deactivateAdvert(data.advert.id)
            .then((response) => {
                Object.assign(data.advert, response.data);
                NotificationService.displayMessage('success', 'Pomyślnie zdezaktywowano reklamę.');
            })
            .finally(() => {
                notify.close();
            })
    }

    /* File Upload */
    import AdvertsUploader from '@/modules/components/AdvertsUploader.vue';

    const updateAdvertFile = (fileName, fileType) => {
        data.advert.fileName = fileName;
        data.advert.fileType = fileType;
    };

    /* Invoices List */
    const invoicesPagination = reactive({
        invoices: [],
        current_page: 1,
        per_page: 5,
        total: computed(() => data.advert.invoices.length)
    });

    const handlePageChange = (newPage = invoicesPagination.current_page) => {
        invoicesPagination.current_page = newPage;
        const offset = (invoicesPagination.current_page - 1) * invoicesPagination.per_page;
        invoicesPagination.invoices = data.advert.invoices.slice(offset, invoicesPagination.per_page + offset);
    };

    /* Advert Stats */
    const handleAdvertStatsChange = () => {
        if (!filter.advertId || filter.advertId === null || filter[filter.type] === null) {
			return;
		}

        isAdvertStatsFetching.value = true;

		adStore.getAdvertStats(filter.advertId, { format: filter.type, date: filter[filter.type] })
			.then((response) => {
				Object.assign(data.advert, response.data);
			})
            .finally(() => {
                isAdvertStatsFetching.value = false;
            })
    };
</script>

<style lang="scss" scoped>
    ::v-deep(.color-warning) {
        color: $--color-error;
    }

    ::v-deep(.color-success) {
        color: $--color-success;
    }

    .card__pagination {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }

    .card__url {
        text-decoration: none;
        color: $--color-primary;
    }

    .button__group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;

        .card__button {
            margin-left: 0px;
        }
    }

    @media screen and (max-width: $--breakpoint-medium-devices){
        .button__group {
            flex-direction: column;
            .card__button {
                width: 100%;
            }
        }
    }

    .card__button--fill {
        width: 100%;
    }

    .tab-date-picker {
        display: flex;
        justify-content: center;
    }

    .date-picker {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    @media screen and (max-width: $--breakpoint-mobile) {
        .tab-date-picker {
            display: block;
        }
    }

    .btn-spacing {
        margin-left: 10px;
    }

</style>