<!-- eslint-disable vue/multi-word-component-names -->
<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="24">
                <el-card class="card">
                    <div class="container__title">Ustawienia konta</div>
                    <el-tabs
                        v-model="activeName"
                        type="card"
                        class="demo-tabs"
                        @tab-click="handleClick"
                    >
                        <el-tab-pane label="Bezpieczeństwo" name="first">
                            <SettingsSecurity />
                        </el-tab-pane>
                        <el-tab-pane label="Preferencje" name="second">
                            <SettingsPreferences />
                        </el-tab-pane>
                    </el-tabs>
                </el-card>
            </el-col>
        </el-row>
    </el-main>
</template>

<script setup>
import { ref } from 'vue'
import SettingsSecurity from '@/modules/components/SettingsSecurity.vue'
import SettingsPreferences from '@/modules/components/SettingsPreferences.vue'

const activeName = ref('first')
const handleClick = (tab, event) => {
    console.log(tab, event)
}
</script>

<style scoped lang="scss">
.demo-tabs,
.container__title,
.el-tabs__content {
    padding: 32px;
    color: #6b778c;
    font-size: 2rem;
    font-weight: 600;
}

.container__title {
    padding: 0;
}
</style>
