import axios from 'axios'

const ApiService = {
  init() {
    axios.defaults.baseURL = import.meta.env.VUE_APP_API_URL
  }
}
export default ApiService
