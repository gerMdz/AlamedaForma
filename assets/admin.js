import { createApp } from 'vue'
import Admin from './page/vue/components/Admin.vue'
import { createVuetify } from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

const vuetify = createVuetify({})

createApp(Admin).use(vuetify).mount('#app')
