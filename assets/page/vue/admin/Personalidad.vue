<template>
  <v-container class="admin-personalidad-wrapper" fluid>
    <v-row class="justify-center">
      <v-col cols="12" md="11" lg="10">
        <v-card class="pa-2 pa-sm-4">
          <v-card-title class="d-flex align-center justify-space-between flex-wrap" style="gap: 8px;">
            <span class="text-h6">Gestión de Personalidad</span>
            <div class="d-flex align-center" style="gap: 8px;">
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
                  <th>D</th>
                  <th>I</th>
                  <th>S</th>
                  <th>C</th>
                  <th style="width: 260px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="it in items" :key="it.id">
                  <td class="text-nowrap">{{ it.id }}</td>
                  <td>{{ it.D }}</td>
                  <td>{{ it.I }}</td>
                  <td>{{ it.S }}</td>
                  <td>{{ it.C }}</td>
                  <td>
                    <div class="d-flex" style="gap: 6px;">
                      <v-btn size="small" color="primary" variant="text" @click="ver(it)">
                        <v-icon icon="mdi-eye" class="mr-1" /> Ver
                      </v-btn>
                      <v-btn size="small" color="secondary" variant="text" @click="editar(it)">
                        <v-icon icon="mdi-pencil" class="mr-1" /> Editar
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
          <span>Detalle de Personalidad #{{ seleccionado?.id }}</span>
          <v-btn icon="mdi-close" variant="text" @click="dialogVer=false" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-table density="compact">
            <tbody>
              <tr><th class="text-left pr-6">D</th><td>{{ seleccionado?.D }}</td></tr>
              <tr><th class="text-left pr-6">I</th><td>{{ seleccionado?.I }}</td></tr>
              <tr><th class="text-left pr-6">S</th><td>{{ seleccionado?.S }}</td></tr>
              <tr><th class="text-left pr-6">C</th><td>{{ seleccionado?.C }}</td></tr>
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
          <span>{{ formMode === 'create' ? 'Crear' : 'Editar' }} Personalidad</span>
          <v-btn icon="mdi-close" variant="text" @click="cerrarForm" />
        </v-card-title>
        <v-divider />
        <v-card-text>
          <v-alert v-if="formError" type="error" density="comfortable" class="mb-3">{{ formError }}</v-alert>
          <v-form ref="formRef" v-model="formValid">
            <v-row>
              <v-col cols="12" md="6">
                <v-text-field label="D" v-model="form.D" :rules="reqRules" required />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="I" v-model="form.I" :rules="reqRules" required />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="S" v-model="form.S" :rules="reqRules" required />
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field label="C" v-model="form.C" :rules="reqRules" required />
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
  </v-container>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(false)
const error = ref('')

const dialogVer = ref(false)
const dialogForm = ref(false)
const formMode = ref('create') // 'create' | 'edit'
const seleccionado = ref(null)

const form = ref({ id: null, D: '', I: '', S: '', C: '' })
const formRef = ref(null)
const formValid = ref(false)
const formLoading = ref(false)
const formError = ref('')
const reqRules = [v => !!v || 'Campo obligatorio']

const cargar = async () => {
  loading.value = true
  error.value = ''
  try {
    // Intentar primero con API Platform
    let res
    try {
      res = await axios.get('/api/personalidad')
      const member = res.data['hydra:member'] ?? res.data['member'] ?? res.data ?? []
      items.value = (Array.isArray(member) ? member : []).map(m => normalize(m))
      return
    } catch (e) {
      // fallback al controlador personalizado existente
      res = await axios.get('/api/listado')
      const data = Array.isArray(res.data) ? res.data : []
      items.value = data.map(m => normalize(m))
    }
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado de Personalidad.'
  } finally {
    loading.value = false
  }
}

function normalize(m) {
  const iri = m['@id'] ?? ''
  const iriId = typeof iri === 'string' && iri.includes('/') ? iri.split('/').pop() : ''
  return {
    id: m.id ?? iriId ?? '',
    D: m.D ?? m.d ?? '',
    I: m.I ?? m.i ?? '',
    S: m.S ?? m.s ?? '',
    C: m.C ?? m.c ?? '',
  }
}

function ver(it) {
  seleccionado.value = { ...it }
  dialogVer.value = true
}

function abrirCrear() {
  formMode.value = 'create'
  form.value = { id: null, D: '', I: '', S: '', C: '' }
  formError.value = ''
  dialogForm.value = true
}

function editar(it) {
  formMode.value = 'edit'
  form.value = { id: it.id, D: it.D, I: it.I, S: it.S, C: it.C }
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
    // validate
    if (formRef.value) {
      const ok = await formRef.value.validate()
      if (!ok.valid) {
        formLoading.value = false
        return
      }
    }
    if (formMode.value === 'create') {
      const { data } = await axios.post('/api/personalidad', {
        D: form.value.D, I: form.value.I, S: form.value.S, C: form.value.C,
      })
      items.value = [normalize(data), ...items.value]
    } else {
      const { data } = await axios.put(`/api/personalidad/${form.value.id}`, {
        D: form.value.D, I: form.value.I, S: form.value.S, C: form.value.C,
      })
      // update in place
      const idx = items.value.findIndex(x => String(x.id) === String(data.id))
      if (idx !== -1) items.value[idx] = normalize(data)
    }
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
.admin-personalidad-wrapper { padding-top: 16px; padding-bottom: 16px; }
</style>
