<template>
    <el-main>
        <el-row class="cards__container">
            <el-col>
                <el-card class="card">
                    <div class="card__title">Lista powiadomień</div>
                    <el-table :data="notifications.entries" stripe v-loading="isNotificationsFetching" :row-class-name="isNotificationSeen">
                        <el-table-column prop="date" label="Data" width="180"/>
                        <el-table-column prop="title" label="Tytuł" min-width="200"/>
                        <el-table-column prop="description" label="Szczegóły" min-width="300"/>
                        <el-table-column label="Operacje" width="100">
                            <template #default="scope">
                                <font-awesome-icon @click="changeSeenStatus(scope.row)" class="notification__icon" icon="eye" title="Zaznacz jako odczytana" />
                                <router-link v-if="scope.row.adId" :to="{ name: 'Ads', params: { id: scope.row.adId }}">
                                    <font-awesome-icon class="notification__icon" icon="link" title="Wyświetl reklamę" />
                                </router-link>
                            </template>
                        </el-table-column>
                    </el-table>
                    <el-pagination
                        v-if="!isObjectEmpty(notifications)"
                        class="notification__pagination"
                        :current-page="notifications.current_page"
                        :page-size="notifications.per_page"
                        :total="notifications.total"
                        :small="true"
                        :disabled="false"
                        :background="true"
                        layout="prev, pager, next, jumper"
                        @current-change="handlePageChange"
                    />
                </el-card>
            </el-col>
        </el-row>
    </el-main>
</template>


<script setup>
    import { ref, onMounted } from 'vue'

    import { isObjectEmpty } from '@/common/helpers/utility.helper'

    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    const isNotificationsFetching = ref(false);
    const notifications = ref('');

    onMounted(() => {
        handlePageChange(1);
    });

    const changeSeenStatus = async (notification) => {
        await authStore.changeNotificationStatus(notification.id)
            .then((response) => {
                notification.isSeen = response.data.isSeen;
            })

        if (authStore.getUser.hasUnseenNotification == true && !notifications.value.entries.some(notification => notification.isSeen == 0)) {
            authStore.user.hasUnseenNotification = false;
        }
    };

    const isNotificationSeen = (data) => {
        if (data.row.isSeen == 0) return 'notification__unseen';
        return '';
    };

    const handlePageChange = (newPage) => {
        isNotificationsFetching.value = true;
        authStore.fetchNotifications(newPage)
            .then((response) => {
                notifications.value = response.data;
            })
            .finally(() => {
                isNotificationsFetching.value = false;
            })
    };
</script>

<style lang="scss" scoped>
    .notification__icon {
        margin: 0px 5px;
        cursor: pointer;
        transition: color .15s ease-in-out;

        &:hover {
            transition: color .15s ease-in-out;
            color: $--color-text-muted-3;
        }
    }

    .notification__pagination {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }

    a {
        color: $--color-text;
        text-decoration: none;
    }

    ::v-deep(.notification__unseen) {
        color: $--color-primary;

        a {
            color: $--color-primary;
            text-decoration: none;
        }
    }
</style>