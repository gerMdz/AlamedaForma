<template>
  <div class="antecedentes-admin">
    <h1 class="mb-3">Administrar Antecedentes</h1>

    <v-alert v-if="error" type="error" density="compact" class="mb-3">{{ error }}</v-alert>
    <v-alert v-if="notice" type="success" density="compact" class="mb-3">{{ notice }}</v-alert>

    <v-card class="mb-4">
      <v-card-title>Nuevo antecedente</v-card-title>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="4">
            <v-text-field v-model="createForm.label" label="Etiqueta (Label)" density="comfortable" />
          </v-col>
          <v-col cols="12" md="4">
            <v-checkbox v-model="createForm.esTitulo" label="Es Título" density="comfortable" hide-details />
          </v-col>
          <v-col cols="12" md="4">
            <v-checkbox v-model="createForm.activo" label="Activo" density="comfortable" hide-details />
          </v-col>
          <v-col cols="12">
            <v-textarea v-model="createForm.consulta" label="Pregunta / Consulta" rows="2" auto-grow density="comfortable" />
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
              <th>Etiqueta (Label)</th>
              <th>Consulta</th>
              <th style="width: 100px;">¿Título?</th>
              <th style="width: 100px;">Estado</th>
              <th style="width:320px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="it in items" :key="it.id">
              <td>
                <v-text-field v-model="it.edit.label" density="compact" hide-details />
              </td>
              <td>
                <v-text-field v-model="it.edit.consulta" density="compact" hide-details />
              </td>
              <td>
                <v-checkbox v-model="it.edit.esTitulo" density="compact" hide-details @change="guardar(it)" />
              </td>
              <td>
                <v-chip :color="it.activo ? 'green' : 'red'" size="small" @click="toggleActivo(it)" style="cursor: pointer">
                  {{ it.activo ? 'Activo' : 'Inactivo' }}
                </v-chip>
              </td>
              <td>
                <div class="d-flex align-center" style="gap:8px; flex-wrap: wrap;">
                  <v-btn size="small" color="secondary" variant="tonal" @click="ver(it)">Ver</v-btn>
                  <v-btn size="small" color="primary" variant="tonal" @click="guardar(it)" :loading="it.saving">Guardar</v-btn>
                  <v-btn size="small" :color="it.activo ? 'warning' : 'success'" variant="tonal" @click="toggleActivo(it)" :loading="it.disabling">
                    {{ it.activo ? 'Desactivar' : 'Activar' }}
                  </v-btn>
                  <v-btn size="small" color="error" variant="text" @click="eliminar(it)" :loading="it.deleting">Eliminar</v-btn>
                  <v-btn size="small" variant="text" @click="resetEdits(it)">Descartar</v-btn>
                </div>
              </td>
            </tr>
          </tbody>
        </v-table>

        <v-alert v-else-if="!loading" type="info" density="compact" variant="tonal">No hay registros.</v-alert>
      </v-card-text>
    </v-card>

    <!-- Modal de detalle/edición -->
    <v-dialog v-model="showDialog" max-width="720">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <span>Detalle del antecedente</span>
          <v-btn icon="mdi-close" variant="text" @click="showDialog = false" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <div v-if="dialogItem">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field v-model="dialogEdit.label" label="Etiqueta (Label)" />
              </v-col>
              <v-col cols="12" md="3">
                <v-checkbox v-model="dialogEdit.esTitulo" label="Es Título" />
              </v-col>
              <v-col cols="12" md="3">
                <v-switch v-model="dialogEdit.activo" label="Activo" color="success" />
              </v-col>
              <v-col cols="12">
                <v-textarea v-model="dialogEdit.consulta" label="Pregunta / Consulta" rows="6" auto-grow />
              </v-col>
            </v-row>
          </div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="text" @click="showDialog=false">Cerrar</v-btn>
          <v-btn color="primary" :disabled="!dialogItem" :loading="dialogSaving" @click="guardarDesdeDialog">
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
const createForm = ref({ label: '', consulta: '', activo: true, esTitulo: false })

// Estado del modal
const showDialog = ref(false)
const dialogItem = ref(null)
const dialogEdit = ref({ label: '', consulta: '', activo: true, esTitulo: false })
const dialogSaving = ref(false)

function normalize(item) {
  return {
    id: item.id,
    label: item.label ?? '',
    consulta: item.consulta ?? '',
    activo: !!item.activo,
    esTitulo: !!item.esTitulo,
  }
}

function withEdit(raw) {
  return {
    ...raw,
    edit: { label: raw.label, consulta: raw.consulta, activo: raw.activo, esTitulo: raw.esTitulo },
    saving: false,
    disabling: false,
    deleting: false,
  }
}

async function cargar() {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/admin/antecedentes/')
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
    label: it.edit.label,
    consulta: it.edit.consulta,
    activo: it.edit.activo,
    esTitulo: it.edit.esTitulo,
  }
  showDialog.value = true
}

function resetEdits(it) {
  it.edit.label = it.label
  it.edit.consulta = it.consulta
  it.edit.activo = it.activo
  it.edit.esTitulo = it.esTitulo
}

async function guardar(it) {
  it.saving = true
  error.value = ''
  notice.value = ''
  try {
    await axios.patch(`/admin/antecedentes/${it.id}`, {
      label: it.edit.label,
      consulta: it.edit.consulta,
      activo: it.edit.activo,
      esTitulo: it.edit.esTitulo,
    })
    it.label = it.edit.label
    it.consulta = it.edit.consulta
    it.activo = it.edit.activo
    it.esTitulo = it.edit.esTitulo
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
  dialogItem.value.edit.label = dialogEdit.value.label
  dialogItem.value.edit.consulta = dialogEdit.value.consulta
  dialogItem.value.edit.activo = dialogEdit.value.activo
  dialogItem.value.edit.esTitulo = dialogEdit.value.esTitulo
  try {
    await guardar(dialogItem.value)
    showDialog.value = false
  } finally {
    dialogSaving.value = false
  }
}

async function toggleActivo(it) {
  it.disabling = true
  error.value = ''
  notice.value = ''
  try {
    const res = await axios.patch(`/admin/antecedentes/${it.id}/toggle-activo`)
    it.activo = res.data.activo
    it.edit.activo = res.data.activo
    notice.value = it.activo ? 'Antecedente activado.' : 'Antecedente desactivado.'
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cambiar el estado.'
  } finally {
    it.disabling = false
  }
}

async function eliminar(it) {
  if (!confirm('¿Está seguro de eliminar este antecedente?')) return
  it.deleting = true
  error.value = ''
  notice.value = ''
  try {
    await axios.delete(`/admin/antecedentes/${it.id}`)
    items.value = items.value.filter(i => i.id !== it.id)
    notice.value = 'Antecedente eliminado.'
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo eliminar el registro.'
  } finally {
    it.deleting = false
  }
}

async function crear() {
  loadingCreate.value = true
  error.value = ''
  notice.value = ''
  try {
    const payload = {
      label: createForm.value.label?.trim() || '',
      consulta: createForm.value.consulta?.trim() || '',
      activo: !!createForm.value.activo,
      esTitulo: !!createForm.value.esTitulo,
    }
    if (!payload.label || !payload.consulta) {
      error.value = 'Complete la etiqueta y la consulta.'
      loadingCreate.value = false
      return
    }
    const res = await axios.post('/admin/antecedentes/', payload)
    const id = res.data?.id
    if (id) {
      await cargar()
      createForm.value = { label: '', consulta: '', activo: true, esTitulo: false }
      notice.value = 'Antecedente creado.'
    } else {
      error.value = 'No se pudo crear el antecedente.'
    }
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo crear el antecedente.'
  } finally {
    loadingCreate.value = false
  }
}

onMounted(cargar)
</script>

<style scoped>
.antecedentes-admin { padding-top: 16px; padding-bottom: 16px; }
</style>
