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
        <v-sheet class="pa-4 mx-auto bg-transparent" rounded="lg" border="thin" elevation="0" style="width: 100%; max-width: 1280px;">
          <FormPersonalidades
                      :persona-id="props.personaId || ''"
                      :intro-extro-ready="introExtroReady"
                      :intro-extro-data="introExtroData"
                      @saved="onDiscSaved"
                    />
        </v-sheet>

        <!-- Gráfico simple con las sumas DISC (se muestra luego de guardar) -->
        <div v-if="savedTotals" class="mt-8 mx-auto" style="width:100%; max-width: 960px;">
          <v-card title="Resultados DISC" class="mb-4">
            <v-card-text>
              <div class="mb-4">Visualización preliminar de tus resultados. Luego podemos refinar estilo/colores.</div>
              <v-row>
                <v-col cols="12" md="6">
                  <div class="mb-2"><strong>D:</strong> {{ savedTotals.d }}</div>
                  <v-progress-linear :model-value="savedTotals.d" :max="120" color="red" height="12" rounded></v-progress-linear>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-2"><strong>I:</strong> {{ savedTotals.i }}</div>
                  <v-progress-linear :model-value="savedTotals.i" :max="120" color="orange" height="12" rounded></v-progress-linear>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-2 mt-4"><strong>S:</strong> {{ savedTotals.s }}</div>
                  <v-progress-linear :model-value="savedTotals.s" :max="120" color="green" height="12" rounded></v-progress-linear>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="mb-2 mt-4"><strong>C:</strong> {{ savedTotals.c }}</div>
                  <v-progress-linear :model-value="savedTotals.c" :max="120" color="blue" height="12" rounded></v-progress-linear>
                </v-col>
              </v-row>
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

const props = defineProps({
  personaId: { type: [String], required: false },
  email: { type: String, required: false, default: '' },
  phone: { type: String, required: false, default: '' },
})

const loading = ref(false)
const error = ref('')

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
  // Tras guardar, se podría mostrar un gráfico con los totales D/I/S/C
  console.log('DISC guardado', payload)
}

onMounted(() => {
  fetchIntroExtro()
})
</script>

<style scoped>
/* sin estilos especiales por ahora */
</style>
