<template>
  <v-container class="mt-6">
    <v-row>
      <v-col cols="12">
        <v-alert type="success" class="mb-4">Se guardaron tus resultados.</v-alert>
      </v-col>
    </v-row>
    <v-row justify="center">
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
    <v-row class="mt-6">
      <v-col cols="12">
        <v-card>
          <v-tabs v-model="tab" class="better-tabs" bg-color="transparent" grow>
            <v-tab value="results" prepend-icon="mdi-school">Resultados F (Formación)</v-tab>
            <v-tab v-if="store.hasO && store.hasO.value" value="orientacion" :disabled="!canOpenOrientacion"
                   prepend-icon="mdi-compass-outline">O (Orientación)
            </v-tab>
          </v-tabs>
          <v-card-text>
            <v-window v-model="tab">
              <v-window-item value="results">
                <v-row>
                  <v-col cols="12" md="8" class="mx-auto">
                    <v-card title="Tus 3 dones guardados">
                      <v-list>
                        <v-list-item v-for="(pf, idx) in formations" :key="idx">
                          <v-list-item-title>
                            <div class="d-flex align-center" style="gap: 6px;">
                              <strong>Don:</strong>
                              <span>{{ getDonName(pf) }}</span>
                              <v-tooltip v-if="false" location="top" open-delay="200" :offset="[0,8]"
                                         v-model:opened="openedTooltips[idx]" close-on-content-click="false" eager>
                                <template #activator="{ props: tip }">
                                  <span class="d-flex align-center" style="gap: 4px;">
                                    <span
                                        v-bind="tip"
                                        class="text-primary don-name-activator"
                                        role="button"
                                        tabindex="0"
                                        :aria-label="`Ver descripción de ${getDonName(pf)}`"
                                        :aria-expanded="!!openedTooltips[idx]"
                                        @touchstart.prevent.stop="toggleTooltip(idx)"
                                        @click="toggleTooltip(idx)"
                                        @keydown.enter.prevent="toggleTooltip(idx)"
                                        @keydown.space.prevent="toggleTooltip(idx)"
                                    >
                                      {{ getDonName(pf) }}
                                    </span>
                                    <v-btn
                                        v-bind="tip"
                                        icon="mdi-information-outline"
                                        size="x-small"
                                        variant="text"
                                        density="comfortable"
                                        class="don-info-icon"
                                        :aria-label="`Ver descripción de ${getDonName(pf)}`"
                                        :aria-expanded="!!openedTooltips[idx]"
                                        @touchstart.prevent.stop="toggleTooltip(idx)"
                                        @click.stop.prevent="toggleTooltip(idx)"
                                    />
                                  </span>
                                </template>
                                <v-card class="don-tooltip-card" max-width="360">
                                  <v-card-title class="text-subtitle-1">
                                    Descripción de {{ getDonName(pf) }}
                                  </v-card-title>
                                  <v-card-text>
                                    <div v-html="formatDescription(getDonDescription(pf))"></div>
                                  </v-card-text>
                                </v-card>
                              </v-tooltip>
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
                            <div><strong>Porcentaje:</strong> {{ pf?.percentDon }} %</div>
                          </v-list-item-title>
                          <v-list-item-subtitle>
                            <span v-if="pf?.commentDon && pf.commentDon.trim().length > 0"><strong>Comentario:</strong> {{
                                pf.commentDon
                              }}</span>
                            <span v-else class="text-disabled">(Sin comentario)</span>
                          </v-list-item-subtitle>
                        </v-list-item>
                      </v-list>
                    </v-card>
                  </v-col>
                </v-row>
              </v-window-item>
              <v-window-item v-if="store.hasO && store.hasO.value" value="orientacion">
                <div v-if="orientacionSaved" class="mt-2">
                  <v-alert type="success" class="mb-4">Se guardaron tus datos de Orientación.</v-alert>
                  <v-card title="Tu Orientación guardada">
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
                          <v-list density="compact">
                            <v-list-item v-for="(label, i) in orientacionSummary.selectedLabels" :key="i">
                              <v-list-item-title>{{ i + 1 }}. {{ label }}</v-list-item-title>
                            </v-list-item>
                          </v-list>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                </div>
                <Orientacion v-else-if="canOpenOrientacion" :persona-id="person?.id" :email="person?.email"
                             :phone="person?.phone" @saved="onOrientacionSaved"/>
                <v-alert v-else type="info" class="mt-2">
                  Para completar Orientación primero debes terminar Formación.
                </v-alert>
              </v-window-item>
            </v-window>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import {computed, ref, onMounted} from 'vue'
import {defineProps} from 'vue'
import axios from 'axios'
import Orientacion from './Orientacion.vue'
import {store} from '../../assets/almacen'

const props = defineProps({
  data: {type: Object, default: () => ({})}
})

const person = computed(() => props.data?.person || {})
const formations = computed(() => props.data?.formations || [])

// Gate: allow opening O (Orientación) only if user finished F (Formación)
const hasCompletedFormation = computed(() => Array.isArray(formations.value) && formations.value.length >= 3)
const canOpenOrientacion = computed(() => hasCompletedFormation.value)

// Tabs state
const tab = ref('results')

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
</style>
