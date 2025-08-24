<template>
  <div class="admin-inicio-wrapper">
    <h2>Gestión de Inicio</h2>
    <p>Use los siguientes accesos para administrar los contenidos de Inicio (controlador Symfony InicioController).</p>

    <div class="links">
      <a class="link" href="/admin/inicio/">Ver listado</a>
      <a class="link" href="/admin/inicio/new">Crear nuevo</a>
    </div>

    <v-alert type="info" density="comfortable" class="mt-3">
      Estas vistas son proporcionadas por Symfony (Twig). El acceso requiere estar autenticado.
    </v-alert>
  </div>
</template>

<script setup>
// Este componente sirve como punto de acceso desde el panel Admin hacia las rutas
// del controlador Symfony /admin/inicio administradas por InicioController.
</script>

<style scoped>
.admin-inicio-wrapper { max-width: 900px; margin: 1.5rem auto; padding: 0 0.5rem; }
.links { display: flex; gap: 0.75rem; margin-top: 0.75rem; }
.link { text-decoration: none; color: #2f6feb; border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; border-radius: 8px; background: #fff; }
.link:hover { background: #f8fafc; }
.mt-3 { margin-top: 1rem; }
</style>
