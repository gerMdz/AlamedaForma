<template>
  <v-container class="mt-6">
    <v-row>
      <v-col cols="12">
        <v-alert type="success" class="mb-4">Se guardaron tus resultados.</v-alert>
      </v-col>
    </v-row>
    <v-row>
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
      <v-col cols="12" md="6">
        <v-card title="Tus 3 dones guardados">
          <v-list>
            <v-list-item v-for="(pf, idx) in formations" :key="idx">
              <v-list-item-title>
                <div class="d-flex align-center" style="gap: 6px;">
                  <strong>Don:</strong>
                  <v-tooltip location="top" open-delay="200" :offset="[0,8]" v-model:opened="openedTooltips[idx]" close-on-content-click="false" eager>
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
                </div>
                <div><strong>Porcentaje:</strong> {{ pf?.percentDon }} %</div>
              </v-list-item-title>
              <v-list-item-subtitle>
                <span v-if="pf?.commentDon && pf.commentDon.trim().length > 0"><strong>Comentario:</strong> {{ pf.commentDon }}</span>
                <span v-else class="text-disabled">(Sin comentario)</span>
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { defineProps } from 'vue'
import axios from 'axios'

const props = defineProps({
  data: { type: Object, default: () => ({}) }
})

const person = computed(() => props.data?.person || {})
const formations = computed(() => props.data?.formations || [])

// Estado de tooltips abiertos por índice para soportar pantallas táctiles
const openedTooltips = ref({})
const autoCloseTimers = ref({})
const AUTO_CLOSE_MS = 7000

const ensureState = (idx) => {
  if (!openedTooltips.value) openedTooltips.value = {}
  if (openedTooltips.value[idx] === undefined) openedTooltips.value[idx] = false
}

const toggleTooltip = (idx) => {
  ensureState(idx)
  openedTooltips.value[idx] = !openedTooltips.value[idx]
  if (openedTooltips.value[idx]) {
    // autohide para evitar que quede pegado en móviles
    try {
      clearTimeout(autoCloseTimers.value[idx])
    } catch (e) { /* noop */ }
    autoCloseTimers.value[idx] = setTimeout(() => {
      openedTooltips.value[idx] = false
    }, AUTO_CLOSE_MS)
  }
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
      const info = { name: d?.name || d?.identifier || '', description: d?.description || '' }
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
</style>
