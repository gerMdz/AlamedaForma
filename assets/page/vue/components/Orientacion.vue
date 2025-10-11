<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from "../../../vendor/axios/axios.index";

const emit = defineEmits(['saved'])

// Props with identity to fetch exact same persona as SavedResults
const props = defineProps({
  personaId: {type: String, default: ''},
  email: {type: String, default: ''},
  phone: {type: String, default: ''}
})

// Active person
const persona = ref(null)
const loading = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')

// Results/Avance for O
const hasAvanceO = ref(false)
const editMode = ref(false)

// Form fields for PersonalOrientacion
const action_1 = ref('')
const action_2 = ref('')
const action_3 = ref('')
const trabajar = ref('')
const resolver = ref('')

// DetalleOrientacion list and selection (max 3)
const detalles = ref([])
const selected = ref([]) // will hold DetalleOrientacion ids
const canSelectMore = computed(() => selected.value.length < 3)

// Helper para extraer ID desde IRI o desde objeto expandido
const extractId = (val) => {
  if (!val) return null
  if (typeof val === 'string') {
    const parts = val.split('/')
    return parts[parts.length - 1] || null
  }
  if (typeof val === 'object' && val.id) return val.id
  return null
}

// Existing PersonalOrientacion entity
const po = ref(null)

const toggleSelect = (id) => {
  const idx = selected.value.indexOf(id)
  if (idx >= 0) {
    selected.value.splice(idx, 1)
  } else if (canSelectMore.value) {
    selected.value.push(id)
  }
}

// Consider completed if there is an existing record and some content or selections
const computeLocalCompletion = () => {
  const hasPO = !!po.value?.id
  const hasFields = [action_1.value, action_2.value, action_3.value, trabajar.value, resolver.value]
    .some(v => (v ?? '').toString().trim().length > 0)
  const hasChoices = (selected.value?.length || 0) > 0
  return !!(hasPO && (hasFields || hasChoices))
}

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

const fetchDetalles = async () => {
  try {
    const res = await axios.get('api/detalle-orientacion')
    const list = res?.data?.member || []
    detalles.value = list.filter(d => !d.deletedAt)
        .sort((a, b) => (a.orden ?? 0) - (b.orden ?? 0))
  } catch (e) {
    try {
      const res2 = await axios.get('api/detalle-orientaciones')
      const list2 = res2?.data?.member || []
      detalles.value = list2.filter(d => !d.deletedAt)
          .sort((a, b) => (a.orden ?? 0) - (b.orden ?? 0))
    } catch (err) {
      console.error(err)
      errorMsg.value = 'No fue posible cargar las opciones de orientación.'
    }
  }
}

const fetchExistingOrientacion = async () => {
  if (!persona.value?.id) return
  try {
    const iriPersona = `/api/personales/${persona.value.id}`
    const res = await axios.get('api/personal-orientacion', { params: { persona: iriPersona }})
    const list = res?.data?.member || []
    if (list.length > 0) {
      po.value = list[0]
      action_1.value = po.value.action_1 || ''
      action_2.value = po.value.action_2 || ''
      action_3.value = po.value.action_3 || ''
      trabajar.value = po.value.trabajar || ''
      resolver.value = po.value.resolver || ''
      // si ya hay información local, considerar completado
      if (computeLocalCompletion()) {
        hasAvanceO.value = true
      }
    }
  } catch (e) { /* noop */ }
}

const fetchExistingPODetalles = async () => {
  if (!po.value?.id) return
  try {
    const iriPO = `/api/personal-orientacion/${po.value.id}`
    const res = await axios.get('api/personal-orientacion-detalle', { params: { personalOrientacion: iriPO }})
    const list = res?.data?.member || []
    selected.value = list.map(x => extractId(x?.detalleOrientacion)).filter(Boolean)
    // tras cargar selecciones, re-evaluar finalización local
    if (computeLocalCompletion()) {
      hasAvanceO.value = true
    }
  } catch (e) { /* noop */ }
}

const fetchAvanceO = async () => {
  if (!persona.value?.id) return
  try {
    const res = await axios.get(`api/forma/avance-o-estado/${encodeURIComponent(persona.value.id)}`)
    const apiHas = !!res?.data?.hasAvanceO
    // mantener "true" si ya lo inferimos localmente
    hasAvanceO.value = apiHas || computeLocalCompletion()
  } catch (e) {
    // si falla la API, al menos usar la inferencia local
    hasAvanceO.value = computeLocalCompletion()
  }
}

// Sugerencias y diálogos
const suggestionsDialog = ref(false)
const resolverSuggestionsDialog = ref(false)
const suggestions = ref([
  'Niños', 'Adolescentes', 'Jóvenes', 'Matrimonios', 'Adultos mayores', 'Personas en situación de vulnerabilidad',
  'Personas con adicciones', 'Personas privadas de su libertad', 'Personas con discapacidad', 'Mujeres', 'Hombres'
])
const resolverSuggestions = ref([
  'Evangelismo en la comunidad', 'Discipulado', 'Misiones', 'Consejería', 'Obras de misericordia',
  'Acompañamiento a familias', 'Niñez y educación', 'Adoración y música', 'Intercesión', 'Tecnología y medios'
])
const addSuggestion = (text) => {
  const cur = (trabajar.value || '').trim()
  if (!cur) { trabajar.value = text; return }
  // evitar duplicados simples
  const parts = cur.split(/,\s*/).map(s => s.trim()).filter(Boolean)
  if (!parts.includes(text)) parts.push(text)
  trabajar.value = parts.join(', ')
}
const addResolverSuggestion = (text) => {
  const cur = (resolver.value || '').trim()
  if (!cur) { resolver.value = text; return }
  const parts = cur.split(/,\s*/).map(s => s.trim()).filter(Boolean)
  if (!parts.includes(text)) parts.push(text)
  resolver.value = parts.join(', ')
}

onMounted(async () => {
  loading.value = true
  errorMsg.value = ''
  try {
    await fetchPersonaActiva()
    await fetchDetalles()
    await fetchExistingOrientacion()
    await fetchExistingPODetalles()
    await fetchAvanceO()
    // Si ya está completo (por API o inferencia local), registrar Avance O (idempotente)
    if (persona.value?.id && (hasAvanceO.value || computeLocalCompletion())) {
      try { await axios.post('api/forma/registrar-avance-o', { personalId: persona.value.id }) } catch (_) {}
      hasAvanceO.value = true
    }
  } finally {
    loading.value = false
  }
})

const save = async () => {
  errorMsg.value = ''
  successMsg.value = ''
  if (!persona.value?.id) {
    errorMsg.value = 'No hay persona activa para guardar.'
    return
  }
  if (selected.value.length > 3) {
    errorMsg.value = 'Solo puedes seleccionar hasta 3 opciones.'
    return
  }
  saving.value = true
  try {
    // 1) Crear/actualizar PersonalOrientacion
    const payload = {
      persona: `/api/personales/${persona.value.id}`,
      action_1: action_1.value || null,
      action_2: action_2.value || null,
      action_3: action_3.value || null,
      trabajar: trabajar.value || null,
      resolver: resolver.value || null,
    }
    const res = await axios.post('api/personal-orientacion', payload)
    const savedPO = res?.data
    if (!savedPO?.id) {
      throw new Error('Respuesta inválida al guardar orientación')
    }
    po.value = savedPO

    // 2) Crear hasta 3 PersonalOrientacionDetalle con posicion 1..3
    // Primero obtener actuales para calcular diff (si existieran)
    const iriPO = `/api/personal-orientacion/${po.value.id}`
    let current = []
    try {
      const cur = await axios.get('api/personal-orientacion-detalle', { params: { personalOrientacion: iriPO }})
      current = cur?.data?.member || []
    } catch (_) {}

    const currentIds = new Set(current.map(x => extractId(x?.detalleOrientacion)).filter(Boolean))
    const selectedSet = new Set(selected.value)

    const toAdd = [...selectedSet].filter(id => !currentIds.has(id))
    const toRemove = current.filter(x => {
      const did = extractId(x?.detalleOrientacion)
      return did && !selectedSet.has(did)
    })

    const addPromises = toAdd.map((detalleId, index) => axios.post('api/personal-orientacion-detalle', {
      personalOrientacion: iriPO,
      detalleOrientacion: `/api/detalle-orientacion/${detalleId}`,
      posicion: (index + 1)
    }))
    const delPromises = toRemove.map(x => axios.delete(`api/personal-orientacion-detalle/${x.id}`))

    await Promise.all([...addPromises, ...delPromises])

    // Registrar avance O (idempotente)
    try {
      await axios.post('api/forma/registrar-avance-o', { personalId: persona.value.id })
    } catch (_) { /* noop */ }

    successMsg.value = 'Resultados guardados correctamente.'
    hasAvanceO.value = true
    editMode.value = false

    // Emitir evento con resumen
    const summary = {
      action_1: action_1.value || null,
      action_2: action_2.value || null,
      action_3: action_3.value || null,
      trabajar: trabajar.value || null,
      resolver: resolver.value || null,
      selectedIds: [...selected.value],
      selectedLabels: detalles.value
          .filter(d => selected.value.includes(d.id))
          .map(d => d.descripcion),
      personalOrientacionId: po.value.id,
      personaId: persona.value?.id || null,
    }
    emit('saved', summary)
  } catch (e) {
    console.error(e)
    errorMsg.value = e?.response?.data?.detail || 'Ocurrió un error al guardar.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <v-container fluid class="fill-height" style="max-width: 1240px; margin: 0 auto;">

    <!-- Vista de resultados si ya hay Avance O y no estamos editando -->
    <template v-if="hasAvanceO && !editMode">
      <v-row>
        <v-col cols="12" md="12" class="mx-auto">
          <v-card title="Resumen de tu orientación" class="mb-4">
            <v-list lines="two">
              <v-list-item>
                <v-list-item-title><strong>Mis acciones:</strong></v-list-item-title>
                <v-list-item-subtitle>{{ action_1 || '—' }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title><strong>Con quién me gusta trabajar:</strong></v-list-item-title>
                <v-list-item-subtitle>{{ trabajar || '—' }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title><strong>Asuntos que me entusiasman o preocupan:</strong></v-list-item-title>
                <v-list-item-subtitle>{{ resolver || '—' }}</v-list-item-subtitle>
              </v-list-item>
              <v-list-item>
                <v-list-item-title><strong>Top 3 cosas que me apasiona hacer:</strong></v-list-item-title>
                <v-list-item-subtitle>
                  <template v-if="selected.length > 0">
                    <ol class="mt-1 mb-0 pl-4">
                      <li v-for="id in selected" :key="id">{{ (detalles.find(d => d.id === id)?.descripcion) || '—' }}</li>
                    </ol>
                  </template>
                  <span v-else class="text-medium-emphasis">No seleccionaste elementos.</span>
                </v-list-item-subtitle>
              </v-list-item>
            </v-list>
          </v-card>
        </v-col>
      </v-row>
    </template>

    <!-- Formulario de carga/edición -->
    <template v-else>
      <h1 class="text-center">[O]rientación del corazón</h1>
      <p class="text-center subtitle-1 mb-4">(¿Por qué late tu corazón?)</p>
      <div class="text-left mb-4">
        <p>La Biblia usa el término "corazón" para representar el centro de tu motivación, deseos e inclinaciones.</p>
        <p><i>"Deléitate con el Señor, Él te dará lo que tu corazón anhela."</i></p>
        <p>Salmo 37:4</p>
      </div>

      <div class="mb-6">
        <p class="font-weight-medium mb-2">Si yo supiera que no podría fallar, yo trataría de hacer con mi vida lo
          siguiente para Dios:</p>
        <v-textarea v-model="action_1" auto-grow rows="2" variant="outlined" class="mb-3" label="Mis acciones"/>
        <!--      <v-textarea v-model="action_2" auto-grow rows="2" variant="outlined" class="mb-3" label="Acción 2" />-->
        <!--      <v-textarea v-model="action_3" auto-grow rows="2" variant="outlined" class="mb-3" label="Acción 3" />-->
        <p class="font-weight-medium mb-2">
          Con quien me gusta trabajar mas (edad y tipo de personas):
          <v-tooltip text="Sugerencias" location="top">
            <template #activator="{ props }">
              <v-btn v-bind="props" icon="mdi-help-circle" variant="text" size="small" class="ml-2" @click="suggestionsDialog = true"></v-btn>
            </template>
          </v-tooltip>
        </p>
        <v-textarea v-model="trabajar" auto-grow rows="2" variant="outlined" class="mb-3"
                    label="Con quién me gusta más trabajar, y la edad o el tipo de personas"/>

        <v-dialog v-model="suggestionsDialog" max-width="600">
          <v-card>
            <v-card-title>Sugerencias de respuestas</v-card-title>
            <v-card-text>
              <p>Selecciona una o varias opciones que describan con quién te gusta trabajar. Se añadirán al campo.</p>
              <div class="d-flex flex-wrap">
                <v-chip
                  v-for="s in suggestions"
                  :key="s"
                  class="ma-1"
                  variant="outlined"
                  @click="addSuggestion(s)"
                >{{ s }}</v-chip>
              </div>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text="Cerrar" @click="suggestionsDialog = false"></v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

        <p class="font-weight-medium mb-2">
          Asuntos de la iglesia, ministerios o necesidades que más me entusiasman o
          conciernen:
          <v-tooltip text="Sugerencias" location="top">
            <template #activator="{ props }">
              <v-btn v-bind="props" icon="mdi-help-circle" variant="text" size="small" class="ml-2" @click="resolverSuggestionsDialog = true"></v-btn>
            </template>
          </v-tooltip>
        </p>
        <v-textarea v-model="resolver" auto-grow rows="2" variant="outlined" class="mb-3"
                    label="Problemas, ministerios o posibles necesidades de la Iglesia que me apasiona resolver o me preocupan"/>

        <v-dialog v-model="resolverSuggestionsDialog" max-width="700">
          <v-card>
            <v-card-title>Sugerencias de temas</v-card-title>
            <v-card-text>
              <p>Selecciona una o varias opciones que describan los asuntos, ministerios o necesidades que más te entusiasman o te preocupan. Se añadirán al campo.</p>
              <div class="d-flex flex-wrap">
                <v-chip
                  v-for="s in resolverSuggestions"
                  :key="s"
                  class="ma-1"
                  variant="outlined"
                  @click="addResolverSuggestion(s)"
                >{{ s }}</v-chip>
              </div>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn text="Cerrar" @click="resolverSuggestionsDialog = false"></v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </div>

      <div class="mb-4">
        <h2 class="text-left">Qué es lo que me apasiona hacer</h2>
        <p class="text-left caption">* Marca los 3 principales que desatan una pasión en ti</p>

        <v-alert v-if="errorMsg" type="error" class="my-2">{{ errorMsg }}</v-alert>
        <v-alert v-if="successMsg" type="success" class="my-2">{{ successMsg }}</v-alert>

        <v-skeleton-loader v-if="loading" type="list-item@6" class="my-3"/>

        <v-row v-else>
          <v-col v-for="d in detalles" :key="d.id" cols="12" sm="6" md="4">
            <v-checkbox
                :label="d.descripcion"
                :value="d.id"
                v-model="selected"
                :disabled="!selected.includes(d.id) && !canSelectMore"
            />
          </v-col>
        </v-row>
      </div>

      <div class="text-right mt-6">
        <v-btn color="primary" :loading="saving" :disabled="saving || !persona" @click="save">
          Grabar resultados
        </v-btn>
      </div>
    </template>
  </v-container>
</template>

<style scoped>
.caption {
  color: #666;
}
</style>
