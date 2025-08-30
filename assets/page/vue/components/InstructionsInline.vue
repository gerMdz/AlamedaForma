<template>
  <v-container class="mb-4">
    <div v-for="(inst, idx) in instrucciones" :key="idx" class="mb-2">
      <div class="text-subtitle-1" v-html="inst.Title"></div>
      <div v-html="inst.Content"></div>
    </div>
  </v-container>
</template>

<script setup>
import {ref, onMounted} from 'vue'
import axios from 'axios'

const instrucciones = ref([])

async function fetchInstructions() {
  try {
    const res = await axios.get('api/instructions')
    instrucciones.value = Array.isArray(res?.data?.member) ? res.data.member : []
  } catch (e) {
    console.warn('No se pudieron cargar las instrucciones', e)
    instrucciones.value = []
  }
}

onMounted(() => fetchInstructions())
</script>

<style scoped>
</style>
