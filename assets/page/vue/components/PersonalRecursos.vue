<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from "../../../vendor/axios/axios.index";

const emit = defineEmits(['saved'])

const props = defineProps({
  personaId: {type: String, default: ''},
  email: {type: String, default: ''},
  phone: {type: String, default: ''},
  // Controla si este componente debe renderizar su propio encabezado de "Resumen R".
  // Por defecto lo ocultamos para evitar duplicación cuando el padre ya muestra un encabezado.
  showHeader: { type: Boolean, default: false },
})

const persona = ref(null)
const loading = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')

// Modo resultados para R segun AvanceForma
const hasAvanceR = ref(false)
const editMode = ref(false) // permitir editar después de ver resultados

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

// Extraer ID desde IRI o desde objeto expandido
const extractId = (val) => {
  if (!val) return null
  if (typeof val === 'string') {
    const parts = val.split('/')
    return parts[parts.length - 1] || null
  }
  if (typeof val === 'object') {
    if (val.id) return val.id
    if (typeof val['@id'] === 'string') {
      const parts = val['@id'].split('/')
      return parts[parts.length - 1] || null
    }
  }
  return null
}

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
    // preseleccionar habilidades (acepta IRI o objeto)
    selectedHabIds.value = list.map(x => extractId(x?.habilidad)).filter(Boolean)
  } catch (e) {
    // ignore
  }
}

const fetchAvanceR = async () => {
  if (!persona.value?.id) return
  try {
    const res = await axios.get(`api/forma/avance-r-estado/${encodeURIComponent(persona.value.id)}`)
    hasAvanceR.value = !!res?.data?.hasAvanceR
  } catch (e) {
    hasAvanceR.value = false
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
    await fetchAvanceR()
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
      persona: `/api/personales/${persona.value.id}`,
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

    const currentHabIds = new Set(current.map(x => extractId(x?.habilidad)).filter(Boolean))
    const selectedSet = new Set(selectedHabIds.value)

    const toAdd = [...selectedSet].filter(id => !currentHabIds.has(id))
    const toRemove = current.filter(x => {
      const hid = extractId(x?.habilidad)
      return hid && !selectedSet.has(hid)
    })

    // Agregar nuevos (con IRIs)
    const addPromises = toAdd.map(hid => axios.post('api/personal-recursos-habilidades', {
      personalRecursos: iriPR,
      habilidad: `/api/habilidades/${hid}`,
    }))
    // Eliminar los que ya no están
    const delPromises = toRemove.map(x => axios.delete(`api/personal-recursos-habilidades/${x.id}`))

    await Promise.all([...addPromises, ...delPromises])

    // 3) Registrar avance R (idempotente)
    try {
      await axios.post('api/forma/registrar-avance-r', { personalId: persona.value.id })
    } catch (_) { /* noop */ }

    successMsg.value = 'Recursos guardados correctamente.'
    hasAvanceR.value = true
    editMode.value = false

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
  <v-container fluid class="fill-height" style="max-width: 1280px; margin: 0 auto;">

    <!-- Vista de resultados si ya hay Avance R y no estamos editando -->
    <template v-if="hasAvanceR && !editMode">
      <div class="w-100">
      <!-- Encabezado (opcional) para evitar duplicaciones cuando el padre ya lo muestra -->
      <v-sheet v-if="props.showHeader" color="primary" class="text-white py-3 px-4 mb-3">
        <div class="d-flex align-center" style="gap:8px;">
          <v-icon icon="mdi-hammer-wrench" size="20" class="me-1"></v-icon>
          <span class="font-weight-medium">Resumen R (Recursos y habilidades)</span>
        </div>
      </v-sheet>

      <v-card class="mb-4">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" md="6">
              <v-card variant="outlined" class="pa-3 mb-3">
                <div class="text-subtitle-2 text-medium-emphasis">1. Vocación</div>
                <div class="text-body-2">{{ vocacion || '—' }}</div>
              </v-card>
              <v-card variant="outlined" class="pa-3">
                <div class="text-subtitle-2 text-medium-emphasis">3. Clases o seminarios</div>
                <div class="text-body-2">{{ clases || '—' }}</div>
              </v-card>
            </v-col>
            <v-col cols="12" md="6">
              <v-card variant="outlined" class="pa-3 mb-3">
                <div class="text-subtitle-2 text-medium-emphasis">2. Trabajos / experiencia</div>
                <div class="text-body-2">{{ trabajos || '—' }}</div>
              </v-card>
              <v-card variant="outlined" class="pa-3">
                <div class="text-subtitle-2 text-medium-emphasis">4. Contribución personal</div>
                <div class="text-body-2">{{ contribucion || '—' }}</div>
              </v-card>
            </v-col>
          </v-row>

          <div class="mt-4">
            <div class="text-subtitle-2 mb-2"><strong>5. Habilidades seleccionadas</strong></div>
            <div v-if="selectedHabIds.length === 0" class="text-medium-emphasis">No seleccionaste habilidades.</div>
            <div v-else class="grid-table">
              <div class="grid-header">Habilidad</div>
              <div class="grid-header">Descripción</div>
              <template v-for="hid in selectedHabIds" :key="hid">
                <div class="grid-cell">{{ (habilidades.find(h => h.id === hid)?.nombre) || 'Habilidad' }}</div>
                <div class="grid-cell text-medium-emphasis">{{ (habilidades.find(h => h.id === hid)?.discripcion) || '' }}</div>
              </template>
            </div>
          </div>
        </v-card-text>
        <v-card-actions class="d-flex justify-end" v-if="false">
          <v-btn color="primary" @click="editMode = true">Editar</v-btn>
        </v-card-actions>
      </v-card>
      </div>
    </template>

    <!-- Formulario de carga/edición -->
    <template v-else>
      <h2 class="section-title">[R]ecursos Personales</h2>
      <div class="question-block mb-6">
        <p class="font-weight-medium mb-2">1. Mi vocación es la siguiente:</p>
        <v-textarea v-model="vocacion" auto-grow rows="2" variant="outlined" label="Mi vocación" class="mb-0"></v-textarea>
      </div>

      <div class="question-block mb-6">
        <p class="font-weight-medium mb-2">2. Otros trabajos o habilidades en las cuales yo tengo experiencia:</p>
        <v-textarea v-model="trabajos" auto-grow rows="2" variant="outlined" label="Trabajos o habilidades" class="mb-0"></v-textarea>
      </div>

      <div class="question-block mb-6">
        <p class="font-weight-medium mb-2">3. He dado clases o seminarios en:</p>
        <v-textarea v-model="clases" auto-grow rows="2" variant="outlined" label="Clases o seminarios" class="mb-0"></v-textarea>
      </div>

      <div class="question-block mb-6">
        <p class="font-weight-medium mb-2">4. Mi contribución personal de mayor valor es:</p>
        <v-textarea v-model="contribucion" auto-grow rows="2" variant="outlined" label="Contribución de mayor valor" class="mb-0"></v-textarea>
      </div>

      <div class="mb-6">
        <p class="font-weight-medium mb-2">5. Selecciona hasta {{ maxHabs }} habilidades con las que te identificas:</p>
        <div v-if="habilidades.length === 0" class="text-medium-emphasis">No hay habilidades activas.</div>
        <v-row>
          <v-col v-for="h in habilidades" :key="h.id" cols="12" md="4">
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
    </template>
  </v-container>
</template>

<style scoped>
/* Asegurar apilado vertical y separación visual marcada */
.section-title {
  display: block; /* fuerza bloque completo */
  width: 100%;
  font-size: 1.375rem;
  line-height: 1.2;
  font-weight: 700;
  color: rgba(0,0,0,0.85);
  margin: 8px 0 18px; /* mayor separación del contenido */
  padding-bottom: 8px;
  border-bottom: 2px solid rgba(0,0,0,0.08);
}

.question-block {
  display: block; /* evita que se alinee en la misma fila */
  width: 100%;
  padding: 14px 16px;
  background: rgba(0, 0, 0, 0.035);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 10px;
}

/* Aumentar el aire entre bloques consecutivos */
.question-block + .question-block {
  margin-top: 16px;
}

/* Mejorar legibilidad del encabezado de cada pregunta */
.question-block > p {
  margin-bottom: 8px;
  font-weight: 600;
}
</style>


<style scoped>
/* Tabla ligera para listas (sin usar <table>) */
.grid-table {
  display: grid;
  grid-template-columns: 1fr 2fr;
  border: 1px solid rgba(0,0,0,0.08);
  border-radius: 8px;
  overflow: hidden;
}
.grid-header {
  background: rgba(0,0,0,0.04);
  font-weight: 600;
  padding: 8px 12px;
}
.grid-cell {
  padding: 8px 12px;
  border-top: 1px solid rgba(0,0,0,0.06);
}
/* separador vertical entre columnas en filas de datos */
.grid-table > .grid-cell:nth-child(4n+1),
.grid-table > .grid-cell:nth-child(4n+3) { /* primera col de cada fila de datos */
  border-right: 1px solid rgba(0,0,0,0.06);
}

@media (max-width: 600px) {
  .grid-table {
    grid-template-columns: 1fr; /* stack in mobile for readability */
  }
  /* Ocultar cabecera de descripción en móvil y dejar la de nombre */
  .grid-table .grid-header:nth-child(2) {
    display: none;
  }
  /* En móvil, añadir separador bajo cada nombre para diferenciar bloques */
  .grid-table > .grid-cell {
    border-right: none !important;
  }
}
</style>
