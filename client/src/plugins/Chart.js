import VueChartkick from 'vue-chartkick'
import 'chartkick/chart.js'
import Chart from 'chart.js/auto'

const chart = [VueChartkick, Chart]
export default (app) => {
  chart.forEach((chart) => {
    app.use(chart)
  })
}
