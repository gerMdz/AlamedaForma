<script setup>
import { onMounted, watch, ref, computed } from 'vue'
import HelloForma from "./HelloForma.vue";
import {store} from "../../assets/almacen";
import FormPersonalidades from "./FormPersonalidades.vue";
import Formation from "./Formation.vue";
import CompletedFormation from "./CompletedFormation.vue";
import Inicio from "./Inicio.vue";
import PersonalRecursos from "./PersonalRecursos.vue";
import Orientacion from "./Orientacion.vue";
import OrientacionSummary from './OrientacionSummary.vue'
import MiPersonalidad from "./MiPersonalidad.vue";
import DiscProfileChart from './DiscProfileChart.vue'
// Usar la instancia configurada de axios (autenticación/cabeceras) para evitar inconsistencias
import axios from '../../../vendor/axios/axios.index'

const responseData = store.responseData

const termsAccepted = store.termsAccepted
const checking = ref(false)
const checkingF = ref(false)
const hasAvanceF = ref(false)
const checkingR = ref(false)
const hasAvanceR = ref(false)
// resultsMode is true either if backend says there is avance F or if user just saved
const resultsMode = store.resultsMode

// identity props for child forms
const pid = computed(() => responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id || '')
const pemail = computed(() => responseData?.value?.email || '')
const pphone = computed(() => responseData?.value?.phone || '')

// Hydrate full person details if we only have the ID
function needsPersonHydration(val) {
  const v = val || responseData?.value || null
  if (!v) return false
  const id = v.id || v.ID || v.Id
  const hasAnyField = !!(v.nombre || v.apellido || v.email || v.phone || v.point)
  return !!id && !hasAnyField
}

async function hydratePerson(val) {
  const v = val || responseData?.value || null
  if (!v) return
  const id = v.id || v.ID || v.Id
  if (!id) return
  try {
    const res = await axios.get(`/api/personal/${encodeURIComponent(id)}`)
    if (res?.data) {
      try { store.setResponseData(res.data) } catch (_) { /* noop */ }
    }
  } catch (e) {
    console.warn('No se pudo hidratar datos de la persona', e)
  }
}

// Tabs para Formación (F), Orientación (O), Recursos (R) y Mi Personalidad (M)
// Única fuente de verdad (persistida) en el store
const activeTab = computed({
  get: () => store.activeTab?.value || 'F',
  set: (v) => {
    try { store.setActiveTab(v) } catch(_) {}
  }
})
function selectDefaultTab() {
  if (store.hasF && store.hasF.value) {
    activeTab.value = 'F'
  } else if (store.hasO && store.hasO.value) {
    activeTab.value = 'O'
  } else if (store.hasR && store.hasR.value) {
    activeTab.value = 'R'
  } else if (store.hasM && store.hasM.value) {
    activeTab.value = 'M'
  }
}

async function checkTerms() {
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checking.value = true
  try {
    const res = await axios.get(`/api/forma/terminos-estado/${encodeURIComponent(personalId)}`)
    store.setTermsAccepted(!!res?.data?.accepted)
  } catch (e) {
    console.warn('No se pudo verificar estado de Términos', e)
    // Si la persona no existe en el backend, limpiar el caché local y volver a paso Personal
    const status = e?.response?.status || e?.status || null;
    if (status === 404) {
      try { store.setResponseData(null); } catch(_) {}
    }
    // Por seguridad, si falla la verificación mostramos Términos como no aceptados
    store.setTermsAccepted(false)
  } finally {
    checking.value = false
  }
}

async function checkAvanceF() {
  // Solo aplica si F está habilitado
  if (!(store.hasF && store.hasF.value)) {
    hasAvanceF.value = false
    try { store.setResultsMode(false) } catch(_) {}
    return
  }
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checkingF.value = true
  try {
    const res = await axios.get(`/api/forma/avance-f-estado/${encodeURIComponent(personalId)}`)
    hasAvanceF.value = !!res?.data?.hasAvanceF
    if (hasAvanceF.value) {
      try { store.setResultsMode(true) } catch(_) {}
    } else {
      // Si no tiene Avance F, aseguramos que no quede en modo resultados
      try { store.setResultsMode(false) } catch(_) {}
    }
  } catch (e) {
    console.warn('No se pudo verificar avance F', e)
    hasAvanceF.value = false
    try { store.setResultsMode(false) } catch(_) {}
  } finally {
    checkingF.value = false
  }
}

async function checkAvanceR() {
  // Aplica solo si R está habilitado
  if (!(store.hasR && store.hasR.value)) {
    hasAvanceR.value = false
    return
  }
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checkingR.value = true
  try {
    const res = await axios.get(`/api/forma/avance-r-estado/${encodeURIComponent(personalId)}`)
    hasAvanceR.value = !!res?.data?.hasAvanceR
  } catch (e) {
    console.warn('No se pudo verificar avance R', e)
    hasAvanceR.value = false
  } finally {
    checkingR.value = false
  }
}

// ====== Estado de Orientación guardada (para mutar entre formulario y resumen) ======
const orientacionLoading = ref(false)
const orientacionSummary = ref(null)
const orientacionError = ref('')
const hasOrientacion = computed(() => !!orientacionSummary.value)

// Helpers para cargar catálogo y etiquetas de DetalleOrientacion (para las 3 pasiones)
const doInfoMap = ref({}) // id -> { descripcion }
function valueToId(x) {
  if (!x) return null
  if (typeof x === 'string') {
    if (x.startsWith('/')) return x.substring(x.lastIndexOf('/') + 1)
    return x
  }
  if (typeof x === 'object') {
    if (x.id != null) return String(x.id)
    if (x['@id']) return String(x['@id']).substring(String(x['@id']).lastIndexOf('/') + 1)
  }
  return null
}

async function loadDetalleOrientacionCatalog() {
  try {
    const res = await axios.get('api/detalle-orientacion')
    const list = Array.isArray(res?.data?.member) ? res.data.member : []
    const map = {}
    for (const d of list) {
      const info = { descripcion: d?.descripcion || d?.name || d?.identifier || '' }
      if (d?.id != null) map[String(d.id)] = info
      if (d?.identifier) map[String(d.identifier)] = info
      if (d?.['@id']) {
        const tail = String(d['@id']).substring(String(d['@id']).lastIndexOf('/') + 1)
        if (tail) map[tail] = info
      }
    }
    doInfoMap.value = map
  } catch (e) {
    // silencio; el resumen se mostrará sin etiquetas si falla
    doInfoMap.value = {}
  }
}

async function fetchOrientacionResumen() {
  orientacionLoading.value = true
  orientacionError.value = ''
  orientacionSummary.value = null
  try {
    const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
    if (!personalId) return
    // Obtener registros de personal-orientacion para la persona
    let res
    try {
      res = await axios.get('api/personal-orientacion', { params: { persona: `/api/personales/${personalId}` } })
    } catch (e) {
      res = await axios.get('api/personal-orientacion')
    }
    const list = Array.isArray(res?.data?.member) ? res.data.member : []
    const samePersona = list.filter(po => {
      const rel = po?.persona
      if (typeof rel === 'string') return rel.endsWith('/' + personalId)
      if (rel && typeof rel === 'object') return valueToId(rel) === String(personalId)
      return false
    })
    if (samePersona.length === 0) return
    samePersona.sort((a, b) => (new Date(b?.updatedAt || b?.createdAt || 0).getTime()) - (new Date(a?.updatedAt || a?.createdAt || 0).getTime()))
    const po = samePersona[0]

    // Cargar detalle (pasiones) y mapa de etiquetas
    await loadDetalleOrientacionCatalog()
    let detRes
    try {
      detRes = await axios.get('api/personal-orientacion-detalle', { params: { personalOrientacion: `/api/personal-orientacion/${po.id}` } })
    } catch (e) {
      detRes = await axios.get('api/personal-orientacion-detalle')
    }
    const detListRaw = Array.isArray(detRes?.data?.member) ? detRes.data.member : []
    const detList = detListRaw.filter(pod => {
      const rel = pod?.personalOrientacion
      if (typeof rel === 'string') return rel.endsWith('/' + po.id)
      if (rel && typeof rel === 'object') return valueToId(rel) === String(po.id)
      return false
    }).sort((a,b) => (a?.posicion||0) - (b?.posicion||0))
    const selectedIds = detList.map(pod => valueToId(pod?.detalleOrientacion)).filter(Boolean)
    const labels = selectedIds.map(id => doInfoMap.value[String(id)]?.descripcion || String(id))

    orientacionSummary.value = {
      action_1: po?.action_1 || null,
      action_2: po?.action_2 || null,
      action_3: po?.action_3 || null,
      trabajar: po?.trabajar || null,
      resolver: po?.resolver || null,
      selectedLabels: labels,
    }
  } catch (e) {
    // No bloquear; simplemente no hay resumen disponible
    orientacionError.value = ''
  } finally {
    orientacionLoading.value = false
  }
}

// ====== Estado DISC guardado (para mutar entre formulario y gráfico) ======
const discLoading = ref(false)
const discTotals = ref(null) // { d,i,s,c }
const discError = ref('')

async function fetchDiscResults() {
  discLoading.value = true
  discTotals.value = null
  discError.value = ''
  try {
    const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
    if (!personalId) return
    const res = await axios.get(`/api/personal-disc/by-person/${encodeURIComponent(personalId)}`)
    const items = res?.data?.items || []
    if (Array.isArray(items) && items.length > 0) {
      const latest = items[0]
      discTotals.value = { d: latest?.d ?? 0, i: latest?.i ?? 0, s: latest?.s ?? 0, c: latest?.c ?? 0 }
    }
  } catch (e) {
    discError.value = ''
  } finally {
    discLoading.value = false
  }
}

onMounted(async () => {
  if (responseData?.value) {
    if (needsPersonHydration(responseData.value)) {
      await hydratePerson(responseData.value)
    }
    checkTerms()
    checkAvanceF()
    checkAvanceR()
    // cargar estados de orientación y DISC
    try { await Promise.all([fetchOrientacionResumen(), fetchDiscResults()]) } catch(_) {}
  }
  // set default tab after flags are known
  selectDefaultTab()
})

watch(responseData, async (val) => {
  if (val) {
    if (needsPersonHydration(val)) {
      await hydratePerson(val)
    }
    checkTerms()
    checkAvanceF()
    checkAvanceR()
    try { await Promise.all([fetchOrientacionResumen(), fetchDiscResults()]) } catch(_) {}
  }
})

// adjust default tab when flags change
watch(() => [store.hasF && store.hasF.value, store.hasO && store.hasO.value, store.hasR && store.hasR.value, store.hasM && store.hasM.value], () => {
  selectDefaultTab()
  // recheck avance R if R becomes enabled
  checkAvanceR()
})
</script>

<template>
  <v-container fluid class="fill-height">
    <HelloForma :response-data="responseData?.value"/>
    <v-container v-if="responseData" fluid class="text-center">
      <div v-if="checking">Verificando términos...</div>
      <template v-else>
        <template v-if="!termsAccepted">
          <!-- Paso 2: Términos y condiciones + instrucciones -->
          <Inicio />
        </template>
        <template v-else>
          <!-- Presentación: mostrar siempre arriba de los tabs -->
          <div class="mt-6" style="width:100%; max-width:1280px; margin: 0 auto;">
            <v-row>
              <v-col cols="12">
                <v-alert v-if="(resultsMode || hasAvanceF)" type="success" class="mb-4">
                  Ya registraste tus resultados de Formación. Aquí está tu resumen.
                </v-alert>
              </v-col>
            </v-row>
            <v-row justify="center">
              <v-col cols="12" md="6">
                <v-card title="Datos de la persona" class="mb-4">
                  <v-card-text>
                    <div><strong>Nombre/s:</strong> {{ responseData?.nombre }} {{ responseData?.apellido }}</div>
                    <div><strong>Email:</strong> {{ responseData?.email }}</div>
                    <div><strong>Teléfono:</strong> {{ responseData?.phone }}</div>
                    <div v-if="responseData?.point"><strong>Grupo:</strong> {{ responseData?.point }}</div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </div>

          <!-- Tabs visibles siempre que haya alguna sección habilitada. Si hay resultados de F, se muestra el resumen dentro de la pestaña F. -->
          <div v-if="(store.hasF && store.hasF.value) || (store.hasO && store.hasO.value) || (store.hasR && store.hasR.value) || (store.hasM && store.hasM.value)" class="mt-6">
            <v-tabs v-model="activeTab" grow>
              <v-tab v-if="store.hasF && store.hasF.value" value="F" :class="['forma-tab', { 'is-active': activeTab==='F' }]">[F]ormación</v-tab>
              <v-tab v-if="store.hasO && store.hasO.value" value="O" :class="['forma-tab', { 'is-active': activeTab==='O' }]">[O]rientación</v-tab>
              <v-tab v-if="store.hasR && store.hasR.value" value="R" :class="['forma-tab', { 'is-active': activeTab==='R' }]">[R]ecursos y Habilidades</v-tab>
              <v-tab v-if="store.hasM && store.hasM.value" value="M" :class="['forma-tab', { 'is-active': activeTab==='M' }]">[M]i Personalidad</v-tab>
            </v-tabs>
            <!-- Reducimos el espacio entre tabs y encabezados para uniformar con O -->
            <v-window v-model="activeTab" class="mt-0">
              <v-window-item v-if="store.hasF && store.hasF.value" value="F">
                <template v-if="resultsMode || hasAvanceF">
                  <CompletedFormation />
                </template>
                <template v-else>
                  <Formation />
                </template>
              </v-window-item>
              <v-window-item v-if="store.hasO && store.hasO.value" value="O">
                <template v-if="hasOrientacion">
                  <OrientacionSummary :summary="orientacionSummary" />
                </template>
                <template v-else>
                  <Orientacion :persona-id="pid" :email="pemail" :phone="pphone" />
                </template>
              </v-window-item>
              <v-window-item v-if="store.hasR && store.hasR.value" value="R">
                <v-sheet color="primary" class="text-white py-3 px-4 mb-2">
                  <div class="d-flex align-center" style="gap:8px;">
                    <v-icon icon="mdi-tools" size="20" class="me-1"></v-icon>
                    <span class="font-weight-medium">Resumen R (Recursos y habilidades)</span>
                  </div>
                </v-sheet>
                <PersonalRecursos :persona-id="pid" :email="pemail" :phone="pphone" />
              </v-window-item>
              <v-window-item v-if="store.hasM && store.hasM.value" value="M">
                <template v-if="discTotals">
                  <v-sheet color="primary" class="text-white py-3 px-4 mb-2">
                    <div class="d-flex align-center" style="gap:8px;">
                      <v-icon icon="mdi-account" size="20" class="me-1"></v-icon>
                      <span class="font-weight-medium">Resumen M (Mi Personalidad)</span>
                    </div>
                  </v-sheet>
                  <div class="disc-chart-container wider">
                    <DiscProfileChart :d="discTotals.d" :i="discTotals.i" :s="discTotals.s" :c="discTotals.c" />
                  </div>
                  <v-row class="mt-4">
                    <v-col cols="6" sm="3"><strong>D:</strong> {{ discTotals.d }}</v-col>
                    <v-col cols="6" sm="3"><strong>I:</strong> {{ discTotals.i }}</v-col>
                    <v-col cols="6" sm="3"><strong>S:</strong> {{ discTotals.s }}</v-col>
                    <v-col cols="6" sm="3"><strong>C:</strong> {{ discTotals.c }}</v-col>
                  </v-row>
                </template>
                <template v-else>
                  <MiPersonalidad :persona-id="pid" :email="pemail" :phone="pphone" />
                </template>
              </v-window-item>
            </v-window>
          </div>
        </template>
      </template>
    </v-container>
  </v-container>

</template>

<style scoped>
.forma-tab.is-active {
  font-weight: 700;
  color: #1976d2; /* primary-ish */
  border-bottom: 2px solid #1976d2;
}

.disc-chart-container {
  width: 100%;
  height: 560px;
  max-width: 100%;
  margin-inline: auto;
}

@media (max-width: 900px) {
  .disc-chart-container { height: 420px; }
}
</style>