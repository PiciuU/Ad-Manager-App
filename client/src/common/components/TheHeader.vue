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
                <div class="options__notification" :class="{'options__notification-unseen': authStore.getUser.hasUnseenNotification}">
                    <font-awesome-icon @click="showNotifications" class="options__icon" icon="bell" title="Powiadomienia" />
                    <transition name="fade">
                        <div v-show="isNotificationsVisible" @click="showNotifications" class="notification__overlay"></div>
                    </transition>
                    <transition name="fade">
                        <div class="notification" v-show="isNotificationsVisible">
                            <div v-if="notifications" class="notification__content">
                                <div class="notification__item" v-for="notification in notifications" :key="notification.id" :class="{'notification__not-seen': notification.isSeen == 0}">
                                    <span>
                                        <router-link @click="showNotifications" :to="{path: '/panel/powiadomienia'}" class="notification__text">
                                            {{ truncateString(notification .title, 75) }}
                                        </router-link>
                                    </span>
                                    <font-awesome-icon @click="changeSeenStatus(notification)" class="options__icon" icon="eye" title="Zaznacz jako odczytana" />
                                </div>
                                <div class="notification__footer">
                                    <router-link @click="showNotifications" :to="{path: '/panel/powiadomienia'}">
                                        Wyświetl wszystkie powiadomienia
                                    </router-link>
                                </div>
                            </div>
                            <div v-else class="notification__content notification__content-loading" >
                                <div class="notification__item">
                                    <span>Trwa wczytywanie powiadomień...</span>
                                </div>
                                <div class="notification__footer">
                                    <router-link @click="showNotifications" :to="{path: '/panel/powiadomienia'}">
                                        Wyświetl wszystkie powiadomienia
                                    </router-link>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
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
    import { ref, watchEffect } from 'vue';
    import { truncateString } from '@/common/helpers/utility.helper';
    import { useDataStore } from '@/stores/DataStore';
    import { useAuthStore } from '@/stores/AuthStore';

    const dataStore = useDataStore();
    const authStore = useAuthStore();

    const handleAction = () => {
        if (window.innerWidth > 768) dataStore.collapseSidebar();
        else dataStore.hideSidebar();
    }

    watchEffect(() => {
        if (dataStore.isScrollDisabled) document.querySelector('body').classList.add('disable-scroll')
        else document.querySelector('body').classList.remove('disable-scroll')
    });

    /* Notifications */

    const isNotificationsVisible = ref(false);
    const notifications = ref('');

    const showNotifications = () => {
        if (!notifications.value){
            authStore.fetchLatestNotifications()
                .then((response) => {
                    notifications.value = response.data;
                })
        }
        isNotificationsVisible.value = !isNotificationsVisible.value;
    };

    const changeSeenStatus = async (notification) => {
        await authStore.changeNotificationStatus(notification.id)
            .then((response) => {
                notification.isSeen = response.data.isSeen;
            })

        if (authStore.getUser.hasUnseenNotification == true && !notifications.value.some(notification => notification.isSeen == 0)) {
            authStore.user.hasUnseenNotification = false;
        }
    };
</script>

<style lang="scss" scoped>

.header {
        position: fixed;
        width: 100%;
        z-index: $--z-index-header;

        display: flex;
        justify-content: flex-start;
        align-items: center;
        background-color: $--color-header;
        border-bottom: 1px solid $--color-text-muted-3;
        color: $--color-text-on-header;

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
            font-size: 1.8rem;

            a {
                display: flex;
                color: $--color-text-on-header;
                text-decoration: none;
                font-weight: bold;
                letter-spacing: 1px;
                transition: color .25s ease-in-out;

                &:hover {
                    color: $--color-primary;
                }
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
            font-size: 1.8rem;
            cursor: pointer;
            transition: color .15s ease-in-out;

            &:hover {
                transition: color .15s ease-in-out;
                color: $--color-primary;
            }
        }

        &__notification {
            display: inline-block;
            position: relative;

        }

        &__notification-unseen {
                &:after {
                content: '';
                position: absolute;
                width: 5px;
                height: 5px;
                border-radius: 50%;
                top: 5px;
                right: 5px;
                background:$--color-error;
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
            font-size: 1.8rem;
        }

        &__company {
            word-break: break-word;
            font-size: 1.4rem;
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

    .notification {
        width: 400px;
        height: 200px;
        background: $--color-overlay;
        box-shadow: 5px 5px 20px 0 rgba(black, 0.2);
        border-radius: 5px;
        border: 1px solid $--color-border-1;
        position: absolute;
        left: -182px;
        top: 40px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        z-index: 200;

        &__content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        &__content.notification__content-loading &__item {
            flex: 1;
            justify-content: center;
        }

        &:after {
            content: "";
            position: absolute;
            top: 0px;
            right: 182px;
            width: 0;
            height: 0;
            transform: translate(-10px, -100%);
            border-left: 0.75rem solid transparent;
            border-right: 0.75rem solid transparent;
            border-bottom: 0.75rem solid $--color-text-muted-3;
        }

        &__overlay {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow: auto;
        }

        &__item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            color: $--color-text-muted-2;

            a {
                color: inherit;
                text-decoration: none;
            }

            &:after {
                position: absolute;
                content: '';
                width: 95%;
                left: 50%;
                transform: translateX(-50%);
                height: 1px;
                background: $--color-text-muted-3;
                bottom: -7.5px;
            }
        }

        &__not-seen {
            color: $--color-primary;
        }

        &__text {
            flex: 1;
            padding: 0px 10px;
            font-size: 1.4rem;
        }

        .options__icon {
            font-size: 1.2rem;
        }

        &__footer {
            margin: auto;
            text-align: center;

            a {
                color: $--color-text;
                text-decoration: none;
                font-size: 1.4rem;
            }
        }
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s ease;
    }

    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }

</style>
