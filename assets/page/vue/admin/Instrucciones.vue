<template>
  <v-container class="admin-instrucciones-wrapper" fluid>
    <v-row class="justify-center">
      <v-col cols="12" md="11" lg="10">
        <v-card class="pa-2 pa-sm-4">
          <v-card-title class="d-flex align-center justify-space-between flex-wrap" style="gap: 8px;">
            <span class="text-h6">Gestión de Instrucciones</span>
            <div class="d-flex align-center" style="gap: 8px;">
              <v-btn color="primary" variant="elevated" :href="'/admin/instrucciones/new'">
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
                  <th>Título</th>
                  <th>Habilitado</th>
                  <th class="text-nowrap">Creado</th>
                  <th class="text-nowrap">Actualizado</th>
                  <th style="width: 180px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="it in items" :key="it.id">
                  <td>{{ it.title }}</td>
                  <td>{{ it.enabled ? 'Sí' : 'No' }}</td>
                  <td>{{ it.createdAt }}</td>
                  <td>{{ it.updateAt }}</td>
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

    <v-dialog v-model="dialog" max-width="900px">
      <v-card>
        <v-card-title class="d-flex justify-space-between align-center">
          <span>{{ selected?.title }}</span>
          <v-btn icon="mdi-close" variant="text" @click="dialog=false" />
        </v-card-title>
        <v-card-subtitle class="px-4">{{ selected?.enabled ? 'Habilitado' : 'Deshabilitado' }} • Creado: {{ selected?.createdAt }} • Actualizado: {{ selected?.updateAt }}</v-card-subtitle>
        <v-divider />
        <v-card-text class="px-4">
          <div v-if="selected" v-html="selected.content"></div>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn variant="tonal" color="secondary" :href="selected ? `/admin/instrucciones/${selected.id}/edit` : '#'">
            <v-icon icon="mdi-pencil" class="mr-1" /> Editar
          </v-btn>
          <v-btn color="primary" @click="dialog=false">Cerrar</v-btn>
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
const selected = ref(null)

const normalizeDate = (val) => {
  if (!val) return ''
  try { return new Date(val).toLocaleString() } catch(e) { return val }
}

const cargar = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/api/instructions')
    const member = res.data['hydra:member'] ?? res.data['member'] ?? []
    items.value = member.map(m => {
      const iri = m['@id'] ?? ''
      const iriId = typeof iri === 'string' && iri.includes('/') ? iri.split('/').pop() : ''
      return {
        id: m.id ?? iriId ?? '',
        title: m.title ?? m.Title ?? '',
        content: m.content ?? m.Content ?? '',
        enabled: m.enabled ?? m.Enabled ?? false,
        createdAt: normalizeDate(m.createdAt ?? m.CreatedAt),
        updateAt: normalizeDate(m.updateAt ?? m.UpdateAt),
      }
    })
  } catch (e) {
    console.error(e)
    error.value = 'No se pudo cargar el listado de instrucciones.'
  } finally {
    loading.value = false
  }
}

function ver(it) {
  selected.value = it
  dialog.value = true
}

function editar(it) {
  if (typeof window !== 'undefined') {
    window.location.hash = `#instrucciones-editar:${it.id}`
  }
}

onMounted(cargar)
</script>

<style scoped>
.admin-instrucciones-wrapper { padding-top: 16px; padding-bottom: 16px; }
</style>
