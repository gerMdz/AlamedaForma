// import "./theme/pages/assets.scss";
// import "./theme/components/button.scss";
import { createApp } from 'vue'
import App from './page/vue/App.vue'
import { createVuetify } from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
const vuetify = createVuetify({})

console.log("Happy coding !!");

createApp(App).use(vuetify).mount('#app')

