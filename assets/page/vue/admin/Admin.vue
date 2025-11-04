<template>
  <div class="admin-wrapper">
    <template v-if="isFHRoute">
      <FormulariosHabilitacion />
    </template>
    <template v-else-if="isInstructionsRoute">
      <Instrucciones />
    </template>
    <template v-else-if="isInstructionsEditRoute">
      <InstruccionesEditar :id="instructionId" />
    </template>
    <template v-else-if="isDetalleOrientacionRoute">
      <DetalleOrientacion />
    </template>
    <template v-else-if="isHabilidadesRoute">
      <Habilidades />
    </template>
    <template v-else-if="isPersonalidadRoute">
      <Personalidad />
    </template>
    <template v-else-if="isIntroExtroRoute">
      <IntroExtro />
    </template>
    <template v-else>
      <h1 class="title">Panel de Administración</h1>
      <p class="subtitle">Acceso a las distintas configuraciones</p>

      <div class="cards">
        <a class="card" href="/admin/inicio">
          <div class="card-body">
            <h2>Inicio</h2>
            <p>Gestionar contenidos de Inicio (InicioController)</p>
          </div>
        </a>

        <a class="card" href="/admin#instrucciones">
          <div class="card-body">
            <h2>Instrucciones</h2>
            <p>Administrar instrucciones (InstructionsController)</p>
          </div>
        </a>

        <a class="card" href="/admin#personalidad">
          <div class="card-body">
            <h2>Personalidad</h2>
            <p>Administrar configuraciones de Personalidad (PersonalidadController)</p>
          </div>
        </a>

        <a class="card" href="/admin#intro-extro">
          <div class="card-body">
            <h2>Intro/Extro</h2>
            <p>Administrar configuraciones de IntroExtro (IntroExtroController)</p>
          </div>
        </a>

        <a class="card" href="/admin#formularios-habilitacion">
          <div class="card-body">
            <h2>Formularios de Habilitación</h2>
            <p>Crear, editar y deshabilitar formularios habilitados (FormularioHabilitacion)</p>
          </div>
        </a>

        <a class="card" href="/admin#detalle-orientacion">
          <div class="card-body">
            <h2>Detalle de Orientación</h2>
            <p>Administrar DetalleOrientacion (DetalleOrientacionController)</p>
          </div>
        </a>

        <a class="card" href="/admin#habilidades">
          <div class="card-body">
            <h2>Habilidades</h2>
            <p>Administrar Habilidades (HabilidadesController)</p>
          </div>
        </a>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import FormulariosHabilitacion from './FormulariosHabilitacion.vue'
import Instrucciones from './Instrucciones.vue'
import InstruccionesEditar from './InstruccionesEditar.vue'
import DetalleOrientacion from './DetalleOrientacion.vue'
import Habilidades from './Habilidades.vue'
import Personalidad from './Personalidad.vue'
import IntroExtro from './IntroExtro.vue'

const currentHash = ref(typeof window !== 'undefined' ? window.location.hash : '')

function onHashChange() {
  currentHash.value = window.location.hash
}

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('hashchange', onHashChange)
  }
})

onBeforeUnmount(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('hashchange', onHashChange)
  }
})

const isFHRoute = computed(() => currentHash.value === '#formularios-habilitacion')
const isInstructionsRoute = computed(() => currentHash.value === '#instrucciones')
const isInstructionsEditRoute = computed(() => currentHash.value.startsWith('#instrucciones-editar:'))
const isDetalleOrientacionRoute = computed(() => currentHash.value === '#detalle-orientacion')
const isHabilidadesRoute = computed(() => currentHash.value === '#habilidades')
const isPersonalidadRoute = computed(() => currentHash.value === '#personalidad')
const isIntroExtroRoute = computed(() => currentHash.value === '#intro-extro')
const instructionId = computed(() => {
  if (!isInstructionsEditRoute.value) return ''
  return currentHash.value.split(':')[1] || ''
})
</script>

<style scoped>
.admin-wrapper {
  max-width: 1280px;
  margin: 2rem auto;
  padding: 1rem;
}
.title {
  margin: 0 0 0.25rem 0;
}
.subtitle {
  margin: 0 0 1.5rem 0;
  color: #666;
}
.cards {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1rem;
}
.card {
  text-decoration: none;
  color: inherit;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  background: #fff;
  transition: box-shadow 0.2s ease, transform 0.1s ease;
}
.card:hover {
  box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  transform: translateY(-1px);
}
.card-body {
  padding: 1rem 1.25rem;
}
.card-body h2 {
  margin: 0 0 0.5rem 0;
  font-size: 1.25rem;
}
.card-body p {
  margin: 0;
  color: #555;
}
</style>
