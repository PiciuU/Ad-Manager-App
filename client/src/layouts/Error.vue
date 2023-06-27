<template>
	<div class="container">
		<div class="container__error">Błąd 404</div>
		<div class="container__description">Ups, wygląda na to że wyszukana strona nie istnieje!</div>
		<el-button class="container__button" type="primary" @click="handleReturnButton">Wróć do poprzedniej strony</el-button>
	</div>
</template>

<script>
export default {
	name: 'Error',
	setup() {
		function handleReturnButton() {
			history.back();
		}

		return { handleReturnButton };
	}
}
</script>

<style lang="scss" scoped>
	.container {
		width: 100%;
		height: 100%;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
        flex: 1;
        text-align: center;

        &__error {
            font-size: 6.4rem;
            font-weight: bold;
        }

		&__description {
			margin-top: 30px;
			font-size: 2.6rem;
			font-weight: bold;
			padding: 0px 10px;
		}

		&__button {
			margin-top: 30px;
		}
	}

</style>