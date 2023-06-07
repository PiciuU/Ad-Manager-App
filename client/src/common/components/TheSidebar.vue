<template>
    <el-aside :class="{ 'nav--collapse': isCollapse, 'nav--hidden-mobile': false }">
        <el-radio-group v-model="isCollapse" style="margin-left: 200px">
            <el-radio-button :label="false">expand</el-radio-button>
            <el-radio-button :label="true">collapse</el-radio-button>
        </el-radio-group>
        <el-menu
            default-active="2"
            class="el-menu-vertical-demo"
            :collapse="isCollapse"
            @open="handleOpen"
            @close="handleClose"
        >
            <div class="el-menu-item nav__namespace">
                <transition name="el-fade-in" mode="out-in">
                    <span v-if="!isCollapse">Nawigacja</span>
                    <font-awesome-icon v-else icon="compass" />
                </transition>
            </div>
            <el-menu-item index="/panel">
                <font-awesome-icon class="nav__icon" icon="home" />
                <span class="white-text">Strona główna</span>
            </el-menu-item>
            <el-menu-item index="/panel/szczegoly">
                <font-awesome-icon class="nav__icon" icon="chart-bar" />
                <span class="white-text">Szczegóły reklam</span>
            </el-menu-item>
            <el-menu-item index="/panel/dane">
                <font-awesome-icon class="nav__icon" icon="address-card" />
                <span class="white-text">Dane firmowe</span>
            </el-menu-item>
        </el-menu>
    </el-aside>
</template>

<script setup>
import { ref } from 'vue'

const isCollapse = ref(true)
</script>

<style lang="scss" scoped>
.el-aside {
    margin-top: 10vh;
}
.el-menu {
    background-color: rgba(3, 3, 3, 0.8);
    position: fixed;
    height: 100%;
    background-color: #060216;
    margin-top: 0;
    top: 10vh;
    left: 0;
    color: rgb(90, 156, 255);

    &-item {
        text-align: left;

        &:after {
            content: '';
            position: absolute;
            width: 90%;
            height: 2px;
            background: #696969;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
    }
}
.nav__icon {
    color: rgb(90, 156, 255);
    transition: 0.2;
}
.el-menu-item:hover .nav__icon {
    color: rgb(40, 91, 168);
    scale: 1.4;
}
.nav__namespace {
    cursor: default;
    color: white;
    font-size: 22px;
    background-color: rgba($color: #ffffff, $alpha: 0.2);
}
.white-text {
    color: rgb(255, 255, 255);
}
</style>
