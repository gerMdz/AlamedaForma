<template>
  <v-container>

    <v-form v-if="!showSavedView" ref="form" @submit.prevent="submitForm">
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
              v-model="form.donComments[don.identifier || don.id]"
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
              {{ getComputedValue(don.identifier) }} %
            </v-chip-group>
          </v-card>
        </v-col>
      </v-row>
      <v-btn color="primary" @click="submitForm">
        Guardar resultado
      </v-btn>
        <v-snackbar v-model="snackbarSaved" timeout="4000" color="success" location="top">
          Datos guardados correctamente.
          <template #actions>
            <v-btn variant="text" @click="snackbarSaved = false">Cerrar</v-btn>
          </template>
        </v-snackbar>

        <SavedResults v-if="showSavedView" :data="savedData" />
      </v-form>
  </v-container>

</template>
<script setup>
import {ref, reactive, onMounted, watch} from 'vue';
import {defineProps} from 'vue';
import axios from 'axios';
import { store } from '../../assets/almacen';
import SavedResults from './SavedResults.vue';

const snackbarSaved = ref(false)
const showSavedView = ref(false)
const savedData = ref({})

// console.log de depuración movido para evitar ReferenceError en script-setup
// Nota: use props.dones si se requiere loggear desde script
// console.log("Datos que pasarán a PersonalFormation: ", props?.dones);

const props = defineProps({
  dones: Array,
  getComputedValue: Function,
});


let form = reactive({
  donComments: {},
});

// Preinicializar claves de comentarios para los 3 dones top
const initComments = () => {
  const top3 = props.dones?.slice(0,3) || [];
  for (const d of top3) {
    const key = d?.identifier || d?.id;
    if (key && form.donComments[key] === undefined) {
      form.donComments[key] = '';
    }
  }
};

onMounted(() => initComments());
watch(() => props.dones, () => initComments(), { immediate: false });

const submitForm = async () => {
  const personData = store.responseData?.value || null;
  const personId = personData?.id || personData?.['@id'] || null;

  // Guardas tempranas
  if (!personId) {
    console.warn('No se encontró la persona en el store; no se puede guardar.');
    snackbarSaved.value = false;
    try { alert('Antes de guardar, completa tus datos personales.'); } catch(e) {}
    return;
  }

  const top3 = props.dones?.slice(0,3) || [];
  const payloads = [];

  // Asegurar estructura segura de comentarios
  const comments = (form && typeof form === 'object' && form.donComments && typeof form.donComments === 'object')
    ? form.donComments
    : {};

  for (const donObj of top3) {
    const donKey = donObj?.identifier || donObj?.id;
    // Preinicializar clave si no existe para mantener reactividad
    if (donKey && comments[donKey] === undefined) {
      comments[donKey] = '';
    }
    const raw = donKey ? comments[donKey] : '';
    const commentDon = (raw ?? '').toString().slice(0, 200);
    const percentDon = (typeof props.getComputedValue === 'function' && donObj?.identifier)
      ? Math.round(props.getComputedValue(donObj.identifier))
      : null;
    const donId = donObj?.id || donObj?.['@id'] || donKey;

    // Solo enviar si hay persona y don identificable
    if (personId && donId) {
      payloads.push({ percentDon, commentDon, don: donId, person: personId });
    }
  }

  try {
    // Enviar solo los datos de los 3 primeros
    const results = [];
    for (const p of payloads) {
      const res = await axios.post('api/personal-formation', p);
      results.push(res?.data);
    }

    // Aviso de datos guardados
    snackbarSaved.value = true;

    // Redirigir a la nueva vista de confirmación con los datos guardados
    savedData.value = { person: personData, formations: results };
    showSavedView.value = true;
  } catch (e) {
    console.error('Error guardando PersonalFormation', e);
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