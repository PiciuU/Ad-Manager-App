<template>
  <h3>Hello Chart</h3>
  <canvas id="myChart"></canvas>
</template>
<script>
import Chart from 'chart.js/auto'

export default {
  mounted() {
    const ctx = document.getElementById('myChart')

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Mon.', 'Tue.', 'Wed.', 'Thu.', 'Fri', 'Sat', 'Sun.'],
        datasets: [
          {
            label: '# Views',
            data: [20, 15, 8, 35, 88, 62, 101],
            borderWidth: 1
          }
        ]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    })
  }
}
</script>
<style></style>
