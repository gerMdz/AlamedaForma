<template>
  <v-container fluid class="fill-height w-auto">
    <VForm @submit="handleSubmit" class="large-form mx-auto">
      <!-- Cabecera DISC -->
      <div class="d-flex align-center justify-space-between mb-4">
        <div class="text-subtitle-1">Auto‑test DISC</div>
        <div>
          <VBtn size="small" variant="text" color="secondary" class="mr-2" :disabled="!canUndo" @click="undo">
            <v-icon start icon="mdi-undo" /> Deshacer
          </VBtn>
          <VBtn size="small" variant="text" color="secondary" @click="clearAll">
            <v-icon start icon="mdi-eraser" /> Borrar todo
          </VBtn>
        </div>
      </div>

      <!-- Grid de preguntas (inputs numéricos, estilo de la maqueta) -->
      <VRow v-for="(row, index) in questions" :key="row.id" class="align-center">
        <VCol cols="12" md="3">
          <v-sheet class="pa-2 ma-2 d-flex align-center">
            <v-text-field
              v-model.number="answers[index].d"
              type="number"
              min="1"
              max="4"
              step="1"
              density="comfortable"
              variant="outlined"
              hide-details
              clearable
              class="mr-3"
              @keydown.delete.prevent="clearCell(index, 'd')"
              @change="normalizeCell(index, 'd')"
            />
            <div>{{ row.D }}</div>
          </v-sheet>
        </VCol>
        <VCol cols="12" md="3">
          <v-sheet class="pa-2 ma-2 d-flex align-center">
            <v-text-field
              v-model.number="answers[index].i"
              type="number"
              min="1"
              max="4"
              step="1"
              density="comfortable"
              variant="outlined"
              hide-details
              clearable
              class="mr-3"
              @keydown.delete.prevent="clearCell(index, 'i')"
              @change="normalizeCell(index, 'i')"
            />
            <div>{{ row.I }}</div>
          </v-sheet>
        </VCol>
        <VCol cols="12" md="3">
          <v-sheet class="pa-2 ma-2 d-flex align-center">
            <v-text-field
              v-model.number="answers[index].s"
              type="number"
              min="1"
              max="4"
              step="1"
              density="comfortable"
              variant="outlined"
              hide-details
              clearable
              class="mr-3"
              @keydown.delete.prevent="clearCell(index, 's')"
              @change="normalizeCell(index, 's')"
            />
            <div>{{ row.S }}</div>
          </v-sheet>
        </VCol>
        <VCol cols="12" md="3">
          <v-sheet class="pa-2 ma-2 d-flex align-center">
            <v-text-field
              v-model.number="answers[index].c"
              type="number"
              min="1"
              max="4"
              step="1"
              density="comfortable"
              variant="outlined"
              hide-details
              clearable
              class="mr-3"
              @keydown.delete.prevent="clearCell(index, 'c')"
              @change="normalizeCell(index, 'c')"
            />
            <div>{{ row.C }}</div>
            <VBtn size="x-small" variant="text" color="secondary" class="ml-auto" @click="clearRow(index)" :title="'Borrar fila ' + (index+1)">
              <v-icon icon="mdi-eraser" />
            </VBtn>
          </v-sheet>
          <div v-if="rowError(index)" class="text-error text-caption ml-4">{{ rowError(index) }}</div>
        </VCol>
      </VRow>

      <!-- Sumadores -->
      <div class="mt-4">
        <VRow class="text-center font-weight-medium">
          <VCol cols="12" sm="6" md="3">
            <div>Σ D: <strong :class="sumClass(sumD)">{{ sumD }}</strong></div>
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <div>Σ I: <strong :class="sumClass(sumI)">{{ sumI }}</strong></div>
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <div>Σ S: <strong :class="sumClass(sumS)">{{ sumS }}</strong></div>
          </VCol>
          <VCol cols="12" sm="6" md="3">
            <div>Σ C: <strong :class="sumClass(sumC)">{{ sumC }}</strong></div>
          </VCol>
        </VRow>
        <div class="text-center mt-2">Total: <strong :class="{ 'text-success': total === 120 }">{{ total }}</strong> / 120</div>
        <div v-if="validationMsg" class="text-center mt-2 text-error">{{ validationMsg }}</div>
        <div v-if="!props.introExtroReady" class="mt-4">
          <v-alert type="info" variant="tonal">Completa Intro/Extro para habilitar el guardado del test DISC.</v-alert>
        </div>
        <div v-if="successMsg" class="text-center mt-2 text-success">{{ successMsg }}</div>
      </div>

      <div class="text-center mt-6">
        <VBtn :loading="saving" :disabled="!canSave" type="submit" color="primary">Guardar DISC</VBtn>
      </div>
    </VForm>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../../vendor/axios/axios.index'

const emit = defineEmits(['saved'])

const props = defineProps({
  personaId: { type: String, required: true },
  // Intro/Extro debe estar completo para habilitar el guardado final
  introExtroReady: { type: Boolean, default: false },
  // Snapshot de Intro/Extro (objeto { [rowId]: 'intro'|'extro'|'mitad' })
  introExtroData: { type: Object, default: () => ({}) },
})

const questions = ref([]) // [{id, D,I,S,C}]
const answers = ref([])   // [{d,i,s,c}]
const loading = ref(false)
const saving = ref(false)
const errorMsg = ref('')
const successMsg = ref('')

// Borrador local + undo
const draftKey = computed(() => `disc-draft-${props.personaId || 'anon'}`)
const history = ref([]) // stack de snapshots JSON
const canUndo = computed(() => history.value.length > 0)
function pushHistory() {
  try { history.value.push(JSON.stringify(answers.value)); if (history.value.length > 20) history.value.shift() } catch(_) {}
}
function undo(){
  if (!canUndo.value) return
  try {
    const prev = history.value.pop()
    if (prev) answers.value = JSON.parse(prev)
  } catch(_) {}
}

function saveDraft(){
  try { localStorage.setItem(draftKey.value, JSON.stringify({ ids: questions.value.map(q=>q.id), answers: answers.value })) } catch(_) {}
}
function loadDraft(){
  try {
    const raw = localStorage.getItem(draftKey.value)
    if (!raw) return
    const parsed = JSON.parse(raw)
    const ids = (parsed?.ids)||[]
    if (ids.length && JSON.stringify(ids) === JSON.stringify(questions.value.map(q=>q.id))) {
      const arr = parsed?.answers
      if (Array.isArray(arr) && arr.length === questions.value.length) {
        answers.value = arr
      }
    }
  } catch(_) {}
}

async function fetchData() {
  loading.value = true
  errorMsg.value = ''
  try {
    const response = await axios.get('/api/personalidad')
    const arr = Array.isArray(response.data) ? response.data : (response.data?.member || [])
    questions.value = arr
    answers.value = arr.map(() => ({ d: null, i: null, s: null, c: null }))
    loadDraft()
  } catch (err) {
    console.error(err)
    errorMsg.value = 'No se pudieron cargar las filas del test DISC.'
  } finally {
    loading.value = false
  }
}

onMounted(fetchData)

// Normalización y helpers de edición
function normalizeCell(index, key){
  const v = answers.value[index]?.[key]
  if (v === null || v === undefined || v === '') return
  let num = Number(v)
  if (!Number.isFinite(num)) { answers.value[index][key] = null; return }
  num = Math.trunc(num)
  if (num < 1) num = 1
  if (num > 4) num = 4
  answers.value[index][key] = num
}

function clearCell(index, key){
  pushHistory()
  if (answers.value[index]) answers.value[index][key] = null
}

function clearRow(index){
  pushHistory()
  if (answers.value[index]) answers.value[index] = { d: null, i: null, s: null, c: null }
}

function clearAll(){
  if (!questions.value.length) return
  if (!confirm('¿Borrar todas las respuestas del test DISC?')) return
  pushHistory()
  answers.value = questions.value.map(() => ({ d: null, i: null, s: null, c: null }))
  try { localStorage.removeItem(draftKey.value) } catch(_) {}
}

// Validaciones por fila
function rowError(index){
  const a = answers.value[index]
  if (!a) return ''
  const vals = [a.d, a.i, a.s, a.c]
  const anyVal = vals.some(v => v !== null && v !== undefined && v !== '')
  const missing = vals.some(v => v === null || v === undefined || v === '')
  // fuera de rango
  if (vals.some(v => v !== null && v !== undefined && (v < 1 || v > 4))) return 'Valores entre 1 y 4'
  // duplicados
  const filtered = vals.filter(v => v !== null && v !== undefined)
  const hasDup = new Set(filtered).size !== filtered.length
  if (hasDup) return 'No repitas números en la fila'
  if (anyVal && missing) return 'Completa los cuatro valores (1–4)'
  // cuando está completa pero no es permuta exacta 1..4
  if (!missing) {
    const sorted = [...vals].sort()
    if (JSON.stringify(sorted) !== JSON.stringify([1,2,3,4])) return 'Usa cada número 1,2,3,4 una sola vez'
  }
  return ''
}

// Validaciones globales
const allRowsComplete = computed(() => answers.value.length > 0 && answers.value.every(a => [a.d, a.i, a.s, a.c].every(v => typeof v === 'number')))

const rowIsValid = (a) => {
  const vals = [a.d, a.i, a.s, a.c]
  if (vals.some(v => v === null)) return false
  const sorted = [...vals].sort()
  return JSON.stringify(sorted) === JSON.stringify([1,2,3,4])
}

const allRowsValid = computed(() => answers.value.length > 0 && answers.value.every(rowIsValid))

const sumD = computed(() => answers.value.reduce((acc, a) => acc + (a.d || 0), 0))
const sumI = computed(() => answers.value.reduce((acc, a) => acc + (a.i || 0), 0))
const sumS = computed(() => answers.value.reduce((acc, a) => acc + (a.s || 0), 0))
const sumC = computed(() => answers.value.reduce((acc, a) => acc + (a.c || 0), 0))
const total = computed(() => sumD.value + sumI.value + sumS.value + sumC.value)

const canSave = computed(() => allRowsValid.value && total.value === 120 && !!props.personaId && !!props.introExtroReady)

const validationMsg = computed(() => {
  if (!answers.value.length) return ''
  if (!allRowsComplete.value) return 'Completa todas las filas (D, I, S y C) con valores 1–4.'
  if (!allRowsValid.value) return 'En cada fila usa 1, 2, 3 y 4 una única vez (sin repetir).'
  if (total.value !== 120) return 'El total debe ser 120.'
  return ''
})

function sumClass(v){
  return v > 0 ? 'text-primary' : ''
}

watch(answers, () => { saveDraft() }, { deep: true })

async function handleSubmit(event){
  event?.preventDefault?.()
  successMsg.value = ''
  errorMsg.value = ''
  if (!canSave.value) return
  saving.value = true
  try {
    // 1) Guardar snapshot de Intro/Extro primero (una sola vez al final)
    if (!props.introExtroReady) {
      errorMsg.value = 'Completa Intro/Extro para poder guardar.'
      saving.value = false
      return
    }
    const iePayload = { personId: props.personaId, introExtro: props.introExtroData }
    await axios.post('/api/personal-intro-extro', iePayload)

    // 2) Guardar sumas DISC
    const discPayload = { personId: props.personaId, d: sumD.value, i: sumI.value, s: sumS.value, c: sumC.value }
    const res = await axios.post('/api/personal-disc', discPayload)

    successMsg.value = 'Resultados guardados correctamente.'
    // Limpiar borrador local para evitar reenvíos accidentales al recargar
    try { localStorage.removeItem(draftKey.value) } catch(_){ }
    emit('saved', { ...discPayload, id: res?.data?.id || null })
  } catch (e) {
    console.error(e)
    const status = e?.response?.status || 0
    if (status === 401 || status === 403) {
      errorMsg.value = 'No autorizado. Inicia sesión para guardar tus resultados.'
    } else if (status === 422) {
      errorMsg.value = 'Faltan requisitos previos para guardar.'
    } else {
      errorMsg.value = 'No se pudo guardar. Intenta nuevamente.'
    }
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.large-form {
  width: 100%;
  max-width: 1280px;
}
</style>