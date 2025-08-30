<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import Instructions from './Instructions.vue';
import { store } from '../../assets/almacen';

const inicio = ref([]);
const msg = ref('Hola Inicio');
console.log(msg)
let dialog = ref(false);

// Checkbox state moved here from Instructions.vue
const allTheRest = ref(false);
const changeAllTheRest = async () => {
  allTheRest.value = !allTheRest.value
  console.log('allTheRest ', allTheRest.value)
  try {
    if (allTheRest.value) {
      const personalId = store.responseData?.value?.id || store.responseData?.value?.ID || store.responseData?.value?.Id
      if (personalId) {
        await axios.post('/api/forma/aceptar-terminos', { personalId }, { headers: { 'Content-Type': 'application/json' } })
        store.setTermsAccepted(true)
      }
    }
  } catch (e) {
    console.warn('No se pudo registrar la aceptación de términos desde Inicio.vue', e)
  }
}

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
  <v-container fluid class="fill-height">
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
        <v-col cols="8" class="d-flex text-left align-items-star mx-auto" style="gap: 12px; align-items: center;">
          <v-btn color="primary" @click="dialog = true" >
            <span class="d-none d-sm-inline">Ver</span>
            <span class="d-inline d-sm-none">Ver</span>
          </v-btn>
          <span class="d-none d-sm-inline">Acepto términos y condiciones</span>
          <span class="d-inline d-sm-none">Acepto T. y C.</span>
          <VCheckboxBtn @change="changeAllTheRest" value="allTheRest"></VCheckboxBtn>
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
        <Instructions v-if="store.termsAccepted && store.termsAccepted.value" :allTheRest="allTheRest" />
      </VCardText>
    </VCard>
  </v-container>

</template>

<style scoped>
.v-dialog__content {
  padding: 20px;
}
</style>