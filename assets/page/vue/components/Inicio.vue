<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';

const inicio = ref([]);
const msg = ref('Hola Inicio');
console.log(msg)

const fetchData = async () => {
  try {
    const response = await axios.get('api/inicio');
    inicio.value = response.data['hydra:member'];
    console.log(inicio.value);
  } catch (err) {
    console.error(err);
  }
}

fetchData();
</script>

<template>
  <v-container fluid class="fill-height w-auto">
    <VCard v-for="(ini, index) in inicio" :key="ini.id" class="text-center">
      <VCardTitle cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <h2 v-html="ini.Title"></h2>
        </v-col>
      </VCardTitle>
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <p v-html="ini.Content"></p>
        </v-col>
      </VCardItem>
      <VCardText cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="8" class="d-flex text-left align-items-star mx-auto">
          <p v-html="ini.Terms"></p>
        </v-col>
      </VCardText>
    </VCard>
  </v-container>

</template>

<style scoped>

</style>