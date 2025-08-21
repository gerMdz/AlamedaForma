<template>
  <VForm @submit.prevent="submit">
    <v-container>
      <v-row>
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
          <VBtn v-if="isRequiredFilled" @click="submit" color="primary"> Siguiente</VBtn>
        </v-col>
      </v-row>
    </v-container>
  </VForm>

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
let responseData = ref(null);
provide('responseData', responseData);

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
  // Prevent submit if required fields are not filled
  if (!isRequiredFilled.value) {
    // Touch fields to show validation feedback, if any
    v$.value.$touch();
    return;
  }

  // Optional: validate using Vuelidate; if invalid, stop
  if (typeof v$.value?.$validate === 'function') {
    const isValid = await v$.value.$validate();
    if (!isValid) return;
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

console.log('Personal: rd -> ' + responseData)


  } catch (error) {
    console.error(error);
  }
};


</script>