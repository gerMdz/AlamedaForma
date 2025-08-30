<template>
  <v-container class="mt-6">
    <v-row>
      <v-col cols="12">
        <v-alert type="success" class="mb-4">
          Ya registraste tus resultados de Formación. Aquí está tu resumen.
        </v-alert>
      </v-col>
    </v-row>
    <SavedResults v-if="savedData" :data="savedData" />
    <v-row v-else>
      <v-col cols="12">
        <v-alert type="info">Cargando resultados guardados...</v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { store } from '../../assets/almacen'
import SavedResults from './SavedResults.vue'

const savedData = ref(null)

async function loadLatest() {
  const person = store.responseData?.value || null
  const pid = person?.id || person?.ID || person?.Id
  if (!pid) return
  try {
    const latest = await axios.get(`/api/personal-formation/ultimos/${encodeURIComponent(pid)}`)
    savedData.value = { person, formations: latest?.data || [] }
  } catch (e) {
    console.warn('No se pudieron cargar los últimos resultados de formación', e)
    savedData.value = { person, formations: [] }
  }
}

onMounted(() => {
  loadLatest()
  try { store.setResultsMode(true) } catch(e) { /* noop */ }
})
</script>
