<!-- eslint-disable vue/multi-word-component-names -->
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

<script setup>
import 'chartkick/chart.js'

import TheHeader from '@/common/components/TheHeader.vue'
import TheSidebar from '@/common/components/TheSidebar.vue'
</script>

<style lang="scss" scoped>
::v-deep(.cards__container) {
    padding: 0px 4vw;
    word-break: break-word;
    &:first-child {
        margin-top: 10vh;
    }

    .el-col {
        margin-bottom: 30px;
    }
}

.el-container {
    display: flex;
    background-color: var(--color-background);
    background-size: cover;
}

@media screen and (max-width: var(--breakpoint-medium-devices)) {
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
    border-radius: 10px;
    max-height: 40vh;
    background-color: rgba(0, 0, 0, 0.5);

    &:hover {
        transform: scale(1.02);
        border: 1px solid #fff;
    }

    .card__skeleton {
        margin: 50px 0px;
    }

    .card__title {
        font-size: 16px;
        color: var(--color-text);
        text-align: left;
        font-weight: bold;
        margin-bottom: 20vh;
        display: flex;
        justify-content: center;

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
        color: var(--color-text);
        top: 20px;
        right: 20px;
        font-size: 10px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        border: 1px solid (var(--color-text));
    }

    .card__details {
        margin-left: 15px;
    }

    .card__select {
        width: 100%;
    }

    .card__tabs {
        margin-top: 20px;
    }
    .card__data {
        &-ctr {
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
            color: var(--color-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: default;
        }

        &-circle {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        &-amount {
            font-weight: bold;
            cursor: default;
        }

        &-description {
            margin-top: 2px;
            font-size: 14px;
            color: var(--color-text-muted-3);
            cursor: default;
        }

        &-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
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
@media screen and (max-width: var(--breakpoint-mobile)) {
    ::v-deep(.cards__container) {
        padding: 0px;
    }
}
</style>
