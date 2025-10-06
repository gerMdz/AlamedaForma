<template>
  <div class="habilidades-admin">
    <h1 class="mb-3">Administrar Habilidades</h1>

    <v-alert v-if="error" type="error" density="compact" class="mb-3">{{ error }}</v-alert>
    <v-alert v-if="notice" type="success" density="compact" class="mb-3">{{ notice }}</v-alert>

    <v-card class="mb-4">
      <v-card-title>Nueva habilidad</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="createForm.nombre" label="Nombre" density="comfortable" />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="createForm.discripcion" label="Discripcion" density="comfortable" />
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field v-model="createForm.identificador" label="Identificador (único)" density="comfortable" />
          </v-col>
        </v-row>
        <v-btn color="primary" @click="crear" :loading="loadingCreate">Crear</v-btn>
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-title class="d-flex align-center justify-space-between flex-wrap" style="gap:8px;">
        <span>Listado</span>
        <v-btn variant="tonal" @click="cargar" :loading="loading">Refrescar</v-btn>
      </v-card-title>
      <v-card-text>
        <v-skeleton-loader v-if="loading && items.length === 0" type="table" class="mb-3" />

        <v-table v-if="items.length" density="compact">
          <thead>
            <tr>
              <th>Nombre</th>
              <th>Discripcion</th>
              <th>Identificador</th>
              <th>Estado</th>
              <th style="width:320px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="it in items" :key="it.id">
              <td>
                <v-text-field v-model="it.edit.nombre" density="compact" hide-details />
              </td>
              <td>
                <v-text-field v-model="it.edit.discripcion" density="compact" hide-details />
              </td>
              <td>
                <v-text-field v-model="it.edit.identificador" density="compact" hide-details />
              </td>
              <td>
                <v-chip :color="it.isDeleted ? 'red' : 'green'" size="small">{{ it.isDeleted ? 'Eliminada' : 'Activa' }}</v-chip>
              </td>
              <td>
                <div class="d-flex align-center" style="gap:8px; flex-wrap: wrap;">
                  <v-btn size="small" color="secondary" variant="tonal" @click="ver(it)">Ver</v-btn>
                  <v-btn size="small" color="primary" variant="tonal" @click="guardar(it)" :loading="it.saving" :disabled="it.isDeleted">Guardar</v-btn>
                  <v-btn size="small" :color="it.isDeleted ? 'success' : 'warning'" variant="tonal" @click="toggleDelete(it)" :loading="it.disabling">
                    {{ it.isDeleted ? 'Restaurar' : 'Eliminar' }}
                  </v-btn>
                  <v-btn size="small" variant="text" @click="resetEdits(it)">Descartar</v-btn>
                </div>
              </td>
            </tr>
          </tbody>
        </v-table>

        <v-alert v-else-if="!loading" type="info" density="compact" variant="tonal">No hay registros.</v-alert>
      </v-card-text>
    </v-card>

    <!-- Modal de detalle/edición para mostrar todo el registro -->
    <v-dialog v-model="showDialog" max-width="720">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <span>Detalle de la habilidad</span>
          <v-btn icon="mdi-close" variant="text" @click="showDialog = false" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <div v-if="dialogItem">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="dialogEdit.nombre" label="Nombre" :readonly="dialogItem.isDeleted" />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field v-model="dialogEdit.identificador" label="Identificador" :readonly="dialogItem.isDeleted" />
              </v-col>
              <v-col cols="12">
                <v-textarea v-model="dialogEdit.discripcion" label="Discripcion" rows="6" auto-grow :readonly="dialogItem.isDeleted" />
              </v-col>
              <v-col cols="12">
                <div class="d-flex align-center" style="gap:8px;">
                  <span>Estado:</span>
                  <v-chip :color="dialogItem.isDeleted ? 'red' : 'green'" size="small">
                    {{ dialogItem.isDeleted ? 'Eliminada' : 'Activa' }}
                  </v-chip>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="showDialog=false">Cerrar</v-btn>
          <v-btn color="primary" :disabled="!dialogItem || dialogItem.isDeleted" :loading="dialogSaving" @click="guardarDesdeDialog">
            Guardar
          </v-btn>
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

const loadingCreate = ref(false)
const createForm = ref({ nombre: '', discripcion: '', identificador: '' })

// Estado del modal
const showDialog = ref(false)
const dialogItem = ref(null)
const dialogEdit = ref({ nombre: '', discripcion: '', identificador: '' })
const dialogSaving = ref(false)

function normalize(item) {
  return {
    id: item.id,
    nombre: item.nombre ?? '',
    discripcion: item.discripcion ?? '',
    identificador: item.identificador ?? '',
    isDeleted: !!item.isDeleted,
    deletedAt: item.deletedAt ?? null,
  }
}

function withEdit(raw) {
  return {
    ...raw,
    edit: { nombre: raw.nombre, discripcion: raw.discripcion, identificador: raw.identificador },
    saving: false,
    disabling: false,
  }
}

async function cargar() {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/admin/habilidades/')
    const data = Array.isArray(res.data) ? res.data : []
    items.value = data.map(d => withEdit(normalize(d)))
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado.'
  } finally {
    loading.value = false
  }
}

function ver(it) {
  dialogItem.value = it
  dialogEdit.value = {
    nombre: it.edit.nombre,
    discripcion: it.edit.discripcion,
    identificador: it.edit.identificador,
  }
  showDialog.value = true
}

function resetEdits(it) {
  it.edit.nombre = it.nombre
  it.edit.discripcion = it.discripcion
  it.edit.identificador = it.identificador
}

async function guardar(it) {
  it.saving = true
  error.value = ''
  notice.value = ''
  try {
    await axios.patch(`/admin/habilidades/${it.id}`, {
      nombre: it.edit.nombre,
      discripcion: it.edit.discripcion,
      identificador: it.edit.identificador,
    })
    it.nombre = it.edit.nombre
    it.discripcion = it.edit.discripcion
    it.identificador = it.edit.identificador
    notice.value = 'Cambios guardados.'
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo guardar los cambios.'
  } finally {
    it.saving = false
  }
}

async function guardarDesdeDialog() {
  if (!dialogItem.value) return
  dialogSaving.value = true
  // sincronizar los cambios del modal con la fila edit
  dialogItem.value.edit.nombre = dialogEdit.value.nombre
  dialogItem.value.edit.discripcion = dialogEdit.value.discripcion
  dialogItem.value.edit.identificador = dialogEdit.value.identificador
  try {
    await guardar(dialogItem.value)
    showDialog.value = false
  } finally {
    dialogSaving.value = false
  }
}

async function toggleDelete(it) {
  it.disabling = true
  error.value = ''
  notice.value = ''
  try {
    if (it.isDeleted) {
      await axios.put(`/admin/habilidades/${it.id}/restore`)
      it.isDeleted = false
      it.deletedAt = null
      notice.value = 'Habilidad restaurada.'
    } else {
      await axios.delete(`/admin/habilidades/${it.id}`)
      it.isDeleted = true
      it.deletedAt = new Date().toISOString()
      notice.value = 'Habilidad eliminada.'
    }
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo completar la acción.'
  } finally {
    it.disabling = false
  }
}

async function crear() {
  loadingCreate.value = true
  error.value = ''
  notice.value = ''
  try {
    const payload = {
      nombre: createForm.value.nombre?.trim() || '',
      discripcion: createForm.value.discripcion?.trim() || '',
      identificador: createForm.value.identificador?.trim() || '',
    }
    if (!payload.nombre || !payload.discripcion || !payload.identificador) {
      error.value = 'Complete nombre, discripcion e identificador.'
      loadingCreate.value = false
      return
    }
    const res = await axios.post('/admin/habilidades/', payload)
    const id = res.data?.id
    if (id) {
      // recargar listado
      await cargar()
      // limpiar formulario
      createForm.value = { nombre: '', discripcion: '', identificador: '' }
      notice.value = 'Habilidad creada.'
    } else {
      error.value = 'No se pudo crear la habilidad.'
    }
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo crear la habilidad.'
  } finally {
    loadingCreate.value = false
  }
}

onMounted(cargar)
</script>

<style scoped>
.habilidades-admin { padding-top: 16px; padding-bottom: 16px; }
</style>
