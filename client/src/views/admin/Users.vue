<template>
    <el-main>
        <!-- Lista użytkowników -->

        <el-row class="cards__container" :gutter="32">
            <el-col :span="24" :md="16">
                <el-card class="card">
                    <div class="card__title">Lista użytkowników</div>
                    <el-select
                        class="card__select"
                        filterable
                        @change="handleUserChange"
                        v-model="filter.userId"
                        :loading="loaders.isUsersFetching"
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

        <div v-if="filter.userId && filter.userId != null">
            <el-row class="cards__container" :gutter="32">
                <el-col :span="24" :md="16">
                    <el-card class="card">
                        <div class="card__title">Konto użytkownika {{ data.user.login }} (ID: {{ data.user.id ?? '...'}})</div>
                        <div v-if="data.user && !loaders.isUserFetching">
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
                        </div>
                        <el-skeleton :rows="5" animated v-else />
                    </el-card>
                </el-col>

                <el-col :span="24" :md="8">
                    <el-card class="card">
                        <div class="card__title">Informacje o użytkowniku</div>
                        <div v-if="data.user && !loaders.isUserFetching">
                            <el-descriptions :column="1">
                                <el-descriptions-item label="Identyfikator użytkownika: " label-class-name="card__data-label" class-name="card__data-line">
                                    {{ data.user.id }} | {{ data.user.userRole == 'User' ? 'Użytkownik' : 'Administrator' }}
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
                        </div>
                        <el-skeleton :rows="5" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <!-- Operacje -->
            <el-row class="cards__container" :gutter="32">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Operacje na użytkowniku</div>
                        <div v-if="data.user && !loaders.isUserFetching">
                            <div class="button__group">
                                <el-button @click="toggleModal('edit')" class="card__button" type="primary" plain>Edytuj dane użytkownika</el-button>
                                <el-button @click="toggleModal('password')" class="card__button" type="primary" plain>Zmień hasło użytkownika</el-button>
                                <el-button v-if="!data.user.activationKey && !data.user.activatedAt" @click="toggleModal('activation')" class="card__button" type="primary" plain>Wygeneruj klucz aktywacyjny</el-button>
                                <el-button @click="toggleModal('ban')" class="card__button" type="primary" plain>{{ data.user.isBanned == true ? 'Odblokuj użytkownika' : 'Zablokuj użytkownika' }}</el-button>
                                <el-button @click="toggleModal('notification')" class="card__button" type="primary" plain>Wyślij powiadomienie</el-button>
                            </div>
                        </div>
                        <el-skeleton :rows="1" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

             <!-- Reklamy -->
            <el-row class="cards__container" :gutter="32">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Reklamy użytkownika</div>
                        <div v-if="data.user?.adverts">
                            <el-select
                                class="card__select"
                                filterable
                                v-model="filter.advertId"
                                loading-text="Wyszukiwanie reklam..."
                                placeholder="Wybierz reklamę..."
                                no-data-text="Nie znaleziono żadnych reklam."
                                clearable
                            >
                                <el-option v-for="advert in data.user.adverts" :key="advert.id" :label="advert.name" :value="advert.id"></el-option>
                            </el-select>
                            <router-link v-if="filter.advertId" :to="{ name: 'AdminAds', params: { id: filter.advertId }}">
                                <el-button class="card__button card__button--fill card__button-margin" type="primary" plain>Przejdź do szczegółów reklamy</el-button>
                            </router-link>
                        </div>
                        <el-skeleton :rows="1" animated v-else />
                    </el-card>
                </el-col>
            </el-row>

            <!-- Dziennik Zdarzeń -->
            <el-row class="cards__container" :gutter="32">
                <el-col :span="24">
                    <el-card class="card">
                        <div class="card__title">Dziennik zdarzeń użytkownika</div>
                        <div v-if="data.user?.logs">
                            <el-table @row-click="editLog" :data="data.user.logs.entries" stripe style="width: 100%" v-loading="loaders.isUserLogsFetching" class-name="make-clickable">
                                <el-table-column prop="id" label="ID" width="50"/>
                                <el-table-column prop="createdAt" label="Data" width="200"/>
                                <el-table-column label="Typ" min-width="200">
                                    <template #default="scope">
                                        <el-tag>{{ scope.row.operationTags }}</el-tag>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="message" label="Opis" min-width="300" width="auto"/>
                            </el-table>
                            <el-pagination
                                class="card__pagination"
                                :current-page="data.user.logs.current_page"
                                :page-size="data.user.logs.per_page"
                                :total="data.user.logs.total"
                                :small="true"
                                :disabled="false"
                                :background="true"
                                layout="prev, pager, next, jumper"
                                @current-change="fetchUserLogs"
                            />
                        </div>
                        <el-skeleton :rows="2" animated v-else />
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
        <ModalUserNotification v-if="modals.currentModal === 'notification'" :userId="data.user.id" @close="toggleModal"/>

        <ModalLogEdit v-if="modals.currentModal === 'log'" :log="data.user.log" @update="updateLog" @close="toggleModal"/>
    </el-main>
</template>

<script setup>
    import { reactive, onMounted, onUpdated } from 'vue'
    import { useRoute, useRouter } from 'vue-router';

    import { stringToLocale } from '@/common/helpers/utility.helper'

    import { useAdminStore } from '@/stores/AdminStore';

    import ModalUserActivationKey from '@/modules/components/admin/ModalUserActivationKey.vue';
    import ModalUserBan from '@/modules/components/admin/ModalUserBan.vue';
    import ModalUserPassword from '@/modules/components/admin/ModalUserPassword.vue';
    import ModalUserEdit from '@/modules/components/admin/ModalUserEdit.vue';
    import ModalUserCreate from '@/modules/components/admin/ModalUserCreate.vue';
    import ModalUserNotification from '@/modules/components/admin/ModalUserNotification.vue';

    import NotificationService from '@/services/notification.service';

    const route = useRoute();
    const router = useRouter();
    const adminStore = useAdminStore();

    const loaders = reactive({
        isUsersFetching: false,
        isUserFetching: false,
        isUserAdvertsFetching: false,
        isUserLogsFetching: false,
    });

    /* Main Data */

    const data = reactive({
		users: {},
        user: {},
        logs: {},
        log: {}
	});

    const filter = reactive({
		userId: null,
        advertId: null,
	});

    onMounted(() => {
        loaders.isUsersFetching = true;
        adminStore.fetchUsers()
            .then((response) => {
                data.users = response.data;
                if (route.params.id) {
                    filter.userId = parseInt(route.params.id);
                    handleUserChange();
                }
            })
            .catch(() => {
                NotificationService.displayMessage('error', 'Wystąpił nieoczekiwany błąd przy pobieraniu listy użytkowników, spróbuj ponownie później.');
            })
            .finally(() => {
                loaders.isUsersFetching = false;
            })
	});

    onUpdated(() => {
        if (route.params.id && filter.userId !== parseInt(route.params.id)) {
            filter.userId = parseInt(route.params.id);
            handleUserChange();
        }
    })

    /* Modals */
    const modals = reactive({
        currentModal: ''
    });

    const toggleModal = (modal = '') => {
        modals.currentModal = modal;
    };

    /* User */
    const handleUserChange = async () => {
        router.push({ name: 'AdminUsers', params: { id: filter.userId }});

        data.user = {};
        filter.advertId = null;

		if (!filter.userId || filter.userId === null) return;

        loaders.isUserFetching = true;

        let isErrorOccurred = false;
        let userResponseHandler = null;

        await adminStore.fetchUser(filter.userId)
            .then((response) => {
                userResponseHandler = response.data
            })
            .catch(() => {
                isErrorOccurred = true;
            })

        loaders.isUserFetching = false;

        if (isErrorOccurred) {
            NotificationService.displayMessage('error', 'Wystąpił błąd przy pobieraniu wybranego użytkownika, spróbuj ponownie później.');
            filter.userId = null;
            return;
        }

        data.user = userResponseHandler;

        fetchUserAdverts();
        fetchUserLogs(1);
	};

    /* User Details */
    const updateUser = (item) => {
        if (data.user.login != item.login) {
            const user = data.users.find((user) => user.login == data.user.login);
            user.login = item.login;
        }
        Object.assign(data.user, item);
    };

    const addUser = (item) => {
        data.users.push(item);
        data.user = item;
    };

    /* Adverts */
    const fetchUserAdverts = () => {
        loaders.isUserAdvertsFetching = true;
        adminStore.fetchUserAdverts(data.user.id)
                .then((response) => {
                    data.user.adverts = response.data;
                })
                .finally(() => {
                    loaders.isUserAdvertsFetching = false;
                })
    };

    /* Logs */
    import ModalLogEdit from '@/modules/components/admin/ModalLogEdit.vue';

    const fetchUserLogs = (page) => {
        loaders.isUserLogsFetching = true;
        adminStore.fetchUserLogs(data.user.id, page)
                .then((response) => {
                    data.user.logs = response.data;
                })
                .finally(() => {
                    loaders.isUserLogsFetching = false;
                })
    };

    const editLog = (log) => {
        data.user.log = log;
        toggleModal('log');
    };

    const updateLog = (item) => {
        let log = data.user.logs.entries.find((log) => log.id === item.id);
        Object.assign(log, item);
    };
</script>

<style lang="scss" scoped>

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

    .card__button-margin {
        margin-top: 20px;
    }

    .card__pagination {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }

    .make-clickable {
        cursor: pointer;
    }
</style>
