<template>
  <v-container class="admin-detalle-orientacion" fluid>
    <v-row class="justify-center">
      <v-col cols="12" md="11" lg="10">
        <v-card class="pa-2 pa-sm-4">
          <v-card-title class="d-flex align-center justify-space-between flex-wrap" style="gap: 8px;">
            <span class="text-h6">Gestión de Detalle de Orientación</span>
            <div class="d-flex align-center" style="gap: 8px;">
              <v-btn variant="text" :href="'/admin'">
                <v-icon icon="mdi-arrow-left" class="mr-1" />
                <span>Volver al Panel</span>
              </v-btn>
              <v-btn color="primary" variant="elevated" @click="abrirNuevo">
                <v-icon icon="mdi-plus" class="mr-1" />
                <span>Nuevo</span>
              </v-btn>
              <v-btn variant="tonal" @click="cargar" :loading="loading">
                <v-icon icon="mdi-refresh" class="mr-1" />
                <span>Refrescar</span>
              </v-btn>
            </div>
          </v-card-title>
          <v-card-text>
            <v-alert v-if="error" type="error" density="comfortable" class="mb-3">{{ error }}</v-alert>
            <v-skeleton-loader v-if="loading && items.length === 0" type="table" class="mb-3" />
            <v-table v-if="items.length">
              <thead>
                <tr>
                  <th class="text-nowrap">Orden</th>
                  <th>Descripción</th>
                  <th class="text-nowrap">Identifier</th>
                  <th class="text-nowrap">Estado</th>
                  <th style="width: 260px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="it in items" :key="it.id">
                  <td>{{ it.orden }}</td>
                  <td>{{ it.descripcion }}</td>
                  <td>{{ it.identifier }}</td>
                  <td>
                    <v-chip :color="it.deletedAt ? 'error' : 'success'" size="small" variant="tonal">
                      {{ it.deletedAt ? 'Eliminado' : 'Activo' }}
                    </v-chip>
                  </td>
                  <td>
                    <div class="d-flex" style="gap: 6px;">
                      <v-btn size="small" color="secondary" variant="text" @click="editar(it)">
                        <v-icon icon="mdi-pencil" class="mr-1" /> Editar
                      </v-btn>
                      <template v-if="!it.deletedAt">
                        <v-btn size="small" color="error" variant="text" @click="confirmarEliminar(it)" :loading="it._deleting">
                          <v-icon icon="mdi-delete" class="mr-1" /> Eliminar
                        </v-btn>
                      </template>
                      <template v-else>
                        <v-btn size="small" color="success" variant="text" @click="restaurar(it)" :loading="it._restoring">
                          <v-icon icon="mdi-restore" class="mr-1" /> Restaurar
                        </v-btn>
                      </template>
                    </div>
                  </td>
                </tr>
              </tbody>
            </v-table>
            <v-alert v-else-if="!loading" type="info" density="comfortable" variant="tonal">No hay registros.</v-alert>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-dialog v-model="dialog" max-width="700px">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <span>{{ form.id ? 'Editar' : 'Nuevo' }} Detalle de Orientación</span>
          <v-btn icon="mdi-close" variant="text" @click="cerrarDialogo" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-alert v-if="form.deletedAt" type="warning" density="comfortable" class="mb-3">
            Este registro está eliminado. Puedes reactivarlo para volver a habilitarlo.
          </v-alert>
          <v-alert v-if="formError" type="error" density="comfortable" class="mb-3">{{ formError }}</v-alert>
          <v-form @submit.prevent="guardar">
            <v-row>
              <v-col cols="12" md="4">
                <v-text-field v-model.number="form.orden" label="Orden" type="number" :readonly="false" required />
              </v-col>
              <v-col cols="12" md="8">
                <v-text-field v-model="form.descripcion" label="Descripción" required />
              </v-col>
              <v-col cols="12">
                <v-text-field v-model="form.identifier" label="Identifier" hint="Debe ser único" persistent-hint required />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="tonal" @click="cerrarDialogo">Cancelar</v-btn>
          <v-btn v-if="form.id && form.deletedAt" color="success" :loading="restaurando" @click="reactivarDesdeEdicion">
            <v-icon icon="mdi-restore" class="mr-1" /> Reactivar
          </v-btn>
          <v-btn color="primary" :loading="saving" @click="guardar">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="confirmDeleteDialog" max-width="520px">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <span>Eliminar registro</span>
          <v-btn icon="mdi-close" variant="text" @click="closeConfirmDelete" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <p>¿Confirma eliminar el registro <strong>{{ itemToDelete?.descripcion }}</strong>? Esta acción es una eliminación lógica.</p>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="tonal" @click="closeConfirmDelete" :disabled="confirmDeleteLoading">Cancelar</v-btn>
          <v-btn color="error" @click="confirmDelete" :loading="confirmDeleteLoading">Eliminar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(false)
const error = ref('')

const dialog = ref(false)
const saving = ref(false)
const formError = ref('')
const form = ref({ id: '', orden: 1, descripcion: '', identifier: '', deletedAt: null })

const confirmDeleteDialog = ref(false)
const itemToDelete = ref(null)
const confirmDeleteLoading = ref(false)
const restaurando = ref(false)

const cargar = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/admin/detalle-orientacion/')
    items.value = Array.isArray(res.data) ? res.data : []
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado.'
  } finally {
    loading.value = false
  }
}

function abrirNuevo() {
  // Por requerimiento: el valor por defecto de orden es 1 más que el último grabado.
  const activos = items.value.filter(i => !i.deletedAt)
  const maxOrden = activos.length ? Math.max(...activos.map(i => Number(i.orden) || 0)) : 0
  form.value = { id: '', orden: maxOrden + 1, descripcion: '', identifier: '', deletedAt: null }
  formError.value = ''
  dialog.value = true
}

function editar(it) {
  form.value = { id: it.id, orden: it.orden, descripcion: it.descripcion, identifier: it.identifier, deletedAt: it.deletedAt || null }
  formError.value = ''
  dialog.value = true
}

function cerrarDialogo() {
  dialog.value = false
}

async function guardar() {
  formError.value = ''
  saving.value = true
  try {
    const payload = { orden: Number(form.value.orden), descripcion: String(form.value.descripcion), identifier: String(form.value.identifier) }
    if (!form.value.id) {
      await axios.post('/admin/detalle-orientacion/', payload)
    } else {
      await axios.put(`/admin/detalle-orientacion/${form.value.id}`, payload)
    }
    await cargar()
    dialog.value = false
  } catch (e) {
    console.error(e)
    formError.value = e?.response?.data?.error || 'No se pudo guardar el registro.'
  } finally {
    saving.value = false
  }
}

function confirmarEliminar(it) {
  itemToDelete.value = it
  confirmDeleteDialog.value = true
}

function closeConfirmDelete() {
  confirmDeleteDialog.value = false
  itemToDelete.value = null
}

async function confirmDelete() {
  if (!itemToDelete.value) return
  confirmDeleteLoading.value = true
  itemToDelete.value._deleting = true
  try {
    await axios.delete(`/admin/detalle-orientacion/${itemToDelete.value.id}`)
    await cargar()
    closeConfirmDelete()
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo eliminar.'
  } finally {
    confirmDeleteLoading.value = false
    if (itemToDelete.value) itemToDelete.value._deleting = false
  }
}

async function restaurar(it) {
  if (!it) return
  it._restoring = true
  try {
    await axios.put(`/admin/detalle-orientacion/${it.id}/restore`, {})
    await cargar()
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo restaurar.'
  } finally {
    it._restoring = false
  }
}

async function reactivarDesdeEdicion() {
  if (!form.value.id) return
  restaurando.value = true
  try {
    await axios.put(`/admin/detalle-orientacion/${form.value.id}/restore`, {})
    await cargar()
    form.value.deletedAt = null
  } catch (e) {
    console.error(e)
    formError.value = 'No se pudo reactivar.'
  } finally {
    restaurando.value = false
  }
}

onMounted(cargar)
</script>

<style scoped>
.admin-detalle-orientacion { padding-top: 16px; padding-bottom: 16px; }
</style>
