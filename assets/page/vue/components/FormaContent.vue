<script setup>
import { onMounted, watch, ref, computed } from 'vue'
import HelloForma from "./HelloForma.vue";
import {store} from "../../assets/almacen";
import FormPersonalidades from "./FormPersonalidades.vue";
import Formation from "./Formation.vue";
import CompletedFormation from "./CompletedFormation.vue";
import Inicio from "./Inicio.vue";
import PersonalRecursos from "./PersonalRecursos.vue";
import Orientacion from "./Orientacion.vue";
import axios from 'axios'

const responseData = store.responseData

const termsAccepted = store.termsAccepted
const checking = ref(false)
const checkingF = ref(false)
const hasAvanceF = ref(false)
const checkingR = ref(false)
const hasAvanceR = ref(false)
// resultsMode is true either if backend says there is avance F or if user just saved
const resultsMode = store.resultsMode

// identity props for child forms
const pid = computed(() => responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id || '')
const pemail = computed(() => responseData?.value?.email || '')
const pphone = computed(() => responseData?.value?.phone || '')

// Hydrate full person details if we only have the ID
function needsPersonHydration(val) {
  const v = val || responseData?.value || null
  if (!v) return false
  const id = v.id || v.ID || v.Id
  const hasAnyField = !!(v.nombre || v.apellido || v.email || v.phone || v.point)
  return !!id && !hasAnyField
}

async function hydratePerson(val) {
  const v = val || responseData?.value || null
  if (!v) return
  const id = v.id || v.ID || v.Id
  if (!id) return
  try {
    const res = await axios.get(`/api/personal/${encodeURIComponent(id)}`)
    if (res?.data) {
      try { store.setResponseData(res.data) } catch (_) { /* noop */ }
    }
  } catch (e) {
    console.warn('No se pudo hidratar datos de la persona', e)
  }
}

// Tabs for Formación (F), Orientación (O) y Recursos (R)
const activeTab = ref('F')
function selectDefaultTab() {
  if (store.hasF && store.hasF.value) {
    activeTab.value = 'F'
  } else if (store.hasO && store.hasO.value) {
    activeTab.value = 'O'
  } else if (store.hasR && store.hasR.value) {
    activeTab.value = 'R'
  }
}

async function checkTerms() {
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checking.value = true
  try {
    const res = await axios.get(`/api/forma/terminos-estado/${encodeURIComponent(personalId)}`)
    store.setTermsAccepted(!!res?.data?.accepted)
  } catch (e) {
    console.warn('No se pudo verificar estado de Términos', e)
    // Si la persona no existe en el backend, limpiar el caché local y volver a paso Personal
    const status = e?.response?.status || e?.status || null;
    if (status === 404) {
      try { store.setResponseData(null); } catch(_) {}
    }
    // Por seguridad, si falla la verificación mostramos Términos como no aceptados
    store.setTermsAccepted(false)
  } finally {
    checking.value = false
  }
}

async function checkAvanceF() {
  // Solo aplica si F está habilitado
  if (!(store.hasF && store.hasF.value)) {
    hasAvanceF.value = false
    try { store.setResultsMode(false) } catch(_) {}
    return
  }
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checkingF.value = true
  try {
    const res = await axios.get(`/api/forma/avance-f-estado/${encodeURIComponent(personalId)}`)
    hasAvanceF.value = !!res?.data?.hasAvanceF
    if (hasAvanceF.value) {
      try { store.setResultsMode(true) } catch(_) {}
    } else {
      // Si no tiene Avance F, aseguramos que no quede en modo resultados
      try { store.setResultsMode(false) } catch(_) {}
    }
  } catch (e) {
    console.warn('No se pudo verificar avance F', e)
    hasAvanceF.value = false
    try { store.setResultsMode(false) } catch(_) {}
  } finally {
    checkingF.value = false
  }
}

async function checkAvanceR() {
  // Aplica solo si R está habilitado
  if (!(store.hasR && store.hasR.value)) {
    hasAvanceR.value = false
    return
  }
  const personalId = responseData?.value?.id || responseData?.value?.ID || responseData?.value?.Id
  if (!personalId) return
  checkingR.value = true
  try {
    const res = await axios.get(`/api/forma/avance-r-estado/${encodeURIComponent(personalId)}`)
    hasAvanceR.value = !!res?.data?.hasAvanceR
  } catch (e) {
    console.warn('No se pudo verificar avance R', e)
    hasAvanceR.value = false
  } finally {
    checkingR.value = false
  }
}

onMounted(async () => {
  if (responseData?.value) {
    if (needsPersonHydration(responseData.value)) {
      await hydratePerson(responseData.value)
    }
    checkTerms()
    checkAvanceF()
    checkAvanceR()
  }
  // set default tab after flags are known
  selectDefaultTab()
})

watch(responseData, async (val) => {
  if (val) {
    if (needsPersonHydration(val)) {
      await hydratePerson(val)
    }
    checkTerms()
    checkAvanceF()
    checkAvanceR()
  }
})

// adjust default tab when flags change
watch(() => [store.hasF && store.hasF.value, store.hasO && store.hasO.value, store.hasR && store.hasR.value], () => {
  selectDefaultTab()
  // recheck avance R if R becomes enabled
  checkAvanceR()
})
</script>

<template>
  <v-container fluid class="fill-height">
    <HelloForma :response-data="responseData?.value"/>
    <v-container v-if="responseData" fluid class="text-center">
      <div v-if="checking">Verificando términos...</div>
      <template v-else>
        <template v-if="!termsAccepted">
          <!-- Paso 2: Términos y condiciones + instrucciones -->
          <Inicio />
        </template>
        <template v-else>
          <!-- Presentación: mostrar siempre arriba de los tabs -->
          <div class="mt-6" style="width:100%; max-width:1280px; margin: 0 auto;">
            <v-row>
              <v-col cols="12">
                <v-alert v-if="(resultsMode || hasAvanceF)" type="success" class="mb-4">
                  Ya registraste tus resultados de Formación. Aquí está tu resumen.
                </v-alert>
              </v-col>
            </v-row>
            <v-row justify="center">
              <v-col cols="12" md="6">
                <v-card title="Datos de la persona --" class="mb-4">
                  <v-card-text>
                    <div><strong>Nombre/s:</strong> {{ responseData?.nombre }} {{ responseData?.apellido }}</div>
                    <div><strong>Email:</strong> {{ responseData?.email }}</div>
                    <div><strong>Teléfono:</strong> {{ responseData?.phone }}</div>
                    <div v-if="responseData?.point"><strong>Grupo:</strong> {{ responseData?.point }}</div>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
          </div>

          <!-- Pestañas para Formación, Orientación y Recursos (según habilitación) -->
          <!-- Cuando estamos en modo resultados, mostramos únicamente el resumen con sus propias tabs (con iconos) -->
          <div v-if="(resultsMode || hasAvanceF)">
            <CompletedFormation />
          </div>
          <div v-else-if="(store.hasF && store.hasF.value) || (store.hasO && store.hasO.value) || (store.hasR && store.hasR.value)" class="mt-6">
            <v-tabs v-model="activeTab" grow>
              <v-tab v-if="store.hasF && store.hasF.value" value="F" :class="['forma-tab', { 'is-active': activeTab==='F' }]">[F]ormación</v-tab>
              <v-tab v-if="store.hasO && store.hasO.value" value="O" :class="['forma-tab', { 'is-active': activeTab==='O' }]">[O]rientación</v-tab>
              <v-tab v-if="store.hasR && store.hasR.value" value="R" :class="['forma-tab', { 'is-active': activeTab==='R' }]">[R]ecursos y Habilidades</v-tab>
            </v-tabs>
            <v-window v-model="activeTab" class="mt-4">
              <v-window-item v-if="store.hasF && store.hasF.value" value="F">
                <template v-if="store.hasF && store.hasF.value">
                  <Formation />
                </template>
              </v-window-item>
              <v-window-item v-if="store.hasO && store.hasO.value" value="O">
                <Orientacion :persona-id="pid" :email="pemail" :phone="pphone" />
              </v-window-item>
              <v-window-item v-if="store.hasR && store.hasR.value" value="R">
                <PersonalRecursos :persona-id="pid" :email="pemail" :phone="pphone" />
              </v-window-item>
            </v-window>
          </div>
        </template>
      </template>
    </v-container>
  </v-container>

</template>

<style scoped>
.forma-tab.is-active {
  font-weight: 700;
  color: #1976d2; /* primary-ish */
  border-bottom: 2px solid #1976d2;
}
</style>