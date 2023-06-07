<template>
    <el-header class="header">
        <font-awesome-icon class="header__hamburger" icon="bars" @click="collapseMenu" />
        <div class="header__logo"></div>
        <div class="header__group">
            <div class="options">
                <router-link class="options__link" :to="{ path: '/panel/ustawienia' }">
                    <font-awesome-icon class="fa-wrench" icon="fa-wrench" title="Ustawienia konta"
                /></router-link>
                <font-awesome-icon
                    class="options__icon"
                    icon="sign-out-alt"
                    title="Wyloguj"
                    @click="logout"
                />
            </div>
            <div class="user">
                <div class="user__data">
                    <div class="user__name">Zilak</div>
                    <div class="user__company">Mieszalnia farb</div>
                </div>
                <div class="icon">
                    <font-awesome-icon
                        class="fa-circle-user"
                        icon="fa-circle-user"
                        title="fa-circle-cser"
                    />
                </div>
            </div>
        </div>
    </el-header>
</template>
<script>
export default {}
</script>
<style lang="scss" scoped>
.header {
    position: fixed;
    width: 100%;
    height: 10vh;
    background-color: #060216;
    margin-top: 0;
    top: 0;
    left: 0;
    border-bottom: 1px solid white;

    display: flex;
    justify-content: flex-start;
    align-items: center;
    color: rgb(90, 156, 255);

    &__hamburger {
        order: 1;
        font-size: 24px;
        margin: 0px 20px 0px 0px;
        cursor: pointer;
        transition: color 0.15s ease-in-out;

        &:hover {
            transition: color 0.15s ease-in-out;
            color: rgb(40, 91, 168);
        }
    }

    &__logo {
        order: 2;
        font-size: 18px;

        a {
            display: flex;
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
    margin: 5px;
    display: flex;
    flex-direction: row;
    justify-self: center;
    align-items: center;
    color: rgb(90, 156, 255);

    &__link {
        color: rgb(90, 156, 255);
        &:hover {
            scale: 1.3;
            transition: color 0.15s ease-in-out;
            color: rgb(40, 91, 168);
        }
    }

    &__icon {
        padding: 2vw;
        font-size: 20px;
        cursor: pointer;
        transition: color 0.15s ease-in-out;

        &:hover {
            transition: color 0.15s ease-in-out;
            color: rgb(40, 91, 168);
            scale: 1.3;
        }
    }
}

.user {
    display: flex;
    flex-direction: row;
    justify-self: center;
    align-items: center;
    margin: 20px;

    &__data {
        display: flex;
        flex-direction: column;
        text-align: right;
    }

    &__name {
        word-break: break-word;
        font-size: 18px;
        transform: translateX(-50%);
        right: 50px;
    }

    &__company {
        word-break: break-word;
        font-size: 14px;
        color: #e2dfdf;
        transform: translateX(-50%);
    }

    &__avatar {
        margin-left: 20px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        user-select: none;
    }
}
.icon{
    font-size: 40px;
}
</style>
