<template>
    <h1>hello SP</h1>
</template>

<script>
export default {}
</script>

<style></style>


<!-- 
<template>
	<el-aside
		:class="{
			'nav--collapse': isSidebarCollapsed,
			'nav--hidden-mobile': isSidebarHidden,
		}"
	>
		<el-row>
			<el-col :span="24">
				<div class="nav__namespace">
					<transition name="el-fade-in" mode="out-in">
						<span v-if="!isSidebarCollapsed">Nawigacja</span>
						<font-awesome-icon v-else icon="compass" />
					</transition>
				</div>

				<el-menu
					:default-active="$route.path"
					router
					:collapse="isSidebarCollapsed"
					:collapse-transition="false"
					@select="handleRoute"
				>
					<el-menu-item index="/panel">
						<font-awesome-icon class="nav__icon" icon="home" />
						<span>Strona główna</span>
					</el-menu-item>

					<el-menu-item index="/panel/szczegoly">
						<font-awesome-icon class="nav__icon" icon="chart-bar" />
						<span>Szczegóły reklam</span>
					</el-menu-item>

					<el-menu-item index="/panel/dane">
						<font-awesome-icon class="nav__icon" icon="address-card" />
						<span>Dane firmowe</span>
					</el-menu-item>
				</el-menu>

				<div class="nav__user">
					<img
						class="nav__avatar"
						src="@/assets/icons/avatar.jpg"
						alt="Avatar użytkownika"
					/>
					<div class="nav__details">
						<div class="nav__name">{{ truncateString(user.login) }}</div>
						<div class="nav__company">{{ truncateString(user.name) }}</div>
					</div>
					<div class="nav__options">
						<router-link
							class="options__link"
							:to="{ path: '/panel/ustawienia' }"
							@click="handleRoute"
						>
							<font-awesome-icon class="nav__icon--secondary" icon="cog" />
						</router-link>
						<font-awesome-icon
							class="nav__icon--secondary"
							icon="sign-out-alt"
							@click="logout"
						/>
					</div>
				</div>
			</el-col>
		</el-row>
	</el-aside>
</template> -->

<!-- <script>
import { computed } from 'vue';
import { useStore } from 'vuex';

import { SIDEBAR_HIDE, AUTH_LOGOUT } from '@/store/actions.type';
import { truncateString } from '@/common/helpers/utility.helper';

export default {
	name: 'Sidebar',
	setup() {
		const store = useStore();

		const user = computed(() => store.getters.currentUser);

		const isSidebarCollapsed = computed(() => store.getters.isSidebarCollapsed);
		const isSidebarHidden = computed(() => store.getters.isSidebarHidden);

		function handleRoute() {
			if (!isSidebarHidden.value) store.dispatch(SIDEBAR_HIDE);
		}

		function logout() {
			if (!isSidebarHidden.value) store.dispatch(SIDEBAR_HIDE);
			store.dispatch(AUTH_LOGOUT);
		}

		return {
			user,
			isSidebarCollapsed,
			isSidebarHidden,
			handleRoute,
			logout,
			truncateString,
		};
	},
};
</script>

<style lang="scss" scoped>
.el-aside {
	position: fixed;
	width: 250px;
	height: 100%;
	background-color: #fff;
	left: 0;
	display: flex;
	flex-flow: column;
	box-shadow: 2px 0 10px -1px rgba(136, 136, 136, 0.1);
	transition: width 0.25s ease-in-out, left 0.35s ease-in-out;

	&.nav--collapse {
		width: 64px;
		transition: width 0.25s ease-in-out;

		.nav__namespace {
			color: #307dfb;
			font-size: 24px;
		}
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

	&--collapse &-item:after {
		width: 100%;
	}

	&-item {
		text-align: left;

		&:after {
			content: '';
			position: absolute;
			width: 90%;
			height: 2px;
			background: #f5f8fa;
			bottom: 0;
			left: 50%;
			transform: translateX(-50%);
		}
	}
}

.nav {
	&__namespace {
		position: relative;
		height: 20px;
		font-size: 16px;
		letter-spacing: 2px;
		padding: 20px;
		color: #767777;
		text-transform: uppercase;
		display: flex;
		justify-content: center;
		align-items: center;
		transition-delay: 0.25s;

		&:after {
			content: '';
			position: absolute;
			width: 100%;
			height: 2px;
			background: #f5f8fa;
			bottom: 0;
			left: 50%;
			transform: translateX(-50%);
		}
	}

	&__icon {
		margin-right: 20px;
		width: 24px;
		font-size: 18px;
	}

	&__user {
		position: relative;
		display: none;
		justify-content: center;
		align-items: center;
		padding: 0px 10px;
		margin-top: auto;
		margin-bottom: 20px;

		&:before {
			content: '';
			position: absolute;
			width: 100%;
			height: 2px;
			background: #f5f8fa;
			top: -20px;
			left: 50%;
			transform: translateX(-50%);
		}
	}

	&__avatar {
		width: 35px;
		height: 35px;
		border-radius: 50%;
		margin-right: 25px;
	}

	&__details {
		text-align: left;
		margin-right: 25px;
	}

	&__name {
		word-break: break-word;
		font-size: 18px;
	}

	&__company {
		word-break: break-word;
		font-size: 14px;
		color: #767777;
	}

	&__icon--secondary {
		width: 24px;
		font-size: 18px;
		color: $--color-blue;
		cursor: pointer;
		transition: color 0.15s ease-in-out;

		&:hover {
			color: #409eff;
			transition: color 0.15s ease-in-out;
		}

		&:first-child {
			margin-right: 10px;
		}
	}
}

@media screen and (max-width: $--breakpoint-small-devices) {
	.el-aside {
		position: fixed;
		width: 100%;
		height: calc(100% - $--height-header);
		overflow: scroll;
		z-index: 20;

		&.nav--hidden-mobile {
			left: -100%;
		}
	}
}

@media screen and (max-width: $--breakpoint-mobile) {
	.nav__user {
		display: flex;
	}
}
</style> -->
