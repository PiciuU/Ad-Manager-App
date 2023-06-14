<template>
    <el-menu default-active="/panel" class="el-menu--collapse" collapse router>
        <el-menu-item index="/panel">
            <font-awesome-icon class="nav__icon" icon="home" />
            <template #title>Strona główna</template>
        </el-menu-item>
        <el-menu-item index="/panel/szczegoly">
            <font-awesome-icon class="nav__icon" icon="chart-bar" />
            <template #title>Szczegóły reklam</template>
        </el-menu-item>
        <el-menu-item index="/panel/dane">
            <font-awesome-icon class="nav__icon" icon="address-card" />
            <template #title>Dane firmowe</template>
        </el-menu-item>
    </el-menu>
</template>

<script></script>

<style lang="scss" scoped>
.el-menu-vertical-demo:not(.el-menu--collapse) {
    position: fixed;
    height: 100%;
    left: 0;
    display: flex;
    flex-flow: column;
    z-index: 999;
}
.el-menu {
    position: fixed;
    top: 10vh;
    &.nav--collapse {
        width: 64px;
        transition: width 0.25s ease-in-out;
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

    &-item {
        text-align: left;

        &:after {
            content: '';
            position: absolute;
            width: 90%;
            height: 2px;
            background: #f4f3f5;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
    }
}

@media screen and (max-width: var(--breakpoint-small-devices)) {
    .el-aside {
        position: fixed;
        width: 100%;
        height: calc(100% - var(--height-header));
        overflow: scroll;
        z-index: 20;

        &.nav--hidden-mobile {
            left: -100%;
        }
    }
}

@media screen and (max-width: var(--breakpoint-mobile)) {
    .nav__user {
        display: flex;
    }
}
</style>
