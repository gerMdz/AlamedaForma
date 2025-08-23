<template>
  <div class="admin-form-wrapper">
    <h2>Formulario Habilitación - Administración</h2>

    <v-card class="mb-4">
      <v-card-title>Crear nueva habilitación</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.nombreFormulario" label="Nombre del formulario"></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.activoDesde" type="datetime-local" label="Activo desde"></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.activoHasta" type="datetime-local" label="Activo hasta (opcional)"></v-text-field>
          </v-col>
        </v-row>
        <v-btn color="primary" @click="crear">Crear</v-btn>
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-title>Listado (API Platform)</v-card-title>
      <v-card-text>
        <v-alert v-if="error" type="error" density="compact" class="mb-2">{{ error }}</v-alert>
        <v-table>
          <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Desde</th>
            <th>Hasta</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="item in items" :key="item.id">
            <td>{{ item.id }}</td>
            <td>{{ item.nombreFormulario }}</td>
            <td>{{ item.activoDesde }}</td>
            <td>{{ item.activoHasta }}</td>
          </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const error = ref('')

const form = ref({
  nombreFormulario: '',
  activoDesde: '', // datetime-local
  activoHasta: '', // datetime-local
})

const toIsoOrNull = (val) => {
  if (!val) return null
  // datetime-local produces 'YYYY-MM-DDTHH:mm'
  // Append seconds and timezone (assume local) -> use new Date to create ISO
  const d = new Date(val)
  return d.toISOString()
}

const cargar = async () => {
  try {
    const res = await axios.get('/api/formulario_habilitacions')
    // API Platform JSON-LD returns { "hydra:member": [] } or "member" depending config; try both
    const member = res.data['hydra:member'] ?? res.data['member'] ?? []
    items.value = member.map(m => ({
      id: m.id ?? m['@id'] ?? '',
      nombreFormulario: m.nombreFormulario,
      activoDesde: m.activoDesde,
      activoHasta: m.activoHasta,
    }))
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado.'
  }
}

const crear = async () => {
  error.value = ''
  try {
    const payload = {
      nombreFormulario: form.value.nombreFormulario,
      activoDesde: toIsoOrNull(form.value.activoDesde),
      activoHasta: toIsoOrNull(form.value.activoHasta),
    }
    await axios.post('/api/formulario_habilitacions', payload, {
      headers: { 'Content-Type': 'application/json' }
    })
    await cargar()
    form.value = { nombreFormulario: '', activoDesde: '', activoHasta: '' }
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo crear la habilitación.'
  }
}

onMounted(cargar)
</script>

<style scoped>
.admin-form-wrapper { max-width: 1100px; margin: 1rem auto; padding: 0 0.5rem; }
.mb-4 { margin-bottom: 1rem; }
</style>
