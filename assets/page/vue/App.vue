<template>
  <div class="vue-app">
    <SaludoForma />
    <template v-if="loaded && hasActive">
      <!-- Paso 1: formulario de datos personales -->
      <Personal />
      <!-- Paso 2: mostrar términos si no han sido aceptados; o Formation si ya están aceptados -->
      <FormaContent v-if="store.responseData && store.responseData.value" />
    </template>
  </div>
</template>

<script setup>
import SaludoForma from "./components/SaludoForma.vue";
import Personal from "./components/Personal.vue";
import FormaContent from "./components/FormaContent.vue";

import { ref, onMounted } from 'vue'
import axios from 'axios'
import { store } from '../assets/almacen'

const hasActive = ref(false)
const loaded = ref(false)

// expose store to template (script setup exposes imports by default)

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

onMounted(() => {
  try { store.hydrate(); } catch(e) { /* noop */ }
  fetchEstado()
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