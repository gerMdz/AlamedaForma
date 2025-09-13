<script setup>
import { onMounted, watch, ref } from 'vue'
import HelloForma from "./HelloForma.vue";
import {store} from "../../assets/almacen";
import FormPersonalidades from "./FormPersonalidades.vue";
import Formation from "./Formation.vue";
import CompletedFormation from "./CompletedFormation.vue";
import Inicio from "./Inicio.vue";
import axios from 'axios'

const responseData = store.responseData
const termsAccepted = store.termsAccepted
const checking = ref(false)
const checkingF = ref(false)
const hasAvanceF = ref(false)
// resultsMode is true either if backend says there is avance F or if user just saved
const resultsMode = store.resultsMode

async function checkTerms() {
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checking.value = true
  try {
    const res = await axios.get(`/api/forma/terminos-estado/${encodeURIComponent(personalId)}`)
    store.setTermsAccepted(!!res?.data?.accepted)
  } catch (e) {
    console.warn('No se pudo verificar estado de Términos', e)
    // Si la persona no existe en el backend, limpiar el caché local y volver a paso Personal
    const status = e?.response?.status || e?.status || null;
    if (status === 404) {
      try { store.setResponseData(null); } catch(_) {}
    }
    // Por seguridad, si falla la verificación mostramos Términos como no aceptados
    store.setTermsAccepted(false)
  } finally {
    checking.value = false
  }
}

async function checkAvanceF() {
  // Solo aplica si F está habilitado
  if (!(store.hasF && store.hasF.value)) {
    hasAvanceF.value = false
    try { store.setResultsMode(false) } catch(_) {}
    return
  }
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checkingF.value = true
  try {
    const res = await axios.get(`/api/forma/avance-f-estado/${encodeURIComponent(personalId)}`)
    hasAvanceF.value = !!res?.data?.hasAvanceF
    if (hasAvanceF.value) {
      try { store.setResultsMode(true) } catch(_) {}
    } else {
      // Si no tiene Avance F, aseguramos que no quede en modo resultados
      try { store.setResultsMode(false) } catch(_) {}
    }
  } catch (e) {
    console.warn('No se pudo verificar avance F', e)
    hasAvanceF.value = false
    try { store.setResultsMode(false) } catch(_) {}
  } finally {
    checkingF.value = false
  }
}

onMounted(() => {
  if (responseData?.value) {
    checkTerms()
    checkAvanceF()
  }
})

watch(responseData, (val) => {
  if (val) {
    checkTerms()
    checkAvanceF()
  }
})
</script>

<template>
  <v-container fluid class="fill-height">
    <template v-if="(resultsMode || hasAvanceF) && (store.hasF && store.hasF.value)">
      <!-- Resultados reemplazan a las otras pantallas -->
      <CompletedFormation />
    </template>
    <template v-else>
      <HelloForma/>
      <v-container v-if="responseData" fluid class="text-center">
        <div v-if="checking">Verificando términos...</div>
        <template v-else>
          <template v-if="!termsAccepted">
            <!-- Paso 2: Términos y condiciones + instrucciones -->
            <Inicio />
          </template>
          <template v-else>
            <!-- Paso 3: Formulario de dones (solo si F está habilitado) -->
            <Formation v-if="store.hasF && store.hasF.value" />
          </template>
        </template>
      </v-container>
    </template>
  </v-container>

</template>

<style scoped>

</style>