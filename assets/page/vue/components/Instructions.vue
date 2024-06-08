<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';

import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

const instrucciones = ref([]);
const msg = ref('Hola Instructions');
console.log(msg)
let allTheRest = ref(false);

const changeAllTheRest = () => {
  allTheRest.value = !allTheRest.value
  console.log('allTheRest ', allTheRest.value)
}

const fetchData = async () => {
  try {
    const response = await axios.get('api/instructions');
    instrucciones.value = response.data['hydra:member'];
    console.log(instrucciones.value);
  } catch (err) {
    console.error(err);
  }
}

console.log('allTheRest ', allTheRest.value)

fetchData();
</script>

<template>
  <VCardText cols="12" sm="12" md="12" lg="12" xl="12">
    <v-col cols="8" class="d-flex text-left align-items-star mx-auto">
      <span class="d-none d-sm-inline">Acepto términos y condiciones</span>
      <span class="d-inline d-sm-none">Acepto T. y C.</span>
      <VCheckboxBtn

          @change="changeAllTheRest"

          value="allTheRest"

      ></VCheckboxBtn>

    </v-col>
  </VCardText>
  <v-container fluid class="fill-height w-auto">

    <VCard v-for="(ini, index) in instrucciones" :key="ini.id" v-if="allTheRest">
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3" class="col-xs-12 col-sm-12 col-md-12 col-lg-12"
                 style="text-align: justify;">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <h2 v-html="ini.Title" class="text-center"></h2>
        </v-col>
      </VCardItem>
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <p v-html="ini.Content"></p>
        </v-col>
      </VCardItem>

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