<template>
    <svg class="background-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 300" :class="`wave-${$route.name}`" >
        <path fill-opacity="1" d="M0,160L48,138.7C96,117,192,75,288,90.7C384,107,480,181,576,213.3C672,245,768,235,864,224C960,213,1056,203,1152,213.3C1248,224,1344,256,1392,272L1440,288L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
    </svg>
</template>

<script>
export default {
	name: 'backgroundWave',
}
</script>

<style lang="scss" scoped>
    .background-wave {
        position: absolute;
        bottom: 0;
        transition: fill .25s ease-in-out;
    }
    .wave-login {
        fill: $--color-blue;
    }

    .wave-register {
        fill: $--color-green;
    }

    .wave-recover-password, .wave-reset-password {
        fill: $--color-orange;
    }

    @media screen and (max-width: $--breakpoint-small-devices) {
        .background-wave {
            display: none;
        }
    }
</style>