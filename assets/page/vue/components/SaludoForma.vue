<template>
  <v-container v-if="loaded && !hasActive" fluid class="landing-hero">
    <div class="hero-inner">
      <h1 class="hero-title">
        Muy pronto aquí encontrarás  una herramienta que te ayudará a conocer tu diseño dado por Dios, es decir tu F.O.R.M.A
      </h1>
    </div>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const hasActive = ref(false)
const loaded = ref(false)

const fetchEstado = async () => {
  try {
    const res = await axios.get('/api/formularios-habilitacion/activo')
    hasActive.value = !!res.data?.hasActive
  } catch (e) {
    // On error, be safe and show greeting (assume no active)
    console.error('Error consultando formularios habilitados', e)
    hasActive.value = false
  } finally {
    loaded.value = true
  }
}

onMounted(fetchEstado)
</script>

<style scoped>
.landing-hero {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: radial-gradient(1200px 600px at 20% 20%, #f5f7ff 0%, #ffffff 60%);
}
.hero-inner {
  max-width: 1000px;
  text-align: center;
}
.hero-title {
  margin: 0;
  font-size: clamp(1.75rem, 2.5vw + 1rem, 3rem);
  line-height: 1.25;
  font-weight: 700;
  color: #2c3e50;
}
</style>
