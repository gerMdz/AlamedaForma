<template>
  <div class="vue-app">
    <SaludoForma :loaded="loaded" :has-active="hasActive"/>
    <div class="mt-2" v-if="store.responseData && store.responseData.value"
         style="width:100%; max-width:1280px; padding: 0 12px;">
      <v-btn size="small" variant="text" color="secondary"
             @click="switchUser">Iniciar nuevo test
      </v-btn>
    </div>
    <template v-if="loaded && hasActive">
      <!-- Si está en modo resultados, mostramos solo la pantalla de resultados (dentro de FormaContent) -->
      <template v-if="store.resultsMode && store.resultsMode.value">
        <FormaContent v-if="store.responseData && store.responseData.value"/>
      </template>
      <template v-else>
        <!-- Mostrar formulario de datos personales solo si NO hay persona activa -->
        <!-- Mostrar formulario si no hay persona activa o si estamos corrigiendo datos personales -->
        <template
            v-if="!(store.responseData && store.responseData.value) || (store.editPersonalMode && store.editPersonalMode.value)">
          <Personal/>
        </template>
        <!-- Si ya hay persona activa y no estamos corrigiendo, continuar con el flujo -->
        <FormaContent
            v-if="store.responseData && store.responseData.value && !(store.editPersonalMode && store.editPersonalMode.value)"/>
      </template>
    </template>
  </div>
</template>

<script setup>
import SaludoForma from "./components/SaludoForma.vue";
import Personal from "./components/Personal.vue";
import FormaContent from "./components/FormaContent.vue";

import {ref, onMounted, watch} from 'vue'
import axios from 'axios'
import {store} from '../assets/almacen'

const hasActive = ref(false)
const loaded = ref(false)

// Helper: hydrate person details if only ID is present
function needsPersonHydration(val) {
    const v = val || store.responseData?.value || null

  if (!v) return false
  const id = v.id || v.ID || v.Id
  const hasAnyField = !!(v.nombre || v.apellido || v.email || v.phone || v.point)
  return !!id && !hasAnyField
}

async function hydratePerson(val) {
  console.log('Hidratando datos de la persona (App)', val)
  const v = val || store.responseData?.value || null
  if (!v) return
  const id = v.id || v.ID || v.Id
  if (!id) return
  try {
    console.log('Hidratando datos de la persona (App)', id)
    const res = await axios.get(`/api/personal/${encodeURIComponent(id)}`)
    if (res?.data) {
      console.log('Hidratando datos de la persona (App)', res.data)
      try {
        store.setResponseData(res.data)
      } catch (_) { /* noop */
      }
    }
  } catch (e) {
    console.warn('No se pudo hidratar datos de la persona (App)', e)
  }
}

const fetchEstado = async () => {
  try {
    const res = await axios.get('/api/formularios-habilitacion/activo')
    hasActive.value = !!res.data?.hasActive
    try {
      store.setHabilitacionFlags(res?.data?.flags || {});
    } catch (_) { /* noop */
    }
  } catch (e) {
    console.error('Error consultando formularios habilitados', e)
    hasActive.value = false
    try {
      store.setHabilitacionFlags({T: false, F: false, O: false, R: false});
    } catch (_) { /* noop */
    }
  } finally {
    loaded.value = true
  }
}

const switchUser = () => {
  try {
    store.clearAll();
  } catch (e) { /* noop */
  }
}

onMounted(async () => {
  try {
    store.hydrate();
  } catch (e) { /* noop */
  }
  // Si hay solo ID, hidratar datos completos antes de continuar
  if (store.responseData && store.responseData.value && needsPersonHydration(store.responseData.value)) {
    await hydratePerson(store.responseData.value)
  }
  fetchEstado()
})

// Reaccionar a cambios en responseData (por ejemplo, después de crear persona)
watch(store.responseData, async (val) => {
  if (val && needsPersonHydration(val)) {
    await hydratePerson(val)
  }
})
</script>

<style>
.vue-app {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
}

.logo {
  height: 10em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}

.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}

.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>