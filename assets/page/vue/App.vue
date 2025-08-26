<template>
  <div class="vue-app">
    <SaludoForma />
    <template v-if="loaded && hasActive">
      <Personal />
      <FormaContent />
    </template>
  </div>
</template>

<script setup>
import SaludoForma from "./components/SaludoForma.vue";
import Personal from "./components/Personal.vue";
import FormaContent from "./components/FormaContent.vue";

import { ref, onMounted } from 'vue'
import axios from 'axios'

const hasActive = ref(false)
const loaded = ref(false)

const fetchEstado = async () => {
  try {
    const res = await axios.get('/api/formularios-habilitacion/activo')
    hasActive.value = !!res.data?.hasActive
  } catch (e) {
    console.error('Error consultando formularios habilitados', e)
    hasActive.value = false
  } finally {
    loaded.value = true
  }
}

onMounted(fetchEstado)
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