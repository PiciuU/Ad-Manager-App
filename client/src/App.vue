<template>
    <el-config-provider :locale="locale">
		<router-view/>
	</el-config-provider>
</template>

<script setup>
    import pl from 'element-plus/lib/locale/lang/pl'

    const locale = pl;
</script>
