<script setup>
import { onMounted, watch, ref } from 'vue'
import HelloForma from "./HelloForma.vue";
import {store} from "../../assets/almacen";
import FormPersonalidades from "./FormPersonalidades.vue";
import Formation from "./Formation.vue";
import Inicio from "./Inicio.vue";
import axios from 'axios'

const responseData = store.responseData
const termsAccepted = store.termsAccepted
const checking = ref(false)

async function checkTerms() {
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checking.value = true
  try {
    const res = await axios.get(`/api/forma/terminos-estado/${encodeURIComponent(personalId)}`)
    store.setTermsAccepted(!!res?.data?.accepted)
  } catch (e) {
    console.warn('No se pudo verificar estado de Términos', e)
    // Por seguridad, si falla la verificación mostramos Términos
    store.setTermsAccepted(false)
  } finally {
    checking.value = false
  }
}

onMounted(() => {
  if (responseData?.value) {
    checkTerms()
  }
})

watch(responseData, (val) => {
  if (val) {
    checkTerms()
  }
})
</script>

<template>
  <v-container fluid class="fill-height">
    <HelloForma/>

    <v-container v-if="responseData" fluid class="text-center">
      <div v-if="checking">Verificando términos...</div>
      <template v-else>
        <template v-if="!termsAccepted">
          <!-- Paso 2: Términos y condiciones + instrucciones -->
          <Inicio />
        </template>
        <template v-else>
          <!-- Paso 3: Formulario de dones -->
          <Formation/>
        </template>
      </template>
    </v-container>
  </v-container>

</template>

<style scoped>

</style>