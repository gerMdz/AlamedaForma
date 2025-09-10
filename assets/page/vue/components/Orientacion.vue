<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from "../../../vendor/axios/axios.index";
const emit = defineEmits(['saved'])

// Props with identity to fetch exact same persona as SavedResults
const props = defineProps({
  personaId: { type: String, default: '' },
  email: { type: String, default: '' },
  phone: { type: String, default: '' }
})

// Active person (as per project pattern, usually the last created or selected one)
const persona = ref(null)
const loading = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')

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

const toggleSelect = (id) => {
  const idx = selected.value.indexOf(id)
  if (idx >= 0) {
    selected.value.splice(idx, 1)
  } else if (canSelectMore.value) {
    selected.value.push(id)
  }
}

const fetchPersonaActiva = async () => {
  // Prefer exact ID if provided by parent component
  if (props.personaId) {
    try {
      const res = await axios.get(`api/personales/${props.personaId}`)
      if (res?.data) {
        persona.value = res.data
        return
      }
    } catch (e) {
      // If item endpoint fails, at least set a minimal persona with the given id
      persona.value = { id: props.personaId, email: props.email || null, phone: props.phone || null }
      return
    }
  }
  // Fallback: try to resolve by email+phone
  const params = {}
  if (props.email && props.phone) {
    params.email = props.email
    params.phone = props.phone
  }
  const res = await axios.get('api/personales', { params })
  const list = res?.data?.member || []
  if (list.length > 0) {
    persona.value = list[0]
  }
}

const fetchDetalles = async () => {
  // There isn't an explicit ApiResource for DetalleOrientacion in the session history
  // but migrations exist. Let's expose via a small state endpoint if available.
  // If not, assume backend provides 'api/detalle-orientacion' collection through Api Platform defaults.
  try {
    const res = await axios.get('api/detalle-orientacion')
    const list = res?.data?.member || []
    // filter activos => deletedAt == null
    detalles.value = list.filter(d => !d.deletedAt)
      .sort((a, b) => (a.orden ?? 0) - (b.orden ?? 0))
  } catch (e) {
    // fallback: try a custom endpoint name if configured differently
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

onMounted(async () => {
  loading.value = true
  errorMsg.value = ''
  try {
    await fetchPersonaActiva()
    await fetchDetalles()
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
    // 1) Crear PersonalOrientacion
    const payload = {
      persona: `/api/personales/${persona.value.id}`,
      action_1: action_1.value || null,
      action_2: action_2.value || null,
      action_3: action_3.value || null,
      trabajar: trabajar.value || null,
      resolver: resolver.value || null,
    }
    const res = await axios.post('api/personal-orientacion', payload)
    const po = res?.data
    if (!po?.id) {
      throw new Error('Respuesta inválida al crear orientación')
    }

    // 2) Crear hasta 3 PersonalOrientacionDetalle con posicion 1..3
    const posts = selected.value.map((detalleId, index) => {
      return axios.post('api/personal-orientacion-detalle', {
        personalOrientacion: `/api/personal-orientacion/${po.id}`,
        detalleOrientacion: `/api/detalle-orientacion/${detalleId}`,
        posicion: index + 1,
      })
    })
    await Promise.all(posts)

    successMsg.value = 'Resultados guardados correctamente.'

    // Emitir evento al padre con un resumen para ocultar el formulario y mostrar los datos guardados
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
      personalOrientacionId: po.id,
      personaId: persona.value?.id || null,
    }
    emit('saved', summary)
  } catch (e) {
    console.error(e)
    // backend enforces max 3 y constraints
    errorMsg.value = e?.response?.data?.detail || 'Ocurrió un error al guardar.'
  } finally {
    saving.value = false
  }
}
</script>

<template>
  <v-container fluid class="fill-height" style="max-width: 960px; margin: 0 auto;">
    <h1 class="text-center">[O]rientación del corazón</h1>
    <p class="text-center subtitle-1 mb-4">(¿Por qué late tu corazón?)</p>

    <div class="text-left mb-4">
      <p>La Biblia usa el término "corazón" para representar el centro de tu motivación, deseos e inclinaciones.</p>
      <p><i>"Deléitate con el Señor, Él te dará lo que tu corazón anhela."</i></p>
      <p>Salmo 37:4</p>
    </div>

    <div class="mb-6">
      <p class="font-weight-medium mb-2">Tres cosas que me encanta hacer que traen alegría y satisfacción a mi vida:</p>
      <v-textarea v-model="action_1" auto-grow rows="2" variant="outlined" class="mb-3" label="Acción 1" />
      <v-textarea v-model="action_2" auto-grow rows="2" variant="outlined" class="mb-3" label="Acción 2" />
      <v-textarea v-model="action_3" auto-grow rows="2" variant="outlined" class="mb-3" label="Acción 3" />

      <v-textarea v-model="trabajar" auto-grow rows="2" variant="outlined" class="mb-3"
                  label="Con quién me gusta más trabajar, y la edad o el tipo de personas" />

      <v-textarea v-model="resolver" auto-grow rows="2" variant="outlined" class="mb-3"
                  label="Problemas, ministerios o posibles necesidades de la Iglesia que me apasiona resolver o me preocupan" />
    </div>

    <div class="mb-4">
      <h2 class="text-left">Qué es lo que me apasiona hacer</h2>
      <p class="text-left caption">* Marca los 3 principales que desatan una pasión en ti</p>

      <v-alert v-if="errorMsg" type="error" class="my-2">{{ errorMsg }}</v-alert>
      <v-alert v-if="successMsg" type="success" class="my-2">{{ successMsg }}</v-alert>

      <v-skeleton-loader v-if="loading" type="list-item@6" class="my-3" />

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
  </v-container>
</template>

<style scoped>
.caption { color: #666; }
</style>
