import { createApp } from 'vue'
import Login from './page/vue/components/Login.vue'
import { createVuetify } from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

const vuetify = createVuetify({})

createApp(Login).use(vuetify).mount('#app')
