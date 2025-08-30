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
                <strong>{{ getDonName(pf) }}</strong> — {{ pf?.percentDon }} %
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ pf?.commentDon }}
              </v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { computed } from 'vue'
import { defineProps } from 'vue'

const props = defineProps({
  data: { type: Object, default: () => ({}) }
})

const person = computed(() => props.data?.person || {})
const formations = computed(() => props.data?.formations || [])

const getDonName = (pf) => {
  // Si la API devuelve el objeto don embebido, usar su nombre.
  if (pf?.don && typeof pf.don === 'object') return pf.don.name || pf.don.identifier || 'Don'
  // Si devuelve solo ID, mostramos el ID (o podríamos mapearlo si estuviera disponible)
  return pf?.don || 'Don'
}
</script>
