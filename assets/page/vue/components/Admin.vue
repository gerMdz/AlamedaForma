<template>
  <div class="admin-wrapper">
    <template v-if="isFHRoute">
      <FormulariosHabilitacion />
    </template>
    <template v-else>
      <h1 class="title">Panel de Administración</h1>
      <p class="subtitle">Acceso a las distintas configuraciones</p>

      <div class="cards">
        <a class="card" href="/inicios/list">
          <div class="card-body">
            <h2>Inicios</h2>
            <p>Gestionar contenidos de Inicio (InicioApiController)</p>
          </div>
        </a>

        <a class="card" href="/personalidad">
          <div class="card-body">
            <h2>Personalidad</h2>
            <p>Administrar configuraciones de Personalidad (PersonalidadController)</p>
          </div>
        </a>

        <a class="card" href="/admin#formularios-habilitacion">
          <div class="card-body">
            <h2>Formularios de Habilitación</h2>
            <p>Crear, editar y deshabilitar formularios habilitados (FormularioHabilitacion)</p>
          </div>
        </a>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import FormulariosHabilitacion from "../admin/FormulariosHabilitacion.vue"

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
