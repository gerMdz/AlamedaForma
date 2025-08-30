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
                <div><strong>Don:</strong> {{ getDonName(pf) }}</div>
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

// Mapa local de dones para resolver IRI/ID -> nombre
const donMap = ref({})

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
    const hit = donMap.value[k]
    if (hit) return hit
  }
  // Fallback: si era una IRI, mostrar solo el identificador final; si no, mostrar literal
  if (typeof pf?.don === 'string' && pf.don.startsWith('/')) {
    const tail = pf.don.substring(pf.don.lastIndexOf('/') + 1)
    return tail || 'Don'
  }
  return pf?.don || 'Don'
}

onMounted(async () => {
  try {
    // Cargar catálogo de dones para mapear IDs/IRIs a nombres
    const res = await axios.get('api/dones')
    const list = Array.isArray(res?.data?.member) ? res.data.member : []
    const map = {}
    for (const d of list) {
      // aceptar varias llaves para resolver
      if (d?.id != null) map[String(d.id)] = d.name || d.identifier || String(d.id)
      if (d?.identifier) map[String(d.identifier)] = d.name || d.identifier
      if (d?.['@id']) map[String(d['@id'])] = d.name || d.identifier
    }
    donMap.value = map
  } catch (e) {
    // silenciar errores de catálogo; el UI igual muestra fallback legible
    console.warn('No se pudo cargar el catálogo de dones', e)
  }
})
</script>
