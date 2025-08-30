<template>
  <v-container fluid class="personal-container">
    <h1 class="text-center mt-10">Te damos la bienvenida a F.O.R.M.A.</h1>
    <h3 class="text-center mt-2">Te pedimos que completes tus datos para iniciar con el test</h3>
    <VForm @submit.prevent="submit">
      <v-row>
        <v-col cols="12">
          <v-alert v-if="existsNotice" type="warning" density="comfortable" class="mb-3">
            {{ existsNotice }}
          </v-alert>
        </v-col>
        <v-col
            cols="12"
            md="6"
        >
          <v-text-field
              v-model="nombre"
              label="Nombre"
              hide-details
              required
              placeholder = "Ingresar nombre"
              @blur="v$.nombre.$touch"
              @input="v$.nombre.$touch"
          ></v-text-field>
        </v-col>

        <v-col
            cols="12"
            md="6"
        >
          <v-text-field
              v-model="apellido"
              label="Apellido"
              hide-details
              required
          ></v-text-field>
        </v-col>

      </v-row>

      <v-row>
        <v-col
            cols="12"
            md="6"
        >
          <v-text-field
              v-model="email"
              label="Email"
              hide-details
              required
              :error-messages="v$.email.$errors.map(e => e.$message)"
              @blur="v$.email.$touch"
              @input="v$.email.$touch"
          ></v-text-field>
        </v-col>

        <v-col
            cols="12"
            md="6"
        >
          <v-text-field
              v-model="phone"
              label="Teléfono"
              hide-details
              required
          ></v-text-field>
        </v-col>

      </v-row>

      <v-row>
        <v-col
            cols="12"
            md="12"
        >
          <v-text-field
              v-model="point"
              label="Grupo"
              hide-details
              required
          ></v-text-field>
        </v-col>


      </v-row>


      <!--      <div>-->
      <!--        <label>Observaciones</label>-->
      <!--        <input type="text" v-model="observaciones">-->
      <!--      </div>-->

      <v-row>
        <v-col class="d-flex" style="gap: 12px;">
          <VBtn
                      :disabled="!isRequiredFilled || submitting || submittedSuccessfully"
                      :loading="submitting"
                      @click="submit"
                      color="primary"
                    >
                      {{ isEditMode ? (submittedSuccessfully ? 'Guardado' : 'Guardar cambios') : (submittedSuccessfully ? 'Guardado' : 'Siguiente') }}
                    </VBtn>
          <VBtn v-if="isEditMode" variant="text" color="secondary" @click="cancelEdit" :disabled="submitting">Cancelar</VBtn>
        </v-col>
      </v-row>
    </VForm>

    <!-- Saludo debajo del formulario cuando ya tenemos una persona grabada -->
    <v-row v-if="store.responseData && store.responseData.value" class="mt-4">
      <v-col cols="12">
        <v-alert type="success" variant="tonal" density="comfortable">
          Gracias {{ (store.responseData.value?.nombre || nombre) }} {{ (store.responseData.value?.apellido || apellido) }} por ingresar al test F.O.R.M.A.
        </v-alert>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import {ref, provide, computed, onMounted} from 'vue';
import axios from 'axios';
import {useVuelidate} from '@vuelidate/core'
import {email as emailValidator, required} from '@vuelidate/validators'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import {store}  from "../../assets/almacen";


const  nombre= ref('');
const  apellido= ref('');
const  email= ref('');
const  phone= ref('');
const  point= ref('');

const isEditMode = computed(() => !!(store.editPersonalMode && store.editPersonalMode.value));

onMounted(() => {
  try {
    if (isEditMode.value && store.responseData && store.responseData.value) {
      const p = store.responseData.value;
      nombre.value = p?.nombre || '';
      apellido.value = p?.apellido || '';
      email.value = p?.email || '';
      phone.value = p?.phone || '';
      point.value = p?.point || '';
    }
  } catch(e) { /* noop */ }
});
const existsNotice = ref('');
let responseData = ref(null);
provide('responseData', responseData);

// Control de envío para desactivar el botón tras éxito y evitar doble envío
const submitting = ref(false)
const submittedSuccessfully = ref(false)

// Computed to check if all required fields are filled (ignores email format)
const isRequiredFilled = computed(() =>
  Boolean(nombre.value && nombre.value.toString().trim()) &&
  Boolean(apellido.value && apellido.value.toString().trim()) &&
  Boolean(email.value && email.value.toString().trim()) &&
  Boolean(phone.value && phone.value.toString().trim())
);

const validationRules = {
  nombre: { required },
  email: { required, emailValidator },
  apellido: { required },
  phone: { required },
}




const v$ = useVuelidate(validationRules, { nombre, apellido, email, phone, point })

const cancelEdit = () => {
  try { if (store.editPersonalMode) store.editPersonalMode.value = false } catch(e) { /* noop */ }
}

const submit = async () => {
  if (submittedSuccessfully.value || submitting.value) return;
  submitting.value = true;
  // Prevent submit if required fields are not filled
  if (!isRequiredFilled.value) {
    // Touch fields to show validation feedback, if any
    v$.value.$touch();
    submitting.value = false;
    return;
  }

  // Optional: validate using Vuelidate; if invalid, stop
  if (typeof v$.value?.$validate === 'function') {
    const isValid = await v$.value.$validate();
    if (!isValid) {
      submitting.value = false;
      return;
    }
  }

  const data = {
    nombre: nombre.value,
    apellido: apellido.value,
    email: email.value,
    phone: phone.value,
    point: point.value,
  }
  try {
    let response;
    if (isEditMode.value && store.responseData && store.responseData.value && (store.responseData.value.id || store.responseData.value.ID || store.responseData.value.Id)) {
      const pid = store.responseData.value.id || store.responseData.value.ID || store.responseData.value.Id
      response = await axios.patch(`/api/personal/${encodeURIComponent(pid)}`, data, { headers: { 'Content-Type': 'application/json' } });
    } else {
      response = await axios.post('/api/personal', data);
    }
    store.setResponseData(response.data);

    if (response && response.status === 200) {
      existsNotice.value = 'Ya existe una persona con ese email y teléfono. Continuamos con su registro.';
    } else {
      existsNotice.value = '';
    }

    // Éxito: si es edición, salir del modo edición y no bloquear el botón; si es creación, marcar como enviado
    if (isEditMode.value) {
      try { if (store.editPersonalMode) store.editPersonalMode.value = false } catch(e) { /* noop */ }
      submittedSuccessfully.value = false;
    } else {
      submittedSuccessfully.value = true;
    }

  } catch (error) {
    console.error(error);
    const status = error?.response?.status || error?.status || null;
    if (status === 409) {
      existsNotice.value = 'Ya existe otra persona con ese email y teléfono. Por favor verifique los datos.';
    }
  } finally {
    submitting.value = false;
  }
};


</script>

<style scoped>
.personal-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 12px;
}
</style>
