<template>
  <VForm @submit.prevent="submit">
    <v-container>
      <v-row>
        <v-col
            cols="12"
            md="6"
        >
          <v-text-field
              v-model="state.nombre"
              label="Nombre"
              hide-details
              required
          ></v-text-field>
        </v-col>

        <v-col
            cols="12"
            md="6"
        >
          <v-text-field
              v-model="state.apellido"
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
              v-model="state.email"
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
              v-model="state.phone"
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
              v-model="state.point"
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
          <VBtn @click="submit" color="primary" > Enviar</VBtn>
        </v-col>
      </v-row>
    </v-container>
  </VForm>
</template>

<script setup>
import {ref} from 'vue';
import {reactive} from 'vue'
import axios from 'axios';
import {useVuelidate} from '@vuelidate/core'
import {email as mail, required} from '@vuelidate/validators'
import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'

const initialState = {

  nombre: '',
  apellido: '',
  email: '',
  phone: '',
  point: ''
}

const state = reactive({
  ...initialState,
})
const rules = {
  nombre: {required},
  email: {required, mail},
  apellido: {required},
  phone: {required}
}

const v$ = useVuelidate(rules, state)

const submit = async () => {
  try {
    const response = await axios.post('/api/personal', {
      nombre: state.nombre.value,
      apellido: state.apellido.value,
      email: state.email.value,

      phone: state.phone.value,
      point: state.point.value,
    });
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
};
</script>