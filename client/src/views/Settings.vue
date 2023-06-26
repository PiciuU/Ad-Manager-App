<!-- eslint-disable vue/multi-word-component-names -->
<template>
    <el-main>
        <el-row class="cards__container" :gutter="32">
            <el-col :span="24">
                <el-card class="card">
                    <div class="container__title">Ustawienia konta</div>
                    <el-tabs v-model="activeTab" type="card" class="card__tabs">
                        <el-tab-pane label="Bezpieczeństwo" name="security" lazy>
                            <SettingsSecurity />
                        </el-tab-pane>
                        <el-tab-pane label="Preferencje" name="preferences" lazy>
                            <SettingsPreferences />
                        </el-tab-pane>
                        <el-tab-pane label="Kontakt" name="support" lazy>
                            <SettingsSupport />
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
    import SettingsSupport from '@/modules/components/SettingsSupport.vue'

    const activeTab = ref('security');
</script>

<style lang="scss" scoped>
    .card__tabs {
        margin-top: 10px;
    }
</style>
