<template>
  <form @submit.prevent="submit">
    <div>
      <label>Nombre</label>
      <input type="text" v-model="nombre">
    </div>
    <div>
      <label>Apellido</label>
      <input type="text" v-model="apellido">
    </div>
    <div>
      <label>Email</label>
      <input type="email" v-model="email">
    </div>
    <div>
      <label>Observaciones</label>
      <input type="text" v-model="observaciones">
    </div>
    <div>
      <label>Teléfono</label>
      <input type="text" v-model="phone">
    </div>
    <div>
      <label>Punto</label>
      <input type="text" v-model="point">
    </div>
    <button>Enviar</button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const nombre = ref('');
const apellido = ref('');
const email = ref('');
const observaciones = ref('');
const phone = ref('');
const point = ref('');

const submit = async () => {
  try {
    const response = await axios.post('/api/personal', {
      nombre: nombre.value,
      apellido: apellido.value,
      email: email.value,
      observaciones: observaciones.value,
      phone: phone.value,
      point: point.value,
    });
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
};
</script>