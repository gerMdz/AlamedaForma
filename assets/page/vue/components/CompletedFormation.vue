<template>
  <v-container class="mt-6">
    <SavedResults v-if="savedData" :data="savedData" :show-header="false" :show-person="false" />
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

const switchUser = () => {
  try { store.clearAll(); } catch(e) { /* noop */ }
}

onMounted(() => {
  loadLatest()
  try { store.setResultsMode(true) } catch(e) { /* noop */ }
})
</script>
