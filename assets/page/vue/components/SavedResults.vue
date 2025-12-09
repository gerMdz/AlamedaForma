<template>
  <!-- Hacemos el contenedor fluido para permitir más ancho en desktop -->
  <v-container class="mt-2" fluid>
    <v-row v-if="showHeader">
      <v-col cols="12">
        <v-alert type="success" class="mb-4">Se guardaron tus resultados.</v-alert>
      </v-col>
    </v-row>
    <v-row v-if="showPerson" justify="center">
      <v-col cols="12" md="6">
        <v-card title="Datos de la persona" class="mb-4">
          <v-card-text>
            <div><strong>Nombre:</strong> {{ person?.nombre }} {{ person?.apellido }}</div>
            <div><strong>Email:</strong> {{ person?.email }}</div>
            <div><strong>Teléfono:</strong> {{ person?.phone }}</div>
            <div v-if="person?.point"><strong>Grupo:</strong> {{ person?.point }}</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row class="mt-1">
      <v-col cols="12">
        <v-card>
          <v-card-text>
            <!-- Mostrar SOLO el contenido de la pestaña superior seleccionada -->
            <template v-if="activeTab === 'F'">
              <v-row class="section-block">
                <!-- Ensanchamos el área de resultados para ocupar todo el ancho disponible en desktop -->
                <v-col cols="12" md="12" class="mx-auto">
                  <v-sheet color="primary" class="text-white py-3 px-4">
                    <div class="d-flex align-center" style="gap:8px;">
                      <v-icon icon="mdi-school" size="20" class="me-1"></v-icon>
                      <span class="font-weight-medium">Resumen F (Formación)</span>
                    </div>
                  </v-sheet>
                  <v-card class="mt-2">
                    <v-card-text>
                      <div class="mb-3">Tus 3 dones guardados:</div>
                      <v-list density="compact" class="passion-list">
                        <v-list-item v-for="(pf, idx) in formations" :key="idx" class="passion-item">
                          <v-list-item-title class="wrap-title">
                            <div class="d-flex align-center" style="gap: 6px;">
                              <strong>{{ idx + 1 }}.</strong>
                              <span class="mr-1">Don:</span>
                              <span>{{ getDonName(pf) }}</span>
                              <!-- Modal compacto para descripción del don -->
                              <v-btn
                                icon="mdi-information-outline"
                                size="x-small"
                                variant="text"
                                color="primary"
                                density="comfortable"
                                class="don-info-icon"
                                :aria-label="`Ver descripción de ${getDonName(pf)}`"
                                @touchstart.prevent.stop="openDialog(idx)"
                                @click.stop.prevent="openDialog(idx)"
                              />
                              <v-dialog v-model="openedDialogs[idx]" max-width="420" :scrim="true">
                                <v-card>
                                  <v-card-title class="text-subtitle-1">
                                    {{ getDonName(pf) }}
                                  </v-card-title>
                                  <v-card-text>
                                    <div v-html="formatDescription(getDonDescription(pf))"></div>
                                  </v-card-text>
                                  <v-card-actions>
                                    <v-spacer></v-spacer>
                                    <v-btn color="primary" variant="text" @click="closeDialog(idx)">Cerrar</v-btn>
                                  </v-card-actions>
                                </v-card>
                              </v-dialog>
                            </div>
                            <div class="text-medium-emphasis mt-1"><strong>Porcentaje:</strong> {{ pf?.percentDon }} %</div>
                            <div class="text-medium-emphasis mt-1">
                              <strong>Comentario:</strong>
                              <span v-if="pf?.commentDon && pf.commentDon.trim().length > 0">{{ pf.commentDon }}</span>
                              <span v-else class="text-disabled">(Sin comentario)</span>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
              </template>

              <template v-else-if="activeTab === 'O' && (store.hasO && store.hasO.value)">
                  <div v-if="orientacionSaved">
                    <v-row class="section-block">
                      <v-col cols="12" md="12" class="mx-auto">
                        <v-sheet color="primary" class="text-white py-3 px-4">
                          <div class="d-flex align-center" style="gap:8px;">
                            <v-icon icon="mdi-compass-outline" size="20" class="me-1"></v-icon>
                            <span class="font-weight-medium">Resumen O (Orientación)</span>
                          </div>
                        </v-sheet>
                        <v-card>
                          <v-card-text>
                            <v-row v-if="orientacionSummary">
                              <v-col v-if="orientacionSummary?.action_1" cols="12" md="4">
                                <strong>Mis acciones:</strong>
                                <div v-html="formatParagraphs(orientacionSummary.action_1)"></div>
                              </v-col>
                              <v-col v-if="orientacionSummary?.action_2" cols="12" md="4">
                                <strong>Acción 2:</strong>
                                <div v-html="formatParagraphs(orientacionSummary.action_2)"></div>
                              </v-col>
                              <v-col v-if="orientacionSummary?.action_3" cols="12" md="4">
                                <strong>Acción 3:</strong>
                                <div v-html="formatParagraphs(orientacionSummary.action_3)"></div>
                              </v-col>

                              <v-col v-if="orientacionSummary?.trabajar" cols="12" md="6" class="mt-2">
                                <strong>Con quién me gusta trabajar:</strong>
                                <div v-html="formatParagraphs(orientacionSummary.trabajar)"></div>
                              </v-col>
                              <v-col v-if="orientacionSummary?.resolver" cols="12" md="6" class="mt-2">
                                <strong>Problemas que me apasiona resolver:</strong>
                                <div v-html="formatParagraphs(orientacionSummary.resolver)"></div>
                              </v-col>

                              <v-col v-if="orientacionSummary?.selectedLabels?.length" cols="12" class="mt-4">
                                <strong>Mis 3 pasiones principales:</strong>
                                <v-list density="compact" class="passion-list">
                                  <v-list-item v-for="(label, i) in orientacionSummary.selectedLabels" :key="i" class="passion-item">
                                    <v-list-item-title class="wrap-title">{{ i + 1 }}. {{ label }}</v-list-item-title>
                                  </v-list-item>
                                </v-list>
                              </v-col>
                            </v-row>
                          </v-card-text>
                        </v-card>
                      </v-col>
                    </v-row>
                  </div>
                  <Orientacion v-else-if="canOpenOrientacion" :persona-id="person?.id" :email="person?.email" :phone="person?.phone" @saved="onOrientacionSaved"/>
                  <v-alert v-else type="info" class="mt-2">
                    Para completar Orientación primero debes terminar Formación.
                  </v-alert>
              </template>

              <template v-else-if="activeTab === 'R' && (store.hasR && store.hasR.value)">
                  <v-row class="section-block">
                    <v-col cols="12" md="12" class="mx-auto">
                      <v-sheet color="primary" class="text-white py-3 px-4">
                        <div class="d-flex align-center" style="gap:8px;">
                          <v-icon icon="mdi-tools" size="20" class="me-1"></v-icon>
                          <span class="font-weight-medium">Resumen R (Recursos y habilidades)</span>
                        </div>
                      </v-sheet>
                      <PersonalRecursos :persona-id="person?.id" :email="person?.email" :phone="person?.phone" />
                    </v-col>
                  </v-row>
              </template>

              <template v-else-if="activeTab === 'M' && (store.hasM && store.hasM.value)">
                  <v-row class="section-block">
                    <v-col cols="12" md="12" class="mx-auto">
                      <v-sheet color="primary" class="text-white py-3 px-4">
                        <div class="d-flex align-center" style="gap:8px;">
                          <v-icon icon="mdi-account" size="20" class="me-1"></v-icon>
                          <span class="font-weight-medium">Resumen M (Mi Personalidad)</span>
                        </div>
                      </v-sheet>
                      <v-card class="mt-2">
                        <v-card-text>
                          <v-progress-linear v-if="discLoading" indeterminate color="primary" class="mb-4" />
                          <v-alert v-if="discError" type="error" variant="tonal" class="mb-2">{{ discError }}</v-alert>
                          <v-alert v-else-if="!discLoading && !discTotals" type="info" variant="tonal" class="mb-2">Aún no hay resultados de Mi Personalidad para esta persona.</v-alert>
                          <div v-else>
                            <div class="mb-3">Perfil DISC (escala 12–48 con banda intermedia 28–32):</div>
                            <div class="disc-chart-container wider">
                              <DiscProfileChart
                                :d="discChartVals.d"
                                :i="discChartVals.i"
                                :s="discChartVals.s"
                                :c="discChartVals.c"
                                :raw-d="discTotals.d"
                                :raw-i="discTotals.i"
                                :raw-s="discTotals.s"
                                :raw-c="discTotals.c"
                                label-mode="normalized"
                              />
                            </div>
                            <v-row class="mt-4">
                              <v-col cols="6" sm="3"><strong>D:</strong> {{ discTotals.d }}</v-col>
                              <v-col cols="6" sm="3"><strong>I:</strong> {{ discTotals.i }}</v-col>
                              <v-col cols="6" sm="3"><strong>S:</strong> {{ discTotals.s }}</v-col>
                              <v-col cols="6" sm="3"><strong>C:</strong> {{ discTotals.c }}</v-col>
                            </v-row>

                            <!-- Intro / Extro resumen -->
                            <v-divider class="my-6" />
                            <div class="mb-3">Intro / Extro</div>
                            <v-alert v-if="ieError" type="error" variant="tonal" class="mb-2">{{ ieError }}</v-alert>
                            <v-progress-linear v-else-if="ieLoading" indeterminate color="primary" class="mb-2" />
                            <div v-else>
                              <div v-if="introExtroSummary.length === 0" class="text-medium-emphasis">No hay selecciones registradas.</div>
                              <v-list v-else density="compact">
                                <v-list-item v-for="(item, idx) in introExtroSummary" :key="item.id">
                                  <v-list-item-title>
                                    <strong>{{ idx + 1 }}.</strong>
                                    <span class="ml-2">{{ item.intro }}</span>
                                    <span class="mx-1">/</span>
                                    <span>{{ item.extro }}</span>
                                    <span class="mx-2">→</span>
                                    <span class="font-weight-medium" :class="{
                                      'text-primary': item.choice === 'intro',
                                      'text-success': item.choice === 'extro',
                                    }">
                                      {{ item.choiceLabel }}
                                    </span>
                                  </v-list-item-title>
                                </v-list-item>
                              </v-list>
                            </div>
                          </div>
                        </v-card-text>
                      </v-card>
                    </v-col>
                  </v-row>
              </template>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </template>

<script setup>
import {computed, ref, onMounted} from 'vue'
import {defineProps} from 'vue'
import axios from '../../../vendor/axios/axios.index'
import Orientacion from './Orientacion.vue'
import PersonalRecursos from './PersonalRecursos.vue'
import {store} from '../../assets/almacen'
import DiscProfileChart from './DiscProfileChart.vue'

// Tab activa global (F/O/R/M) – usamos la del store
const activeTab = computed(() => store.activeTab?.value || 'F')

const props = defineProps({
  data: {type: Object, default: () => ({})},
  showHeader: {type: Boolean, default: true},
  showPerson: {type: Boolean, default: true}
})

const person = computed(() => props.data?.person || {})
const formations = computed(() => props.data?.formations || [])

// Gate: allow opening O (Orientación) only if user finished F (Formación)
const hasCompletedFormation = computed(() => Array.isArray(formations.value) && formations.value.length >= 3)
const canOpenOrientacion = computed(() => hasCompletedFormation.value)

// Ya no hay tabs internos; seguimos la pestaña global superior

// Estado (legacy) de tooltips – desactivados; usamos diálogos
const openedTooltips = ref({})
const openedDialogs = ref({})

// Estado para Orientación guardada en esta sesión
const orientacionSaved = ref(false)
const orientacionSummary = ref(null)
const onOrientacionSaved = (payload) => {
  orientacionSaved.value = true
  orientacionSummary.value = payload
}

const ensureState = (idx) => {
  if (!openedTooltips.value) openedTooltips.value = {}
  if (openedTooltips.value[idx] === undefined) openedTooltips.value[idx] = false
  if (!openedDialogs.value) openedDialogs.value = {}
  if (openedDialogs.value[idx] === undefined) openedDialogs.value[idx] = false
}

const toggleTooltip = (idx) => {
  // En vez de tooltips, abrimos el modal
  openDialog(idx)
}

const openDialog = (idx) => {
  ensureState(idx)
  openedDialogs.value[idx] = true
}
const closeDialog = (idx) => {
  ensureState(idx)
  openedDialogs.value[idx] = false
}

// Mapa local de dones para resolver IRI/ID -> nombre y descripción
const donInfoMap = ref({})

const buildDonKeyVariants = (don) => {
  const keys = []
  if (don == null) return keys
  if (typeof don === 'object') {
    if (don.id != null) keys.push(String(don.id))
    if (don['@id']) keys.push(String(don['@id']))
    if (don.identifier) keys.push(String(don.identifier))
  } else if (typeof don === 'string') {
    keys.push(don)
    // si es IRI, agregar la cola como posible id
    if (don.startsWith('/')) {
      const tail = don.substring(don.lastIndexOf('/') + 1)
      if (tail) keys.push(tail)
    }
  } else if (typeof don === 'number') {
    keys.push(String(don))
  }
  return keys
}

const getDonName = (pf) => {
  // Si la API devuelve el objeto don embebido, usar su nombre de inmediato
  if (pf?.don && typeof pf.don === 'object') return pf.don.name || pf.don.identifier || 'Don'
  // Caso contrario: intentar resolver contra el mapa local
  const variants = buildDonKeyVariants(pf?.don)
  for (const k of variants) {
    const hit = donInfoMap.value[k]
    if (hit && hit.name) return hit.name
  }
  // Fallback: si era una IRI, mostrar solo el identificador final; si no, mostrar literal
  if (typeof pf?.don === 'string' && pf.don.startsWith('/')) {
    const tail = pf.don.substring(pf.don.lastIndexOf('/') + 1)
    return tail || 'Don'
  }
  return pf?.don || 'Don'
}

const getDonDescription = (pf) => {
  // Preferir descripción embebida si viene del backend
  if (pf?.don && typeof pf.don === 'object') {
    const desc = pf.don.description || ''
    if (desc && desc.trim().length > 0) return desc
  }
  // Buscar en el mapa local por cualquiera de las variantes de llave
  const variants = buildDonKeyVariants(pf?.don)
  for (const k of variants) {
    const hit = donInfoMap.value[k]
    if (hit && typeof hit.description === 'string' && hit.description.trim().length > 0) {
      return hit.description
    }
  }
  return 'Sin descripción disponible'
}

// ==== Resultados M (Mi Personalidad — DISC) ====
const discLoading = ref(false)
const discError = ref('')
const discTotals = ref(null) // { d,i,s,c }

// Normalización 0–120 -> 12–48 para dibujar el perfil clásico
const normalizeTo48 = (v) => {
  const n = Number(v) || 0
  const clamped = Math.max(0, Math.min(120, n))
  return Math.round(12 + clamped * 0.3)
}

const discChartVals = computed(() => {
  const d = discTotals.value?.d ?? 0
  const i = discTotals.value?.i ?? 0
  const s = discTotals.value?.s ?? 0
  const c = discTotals.value?.c ?? 0
  return {
    d: normalizeTo48(d),
    i: normalizeTo48(i),
    s: normalizeTo48(s),
    c: normalizeTo48(c),
  }
})

async function fetchDiscResults() {
  if (!person?.value?.id) return
  if (!(store.hasM && store.hasM.value)) return
  discLoading.value = true
  discError.value = ''
  discTotals.value = null
  try {
    const res = await axios.get(`/api/personal-disc/by-person/${encodeURIComponent(person.value.id)}`)
    const items = res?.data?.items || []
    if (Array.isArray(items) && items.length > 0) {
      const latest = items[0]
      discTotals.value = { d: latest.d ?? 0, i: latest.i ?? 0, s: latest.s ?? 0, c: latest.c ?? 0 }
    } else {
      discTotals.value = null
    }
  } catch (e) {
    console.error(e)
    const status = e?.response?.status || 0
    if (status === 401 || status === 403) discError.value = 'No autorizado para leer resultados de Mi Personalidad.'
    else discError.value = 'No se pudieron cargar los resultados de Mi Personalidad.'
  } finally {
    discLoading.value = false
  }
}

onMounted(() => {
  try { fetchDiscResults() } catch(_) {}
})

// ==== Intro / Extro (Resumen en pestaña M) ====
const ieLoading = ref(false)
const ieError = ref('')
const introExtroRows = ref([]) // catálogo: [{id,intro,extro,mitad}]
const introExtroSummary = ref([]) // lista renderizable con choiceLabel

async function fetchIntroExtroRows() {
  const { data } = await axios.get('/api/intro-extro?activo=true')
  return Array.isArray(data) ? data : []
}

async function fetchIntroExtroSnapshot(personId) {
  const res = await axios.get(`/api/personal-intro-extro/by-person/${encodeURIComponent(personId)}`)
  const items = res?.data?.items || []
  if (items.length > 0) return items[0]?.introExtro || {}
  return {}
}

async function loadIntroExtroForPerson() {
  if (!person?.value?.id) return
  if (!(store.hasM && store.hasM.value)) return
  ieLoading.value = true
  ieError.value = ''
  introExtroSummary.value = []
  try {
    const [rows, snapshot] = await Promise.all([
      fetchIntroExtroRows(),
      fetchIntroExtroSnapshot(person.value.id),
    ])
    introExtroRows.value = rows
    // Construir resumen legible
    const out = []
    for (const r of rows) {
      const choice = snapshot?.[r.id] ?? null
      if (choice === 'intro' || choice === 'extro' || choice === 'mitad') {
        out.push({
          id: r.id,
          intro: r.intro,
          extro: r.extro,
          mitad: r.mitad,
          choice,
          choiceLabel: choice === 'intro' ? r.intro : choice === 'extro' ? r.extro : r.mitad,
        })
      }
    }
    introExtroSummary.value = out
  } catch (e) {
    console.error(e)
    const status = e?.response?.status || 0
    if (status === 401 || status === 403) ieError.value = 'No autorizado para leer Intro/Extro.'
    else ieError.value = 'No se pudieron cargar Intro/Extro.'
  } finally {
    ieLoading.value = false
  }
}

onMounted(() => {
  try { loadIntroExtroForPerson() } catch(_) {}
})

onMounted(async () => {
  try {
    // Cargar catálogo de dones para mapear IDs/IRIs a nombre y descripción
    const res = await axios.get('api/dones')
    const list = Array.isArray(res?.data?.member) ? res.data.member : []
    const map = {}
    for (const d of list) {
      const info = {name: d?.name || d?.identifier || '', description: d?.description || ''}
      // aceptar varias llaves para resolver
      if (d?.id != null) map[String(d.id)] = info
      if (d?.identifier) map[String(d.identifier)] = info
      if (d?.['@id']) map[String(d['@id'])] = info
    }
    donInfoMap.value = map
  } catch (e) {
    // silenciar errores de catálogo; el UI igual muestra fallback legible
    console.warn('No se pudo cargar el catálogo de dones', e)
  }
})

// Formatea la descripción respetando saltos de línea y espacios básicos
function escapeHtml(str) {
  return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
}

const formatDescription = (text) => {
  const safe = escapeHtml(text || '')
  // Convertir saltos de línea a <br> para mantener formato básico
  return safe.replace(/\n/g, '<br>')
}

// Formatea texto libre (de textareas) como párrafos seguros
const formatParagraphs = (text) => {
  const safe = escapeHtml(text || '')
  const lines = safe.split(/\r?\n/).map(l => l.trim()).filter(l => l.length > 0)
  if (lines.length === 0) return ''
  return lines.map(l => `<p class="mb-1">${l}</p>`).join('')
}
// Catálogo de DetalleOrientación para mapear IDs a descripciones
const doInfoMap = ref({})

function valueToId(val) {
  if (val == null) return null
  if (typeof val === 'object') {
    if (val.id != null) return String(val.id)
    if (val['@id']) {
      const iri = String(val['@id'])
      const tail = iri.substring(iri.lastIndexOf('/') + 1)
      return tail || iri
    }
  }
  if (typeof val === 'string') {
    if (val.startsWith('/')) {
      const tail = val.substring(val.lastIndexOf('/') + 1)
      return tail || val
    }
    return val
  }
  return String(val)
}

async function loadDetalleOrientacionCatalog() {
  try {
    const res = await axios.get('api/detalle-orientacion')
    const list = Array.isArray(res?.data?.member) ? res.data.member : []
    const map = {}
    for (const d of list) {
      const id = d?.id != null ? String(d.id) : (d?.['@id'] ? String(d['@id']) : null)
      if (!id) continue
      map[id] = {descripcion: d?.descripcion || ''}
      // también aceptar la variante de solo UUID (cola) si vino como IRI
      if (d?.['@id']) {
        const tail = String(d['@id']).substring(String(d['@id']).lastIndexOf('/') + 1)
        if (tail) map[tail] = {descripcion: d?.descripcion || ''}
      }
    }
    doInfoMap.value = map
  } catch (e) {
    // silencioso, el UI puede mostrar los IDs si no hay catálogo
    console.warn('No se pudo cargar catálogo de DetalleOrientación', e)
  }
}

async function getPersonaId() {
  const p = person?.value || {}
  if (p?.id) return p.id
  // fallback: buscar por email+phone si están disponibles
  const email = p?.email
  const phone = p?.phone
  if (email && phone) {
    try {
      const res = await axios.get('api/personales', {params: {email, phone}})
      const list = Array.isArray(res?.data?.member) ? res.data.member : []
      if (list.length > 0) return list[0]?.id || null
    } catch (e) {
      console.warn('No se pudo resolver persona por email+phone', e)
    }
  }
  return null
}

async function tryLoadOrientacionSaved() {
  try {
    const personaId = await getPersonaId()
    if (!personaId) return
    // Intentar filtrar por persona; si el backend no filtra, de todos modos filtramos client-side
    const res = await axios.get('api/personal-orientacion', {params: {persona: `/api/personales/${personaId}`}})
    const list = Array.isArray(res?.data?.member) ? res.data.member : []
    const samePersona = list.filter(po => {
      const rel = po?.persona
      if (typeof rel === 'string') return rel.endsWith('/' + personaId)
      if (rel && typeof rel === 'object') return valueToId(rel) === String(personaId)
      return false
    })
    if (samePersona.length === 0) return
    // elegir el más reciente por updatedAt o createdAt
    samePersona.sort((a, b) => {
      const ad = new Date(a?.updatedAt || a?.createdAt || 0).getTime()
      const bd = new Date(b?.updatedAt || b?.createdAt || 0).getTime()
      return bd - ad
    })
    const po = samePersona[0]

    // Cargar detalles
    let detRes
    try {
      detRes = await axios.get('api/personal-orientacion-detalle', {params: {personalOrientacion: `/api/personal-orientacion/${po.id}`}})
    } catch (e) {
      detRes = await axios.get('api/personal-orientacion-detalle')
    }
    const detListRaw = Array.isArray(detRes?.data?.member) ? detRes.data.member : []
    const detList = detListRaw.filter(pod => {
      const rel = pod?.personalOrientacion
      if (typeof rel === 'string') return rel.endsWith('/' + po.id)
      if (rel && typeof rel === 'object') return valueToId(rel) === String(po.id)
      return false
    })
    // ordenar por posicion
    detList.sort((a, b) => (a?.posicion || 0) - (b?.posicion || 0))
    const selectedIds = detList.map(pod => valueToId(pod?.detalleOrientacion)).filter(Boolean)

    // asegurar catálogo cargado
    if (!doInfoMap.value || Object.keys(doInfoMap.value).length === 0) {
      await loadDetalleOrientacionCatalog()
    }
    const labels = selectedIds.map(id => {
      const info = doInfoMap.value[String(id)]
      return info?.descripcion || String(id)
    })

    orientacionSummary.value = {
      action_1: po?.action_1 || null,
      action_2: po?.action_2 || null,
      action_3: po?.action_3 || null,
      trabajar: po?.trabajar || null,
      resolver: po?.resolver || null,
      selectedIds,
      selectedLabels: labels,
      personalOrientacionId: po?.id || null,
      personaId: personaId
    }
    orientacionSaved.value = true
  } catch (e) {
    // No bloquear la UI si falla; simplemente dejar el formulario disponible
    console.warn('No se pudo cargar Orientación existente', e)
  }
}

onMounted(async () => {
  // Cargar el resumen de Orientación si ya existe
  await loadDetalleOrientacionCatalog()
  await tryLoadOrientacionSaved()
})

</script>

<style scoped>
.section-block {
  /* Reducimos el margen para pegar más el encabezado a las tabs, como en O */
  margin-top: 0px;
}

.don-name-activator {
  display: inline-block;
  cursor: help;
  text-decoration: underline dotted;
}

.don-tooltip-card {
  padding-right: 4px;
  z-index: 3000;
}

.don-info-icon :deep(.v-btn__content) {
  pointer-events: none; /* allow button wrapper to handle events uniformly */
}

.don-tooltip-card :deep(.v-card-text) {
  white-space: normal;
  line-height: 1.4;
  font-size: 0.95rem;
}

/* Enhanced tabs differentiation */
.better-tabs {
  background-color: transparent;
  padding: 8px 8px 0;
}

.better-tabs :deep(.v-slide-group__content) {
  gap: 8px;
}

.better-tabs :deep(.v-tabs-slider) {
  display: none; /* hide the thin default slider to emphasize pill selection */
}

.better-tabs :deep(.v-tab) {
  border: 1px solid rgba(0, 0, 0, 0.12);
  background-color: rgba(0, 0, 0, 0.04);
  border-radius: 999px;
  text-transform: none;
  font-weight: 600;
  font-size: 1.1rem; /* increase tab label size for better readability */
  color: rgba(0, 0, 0, 0.8);
}

.better-tabs :deep(.v-tab:hover) {
  background-color: rgba(0, 0, 0, 0.06);
}

.better-tabs :deep(.v-tab.v-tab--selected) {
  background-color: #1976d2; /* primary fallback */
  color: white;
  border-color: transparent;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

/* Mobile tweaks: make tabs smaller and allow horizontal scroll */
@media (max-width: 600px) {
  .better-tabs :deep(.v-slide-group__container) {
    overflow-x: auto;
  }
  .better-tabs :deep(.v-tab) {
    font-size: 0.95rem;
    padding-inline: 10px;
    min-width: auto;
  }
}

.disc-chart-container {
  width: 100%;
  height: 560px;
  max-width: 100%; /* ocupar todo el ancho disponible como en O */
  margin-inline: auto;
}

@media (max-width: 900px) {
  .disc-chart-container { height: 420px; }
}

/* Variante explícita más ancha (por si queremos ajustar solo ciertos gráficos) */
.disc-chart-container.wider { max-width: 100%; }
.wrap-title {
  white-space: normal; /* allow multi-line */
  overflow: visible;
  text-overflow: clip;
  word-break: break-word; /* prevent overflow on long words */
}

/* Visual separation for passions list */
.passion-list :deep(.v-list-item) {
  margin-bottom: 10px;
  padding: 10px 12px;
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 10px;
  background-color: rgba(0, 0, 0, 0.02);
}
.passion-list :deep(.v-list-item:last-child) {
  margin-bottom: 0;
}

@media (max-width: 600px) {
  .passion-list :deep(.v-list-item) {
    margin-bottom: 12px;
    padding: 12px;
  }
}

</style>
