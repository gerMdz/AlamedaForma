<template>
  <v-container>

    <v-form v-if="!showSavedView" ref="formEl" @submit.prevent="submitForm">
      <v-row>
        <v-col :cols="12" :sm="6" :md="4" v-for="don in dones.slice(0,3)" :key="don.identifier || don.id">
          <v-card class="cardStyle mx-auto my-12 bg-light-blue-accent-3 d-flex flex-column justify-space-between"
                  max-width="374">
            <v-card-item class="bg-blue-accent-3">
              <v-card-title>{{ don.name }}</v-card-title>
            </v-card-item>
            <v-card-text>
              {{ don.description }}
            </v-card-text>
            <v-textarea
              v-model="formState.donComments[don.identifier || don.id]"
              :label="`¿Cómo te sientes con el don “${don.name}”?`"
              placeholder="Escribe aquí tu comentario..."
              counter="200"
                            :maxlength="200"
              variant="outlined"
              color="primary"
              class="textareaHighlight"
              density="comfortable"
              rows="4"
              clearable
              persistent-hint
              hint="Escribe tu reflexión aquí"
              prepend-inner-icon="mdi-pencil"
            />
            <v-chip-group v-model="selection">
              {{ gv(don.identifier) }} %
            </v-chip-group>
          </v-card>
        </v-col>
      </v-row>
      <v-btn color="primary" :loading="saving" :disabled="saving" @click="submitForm">
        Guardar resultado
      </v-btn>
        <v-snackbar v-model="snackbarSaved" timeout="4000" color="success" location="top">
          Datos guardados correctamente.
          <template #actions>
            <v-btn variant="text" @click="snackbarSaved = false">Cerrar</v-btn>
          </template>
        </v-snackbar>
        <v-snackbar v-model="snackbarError" timeout="5000" color="error" location="top">
          Ocurrió un error al guardar los resultados. Inténtalo nuevamente.
          <template #actions>
            <v-btn variant="text" @click="snackbarError = false">Cerrar</v-btn>
          </template>
        </v-snackbar>

        <SavedResults v-if="showSavedView" :data="savedData" />
      </v-form>
  </v-container>

</template>
<script setup>
import {ref, reactive, onMounted, watch, computed} from 'vue';
import {defineProps} from 'vue';
import axios from 'axios';
import { store } from '../../assets/almacen';
import SavedResults from './SavedResults.vue';

const snackbarSaved = ref(false)
const snackbarError = ref(false)
const saving = ref(false)
const showSavedView = ref(false)
const savedData = ref({})

// Helper para obtener el porcentaje desde getComputedValue (soporta function o computed)
const gv = (id) => {
  try {
    if (props.getComputedValue && typeof props.getComputedValue === 'function') {
      return Math.round(props.getComputedValue(id) || 0)
    }
    if (props.getComputedValue && typeof props.getComputedValue.value === 'function') {
      return Math.round(props.getComputedValue.value(id) || 0)
    }
  } catch(e) { /* noop */ }
  return 0
}

// Habilita guardar solo si existe una referencia válida a la persona
const canSave = computed(() => {
  const personData = store.responseData?.value || null;
  if (personData && typeof personData === 'object') {
    if (typeof personData['@id'] === 'string' && personData['@id'].length > 0) {
      return true;
    }
    if (personData.id !== undefined && personData.id !== null) {
      const n = typeof personData.id === 'string' ? Number(personData.id) : personData.id;
      return Number.isFinite(n) && n > 0;
    }
  }
  return false;
})

// console.log de depuración movido para evitar ReferenceError en script-setup
// Nota: use props.dones si se requiere loggear desde script
// console.log("Datos que pasarán a PersonalFormation: ", props?.dones);

const props = defineProps({
  dones: Array,
  getComputedValue: Function,
});


let formState = reactive({
  donComments: {},
});

// Preinicializar claves de comentarios para los 3 dones top
const initComments = () => {
  const top3 = props.dones?.slice(0,3) || [];
  for (const d of top3) {
    const key = d?.identifier || d?.id;
    if (key && formState.donComments[key] === undefined) {
      formState.donComments[key] = '';
    }
  }
};

onMounted(() => initComments());
watch(() => props.dones, () => initComments(), { immediate: false });

const submitForm = async () => {
  const personData = store.responseData?.value || null;
  // Accept '@id', numeric id, or numeric string id
  let personRef = null;
  if (personData && typeof personData === 'object') {
    // Accept IRI like /api/personal/{id}
    const iri = typeof personData['@id'] === 'string' ? personData['@id'] : null;
    if (iri && iri.length > 0) {
      personRef = iri;
    } else {
      // Accept id in any common casing (id, ID, Id) and as UUID string or number
      const rawId = personData.id ?? personData.ID ?? personData.Id ?? null;
      if (rawId !== undefined && rawId !== null) {
        if (typeof rawId === 'string') {
          const trimmed = rawId.trim();
          // If it looks like a UUID or any non-empty string, send as-is
          if (trimmed.length > 0) {
            personRef = trimmed;
          }
        } else if (typeof rawId === 'number') {
          if (Number.isFinite(rawId) && rawId > 0) {
            personRef = rawId;
          }
        }
      }
    }
  }

  // Guardas tempranas (no usar alert intrusivo)
  if (!personRef) {
    console.warn('No se encontró la persona en el store; no se puede guardar.');
    // No mostrar snackbar de error porque no se realizó ninguna petición
    // Simplemente salir silenciosamente. El botón ya está deshabilitado si no hay persona.
    return;
  }

  const top3 = props.dones?.slice(0,3) || [];
  const payloads = [];

  // Asegurar estructura segura de comentarios
  const comments = (formState && typeof formState === 'object' && formState.donComments && typeof formState.donComments === 'object')
    ? formState.donComments
    : {};

  for (const donObj of top3) {
    const donKey = donObj?.identifier || donObj?.id;
    // Preinicializar clave si no existe para mantener reactividad
    if (donKey && comments[donKey] === undefined) {
      comments[donKey] = '';
    }
    const raw = donKey ? comments[donKey] : '';
    const commentDon = (raw ?? '').toString().slice(0, 200);
    let percentDon = null;
    if (donObj?.identifier) {
      // getComputedValue is a ComputedRef that returns a function; handle both cases
      if (props.getComputedValue && typeof props.getComputedValue === 'function') {
        percentDon = Math.round(props.getComputedValue(donObj.identifier));
      } else if (props.getComputedValue && typeof props.getComputedValue.value === 'function') {
        percentDon = Math.round(props.getComputedValue.value(donObj.identifier));
      }
    }
    const donId = donObj?.id || donObj?.['@id'] || donKey;

    // Solo enviar si hay persona y don identificable
    if (personRef && donId) {
      payloads.push({ percentDon, commentDon, don: donId, person: personRef });
    }
  }

  saving.value = true;
try {
    // Enviar solo los datos de los 3 primeros
    const results = [];
    for (const p of payloads) {
      const res = await axios.post('/api/personal-formation', p);
      results.push(res?.data);
    }

    // Aviso de datos guardados
    snackbarSaved.value = true;

    // Redirigir a la nueva vista de confirmación con los datos guardados
    savedData.value = { person: personData, formations: results };
    showSavedView.value = true;
  } catch (e) {
    console.error('Error guardando PersonalFormation', e);
    snackbarError.value = true;
  } finally {
    saving.value = false;
  }
};
</script>
<style scoped>

.cardStyle {
  flex-direction: column;
  min-height: 270px;
}

.textareaHighlight :deep(textarea),
.textareaHighlight :deep(.v-field__outline),
.textareaHighlight :deep(.v-field) {
  border-width: 2px !important;
}

/* Fondo azul más notorio para destacar el área de escritura */
.textareaHighlight :deep(.v-field) {
  background-color: rgba(21, 101, 192, 0.35); /* azul más intenso, similar al header */
  border-radius: 10px;
}

.textareaHighlight:focus-within :deep(.v-field) {
  box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.45);
}

/* Texto del usuario en blanco para máximo contraste */
.textareaHighlight :deep(textarea) {
  color: #ffffff;
  padding: 12px 14px;
  min-height: 120px;
  font-size: 1rem; /* ligeramente más grande para legibilidad */
  text-shadow: 0 1px 1px rgba(0,0,0,0.2);
}

/* Label y placeholder más visibles sobre fondo azul */
.textareaHighlight :deep(.v-field__input)::-webkit-input-placeholder { color: rgba(255,255,255,0.9) !important; }
.textareaHighlight :deep(.v-field__input)::placeholder { color: rgba(255,255,255,0.9) !important; }

/* Colores de label, hint y counter claros */
.textareaHighlight :deep(.v-field-label) { color: #ffffff !important; text-shadow: 0 1px 1px rgba(0,0,0,0.25); font-size: 0.98rem; }
.textareaHighlight :deep(.v-messages__message) { color: #e3f2fd !important; }
.textareaHighlight :deep(.v-counter) { color: #e3f2fd !important; }

</style>