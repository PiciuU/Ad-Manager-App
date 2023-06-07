<!-- eslint-disable vue/multi-word-component-names -->
<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="6" class="el-hidden-md-and-down">
                <el-card class="card card--has-hint">
                    <el-popover
                        placement="right-start"
                        :width="220"
                        trigger="hover"
                        content="Współczynnik CTR (klikalności) to odsetek wyświetleń reklamy, które doprowadziły do kliknięcia."
                    >
                        <template #reference>
                            <div class="card__hint"><font-awesome-icon icon="question" /></div>
                        </template>
                    </el-popover>

                    <div class="card__title">Współczynnik CTR (ostatni miesiąc)</div>
                </el-card>
            </el-col>
            <el-col :span="24" :lg="18">
                <el-card class="card">
                    <div class="card__title">Statystyki</div>
                    <div class="card__body">
                        <div class="card__item">
                            <font-awesome-icon class="nav__icon" icon="chart-bar" color="white" />
                        </div>
                    </div>

                    <div class="card__details"></div>
                </el-card>
            </el-col>
        </el-row>

        <el-row class="cards__container" :gutter="32">
            <el-col :span="24" :lg="12">
                <el-card class="card cardstats">
                    <div class="card__title">Wszystkie wyświetlenia (ostatni rok)</div>
                    <line-chart
                        class="wykreschart"
                        :data="{
                            '2017-05-13': 50,
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
                </el-card>
            </el-col>
            <el-col :span="24" :lg="12">
                <el-card class="card">
                    <div class="card__title">Wszystkie kliknięcia (ostatni rok)</div>
                    <line-chart
                        class="wykreschart"
                        :data="{
                            '2017-05-13': 20,
                            '2017-01-01': 42,
                            '2017-02-01': 63,
                            '2017-03-01': 65,
                            '2017-04-01': 233,
                            '2017-05-01': 171,
                            '2017-06-01': 321,
                            '2017-07-01': 466,
                            '2017-08-01': 992,
                            '2017-09-01': 1130,
                            '2017-10-01': 1000,
                            '2017-11-01': 1294,
                            '2017-11-01': 2054
                        }"
                    ></line-chart>
                </el-card>
            </el-col>
        </el-row>
    </el-main>
</template>

<script></script>

<style lang="scss" scoped>
@media screen and (max-width: 1000px) {
    .card {
        &__body {
            grid-template-columns: auto auto;
        }

        &__item {
            &:nth-child(-n + 2) {
                margin-bottom: 20px;
            }
        }
    }
}

@media screen and (max-width: var(--breakpoint-small-devices)) {
    .card {
        &__body {
            grid-template-columns: auto;
        }

        &__item {
            margin-bottom: 20px;
        }
    }
}

@media screen and (max-width: var(--breakpoint-mobile)) {
    .cards__container {
        padding: 0px;
    }
}
.card {
    position: relative;
}
.wykreschart {
    position: relative;
    top: -200px;
}
</style>
