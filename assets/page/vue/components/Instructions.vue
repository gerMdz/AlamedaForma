<script setup>
import { ref } from 'vue';
import axios from 'axios';

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import Personal from "./Personal.vue";

// Accept control from parent (Inicio.vue)
const props = defineProps({
  allTheRest: { type: Boolean, default: false }
})

const instrucciones = ref([]);
const msg = ref('Hola Instructions');
console.log(msg)

const fetchData = async () => {
  try {
    const response = await axios.get('api/instructions');
    instrucciones.value = response.data['member'];
    console.log(instrucciones.value);
  } catch (err) {
    console.error(err);
  }
}

fetchData();
</script>

<template>
  <v-container fluid class="fill-height w-auto">
    <VCard v-for="(ini, index) in instrucciones" :key="ini.id" v-if="props.allTheRest">
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <div v-html="ini.Title" class="text-center"></div>
        </v-col>
      </VCardItem>
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <p v-html="ini.Content"></p>
        </v-col>
      </VCardItem>
      <Personal />
    </VCard>
  </v-container>
</template>

<style scoped>
.v-selection-control__input input {
  opacity: 1 !important;
  position: static !important;
  margin-right: 5px !important;
  color: $primary !important;
}
</style>