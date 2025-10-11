<template>
  <v-container fluid class="fill-height">
    <div v-if="currentPerson">
      <h3> Gracias {{ currentPerson.nombre }} {{ currentPerson.apellido }} </h3>
      <p>
        A continuación encontrarás varias secciones, que están clasificadas según el acróstico F.O.R.M.A. Deberás ir
        completando cada sección y de esta manera ir avanzando para completar el test. *
      </p>
    </div>
  </v-container>

</template>

<script setup>
import { computed } from 'vue'
import { defineProps } from 'vue'
import { store } from "../../assets/almacen"

const props = defineProps({
  responseData: { type: Object, default: null }
})

// Preferir los datos recibidos por props; si no vienen, usar el store como respaldo
const currentPerson = computed(() => {
  if (props.responseData) return props.responseData
  const rd = store.responseData?.value || null
  return rd || null
})
</script>

<style scoped>
</style>