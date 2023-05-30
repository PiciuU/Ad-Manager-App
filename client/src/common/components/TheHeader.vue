<template>
  <nav class="container mb-4 mt-4">
    <div class="col-9">
      <div class="col-3">
        <select class="custom-select" v-model="lang" @change="handleChange($event)">
          <option value="pl">Polski</option>
          <option value="eng">English</option>
          <option value="de">Deutsch</option>
        </select>
      </div>
    </div>
  </nav>
  <h1>Hello theheader</h1>
</template>

<script>
export default {
  data: function () {
    const lang = localStorage.getItem('lang') || 'pl'
    return {
      lang: lang
    }
  },
  methods: {
    handleChange(event) {
      localStorage.setItem('lang', event.target.value)
      window.location.reload()
    }
  }
}
</script>

<style></style>
