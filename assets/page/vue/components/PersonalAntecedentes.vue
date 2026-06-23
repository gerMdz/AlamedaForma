<template>
  <v-container class="personal-antecedentes-component">
    <v-card v-if="loading" flat class="text-center pa-5">
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
      <div class="mt-2">Cargando antecedentes...</div>
    </v-card>

    <div v-else>
      <div v-for="ant in sortedAntecedentes" :key="ant.id" class="mb-4">
        <!-- Título -->
        <h3 v-if="ant.esTitulo" class="text-h5 font-weight-bold mt-6 mb-2 border-bottom pb-2 color-primary">
          {{ ant.label }}
        </h3>

        <!-- Antecedente con pregunta y textarea -->
        <v-card v-else variant="flat" class="pa-3 bg-grey-lighten-4 rounded-lg">
          <div class="text-subtitle-1 font-weight-medium mb-2">{{ ant.consulta }}</div>
          <v-textarea
            v-model="respuestas[ant.id]"
            :label="ant.label"
            variant="outlined"
            rows="3"
            auto-grow
            density="comfortable"
            hide-details="auto"
            bg-color="white"
            @blur="autoSave(ant.id)"
          ></v-textarea>
        </v-card>
      </div>

      <v-divider class="my-6"></v-divider>

      <v-btn
        color="primary"
        size="large"
        :loading="saving"
        :disabled="saving || !canSave"
        @click="saveAll"
        prepend-icon="mdi-content-save"
      >
        Guardar Antecedentes
      </v-btn>
    </div>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" timeout="3000" location="top">
      {{ snackbar.text }}
      <template #actions>
        <v-btn variant="text" @click="snackbar.show = false">Cerrar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue'
import axios from 'axios'
import { store } from '../../assets/almacen'

const emit = defineEmits(['saved'])

const antecedentes = ref([])
const respuestas = reactive({})
const loading = ref(true)
const saving = ref(false)

const snackbar = reactive({
  show: false,
  text: '',
  color: 'success'
})

// Cargar antecedentes activos
const fetchAntecedentes = async () => {
  try {
    const res = await axios.get('/api/antecedentes')
    // Asumiendo que vienen en un array o en res.data['hydra:member'] dependiendo de la config de API Platform
    const data = res.data['hydra:member'] || res.data
    antecedentes.value = data.filter(a => a.activo !== false)
  } catch (e) {
    console.error('Error al cargar antecedentes:', e)
    showSnackbar('Error al cargar los antecedentes.', 'error')
  }
}

// Cargar respuestas previas de la persona
const fetchRespuestas = async () => {
  const person = store.responseData?.value
  if (!person || !person.id) return

  try {
    // Buscamos las respuestas ya existentes para esta persona
    // Esto dependerá de si tenemos un endpoint filtrado o si lo hacemos manualmente
    // Por ahora intentamos obtener por colección filtrando por persona si es posible
    const res = await axios.get(`/api/personal-antecedentes?persona=${person.id}`)
    const data = res.data['hydra:member'] || res.data
    
    data.forEach(pa => {
      // pa.antecedente puede ser un IRI o un objeto con id
      const antId = typeof pa.antecedente === 'string' 
        ? pa.antecedente.split('/').pop() 
        : (pa.antecedente.id || pa.antecedente)
      
      respuestas[antId] = pa.respuesta
      // Guardamos el ID del registro PersonalAntecedentes para actualizaciones (opcional si usamos PATCH con lógica inteligente)
      paIds[antId] = pa.id
    });
  } catch (e) {
    console.error('Error al cargar respuestas:', e)
  }
}

const paIds = reactive({}) // Para guardar los IDs de PersonalAntecedentes existentes

const sortedAntecedentes = computed(() => {
  // Aquí podríamos añadir lógica de ordenación si fuera necesario
  return [...antecedentes.value]
})

const canSave = computed(() => {
  return !!store.responseData?.value?.id
})

const showSnackbar = (text, color = 'success') => {
  snackbar.text = text
  snackbar.color = color
  snackbar.show = true
}

const saveAll = async () => {
  const person = store.responseData?.value
  if (!person || !person.id) {
    showSnackbar('No se ha identificado a la persona.', 'error')
    return
  }

  saving.value = true
  try {
    const promises = []
    
    for (const ant of antecedentes.value) {
      if (ant.esTitulo) continue
      
      const respuesta = respuestas[ant.id] || ''
      const existingPaId = paIds[ant.id]
      const personIri = person['@id'] || `/api/personales/${person.id}`
      const antIri = ant['@id'] || `/api/antecedentes/${ant.id}`

      if (existingPaId) {
        // Actualizar
        promises.push(axios.patch(`/api/personal-antecedentes/${existingPaId}`, {
          respuesta: respuesta
        }, {
          headers: { 'Content-Type': 'application/merge-patch+json' }
        }))
      } else if (respuesta.trim() !== '') {
        // Crear nuevo
        promises.push(axios.post('/api/personal-antecedentes', {
          persona: personIri,
          antecedente: antIri,
          respuesta: respuesta
        }).then(res => {
          paIds[ant.id] = res.data.id
        }))
      }
    }

    if (promises.length > 0) {
      await Promise.all(promises)
      showSnackbar('Antecedentes guardados con éxito.')
      
      // Registrar avance A
      try {
        const personalId = person.id || person.ID || person.Id
        if (personalId) {
          await axios.post('/api/forma/registrar-avance-a', { personalId })
        }
      } catch (e2) {
        console.warn('No se pudo registrar avance A', e2)
      }
      
      emit('saved')
    } else {
      showSnackbar('No hay cambios para guardar.', 'info')
    }
  } catch (e) {
    console.error('Error al guardar antecedentes:', e)
    showSnackbar('Error al guardar los antecedentes.', 'error')
  } finally {
    saving.value = false
  }
}

// Opcional: Auto-guardar al perder el foco
const autoSave = async (antId) => {
  // Podríamos implementar auto-guardado individual aquí si se desea
}

onMounted(async () => {
  await fetchAntecedentes()
  await fetchRespuestas()
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
.personal-antecedentes-component {
  max-width: 800px;
  margin: 0 auto;
}
</style>
