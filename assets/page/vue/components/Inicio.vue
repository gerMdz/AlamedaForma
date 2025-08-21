<script setup>
import {ref, computed} from 'vue';
import axios from 'axios';

const inicio = ref([]);
const msg = ref('Hola Inicio');
console.log(msg)
let dialog = ref(false);

const hideDialog = () => {
  dialog.value = false
}

const fetchData = async () => {
  try {
    const response = await axios.get('api/inicios');
    inicio.value = response.data['member'];
    console.log('18')
    console.log(inicio);
  } catch (err) {
    console.error(err);
  }
}

fetchData();
</script>

<template>
  <v-container fluid class="fill-height w-auto">
    <VCard v-for="(ini, index) in inicio" :key="ini.id">
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <h2 v-html="ini.Title" class="text-center"></h2>
        </v-col>
      </VCardItem>
      <VCardItem cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="12" class="d-flex justify-center align-items-center">
          <p v-html="ini.Content"></p>
        </v-col>
      </VCardItem>
      <VCardText cols="12" sm="12" md="6" lg="3" xl="3">
        <v-col cols="8" class="d-flex text-left align-items-star mx-auto">
          <v-btn color="primary" @click="dialog = true" >
            <span class="d-none d-sm-inline">Ver términos y condiciones</span>
            <span class="d-inline d-sm-none">Ver T. y C.</span>
          </v-btn>
          <v-dialog v-model="dialog" max-width="900px" class="mx-12">
            <v-card>
              <v-card-title class="text-right">
                <v-btn @click="dialog = false">
                  Cerrar
                </v-btn>
              </v-card-title>
              <v-card-text class="mx-5" v-html="ini.Terms"></v-card-text>
            </v-card>
          </v-dialog>
        </v-col>
      </VCardText>
    </VCard>
  </v-container>

</template>

<style scoped>
.v-dialog__content {
  padding: 20px;
}
</style>