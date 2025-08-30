<template>
  <v-container fluid class="personal-container">
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
        <v-col>
          <VBtn
                      :disabled="!isRequiredFilled || submitting || submittedSuccessfully"
                      :loading="submitting"
                      @click="submit"
                      color="primary"
                    >
                      {{ submittedSuccessfully ? 'Guardado' : 'Siguiente' }}
                    </VBtn>
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
import {ref, provide, computed} from 'vue';
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
    const response = await axios.post('/api/personal', data);
    store.setResponseData(response.data);

    if (response && response.status === 200) {
      existsNotice.value = 'Ya existe una persona con ese email y teléfono. Continuamos con su registro.';
    } else {
      existsNotice.value = '';
    }

    // Éxito: desactivar el botón "Siguiente" en adelante
    submittedSuccessfully.value = true;

  } catch (error) {
    console.error(error);
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
