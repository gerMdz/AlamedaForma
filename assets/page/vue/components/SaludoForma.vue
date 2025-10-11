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
import { defineProps } from 'vue'

const props = defineProps({
  hasActive: { type: Boolean, default: false },
  loaded: { type: Boolean, default: false }
})

const hasActive = props.hasActive
const loaded = props.loaded
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
