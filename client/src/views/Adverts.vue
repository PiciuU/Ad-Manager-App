<!-- eslint-disable vue/multi-word-component-names -->
<template>
    <!-- <el-main v-loading="componentState.isFetching"> -->
    <el-main>
        <el-row class="cards__container cards__container--ordered" :gutter="32">
            <el-col :span="24" :md="12">
                <el-card class="card">
                    <div class="card__title">Informacje o reklamie</div>
                    <el-empty v-if="isObjectEmpty(data.advert)" description="Brak reklamy"></el-empty>
                    <div v-else>
                        <!-- <div v-else> -->
                        <el-descriptions class="el-descriptions-two-column" :column="2">
                            <el-descriptions-item
                                label="Nazwa reklamy: "
                                label-class-name="card__data-label"
                                class-name="card__data-line"
                            >
                            Placeholder dla nazwy
                            </el-descriptions-item>
                            <el-descriptions-item
                                label="Typ reklamy: "
                                label-class-name="card__data-label"
                                class-name="card__data-line"
                            >
                            Placeholder dla typu
                            </el-descriptions-item>
                            <el-descriptions-item
                                label="Status reklamy: "
                                label-class-name="card__data-label"
                                class-name="card__data-line"
                            >
                            Placeholder dla statusu
                            </el-descriptions-item>
                            <el-descriptions-item
                                label="Kolaudacja: "
                                label-class-name="card__data-label"
                                class-name="card__data-line"
                                ><a class="card__data-link">Podgląd</a></el-descriptions-item
                            >
                            <el-descriptions-item
                                label="Wyświetlana od: "
                                label-class-name="card__data-label"
                                class-name="card__data-line"
                            >
                            Placeholder dla daty początek
                            </el-descriptions-item>
                            <el-descriptions-item
                                label="Wyświetlana do: "
                                label-class-name="card__data-label"
                                class-name="card__data-line"
                            >
                            Placeholder dla daty koniec
                            </el-descriptions-item>
                        </el-descriptions>
                    </div>
                </el-card>
            </el-col>
            <el-col :span="24" :md="12">
                <el-card class="card">
                    <div class="card__title">Lista reklam oraz filtry statystyk</div>
                    <el-select
                        class="card__select"
                        @change="handleAdvertChange"
                        v-model="searchFilter.advertId"
                        loading-text="Wyszukiwanie reklam..."
                        placeholder="Wybierz reklamę..."
                        no-data-text="Nie znaleziono żadnych reklam."
                        clearable
                    >
                        <el-option v-for="ad in data.ads" :key="ad.id" :label="ad.name" :value="ad.id"></el-option>
                    </el-select>

                    <el-tabs class="card__tabs" v-model="searchFilter.type">
                        <el-tab-pane label="Tydzień" name="week">
                            <div class="date-picker">
                                <el-date-picker
                                    v-model="searchFilter.week"
                                    type="week"
                                    placeholder="Wybierz tydzień"
                                    format="[Tydzień] ww"
                                    value-format="YYYY-MM-DD"
                                    :disabled="!searchFilter.advertId"
                                    :clearable="false"
                                    @change="handleAdvertChange"
                                >
                                </el-date-picker>
                                <el-button class="btn-spacing" @click="handleAdvertChange" :disabled="!searchFilter.advertId">
                                    <font-awesome-icon icon="search"/>
                                </el-button>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Miesiąc" name="month">
                            <div class="date-picker">
                                <el-date-picker
                                    v-model="searchFilter.month"
                                    type="month"
                                    placeholder="Wybierz miesiąc"
                                    format="YYYY/MM"
                                    value-format="YYYY-MM"
                                    :disabled="!searchFilter.advertId"
                                    :clearable="false"
                                    @change="handleAdvertChange"
                                >
                                </el-date-picker>
                                <el-button class="btn-spacing" @click="handleAdvertChange" :disabled="!searchFilter.advertId">
                                    <font-awesome-icon icon="search"/>
                                </el-button>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Rok" name="year">
                            <div class="date-picker">
                                <el-date-picker
                                    v-model="searchFilter.year"
                                    type="year"
                                    placeholder="Wybierz rok"
                                    format="YYYY"
                                    value-format="YYYY"
                                    :disabled="!searchFilter.advertId"
                                    :clearable="false"
                                    @change="handleAdvertChange"
                                >
                                </el-date-picker>
                                <el-button class="btn-spacing" @click="handleAdvertChange" :disabled="!searchFilter.advertId">
                                    <font-awesome-icon icon="search"/>
                                </el-button>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Zakres dat" name="monthrange">
                            <div class="date-picker">
                                <el-date-picker
                                    v-model="searchFilter.monthrange"
                                    type="monthrange"
                                    unlink-panels
                                    range-separator="-"
                                    start-placeholder="Data początkowa"
                                    end-placeholder="Data końcowa"
                                    format="YYYY/MM"
                                    value-format="YYYY-MM"
                                    :shortcuts="searchFilter.shortcuts"
									:disabled="!searchFilter.advertId"
                                    :clearable="false"
                                    @change="handleAdvertChange"
                                >
                                </el-date-picker>
                                <el-button class="btn-spacing" @click="handleAdvertChange" :disabled="!searchFilter.advertId">
                                    <font-awesome-icon icon="search"/>
                                </el-button>
                            </div>
                        </el-tab-pane>
                    </el-tabs>
                </el-card>
            </el-col>
        </el-row>

        <el-row class="cards__container" :gutter="32" v-if="!isObjectEmpty(data.advert)">
            <el-col :span="24">
                <el-card class="card">
                    <div class="card__title">Wyświetlenia</div>
                    <el-tabs v-model="searchFilter.graphs.currentClicksTab">
                        <el-tab-pane label="Wykres liniowy" name="line">
                            <line-chart
                                download="Wyswietlenia_liniowy"
                                :data="{
                                    '2017-01-01': 82,
                                    '2017-02-01': 160,
                                    '2017-03-01': 133,
                                    '2017-04-01': 460,
                                    '2017-05-01': 320,
                                    '2017-06-01': 673,
                                    '2017-07-01': 970,
                                    '2017-08-01': 1900,
                                    '2017-09-01': 2150,
                                    '2017-10-01': 2004,
                                    '2017-11-01': 3402,
                                    '2017-11-01': 4421
                                }"
                                :discrete="true"
                                empty="Brak danych"
                            ></line-chart>
                        </el-tab-pane>
                        <el-tab-pane label="Wykres kolumnowy" name="column">
                            <column-chart
                                download="Wyswietlenia_kolumnowy"
                                :data="{
                                    '2017-01-01': 82,
                                    '2017-02-01': 160,
                                    '2017-03-01': 133,
                                    '2017-04-01': 460,
                                    '2017-05-01': 320,
                                    '2017-06-01': 673,
                                    '2017-07-01': 970,
                                    '2017-08-01': 1900,
                                    '2017-09-01': 2150,
                                    '2017-10-01': 2004,
                                    '2017-11-01': 3402,
                                    '2017-11-01': 4421
                                }"
                                :discrete="true"
                                empty="Brak danych"
                            ></column-chart>
                        </el-tab-pane>

                        <el-tab-pane label="Wykres słupkowy" name="bar">
                            <bar-chart
                                download="Wyswietlenia_slupkowy"
                                :data="{
                                    '2017-01-01': 82,
                                    '2017-02-01': 160,
                                    '2017-03-01': 133,
                                    '2017-04-01': 460,
                                    '2017-05-01': 320,
                                    '2017-06-01': 673,
                                    '2017-07-01': 970,
                                    '2017-08-01': 1900,
                                    '2017-09-01': 2150,
                                    '2017-10-01': 2004,
                                    '2017-11-01': 3402,
                                    '2017-11-01': 4421
                                }"
                                :discrete="true"
                                empty="Brak danych"
                            ></bar-chart>
                        </el-tab-pane>
                        <el-tab-pane label="Dane ogólne" name="details">
                            <el-descriptions :column="1">
                                <el-descriptions-item
                                    label="Wszystkie wyświetlenia: "
                                    label-class-name="card__data-label"
                                    class-name="card__data-line"
                                >
                                    19157
                                    <!-- {{ data.summary.all_views }} -->
                                </el-descriptions-item>
                                <el-descriptions-item
                                    label="Najwięcej wyświetleń w: "
                                    label-class-name="card__data-label"
                                    class-name="card__data-line"
                                >
                                    Listopad
                                    <!-- {{ data.summary.most_views.date }} -->
                                </el-descriptions-item>
                                <el-descriptions-item
                                    label="Najmniej wyświetleń w: "
                                    label-class-name="card__data-label"
                                    class-name="card__data-line"
                                >
                                    Styczeń
                                    <!-- {{ data.summary.least_views.date }} -->
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-tab-pane>
                    </el-tabs>
                </el-card>
            </el-col>
        </el-row>

        <el-row class="cards__container" :gutter="32" v-if="!isObjectEmpty(data.advert)">
            <el-col :span="24">
                <el-card class="card">
                    <div class="card__title">Kliknięcia</div>
                    <el-tabs v-model="searchFilter.graphs.currentClicksTab">
                        <el-tab-pane label="Wykres liniowy" name="line">
                            <line-chart
                                download="Klikniecia_liniowy"
                                :discrete="true"
                                empty="Brak danych"
                                :data="{
                                    '2017-01-01': 82,
                                    '2017-02-01': 160,
                                    '2017-03-01': 133,
                                    '2017-04-01': 460,
                                    '2017-05-01': 320,
                                    '2017-06-01': 673,
                                    '2017-07-01': 970,
                                    '2017-08-01': 1900,
                                    '2017-09-01': 2150,
                                    '2017-10-01': 2004,
                                    '2017-11-01': 3402,
                                    '2017-11-01': 4421
                                }"
                            ></line-chart>
                        </el-tab-pane>
                        <el-tab-pane label="Wykres kolumnowy" name="column">
                            <column-chart
                                download="Klikniecia_kolumnowy"
                                :discrete="true"
                                empty="Brak danych"
                                :data="{
                                    '2017-01-01': 82,
                                    '2017-02-01': 160,
                                    '2017-03-01': 133,
                                    '2017-04-01': 460,
                                    '2017-05-01': 320,
                                    '2017-06-01': 673,
                                    '2017-07-01': 970,
                                    '2017-08-01': 1900,
                                    '2017-09-01': 2150,
                                    '2017-10-01': 2004,
                                    '2017-11-01': 3402,
                                    '2017-11-01': 4421
                                }"
                            ></column-chart>
                        </el-tab-pane>
                        <el-tab-pane label="Wykres słupkowy" name="bar">
                            <bar-chart
                                download="Klikniecia_slupkowy"
                                :discrete="true"
                                empty="Brak danych"
                                :data="{
                                    '2017-01-01': 82,
                                    '2017-02-01': 160,
                                    '2017-03-01': 133,
                                    '2017-04-01': 460,
                                    '2017-05-01': 320,
                                    '2017-06-01': 673,
                                    '2017-07-01': 970,
                                    '2017-08-01': 1900,
                                    '2017-09-01': 2150,
                                    '2017-10-01': 2004,
                                    '2017-11-01': 3402,
                                    '2017-11-01': 4421
                                }"
                            ></bar-chart>
                        </el-tab-pane>
                        <el-tab-pane label="Dane ogólne" name="details">
                            <el-descriptions :column="1">
                                <el-descriptions-item
                                    label="Wszystkie kliknięcia: "
                                    label-class-name="card__data-label"
                                    class-name="card__data-line"
                                >
                                    19157
                                    <!-- {{ data.summary.all_clicks }} -->
                                </el-descriptions-item>
                                <el-descriptions-item
                                    label="Najwięcej kliknięć w: "
                                    label-class-name="card__data-label"
                                    class-name="card__data-line"
                                >
                                    marzec
                                    <!-- {{ data.summary.most_clicks.date }} -->
                                </el-descriptions-item>
                                <el-descriptions-item
                                    label="Najmniej kliknięć w: "
                                    label-class-name="card__data-label"
                                    class-name="card__data-line"
                                >
                                    grudzien
                                    <!-- {{ data.summary.least_clicks.date }} -->
                                </el-descriptions-item>
                            </el-descriptions>
                        </el-tab-pane>
                    </el-tabs>
                </el-card>
            </el-col>
        </el-row>
    </el-main>
</template>

<script setup>
    import { reactive, onMounted } from 'vue';

    import DateHelper from '@/common/helpers/date.helper';
    import { advertStatusToLocaleString, advertTypeToLocaleString, stringToLocale, isObjectEmpty } from '@/common/helpers/utility.helper';

		// onMounted(() => {
        //     adStore.getAdverts()
        //         .then((response) => {
        //             data.ads = response.data
        //         })
		// });

		const data = reactive({
			ads: {},
			advert: {},
			views: {},
			clicks: {},
			summary: {
				most_views: {},
				least_views: {},
				most_clicks: {},
				least_clicks: {}
			}
		});

		const searchFilter = reactive({
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

		function handleAdvertChange() {
			if (!searchFilter.advertId || searchFilter.advertId === null || searchFilter[searchFilter.type] === null) {
				data.advert = {};
				return;
			}

			const body = {
				format: searchFilter.type,
				date: searchFilter[searchFilter.type]
			};

            // adStore.getAdvert(searchFilter.advertId, body)
            //     .then((response) => {
            //         Object.assign(data, response.data);
            //     })
		}

</script>

<style lang="scss" scoped>
.date-picker {
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.btn-spacing {
    margin-left: 10px;
}
</style>
