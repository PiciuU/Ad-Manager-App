<!-- eslint-disable vue/no-parsing-error -->
<template>
    <el-container class="el-container-outer is-vertical">
        <TheHeader />
        <el-container class="el-container-inner">
            <TheSidebar />
            <router-view v-slot="{ Component }">
                <keep-alive>
                    <component :is="Component" />
                </keep-alive>
            </router-view>
        </el-container>
    </el-container>
</template>

<script>
import TheHeader from '@/common/components/TheHeader.vue'
import TheSidebar from '@/common/components/TheSidebar.vue'

export default {
    name: 'AuthorizedComponent',
    components: {
        TheHeader,
        TheSidebar
    }
}
</script>

<style lang="scss" scoped>
::v-deep(.cards__container) {
    padding: 0px 20px;
    word-break: break-word;
    &:first-child {
        margin-top: 120px;
    }

    .el-col {
        margin-bottom: 30px;
    }
}

.el-container-outer {
    position: relative;
    background-image: url('https://cdn.pixabay.com/photo/2018/08/23/07/35/thunderstorm-3625405_1280.jpg');
    background-size: cover;
    width: 100vw;
    height: 100vh;
    left: 0;
    top: 0;

    &::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: rgba(2, 1, 12, 0.95);
    }
}

@media screen {
    ::v-deep(.cards__container--ordered) {
        display: flex;
        .el-col:first-child {
            order: 2;
        }
        .el-col:last-child {
            order: 1;
        }
    }
}

::v-deep(.card) {
    position: relative;
    min-height: 100%;
    max-height: 40vh;
    border-radius: 10px;
    background-color: rgba(118, 69, 233, 0.1);
    box-shadow: 2px 5px #50465a;
    transition: transform 0.3s;

    &:hover {
        transform: scale(1.05); /* Powiększamy element o 10% */
    }

    .card__skeleton {
        margin: 50px 0px;
    }

    .card__title {
        font-size: 16px;
        color: #ffffff;
        text-align: left;
        font-weight: bold;
        margin-bottom: 200px;

        &--special {
            margin-bottom: 10px;
            margin-top: 20px;
        }
    }

    .card__body {
        display: flex;
        justify-content: space-around;

        @media screen and (max-width: 1000px) {
            & {
                display: grid;
            }
        }

        @media screen and (min-width: 1001px) {
            & {
                display: flex;
            }
        }
    }

    .card__item {
        display: flex;
        align-items: center;
        text-align: left;
    }

    .card__hint {
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #ffffff;
        top: 20px;
        right: 20px;
        font-size: 10px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 1px solid #ffffff;
    }

    .card__details {
        margin-left: 15px;
    }

    .card__buttons {
        &--left {
            text-align: left;
        }
        &--bottom {
            margin-top: auto;
        }
    }

    .card__tabs {
        margin-top: 20px;
    }
}

::v-deep(.card--has-hint) {
    .card__title {
        margin-right: 20px;
    }
}

::v-deep(.card--full-height) {
    display: flex;

    .el-card__body {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
}
</style>
