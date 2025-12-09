<template>
  <v-container fluid class="mt-4">
    <v-row>
      <v-col cols="12">
        <div class="text-h6 mb-2">Mi Personalidad</div>
        <div class="text-body-1 mb-3">
          Todos tenemos diferentes personalidades, diferentes mezclas de temperamentos. Así que, vamos a preguntarnos, ¿En dónde se adapta mejor mi personalidad para servir?
        </div>
        <div class="text-body-1 mb-3">
          Dios nunca ha usado el mismo molde para dos personas. Dios ama la variedad: simplemente mira a tu alrededor. No hay temperamento bueno o malo, correcto o incorrecto. Necesitamos los temperamentos opuestos para mantener a la iglesia balanceada.
        </div>
        <div class="text-body-1 mb-4">
          A continuación encontrarás un auto-test que te ayudará a generar una idea preliminar y básica acerca de tu temperamento:
        </div>
        <div class="text-body-1 mb-4" style="font-style: italic;">
          …porque ¿quién de entre los hombres conoce las cosas del hombre, sino el espíritu del hombre que está en él? Del mismo modo, nadie conoció las cosas de Dios, sino el Espíritu de Dios.
          <div class="mt-1">1ª Corintios 2:11</div>
        </div>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12">
        <v-progress-linear v-if="loading" indeterminate color="primary" class="mb-4" />

        <v-alert v-if="error" type="error" variant="tonal" class="mb-4">
          {{ error }}
        </v-alert>

        <v-alert v-if="!loading && !error && introExtroRows.length === 0" type="info" variant="tonal" class="mb-4">
          Aún no hay opciones de Intro/Extro configuradas.
        </v-alert>

        <v-divider inset class="my-8 opacity-50" />

        <v-sheet class="pa-4 mx-auto bg-transparent" rounded="lg" border="thin" elevation="0" style="max-width: 960px;">
          <div class="text-subtitle-1 mb-4">Auto‑test Intro/Extro</div>

          <!-- Lista de filas Intro/Extro: cada fila muestra 3 radios (Intro / Extro / 50/50) -->
          <div v-for="row in introExtroRows" :key="row.id" class="mb-3">
            <v-radio-group
              v-model="selectedByRow[row.id]"
              inline
              class="d-flex align-center justify-center flex-wrap text-center"
              role="radiogroup"
              :aria-label="`Fila ${row.intro} / ${row.extro} / ${row.mitad}`"
              @change="onRowChange(row)"
            >
              <v-radio :label="row.intro" :value="'intro'" class="mr-4" />
              <v-radio :label="row.extro" :value="'extro'" class="mr-4" />
              <v-radio :label="row.mitad" :value="'mitad'" />
            </v-radio-group>
          </div>
        </v-sheet>

        <!-- Separación sutil antes del DISC -->
        <v-divider class="my-8" />

        <!-- Bloque DISC -->
        <v-sheet v-if="!savedTotals" class="pa-4 mx-auto bg-transparent" rounded="lg" border="thin" elevation="0" style="width: 100%; max-width: 1280px;">
          <FormPersonalidades
                      :persona-id="props.personaId || ''"
                      :intro-extro-ready="introExtroReady"
                      :intro-extro-data="introExtroData"
                      @saved="onDiscSaved"
                    />
        </v-sheet>

        <!-- Gráfico simple con las sumas DISC (se muestra luego de guardar) -->
        <div v-if="savedTotals" class="mt-8 mx-auto" style="width:100%; max-width: 960px;">
          <v-card class="mb-4">
            <v-card-title>Resultados DISC</v-card-title>
            <v-card-text>
              <!-- Gráfico de líneas estilo PERFIL DISC (escala 12–48) -->
              <DiscProfileChart
                :d="chartVals.d"
                :i="chartVals.i"
                :s="chartVals.s"
                :c="chartVals.c"
                :raw-d="savedTotals?.d ?? null"
                :raw-i="savedTotals?.i ?? null"
                :raw-s="savedTotals?.s ?? null"
                :raw-c="savedTotals?.c ?? null"
                label-mode="normalized"
              />
            </v-card-text>
          </v-card>

          <!-- Resumen Intro/Extro (se mantiene visible junto al resultado) -->
          <v-card class="mb-4">
            <v-card-title>Intro / Extro</v-card-title>
            <v-card-text>
              <div v-if="introExtroSummary.length === 0" class="text-medium-emphasis">
                No hay selecciones registradas.
              </div>
              <v-list v-else density="compact">
                <v-list-item v-for="(item, idx) in introExtroSummary" :key="item.id">
                  <v-list-item-title>
                    <strong>{{ idx + 1 }}.</strong>
                    <span class="ml-2">{{ item.intro }}</span>
                    <span class="mx-1">/</span>
                    <span>{{ item.extro }}</span>
                    <span class="mx-2">→</span>
                    <span class="font-weight-medium" :class="{
                      'text-primary': item.choice === 'intro',
                      'text-success': item.choice === 'extro',
                    }">
                      {{ item.choiceLabel }}
                    </span>
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-card-text>
          </v-card>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from '../../../vendor/axios/axios.index'
import FormPersonalidades from './FormPersonalidades.vue'
import DiscProfileChart from './DiscProfileChart.vue'

const props = defineProps({
  personaId: { type: [String], required: false },
  email: { type: String, required: false, default: '' },
  phone: { type: String, required: false, default: '' },
})

const loading = ref(false)
const error = ref('')
const savedTotals = ref(null)

// Filas provenientes del catálogo IntroExtro
const introExtroRows = ref([]) // [{id, intro, extro, mitad}]
// Selección del usuario por fila: { [rowId]: 'intro' | 'extro' | 'mitad' }
const selectedByRow = ref({})

// Estado derivado: Intro/Extro completo y snapshot listo para guardar
const introExtroReady = computed(() => {
  const rows = introExtroRows.value || []
  if (!rows.length) return false
  for (const r of rows) {
    const v = selectedByRow.value?.[r.id]
    if (!(v === 'intro' || v === 'extro' || v === 'mitad')) return false
  }
  return true
})

const introExtroData = computed(() => {
  const out = {}
  const rows = introExtroRows.value || []
  for (const r of rows) {
    const v = selectedByRow.value?.[r.id]
    if (v === 'intro' || v === 'extro' || v === 'mitad') {
      out[r.id] = v
    }
  }
  return out
})

// Resumen legible de las selecciones Intro/Extro para mostrar junto al gráfico
const introExtroSummary = computed(() => {
  const rows = introExtroRows.value || []
  const sel = selectedByRow.value || {}
  return rows.map(r => {
    const choice = sel[r.id] ?? null
    let choiceLabel = ''
    if (choice === 'intro') choiceLabel = r.intro
    else if (choice === 'extro') choiceLabel = r.extro
    else if (choice === 'mitad') choiceLabel = r.mitad
    return {
      id: r.id,
      intro: r.intro,
      extro: r.extro,
      mitad: r.mitad,
      choice,
      choiceLabel,
    }
  }).filter(it => it.choice)
})

async function fetchIntroExtro() {
  loading.value = true
  error.value = ''
  try {
    // Cargar filas activas de Intro/Extro
    const { data } = await axios.get('/api/intro-extro?activo=true')
    const arr = Array.isArray(data) ? data : []
    introExtroRows.value = arr

    // Inicializar selección si fuese necesario (por ahora sin default)
    const sel = {}
    for (const r of arr) {
      if (!(r.id in sel)) sel[r.id] = null
    }
    selectedByRow.value = sel

    // Si hay persona, intentar recuperar el último snapshot guardado para pre-cargar selecciones
    if (props.personaId) {
      await loadExistingIntroExtro(props.personaId)
    }
  } catch (e) {
    console.error(e)
    const status = e?.response?.status || e?.status || 0
    if (status === 401 || status === 403) {
      error.value = 'No autorizado para leer opciones de Intro/Extro.'
    } else {
      error.value = 'No se pudieron cargar las opciones de Intro/Extro.'
    }
  } finally {
    loading.value = false
  }
}

function onRowChange(row) {
  // Aquí podríamos persistir borrador local si se desea
}

function onDiscSaved(payload){
  // Mostrar el gráfico con los totales D/I/S/C y ocultar el formulario
  if (payload && typeof payload.d === 'number') {
    savedTotals.value = { d: payload.d, i: payload.i, s: payload.s, c: payload.c }
    // Opcional: desplazar a la sección de resultados
    setTimeout(() => {
      try { document?.querySelector('#app')?.scrollIntoView({ behavior: 'smooth' }) } catch(_) {}
    }, 50)
  }
}

async function loadExistingIntroExtro(personId){
  try {
    const res = await axios.get(`/api/personal-intro-extro/by-person/${encodeURIComponent(personId)}`)
    const items = res?.data?.items || []
    if (items.length > 0) {
      const latest = items[0]
      const snapshot = latest?.introExtro || {}
      const sel = { ...selectedByRow.value }
      for (const k of Object.keys(snapshot)) {
        sel[k] = snapshot[k]
      }
      selectedByRow.value = sel
    }
  } catch (e) {
    // silencioso
  }
}

async function loadSavedDisc(personId){
  try {
    const res = await axios.get(`/api/personal-disc/by-person/${encodeURIComponent(personId)}`)
    const items = res?.data?.items || []
    if (items.length > 0) {
      const latest = items[0]
      savedTotals.value = { d: latest.d, i: latest.i, s: latest.s, c: latest.c }
    }
  } catch (e) {
    // silencioso
  }
}

onMounted(async () => {
  await fetchIntroExtro()
  if (props.personaId) {
    await loadSavedDisc(props.personaId)
  }
})

// Normalización de 0–120 a escala 12–48 del gráfico de referencia
const normalizeTo48 = (v) => {
  const n = Number(v) || 0
  const clamped = Math.max(0, Math.min(120, n))
  return Math.round(12 + clamped * 0.3) // 0->12, 120->48
}

const chartVals = computed(() => {
  const d = savedTotals.value?.d ?? 0
  const i = savedTotals.value?.i ?? 0
  const s = savedTotals.value?.s ?? 0
  const c = savedTotals.value?.c ?? 0
  return {
    d: normalizeTo48(d),
    i: normalizeTo48(i),
    s: normalizeTo48(s),
    c: normalizeTo48(c),
  }
})
</script>

<style scoped>
/* sin estilos especiales por ahora */
</style>
