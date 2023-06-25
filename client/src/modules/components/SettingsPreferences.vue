<template>
    <div class="container">
        <div class="container__item">
            <div class="container__title">Widoczność menu bocznego</div>
            <div class="container__description">
                Jeżeli chcesz, aby na tym urządzeniu menu boczne domyślnie było wyświetlane w wersji
                zwiniętej, możesz skorzystać z poniższej opcji.
            </div>

            <el-switch
                v-model="preferences.sidebarCollapse"
                @change="dataStore.sidebarVisibilityPreference(preferences.sidebarCollapse)"
                active-text="Zwinięta"
                inactive-text="Domyślna"
            ></el-switch>
        </div>
    </div>
</template>

<script setup>
    import { reactive } from 'vue';
    import { useDataStore } from '@/stores/DataStore.js/';

    const dataStore = useDataStore();

    const preferences = reactive({
        sidebarCollapse: dataStore.isSidebarCollapsedPreference
    });
</script>

<style scoped lang="scss">
    .el-switch {
        margin-top: 20px;
    }

    .container {
        display: flex;
        gap: 50px;

        &__item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 50%;
            margin: 10px 0px;
            padding: 0px 10px;
            text-align: left;
        }

        &__title {
            position: relative;
            font-size: 1.6rem;
            color: $--color-text;
            font-weight: bold;
            margin-bottom: 20px;
        }

        &__description {
            color: $--color-text;
            font-size: 1.4rem;
        }
    }

    @media screen and (max-width: 1000px) {
        .container {
            flex-direction: column;
            gap: 40px;

            &__item {
                width: 100%;
            }
        }
    }

    @media screen and (max-width: $--breakpoint-mobile) {
        .container__item {
            padding: 0px;
        }

        .container__title {
            margin-bottom: 10px;
        }

        .container__button {
            width: 100%;
        }
    }
</style>
