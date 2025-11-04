<template>
  <v-container class="admin-introextro-wrapper" fluid>
    <v-row class="justify-center">
      <v-col cols="12" md="11" lg="10">
        <v-card class="pa-2 pa-sm-4">
          <v-card-title class="d-flex align-center justify-space-between flex-wrap" style="gap: 8px;">
            <span class="text-h6">Gestión de Intro/Extro</span>
            <div class="d-flex align-center flex-wrap" style="gap: 8px;">
              <v-select
                v-model="filtroActivo"
                :items="filtroOptions"
                item-title="label"
                item-value="value"
                density="compact"
                style="min-width: 220px;"
                label="Mostrar"
              />
              <v-btn variant="text" @click="volverAlPanel">
                <v-icon icon="mdi-arrow-left" class="mr-1" />
                <span>Volver al panel</span>
              </v-btn>
              <v-btn color="primary" variant="elevated" @click="abrirCrear">
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
                  <th class="text-nowrap">ID</th>
                  <th>Intro</th>
                  <th>Extro</th>
                  <th>Mitad</th>
                  <th class="text-nowrap">Activo</th>
                  <th style="width: 320px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="it in items" :key="it.id">
                  <td class="text-nowrap">{{ it.id }}</td>
                  <td>{{ it.intro }}</td>
                  <td>{{ it.extro }}</td>
                  <td>{{ it.mitad }}</td>
                  <td class="text-nowrap">{{ it.activo ? 'Sí' : 'No' }}</td>
                  <td>
                    <div class="d-flex" style="gap: 6px;">
                      <v-btn size="small" color="primary" variant="text" @click="ver(it)">
                        <v-icon icon="mdi-eye" class="mr-1" /> Ver
                      </v-btn>
                      <v-btn size="small" color="secondary" variant="text" @click="editar(it)">
                        <v-icon icon="mdi-pencil" class="mr-1" /> Editar
                      </v-btn>
                      <v-btn size="small" color="error" variant="text" @click="confirmarBaja(it)"
                             :disabled="!it.activo">
                        <v-icon icon="mdi-trash-can-outline" class="mr-1" /> Desactivar
                      </v-btn>
                    </div>
                  </td>
                </tr>
              </tbody>
            </v-table>

            <v-alert v-else-if="!loading" type="info" density="comfortable" variant="tonal">
              No hay registros.
            </v-alert>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Dialogo Ver -->
    <v-dialog v-model="dialogVer" max-width="720px">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <span>Detalle #{{ seleccionado?.id }}</span>
          <v-btn icon="mdi-close" variant="text" @click="dialogVer=false" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-table density="compact">
            <tbody>
              <tr><th class="text-left pr-6">Intro</th><td>{{ seleccionado?.intro }}</td></tr>
              <tr><th class="text-left pr-6">Extro</th><td>{{ seleccionado?.extro }}</td></tr>
              <tr><th class="text-left pr-6">Mitad</th><td>{{ seleccionado?.mitad }}</td></tr>
              <tr><th class="text-left pr-6">Activo</th><td>{{ seleccionado?.activo ? 'Sí' : 'No' }}</td></tr>
            </tbody>
          </v-table>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn color="primary" @click="dialogVer=false">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Dialogo Crear/Editar -->
    <v-dialog v-model="dialogForm" max-width="720px">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <span>{{ formMode === 'create' ? 'Crear' : 'Editar' }} Intro/Extro</span>
          <v-btn icon="mdi-close" variant="text" @click="cerrarForm" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-alert v-if="formError" type="error" density="comfortable" class="mb-3">{{ formError }}</v-alert>
          <v-form ref="formRef" v-model="formValid">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field label="Intro" v-model="form.intro" :rules="reqRules" required />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Extro" v-model="form.extro" :rules="reqRules" required />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="Mitad" v-model="form.mitad" :rules="reqRules" required />
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="tonal" @click="cerrarForm">Cancelar</v-btn>
          <v-btn color="primary" :loading="formLoading" @click="guardar">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <!-- Confirmación de desactivación -->
    <v-dialog v-model="dialogConfirm" max-width="520px">
      <v-card>
        <v-card-title class="d-flex align-center justify-space-between">
          <span>Confirmar desactivación</span>
          <v-btn icon="mdi-close" variant="text" @click="dialogConfirm=false" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          ¿Seguro que deseas desactivar el registro "{{ aEliminar?.intro }} / {{ aEliminar?.extro }}"?
          <v-alert v-if="deleteError" type="error" density="comfortable" class="mt-3">{{ deleteError }}</v-alert>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="tonal" @click="dialogConfirm=false">Cancelar</v-btn>
          <v-btn color="error" :loading="deleteLoading" @click="eliminar">Desactivar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(false)
const error = ref('')

// Filtro de activos
const filtroActivo = ref('true') // 'true' | 'false' | 'all'
const filtroOptions = [
  { label: 'Activos', value: 'true' },
  { label: 'Desactivados', value: 'false' },
  { label: 'Todos', value: 'all' },
]

watch(filtroActivo, () => {
  // recargar cuando cambia el filtro
  cargar()
})

const dialogVer = ref(false)
const dialogForm = ref(false)
const dialogConfirm = ref(false)
const deleteLoading = ref(false)
const deleteError = ref('')
const formMode = ref('create') // 'create' | 'edit'
const seleccionado = ref(null)
const aEliminar = ref(null)

const form = ref({ id: null, intro: '', extro: '', mitad: '50/50' })
const formRef = ref(null)
const formValid = ref(false)
const formLoading = ref(false)
const formError = ref('')
const reqRules = [v => !!v || 'Campo obligatorio']

const cargar = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/api/intro-extro', { params: { activo: filtroActivo.value } })
    const data = Array.isArray(res.data) ? res.data : []
    items.value = data.map(m => normalize(m))
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado.'
  } finally {
    loading.value = false
  }
}

function normalize(m) {
  return {
    id: m.id ?? m.ID ?? '',
    intro: m.intro ?? m.Intro ?? '',
    extro: m.extro ?? m.Extro ?? '',
    mitad: m.mitad ?? m.Mitad ?? '50/50',
    activo: m.activo ?? m.Activo ?? true,
  }
}

function ver(it) {
  seleccionado.value = { ...it }
  dialogVer.value = true
}

function abrirCrear() {
  formMode.value = 'create'
  form.value = { id: null, intro: '', extro: '', mitad: '50/50' }
  formError.value = ''
  dialogForm.value = true
}

function editar(it) {
  formMode.value = 'edit'
  form.value = { id: it.id, intro: it.intro, extro: it.extro, mitad: it.mitad }
  formError.value = ''
  dialogForm.value = true
}

function cerrarForm() {
  dialogForm.value = false
}

async function guardar() {
  try {
    formError.value = ''
    formLoading.value = true
    if (formRef.value) {
      const ok = await formRef.value.validate()
      if (!ok.valid) {
        formLoading.value = false
        return
      }
    }

    if (formMode.value === 'create') {
      await axios.post('/api/intro-extro', {
        intro: form.value.intro, extro: form.value.extro, mitad: form.value.mitad,
      })
    } else {
      await axios.put(`/api/intro-extro/${form.value.id}`, {
        intro: form.value.intro, extro: form.value.extro, mitad: form.value.mitad,
      })
    }
    await cargar()
    dialogForm.value = false
  } catch (e) {
    console.error(e)
    if (e.response?.data?.errors) {
      const errs = e.response.data.errors
      formError.value = Object.values(errs).join('. ')
    } else {
      formError.value = 'No se pudo guardar.'
    }
  } finally {
    formLoading.value = false
  }
}

function confirmarBaja(it) {
  aEliminar.value = it
  deleteError.value = ''
  dialogConfirm.value = true
}

async function eliminar() {
  if (!aEliminar.value) return
  deleteLoading.value = true
  deleteError.value = ''
  try {
    await axios.delete(`/api/intro-extro/${aEliminar.value.id}`)
    await cargar()
    dialogConfirm.value = false
  } catch (e) {
    console.error(e)
    deleteError.value = 'No se pudo desactivar.'
  } finally {
    deleteLoading.value = false
  }
}

function volverAlPanel() {
  if (typeof window !== 'undefined') {
    try {
      window.location.hash = ''
    } catch (e) {
      window.location.href = '/admin'
    }
  }
}

onMounted(cargar)
</script>

<style scoped>
.admin-introextro-wrapper { padding-top: 16px; padding-bottom: 16px; }
</style>
