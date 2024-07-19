<template>
  <v-container fluid class="fill-height w-auto">
    <div v-if="responseData">
      <h3> Gracias {{ responseData.nombre }} {{ responseData.apellido }} </h3>
      <p>
        A continuación encontrarás varias secciones, que están clasificadas según el acróstico F.O.R.M.A. Deberás ir
        completando cada sección y de esta manera ir avanzando para completar el test. *
      </p>
    </div>
  </v-container>

</template>

<script setup>
import {ref} from 'vue';
import {inject} from 'vue'
import {store} from "../../assets/almacen";

const responseData = store.responseData
if (typeof responseData === 'undefined') {
  console.log('responseData es undefined');
} else {
  console.log('responseData tiene un valor:', responseData);
}

console.log('HelloForma: rd -> ' + responseData)
</script>

<style scoped>


</style>