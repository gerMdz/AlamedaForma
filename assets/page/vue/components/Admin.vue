<template>
  <div class="admin-wrapper">
    <h1 class="title">Panel de Administración</h1>
    <p class="subtitle">Acceso a las distintas configuraciones</p>

    <div class="cards">
      <a class="card" href="/inicios">
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
    </div>
  </div>
</template>

<script setup>
// No special logic needed for simple navigation cards.
</script>

<style scoped>
.admin-wrapper {
  max-width: 1000px;
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
