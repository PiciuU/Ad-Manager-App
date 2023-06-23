<template>
    <el-main>
        <el-row class="cards__container">
            <el-col>
                <el-card class="card">
                    <div class="card__title">Lista powiadomień</div>
                    <el-table :data="notifications.entries" stripe v-loading="authStore.isLoading" :row-class-name="isNotificationSeen">
                        <el-table-column prop="date" label="Data" width="180"/>
                        <el-table-column prop="title" label="Tytuł" min-width="200"/>
                        <el-table-column prop="description" label="Szczegóły" min-width="300"/>
                        <el-table-column label="Operacje" width="100">
                            <template #default="scope">
                                <font-awesome-icon @click="changeSeenStatus(scope.row)" class="notification__icon" icon="eye" title="Zaznacz jako odczytana" />
                                <font-awesome-icon v-if="scope.row.advertId" @click="changeSeenStatus(scope.row)" class="notification__icon" icon="link" title="Wyświetl reklamę" />
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
    import { reactive, onMounted } from 'vue'

    import { isObjectEmpty } from '@/common/helpers/utility.helper'

    import { useAuthStore } from '@/stores/AuthStore';

    const authStore = useAuthStore();

    onMounted(() => {
        authStore.fetchNotifications(1)
            .then((response) => {
                notifications = response.data;
            })
    });

    const notifications = reactive({
        current_page: 1,
        per_page: 10,
        total: 20,
        entries: [
            {
                id: 1,
                advertId: 1,
                date: "2023-06-22 00:06:27",
                title: "Nieopłacona faktura za reklamę",
                description: "Masz nieopłaconą fakturę (INV-05404520) za reklamę \"Rerum repudiandae laudantium et dolorem hic.\".",
                isSeen: 0
            },
            {
                id: 2,
                date: "2023-06-21 09:17:32",
                title: "Zakończenie publikacji reklamy",
                description: "Twoja reklama \"Beatae ut adipisci aut aliquid accusantium.\" zakończyła swoją publikację.",
                isSeen: 1
            },
            {
                id: 3,
                date: "2023-06-19 22:12:55",
                title: "Nieopłacona faktura za reklamę",
                description: "Masz nieopłaconą fakturę (INV-05404520) za reklamę \"Recusandae alias harum consequuntur neque vel assumenda.\".",
                isSeen: 1
            },
        ]
    });

    const changeSeenStatus = (notification) => {
        authStore.changeNotificationStatus(notification.id, { isSeen: !notification.isSeen })
            .then(() => {
                notification.isSeen = !notification.isSeen;
            })
    };

    const isNotificationSeen = (data) => {
        if (data.row.isSeen == 0) return 'notification__unseen';
        return '';
    };

    const handlePageChange = (newPage) => {
        authStore.fetchNotifications(newPage)
            .then((response) => {
                notifications = response.data;
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

    ::v-deep(.notification__unseen) {
        color: $--color-primary;
    }
</style>