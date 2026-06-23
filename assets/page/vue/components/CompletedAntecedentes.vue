<template>
  <v-container class="completed-antecedentes-component">
    <v-sheet color="success" class="text-white py-3 px-4 mb-4 rounded-lg">
      <div class="d-flex align-center" style="gap:8px;">
        <v-icon icon="mdi-check-circle" size="24"></v-icon>
        <span class="font-weight-medium">Has completado tus antecedentes.</span>
      </div>
    </v-sheet>

    <div v-if="loading" class="text-center pa-5">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>

    <div v-else>
      <div v-for="ant in sortedAntecedentes" :key="ant.id" class="mb-4">
        <h3 v-if="ant.esTitulo" class="text-h5 font-weight-bold mt-6 mb-2 border-bottom pb-2 color-primary">
          {{ ant.label }}
        </h3>

        <v-card v-else variant="flat" class="pa-3 bg-grey-lighten-4 rounded-lg">
          <div class="text-subtitle-1 font-weight-medium mb-1">{{ ant.consulta }}</div>
          <div class="bg-white pa-3 rounded border">
            {{ respuestas[ant.id] || '(Sin respuesta)' }}
          </div>
        </v-card>
      </div>
      
      <v-btn
        v-if="allowEdit"
        variant="text"
        color="primary"
        class="mt-4"
        prepend-icon="mdi-pencil"
        @click="$emit('edit')"
      >
        Modificar respuestas
      </v-btn>
    </div>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import axios from 'axios'
import { store } from '../../assets/almacen'

const props = defineProps({
  allowEdit: {
    type: Boolean,
    default: true
  }
})

defineEmits(['edit'])

const antecedentes = ref([])
const respuestas = reactive({})
const loading = ref(true)

const fetchAntecedentes = async () => {
  try {
    const res = await axios.get('/api/antecedentes')
    const data = res.data['hydra:member'] || res.data
    antecedentes.value = data.filter(a => a.activo !== false)
  } catch (e) {
    console.error('Error al cargar antecedentes:', e)
  }
}

const fetchRespuestas = async () => {
  const person = store.responseData?.value
  if (!person || !person.id) return

  try {
    const res = await axios.get(`/api/personal-antecedentes?persona=${person.id}`)
    const data = res.data['hydra:member'] || res.data
    
    data.forEach(pa => {
      const antId = typeof pa.antecedente === 'string' 
        ? pa.antecedente.split('/').pop() 
        : (pa.antecedente.id || pa.antecedente)
      
      respuestas[antId] = pa.respuesta
    })
  } catch (e) {
    console.error('Error al cargar respuestas:', e)
  }
}

const sortedAntecedentes = computed(() => {
  return [...antecedentes.value]
})

onMounted(async () => {
  await Promise.all([fetchAntecedentes(), fetchRespuestas()])
  loading.value = false
})
</script>

<style scoped>
.color-primary {
  color: #1976D2;
}
.border-bottom {
  border-bottom: 2px solid #e0e0e0;
}
.completed-antecedentes-component {
  max-width: 800px;
  margin: 0 auto;
}
</style>
