import { createApp } from 'vue'
import Admin from './page/vue/admin/Admin.vue'
import { createVuetify } from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import '@mdi/font/css/materialdesignicons.css'

const vuetify = createVuetify({})

createApp(Admin).use(vuetify).mount('#app')
