<template>
	<main>
		<div class="container">
			<router-view v-slot="{ Component }">
				<keep-alive>
					<component :is="Component"></component>
				</keep-alive>
			</router-view>
		</div>
	</main>
</template>

<style lang="scss" scoped>
	:deep() { @import '@/assets/styles/auth.scss'; }

	main {
		display: flex;
		flex: 1;
	}

	.container {
		display: flex;
        justify-content: center;
        align-items: center;
		max-width: 1280px;
		margin-left: auto;
		margin-right: auto;
		width: 100%;
		flex: 1;
	}

	::v-deep(.el-card__header) {
		border: none;
	}
</style>