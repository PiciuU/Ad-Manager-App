<template>
  <div id="app">
    <app-theheader></app-theheader>

    <app-theposts></app-theposts>

    <app-chartcomponent></app-chartcomponent>

    <p>Pozdrawiam wszystkich polaków</p>
    <font-awesome-icon icon="fa-solid fa-user-secret" />

    <font-awesome-icon icon="fa-solid fa-arrow-down" />
    <font-awesome-icon icon="fa-solid fa-home" />
    <el-button type="success" round>Success</el-button>

    <el-button type="warning" round>Warning</el-button>

    <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
    <font-awesome-icon icon="fa-solid fa-user-secret" />

    <line-chart :data="chartData"></line-chart>
  </div>
</template>

<script>
import TheHeader from './common/components/TheHeader.vue'
import ThePosts from './common/components/ThePosts.vue'

export default {
  name: 'app',
  data() {
    return {
      chartData: {
        '2017-05-13': 2,
        '2017-05-14': 5,
        '2017-05-15': 4
      }
    }
  },
  components: {
    'app-theheader': TheHeader,
    'app-theposts': ThePosts
  }
}
</script>

<style></style>
