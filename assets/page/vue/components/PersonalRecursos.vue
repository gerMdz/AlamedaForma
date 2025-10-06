<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from "../../../vendor/axios/axios.index";

const emit = defineEmits(['saved'])

const props = defineProps({
  personaId: {type: String, default: ''},
  email: {type: String, default: ''},
  phone: {type: String, default: ''}
})

const persona = ref(null)
const loading = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')

// Campos de texto
const vocacion = ref('')
const trabajos = ref('')
const clases = ref('')
const contribucion = ref('')

// Habilidades activas
const habilidades = ref([]) // [{id, nombre, discripcion}]
const selectedHabIds = ref([])
const maxHabs = 5
const canSelectMore = computed(() => selectedHabIds.value.length < maxHabs)

function toggleHab(id) {
  const idx = selectedHabIds.value.indexOf(id)
  if (idx >= 0) {
    selectedHabIds.value.splice(idx, 1)
  } else if (canSelectMore.value) {
    selectedHabIds.value.push(id)
  }
}

// Persona activa (mismo patrón que Orientacion.vue)
const fetchPersonaActiva = async () => {
  if (props.personaId) {
    try {
      const res = await axios.get(`api/personales/${props.personaId}`)
      if (res?.data) {
        persona.value = res.data
        return
      }
    } catch (e) {
      persona.value = {id: props.personaId, email: props.email || null, phone: props.phone || null}
      return
    }
  }
  const params = {}
  if (props.email && props.phone) {
    params.email = props.email
    params.phone = props.phone
  }
  const res = await axios.get('api/personales', {params})
  const list = res?.data?.member || []
  if (list.length > 0) {
    persona.value = list[0]
  }
}

const fetchHabilidades = async () => {
  const res = await axios.get('api/habilidades')
  const list = res?.data?.member || []
  habilidades.value = list.filter(h => !h.deletedAt)
    .sort((a, b) => (a.nombre || '').localeCompare(b.nombre || ''))
}

// Cargar datos existentes de PersonalRecursos y PRH
const pr = ref(null) // PersonalRecursos actual

const fetchExistingRecursos = async () => {
  if (!persona.value?.id) return
  try {
    // ApiFilter exact por persona usando IRI
    const iriPersona = `/api/personales/${persona.value.id}`
    const res = await axios.get('api/personal-recursos', {params: {persona: iriPersona}})
    const list = res?.data?.member || []
    if (list.length > 0) {
      pr.value = list[0]
      vocacion.value = pr.value.vocacion || ''
      trabajos.value = pr.value.trabajos || ''
      clases.value = pr.value.clases || ''
      contribucion.value = pr.value.contribucion || ''
    }
  } catch (e) {
    // ignore: puede que no exista aún
  }
}

const fetchExistingPRH = async () => {
  if (!pr.value?.id) return
  try {
    const iriPR = `/api/personal-recursos/${pr.value.id}`
    const res = await axios.get('api/personal-recursos-habilidades', {params: {personalRecursos: iriPR}})
    const list = res?.data?.member || []
    // preseleccionar habilidades
    selectedHabIds.value = list.map(x => x?.habilidad?.id).filter(Boolean)
  } catch (e) {
    // ignore
  }
}

onMounted(async () => {
  loading.value = true
  errorMsg.value = ''
  try {
    await fetchPersonaActiva()
    await fetchHabilidades()
    await fetchExistingRecursos()
    await fetchExistingPRH()
  } finally {
    loading.value = false
  }
})

// Guardado
const save = async () => {
  errorMsg.value = ''
  successMsg.value = ''
  if (!persona.value?.id) {
    errorMsg.value = 'No hay persona activa para guardar.'
    return
  }
  if (selectedHabIds.value.length > maxHabs) {
    errorMsg.value = `Solo puedes seleccionar hasta ${maxHabs} habilidades.`
    return
  }

  saving.value = true
  try {
    // 1) Upsert PersonalRecursos con el controlador custom (acepta persona UUID)
    const upsertPayload = {
      persona: persona.value.id,
      vocacion: vocacion.value || null,
      trabajos: trabajos.value || null,
      clases: clases.value || null,
      contribucion: contribucion.value || null,
    }
    const res = await axios.post('api/personal-recursos', upsertPayload)
    const saved = res?.data
    if (!saved?.id) {
      throw new Error('Respuesta inválida al guardar recursos')
    }
    pr.value = saved

    // 2) Sincronizar PRH (PersonalRecursosHabilidades)
    const iriPR = `/api/personal-recursos/${pr.value.id}`
    // Leer actuales para calcular diff
    let current = []
    try {
      const curRes = await axios.get('api/personal-recursos-habilidades', {params: {personalRecursos: iriPR}})
      current = curRes?.data?.member || []
    } catch (e) {}

    const currentHabIds = new Set(current.map(x => x?.habilidad?.id).filter(Boolean))
    const selectedSet = new Set(selectedHabIds.value)

    const toAdd = [...selectedSet].filter(id => !currentHabIds.has(id))
    const toRemove = current.filter(x => x?.habilidad?.id && !selectedSet.has(x.habilidad.id))

    // Agregar nuevos (con IRIs)
    const addPromises = toAdd.map(hid => axios.post('api/personal-recursos-habilidades', {
      personalRecursos: iriPR,
      habilidad: `/api/habilidades/${hid}`,
    }))
    // Eliminar los que ya no están
    const delPromises = toRemove.map(x => axios.delete(`api/personal-recursos-habilidades/${x.id}`))

    await Promise.all([...addPromises, ...delPromises])

    successMsg.value = 'Recursos guardados correctamente.'

    emit('saved', {
      personaId: persona.value.id,
      recursosId: pr.value.id,
      vocacion: vocacion.value || null,
      trabajos: trabajos.value || null,
      clases: clases.value || null,
      contribucion: contribucion.value || null,
      habilidadesIds: [...selectedHabIds.value],
    })
  } catch (e) {
    console.error(e)
    errorMsg.value = e?.response?.data?.detail || e?.response?.data?.error || 'Ocurrió un error al guardar.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <v-container fluid class="fill-height" style="max-width: 960px; margin: 0 auto;">
    <h1 class="text-center">[R]ecursos Personales</h1>

    <div class="mb-6">
      <p class="font-weight-medium mb-2">1. Mi vocación es la siguiente:</p>
      <v-textarea v-model="vocacion" auto-grow rows="2" variant="outlined" label="Mi vocación"></v-textarea>
    </div>

    <div class="mb-6">
      <p class="font-weight-medium mb-2">2. Otros trabajos o habilidades en las cuales yo tengo experiencia:</p>
      <v-textarea v-model="trabajos" auto-grow rows="2" variant="outlined" label="Trabajos o habilidades"></v-textarea>
    </div>

    <div class="mb-6">
      <p class="font-weight-medium mb-2">3. He dado clases o seminarios en:</p>
      <v-textarea v-model="clases" auto-grow rows="2" variant="outlined" label="Clases o seminarios"></v-textarea>
    </div>

    <div class="mb-6">
      <p class="font-weight-medium mb-2">4. Mi contribución personal de mayor valor es:</p>
      <v-textarea v-model="contribucion" auto-grow rows="2" variant="outlined" label="Contribución de mayor valor"></v-textarea>
    </div>

    <div class="mb-6">
      <p class="font-weight-medium mb-2">5. Selecciona hasta {{ maxHabs }} habilidades con las que te identificas:</p>
      <div v-if="habilidades.length === 0" class="text-medium-emphasis">No hay habilidades activas.</div>
      <v-row>
        <v-col v-for="h in habilidades" :key="h.id" cols="12" md="6">
          <v-card variant="outlined" class="pa-3">
            <div class="d-flex align-center">
              <v-checkbox
                :model-value="selectedHabIds.includes(h.id)"
                :label="h.nombre"
                :disabled="!selectedHabIds.includes(h.id) && !canSelectMore"
                hide-details
                @change="() => toggleHab(h.id)"
              />
            </div>
            <div class="text-body-2 text-medium-emphasis">{{ h.discripcion }}</div>
          </v-card>
        </v-col>
      </v-row>
      <div class="mt-2">Seleccionadas: {{ selectedHabIds.length }} / {{ maxHabs }}</div>
    </div>

    <v-alert v-if="errorMsg" type="error" class="mb-3">{{ errorMsg }}</v-alert>
    <v-alert v-if="successMsg" type="success" class="mb-3">{{ successMsg }}</v-alert>

    <div class="d-flex justify-end">
      <v-btn color="primary" :loading="saving" :disabled="loading || saving" @click="save">Guardar</v-btn>
    </div>
  </v-container>
</template>

<style scoped>
</style>
