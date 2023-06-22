<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="24">
                <el-card class="card">
                    <div class="card__title">Dziennik zdarzeń użytkownika</div>
                    <el-table @row-click="editLog" :data="data.logs.entries" stripe style="width: 100%" v-loading="adminStore.isLoading">
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

        <!-- Modale -->

        <ModalLogEdit v-if="modals.currentModal === 'log'" :log="data.log" @update="updateLog" @close="toggleModal"/>
    </el-main>
</template>

<script setup>
    import { reactive, onMounted } from 'vue'

    import { isObjectEmpty, stringToLocale } from '@/common/helpers/utility.helper'

    import { useAdminStore } from '@/stores/AdminStore';

    import ModalLogEdit from '@/modules/components/admin/ModalLogEdit.vue';

    const adminStore = useAdminStore();

    /* Modals */

    const modals = reactive({
        currentModal: ''
    });

    const toggleModal = (modal = '') => {
        modals.currentModal = modal;
    };

    /* Main Data */

    const data = reactive({
        logs: {},
        log: {}
	});

    onMounted(() => {
        adminStore.getLogs()
            .then((response) => {
                data.logs = response.data;
            })
	});

    /* Logs */

    const handlePageChange = (newPage) => {
        adminStore.getLogs(newPage)
                .then((response) => {
                    data.logs = response.data;
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
    .card__pagination {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
</style>
