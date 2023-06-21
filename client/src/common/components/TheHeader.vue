<template>
    <el-header class="header">
        <font-awesome-icon class="header__hamburger" icon="bars" @click="handleAction" />
        <div class="header__logo">
            <router-link to="/panel">
                Ad System
            </router-link>
        </div>
        <div class="header__group">
            <div class="options">
                <router-link class="options__link" :to="{path: '/panel/ustawienia'}"><font-awesome-icon class="options__icon" icon="cog" title="Ustawienia konta" /></router-link>
                <font-awesome-icon class="options__icon" icon="sign-out-alt" title="Wyloguj" @click="authStore.logout" />
            </div>
            <div class="user">
                <div class="user__data">
                    <div class="user__name">{{ authStore.getUser.login }}</div>
                    <div class="user__company">{{ authStore.getUser.name }}</div>
                </div>
                <img class="user__avatar" src="@/assets/icons/avatar.jpg" alt="Avatar użytkownika">
            </div>
        </div>
    </el-header>
</template>

<script setup>
    import { watchEffect } from 'vue';
    import { useDataStore } from '@/stores/DataStore';
    import { useAuthStore } from '@/stores/AuthStore';

    const dataStore = useDataStore();
    const authStore = useAuthStore();

    function handleAction() {
        if (window.innerWidth > 768) dataStore.collapseSidebar();
        else dataStore.hideSidebar();
    }

    watchEffect(() => {
        if (dataStore.isScrollDisabled) document.querySelector('body').classList.add('disable-scroll')
        else document.querySelector('body').classList.remove('disable-scroll')
    });
</script>

<style lang="scss" scoped>

.header {
        position: fixed;
        width: 100%;
        z-index: $--z-index-header;

        display: flex;
        justify-content: flex-start;
        align-items: center;
        background-color: $--color-overlay;
        border-bottom: 1px solid $--color-text-muted-3;
        color: $--color-text;

        &__hamburger {
            order: 1;
            font-size: 2rem;
            margin: 0px 20px 0px 0px;
            cursor: pointer;
            transition: color .15s ease-in-out;

            &:hover {
                color: $--color-text-muted-3;
            }
        }

        &__logo {
            order: 2;
            font-size: 1.6rem;

            a {
                display: flex;
                color: $--color-text;
                text-decoration: none;
            }

            img {
                width: 70%;
                height: 70%;
                user-select: none;
            }
        }

        &__group {
            order: 3;
            margin-left: auto;
            display: flex;
            flex-direction: row;
            justify-self: center;
            align-items: center;
        }
    }

    .options {
        margin: 0px 10px;

        &__link {
            color: white;
        }

        &__icon {
            padding: 10px;
            font-size: 18px;
            cursor: pointer;
            transition: color .15s ease-in-out;

            &:hover {
                transition: color .15s ease-in-out;
                color: $--color-text-muted-3;
            }
        }
    }

    .user {
        display: flex;
        flex-direction: row;
        justify-self: center;
        align-items: center;

        &__data {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        &__name {
            word-break: break-word;
            font-size: 18px;
        }

        &__company {
            word-break: break-word;
            font-size: 14px;
            color: #e2dfdf;
        }

        &__avatar {
            margin-left: 20px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            user-select: none;
        }
    }

    @media screen and (max-width: $--breakpoint-mobile) {
        .header {
            justify-content: space-between;

            &__group {
                display: none;
            }

            &__hamburger {
                order: 2;
                margin: 0px;
            }

            &__logo {
                order: 1;
            }
        }
    }
</style>
