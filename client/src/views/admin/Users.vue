<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">

            <!-- Spis użytkowników -->

            <el-col :span="24" :md="16">
                <el-card class="card">
                    <div class="card__title">Lista użytkowników</div>
                    <el-select
                        class="card__select"
                        filterable
                        @change="handleUserChange"
                        v-model="searchFilter.userId"
                        :loading="loaders.isUsersLoading"
                        loading-text="Wyszukiwanie użytkowników..."
                        placeholder="Wybierz użytkownika..."
                        no-data-text="Nie znaleziono żadnych użytkowników."
                        clearable
                    >
                        <el-option v-for="user in data.users" :key="user.id" :label="user.login" :value="user.id"></el-option>
                    </el-select>
                </el-card>
            </el-col>
            <el-col :span="24" :md="8">
                <el-card class="card">
                    <div class="card__title">Operacje</div>
                    <el-button @click="toggleModal('create')" class="card__button card__button--fill" type="primary" plain>Utwórz nowego użytkownika</el-button>
                </el-card>
            </el-col>
        </el-row>

         <!-- Szczegóły użytkownika -->

        <div v-if="Object.keys(data.user).length > 0">
            <el-row class="cards__container" :gutter="32">
                <el-col :span="24" :md="16">
                    <el-card class="card">
                        <div class="card__title">Konto użytkownika {{ data.user.login }}</div>
                        <el-descriptions :column="2">
                            <el-descriptions-item label="Adres email: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.email) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Nazwa firmy: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.name) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Osoba kontaktowa: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.representative) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Telefon kontaktowy: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.representativePhone) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Adres firmowy: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.address) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Kod pocztowy: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.postalCode) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Kraj: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.country) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="NIP: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.nip) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Firmowy adres email: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.companyEmail) }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Firmowy telefon: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.companyPhone) }}
                            </el-descriptions-item>
                        </el-descriptions>
                    </el-card>
                </el-col>

                <el-col :span="24" :md="8">
                    <el-card class="card">
                        <div class="card__title">Informacje o użytkowniku</div>
                        <el-descriptions :column="1">
                            <el-descriptions-item label="Identyfikator użytkownika: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ data.user.id }} | {{ data.user.userRole }}
                            </el-descriptions-item>
                            <el-descriptions-item label="Data utworzenia konta: " label-class-name="card__data-label" class-name="card__data-line">
                                {{ stringToLocale(data.user.createdAt) }}
                            </el-descriptions-item>
                            <div v-if="data.user.activatedAt">
                                <el-descriptions-item label="Data aktywacji konta: " label-class-name="card__data-label" class-name="card__data-line">
                                    {{ stringToLocale(data.user.activatedAt) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Ostatnio aktywny: " label-class-name="card__data-label" class-name="card__data-line">
                                    {{ stringToLocale(data.user.recentlyActiveAt) }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Czy zablokowany: " label-class-name="card__data-label" class-name="card__data-line">
                                    {{ data.user.isBanned ? 'Tak' : 'Nie' }}
                                </el-descriptions-item>
                                <el-descriptions-item label="Powód blokady: " label-class-name="card__data-label" class-name="card__data-line" v-if="data.user.isBanned">
                                    {{ stringToLocale(data.user.banReason) }}
                                </el-descriptions-item>
                            </div>
                            <div v-else>
                                <el-descriptions-item label="Status konta: " label-class-name="card__data-label" class-name="card__data-line">
                                    Konto nieaktywowane
                                </el-descriptions-item>
                                <el-descriptions-item label="Klucz aktywacyjny: " label-class-name="card__data-label" class-name="card__data-line">
                                    {{ stringToLocale(data.user.activationKey) }}
                                </el-descriptions-item>
                            </div>
                        </el-descriptions>
                    </el-card>
                </el-col>
            </el-row>

            <el-row class="cards__container" :gutter="32">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Operacje na użytkowniku</div>
                        <div class="button__group">
                            <el-button @click="toggleModal('edit')" class="card__button" type="primary" plain>Edytuj dane użytkownika</el-button>
                            <el-button @click="toggleModal('password')" class="card__button" type="primary" plain>Zmień hasło użytkownika</el-button>
                            <el-button v-if="!data.user.activationKey && !data.user.activatedAt" @click="toggleModal('activation')" class="card__button" type="primary" plain>Wygeneruj klucz aktywacyjny</el-button>
                            <el-button @click="toggleModal('ban')" class="card__button" type="primary" plain>{{ data.user.isBanned == true ? 'Odblokuj użytkownika' : 'Zablokuj użytkownika' }}</el-button>
                        </div>
                    </el-card>
                </el-col>
            </el-row>

             <!-- Reklamy -->

            <el-row class="cards__container" :gutter="32">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Reklamy użytkownika</div>
                        <el-row>
                            <el-col :span="12" :md="6">
                                <el-statistic title="Ilość reklam" :value="5" />
                            </el-col>
                            <el-col :span="12" :md="6">
                                <el-statistic title="Aktywne reklamy" :value="2" />
                            </el-col>
                            <el-col :span="12" :md="6">
                                <el-statistic title="Opłacone faktury" :value="2" />
                            </el-col>
                            <el-col :span="12" :md="6">
                                <el-statistic title="Nieopłacone faktury" :value="1" />
                            </el-col>
                        </el-row>
                        <div class="card__title">Lista reklam</div>
                        <el-row>
                            <el-col :span="24">
                                <el-select
                                    class="card__select"
                                    filterable
                                    @change="handleUserChange"
                                    v-model="searchFilter.adId"
                                    loading-text="Wyszukiwanie reklam..."
                                    placeholder="Wybierz reklamę..."
                                    no-data-text="Nie znaleziono żadnych reklam."
                                    clearable
                                >
                                    <el-option v-for="ad in data.user.ads" :key="ad.id" :label="ad.name" :value="ad.id"></el-option>
                                </el-select>
                            </el-col>
                            <el-col v-if="searchFilter.adId" :span="24">
                                <el-button class="card__button card__button--fill" type="primary" plain>Przejdź do szczegółów reklamy</el-button>
                            </el-col>
                        </el-row>
                    </el-card>
                </el-col>
            </el-row>

            <!-- Dziennik Zdarzeń -->

            <el-row class="cards__container" :gutter="32">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Dziennik zdarzeń użytkownika</div>
                        <el-table @row-click="editLog" :data="data.logs.entries" stripe style="width: 100%" v-loading="loaders.isLogsLoading">
                            <el-table-column prop="id" label="ID" width="50"/>
                            <el-table-column prop="createdAt" label="Data" width="200"/>
                            <el-table-column label="Typ" min-width="200">
                                <template #default="scope">
                                    <el-popover effect="light" trigger="hover" placement="top" width="auto">
                                        <template #default>
                                            <div>Notatki administratora: {{ stringToLocale(scope.row.notes) }}</div>
                                        </template>
                                        <template #reference>
                                            <el-tag>{{ scope.row.operationTags }}</el-tag>
                                        </template>
                                    </el-popover>
                                </template>
                            </el-table-column>
                            <el-table-column prop="message" label="Opis" min-width="300" width="auto"/>
                        </el-table>
                        <el-pagination
                            v-if="!isObjectEmpty(data.logs)"
                            class="card__pagination"
                            :current-page="data.logs.current_page"
                            :page-size="data.logs.per_page"
                            :total="data.logs.total"
                            :small="true"
                            :disabled="false"
                            :background="true"
                            layout="prev, pager, next, jumper"
                            @current-change="handlePageChange"
                        />
                    </el-card>
                </el-col>
            </el-row>
        </div>

        <!-- Modale -->

        <ModalUserCreate v-if="modals.currentModal === 'create'" :user="data.user" @add="addUser" @close="toggleModal"/>
        <ModalUserEdit v-if="modals.currentModal === 'edit'" :user="data.user" @update="updateUser" @close="toggleModal"/>
        <ModalUserPassword v-if="modals.currentModal === 'password'" :user="data.user" @close="toggleModal"/>
        <ModalUserActivationKey v-if="modals.currentModal === 'activation'" :user="data.user" @update="updateUser" @close="toggleModal"/>
        <ModalUserBan v-if="modals.currentModal === 'ban'" :user="data.user" @update="updateUser" @close="toggleModal"/>

        <ModalLogEdit v-if="modals.currentModal === 'log'" :log="data.log" @update="updateLog" @close="toggleModal"/>
    </el-main>
</template>

<script setup>
    import { reactive, onMounted } from 'vue'

    import { isObjectEmpty, stringToLocale } from '@/common/helpers/utility.helper'

    import { useAdminStore } from '@/stores/AdminStore';

    import ModalUserActivationKey from '@/modules/components/admin/ModalUserActivationKey.vue';
    import ModalUserBan from '@/modules/components/admin/ModalUserBan.vue';
    import ModalUserPassword from '@/modules/components/admin/ModalUserPassword.vue';
    import ModalUserEdit from '@/modules/components/admin/ModalUserEdit.vue';
    import ModalUserCreate from '@/modules/components/admin/ModalUserCreate.vue';
    import ModalLogEdit from '@/modules/components/admin/ModalLogEdit.vue';

    const adminStore = useAdminStore();

    const loaders = reactive({
        isUsersLoading: false,
        isLogsLoading: false,
    });

    /* Modals */

    const modals = reactive({
        currentModal: ''
    });

    const toggleModal = (modal = '') => {
        modals.currentModal = modal;
    };

    /* Main Data */

    const data = reactive({
		users: {},
        user: {},
        logs: {},
        log: {}
	});

    onMounted(() => {
        loaders.isUsersLoading = true;
        adminStore.getUsers()
            .then((response) => {
                data.users = response.data;
            })
            .finally(() => {
                loaders.isUsersLoading = false;
            })
	});

    /* Users List */

    const searchFilter = reactive({
		userId: null,
        adId: null,
	});

    const handleUserChange = () => {
		if (!searchFilter.userId) data.user = {};
		else {
            data.user = data.users.find((user) => user.id === searchFilter.userId);
            data.logs = {};
            loaders.isLogsLoading = true;
            adminStore.getUserLogs(data.user.id)
                .then((response) => {
                    data.logs = response.data;
                })
                .finally(() => {
                    loaders.isLogsLoading = false;
                })
        }
	};

    /* User Details */

    const updateUser = (newData) => {
        Object.assign(data.user, newData);
    };

    const addUser = (newData) => {
        data.users.push(newData);
        data.user = newData;
    };

    /* Logs */

    const handlePageChange = (newPage) => {
        loaders.isLogsLoading = true;
        adminStore.getUserLogs(data.user.id, newPage)
                .then((response) => {
                    data.logs = response.data;
                })
                .finally(() => {
                    loaders.isLogsLoading = false;
                })
    };

    const editLog = (log) => {
        data.log = log;
        toggleModal('log');
    };

    const updateLog = (newData) => {
        let log = data.logs.entries.find((log) => log.id === newData.id);
        Object.assign(log, newData);
    };
</script>

<style lang="scss" scoped>

    @media screen and (max-width: $--breakpoint-medium-devices){
        .button__group {
            gap: 10px;
            display: flex;
            flex-direction: column;
            .card__button {
                width: 100%;
                margin-left: 0px;
            }
        }
    }

    .card__button--fill {
        width: 100%;
    }

    .card__pagination {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
</style>
