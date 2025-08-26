<template>
  <div class="fh-admin">
    <h1 class="mb-3">Administrar Formularios de Habilitación</h1>

    <v-alert v-if="error" type="error" density="compact" class="mb-3">{{ error }}</v-alert>
    <v-alert v-if="notice" type="success" density="compact" class="mb-3">{{ notice }}</v-alert>

    <v-card class="mb-4">
      <v-card-title>Nueva habilitación</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="form.nombreFormulario" label="Nombre" class="name-input" />
          </v-col>
          <v-col cols="12" md="2">
            <v-text-field v-model="form.identifier" label="Identificador (opcional)" class="id-input" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="form.activoDesde" type="datetime-local" label="Activo desde" class="date-input" />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="form.activoHasta" type="datetime-local" label="Activo hasta (opcional)" class="date-input" />
          </v-col>
        </v-row>
        <v-btn color="primary" @click="crear" :loading="loading">Crear</v-btn>
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-title>Listado</v-card-title>
      <v-card-text>
        <v-table density="compact">
          <thead>
          <tr>
            <th>Nombre</th>
            <th>Identificador</th>
            <th>Desde</th>
            <th>Hasta</th>
            <th>Activo</th>
            <th style="width:320px;">Acciones</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="item in items" :key="item.id">
            <td>
              <v-text-field v-model="item.edit.nombreFormulario" density="compact" hide-details class="name-input" />
            </td>
            <td>
              <v-text-field v-model="item.edit.identifier" density="compact" hide-details class="id-input" />
            </td>
            <td>
              <v-text-field v-model="item.edit.activoDesde" type="datetime-local" density="compact" hide-details class="date-input" />
            </td>
            <td>
              <v-text-field v-model="item.edit.activoHasta" type="datetime-local" density="compact" hide-details class="date-input" />
            </td>
            <td>
              <v-chip :color="item.estaActivo ? 'green' : 'grey'" size="small">{{ item.estaActivo ? 'Sí' : 'No' }}</v-chip>
            </td>
            <td>
              <v-btn size="small" color="secondary" variant="tonal" class="mr-2" @click="ver(item)">Ver</v-btn>
              <v-btn size="small" color="primary" variant="tonal" class="mr-2" @click="guardar(item)" :loading="item.saving">Guardar</v-btn>
              <v-btn
                size="small"
                :color="item.edit.activoHasta ? 'success' : 'warning'"
                variant="tonal"
                class="mr-2"
                @click="toggleHabilitacion(item)"
                :loading="item.disabling"
              >{{ item.edit.activoHasta ? 'Habilitar' : 'Deshabilitar' }}</v-btn>
              <v-btn size="small" variant="text" @click="resetEdits(item)">Descartar</v-btn>
            </td>
          </tr>
          </tbody>
        </v-table>
      </v-card-text>
    </v-card>

    <v-dialog v-model="showDialog" max-width="600">
      <v-card>
        <v-card-title>Detalle de la habilitación</v-card-title>
        <v-card-text>
          <v-alert v-if="dialogError" type="error" density="compact" class="mb-3">{{ dialogError }}</v-alert>
          <div v-if="dialogLoading">Cargando...</div>
          <div v-else-if="dialogItem">
            <p><strong>ID:</strong> {{ dialogItem.id }}</p>
            <p><strong>Identificador:</strong> {{ dialogItem.identifier || defaultIdentifier(dialogItem.nombreFormulario) }}</p>
            <p><strong>Nombre:</strong> {{ dialogItem.nombreFormulario }}</p>
            <p><strong>Activo desde:</strong> {{ formatEs(dialogItem.activoDesde) }}</p>
            <p><strong>Activo hasta:</strong> {{ formatEs(dialogItem.activoHasta) || '—' }}</p>
            <p><strong>Activo ahora:</strong> {{ dialogItem.estaActivo ? 'Sí' : 'No' }}</p>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="showDialog = false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(false)
const error = ref('')
const notice = ref('')

const showDialog = ref(false)
const dialogLoading = ref(false)
const dialogError = ref('')
const dialogItem = ref(null)

const form = ref({
  nombreFormulario: '',
  identifier: '',
  activoDesde: '', // datetime-local
  activoHasta: '', // datetime-local
})

function toIso(val) {
  if (!val) return null
  const d = new Date(val)
  return d.toISOString()
}

function toLocalInputValue(iso) {
  if (!iso) return ''
  const d = new Date(iso)
  const pad = (n) => String(n).padStart(2, '0')
  const y = d.getFullYear()
  const m = pad(d.getMonth() + 1)
  const da = pad(d.getDate())
  const h = pad(d.getHours())
  const mi = pad(d.getMinutes())
  return `${y}-${m}-${da}T${h}:${mi}`
}

function formatEs(iso) {
  if (!iso) return ''
  try {
    const d = new Date(iso)
    return new Intl.DateTimeFormat('es-AR', { dateStyle: 'short', timeStyle: 'short' }).format(d)
  } catch (e) {
    return ''
  }
}

function defaultIdentifier(nombre) {
  if (!nombre) return ''
  const s = String(nombre).trim()
  return s.length ? s.charAt(0).toUpperCase() : ''
}

function applyEditFields(entity) {
  return {
    ...entity,
    edit: {
      nombreFormulario: entity.nombreFormulario,
      identifier: entity.identifier || '',
      activoDesde: toLocalInputValue(entity.activoDesde),
      activoHasta: toLocalInputValue(entity.activoHasta),
    },
    saving: false,
    disabling: false,
  }
}

async function cargar() {
  error.value = ''
  try {
    const res = await axios.get('/admin/formularios-habilitacion')
    items.value = res.data.map(applyEditFields)
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado.'
  }
}

async function crear() {
  error.value = ''
  notice.value = ''
  loading.value = true
  try {
    const idn = form.value.identifier && form.value.identifier.trim() !== ''
      ? form.value.identifier.trim()
      : defaultIdentifier(form.value.nombreFormulario)
    const payload = {
      nombreFormulario: form.value.nombreFormulario,
      identifier: idn || null,
      activoDesde: toIso(form.value.activoDesde),
      activoHasta: toIso(form.value.activoHasta),
    }
    await axios.post('/admin/formularios-habilitacion', payload, { headers: { 'Content-Type': 'application/json' } })
    notice.value = 'Creado correctamente.'
    form.value = { nombreFormulario: '', identifier: '', activoDesde: '', activoHasta: '' }
    await cargar()
  } catch (e) {
    console.error(e)
    error.value = e?.response?.data?.error ?? 'No se pudo crear.'
  } finally {
    loading.value = false
  }
}

function resetEdits(item) {
  item.edit.nombreFormulario = item.nombreFormulario
  item.edit.identifier = item.identifier || ''
  item.edit.activoDesde = toLocalInputValue(item.activoDesde)
  item.edit.activoHasta = toLocalInputValue(item.activoHasta)
}

async function guardar(item) {
  item.saving = true
  error.value = ''
  notice.value = ''
  try {
    const idn = item.edit.identifier && item.edit.identifier.trim() !== ''
      ? item.edit.identifier.trim()
      : defaultIdentifier(item.edit.nombreFormulario)
    const payload = {
      nombreFormulario: item.edit.nombreFormulario,
      identifier: idn || null,
      activoDesde: toIso(item.edit.activoDesde),
      activoHasta: item.edit.activoHasta ? toIso(item.edit.activoHasta) : null,
    }
    const url = `/admin/formularios-habilitacion/${encodeURIComponent(item.id)}`
    await axios.put(url, payload, { headers: { 'Content-Type': 'application/json' } })
    notice.value = 'Guardado.'
    await cargar()
  } catch (e) {
    console.error(e)
    error.value = e?.response?.data?.error ?? 'No se pudo guardar.'
  } finally {
    item.saving = false
  }
}

async function toggleHabilitacion(item) {
  item.disabling = true
  error.value = ''
  notice.value = ''
  try {
    if (item.edit.activoHasta) {
      // Habilitar: limpiar fecha hasta
      const url = `/admin/formularios-habilitacion/${encodeURIComponent(item.id)}`
      await axios.put(url, { activoHasta: null }, { headers: { 'Content-Type': 'application/json' } })
      notice.value = 'Habilitado.'
    } else {
      // Deshabilitar: endpoint dedicado
      const url = `/admin/formularios-habilitacion/${encodeURIComponent(item.id)}/deshabilitar`
      await axios.post(url)
      notice.value = 'Deshabilitado.'
    }
    await cargar()
  } catch (e) {
    console.error(e)
    error.value = e?.response?.data?.error ?? 'No se pudo cambiar el estado.'
  } finally {
    item.disabling = false
  }
}

async function ver(item) {
  dialogError.value = ''
  dialogItem.value = null
  showDialog.value = true
  dialogLoading.value = true
  try {
    const url = `/admin/formularios-habilitacion/${encodeURIComponent(item.id)}`
    const res = await axios.get(url)
    dialogItem.value = res.data
  } catch (e) {
    console.error(e)
    dialogError.value = e?.response?.data?.error ?? 'No se pudo obtener el detalle.'
  } finally {
    dialogLoading.value = false
  }
}

onMounted(cargar)
</script>

<style scoped>
.fh-admin { max-width: 1280px; margin: 1rem auto; padding: 0 0.5rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mb-4 { margin-bottom: 1rem; }
.mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-size: 12px; }
.mr-2 { margin-right: 0.5rem; }
.name-input { min-width: 360px; }
.date-input { max-width: 200px; }
.id-input { max-width: 120px; }
</style>
