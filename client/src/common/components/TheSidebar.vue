<template>
    <el-aside :class="{ 'nav--collapse': dataStore.isSidebarCollapsed, 'nav--hidden-mobile': dataStore.isSidebarHidden }">
        <el-row>
            <el-col :span="24">

                <div class="nav__namespace">
                    <transition name="el-fade-in" mode="out-in">
                        <span v-if="!dataStore.isSidebarCollapsed">Nawigacja</span>
                        <font-awesome-icon v-else icon="compass" />
                    </transition>
                </div>

                <el-menu :default-active="route.path" router :collapse="dataStore.isSidebarCollapsed" :collapse-transition="false" @select="handleAction">

                    <el-menu-item index="/panel">
                        <font-awesome-icon class="nav__icon" icon="home" />
                        <span>Strona główna</span>
                    </el-menu-item>

                    <el-menu-item index="/panel/szczegoly">
                        <font-awesome-icon class="nav__icon" icon="chart-bar" />
                        <span>Szczegóły reklam</span>
                    </el-menu-item>

                    <el-menu-item index="/panel/dane">
                         <font-awesome-icon class="nav__icon" icon="address-card" />
                        <span>Dane firmowe</span>
                    </el-menu-item>

                </el-menu>

                <div class="nav__user">
                    <img class="nav__avatar" src="@/assets/icons/avatar.jpg" alt="Avatar użytkownika">
                    <div class="nav__details">
                        <div class="nav__name">{{ user.login }}</div>
                        <div class="nav__company">{{ user.name }}</div>
                    </div>
                    <div class="nav__options">
                        <router-link class="options__link" :to="{path: '/panel/ustawienia'}" @click="dataStore.handleAction">
                            <font-awesome-icon class="nav__icon--secondary" icon="cog" />
                        </router-link>
                        <font-awesome-icon class="nav__icon--secondary" icon="sign-out-alt" @click="authStore.logout" />
                    </div>
                </div>
            </el-col>
        </el-row>
    </el-aside>
</template>

<script setup>
    import { reactive } from 'vue';
    import { useRoute } from 'vue-router';
    import { useDataStore } from '@/stores/DataStore';
    import { useAuthStore } from '@/stores/AuthStore';

    const dataStore = useDataStore();
    const authStore = useAuthStore();
    const route = useRoute();

    function handleAction() {
        if(!dataStore.isSidebarHidden) dataStore.hideSidebar();
    }

    const user = reactive({
        login: 'Zilak',
        name: 'Mieszalnia farb'
    });
</script>

<style lang="scss" scoped>
.el-aside {
        position: fixed;
        width: 250px;
        height: 100%;
        background-color: $--color-overlay;
        border-right: 1px solid $--color-text-muted-3;
        left:0;
        display: flex;
        flex-flow: column;
        box-shadow: 2px 0 10px -1px rgba(136, 136, 136, 0.1);
        transition: width .25s ease-in-out,
                    left .35s ease-in-out;

        &.nav--collapse {
            width: 64px;
            transition: width .25s ease-in-out;

            .nav__namespace {
                color: $--color-text;
                font-size: 24px;
            }
        }
    }

    .el-row {
        flex: 1 1 auto;
    }

    .el-col {
        display: flex;
        flex-direction: column;
    }

    .el-menu {
        border-right: none;
        margin-bottom: 20px;

        &--collapse &-item:after {
            width: 100%;
        }

        &-item {
            text-align: left;

            &:after {
                content: '';
                position: absolute;
                width: 100%;
                height: 1px;
                background: $--color-text-muted-3;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
            }
        }
    }

    .nav {
        &__namespace {
            position: relative;
            height: 60px;
            font-size: 16px;
            letter-spacing: 2px;
            padding: 20px;
            color:  $--color-text;
            text-transform: uppercase;
            display: flex;
            justify-content: center;
            align-items: center;
            transition-delay: .25s;

            &:after {
                content: '';
                position: absolute;
                width: 100%;
                height: 2px;
                background: $--color-text-muted-2;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%)
            }
        }

        &__icon {
            margin-right: 20px;
            width: 24px;
            font-size: 18px;
        }

        &__user {
            position: relative;
            display: none;
            justify-content: center;
            align-items: center;
            padding: 0px 10px;
            margin-top: auto;
            margin-bottom: 20px;

            &:before {
                content: '';
                position: absolute;
                width: 100%;
                height: 2px;
                background: $--color-text-muted-2;
                top: -20px;
                left: 50%;
                transform: translateX(-50%);
            }
        }

        &__avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 25px;
        }

        &__details {
            text-align: left;
            margin-right: 25px;
        }

        &__name {
            word-break: break-word;
            font-size: 18px;
        }

        &__company {
            word-break: break-word;
            font-size: 14px;
            color: #767777;
        }

        &__icon--secondary {
            width: 24px;
            font-size: 18px;
            color: $--color-text;
            cursor: pointer;
            transition: color .15s ease-in-out;

            &:hover {
                color: $--color-text-muted-3;
                transition: color .15s ease-in-out;
            }

            &:first-child {
                margin-right: 10px;
            }

        }

    }

    @media screen and (max-width: $--breakpoint-small-devices) {
        .el-aside {
            position: fixed;
            width: 100%;
            height: calc(100% - $--height-header);
            overflow: scroll;
            z-index: 20;
            border: none;

            &.nav--hidden-mobile {
                left: -100%;
            }
        }
    }

    @media screen and (max-width: $--breakpoint-mobile) {
        .nav__user {
            display: flex;
        }
    }
</style>
